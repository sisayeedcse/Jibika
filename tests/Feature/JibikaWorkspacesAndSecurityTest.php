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

class JibikaWorkspacesAndSecurityTest extends TestCase
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

    public function test_all_five_roles_render_their_respective_workspace_dashboards(): void
    {
        $roles = ['worker', 'community', 'contributor', 'vendor', 'admin'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            if ($role === 'worker') {
                WorkerProfile::create(['user_id' => $user->id, 'occupation' => 'Carpenter', 'experience_years' => 4, 'location' => 'Dhaka', 'bio' => '', 'verified' => false]);
            }

            $this->actingAs($user);
            $response = $this->get('/dashboard');
            $response->assertStatus(200);
            $response->assertSee(ucfirst($role));
        }
    }

    public function test_registration_blocks_privileged_admin_or_community_role_escalation(): void
    {
        // Attempting to self-register as admin should fail validation
        $response = $this->postWithCsrf('/register', [
            'name'                  => 'Attacker',
            'email'                 => 'attacker@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'admin',
        ]);

        $response->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', [
            'email' => 'attacker@example.com',
        ]);
    }

    public function test_draft_request_edit_view_renders_and_updates(): void
    {
        $cat = AssetCategory::create(['name' => 'Drill Machine', 'name_bn' => 'ড্রিল মেশিন', 'estimated_cost_bdt' => 12000, 'description' => 'Power tool']);
        $worker = User::factory()->create(['role' => 'worker']);
        WorkerProfile::create(['user_id' => $worker->id, 'occupation' => 'Electrician', 'experience_years' => 3, 'location' => 'Sylhet', 'bio' => '', 'verified' => false]);

        $request = AssetRequest::create([
            'worker_id'            => $worker->id,
            'category_id'          => $cat->id,
            'status'               => 'draft',
            'requested_amount_bdt' => 12000,
            'reason'               => 'Initial reason',
            'livelihood_justification' => 'Initial impact',
        ]);

        $this->actingAs($worker);

        // Edit view should render 200
        $editView = $this->get('/requests/' . $request->id . '/edit');
        $editView->assertStatus(200);
        $editView->assertSee('Initial reason');

        // Update draft
        $updateResp = $this->putWithCsrf('/requests/' . $request->id, [
            'category_id'          => $cat->id,
            'requested_amount_bdt' => 14000,
            'reason'               => 'Updated reason',
            'livelihood_justification' => 'Updated impact',
        ]);
        $updateResp->assertRedirect();

        $request->refresh();
        $this->assertEquals(14000, $request->requested_amount_bdt);
        $this->assertEquals('Updated reason', $request->reason);
    }

    public function test_profile_show_and_edit_views_render_and_persist(): void
    {
        $worker = User::factory()->create(['name' => 'Jamal Hossain', 'role' => 'worker']);
        WorkerProfile::create(['user_id' => $worker->id, 'occupation' => 'Weaver', 'experience_years' => 10, 'location' => 'Tangail', 'bio' => 'Handloom artisan', 'verified' => true]);

        $this->actingAs($worker);

        $showResp = $this->get('/profile');
        $showResp->assertStatus(200);
        $showResp->assertSee('Jamal Hossain');
        $showResp->assertSee('Weaver');

        $editResp = $this->get('/profile/edit');
        $editResp->assertStatus(200);
        $editResp->assertSee('Tangail');

        $updateResp = $this->putWithCsrf('/profile', [
            'name'             => 'Jamal Hossain Master',
            'occupation'       => 'Master Weaver',
            'experience_years' => 12,
            'location'         => 'Tangail Sadar',
            'bio'              => 'Heritage jamdani master weaver.',
        ]);
        $updateResp->assertRedirect();

        $worker->refresh();
        $this->assertEquals('Jamal Hossain Master', $worker->name);
        $this->assertEquals('Master Weaver', $worker->workerProfile->occupation);
        $this->assertEquals(12, $worker->workerProfile->experience_years);
    }

    public function test_non_contributor_cannot_make_pledge(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $request = AssetRequest::create(['worker_id' => $worker->id, 'status' => 'funding_open', 'requested_amount_bdt' => 20000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);
        $pool = FundingPool::create([
            'request_id'        => $request->id,
            'target_amount_bdt' => 20000,
            'current_amount_bdt'=> 0,
            'status'            => 'open',
            'is_demo'           => true,
            'is_simulated_mode' => true,
        ]);

        // Attempting to pledge as a worker should return 403 Forbidden
        $this->actingAs($worker);
        $resp = $this->postWithCsrf('/funding/' . $pool->id . '/contribute', [
            'amount_bdt' => 5000,
        ]);

        $resp->assertStatus(403);
    }

    public function test_delivery_dispute_updates_request_status(): void
    {
        $worker = User::factory()->create(['role' => 'worker']);
        $vendor = User::factory()->create(['role' => 'vendor']);
        $request = AssetRequest::create(['worker_id' => $worker->id, 'status' => 'procurement', 'requested_amount_bdt' => 20000, 'reason' => 'Test', 'livelihood_justification' => 'Test']);

        $procurement = ProcurementOrder::create([
            'request_id' => $request->id,
            'vendor_id'  => $vendor->id,
            'status'     => 'pending',
            'cost_bdt'   => 19500,
        ]);

        $this->actingAs($vendor);
        $resp = $this->postWithCsrf('/procurement/' . $procurement->id . '/confirm-delivery', [
            'status'              => 'disputed',
            'evidence_image_path' => 'evidence/damaged.jpg',
        ]);

        $resp->assertRedirect();

        $request->refresh();
        $this->assertEquals('delivery_disputed', $request->status);

        $this->assertDatabaseHas('deliveries', [
            'procurement_order_id' => $procurement->id,
            'status'               => 'disputed',
        ]);
    }
}
