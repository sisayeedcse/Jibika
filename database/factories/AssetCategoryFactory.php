<?php

namespace Database\Factories;

use App\Models\AssetCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetCategoryFactory extends Factory
{
    protected $model = AssetCategory::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'name_bn' => 'বਿਸ਼েষ আαιটি',
            'estimated_cost_bdt' => $this->faker->numberBetween(10000, 100000),
            'description' => $this->faker->sentence,
        ];
    }
}
