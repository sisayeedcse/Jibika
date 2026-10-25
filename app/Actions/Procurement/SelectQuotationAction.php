<?php

namespace App\Actions\Procurement;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Models\VendorQuotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SelectQuotationAction
{
    public function execute(VendorQuotation $quotation, User $admin): ProcurementOrder
    {
        if (!$admin->isAdmin()) {
            abort(403, 'Only administrators may select vendor quotations.');
        }

        if ($quotation->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'Quotation has already been ' . $quotation->status . '.',
            ]);
        }

        try {
            return DB::transaction(function () use ($quotation, $admin) {
                // Lock quotation record for update
                $lockedQuotation = VendorQuotation::where('id', $quotation->id)->lockForUpdate()->first();
                if (!$lockedQuotation || $lockedQuotation->status !== 'pending') {
                    throw new \InvalidArgumentException('Quotation has already been processed or is not in pending state.');
                }

                $order = $lockedQuotation->procurementOrder;
                if (!$order) {
                    throw new \InvalidArgumentException('Quotation has no associated procurement order.');
                }

                // Lock associated asset request to enforce single active winner invariant
                $assetRequest = AssetRequest::where('id', $order->request_id)->lockForUpdate()->first();
                if (!$assetRequest) {
                    throw new \InvalidArgumentException('Associated asset request not found.');
                }

                if ($assetRequest->status !== 'funded' || $assetRequest->procurement_order_id !== null) {
                    throw new \InvalidArgumentException('Quotation can only be selected for fully funded requests.');
                }

                // Award winning quotation
                $lockedQuotation->update(['status' => 'selected']);

                // Activate the winning procurement order
                $order->update([
                    'vendor_id'    => $lockedQuotation->vendor_id,
                    'quotation_id' => $lockedQuotation->id,
                    'cost_bdt'     => $lockedQuotation->quoted_amount_bdt,
                    'status'       => 'pending',
                ]);

                // Link awarded order to the asset request and advance status
                $assetRequest->update([
                    'status'               => 'procurement',
                    'procurement_order_id' => $order->id,
                ]);

                // Automatically reject all competing pending quotations for this request
                $competingQuotations = VendorQuotation::where('id', '!=', $lockedQuotation->id)
                    ->whereHas('procurementOrder', fn($q) => $q->where('request_id', $assetRequest->id))
                    ->where('status', 'pending')
                    ->get();

                foreach ($competingQuotations as $competing) {
                    $competing->update(['status' => 'rejected']);
                    if ($competing->procurementOrder && $competing->procurementOrder->id !== $order->id) {
                        $competing->procurementOrder->update(['status' => 'failed']);
                    }
                    AuditLog::create([
                        'user_id'        => $admin->id,
                        'action'         => 'reject_quotation',
                        'model_type'     => 'vendor_quotation',
                        'model_id'       => $competing->id,
                        'previous_state' => ['status' => 'pending'],
                        'new_state'      => ['status' => 'rejected', 'reason' => 'Another quotation was selected for request #' . $assetRequest->id],
                    ]);
                    Notification::create([
                        'user_id'  => $competing->vendor_id,
                        'type'     => 'quotation_rejected',
                        'title'    => 'Quotation Not Selected',
                        'title_bn' => 'কোটেশন নির্বাচিত হয়নি',
                        'body_en'  => 'Another quotation was selected for request #' . $assetRequest->id . '.',
                        'body_bn'  => 'অনুরোধ #' . $assetRequest->id . ' এর জন্য অন্য একটি কোটেশন নির্বাচিত হয়েছে।',
                    ]);
                }

                // Log audit trail for winning quotation selection
                AuditLog::create([
                    'user_id'        => $admin->id,
                    'action'         => 'select_quotation',
                    'model_type'     => 'vendor_quotation',
                    'model_id'       => $lockedQuotation->id,
                    'previous_state' => ['status' => 'pending', 'request_status' => 'funded'],
                    'new_state'      => ['status' => 'selected', 'request_status' => 'procurement', 'order_id' => $order->id],
                ]);

                // Notify winning vendor
                Notification::create([
                    'user_id'  => $lockedQuotation->vendor_id,
                    'type'     => 'quotation_selected',
                    'title'    => 'Quotation Selected',
                    'title_bn' => 'কোটেশন নির্বাচিত হয়েছে',
                    'body_en'  => 'Your quotation of BDT ' . number_format($lockedQuotation->quoted_amount_bdt, 0) . ' has been selected for request #' . $assetRequest->id . '.',
                    'body_bn'  => 'অনুরোধ #' . $assetRequest->id . ' এর জন্য আপনার কোটেশন নির্বাচিত হয়েছে।',
                ]);

                // Notify worker
                Notification::create([
                    'user_id'  => $assetRequest->worker_id,
                    'type'     => 'procurement_initiated',
                    'title'    => 'Procurement Initiated',
                    'title_bn' => 'সংগ্রহ কার্যক্রম শুরু হয়েছে',
                    'body_en'  => 'Quotation from supplier has been selected for request #' . $assetRequest->id . '. Procurement has begun.',
                    'body_bn'  => 'আপনার অনুরোধের জন্য সরবরাহকারী নির্বাচিত হয়েছে। সংগ্রহ শুরু হয়েছে।',
                ]);

                return $order;
            });
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'status' => $e->getMessage(),
            ]);
        }
    }
}
