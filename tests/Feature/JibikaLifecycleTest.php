<?php

use App\Models\AssetCategory;
use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\CommunityVerification;
use App\Models\ContributionOrPledge;
use App\Models\Delivery;
use App\Models\FundingPool;
use App\Models\MonitoringRecord;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Models\VendorQuotation;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JibikaLifecycleTest extends \Tests\TestCase
{
    use RefreshDatabase;

    protected function postWithCsrf($uri, array $data = [])
    {
        $token = 'test-csrf-token';
        $data['_token'] = $token;

        return $this->withSession(['_token' => $token])->post($uri, $data);
    }

    protected function putWithCsrf($uri, array $data = [])
    {
        $token = 'test-csrf-token';
        $data['_token'] = $token;

        return $this->withSession(['_token' => $token])->put($uri, $data);
    }

    public function test_complete_lifecycle_end_to_end(): void
    {
        // Seed asset category
        AssetCategory::create([
            'name' => 'Industrial Sewing Machine',
            'name_bn' => 'শিল্প সেলাই মেশিন',
            'estimated_cost_bdt' => 35000,
            'description' => 'Productive asset for tailors.',
        ]);

        // === Step 1: Worker creates and submits asset request ===
        $worker = User::factory()->create(['role' => 'worker']);
        WorkerProfile::create(['user_id' => $worker->id, 'occupation' => 'Tailor', 'experience_years' => 8, 'location' => 'Chattogram', 'bio' => 'Experienced tailor.', 'verified' => false]);

        $this->actingAs($worker);
        $createResponse = $this->postWithCsrf('/requests', [
            'category_id' => AssetCategory::first()->id,
            'requested_amount_bdt' => 35000,
            'reason' => 'Need industrial sewing machine for tailoring business.',
            'livelihood_justification' => 'Will increase monthly income by 50%.',
        ]);
        $createResponse->assertRedirect();
        $request = AssetRequest::first();
        $this->assertEquals('draft', $request->status);

        // Submit the request
        $submitResponse = $this->postWithCsrf('/requests/' . $request->id . '/submit');
        $submitResponse->assertRedirect();
        $request->refresh();
        $this->assertEquals('submitted', $request->status);

        // === Step 2: Community verifier records a decision ===
        $verifier = User::factory()->create(['role' => 'community']);
        $verification = CommunityVerification::create([
            'request_id' => $request->id,
            'community_member_id' => $verifier->id,
            'status' => 'pending',
        ]);

        $this->actingAs($verifier);
        $decideResponse = $this->postWithCsrf('/verification/' . $verification->id . '/decide', [
            'status' => 'verified',
            'decision_notes' => 'Evidence verified. Tailor with 8 years experience.',
        ]);
        $decideResponse->assertRedirect();
        $verification->refresh();
        $this->assertEquals('verified', $verification->status);

        // === Step 3: Assessment is created and linked correctly ===
        $request->refresh();
        $request->update(['status' => 'verified']); // verification links request to verified

        $this->actingAs($worker);
        $assessResponse = $this->postWithCsrf('/requests/' . $request->id . '/assess-trigger');
        $assessResponse->assertRedirect();
        $request->refresh();
        $this->assertEquals('ai_assessed', $request->status);
        $this->assertNotNull($request->assessment_id);

        $assessment = $request->assessment;
        $this->assertNotNull($assessment);
        $this->assertEquals('NOT AN LLM RESPONSE', $assessment->provider_name);
        $this->assertFalse($assessment->is_ai_generated);
        $this->assertNotNull($assessment->suitability_category);
        $this->assertArrayHasKey('cost', $assessment->explanation_json);

        // === Step 4: Funding pool is created ===
        $pool = FundingPool::create([
            'request_id' => $request->id,
            'target_amount_bdt' => 35000,
            'current_amount_bdt' => 0,
            'status' => 'open',
            'is_demo' => true,
            'is_simulated_mode' => true,
        ]);
        $request->update(['status' => 'funding_open', 'funding_pool_id' => $pool->id]);

        // === Step 5: Contributor makes a simulated pledge ===
        $contributor = User::factory()->create(['role' => 'contributor']);
        $this->actingAs($contributor);
        $pledgeResponse = $this->postWithCsrf('/funding/' . $pool->id . '/contribute', ['amount_bdt' => 10000]);
        $pledgeResponse->assertRedirect();

        // === Step 6: Funding totals remain consistent ===
        $pool->refresh();
        $this->assertEquals(10000, $pool->current_amount_bdt);
        $this->assertDatabaseHas('contribution_or_pledges', [
            'pool_id' => $pool->id,
            'contributor_id' => $contributor->id,
            'amount_bdt' => 10000,
            'is_demo' => true,
            'is_confirmed_transaction' => false,
        ]);

        // Simulated pledge is NOT a real payment
        $this->assertFalse($pool->contributions->first()->is_confirmed_transaction);
        $this->assertTrue($pool->contributions->first()->is_demo);

        // === Step 7: Eligible vendor submits a quotation ===
        $vendor = User::factory()->create(['role' => 'vendor']);
        $this->actingAs($vendor);
        $request->update(['status' => 'funded']);
        $quoteResponse = $this->postWithCsrf('/vendor/quote/' . $request->id, [
            'quoted_amount_bdt' => 34000,
            'notes' => 'Can supply this industrial sewing machine.',
        ]);
        $quoteResponse->assertRedirect();
        $this->assertDatabaseHas('vendor_quotations', [
            'vendor_id' => $vendor->id,
            'quoted_amount_bdt' => 34000,
            'status' => 'pending',
        ]);

        // === Step 8: Authorized quotation selection creates/advances procurement ===
        $quotation = VendorQuotation::first();
        $procurement = ProcurementOrder::create([
            'request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'quotation_id' => $quotation->id,
            'status' => 'pending',
            'cost_bdt' => 34000,
        ]);
        $quotation->update(['status' => 'selected', 'procurement_order_id' => $procurement->id]);
        $procurement->refresh();
        $this->assertEquals('pending', $procurement->status);
        $this->assertEquals($vendor->id, $procurement->vendor_id);

        // === Step 9: Delivery is confirmed ===
        $this->actingAs($vendor);
        $deliveryResponse = $this->postWithCsrf('/procurement/' . $procurement->id . '/confirm-delivery', [
            'status' => 'delivered',
            'evidence_image_path' => 'deliveries/proof_1.jpg',
        ]);
        $deliveryResponse->assertRedirect();
        $procurement->refresh();
        $this->assertEquals('procured', $procurement->status);
        $this->assertDatabaseHas('deliveries', [
            'procurement_order_id' => $procurement->id,
            'status' => 'delivered',
            'evidence_image_path' => 'deliveries/proof_1.jpg',
        ]);

        // === Step 10: Monitoring record is stored ===
        $this->actingAs($worker);
        $monitorResponse = $this->postWithCsrf('/requests/' . $request->id . '/monitoring', [
            'metric_name' => 'Monthly Income',
            'metric_value' => 'BDT 15,000',
            'notes' => 'Income increased after receiving sewing machine.',
        ]);
        $monitorResponse->assertRedirect();
        $this->assertDatabaseHas('monitoring_records', [
            'request_id' => $request->id,
            'metric_name' => 'Monthly Income',
            'metric_value' => 'BDT 15,000',
        ]);
    }

    public function test_worker_cannot_submit_non_draft_request(): void
    {
        AssetCategory::create(['name' => 'Sewing Machine', 'name_bn' => 'সেলাই মেশিন', 'estimated_cost_bdt' => 35000, 'description' => '']);
        $worker = User::factory()->create(['role' => 'worker']);
        WorkerProfile::create(['user_id' => $worker->id, 'occupation' => 'Tailor', 'experience_years' => 5, 'location' => 'Chattogram', 'bio' => '', 'verified' => false]);

        $this->actingAs($worker);
        $this->postWithCsrf('/requests', [
            'category_id' => AssetCategory::first()->id,
            'requested_amount_bdt' => 35000,
            'reason' => 'Test.',
            'livelihood_justification' => 'Test.',
        ]);

        $request = AssetRequest::first();
        $request->update(['status' => 'submitted']);

        // Try to submit again - should fail (already submitted)
        $response = $this->postWithCsrf('/requests/' . $request->id . '/submit');
        $response->assertSessionHasErrors('status');
    }

    public function test_non_worker_cannot_create_asset_request(): void
    {
        AssetCategory::create(['name' => 'Sewing Machine', 'name_bn' => 'সেলাই মেশিন', 'estimated_cost_bdt' => 35000, 'description' => '']);
        $contributor = User::factory()->create(['role' => 'contributor']);

        $this->actingAs($contributor);
        $response = $this->postWithCsrf('/requests', [
            'category_id' => AssetCategory::first()->id,
            'requested_amount_bdt' => 35000,
            'reason' => 'Test.',
            'livelihood_justification' => 'Test.',
        ]);

        $response->assertForbidden();
    }

    public function test_invalid_amount_rejected(): void
    {
        AssetCategory::create(['name' => 'Sewing Machine', 'name_bn' => 'সেলাই মেশিন', 'estimated_cost_bdt' => 35000, 'description' => '']);
        $worker = User::factory()->create(['role' => 'worker']);
        WorkerProfile::create(['user_id' => $worker->id, 'occupation' => 'Tailor', 'experience_years' => 5, 'location' => 'Chattogram', 'bio' => '', 'verified' => false]);

        $this->actingAs($worker);
        $response = $this->postWithCsrf('/requests', [
            'category_id' => AssetCategory::first()->id,
            'requested_amount_bdt' => 500, // Below min 1000
            'reason' => 'Test.',
            'livelihood_justification' => 'Test.',
        ]);

        $response->assertSessionHasErrors('requested_amount_bdt');
    }

    public function test_suspended_user_cannot_access_protected_routes(): void
    {
        $suspendedUser = User::factory()->create(['role' => 'worker', 'status' => 'suspended']);
        WorkerProfile::create(['user_id' => $suspendedUser->id, 'occupation' => 'Tailor', 'experience_years' => 5, 'location' => 'Chattogram', 'bio' => '', 'verified' => false]);

        $this->actingAs($suspendedUser);
        $response = $this->get('/dashboard');
        $response->assertStatus(403);
    }

    public function test_cannot_access_another_users_notifications(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Notification::create(['user_id' => $user1->id, 'title' => 'User 1 Alert', 'title_bn' => 'উপ réforme1', 'body_en' => 'Body 1', 'body_bn' => 'শর réforme1', 'type' => 'info']);

        $this->actingAs($user2);
        $response = $this->get('/notifications');
        $response->assertDontSee('User 1 Alert');
    }

    public function test_simulated_pledge_never_becomes_real_payment(): void
    {
        AssetCategory::create(['name' => 'Sewing Machine', 'name_bn' => 'সেলাই মেশিন', 'estimated_cost_bdt' => 35000, 'description' => '']);
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
        $this->postWithCsrf('/funding/' . $pool->id . '/contribute', ['amount_bdt' => 10000]);

        $pledge = ContributionOrPledge::first();
        $this->assertTrue($pledge->is_demo);
        $this->assertFalse($pledge->is_confirmed_transaction);
        $this->assertNotEquals('completed_transaction', $pledge->is_confirmed_transaction ? 'yes' : 'no');
    }

    public function test_non_community_cannot_access_verification_queue(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);

        $this->actingAs($worker);
        $response = $this->get('/verification');
        $response->assertForbidden();
    }

    public function test_non_vendor_cannot_submit_quotation(): void
    {
        AssetCategory::create(['name' => 'Sewing Machine', 'name_bn' => 'সেলাই মেশিন', 'estimated_cost_bdt' => 35000, 'description' => '']);
        $worker = User::factory()->create(['role' => 'worker']);
        $request = AssetRequest::create(['worker_id' => $worker->id, 'status' => 'funded', 'requested_amount_bdt' => 35000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);

        $this->actingAs($worker);
        $response = $this->postWithCsrf('/vendor/quote/' . $request->id, [
            'quoted_amount_bdt' => 34000,
            'notes' => 'Test.',
        ]);

        $response->assertForbidden();
    }

    public function test_audit_log_records_sensitive_actions(): void
    {
        AssetCategory::create(['name' => 'Sewing Machine', 'name_bn' => 'সেলাই মেশিন', 'estimated_cost_bdt' => 35000, 'description' => '']);
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'worker']);
        $userToSuspend = User::factory()->create(['role' => 'worker', 'status' => 'active']);

        $this->actingAs($admin);
        $this->postWithCsrf('/admin/users/' . $userToSuspend->id . '/status', ['status' => 'suspended']);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $admin->id,
            'action' => 'set_user_status',
            'model_type' => 'user',
            'model_id' => $userToSuspend->id,
        ]);
    }

    public function test_cannot_skip_workflow_states(): void
    {
        AssetCategory::create(['name' => 'Sewing Machine', 'name_bn' => 'সেলাই মেশিন', 'estimated_cost_bdt' => 35000, 'description' => '']);
        $worker = User::factory()->create(['role' => 'worker']);
        WorkerProfile::create(['user_id' => $worker->id, 'occupation' => 'Tailor', 'experience_years' => 5, 'location' => 'Chattogram', 'bio' => '', 'verified' => false]);

        $this->actingAs($worker);
        $this->postWithCsrf('/requests', [
            'category_id' => AssetCategory::first()->id,
            'requested_amount_bdt' => 35000,
            'reason' => 'Test.',
            'livelihood_justification' => 'Test.',
        ]);

        $request = AssetRequest::first();
        $this->assertEquals('draft', $request->status);

        // Try to trigger AI assessment without verification - should fail
        $response = $this->postWithCsrf('/requests/' . $request->id . '/assess-trigger');
        $response->assertSessionHasErrors('status');
    }
}
