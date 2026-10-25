<?php

namespace App\Http\Controllers;

use App\Actions\Procurement\ConfirmDeliveryAction;
use App\Actions\Procurement\ResolveDeliveryDisputeAction;
use App\Http\Requests\Procurement\ConfirmDeliveryRequest;
use App\Http\Requests\Procurement\ResolveDisputeRequest;
use App\Models\ProcurementOrder;
use Illuminate\Http\Request;

class ProcurementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = ProcurementOrder::with(['request.worker', 'vendor', 'quotation', 'delivery']);

        if ($user->isVendor()) {
            $query->where('vendor_id', $user->id);
        } elseif ($user->isWorker()) {
            $query->whereHas('request', fn($q) => $q->where('worker_id', $user->id))
                  ->where(function ($q) {
                      $q->whereNull('quotation_id')
                        ->orWhereHas('quotation', fn($sub) => $sub->where('status', 'selected'));
                  });
        }

        $orders = $query->latest()->paginate(15);
        return view('pages.procurement.index', compact('orders'));
    }

    public function confirmDelivery(ConfirmDeliveryRequest $request, ProcurementOrder $order, ConfirmDeliveryAction $action)
    {
        $action->execute($order, $request->validated(), auth()->user());
        return redirect()->route('procurement.index')->with('success', 'Delivery status updated.');
    }

    public function resolveDispute(ResolveDisputeRequest $request, ProcurementOrder $order, ResolveDeliveryDisputeAction $action)
    {
        $action->execute($order, $request->validated(), auth()->user());
        return redirect()->route('procurement.index')->with('success', 'Dispute resolved successfully.');
    }
}
