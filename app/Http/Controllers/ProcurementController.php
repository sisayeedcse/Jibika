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

        $order->status = 'procured';
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
}
