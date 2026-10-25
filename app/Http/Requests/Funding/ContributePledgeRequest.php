<?php

namespace App\Http\Requests\Funding;

use Illuminate\Foundation\Http\FormRequest;

class ContributePledgeRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && ($user->isContributor() || $user->isAdmin());
    }

    public function rules(): array
    {
        return [
            'amount_bdt'   => ['required', 'numeric', 'min:100', 'max:5000000'],
            'is_anonymous' => ['nullable', 'boolean'],
        ];
    }
}
