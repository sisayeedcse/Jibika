<?php

namespace Tests\Feature;

use App\Actions\Assessments\AssessAssetRequestAction;
use App\Models\AssetCategory;
use App\Models\AssetRequest;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Services\AIService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class JibikaAiAndHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
    }

    protected function makeWorker(): User
    {
        $user = User::factory()->create(['role' => 'worker', 'status' => 'active']);
        WorkerProfile::create([
            'user_id'          => $user->id,
            'occupation'       => 'Master Tailor',
            'experience_years' => 6,
            'location'         => 'Chattogram',
            'bio'              => 'Tailor bio',
            'verified'         => true,
        ]);
        return $user;
    }

    protected function makeVerifiedRequest(float $amount = 35000, float $benchmark = 35000): AssetRequest
    {
        $worker = $this->makeWorker();
        $cat = AssetCategory::create([
            'name'               => 'Industrial Sewing Machine',
            'name_bn'            => 'শিল্প সেলাই মেশিন',
            'estimated_cost_bdt' => $benchmark,
            'description'        => 'Heavy-duty machine',
        ]);

        return AssetRequest::create([
            'worker_id'                => $worker->id,
            'category_id'              => $cat->id,
            'status'                   => 'verified',
            'requested_amount_bdt'     => $amount,
            'reason'                   => 'Upgrading tailoring capacity for bulk orders.',
            'livelihood_justification' => 'Will enable school uniform production for local institutions with higher daily throughput.',
        ]);
    }

    public function test_rule_engine_never_claims_ai(): void
    {
        config(['services.ai.provider' => 'gemini', 'services.ai.key' => 'fake-key']);
        $record = app(AIService::class)->assess($this->makeVerifiedRequest());

        $this->assertFalse($record->is_ai_generated);
        $this->assertSame('rule-engine', $record->provider_name);
        $this->assertSame(AIService::METHOD_VERSION, $record->method_version);
    }

    public function test_price_anomaly_is_flagged(): void
    {
        $req = $this->makeVerifiedRequest(90000, 35000);
        $record = app(AIService::class)->assess($req);

        $this->assertArrayHasKey('flags', $record->explanation_json);
        $this->assertStringContainsString('far above', $record->explanation_json['flags']);
        $this->assertNotSame('HIGH', $record->suitability_category);
    }

    public function test_draft_falls_back_when_ai_disabled(): void
    {
        config(['services.ai.provider' => 'development', 'services.ai.key' => '']);
        $worker = $this->makeWorker();
        AssetCategory::create([
            'name'               => 'Industrial Sewing Machine',
            'name_bn'            => 'শিল্প সেলাই মেশিন',
            'estimated_cost_bdt' => 35000,
            'description'        => '',
        ]);

        $response = $this->actingAs($worker)
            ->postJson(route('requests.draft'), ['text' => 'I am a tailor and need a sewing machine']);

        $response->assertOk()
            ->assertJsonPath('source', 'fallback')
            ->assertJsonStructure(['source', 'category_id', 'requested_amount_bdt', 'reason', 'livelihood_justification']);
    }

    public function test_llm_category_outside_catalog_is_discarded(): void
    {
        config(['services.ai.provider' => 'gemini', 'services.ai.key' => 'fake-key', 'services.ai.model' => 'gemini-2.5-flash']);
        $worker = $this->makeWorker();
        AssetCategory::create([
            'name'               => 'Carpentry Power Multi-Tool Set',
            'name_bn'            => 'ছুতারের স্বয়ংক্রিয় সরঞ্জাম সেট',
            'estimated_cost_bdt' => 28000,
            'description'        => '',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'category_id'              => 99999,
                                        'stated_amount_bdt'        => null,
                                        'reason'                   => 'Need tool for furniture shop.',
                                        'livelihood_justification' => 'Will expand woodcraft sales.',
                                        'missing_info'             => ['Stated experience'],
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->actingAs($worker)
            ->postJson(route('requests.draft'), ['text' => 'I need a machine for my small workshop']);

        $response->assertOk()
            ->assertJsonPath('source', 'llm')
            ->assertJsonPath('category_id', null);
    }

    public function test_worker_cannot_open_admin(): void
    {
        $worker = $this->makeWorker();
        foreach (['/admin', '/admin/users', '/admin/audit', '/admin/requests'] as $url) {
            $this->actingAs($worker)->get($url)->assertForbidden();
        }
    }

    public function test_demo_login_is_404_when_disabled(): void
    {
        config(['app.demo_mode' => false]);
        $this->post('/demo-login/admin')->assertNotFound();
    }

    public function test_demo_login_works_when_enabled(): void
    {
        config(['app.demo_mode' => true]);
        User::factory()->create(['email' => 'admin@demo.local', 'role' => 'admin', 'status' => 'active']);

        $this->post('/demo-login/admin')
            ->assertRedirect('/dashboard');

        $this->assertAuthenticated();
        $this->assertSame('admin@demo.local', auth()->user()->email);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        $this->actingAs($admin)
            ->post("/admin/users/{$admin->id}/status", ['status' => 'suspended'])
            ->assertStatus(422);

        $this->assertSame('active', $admin->fresh()->status);
    }

    public function test_demo_account_cannot_be_suspended_in_demo_mode(): void
    {
        config(['app.demo_mode' => true]);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);
        $contributor = User::factory()->create(['email' => 'contributor@demo.local', 'role' => 'contributor', 'status' => 'active']);

        $this->actingAs($admin)
            ->post("/admin/users/{$contributor->id}/status", ['status' => 'suspended'])
            ->assertForbidden();

        $this->assertSame('active', $contributor->fresh()->status);
    }

    public function test_double_trigger_assessment_race_condition_prevented(): void
    {
        $req = $this->makeVerifiedRequest();
        $worker = $req->worker;
        $action = app(AssessAssetRequestAction::class);

        $first = $action->execute($req, $worker);
        $this->assertNotNull($first);
        $this->assertSame('ai_assessed', $req->fresh()->status);

        $this->expectException(ValidationException::class);
        $action->execute($req->fresh(), $worker);
    }

    public function test_stated_amount_outside_bounds_is_discarded_to_benchmark(): void
    {
        config(['services.ai.provider' => 'gemini', 'services.ai.key' => 'fake-key']);
        $worker = $this->makeWorker();
        $cat = AssetCategory::create([
            'name'               => 'Commercial Dough Mixer (20L)',
            'name_bn'            => 'বাণিজ্যিক ময়দা মিক্সার',
            'estimated_cost_bdt' => 45000,
            'description'        => '',
        ]);

        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                [
                                    'text' => json_encode([
                                        'category_id'              => $cat->id,
                                        'stated_amount_bdt'        => 6000000, // exceeds 5,000,000 max bound
                                        'reason'                   => 'Need commercial mixer.',
                                        'livelihood_justification' => 'Will scale bakery output.',
                                        'missing_info'             => [],
                                    ])
                                ]
                            ]
                        ]
                    ]
                ]
            ], 200),
        ]);

        $response = $this->actingAs($worker)
            ->postJson(route('requests.draft'), ['text' => 'Need commercial mixer for bakery business costing 6000000']);

        $response->assertOk()
            ->assertJsonPath('source', 'llm')
            ->assertJsonPath('category_id', $cat->id)
            ->assertJsonPath('requested_amount_bdt', 45000)
            ->assertJsonPath('amount_basis', 'catalog_benchmark');
    }

    public function test_fallback_draft_is_not_cached(): void
    {
        config(['services.ai.provider' => 'development', 'services.ai.key' => '']);
        $worker = $this->makeWorker();
        $cat = AssetCategory::create([
            'name'               => 'Carpentry Power Multi-Tool Set',
            'name_bn'            => 'ছুতারের স্বয়ংক্রিয় সরঞ্জাম সেট',
            'estimated_cost_bdt' => 28000,
            'description'        => '',
        ]);

        $text = 'Need carpentry multi-tool set for furniture repair';

        $res1 = $this->actingAs($worker)->postJson(route('requests.draft'), ['text' => $text]);
        $res1->assertOk()->assertJsonPath('source', 'fallback');

        // Verify that the cache does NOT have a cached draft for this text
        $key = 'draft:' . sha1(mb_strtolower($text) . '|' . AssetCategory::all()->pluck('estimated_cost_bdt', 'id')->toJson());
        $this->assertFalse(Cache::has($key));
    }

    public function test_draft_endpoint_is_rate_limited(): void
    {
        $worker = $this->makeWorker();
        AssetCategory::create([
            'name'               => 'Tool Set',
            'name_bn'            => 'টুল সেট',
            'estimated_cost_bdt' => 20000,
            'description'        => '',
        ]);

        // Route has middleware throttle:6,1
        for ($i = 0; $i < 6; $i++) {
            $this->actingAs($worker)
                ->postJson(route('requests.draft'), ['text' => 'Testing rate limiting for draft endpoint'])
                ->assertOk();
        }

        // 7th request should be throttled (429 Too Many Requests)
        $this->actingAs($worker)
            ->postJson(route('requests.draft'), ['text' => 'Testing rate limiting for draft endpoint'])
            ->assertStatus(429);
    }

    public function test_locale_switch_and_bengali_rendering(): void
    {
        $response = $this->post(route('locale.switch', 'bn'));
        $response->assertSessionHas('locale', 'bn');

        $worker = $this->makeWorker();
        $cat = AssetCategory::create([
            'name'               => 'Industrial Sewing Machine',
            'name_bn'            => 'শিল্প সেলাই মেশিন',
            'estimated_cost_bdt' => 35000,
            'description'        => '',
        ]);

        $page = $this->withSession(['locale' => 'bn'])
            ->actingAs($worker)
            ->get(route('requests.create'));

        $page->assertOk();
        $page->assertSee('নতুন সম্পদ অনুরোধ');
        $page->assertSee('শিল্প সেলাই মেশিন');
        $page->assertSee('খসড়া সংরক্ষণ');

        // Switch back to en
        $this->post(route('locale.switch', 'en'))->assertSessionHas('locale', 'en');
        $enPage = $this->withSession(['locale' => 'en'])
            ->actingAs($worker)
            ->get(route('requests.create'));
        $enPage->assertSee('New Asset Request');
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $response = $this->post('/locale/fr');
        $response->assertNotFound();
    }

    public function test_walkthrough_banner_rendering_in_demo_mode(): void
    {
        config(['app.demo_mode' => true]);
        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('Demo Walkthrough');
        $response->assertSee('Follow the 9-Stage Asset Lifecycle');

        config(['app.demo_mode' => false]);
        $responseDisabled = $this->get(route('home'));
        $responseDisabled->assertOk();
        $responseDisabled->assertDontSee('Demo Walkthrough');
    }
}
