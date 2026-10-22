<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\ContributionOrPledge;
use App\Models\FundingPool;
use App\Models\Notification;
use Illuminate\Http\Request;

class FundingController extends Controller
{
    public function index()
    {
        $pools = FundingPool::with('request.worker')->where('status','open')->latest()->paginate(15);
        return view('pages.funding.index', compact('pools'));
    }

    public function openPool(Request $httpRequest, AssetRequest $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Only administrators can open funding pools.');
        }

        if ($request->status !== 'ai_assessed') {
            return back()->withErrors(['status' => 'Only assessed requests can be opened for funding.']);
        }

        if ($request->fundingPool) {
            return back()->withErrors(['status' => 'A funding pool already exists for this request.']);
        }

        $pool = FundingPool::create([
            'request_id'        => $request->id,
            'target_amount_bdt' => $request->requested_amount_bdt,
            'current_amount_bdt'=> 0,
            'status'            => 'open',
            'is_demo'           => true,
            'is_simulated_mode' => true,
        ]);

        $request->update([
            'status'          => 'funding_open',
            'funding_pool_id' => $pool->id,
        ]);

        AuditLog::create([
            'user_id'    => auth()->id(),
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

        return redirect()->route('funding.index')->with('success', 'Funding pool opened successfully.');
    }

    public function contribute(Request $request, FundingPool $pool)
    {
        // Only contributors (and admins for testing) may pledge
        $user = auth()->user();
        if (!$user->isContributor() && !$user->isAdmin()) {
            abort(403, 'Only contributors may make pledges.');
        }

        if ($pool->status !== 'open') {
            return back()->withErrors(['amount_bdt' => 'This funding pool is closed and no longer accepting pledges.']);
        }

        $validated = $request->validate([
            'amount_bdt' => ['required','numeric','min:100','max:5000000'],
        ]);
        $validatedAmount = (float) $validated['amount_bdt'];

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($pool, $validatedAmount, $user) {
                // Lock pool for update to prevent concurrent race condition overfunding
                $lockedPool = FundingPool::where('id', $pool->id)->lockForUpdate()->first();
                if (!$lockedPool || $lockedPool->status !== 'open') {
                    throw new \InvalidArgumentException('This funding pool is closed and no longer accepting pledges.');
                }

                $remaining = max(0, (float) $lockedPool->target_amount_bdt - (float) $lockedPool->current_amount_bdt);
                if ($validatedAmount > $remaining) {
                    throw new \InvalidArgumentException('Pledge of BDT ' . number_format($validatedAmount, 0) . ' exceeds remaining needed amount of BDT ' . number_format($remaining, 0) . '.');
                }

                $previousAmount = $lockedPool->current_amount_bdt;
                $lockedPool->increment('current_amount_bdt', $validatedAmount);
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

                ContributionOrPledge::create([
                    'pool_id'                  => $lockedPool->id,
                    'contributor_id'           => $user->id,
                    'amount_bdt'               => $validatedAmount,
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
            });
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['amount_bdt' => $e->getMessage()]);
        }

        return redirect()->route('funding.index')->with('success', 'Simulated pledge recorded. This is not a real payment.');
    }
}
