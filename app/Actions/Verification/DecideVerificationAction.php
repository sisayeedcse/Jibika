<?php

namespace App\Actions\Verification;

use App\Models\AuditLog;
use App\Models\CommunityVerification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class DecideVerificationAction
{
    public function execute(CommunityVerification $verification, array $data, User $actor): CommunityVerification
    {
        if (in_array($verification->status, ['verified', 'rejected'], true)) {
            throw ValidationException::withMessages([
                'status' => 'This verification decision has already been finalized.',
            ]);
        }

        $previous = $verification->status;
        $verification->update([
            'status'         => $data['status'],
            'decision_notes' => $data['decision_notes'],
            'verified_at'    => now(),
        ]);

        if ($verification->request) {
            $reqStatus = match ($data['status']) {
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
                Notification::create([
                    'user_id'  => $verification->request->worker_id,
                    'type'     => 'verification_' . $data['status'],
                    'title'    => 'Verification Updated',
                    'title_bn' => 'যাচাইকরণ হালনাগাদ করা হয়েছে',
                    'body_en'  => 'Your asset request #' . $verification->request->id . ' has been marked as ' . str_replace('_', ' ', $data['status']) . ' by the community verifier.',
                    'body_bn'  => 'আপনার সম্পদ অনুরোধ #' . $verification->request->id . ' যাচাইকারী দ্বারা আপডেট করা হয়েছে।',
                ]);
            }
        }

        AuditLog::create([
            'user_id'        => $actor->id,
            'action'         => 'verify_decision',
            'model_type'     => 'community_verification',
            'model_id'       => $verification->id,
            'previous_state' => ['status' => $previous],
            'new_state'      => ['status' => $data['status']],
        ]);

        return $verification;
    }
}
