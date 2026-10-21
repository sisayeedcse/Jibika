<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index()
    {
        $this->authorize('viewAny', User::class);
        $stats = [
            'users' => User::count(),
            'requests' => AssetRequest::count(),
            'pending_verification' => AssetRequest::where('status','submitted')->count(),
            'funded' => AssetRequest::where('status','funded')->count(),
            'delivered' => AssetRequest::whereHas('procurementOrder.delivery', fn($q)=>$q->where('status','delivered'))->count(),
        ];
        return view('pages.admin.index', compact('stats'));
    }

    public function users()
    {
        $this->authorize('viewAny', User::class);
        $users = User::latest()->paginate(20);
        return view('pages.admin.users', compact('users'));
    }

    public function setStatus(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $validated = $request->validate(['status' => ['required','in:active,suspended']]);
        $previous = $user->status;
        $user->update(['status' => $validated['status']]);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'set_user_status',
            'model_type' => 'user',
            'model_id' => $user->id,
            'previous_state' => ['status' => $previous],
            'new_state' => ['status' => $validated['status']],
        ]);
        return back()->with('success', 'User status updated.');
    }

    public function requests()
    {
        $this->authorize('viewAny', AssetRequest::class);
        $requests = AssetRequest::with('worker')->latest()->paginate(20);
        return view('pages.admin.requests', compact('requests'));
    }

    public function audit()
    {
        $this->authorize('viewAny', AuditLog::class);
        $logs = AuditLog::with('user')->latest()->paginate(20);
        return view('pages.admin.audit', compact('logs'));
    }
}
