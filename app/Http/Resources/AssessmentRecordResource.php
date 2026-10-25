<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssessmentRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'suitability_category' => $this->suitability_category,
            'numerical_score'      => (float) $this->numerical_score,
            'explanation'          => $this->explanation_json,
            'method_version'       => $this->method_version,
            'is_ai_generated'      => (bool) $this->is_ai_generated,
            'provider_name'        => $this->provider_name,
            'created_at'           => $this->created_at?->toISOString(),
        ];
    }
}
