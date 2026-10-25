<?php

namespace App\Actions\Procurement;

use App\Models\AuditLog;
use App\Models\Delivery;
use App\Models\ProcurementOrder;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class ConfirmDeliveryAction
{
    public function execute(ProcurementOrder $order, array $data, User $actor): ProcurementOrder
    {
        if ($order->status === 'failed') {
            throw ValidationException::withMessages([
                'status' => 'Cannot confirm delivery on a failed or cancelled procurement order.',
            ]);
        }

        if ($order->quotation && $order->quotation->status !== 'selected') {
            throw ValidationException::withMessages([
                'status' => 'Delivery can only be confirmed after quotation approval.',
            ]);
        }

        if ($order->request && $order->request->procurement_order_id && $order->request->procurement_order_id !== $order->id) {
            throw ValidationException::withMessages([
                'status' => 'This procurement order is not the active order for the asset request.',
            ]);
        }

        $previousOrderStatus = $order->status;
        $previousDeliveryStatus = $order->delivery?->status ?? 'pending';

        $order->status = $data['status'] === 'disputed' ? 'pending' : 'procured';
        $order->save();

        Delivery::updateOrCreate(
            ['procurement_order_id' => $order->id],
            [
                'status'              => $data['status'],
                'evidence_image_path' => $data['evidence_image_path'] ?? null,
                'delivered_at'        => now(),
                'confirmed_by'        => $actor->id,
            ]
        );

        if ($order->request) {
            $order->request->update([
                'status' => $data['status'] === 'delivered' ? 'delivered' : 'delivery_disputed',
            ]);
        }

        AuditLog::create([
            'user_id'        => $actor->id,
            'action'         => 'confirm_delivery',
            'model_type'     => 'procurement_order',
            'model_id'       => $order->id,
            'previous_state' => ['order_status' => $previousOrderStatus, 'delivery_status' => $previousDeliveryStatus],
            'new_state'      => ['status' => $data['status'], 'order_status' => $order->status],
        ]);

        return $order;
    }
}
