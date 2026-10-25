<?php

namespace App\Actions\Procurement;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Models\VendorQuotation;
use Illuminate\Validation\ValidationException;

class SubmitVendorQuotationAction
{
    public function execute(AssetRequest $opportunity, array $data, User $vendor): VendorQuotation
    {
        if ($opportunity->status !== 'funded' || $opportunity->procurement_order_id !== null) {
            throw ValidationException::withMessages([
                'quoted_amount_bdt' => 'Quotations can only be submitted for funded requests awaiting supplier selection.',
            ]);
        }

        $existing = VendorQuotation::where('vendor_id', $vendor->id)
            ->whereHas('procurementOrder', fn($q) => $q->where('request_id', $opportunity->id))
            ->first();

        if ($existing) {
            throw ValidationException::withMessages([
                'quoted_amount_bdt' => 'You have already submitted a quotation for this request.',
            ]);
        }

        $procurement = ProcurementOrder::create([
            'request_id' => $opportunity->id,
            'vendor_id'  => $vendor->id,
            'status'     => 'pending',
            'cost_bdt'   => $data['quoted_amount_bdt'],
        ]);

        $quotation = VendorQuotation::create([
            'procurement_order_id' => $procurement->id,
            'vendor_id'            => $vendor->id,
            'quoted_amount_bdt'    => $data['quoted_amount_bdt'],
            'notes'                => $data['notes'] ?? null,
            'status'               => 'pending',
        ]);

        $procurement->update(['quotation_id' => $quotation->id]);

        AuditLog::create([
            'user_id'    => $vendor->id,
            'action'     => 'submit_quotation',
            'model_type' => 'procurement_order',
            'model_id'   => $procurement->id,
            'new_state'  => ['status' => 'pending', 'quoted_amount' => (string) $data['quoted_amount_bdt']],
        ]);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id'  => $admin->id,
                'type'     => 'new_quotation',
                'title'    => 'New Vendor Quotation',
                'title_bn' => 'নতুন ভেন্ডর কোটেশন',
                'body_en'  => 'Vendor has submitted a quotation of BDT ' . number_format($data['quoted_amount_bdt'], 0) . ' for request #' . $opportunity->id . '.',
                'body_bn'  => 'অনুরোধ #' . $opportunity->id . ' এর জন্য একটি নতুন কোটেশন জমা দেওয়া হয়েছে।',
            ]);
        }

        return $quotation;
    }
}
