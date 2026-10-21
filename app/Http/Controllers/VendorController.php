<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\ProcurementOrder;
use App\Models\VendorQuotation;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', VendorQuotation::class);
        $quotations = VendorQuotation::with(['procurementOrder.request','vendor'])->where('vendor_id', auth()->id())->latest()->paginate(15);
        $opportunities = AssetRequest::where('status','funded')->whereDoesntHave('procurementOrder')->get();
        return view('pages.vendor.index', compact('quotations','opportunities'));
    }

    public function quote(Request $request, AssetRequest $opportunity)
    {
        $this->authorize('create', VendorQuotation::class);
        $validated = $request->validate([
            'quoted_amount_bdt' => ['required','numeric','min:1000'],
            'notes' => ['nullable','string','max:1000'],
        ]);
        $procurement = ProcurementOrder::create([
            'request_id' => $opportunity->id,
            'vendor_id' => auth()->id(),
            'status' => 'pending',
            'cost_bdt' => $validated['quoted_amount_bdt'],
        ]);
        VendorQuotation::create([
            'procurement_order_id' => $procurement->id,
            'vendor_id' => auth()->id(),
            'quoted_amount_bdt' => $validated['quoted_amount_bdt'],
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'submit_quotation',
            'model_type' => 'procurement_order',
            'model_id' => $procurement->id,
            'new_state' => ['status' => 'pending', 'quoted_amount' => (string) $validated['quoted_amount_bdt']],
        ]);
        return redirect()->route('vendor.index')->with('success', 'Quotation submitted.');
    }
}
