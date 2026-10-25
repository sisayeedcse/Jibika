<?php

namespace App\Actions\Funding;

use App\Models\AuditLog;
use App\Models\ContributionOrPledge;
use App\Models\FundingPool;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContributePledgeAction
{
    public function execute(FundingPool $pool, float $amount, User $user): ContributionOrPledge
    {
        if (!$user->isContributor() && !$user->isAdmin()) {
            abort(403, 'Only contributors may make pledges.');
        }

        if ($pool->status !== 'open') {
            throw ValidationException::withMessages([
                'amount_bdt' => 'This funding pool is closed and no longer accepting pledges.',
            ]);
        }

        try {
            return DB::transaction(function () use ($pool, $amount, $user) {
                // Lock pool for update to prevent concurrent overfunding
                $lockedPool = FundingPool::where('id', $pool->id)->lockForUpdate()->first();
                if (!$lockedPool || $lockedPool->status !== 'open') {
                    throw new \InvalidArgumentException('This funding pool is closed and no longer accepting pledges.');
                }

                $remaining = max(0, (float) $lockedPool->target_amount_bdt - (float) $lockedPool->current_amount_bdt);
                if ($amount > $remaining) {
                    throw new \InvalidArgumentException('Pledge amount exceeds the remaining required funding of ' . number_format($remaining, 0) . ' BDT.');
                }

                $previousAmount = $lockedPool->current_amount_bdt;
                $lockedPool->increment('current_amount_bdt', $amount);
                $lockedPool->refresh();

                if ($lockedPool->current_amount_bdt >= $lockedPool->target_amount_bdt) {
                    $lockedPool->update(['status' => 'completed']);
                    if ($lockedPool->request) {
                        $lockedPool->request->update(['status' => 'funded']);
                        Notification::create([
                            'user_id'  => $lockedPool->request->worker_id,
                            'type'     => 'funding_completed',
                            'title'    => 'Funding Goal Reached',
                            'title_bn' => 'তহবিল লক্ষ্যমাত্রা অর্জিত হয়েছে',
                            'body_en'  => 'Your asset request #' . $lockedPool->request->id . ' has reached 100% funding and is ready for vendor quotations.',
                            'body_bn'  => 'আপনার সম্পদ অনুরোধ #' . $lockedPool->request->id . ' ১০০% অর্থায়ন পেয়েছে।',
                        ]);
                    }
                }

                $contribution = ContributionOrPledge::create([
                    'pool_id'                  => $lockedPool->id,
                    'contributor_id'           => $user->id,
                    'amount_bdt'               => $amount,
                    'is_demo'                  => true,
                    'is_confirmed_transaction' => false,
                ]);

                AuditLog::create([
                    'user_id'        => $user->id,
                    'action'         => 'contribute_funding',
                    'model_type'     => 'funding_pool',
                    'model_id'       => $lockedPool->id,
                    'previous_state' => ['current_amount' => (string) $previousAmount],
                    'new_state'      => ['current_amount' => (string) $lockedPool->current_amount_bdt],
                ]);

                return $contribution;
            });
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages([
                'amount_bdt' => $e->getMessage(),
            ]);
        }
    }
}
