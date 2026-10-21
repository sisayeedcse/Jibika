<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())->latest()->paginate(20);
        return view('pages.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, Notification $notification)
    {
        $this->authorize('update', $notification);
        $notification->update(['read_at' => now()]);
        return back()->with('success', 'Marked as read.');
    }

    public function count()
    {
        return response()->json(['unread' => Notification::where('user_id', auth()->id())->whereNull('read_at')->count()]);
    }
}
