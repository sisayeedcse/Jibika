<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\CommunityVerification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', CommunityVerification::class);
        $verifications = CommunityVerification::with(['request.worker','communityMember'])->latest()->paginate(15);
        return view('pages.verification.index', compact('verifications'));
    }

    public function review(CommunityVerification $verification)
    {
        $this->authorize('update', $verification);
        $verification->load('request.worker');
        return view('pages.verification.review', compact('verification'));
    }

    public function decide(Request $request, CommunityVerification $verification)
    {
        $this->authorize('update', $verification);
        $validated = $request->validate([
            'status' => ['required','in:verified,flagged,rejected,needs_info'],
            'decision_notes' => ['required','string','max:1000'],
        ]);
        $previous = $verification->status;
        $verification->update([
            'status' => $validated['status'],
            'decision_notes' => $validated['decision_notes'],
            'verified_at' => now(),
        ]);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'verify_decision',
            'model_type' => 'community_verification',
            'model_id' => $verification->id,
            'previous_state' => ['status' => $previous],
            'new_state' => ['status' => $validated['status']],
        ]);
        return redirect()->route('verification.index')->with('success', 'Verification decision recorded.');
    }
}
