<?php

namespace App\Actions\Procurement;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\User;
use App\Models\VendorQuotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RejectQuotationAction
{
    public function execute(VendorQuotation $quotation, User $admin): VendorQuotation
    {
        if (!$admin->isAdmin()) {
            abort(403, 'Only administrators may reject vendor quotations.');
        }

        if ($quotation->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Quotation has already been ' . $quotation->status . '.',
            ]);
        }

        DB::transaction(function () use ($quotation, $admin) {
            $quotation->update(['status' => 'rejected']);

            // If the provisional order was not awarded, mark it failed so it produces no obligations
            if ($quotation->procurementOrder) {
                $order = $quotation->procurementOrder;
                if (!$order->request || $order->request->procurement_order_id !== $order->id) {
                    $order->update(['status' => 'failed']);
                }
            }

            AuditLog::create([
                'user_id'        => $admin->id,
                'action'         => 'reject_quotation',
                'model_type'     => 'vendor_quotation',
                'model_id'       => $quotation->id,
                'previous_state' => ['status' => 'pending'],
                'new_state'      => ['status' => 'rejected'],
            ]);

            Notification::create([
                'user_id'  => $quotation->vendor_id,
                'type'     => 'quotation_rejected',
                'title'    => 'Quotation Rejected',
                'title_bn' => 'কোটেশন বাতিল করা হয়েছে',
                'body_en'  => 'Your quotation for request #' . ($quotation->procurementOrder?->request_id ?? 'N/A') . ' has been declined.',
                'body_bn'  => 'আপনার কোটেশন প্রত্যাখ্যান করা হয়েছে।',
            ]);
        });

        return $quotation;
    }
}
