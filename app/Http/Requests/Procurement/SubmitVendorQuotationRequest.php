<?php

namespace App\Http\Requests\Procurement;

use App\Models\VendorQuotation;
use Illuminate\Foundation\Http\FormRequest;

class SubmitVendorQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('create', VendorQuotation::class);
    }

    public function rules(): array
    {
        return [
            'quoted_amount_bdt' => ['required', 'numeric', 'min:1000', 'max:5000000'],
            'notes'             => ['nullable', 'string', 'max:1000'],
        ];
    }
}
