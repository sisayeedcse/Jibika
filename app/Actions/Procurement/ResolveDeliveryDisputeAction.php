<?php

namespace App\Actions\Procurement;

use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResolveDeliveryDisputeAction
{
    public function execute(ProcurementOrder $order, array $data, User $admin): ProcurementOrder
    {
        if (!$admin->isAdmin()) {
            abort(403, 'Only administrators may resolve delivery disputes.');
        }

        $delivery = $order->delivery;
        if (!$delivery || $delivery->status !== 'disputed') {
            throw ValidationException::withMessages([
                'status' => 'No active delivery dispute on this order.',
            ]);
        }

        if ($order->request && $order->request->procurement_order_id && $order->request->procurement_order_id !== $order->id) {
            throw ValidationException::withMessages([
                'status' => 'This procurement order is not the active order for the asset request.',
            ]);
        }

        return DB::transaction(function () use ($order, $delivery, $data, $admin) {
            switch ($data['resolution']) {
                case 'confirm_delivered':
                    $delivery->update(['status' => 'delivered', 'confirmed_by' => $admin->id]);
                    $order->update(['status' => 'procured']);
                    if ($order->request) {
                        $order->request->update(['status' => 'delivered']);
                    }
                    break;

                case 'mark_failed':
                    $delivery->update(['status' => 'disputed']);
                    $order->update(['status' => 'failed']);
                    if ($order->request) {
                        $order->request->update(['status' => 'procurement_failed']);
                    }
                    break;

                case 'reopen_procurement':
                    $delivery->update(['status' => 'pending']);
                    $order->update(['status' => 'pending']);
                    if ($order->request) {
                        $order->request->update(['status' => 'procurement']);
                    }
                    break;
            }

            AuditLog::create([
                'user_id'        => $admin->id,
                'action'         => 'resolve_delivery_dispute',
                'model_type'     => 'procurement_order',
                'model_id'       => $order->id,
                'previous_state' => [
                    'order_status'    => $order->getOriginal('status'),
                    'delivery_status' => 'disputed',
                    'request_status'  => 'delivery_disputed',
                ],
                'new_state'      => [
                    'resolution'     => $data['resolution'],
                    'notes'          => $data['notes'],
                    'order_status'   => $order->status,
                    'request_status' => $order->request?->status,
                ],
            ]);

            // Notify worker
            if ($order->request && $order->request->worker_id) {
                $statusMsg = match ($data['resolution']) {
                    'confirm_delivered'  => 'Delivery confirmed after dispute mediation.',
                    'mark_failed'        => 'Procurement failed due to delivery failure.',
                    'reopen_procurement' => 'Delivery dispute resolved: supplier requested to re-dispatch.',
                };
                $statusMsgBn = match ($data['resolution']) {
                    'confirm_delivered'  => 'বিরোধ নিষ্পত্তির পর সরবরাহ নিশ্চিত করা হয়েছে।',
                    'mark_failed'        => 'সরবরাহ ব্যর্থতার কারণে সংগ্রহ বাতিল করা হয়েছে।',
                    'reopen_procurement' => 'বিরোধ নিষ্পত্তির পর পুনরায় সরবরাহের নির্দেশ দেওয়া হয়েছে।',
                };
                Notification::create([
                    'user_id'  => $order->request->worker_id,
                    'type'     => 'dispute_resolved',
                    'title'    => 'Delivery Dispute Resolved',
                    'title_bn' => 'সরবরাহ বিরোধ নিষ্পত্তি',
                    'body_en'  => $statusMsg . ' Notes: ' . $data['notes'],
                    'body_bn'  => $statusMsgBn,
                ]);
            }

            return $order;
        });
    }
}
