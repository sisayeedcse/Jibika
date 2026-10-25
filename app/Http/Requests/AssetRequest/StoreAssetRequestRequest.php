<?php

namespace App\Http\Requests\AssetRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && ($this->user()->isWorker() || $this->user()->isAdmin());
    }

    public function rules(): array
    {
        return [
            'category_id'              => ['nullable', 'exists:asset_categories,id'],
            'requested_amount_bdt'     => ['required', 'numeric', 'min:1000', 'max:5000000'],
            'reason'                   => ['required', 'string', 'max:2000'],
            'livelihood_justification' => ['required', 'string', 'max:2000'],
        ];
    }
}
