<?php

namespace Tests\Feature;

use App\Models\AssetCategory;
use App\Models\AssetRequest;
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
use Tests\TestCase;

class JibikaPhase2AcceptanceTest extends TestCase
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

    /**
     * Test 1: Closed/completed funding pool rejects new pledges.
     */
    public function test_completed_or_closed_pool_rejects_pledges(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $contributor = User::factory()->create(['role' => 'contributor']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'funding_open',
            'requested_amount_bdt' => 10000,
            'reason' => 'Sewing machine',
            'livelihood_justification' => 'Tailoring shop expansion',
        ]);

        $completedPool = FundingPool::create([
            'request_id' => $request->id,
            'target_amount_bdt' => 10000,
            'current_amount_bdt' => 10000,
            'status' => 'completed',
            'is_demo' => true,
            'is_simulated_mode' => true,
        ]);

        $this->actingAs($contributor);
        $response = $this->postWithCsrf("/funding/{$completedPool->id}/contribute", ['amount_bdt' => 500]);
        $response->assertSessionHasErrors(['amount_bdt']);
        $this->assertEquals(10000, $completedPool->fresh()->current_amount_bdt);
    }

    /**
     * Test 2: Pledges exceeding remaining capacity are rejected.
     */
    public function test_pledge_exceeding_remaining_target_is_rejected(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $contributor = User::factory()->create(['role' => 'contributor']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'funding_open',
            'requested_amount_bdt' => 10000,
            'reason' => 'Sewing machine',
            'livelihood_justification' => 'Tailoring shop expansion',
        ]);

        $pool = FundingPool::create([
            'request_id' => $request->id,
            'target_amount_bdt' => 10000,
            'current_amount_bdt' => 8000,
            'status' => 'open',
            'is_demo' => true,
            'is_simulated_mode' => true,
        ]);

        $this->actingAs($contributor);
        // Remaining is 2,000; pledging 2,500 must fail
        $response = $this->postWithCsrf("/funding/{$pool->id}/contribute", ['amount_bdt' => 2500]);
        $response->assertSessionHasErrors(['amount_bdt']);
        $this->assertEquals(8000, $pool->fresh()->current_amount_bdt);

        // Pledging remaining 2,000 succeeds and completes pool and funds request
        $validResponse = $this->postWithCsrf("/funding/{$pool->id}/contribute", ['amount_bdt' => 2000]);
        $validResponse->assertRedirect();
        $this->assertEquals(10000, $pool->fresh()->current_amount_bdt);
        $this->assertEquals('completed', $pool->fresh()->status);
        $this->assertEquals('funded', $request->fresh()->status);
    }

    /**
     * Test 3: Admin can open funding pool on assessed request; non-admin cannot.
     */
    public function test_admin_can_open_funding_pool_for_assessed_request(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $admin = User::factory()->create(['role' => 'admin']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'ai_assessed',
            'requested_amount_bdt' => 25000,
            'reason' => 'Tools',
            'livelihood_justification' => 'Carpentry',
        ]);

        // Worker cannot open pool
        $this->actingAs($worker);
        $denied = $this->postWithCsrf("/requests/{$request->id}/open-funding");
        $denied->assertForbidden();

        // Admin opens pool
        $this->actingAs($admin);
        $response = $this->postWithCsrf("/requests/{$request->id}/open-funding");
        $response->assertRedirect('/funding');
        $this->assertEquals('funding_open', $request->fresh()->status);
        $this->assertNotNull($request->fresh()->funding_pool_id);
        $this->assertDatabaseHas('funding_pools', [
            'request_id' => $request->id,
            'target_amount_bdt' => 25000,
            'status' => 'open',
        ]);
    }

    /**
     * Test 4: Vendor can only quote on funded requests and cannot submit duplicate quotes.
     */
    public function test_vendor_quotation_guards_and_duplicate_prevention(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);

        // Vendor workspace policy check (viewAny)
        $this->actingAs($worker);
        $this->get('/vendor')->assertForbidden();

        $this->actingAs($vendor);
        $this->get('/vendor')->assertOk();

        $unfundedRequest = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'draft',
            'requested_amount_bdt' => 20000,
            'reason' => 'Asset',
            'livelihood_justification' => 'Work',
        ]);

        $this->actingAs($vendor);
        // Quoting on draft fails
        $failResponse = $this->postWithCsrf("/vendor/quote/{$unfundedRequest->id}", [
            'quoted_amount_bdt' => 19000,
        ]);
        $failResponse->assertSessionHasErrors(['quoted_amount_bdt']);

        // Now test on funded request
        $fundedRequest = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'funded',
            'requested_amount_bdt' => 20000,
            'reason' => 'Asset',
            'livelihood_justification' => 'Work',
        ]);

        $successResponse = $this->postWithCsrf("/vendor/quote/{$fundedRequest->id}", [
            'quoted_amount_bdt' => 19500,
            'notes' => 'Genuine equipment with 1-year warranty.',
        ]);
        $successResponse->assertRedirect('/vendor');
        $this->assertDatabaseHas('vendor_quotations', [
            'vendor_id' => $vendor->id,
            'quoted_amount_bdt' => 19500,
            'status' => 'pending',
        ]);

        // Duplicate quote by same vendor on same request must be rejected
        $dupResponse = $this->postWithCsrf("/vendor/quote/{$fundedRequest->id}", [
            'quoted_amount_bdt' => 19000,
        ]);
        $dupResponse->assertSessionHasErrors(['quoted_amount_bdt']);
    }

    /**
     * Test 5: Admin can select quotation, advancing request to procurement.
     */
    public function test_admin_can_select_quotation(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);
        $admin = User::factory()->create(['role' => 'admin']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'funded',
            'requested_amount_bdt' => 30000,
            'reason' => 'Welding generator',
            'livelihood_justification' => 'Metal fabrication',
        ]);

        $this->actingAs($vendor);
        $this->postWithCsrf("/vendor/quote/{$request->id}", ['quoted_amount_bdt' => 29000]);
        $quotation = VendorQuotation::first();

        // Vendor cannot select own quotation
        $denied = $this->postWithCsrf("/admin/quotations/{$quotation->id}/select");
        $denied->assertForbidden();

        // Admin selects quotation
        $this->actingAs($admin);
        $response = $this->postWithCsrf("/admin/quotations/{$quotation->id}/select");
        $response->assertRedirect();
        $this->assertEquals('selected', $quotation->fresh()->status);
        $this->assertEquals('procurement', $request->fresh()->status);
        $this->assertEquals($quotation->procurement_order_id, $request->fresh()->procurement_order_id);
    }

    /**
     * Test 6: Delivery dispute resolution lifecycle (admin resolves deadlocked dispute).
     */
    public function test_delivery_dispute_resolution_workflow(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);
        $admin = User::factory()->create(['role' => 'admin']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'procurement',
            'requested_amount_bdt' => 30000,
            'reason' => 'Equipment',
            'livelihood_justification' => 'Business',
        ]);

        $order = ProcurementOrder::create([
            'request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'status' => 'pending',
            'cost_bdt' => 29000,
        ]);
        $request->update(['procurement_order_id' => $order->id]);

        // Worker disputes delivery
        $this->actingAs($worker);
        $disputeResponse = $this->postWithCsrf("/procurement/{$order->id}/confirm-delivery", [
            'status' => 'disputed',
        ]);
        $disputeResponse->assertRedirect('/procurement');
        $this->assertEquals('delivery_disputed', $request->fresh()->status);
        $this->assertEquals('disputed', $order->fresh()->delivery->status);
        $this->assertEquals('pending', $order->fresh()->status);

        // Worker cannot resolve the dispute (admin only)
        $unauthorizedResolve = $this->postWithCsrf("/procurement/{$order->id}/resolve-dispute", [
            'resolution' => 'confirm_delivered',
            'notes' => 'Attempt by worker',
        ]);
        $unauthorizedResolve->assertForbidden();

        // Admin resolves dispute: confirm_delivered
        $this->actingAs($admin);
        $resolveResponse = $this->postWithCsrf("/procurement/{$order->id}/resolve-dispute", [
            'resolution' => 'confirm_delivered',
            'notes' => 'Mediation complete. Physical delivery verified with local committee.',
        ]);
        $resolveResponse->assertRedirect('/procurement');
        $this->assertEquals('delivered', $request->fresh()->status);
        $this->assertEquals('delivered', $order->fresh()->delivery->status);
        $this->assertEquals('procured', $order->fresh()->status);
    }

    /**
     * Test 7: Dispute resolved as mark_failed transitions request to procurement_failed.
     */
    public function test_dispute_resolution_mark_failed(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);
        $admin = User::factory()->create(['role' => 'admin']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'delivery_disputed',
            'requested_amount_bdt' => 15000,
            'reason' => 'Tool',
            'livelihood_justification' => 'Craft',
        ]);

        $order = ProcurementOrder::create([
            'request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'status' => 'pending',
            'cost_bdt' => 15000,
        ]);

        Delivery::create([
            'procurement_order_id' => $order->id,
            'status' => 'disputed',
            'confirmed_by' => $worker->id,
        ]);

        $this->actingAs($admin);
        $response = $this->postWithCsrf("/procurement/{$order->id}/resolve-dispute", [
            'resolution' => 'mark_failed',
            'notes' => 'Vendor failed to provide replacement. Procurement cancelled.',
        ]);
        $response->assertRedirect('/procurement');
        $this->assertEquals('procurement_failed', $request->fresh()->status);
        $this->assertEquals('failed', $order->fresh()->status);
    }

    /**
     * Test 8: Submitting request creates CommunityVerification and verification updates request status.
     */
    public function test_verification_advances_request_status_and_assigns_queue(): void
    {
        $verifier = User::factory()->create(['role' => 'community', 'status' => 'active']);
        $worker = User::factory()->create(['role' => 'worker', 'status' => 'active']);

        $this->actingAs($worker);
        $cat = AssetCategory::create(['name' => 'Sewing Kit', 'name_bn' => 'সেলাই কিট', 'estimated_cost_bdt' => 20000]);
        $createResponse = $this->postWithCsrf('/requests', [
            'category_id' => $cat->id,
            'requested_amount_bdt' => 20000,
            'reason' => 'Sewing kit',
            'livelihood_justification' => 'Clothing manufacture',
        ]);
        $createResponse->assertRedirect();
        $request = AssetRequest::first();

        // Submit request -> automatically creates CommunityVerification assigned to active verifier
        $submitResponse = $this->postWithCsrf("/requests/{$request->id}/submit");
        $submitResponse->assertRedirect();
        $this->assertEquals('submitted', $request->fresh()->status);
        $this->assertNotNull($request->fresh()->verification_id);

        $verification = CommunityVerification::where('request_id', $request->id)->first();
        $this->assertNotNull($verification);
        $this->assertEquals($verifier->id, $verification->community_member_id);
        $this->assertEquals('pending', $verification->status);

        // Community verifier decides 'verified'
        $this->actingAs($verifier);
        $decideResponse = $this->postWithCsrf("/verification/{$verification->id}/decide", [
            'status' => 'verified',
            'decision_notes' => 'Site visit confirmed valid tailor workshop.',
        ]);
        $decideResponse->assertRedirect('/verification');
        $this->assertEquals('verified', $verification->fresh()->status);
        $this->assertEquals('verified', $request->fresh()->status);

        // Attempting duplicate decision on finalized verification is rejected
        $dupDecide = $this->postWithCsrf("/verification/{$verification->id}/decide", [
            'status' => 'rejected',
            'decision_notes' => 'Changing mind.',
        ]);
        $dupDecide->assertSessionHasErrors(['status']);
        $this->assertEquals('verified', $verification->fresh()->status);
    }

    /**
     * Test 9: Assessment trigger requires verified status and allows owning worker.
     */
    public function test_assessment_trigger_role_and_status_guards(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $otherWorker = User::factory()->create(['role' => 'worker']);

        $unverifiedRequest = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'submitted',
            'requested_amount_bdt' => 15000,
            'reason' => 'Asset',
            'livelihood_justification' => 'Work',
        ]);

        // Worker triggering unverified request gets validation error
        $this->actingAs($worker);
        $failResponse = $this->postWithCsrf("/requests/{$unverifiedRequest->id}/assess-trigger");
        $failResponse->assertSessionHasErrors(['status']);

        // Set status to verified
        $unverifiedRequest->update(['status' => 'verified']);

        // Other worker cannot trigger assessment (403)
        $this->actingAs($otherWorker);
        $forbiddenResponse = $this->postWithCsrf("/requests/{$unverifiedRequest->id}/assess-trigger");
        $forbiddenResponse->assertForbidden();

        // Owning worker triggers assessment successfully
        $this->actingAs($worker);
        $successResponse = $this->postWithCsrf("/requests/{$unverifiedRequest->id}/assess-trigger");
        $successResponse->assertRedirect();
        $this->assertEquals('ai_assessed', $unverifiedRequest->fresh()->status);
        $this->assertNotNull($unverifiedRequest->fresh()->assessment_id);
    }

    /**
     * Test 10: Monitoring records are restricted to post-delivery states and transition delivered to monitoring.
     */
    public function test_monitoring_records_delivery_requirement(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $otherWorker = User::factory()->create(['role' => 'worker']);

        $draftRequest = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'draft',
            'requested_amount_bdt' => 15000,
            'reason' => 'Asset',
            'livelihood_justification' => 'Work',
        ]);

        // Attempting monitoring on draft fails
        $this->actingAs($worker);
        $failResponse = $this->postWithCsrf("/requests/{$draftRequest->id}/monitoring", [
            'metric_name' => 'Weekly Output',
            'metric_value' => '5 garments',
        ]);
        $failResponse->assertSessionHasErrors(['status']);

        // Attempting monitoring on 'procured' (procured but not yet physically delivered) MUST ALSO FAIL
        $draftRequest->update(['status' => 'procured']);
        $failProcured = $this->postWithCsrf("/requests/{$draftRequest->id}/monitoring", [
            'metric_name' => 'Weekly Output',
            'metric_value' => '5 garments',
        ]);
        $failProcured->assertSessionHasErrors(['status']);

        // Attempting monitoring on 'procurement' MUST ALSO FAIL
        $draftRequest->update(['status' => 'procurement']);
        $failProcurement = $this->postWithCsrf("/requests/{$draftRequest->id}/monitoring", [
            'metric_name' => 'Weekly Output',
            'metric_value' => '5 garments',
        ]);
        $failProcurement->assertSessionHasErrors(['status']);

        // On delivered request, other worker cannot submit monitoring (403)
        $draftRequest->update(['status' => 'delivered']);
        $this->actingAs($otherWorker);
        $forbiddenResponse = $this->postWithCsrf("/requests/{$draftRequest->id}/monitoring", [
            'metric_name' => 'Weekly Output',
            'metric_value' => '5 garments',
        ]);
        $forbiddenResponse->assertForbidden();

        // Owning worker submits monitoring on 'delivered': succeeds and transitions to 'monitoring'
        $this->actingAs($worker);
        $successResponse = $this->postWithCsrf("/requests/{$draftRequest->id}/monitoring", [
            'metric_name' => 'Monthly Income',
            'metric_value' => 'BDT 12,000',
            'notes' => 'Productive asset in operation.',
        ]);
        $successResponse->assertRedirect();
        $this->assertDatabaseHas('monitoring_records', [
            'request_id' => $draftRequest->id,
            'metric_name' => 'Monthly Income',
        ]);
        $this->assertEquals('monitoring', $draftRequest->fresh()->status);

        // Subsequent monitoring record on request in 'monitoring' status succeeds and stays 'monitoring'
        $secondSuccess = $this->postWithCsrf("/requests/{$draftRequest->id}/monitoring", [
            'metric_name' => 'Additional Metric',
            'metric_value' => '20 units/week',
            'notes' => 'Follow up visit.',
        ]);
        $secondSuccess->assertRedirect();
        $this->assertEquals('monitoring', $draftRequest->fresh()->status);
    }

    /**
     * Test 11: Zero, negative, and invalid pledges are rejected.
     */
    public function test_zero_negative_and_invalid_pledges_rejected(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $contributor = User::factory()->create(['role' => 'contributor']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'funding_open',
            'requested_amount_bdt' => 10000,
            'reason' => 'Sewing machine',
            'livelihood_justification' => 'Work',
        ]);

        $pool = FundingPool::create([
            'request_id' => $request->id,
            'target_amount_bdt' => 10000,
            'current_amount_bdt' => 0,
            'status' => 'open',
            'is_demo' => true,
            'is_simulated_mode' => true,
        ]);

        $this->actingAs($contributor);

        // Zero pledge
        $resZero = $this->postWithCsrf("/funding/{$pool->id}/contribute", ['amount_bdt' => 0]);
        $resZero->assertSessionHasErrors(['amount_bdt']);

        // Negative pledge
        $resNeg = $this->postWithCsrf("/funding/{$pool->id}/contribute", ['amount_bdt' => -500]);
        $resNeg->assertSessionHasErrors(['amount_bdt']);

        // String / invalid format
        $resInvalid = $this->postWithCsrf("/funding/{$pool->id}/contribute", ['amount_bdt' => 'free-money']);
        $resInvalid->assertSessionHasErrors(['amount_bdt']);

        // Under minimum (min:100)
        $resUnderMin = $this->postWithCsrf("/funding/{$pool->id}/contribute", ['amount_bdt' => 50]);
        $resUnderMin->assertSessionHasErrors(['amount_bdt']);

        $this->assertEquals(0, $pool->fresh()->current_amount_bdt);
    }

    /**
     * Test 12: Community verifier queue isolation - Verifier A cannot decide Verifier B's assignment.
     */
    public function test_community_verifier_queue_isolation(): void
    {
        $verifierA = User::factory()->create(['role' => 'community', 'status' => 'active']);
        $verifierB = User::factory()->create(['role' => 'community', 'status' => 'active']);
        $worker = User::factory()->create(['role' => 'worker', 'status' => 'active']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'submitted',
            'requested_amount_bdt' => 15000,
            'reason' => 'Asset',
            'livelihood_justification' => 'Work',
        ]);

        $verificationForA = CommunityVerification::create([
            'request_id' => $request->id,
            'community_member_id' => $verifierA->id,
            'status' => 'pending',
        ]);

        // Verifier B attempts to decide Verifier A's assigned verification
        $this->actingAs($verifierB);
        $forbiddenDecide = $this->postWithCsrf("/verification/{$verificationForA->id}/decide", [
            'status' => 'verified',
            'decision_notes' => 'Attempted hijack.',
        ]);
        $forbiddenDecide->assertForbidden();

        // Verifier A decides successfully
        $this->actingAs($verifierA);
        $allowedDecide = $this->postWithCsrf("/verification/{$verificationForA->id}/decide", [
            'status' => 'verified',
            'decision_notes' => 'Legitimate verification by assigned verifier.',
        ]);
        $allowedDecide->assertRedirect('/verification');
        $this->assertEquals('verified', $verificationForA->fresh()->status);
    }

    /**
     * Test 13: Cross-user IDOR on notifications - User A cannot mark User B's notification read.
     */
    public function test_cross_user_notification_idor_blocked(): void
    {
        $userA = User::factory()->create(['role' => 'worker']);
        $userB = User::factory()->create(['role' => 'worker']);

        $notifB = Notification::create([
            'user_id' => $userB->id,
            'title' => 'Secret Alert',
            'title_bn' => 'গোপন তথ্য',
            'body_en' => 'Private message for User B',
            'body_bn' => 'ব্যবহারকারী খ এর বার্তা',
            'type' => 'alert',
        ]);

        // User A attempts to mark User B's notification as read
        $this->actingAs($userA);
        $response = $this->postWithCsrf("/notifications/{$notifB->id}/read");
        $response->assertForbidden();
        $this->assertNull($notifB->fresh()->read_at);

        // User B marks own notification as read
        $this->actingAs($userB);
        $success = $this->postWithCsrf("/notifications/{$notifB->id}/read");
        $success->assertRedirect();
        $this->assertNotNull($notifB->fresh()->read_at);
    }

    /**
     * Test 14: Worker profile update strictly isolates to authenticated user.
     */
    public function test_profile_update_strict_isolation(): void
    {
        $worker1 = User::factory()->create(['name' => 'Original Name 1', 'role' => 'worker']);
        $worker2 = User::factory()->create(['name' => 'Original Name 2', 'role' => 'worker']);

        $this->actingAs($worker1);
        $response = $this->putWithCsrf('/profile', [
            'name' => 'Updated Worker 1',
            'occupation' => 'Master Carpenter',
            'experience_years' => 12,
            'location' => 'Dhaka',
            'bio' => 'Experienced artisan.',
        ]);
        $response->assertRedirect('/profile');

        $this->assertEquals('Updated Worker 1', $worker1->fresh()->name);
        $this->assertEquals('Master Carpenter', $worker1->fresh()->workerProfile->occupation);

        // Worker 2 remains unaffected
        $this->assertEquals('Original Name 2', $worker2->fresh()->name);
        $this->assertNull($worker2->fresh()->workerProfile);
    }

    /**
     * Test 15: Complete seamless end-to-end lifecycle purely via HTTP calls (zero manual DB status overrides).
     */
    public function test_seamless_end_to_end_lifecycle_purely_via_http(): void
    {
        // Setup initial entities
        AssetCategory::create([
            'name' => 'Solar Irrigation Pump',
            'name_bn' => 'সৌর সেচ পাম্প',
            'estimated_cost_bdt' => 45000,
            'description' => 'Clean energy irrigation pump for smallholder farmers.',
        ]);

        $verifier = User::factory()->create(['role' => 'community', 'status' => 'active']);
        $worker = User::factory()->create(['role' => 'worker', 'status' => 'active']);
        $contributor = User::factory()->create(['role' => 'contributor', 'status' => 'active']);
        $vendor = User::factory()->create(['role' => 'vendor', 'status' => 'active']);
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'active']);

        // 1. Worker creates draft request
        $this->actingAs($worker);
        $createRes = $this->postWithCsrf('/requests', [
            'category_id' => AssetCategory::first()->id,
            'requested_amount_bdt' => 45000,
            'reason' => 'Need solar pump for year-round crop cultivation.',
            'livelihood_justification' => 'Will reduce diesel pump rental cost by 70%.',
        ]);
        $createRes->assertRedirect();
        $request = AssetRequest::first();
        $this->assertEquals('draft', $request->status);

        // 2. Worker edits draft
        $editRes = $this->putWithCsrf("/requests/{$request->id}", [
            'category_id' => AssetCategory::first()->id,
            'requested_amount_bdt' => 45000,
            'reason' => 'Need 1HP solar pump for year-round crop cultivation.',
            'livelihood_justification' => 'Will reduce diesel pump rental cost by 70% and triple vegetable yields.',
        ]);
        $editRes->assertRedirect();

        // 3. Worker submits request -> automatically creates CommunityVerification assigned to verifier
        $submitRes = $this->postWithCsrf("/requests/{$request->id}/submit");
        $submitRes->assertRedirect();
        $request->refresh();
        $this->assertEquals('submitted', $request->status);
        $this->assertNotNull($request->verification_id);

        // 4. Community verifier decides 'verified' -> request transitions to 'verified'
        $this->actingAs($verifier);
        $verification = CommunityVerification::where('request_id', $request->id)->first();
        $decideRes = $this->postWithCsrf("/verification/{$verification->id}/decide", [
            'status' => 'verified',
            'decision_notes' => 'Agricultural land holding and farm records confirmed.',
        ]);
        $decideRes->assertRedirect('/verification');
        $request->refresh();
        $this->assertEquals('verified', $request->status);

        // 5. Worker triggers assessment -> assessment completed and request transitions to 'ai_assessed'
        $this->actingAs($worker);
        $assessRes = $this->postWithCsrf("/requests/{$request->id}/assess-trigger");
        $assessRes->assertRedirect();
        $request->refresh();
        $this->assertEquals('ai_assessed', $request->status);
        $this->assertNotNull($request->assessment_id);

        // 6. Admin opens funding pool -> request transitions to 'funding_open'
        $this->actingAs($admin);
        $poolRes = $this->postWithCsrf("/requests/{$request->id}/open-funding");
        $poolRes->assertRedirect('/funding');
        $request->refresh();
        $this->assertEquals('funding_open', $request->status);
        $this->assertNotNull($request->funding_pool_id);

        // 7. Contributor pledges full amount -> pool completes -> request transitions to 'funded'
        $this->actingAs($contributor);
        $pool = $request->fundingPool;
        $pledgeRes = $this->postWithCsrf("/funding/{$pool->id}/contribute", [
            'amount_bdt' => 45000,
        ]);
        $pledgeRes->assertRedirect('/funding');
        $pool->refresh();
        $request->refresh();
        $this->assertEquals('completed', $pool->status);
        $this->assertEquals('funded', $request->status);

        // 8. Vendor submits quotation on funded request
        $this->actingAs($vendor);
        $quoteRes = $this->postWithCsrf("/vendor/quote/{$request->id}", [
            'quoted_amount_bdt' => 44000,
            'notes' => 'Complete solar pump system with PV panels and inverter.',
        ]);
        $quoteRes->assertRedirect('/vendor');
        $quotation = VendorQuotation::where('vendor_id', $vendor->id)->first();
        $this->assertNotNull($quotation);
        $this->assertEquals('pending', $quotation->status);

        // 9. Admin selects quotation -> quotation selected -> request transitions to 'procurement'
        $this->actingAs($admin);
        $selectRes = $this->postWithCsrf("/admin/quotations/{$quotation->id}/select");
        $selectRes->assertRedirect();
        $request->refresh();
        $quotation->refresh();
        $this->assertEquals('selected', $quotation->status);
        $this->assertEquals('procurement', $request->status);
        $this->assertNotNull($request->procurement_order_id);

        // 10. Delivery confirmed -> request transitions to 'delivered'
        $this->actingAs($vendor);
        $order = $request->procurementOrder;
        $deliverRes = $this->postWithCsrf("/procurement/{$order->id}/confirm-delivery", [
            'status' => 'delivered',
            'evidence_image_path' => 'deliveries/solar_pump_installation.jpg',
        ]);
        $deliverRes->assertRedirect('/procurement');
        $request->refresh();
        $this->assertEquals('delivered', $request->status);

        // 11. Worker adds post-delivery monitoring metric -> request transitions to 'monitoring'
        $this->actingAs($worker);
        $monitorRes = $this->postWithCsrf("/requests/{$request->id}/monitoring", [
            'metric_name' => 'Acres Irrigated',
            'metric_value' => '3.5 acres',
            'notes' => 'Pump running efficiently on solar power throughout dry season.',
        ]);
        $monitorRes->assertRedirect();
        $request->refresh();
        $this->assertEquals('monitoring', $request->status);
        $this->assertDatabaseHas('monitoring_records', [
            'request_id' => $request->id,
            'metric_name' => 'Acres Irrigated',
            'metric_value' => '3.5 acres',
        ]);
    }
}
