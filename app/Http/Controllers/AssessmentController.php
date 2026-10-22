<?php
namespace App\Http\Controllers;

use App\Models\AssessmentRecord;
use App\Models\AssetRequest;
use App\Services\AIService;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function show(AssetRequest $request, AssessmentRecord $assessment)
    {
        $this->authorize('view', $assessment);
        $assessment->load('request');
        return view('pages.assessment.show', compact('assessment','request'));
    }

    public function trigger(AssetRequest $request, AIService $ai)
    {
        // Only the owning worker (when verified) or admin may trigger assessment.
        $user = auth()->user();
        if (!$user->isAdmin() && $user->id !== $request->worker_id) {
            abort(403, 'You may not trigger assessment for this request.');
        }

        // Status check: produce a validation-style session error, not a 403
        if ($request->status !== 'verified') {
            return back()->withErrors(['status' => 'Assessment can only be triggered for verified requests.']);
        }

        $assessment = $ai->assess($request);
        $request->update(['status' => 'ai_assessed', 'assessment_id' => $assessment->id]);

        return redirect()->route('assessment.show', ['request' => $request, 'assessment' => $assessment])
            ->with('success', 'Assessment completed.');
    }
}
