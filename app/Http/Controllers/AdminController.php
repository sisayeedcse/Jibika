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

        DB::transaction(function () use ($quotation) {
            $quotation->update(['status' => 'selected']);

            $order = $quotation->procurementOrder;
            if ($order) {
                $order->update([
                    'vendor_id'    => $quotation->vendor_id,
                    'quotation_id' => $quotation->id,
                    'cost_bdt'     => $quotation->quoted_amount_bdt,
                    'status'       => 'pending',
                ]);
                if ($order->request) {
                    $order->request->update([
                        'status'               => 'procurement',
                        'procurement_order_id' => $order->id,
                    ]);

                    Notification::create([
                        'user_id'  => $order->request->worker_id,
                        'type'     => 'procurement_initiated',
                        'title'    => 'Procurement Initiated',
                        'title_bn' => 'সংগ্রহ কার্যক্রম শুরু হয়েছে',
                        'body_en'  => 'Quotation from supplier has been selected for request #' . $order->request->id . '. Procurement has begun.',
                        'body_bn'  => 'আপনার অনুরোধের জন্য সরবরাহকারী নির্বাচিত হয়েছে। সংগ্রহ শুরু হয়েছে।',
                    ]);
                }
            }

            AuditLog::create([
                'user_id'    => auth()->id(),
                'action'     => 'select_quotation',
                'model_type' => 'vendor_quotation',
                'model_id'   => $quotation->id,
                'new_state'  => ['status' => 'selected'],
            ]);
        });

        return back()->with('success', 'Quotation selected and procurement order initiated.');
    }

    public function rejectQuotation(Request $request, VendorQuotation $quotation)
    {
        if (!auth()->user()->isAdmin()) { abort(403); }

        if ($quotation->status !== 'pending') {
            return back()->withErrors(['status' => 'Quotation has already been ' . $quotation->status . '.']);
        }

        $quotation->update(['status' => 'rejected']);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'reject_quotation',
            'model_type' => 'vendor_quotation',
            'model_id'   => $quotation->id,
            'new_state'  => ['status' => 'rejected'],
        ]);

        return back()->with('success', 'Quotation rejected.');
    }
}
