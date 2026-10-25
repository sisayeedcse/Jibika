<?php

namespace App\Http\Requests\Verification;

use Illuminate\Foundation\Http\FormRequest;

class DecideVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $verification = $this->route('verification');
        if (!$verification instanceof \App\Models\CommunityVerification) {
            $verification = \App\Models\CommunityVerification::find($verification);
        }
        return $verification && $this->user() && $this->user()->can('update', $verification);
    }

    public function rules(): array
    {
        return [
            'status'         => ['required', 'in:verified,flagged,rejected,needs_info'],
            'decision_notes' => ['required', 'string', 'max:1000'],
        ];
    }
}
