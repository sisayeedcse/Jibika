<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                       => $this->id,
            'worker_id'                => $this->worker_id,
            'status'                   => $this->status,
            'requested_amount_bdt'     => (float) $this->requested_amount_bdt,
            'reason'                   => $this->reason,
            'livelihood_justification' => $this->livelihood_justification,
            'category'                 => new AssetCategoryResource($this->whenLoaded('category')),
            'verification'             => new CommunityVerificationResource($this->whenLoaded('verification')),
            'assessment'               => new AssessmentRecordResource($this->whenLoaded('assessment')),
            'funding_pool_id'          => $this->funding_pool_id,
            'procurement_order_id'     => $this->procurement_order_id,
            'created_at'               => $this->created_at?->toISOString(),
            'updated_at'               => $this->updated_at?->toISOString(),
        ];
    }
}
