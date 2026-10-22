<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\ProcurementOrder;
use App\Models\User;
use App\Models\VendorQuotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user->isAdmin()) {
            abort(403);
        }
        $stats = [
            'users'                => User::count(),
            'requests'             => AssetRequest::count(),
            'pending_verification' => AssetRequest::where('status','submitted')->count(),
            'funded'               => AssetRequest::where('status','funded')->count(),
            'delivered'            => AssetRequest::whereHas('procurementOrder.delivery', fn($q) => $q->where('status','delivered'))->count(),
        ];
        return view('pages.admin.index', compact('stats'));
    }

    public function users()
    {
        if (!auth()->user()->isAdmin()) { abort(403); }
        $users = User::latest()->paginate(20);
        return view('pages.admin.users', compact('users'));
    }

    public function setStatus(Request $request, User $user)
    {
        if (!auth()->user()->isAdmin()) { abort(403); }
        $validated = $request->validate(['status' => ['required','in:active,suspended']]);
        $previous = $user->status;
        $user->update(['status' => $validated['status']]);
        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'set_user_status',
            'model_type'     => 'user',
            'model_id'       => $user->id,
            'previous_state' => ['status' => $previous],
            'new_state'      => ['status' => $validated['status']],
        ]);
        return back()->with('success', 'User status updated.');
    }

    public function requests()
    {
        if (!auth()->user()->isAdmin()) { abort(403); }
        $requests = AssetRequest::with(['worker', 'category', 'fundingPool', 'procurementOrder'])->latest()->paginate(20);
        return view('pages.admin.requests', compact('requests'));
    }

    public function audit()
    {
        if (!auth()->user()->isAdmin()) { abort(403); }
        $logs = AuditLog::with('user')->latest()->paginate(20);
        return view('pages.admin.audit', compact('logs'));
    }

    public function selectQuotation(Request $request, VendorQuotation $quotation)
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        if ($quotation->status !== 'pending') {
            return back()->withErrors(['status' => 'Quotation has already been ' . $quotation->status . '.']);
        }

        try {
            DB::transaction(function () use ($quotation) {
                // Lock quotation record for update to prevent race conditions
                $lockedQuotation = VendorQuotation::where('id', $quotation->id)->lockForUpdate()->first();
                if (!$lockedQuotation || $lockedQuotation->status !== 'pending') {
                    throw new \InvalidArgumentException('Quotation has already been processed or is not in pending state.');
                }

                $order = $lockedQuotation->procurementOrder;
                if (!$order) {
                    throw new \InvalidArgumentException('Quotation has no associated procurement order.');
                }

                // Lock the associated asset request to enforce single active winner invariant
                $assetRequest = AssetRequest::where('id', $order->request_id)->lockForUpdate()->first();
                if (!$assetRequest) {
                    throw new \InvalidArgumentException('Associated asset request not found.');
                }

                if ($assetRequest->status !== 'funded' || $assetRequest->procurement_order_id !== null) {
                    throw new \InvalidArgumentException('Request #' . $assetRequest->id . ' is not eligible for quotation selection. It may already be in procurement or completed.');
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
                        'user_id'        => auth()->id(),
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
                    'user_id'        => auth()->id(),
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
            });
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return back()->with('success', 'Quotation selected and procurement order initiated.');
    }

    public function rejectQuotation(Request $request, VendorQuotation $quotation)
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        if ($quotation->status !== 'pending') {
            return back()->withErrors(['status' => 'Quotation has already been ' . $quotation->status . '.']);
        }

        DB::transaction(function () use ($quotation) {
            $quotation->update(['status' => 'rejected']);

            // If the provisional order was not awarded, mark it failed so it produces no obligations
            if ($quotation->procurementOrder) {
                $order = $quotation->procurementOrder;
                if (!$order->request || $order->request->procurement_order_id !== $order->id) {
                    $order->update(['status' => 'failed']);
                }
            }

            AuditLog::create([
                'user_id'        => auth()->id(),
                'action'         => 'reject_quotation',
                'model_type'     => 'vendor_quotation',
                'model_id'       => $quotation->id,
                'previous_state' => ['status' => 'pending'],
                'new_state'      => ['status' => 'rejected'],
            ]);

            Notification::create([
                'user_id'  => $quotation->vendor_id,
                'type'     => 'quotation_rejected',
                'title'    => 'Quotation Rejected',
                'title_bn' => 'কোটেশন বাতিল করা হয়েছে',
                'body_en'  => 'Your quotation for request #' . ($quotation->procurementOrder?->request_id ?? 'N/A') . ' has been declined.',
                'body_bn'  => 'আপনার কোটেশন প্রত্যাখ্যান করা হয়েছে।',
            ]);
        });

        return back()->with('success', 'Quotation rejected.');
    }
}
