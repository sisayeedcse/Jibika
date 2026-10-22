<?php
namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Delivery;
use App\Models\ProcurementOrder;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = ProcurementOrder::with(['request.worker','vendor','quotation','delivery']);

        if ($user->isVendor()) {
            $query->where('vendor_id', $user->id);
        } elseif ($user->isWorker()) {
            $query->whereHas('request', fn($q) => $q->where('worker_id', $user->id));
        }

        $orders = $query->latest()->paginate(15);
        return view('pages.procurement.index', compact('orders'));
    }

    public function confirmDelivery(Request $request, ProcurementOrder $order)
    {
        $this->authorize('update', $order);
        $validated = $request->validate([
            'status' => ['required','in:delivered,disputed'],
            'evidence_image_path' => ['nullable','string','max:500'],
        ]);

        $order->status = $validated['status'] === 'disputed' ? 'pending' : 'procured';
        $order->save();

        Delivery::updateOrCreate(
            ['procurement_order_id' => $order->id],
            [
                'status' => $validated['status'],
                'evidence_image_path' => $validated['evidence_image_path'] ?? null,
                'delivered_at' => now(),
                'confirmed_by' => auth()->id(),
            ]
        );

        if ($order->request) {
            $order->request->update([
                'status' => $validated['status'] === 'delivered' ? 'delivered' : 'delivery_disputed',
            ]);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'confirm_delivery',
            'model_type' => 'procurement_order',
            'model_id' => $order->id,
            'new_state' => ['status' => $validated['status']],
        ]);

        return redirect()->route('procurement.index')->with('success', 'Delivery status updated.');
    }

    public function resolveDispute(Request $request, ProcurementOrder $order)
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators may resolve delivery disputes.');
        }

        $delivery = $order->delivery;
        if (!$delivery || $delivery->status !== 'disputed') {
            return back()->withErrors(['status' => 'No active delivery dispute on this order.']);
        }

        $validated = $request->validate([
            'resolution' => ['required','in:confirm_delivered,mark_failed,reopen_procurement'],
            'notes'      => ['required','string','max:1000'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $delivery, $validated, $user) {
            switch ($validated['resolution']) {
                case 'confirm_delivered':
                    $delivery->update(['status' => 'delivered', 'confirmed_by' => $user->id]);
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
                'user_id'        => $user->id,
                'action'         => 'resolve_delivery_dispute',
                'model_type'     => 'procurement_order',
                'model_id'       => $order->id,
                'previous_state' => [
                    'order_status'    => $order->getOriginal('status'),
                    'delivery_status' => 'disputed',
                    'request_status'  => 'delivery_disputed',
                ],
                'new_state'      => [
                    'resolution'     => $validated['resolution'],
                    'notes'          => $validated['notes'],
                    'order_status'   => $order->status,
                    'request_status' => $order->request?->status,
                ],
            ]);

            // Notify worker
            if ($order->request && $order->request->worker_id) {
                $statusMsg = match ($validated['resolution']) {
                    'confirm_delivered'  => 'Delivery confirmed after dispute mediation.',
                    'mark_failed'        => 'Procurement failed due to delivery failure.',
                    'reopen_procurement' => 'Delivery dispute resolved: supplier requested to re-dispatch.',
                };
                $statusMsgBn = match ($validated['resolution']) {
                    'confirm_delivered'  => 'বিরোধ নিষ্পত্তির পর সরবরাহ নিশ্চিত করা হয়েছে।',
                    'mark_failed'        => 'সরবরাহ ব্যর্থতার কারণে সংগ্রহ বাতিল করা হয়েছে।',
                    'reopen_procurement' => 'বিরোধ নিষ্পত্তির পর পুনরায় সরবরাহের নির্দেশ দেওয়া হয়েছে।',
                };
                \App\Models\Notification::create([
                    'user_id'  => $order->request->worker_id,
                    'type'     => 'dispute_resolved',
                    'title'    => 'Delivery Dispute Resolved',
                    'title_bn' => 'সরবরাহ বিরোধ নিষ্পত্তি',
                    'body_en'  => $statusMsg . ' Notes: ' . $validated['notes'],
                    'body_bn'  => $statusMsgBn,
                ]);
            }
        });

        return redirect()->route('procurement.index')->with('success', 'Dispute resolved successfully.');
    }
}
