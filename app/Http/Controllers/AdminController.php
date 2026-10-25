<?php

namespace App\Http\Controllers;

use App\Actions\Procurement\RejectQuotationAction;
use App\Actions\Procurement\SelectQuotationAction;
use App\Http\Requests\Admin\SetUserStatusRequest;
use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\VendorQuotation;
use Illuminate\Http\Request;

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
            'pending_verification' => AssetRequest::where('status', 'submitted')->count(),
            'funded'               => AssetRequest::where('status', 'funded')->count(),
            'delivered'            => AssetRequest::whereHas('procurementOrder.delivery', fn($q) => $q->where('status', 'delivered'))->count(),
        ];
        return view('pages.admin.index', compact('stats'));
    }

    public function users()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        $users = User::latest()->paginate(20);
        return view('pages.admin.users', compact('users'));
    }

    public function setStatus(SetUserStatusRequest $request, User $user)
    {
        $validated = $request->validated();
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
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        $requests = AssetRequest::with(['worker', 'category', 'fundingPool', 'procurementOrder'])->latest()->paginate(20);
        return view('pages.admin.requests', compact('requests'));
    }

    public function audit()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }
        $logs = AuditLog::with('user')->latest()->paginate(20);
        return view('pages.admin.audit', compact('logs'));
    }

    public function selectQuotation(Request $request, VendorQuotation $quotation, SelectQuotationAction $action)
    {
        $action->execute($quotation, auth()->user());
        return back()->with('success', 'Quotation selected and procurement order initiated.');
    }

    public function rejectQuotation(Request $request, VendorQuotation $quotation, RejectQuotationAction $action)
    {
        $action->execute($quotation, auth()->user());
        return back()->with('success', 'Quotation rejected.');
    }
}
