<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Models\AssetCategory;
use App\Models\AssetRequest;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Demo category
        $cat = AssetCategory::create([
            'name' => 'Industrial Sewing Machine',
            'name_bn' => 'শিল্প সেলাই মেশিন',
            'estimated_cost_bdt' => 35000,
            'description' => 'Productive asset for tailors.',
        ]);

        // Demo worker: Rahim
        $rahim = User::firstOrCreate(
            ['email' => 'rahim@demo.local'],
            [
                'name' => 'Rahim Uddin',
                'password' => bcrypt('password'),
                'role' => 'worker',
                'status' => 'active',
            ]
        );
        if (!WorkerProfile::where('user_id', $rahim->id)->exists()) {
            WorkerProfile::create([
                'user_id' => $rahim->id,
                'occupation' => 'Tailor',
                'experience_years' => 8,
                'location' => 'Chattogram',
                'bio' => 'Experienced tailor seeking productive asset.',
                'verified' => true,
            ]);
        }

        // Demo request
        $request = AssetRequest::create([
            'worker_id' => $rahim->id,
            'category_id' => $cat->id,
            'status' => 'funding_open',
            'requested_amount_bdt' => 35000,
            'reason' => 'Need industrial sewing machine to expand business.',
            'livelihood_justification' => 'Will increase monthly income.',
        ]);
    }
}
