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
        $user = auth()->user();
        $query = CommunityVerification::with(['request.worker','communityMember']);
        if ($user->isCommunity()) {
            $query->where('community_member_id', $user->id);
        }
        $verifications = $query->latest()->paginate(15);
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

        if (in_array($verification->status, ['verified', 'rejected'], true)) {
            return back()->withErrors(['status' => 'This verification decision has already been finalized.']);
        }

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

        if ($verification->request) {
            $reqStatus = match ($validated['status']) {
                'verified'   => 'verified',
                'rejected'   => 'rejected',
                'needs_info' => 'needs_information',
                'flagged'    => 'under_review',
                default      => 'under_review',
            };
            $verification->request->update([
                'status'          => $reqStatus,
                'verification_id' => $verification->id,
            ]);

            if ($verification->request->worker_id) {
                \App\Models\Notification::create([
                    'user_id'  => $verification->request->worker_id,
                    'type'     => 'verification_' . $validated['status'],
                    'title'    => 'Verification Updated',
                    'title_bn' => 'যাচাইকরণ হালনাগাদ করা হয়েছে',
                    'body_en'  => 'Your asset request #' . $verification->request->id . ' has been marked as ' . str_replace('_', ' ', $validated['status']) . ' by the community verifier.',
                    'body_bn'  => 'আপনার সম্পদ অনুরোধ #' . $verification->request->id . ' যাচাইকারী দ্বারা আপডেট করা হয়েছে।',
                ]);
            }
        }

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
