<?php

namespace App\Http\Controllers;

use App\Actions\Procurement\SubmitVendorQuotationAction;
use App\Http\Requests\Procurement\SubmitVendorQuotationRequest;
use App\Models\AssetRequest;
use App\Models\VendorQuotation;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', VendorQuotation::class);
        $quotations = VendorQuotation::with(['procurementOrder.request', 'vendor'])->where('vendor_id', auth()->id())->latest()->paginate(15);
        $opportunities = AssetRequest::where('status', 'funded')->whereDoesntHave('procurementOrder')->get();
        return view('pages.vendor.index', compact('quotations', 'opportunities'));
    }

    public function quote(SubmitVendorQuotationRequest $request, AssetRequest $opportunity, SubmitVendorQuotationAction $action)
    {
        $action->execute($opportunity, $request->validated(), auth()->user());
        return redirect()->route('vendor.index')->with('success', 'Quotation submitted successfully.');
    }
}
