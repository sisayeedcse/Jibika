<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
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

        if ($opportunity->status !== 'funded') {
            return back()->withErrors(['quoted_amount_bdt' => 'Quotations can only be submitted for funded requests.']);
        }

        $validated = $request->validate([
            'quoted_amount_bdt' => ['required','numeric','min:1000','max:5000000'],
            'notes'             => ['nullable','string','max:1000'],
        ]);

        $existing = VendorQuotation::where('vendor_id', auth()->id())
            ->whereHas('procurementOrder', fn($q) => $q->where('request_id', $opportunity->id))
            ->first();
        if ($existing) {
            return back()->withErrors(['quoted_amount_bdt' => 'You have already submitted a quotation for this request.']);
        }

        $procurement = ProcurementOrder::create([
            'request_id' => $opportunity->id,
            'vendor_id'  => auth()->id(),
            'status'     => 'pending',
            'cost_bdt'   => $validated['quoted_amount_bdt'],
        ]);

        $quotation = VendorQuotation::create([
            'procurement_order_id' => $procurement->id,
            'vendor_id'            => auth()->id(),
            'quoted_amount_bdt'    => $validated['quoted_amount_bdt'],
            'notes'                => $validated['notes'] ?? null,
            'status'               => 'pending',
        ]);

        $procurement->update(['quotation_id' => $quotation->id]);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'submit_quotation',
            'model_type' => 'procurement_order',
            'model_id'   => $procurement->id,
            'new_state'  => ['status' => 'pending', 'quoted_amount' => (string) $validated['quoted_amount_bdt']],
        ]);

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Notification::create([
                'user_id'  => $admin->id,
                'type'     => 'new_quotation',
                'title'    => 'New Vendor Quotation',
                'title_bn' => 'নতুন ভেন্ডর কোটেশন',
                'body_en'  => 'Vendor has submitted a quotation of BDT ' . number_format($validated['quoted_amount_bdt'], 0) . ' for request #' . $opportunity->id . '.',
                'body_bn'  => 'অনুরোধ #' . $opportunity->id . ' এর জন্য একটি নতুন কোটেশন জমা দেওয়া হয়েছে।',
            ]);
        }

        return redirect()->route('vendor.index')->with('success', 'Quotation submitted successfully.');
    }
}
