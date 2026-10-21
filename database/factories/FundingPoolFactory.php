<?php

namespace Database\Factories;

use App\Models\FundingPool;
use Illuminate\Database\Eloquent\Factories\Factory;

class FundingPoolFactory extends Factory
{
    protected $model = FundingPool::class;

    public function definition(): array
    {
        return [
            'request_id' => \App\Models\AssetRequest::factory(),
            'target_amount_bdt' => $this->faker->numberBetween(10000, 100000),
            'current_amount_bdt' => 0,
            'status' => 'open',
            'is_demo' => true,
            'is_simulated_mode' => true,
        ];
    }
}
