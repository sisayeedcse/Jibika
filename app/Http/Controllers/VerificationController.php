<?php

namespace App\Http\Controllers;

use App\Actions\Verification\DecideVerificationAction;
use App\Http\Requests\Verification\DecideVerificationRequest;
use App\Models\CommunityVerification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', CommunityVerification::class);
        $user = auth()->user();
        $query = CommunityVerification::with(['request.worker', 'communityMember']);
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

    public function decide(DecideVerificationRequest $request, CommunityVerification $verification, DecideVerificationAction $action)
    {
        $action->execute($verification, $request->validated(), auth()->user());
        return redirect()->route('verification.index')->with('success', 'Verification decision recorded.');
    }
}
