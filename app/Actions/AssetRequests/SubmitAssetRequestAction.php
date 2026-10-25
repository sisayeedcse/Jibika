<?php

namespace App\Actions\AssetRequests;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\CommunityVerification;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class SubmitAssetRequestAction
{
    public function execute(AssetRequest $request, User $user): AssetRequest
    {
        if ($user->id !== $request->worker_id) {
            abort(403, 'You do not own this request.');
        }

        if ($request->status !== 'draft') {
            throw ValidationException::withMessages([
                'status' => 'Only draft requests can be submitted.',
            ]);
        }

        $verifier = User::where('role', 'community')->where('status', 'active')->first();
        $verificationId = null;

        if ($verifier) {
            $verification = CommunityVerification::create([
                'request_id'          => $request->id,
                'community_member_id' => $verifier->id,
                'status'              => 'pending',
            ]);
            $verificationId = $verification->id;

            Notification::create([
                'user_id'  => $verifier->id,
                'type'     => 'new_verification_assigned',
                'title'    => 'New Asset Request to Verify',
                'title_bn' => 'নতুন সম্পদ অনুরোধ যাচাইকরণ',
                'body_en'  => 'Asset request #' . $request->id . ' has been submitted and assigned for community verification.',
                'body_bn'  => 'সম্পদ অনুরোধ #' . $request->id . ' জমা দেওয়া হয়েছে এবং যাচাইয়ের জন্য বরাদ্দ করা হয়েছে।',
            ]);
        }

        $request->update([
            'status'          => 'submitted',
            'verification_id' => $verificationId ?? $request->verification_id,
        ]);

        AuditLog::create([
            'user_id'    => $user->id,
            'action'     => 'submit_request',
            'model_type' => 'asset_request',
            'model_id'   => $request->id,
            'new_state'  => ['status' => 'submitted', 'verification_id' => $verificationId],
        ]);

        return $request;
    }
}
