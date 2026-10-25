<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class ResolveDisputeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'resolution' => ['required', 'in:confirm_delivered,mark_failed,reopen_procurement'],
            'notes'      => ['required', 'string', 'max:1000'],
        ];
    }
}
