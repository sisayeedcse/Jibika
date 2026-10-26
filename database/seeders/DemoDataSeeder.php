<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use App\Models\AssetRequest;
use App\Models\AssessmentRecord;
use App\Models\AuditLog;
use App\Models\CommunityProfile;
use App\Models\CommunityVerification;
use App\Models\ContributionOrPledge;
use App\Models\Delivery;
use App\Models\FundingPool;
use App\Models\MonitoringRecord;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Models\VendorProfile;
use App\Models\VendorQuotation;
use App\Models\WorkerProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Asset Categories
        $categories = [
            [
                'name'               => 'Industrial Sewing Machine',
                'name_bn'            => 'শিল্প সেলাই মেশিন',
                'estimated_cost_bdt' => 35000,
                'description'        => 'Heavy-duty automatic needle feed machine for tailors and apparel artisans.',
            ],
            [
                'name'               => 'Solar Water Irrigation Pump',
                'name_bn'            => 'সৌর চালিত সেচ পাম্প',
                'estimated_cost_bdt' => 65000,
                'description'        => 'Submersible solar pump with DC motor for year-round agricultural plot irrigation.',
            ],
            [
                'name'               => 'Commercial Dough Mixer (20L)',
                'name_bn'            => 'বাণিজ্যিক ময়দা মিক্সার',
                'estimated_cost_bdt' => 45000,
                'description'        => 'Spiral high-capacity mixer for micro-bakeries and confectioners.',
            ],
            [
                'name'               => 'Carpentry Power Multi-Tool Set',
                'name_bn'            => 'ছুতারের স্বয়ংক্রিয় সরঞ্জাম সেট',
                'estimated_cost_bdt' => 28000,
                'description'        => 'Circular saw, planer, jigsaw, and sander combo for furniture artisans.',
            ],
            [
                'name'               => 'Commercial Rice Thresher',
                'name_bn'            => 'ধান মাড়াই মেশিন',
                'estimated_cost_bdt' => 42000,
                'description'        => 'Mobile diesel-operated thresher for harvest-season livelihood services.',
            ],
        ];

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[] = AssetCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }

        $demoPassword = config('app.demo_password', 'password');

        // Helper to safely create user without mass-assignment constraints
        $createUser = function (string $name, string $email, string $role) use ($demoPassword) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = new User();
                $user->name     = $name;
                $user->email    = $email;
                $user->password = Hash::make($demoPassword);
                $user->role     = $role;
                $user->status   = 'active';
                $user->save();
            }
            return $user;
        };

        // 2. Demo Users for all 5 roles
        $worker = $createUser('Rahim Uddin', 'worker@demo.local', 'worker');
        WorkerProfile::firstOrCreate(['user_id' => $worker->id], [
            'occupation'       => 'Master Tailor',
            'experience_years' => 8,
            'location'         => 'Chattogram, Bangladesh',
            'bio'              => 'Eight years operating a small neighborhood repair shop. Seeking industrial machine to undertake wholesale school uniform contracts.',
            'verified'         => true,
        ]);

        $worker2 = $createUser('Fatima Begum', 'fatima@demo.local', 'worker');
        WorkerProfile::firstOrCreate(['user_id' => $worker2->id], [
            'occupation'       => 'Artisan Baker',
            'experience_years' => 5,
            'location'         => 'Cumilla, Bangladesh',
            'bio'              => 'Bakes handmade rotis and dry snacks for local markets. A commercial mixer will triple daily batch output.',
            'verified'         => true,
        ]);

        $community = $createUser('Master Anisur Rahman', 'community@demo.local', 'community');
        CommunityProfile::firstOrCreate(['user_id' => $community->id], [
            'full_name' => 'Master Anisur Rahman',
            'location'  => 'Chattogram & Cumilla District Somitte',
        ]);

        $contributor = $createUser('Dr. Tariqul Islam', 'contributor@demo.local', 'contributor');

        $vendor = $createUser('Bengal Machinery & Tools Ltd', 'vendor@demo.local', 'vendor');
        VendorProfile::firstOrCreate(['user_id' => $vendor->id], [
            'company_name'  => 'Bengal Machinery & Tools Ltd',
            'supplier_type' => 'Industrial Tailoring & Agriculture Equipment',
            'verified'      => true,
        ]);

        $admin = $createUser('JIBIKA Lead Administrator', 'admin@demo.local', 'admin');

        // 3. Requests Across Workflow Stages

        // Request 1: Fully Delivered & Monitored
        $req1 = AssetRequest::firstOrCreate(
            ['worker_id' => $worker->id, 'reason' => 'Expansion to wholesale uniform stitching.'],
            [
                'category_id'             => $catModels[0]->id,
                'requested_amount_bdt'    => 35000,
                'livelihood_justification'=> 'Contract with 2 local schools ready for 300 uniforms/year; machine increases stitching rate by 60%.',
                'status'                  => 'delivered',
            ]
        );

        $ver1 = CommunityVerification::firstOrCreate(
            ['request_id' => $req1->id],
            [
                'community_member_id' => $community->id,
                'status'              => 'verified',
                'evidence_notes'      => 'Visited Rahim workshop at Station Road. Validated trade practice and customer log.',
                'decision_notes'      => 'High integrity candidate. Trade necessity confirmed.',
                'verified_at'         => now()->subDays(10),
            ]
        );

        $ass1 = AssessmentRecord::firstOrCreate(
            ['request_id' => $req1->id],
            [
                'suitability_category' => 'HIGH',
                'numerical_score'      => 85.00,
                'explanation_json'     => [
                    'experience' => '8 years demonstrated trade experience.',
                    'demand'     => 'Local wholesale contract verified.',
                    'viability'  => 'Cost matches market baseline for direct productive output.',
                ],
                'method_version'       => 'v1.1-rules',
                'is_ai_generated'      => false,
                'provider_name'        => 'rule-engine',
            ]
        );

        $pool1 = FundingPool::firstOrCreate(
            ['request_id' => $req1->id],
            [
                'target_amount_bdt'  => 35000,
                'current_amount_bdt' => 35000,
                'status'             => 'completed',
                'is_demo'            => true,
                'is_simulated_mode'  => true,
            ]
        );

        ContributionOrPledge::firstOrCreate(
            ['pool_id' => $pool1->id, 'contributor_id' => $contributor->id],
            [
                'amount_bdt'             => 35000,
                'is_confirmed_transaction' => false,
                'is_demo'                => true,
            ]
        );

        $proc1 = ProcurementOrder::firstOrCreate(
            ['request_id' => $req1->id],
            [
                'vendor_id' => $vendor->id,
                'status'    => 'procured',
                'cost_bdt'  => 34500,
            ]
        );

        VendorQuotation::firstOrCreate(
            ['procurement_order_id' => $proc1->id, 'vendor_id' => $vendor->id],
            [
                'quoted_amount_bdt' => 34500,
                'notes'             => 'Brand new Juki high-speed lockstitch machine with direct drive motor and 1-yr service guarantee.',
                'status'            => 'selected',
            ]
        );

        Delivery::firstOrCreate(
            ['procurement_order_id' => $proc1->id],
            [
                'status'              => 'delivered',
                'evidence_image_path' => 'deliveries/handover_rahim_machine.jpg',
                'delivered_at'        => now()->subDays(3),
                'confirmed_by'        => $worker->id,
            ]
        );

        // Real monitoring record for Request 1
        MonitoringRecord::firstOrCreate(
            ['request_id' => $req1->id, 'metric_name' => 'Monthly Net Income'],
            [
                'metric_value' => 'BDT 16,500',
                'notes'        => 'Baseline income was BDT 10,000/month; recorded first 15 days of uniform production.',
                'recorded_at'  => now()->subDay(),
            ]
        );

        // Request 2: Open Funding Pool (Simulated Mode)
        $req2 = AssetRequest::firstOrCreate(
            ['worker_id' => $worker2->id, 'reason' => 'Commercial dough mixer for local confectionery supply.'],
            [
                'category_id'             => $catModels[2]->id,
                'requested_amount_bdt'    => 45000,
                'livelihood_justification'=> 'Currently mixing by hand limits production to 20kg flour/day. Machine enables 60kg/day.',
                'status'                  => 'funding_open',
            ]
        );

        $pool2 = FundingPool::firstOrCreate(
            ['request_id' => $req2->id],
            [
                'target_amount_bdt'  => 45000,
                'current_amount_bdt' => 20000,
                'status'             => 'open',
                'is_demo'            => true,
                'is_simulated_mode'  => true,
            ]
        );

        ContributionOrPledge::firstOrCreate(
            ['pool_id' => $pool2->id, 'contributor_id' => $contributor->id],
            [
                'amount_bdt'             => 20000,
                'is_confirmed_transaction' => false,
                'is_demo'                => true,
            ]
        );

        // Request 3: Pending in Community Verification Queue
        $req3 = AssetRequest::firstOrCreate(
            ['worker_id' => $worker->id, 'reason' => 'Second sewing station for apprentice helper.'],
            [
                'category_id'             => $catModels[3]->id,
                'requested_amount_bdt'    => 28000,
                'livelihood_justification'=> 'Allows hiring a local youth apprentice to double workshop daily throughput.',
                'status'                  => 'submitted',
            ]
        );

        CommunityVerification::firstOrCreate(
            ['request_id' => $req3->id],
            [
                'community_member_id' => $community->id,
                'status'              => 'pending',
                'evidence_notes'      => 'Awaiting initial workshop visit scheduled for this weekend.',
            ]
        );

        // Request 4: Fully funded awaiting vendor quotation
        $req4 = AssetRequest::firstOrCreate(
            ['worker_id' => $worker2->id, 'reason' => 'Irrigation pump for vegetable cultivation.'],
            [
                'category_id'             => $catModels[1]->id,
                'requested_amount_bdt'    => 65000,
                'livelihood_justification'=> 'Enables dry season winter vegetable cropping on 1.5 bigha land.',
                'status'                  => 'funded',
            ]
        );

        // 4. Sample Notifications
        Notification::firstOrCreate(
            ['user_id' => $worker->id, 'title' => 'Asset Delivered Successfully'],
            [
                'title_bn' => 'সম্পদ সফলভাবে বিতরণ করা হয়েছে',
                'body_en'  => 'Your Industrial Sewing Machine has been confirmed as delivered by Bengal Machinery Ltd.',
                'body_bn'  => 'আপনার শিল্প সেলাই মেশিন বিতরণ নিশ্চিত করা হয়েছে।',
                'type'     => 'success',
                'read_at'  => null,
            ]
        );

        Notification::firstOrCreate(
            ['user_id' => $community->id, 'title' => 'New Verification Assigned'],
            [
                'title_bn' => 'নতুন যাচাইকরণ অর্পণ করা হয়েছে',
                'body_en'  => 'Request #3 for Carpentry Power Tool Set has been assigned to your verification desk.',
                'body_bn'  => 'অনুরোধ #৩ আপনার যাচাইকরণ ডেস্কে অর্পণ করা হয়েছে।',
                'type'     => 'info',
                'read_at'  => null,
            ]
        );

        // 5. Audit Log Seed
        AuditLog::firstOrCreate(
            ['action' => 'seed_demo_environment'],
            [
                'user_id'       => $admin->id,
                'model_type'    => 'system',
                'model_id'      => 1,
                'new_state'     => ['status' => 'initialized', 'demo_mode' => true],
                'metadata_json' => ['seeded_by' => 'DemoDataSeeder'],
            ]
        );
    }
}
