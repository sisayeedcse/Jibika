<?php

namespace Tests\Feature;

use App\Models\AssetCategory;
use App\Models\AssetRequest;
use App\Models\FundingPool;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JibikaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_fetch_categories_collection(): void
    {
        AssetCategory::create([
            'name'               => 'Commercial Mixer',
            'name_bn'            => 'মিক্সার',
            'estimated_cost_bdt' => 45000,
            'description'        => 'Bakery mixing machine',
        ]);

        $response = $this->getJson('/api/v1/categories');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'name_bn', 'estimated_cost_bdt', 'description', 'created_at'],
            ],
            'links',
            'meta',
        ]);
        $response->assertJsonFragment(['name' => 'Commercial Mixer']);
    }

    public function test_public_can_fetch_single_category(): void
    {
        $category = AssetCategory::create([
            'name'               => 'Solar Pump',
            'name_bn'            => 'সৌর পাম্প',
            'estimated_cost_bdt' => 60000,
            'description'        => 'Irrigation equipment',
        ]);

        $response = $this->getJson('/api/v1/categories/' . $category->id);
        $response->assertStatus(200);
        $response->assertJsonPath('data.name', 'Solar Pump');
        $this->assertEquals(60000, $response->json('data.estimated_cost_bdt'));
    }

    public function test_public_can_fetch_open_funding_pools(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $request = AssetRequest::create([
            'worker_id'            => $worker->id,
            'status'               => 'funding_open',
            'requested_amount_bdt' => 50000,
            'reason'               => 'Weaving frame',
            'livelihood_justification' => 'Production expansion',
        ]);

        FundingPool::create([
            'request_id'         => $request->id,
            'target_amount_bdt'  => 50000,
            'current_amount_bdt' => 15000,
            'status'             => 'open',
            'is_demo'            => true,
            'is_simulated_mode'  => true,
        ]);

        $response = $this->getJson('/api/v1/funding-pools');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'target_amount_bdt',
                    'current_amount_bdt',
                    'progress_percent',
                    'status',
                    'is_simulated_mode',
                    'disclaimer',
                ],
            ],
        ]);
        $response->assertJsonFragment([
            'target_amount_bdt' => 50000.0,
            'current_amount_bdt' => 15000.0,
            'progress_percent' => 30.0,
            'is_simulated_mode' => true,
        ]);
    }

    public function test_unauthenticated_request_blocked_from_session_api(): void
    {
        $response = $this->getJson('/api/v1/session/requests');
        $response->assertStatus(401);
    }

    public function test_worker_can_fetch_own_requests_via_api(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $otherWorker = User::factory()->create(['role' => 'worker']);

        AssetRequest::create([
            'worker_id'            => $worker->id,
            'status'               => 'draft',
            'requested_amount_bdt' => 20000,
            'reason'               => 'Sewing machine',
            'livelihood_justification' => 'Tailoring',
        ]);

        AssetRequest::create([
            'worker_id'            => $otherWorker->id,
            'status'               => 'draft',
            'requested_amount_bdt' => 30000,
            'reason'               => 'Leather tool',
            'livelihood_justification' => 'Crafting',
        ]);

        $this->actingAs($worker);
        $response = $this->getJson('/api/v1/session/requests');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonPath('data.0.reason', 'Sewing machine');
    }

    public function test_worker_cannot_fetch_other_worker_request_via_api(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $otherWorker = User::factory()->create(['role' => 'worker']);

        $request = AssetRequest::create([
            'worker_id'            => $otherWorker->id,
            'status'               => 'draft',
            'requested_amount_bdt' => 30000,
            'reason'               => 'Secret tool',
            'livelihood_justification' => 'Crafting',
        ]);

        $this->actingAs($worker);
        $response = $this->getJson('/api/v1/session/requests/' . $request->id);
        $response->assertStatus(403);
    }

    public function test_admin_can_fetch_any_request_via_api(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'worker']);

        $request = AssetRequest::create([
            'worker_id'            => $worker->id,
            'status'               => 'draft',
            'requested_amount_bdt' => 25000,
            'reason'               => 'Admin inspected asset',
            'livelihood_justification' => 'Verification',
        ]);

        $this->actingAs($admin);
        $response = $this->getJson('/api/v1/session/requests/' . $request->id);
        $response->assertStatus(200);
        $response->assertJsonPath('data.reason', 'Admin inspected asset');
    }
}
