<?php

namespace App\Actions\Assessments;

use App\Models\AssessmentRecord;
use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AIService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssessAssetRequestAction
{
    public function __construct(
        protected AIService $aiService
    ) {}

    public function execute(AssetRequest $request, User $user): AssessmentRecord
    {
        if (!$user->isAdmin() && $user->id !== $request->worker_id) {
            abort(403, 'You may not trigger assessment for this request.');
        }

        return DB::transaction(function () use ($request, $user) {
            $lockedRequest = AssetRequest::whereKey($request->id)->lockForUpdate()->firstOrFail();

            if ($lockedRequest->status !== 'verified') {
                throw ValidationException::withMessages([
                    'status' => 'Assessment can only be triggered for verified requests.',
                ]);
            }

            $assessment = $this->aiService->assess($lockedRequest);

            $lockedRequest->update([
                'status'        => 'ai_assessed',
                'assessment_id' => $assessment->id,
            ]);

            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'assess_request',
                'model_type' => 'asset_request',
                'model_id'   => $lockedRequest->id,
                'new_state'  => ['status' => 'ai_assessed', 'assessment_id' => $assessment->id],
            ]);

            return $assessment;
        });
    }
}
