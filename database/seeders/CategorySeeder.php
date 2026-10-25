<?php

namespace Database\Seeders;

use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
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

        foreach ($categories as $cat) {
            AssetCategory::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
