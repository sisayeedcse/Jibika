<?php

namespace App\Http\Controllers;

use App\Actions\Assessments\AssessAssetRequestAction;
use App\Models\AssessmentRecord;
use App\Models\AssetRequest;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function show(AssetRequest $request, AssessmentRecord $assessment)
    {
        $this->authorize('view', $assessment);
        $assessment->load('request');
        return view('pages.assessment.show', compact('assessment', 'request'));
    }

    public function trigger(AssetRequest $request, AssessAssetRequestAction $action)
    {
        $assessment = $action->execute($request, auth()->user());

        return redirect()->route('assessment.show', ['request' => $request, 'assessment' => $assessment])
            ->with('success', 'Assessment completed.');
    }
}
