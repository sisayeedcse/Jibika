<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunityVerificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'status'         => $this->status,
            'decision_notes' => $this->decision_notes,
            'evidence_notes' => $this->evidence_notes,
            'verified_at'    => $this->verified_at?->toISOString(),
            'created_at'     => $this->created_at?->toISOString(),
        ];
    }
}
