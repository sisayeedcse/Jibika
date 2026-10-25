<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'name'               => $this->name,
            'name_bn'            => $this->name_bn,
            'estimated_cost_bdt' => (float) $this->estimated_cost_bdt,
            'description'        => $this->description,
            'created_at'         => $this->created_at?->toISOString(),
        ];
    }
}
