<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FundingPoolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $target = (float) $this->target_amount_bdt;
        $current = (float) $this->current_amount_bdt;
        $progressPct = $target > 0 ? min(100.0, round(($current / $target) * 100, 1)) : 0;

        return [
            'id'                 => $this->id,
            'request_id'         => $this->request_id,
            'target_amount_bdt'  => $target,
            'current_amount_bdt' => $current,
            'progress_percent'   => $progressPct,
            'status'             => $this->status,
            'is_simulated_mode'  => (bool) $this->is_simulated_mode,
            'disclaimer'         => 'Simulated funding mode only. Not a real financial transaction.',
            'request'            => new AssetRequestResource($this->whenLoaded('request')),
            'created_at'         => $this->created_at?->toISOString(),
        ];
    }
}
