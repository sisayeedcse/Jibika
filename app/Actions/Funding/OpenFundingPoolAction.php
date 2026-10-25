<?php

namespace App\Actions\Funding;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\FundingPool;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class OpenFundingPoolAction
{
    public function execute(AssetRequest $request, User $admin): FundingPool
    {
        if (!$admin->isAdmin()) {
            abort(403, 'Only administrators can open funding pools.');
        }

        if ($request->status !== 'ai_assessed') {
            throw ValidationException::withMessages([
                'status' => 'Only assessed requests can be opened for funding.',
            ]);
        }

        if ($request->fundingPool) {
            throw ValidationException::withMessages([
                'status' => 'A funding pool already exists for this request.',
            ]);
        }

        $pool = FundingPool::create([
            'request_id'         => $request->id,
            'target_amount_bdt'  => $request->requested_amount_bdt,
            'current_amount_bdt' => 0,
            'status'             => 'open',
            'is_demo'            => true,
            'is_simulated_mode'  => true,
        ]);

        $request->update([
            'status'          => 'funding_open',
            'funding_pool_id' => $pool->id,
        ]);

        AuditLog::create([
            'user_id'    => $admin->id,
            'action'     => 'open_funding_pool',
            'model_type' => 'funding_pool',
            'model_id'   => $pool->id,
            'new_state'  => ['status' => 'open', 'target_amount' => (string) $pool->target_amount_bdt],
        ]);

        Notification::create([
            'user_id'  => $request->worker_id,
            'type'     => 'funding_opened',
            'title'    => 'Funding Pool Opened',
            'title_bn' => 'তহবিল সংগ্রহ শুরু হয়েছে',
            'body_en'  => 'Your asset request #' . $request->id . ' is now open for simulated pledges.',
            'body_bn'  => 'আপনার সম্পদ অনুরোধ #' . $request->id . ' এখন অনুদানের জন্য উন্মুক্ত।',
        ]);

        return $pool;
    }
}
