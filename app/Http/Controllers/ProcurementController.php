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
        $orders = ProcurementOrder::with(['request.worker','vendor','quotation','delivery'])->latest()->paginate(15);
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
        $delivery = Delivery::updateOrCreate(
            ['procurement_order_id' => $order->id],
            ['status' => $validated['status'], 'evidence_image_path' => $validated['evidence_image_path'], 'delivered_at' => now()]
        );
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
