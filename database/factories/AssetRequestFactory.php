<?php

namespace Database\Factories;

use App\Models\AssetRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetRequestFactory extends Factory
{
    protected $model = AssetRequest::class;

    public function definition(): array
    {
        return [
            'worker_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\AssetCategory::factory(),
            'status' => 'draft',
            'requested_amount_bdt' => $this->faker->numberBetween(10000, 100000),
            'reason' => $this->faker->sentence,
            'livelihood_justification' => $this->faker->sentence,
        ];
    }
}
