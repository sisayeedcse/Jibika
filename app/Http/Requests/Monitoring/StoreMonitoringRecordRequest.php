<?php

namespace App\Http\Requests\Monitoring;

use App\Models\AssetRequest;
use Illuminate\Foundation\Http\FormRequest;

class StoreMonitoringRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        $assetRequest = $this->route('request');
        if (!$assetRequest instanceof AssetRequest) {
            $assetRequest = AssetRequest::find($assetRequest);
        }

        $user = $this->user();
        if (!$assetRequest || !$user) {
            return false;
        }

        return $user->isAdmin() || $user->id === $assetRequest->worker_id;
    }

    public function rules(): array
    {
        return [
            'metric_name'  => ['required', 'string', 'max:255'],
            'metric_value' => ['required', 'string', 'max:255'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ];
    }
}
