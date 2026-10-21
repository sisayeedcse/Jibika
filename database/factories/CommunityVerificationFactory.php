<?php

namespace Database\Factories;

use App\Models\CommunityVerification;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunityVerificationFactory extends Factory
{
    protected $model = CommunityVerification::class;

    public function definition(): array
    {
        return [
            'request_id' => \App\Models\AssetRequest::factory(),
            'community_member_id' => \App\Models\User::factory(),
            'status' => 'pending',
            'evidence_notes' => $this->faker->sentence,
            'decision_notes' => null,
            'verified_at' => null,
        ];
    }
}
