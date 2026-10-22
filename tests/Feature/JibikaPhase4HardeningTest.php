<?php

namespace Tests\Feature;

use App\Models\AssetCategory;
use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\ContributionOrPledge;
use App\Models\FundingPool;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Models\VendorQuotation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JibikaPhase4HardeningTest extends TestCase
{
    use RefreshDatabase;

    protected function postWithCsrf($uri, array $data = [])
    {
        $token = 'test-csrf-token';
        $data['_token'] = $token;
        return $this->withSession(['_token' => $token])->post($uri, $data);
    }

    /**
     * Test 1: Competing vendor quotations; selecting one awards contract,
     * marks winner 'selected', advances request to 'procurement', marks competitor
     * 'rejected', marks competing provisional order 'failed', and prevents duplicate active orders.
     */
    public function test_competing_vendor_quotations_and_exclusive_contract_award(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'worker']);
        $vendorA = User::factory()->create(['role' => 'vendor', 'name' => 'Vendor Alpha']);
        $vendorB = User::factory()->create(['role' => 'vendor', 'name' => 'Vendor Beta']);

        $category = AssetCategory::create([
            'name' => 'Machinery',
            'name_bn' => 'যন্ত্রপাতি',
            'estimated_cost_bdt' => 30000,
            'description' => 'Industrial sewing machine',
        ]);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'category_id' => $category->id,
            'status' => 'funded',
            'requested_amount_bdt' => 30000,
            'reason' => 'Tailoring business expansion',
            'livelihood_justification' => 'Direct sewing work',
        ]);

        // Vendor A submits quote
        $this->actingAs($vendorA);
        $resA = $this->postWithCsrf("/vendor/quote/{$request->id}", [
            'quoted_amount_bdt' => 29000,
            'notes' => 'Quotation from Alpha',
        ]);
        $resA->assertRedirect();

        // Vendor B submits competing quote
        $this->actingAs($vendorB);
        $resB = $this->postWithCsrf("/vendor/quote/{$request->id}", [
            'quoted_amount_bdt' => 28500,
            'notes' => 'Quotation from Beta (better price)',
        ]);
        $resB->assertRedirect();

        $quoteA = VendorQuotation::where('vendor_id', $vendorA->id)->first();
        $quoteB = VendorQuotation::where('vendor_id', $vendorB->id)->first();

        $this->assertNotNull($quoteA);
        $this->assertNotNull($quoteB);
        $this->assertEquals('pending', $quoteA->status);
        $this->assertEquals('pending', $quoteB->status);

        // Both provisional orders exist initially
        $orderA = ProcurementOrder::where('quotation_id', $quoteA->id)->first();
        $orderB = ProcurementOrder::where('quotation_id', $quoteB->id)->first();
        $this->assertNotNull($orderA);
        $this->assertNotNull($orderB);

        // Admin selects Vendor B
        $this->actingAs($admin);
        $selectRes = $this->postWithCsrf("/admin/quotations/{$quoteB->id}/select");
        $selectRes->assertRedirect();
        $selectRes->assertSessionHas('success');

        // Winner verification
        $quoteB->refresh();
        $orderB->refresh();
        $this->assertEquals('selected', $quoteB->status);
        $this->assertEquals('pending', $orderB->status);

        // Request advanced and tied specifically to Winner's order
        $request->refresh();
        $this->assertEquals('procurement', $request->status);
        $this->assertEquals($orderB->id, $request->procurement_order_id);

        // Competitor verification (Vendor A auto-rejected, provisional order marked failed)
        $quoteA->refresh();
        $orderA->refresh();
        $this->assertEquals('rejected', $quoteA->status);
        $this->assertEquals('failed', $orderA->status);

        // Audit log recorded
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'select_quotation',
            'model_type' => 'vendor_quotation',
            'model_id' => $quoteB->id,
        ]);
    }

    /**
     * Test 2: Repeated selection on already awarded request is rejected with session errors.
     */
    public function test_repeated_selection_on_already_awarded_request_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'procurement',
            'requested_amount_bdt' => 25000,
            'reason' => 'Textile',
            'livelihood_justification' => 'Textile',
        ]);

        $order = ProcurementOrder::create([
            'request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'status' => 'pending',
            'cost_bdt' => 25000,
        ]);
        $request->update(['procurement_order_id' => $order->id]);

        $quotation = VendorQuotation::create([
            'request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'quoted_amount_bdt' => 25000,
            'status' => 'selected',
            'procurement_order_id' => $order->id,
        ]);

        $this->actingAs($admin);
        $response = $this->postWithCsrf("/admin/quotations/{$quotation->id}/select");
        $response->assertRedirect();
        $response->assertSessionHasErrors(['status']);

        // State remains intact
        $this->assertEquals('procurement', $request->fresh()->status);
        $this->assertEquals($order->id, $request->fresh()->procurement_order_id);
    }

    /**
     * Test 3: Quotation selection on non-funded request is rejected.
     */
    public function test_quotation_selection_on_non_funded_request_is_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'under_review',
            'requested_amount_bdt' => 20000,
            'reason' => 'Agriculture',
            'livelihood_justification' => 'Farming',
        ]);

        $quotation = VendorQuotation::create([
            'request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'quoted_amount_bdt' => 20000,
            'status' => 'pending',
        ]);

        $this->actingAs($admin);
        $response = $this->postWithCsrf("/admin/quotations/{$quotation->id}/select");
        $response->assertRedirect();
        $response->assertSessionHasErrors(['status']);

        $this->assertEquals('pending', $quotation->fresh()->status);
        $this->assertEquals('under_review', $request->fresh()->status);
        $this->assertNull($request->fresh()->procurement_order_id);
    }

    /**
     * Test 4: Quoting on an already awarded or non-funded request is rejected.
     */
    public function test_quoting_on_awarded_or_non_funded_request_is_rejected(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);

        // Case A: Draft status
        $draftRequest = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'draft',
            'requested_amount_bdt' => 15000,
            'reason' => 'Draft asset',
            'livelihood_justification' => 'Draft',
        ]);

        $this->actingAs($vendor);
        $responseDraft = $this->postWithCsrf("/vendor/quote/{$draftRequest->id}", [
            'quoted_amount_bdt' => 14000,
            'notes' => 'Cannot quote on draft',
        ]);
        $responseDraft->assertRedirect();
        $responseDraft->assertSessionHasErrors(['quoted_amount_bdt']);

        // Case B: Already in procurement
        $awardedRequest = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'funded',
            'requested_amount_bdt' => 15000,
            'reason' => 'Awarded asset',
            'livelihood_justification' => 'Awarded',
        ]);
        $order = ProcurementOrder::create([
            'request_id' => $awardedRequest->id,
            'vendor_id' => $vendor->id,
            'status' => 'pending',
            'cost_bdt' => 15000,
        ]);
        $awardedRequest->update([
            'status' => 'procurement',
            'procurement_order_id' => $order->id,
        ]);

        $responseAwarded = $this->postWithCsrf("/vendor/quote/{$awardedRequest->id}", [
            'quoted_amount_bdt' => 14000,
            'notes' => 'Cannot quote on awarded',
        ]);
        $responseAwarded->assertRedirect();
        $responseAwarded->assertSessionHasErrors(['quoted_amount_bdt']);

        $this->assertDatabaseMissing('vendor_quotations', [
            'request_id' => $awardedRequest->id,
        ]);
    }

    /**
     * Test 5: Confirming delivery on unselected or failed provisional order is rejected.
     */
    public function test_confirming_delivery_on_unselected_or_failed_order_is_rejected(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'funded',
            'requested_amount_bdt' => 20000,
            'reason' => 'Test',
            'livelihood_justification' => 'Test',
        ]);

        // Pending quotation (not selected yet)
        $quote = VendorQuotation::create([
            'request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'quoted_amount_bdt' => 19000,
            'status' => 'pending',
        ]);

        $order = ProcurementOrder::create([
            'request_id' => $request->id,
            'vendor_id' => $vendor->id,
            'quotation_id' => $quote->id,
            'status' => 'pending',
            'cost_bdt' => 19000,
        ]);

        $this->actingAs($vendor);
        $resPending = $this->postWithCsrf("/procurement/{$order->id}/confirm-delivery", [
            'status' => 'delivered',
        ]);
        $resPending->assertRedirect();
        $resPending->assertSessionHasErrors(['status']);
        $this->assertDatabaseMissing('deliveries', ['procurement_order_id' => $order->id]);

        // Failed order
        $order->update(['status' => 'failed']);
        $quote->update(['status' => 'rejected']);

        $resFailed = $this->postWithCsrf("/procurement/{$order->id}/confirm-delivery", [
            'status' => 'delivered',
        ]);
        $resFailed->assertRedirect();
        $resFailed->assertSessionHasErrors(['status']);
        $this->assertDatabaseMissing('deliveries', ['procurement_order_id' => $order->id]);
    }

    /**
     * Test 6: AuditLog immutability and credential redaction.
     */
    public function test_audit_log_model_immutability_and_redaction(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Create log entry with sensitive fields
        $log = AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'admin_credential_test',
            'model_type' => 'user',
            'model_id' => $admin->id,
            'previous_state' => [
                'password' => 'plaintext_secret_123',
                'remember_token' => 'token_xyz_abc',
                'name' => 'Original Admin',
            ],
            'new_state' => [
                'password' => 'new_secret_456',
                'secret' => 'top_secret_api_key',
                'name' => 'Updated Admin',
            ],
        ]);

        // Assert redaction
        $log->refresh();
        $this->assertEquals('[REDACTED]', $log->previous_state['password']);
        $this->assertEquals('[REDACTED]', $log->previous_state['remember_token']);
        $this->assertEquals('Original Admin', $log->previous_state['name']);

        $this->assertEquals('[REDACTED]', $log->new_state['password']);
        $this->assertEquals('[REDACTED]', $log->new_state['secret']);
        $this->assertEquals('Updated Admin', $log->new_state['name']);

        // Assert immutability on update
        $threwUpdate = false;
        try {
            $log->update(['action' => 'tampered_action']);
        } catch (\RuntimeException $e) {
            $threwUpdate = true;
            $this->assertStringContainsString('append-only', $e->getMessage());
        }
        $this->assertTrue($threwUpdate, 'AuditLog update did not throw RuntimeException.');

        // Assert immutability on delete
        $threwDelete = false;
        try {
            $log->delete();
        } catch (\RuntimeException $e) {
            $threwDelete = true;
            $this->assertStringContainsString('append-only', $e->getMessage());
        }
        $this->assertTrue($threwDelete, 'AuditLog delete did not throw RuntimeException.');
    }

    /**
     * Test 7: Funding concurrency lock rejects pledges exceeding remaining needed target.
     */
    public function test_funding_concurrency_lock_and_target_boundary(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $contributor = User::factory()->create(['role' => 'contributor']);

        $request = AssetRequest::create([
            'worker_id' => $worker->id,
            'status' => 'funding_open',
            'requested_amount_bdt' => 20000,
            'reason' => 'Electric Loom',
            'livelihood_justification' => 'Weaving',
        ]);

        $pool = FundingPool::create([
            'request_id' => $request->id,
            'target_amount_bdt' => 20000,
            'current_amount_bdt' => 15000,
            'status' => 'open',
        ]);

        $this->actingAs($contributor);

        // Attempt pledge exceeding remaining required amount (5000 remaining)
        $resOver = $this->postWithCsrf("/funding/{$pool->id}/contribute", [
            'amount_bdt' => 5001,
            'is_anonymous' => false,
        ]);
        $resOver->assertRedirect();
        $resOver->assertSessionHasErrors(['amount_bdt']);
        $this->assertEquals(15000, $pool->fresh()->current_amount_bdt);

        // Attempt exact remaining amount
        $resExact = $this->postWithCsrf("/funding/{$pool->id}/contribute", [
            'amount_bdt' => 5000,
            'is_anonymous' => false,
        ]);
        $resExact->assertRedirect();
        $resExact->assertSessionHas('success');

        $pool->refresh();
        $request->refresh();
        $this->assertEquals(20000, $pool->current_amount_bdt);
        $this->assertEquals('completed', $pool->status);
        $this->assertEquals('funded', $request->status);
    }

    /**
     * Test 8: Custom branded error views render for 403 and 404 without debug leaks.
     */
    public function test_custom_branded_error_views_for_403_and_404(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);

        // 403 Forbidden check (Worker trying to access Admin Users panel)
        $this->actingAs($worker);
        $res403 = $this->get('/admin/users');
        $res403->assertStatus(403);
        $res403->assertSee('403 Forbidden');
        $res403->assertSee('Access Denied');
        $res403->assertSee('JIBIKA');

        // 404 Not Found check
        $res404 = $this->get('/non-existent-safe-route-for-testing-404');
        $res404->assertStatus(404);
        $res404->assertSee('404 Not Found');
        $res404->assertSee('Page Not Found');
        $res404->assertSee('JIBIKA');
    }
}
