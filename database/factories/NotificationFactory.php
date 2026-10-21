<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence,
            'title_bn' => $this->faker->sentence,
            'body_en' => $this->faker->sentence,
            'body_bn' => $this->faker->sentence,
            'type' => 'info',
            'read_at' => null,
        ];
    }
}
