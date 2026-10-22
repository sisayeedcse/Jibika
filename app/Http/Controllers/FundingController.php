<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\ContributionOrPledge;
use App\Models\FundingPool;
use Illuminate\Http\Request;

class FundingController extends Controller
{
    public function index()
    {
        $pools = FundingPool::with('request.worker')->where('status','open')->latest()->paginate(15);
        return view('pages.funding.index', compact('pools'));
    }

    public function contribute(Request $request, FundingPool $pool)
    {
        // Only contributors (and admins for testing) may pledge
        $user = auth()->user();
        if (!$user->isContributor() && !$user->isAdmin()) {
            abort(403, 'Only contributors may make pledges.');
        }

        $validated = $request->validate([
            'amount_bdt' => ['required','numeric','min:100'],
        ]);
        $validatedAmount = $validated['amount_bdt'];

        // Use DB transaction to keep pool total consistent
        \Illuminate\Support\Facades\DB::transaction(function () use ($pool, $validatedAmount, $user) {
            $pool->increment('current_amount_bdt', $validatedAmount);
            $pool->refresh();
            if ($pool->current_amount_bdt >= $pool->target_amount_bdt) {
                $pool->update(['status' => 'completed']);
            }
            ContributionOrPledge::create([
                'pool_id'                => $pool->id,
                'contributor_id'         => $user->id,
                'amount_bdt'             => $validatedAmount,
                'is_demo'                => true,
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
