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

        $remaining = max(0, (float) $pool->target_amount_bdt - (float) $pool->current_amount_bdt);
        if ($validatedAmount > $remaining) {
            return back()->withErrors([
                'amount_bdt' => 'Pledge of BDT ' . number_format($validatedAmount, 0) . ' exceeds remaining needed amount of BDT ' . number_format($remaining, 0) . '.'
            ]);
        }

        // Use DB transaction to keep pool total consistent
        \Illuminate\Support\Facades\DB::transaction(function () use ($pool, $validatedAmount, $user) {
            $pool->increment('current_amount_bdt', $validatedAmount);
            $pool->refresh();

            if ($pool->current_amount_bdt >= $pool->target_amount_bdt) {
                $pool->update(['status' => 'completed']);
                if ($pool->request) {
                    $pool->request->update(['status' => 'funded']);
                    Notification::create([
                        'user_id'  => $pool->request->worker_id,
                        'type'     => 'funding_completed',
                        'title'    => 'Funding Goal Reached',
                        'title_bn' => 'তহবিল লক্ষ্যমাত্রা অর্জিত হয়েছে',
                        'body_en'  => 'Your asset request #' . $pool->request->id . ' has reached 100% funding and is ready for vendor quotations.',
                        'body_bn'  => 'আপনার সম্পদ অনুরোধ #' . $pool->request->id . ' ১০০% অর্থায়ন পেয়েছে।',
                    ]);
                }
            }

            ContributionOrPledge::create([
                'pool_id'                  => $pool->id,
                'contributor_id'           => $user->id,
                'amount_bdt'               => $validatedAmount,
                'is_demo'                  => true,
                'is_confirmed_transaction' => false,
            ]);

            AuditLog::create([
                'user_id'    => $user->id,
                'action'     => 'contribute_funding',
                'model_type' => 'funding_pool',
                'model_id'   => $pool->id,
                'new_state'  => ['current_amount' => (string) $pool->current_amount_bdt],
            ]);
        });

        return redirect()->route('funding.index')->with('success', 'Simulated pledge recorded. This is not a real payment.');
    }
}
