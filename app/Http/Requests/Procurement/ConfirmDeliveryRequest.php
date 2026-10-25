<?php

namespace App\Http\Requests\Procurement;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmDeliveryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        if (!$order instanceof \App\Models\ProcurementOrder) {
            $order = \App\Models\ProcurementOrder::find($order);
        }
        return $order && $this->user() && $this->user()->can('update', $order);
    }

    public function rules(): array
    {
        return [
            'status'              => ['required', 'in:delivered,disputed'],
            'evidence_image_path' => ['nullable', 'string', 'max:500'],
        ];
    }
}
