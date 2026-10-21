<?php

use App\Models\AssetCategory;
use App\Models\AssetRequest;
use App\Models\CommunityVerification;
use App\Models\ContributionOrPledge;
use App\Models\FundingPool;
use App\Models\Notification;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JibikaWorkflowTest extends \Tests\TestCase
{
    use RefreshDatabase;
    public function test_registration_creates_user_and_redirects(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'worker',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'role' => 'worker']);
    }

    public function test_login_and_logout_work(): void
    {
        $user = User::factory()->create(['email' => 'login@example.com', 'password' => bcrypt('password123')]);

        $response = $this->post('/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout');
        $this->assertGuest();
    }

    public function test_protected_routes_require_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/requests')->assertRedirect('/login');
    }

    public function test_worker_can_create_and_view_own_asset_request(): void
    {
        $user = User::factory()->create(['role' => 'worker']);
        WorkerProfile::create(['user_id' => $user->id, 'occupation' => 'Tailor', 'experience_years' => 5, 'location' => 'Chattogram', 'bio' => '', 'verified' => false]);
        AssetCategory::create(['name' => 'Industrial Sewing Machine', 'name_bn' => 'শিল্প সেলাই মেশিন', 'estimated_cost_bdt' => 35000, 'description' => '']);

        $this->actingAs($user);

        $response = $this->post('/requests', [
            'category_id' => AssetCategory::first()->id,
            'requested_amount_bdt' => 35000,
            'reason' => 'Need asset for livelihood.',
            'livelihood_justification' => 'Will increase income.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('asset_requests', [
            'worker_id' => $user->id,
            'status' => 'draft',
            'requested_amount_bdt' => 35000,
        ]);
    }

    public function test_worker_cannot_update_request_after_submission(): void
    {
        $user = User::factory()->create(['role' => 'worker']);
        WorkerProfile::create(['user_id' => $user->id, 'occupation' => 'Tailor', 'experience_years' => 5, 'location' => 'Chattogram', 'bio' => '', 'verified' => false]);

        $request = AssetRequest::create([
            'worker_id' => $user->id,
            'status' => 'submitted',
            'requested_amount_bdt' => 35000,
            'reason' => 'Test.',
            'livelihood_justification' => 'Test.',
        ]);

        $this->actingAs($user);
        $response = $this->put('/requests/' . $request->id, [
            'category_id' => AssetCategory::create(['name' => 'Test Cat', 'name_bn' => 'টেস্ট', 'estimated_cost_bdt' => 1000])->id,
            'requested_amount_bdt' => 999999,
            'reason' => 'Updated reason.',
            'livelihood_justification' => 'Updated justification.',
        ]);

        $response->assertForbidden();
    }

    public function test_cross_user_access_denied_for_asset_request(): void
    {
        $owner = User::factory()->create(['role' => 'worker']);
        $other = User::factory()->create(['role' => 'worker']);

        $request = AssetRequest::create(['worker_id' => $owner->id, 'status' => 'draft', 'requested_amount_bdt' => 35000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);

        $this->actingAs($other);
        $response = $this->get('/requests/' . $request->id);
        $response->assertForbidden();
    }

    public function test_community_verification_workflow(): void
    {
        $verifier = User::factory()->create(['role' => 'community']);
        $worker = User::factory()->create(['role' => 'worker']);
        $request = AssetRequest::create(['worker_id' => $worker->id, 'status' => 'submitted', 'requested_amount_bdt' => 35000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);
        $verification = CommunityVerification::create([
            'request_id' => $request->id,
            'community_member_id' => $verifier->id,
            'status' => 'pending',
        ]);

        $this->actingAs($verifier);
        $response = $this->post('/verification/' . $verification->id . '/decide', [
            'status' => 'verified',
            'decision_notes' => 'Verified successfully.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('community_verifications', [
            'id' => $verification->id,
            'status' => 'verified',
        ]);
    }

    public function test_non_community_user_cannot_verify(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $request = AssetRequest::create(['worker_id' => $worker->id, 'status' => 'submitted', 'requested_amount_bdt' => 35000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);
        $verification = CommunityVerification::create([
            'request_id' => $request->id,
            'community_member_id' => $worker->id,
            'status' => 'pending',
        ]);

        $this->actingAs($worker);
        $response = $this->post('/verification/' . $verification->id . '/decide', [
            'status' => 'verified',
            'decision_notes' => 'Test.',
        ]);

        $response->assertForbidden();
    }

    public function test_assessment_fallback_is_labeled_correctly(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $request = AssetRequest::create(['worker_id' => $worker->id, 'status' => 'verified', 'requested_amount_bdt' => 35000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);

        $this->actingAs($worker);
        $response = $this->post('/requests/' . $request->id . '/assess-trigger');

        $response->assertRedirect();
        $this->assertDatabaseHas('assessment_records', [
            'request_id' => $request->id,
            'provider_name' => 'NOT AN LLM RESPONSE',
            'is_ai_generated' => false,
        ]);
    }

    public function test_simulated_pledge_consistency(): void
    {
        $contributor = User::factory()->create(['role' => 'contributor']);
        $worker = User::factory()->create(['role' => 'worker']);
        $request = AssetRequest::create(['worker_id' => $worker->id, 'status' => 'ai_assessed', 'requested_amount_bdt' => 35000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);
        $pool = FundingPool::create([
            'request_id' => $request->id,
            'target_amount_bdt' => 35000,
            'current_amount_bdt' => 0,
            'status' => 'open',
            'is_demo' => true,
            'is_simulated_mode' => true,
        ]);

        $this->actingAs($contributor);
        $this->post('/funding/' . $pool->id . '/contribute', ['amount_bdt' => 10000]);

        $this->assertDatabaseHas('contribution_or_pledges', [
            'pool_id' => $pool->id,
            'contributor_id' => $contributor->id,
            'is_demo' => true,
            'is_confirmed_transaction' => false,
        ]);
    }

    public function test_vendor_quotation_submission(): void
    {
        $vendor = User::factory()->create(['role' => 'vendor']);
        $worker = User::factory()->create(['role' => 'worker']);
        $request = AssetRequest::create(['worker_id' => $worker->id, 'status' => 'funded', 'requested_amount_bdt' => 35000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);

        $this->actingAs($vendor);
        $response = $this->post('/vendor/quote/' . $request->id, [
            'quoted_amount_bdt' => 34000,
            'notes' => 'Can supply this asset.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vendor_quotations', [
            'vendor_id' => $vendor->id,
            'quoted_amount_bdt' => 34000,
        ]);
    }

    public function test_notification_visibility_is_user_scoped(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Notification::create(['user_id' => $user1->id, 'title' => 'User 1 Alert', 'title_bn' => 'উপ reform1', 'body_en' => 'Body 1', 'body_bn' => 'শর reform1', 'type' => 'info']);
        Notification::create(['user_id' => $user2->id, 'title' => 'User 2 Alert', 'title_bn' => 'উপ reform2', 'body_en' => 'Body 2', 'body_bn' => 'শর reform2', 'type' => 'info']);

        $this->actingAs($user1);
        $response = $this->get('/notifications');
        $response->assertSee('User 1 Alert');
        $response->assertDontSee('User 2 Alert');
    }
}
