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
        $pools = FundingPool::with('request.worker')->latest()->paginate(15);
        return view('pages.funding.index', compact('pools'));
    }

    public function contribute(Request $request, FundingPool $pool)
    {
        $request->validate([
            'amount_bdt' => ['required','numeric','min:100'],
        ]);
        $validatedAmount = $request->validated('amount_bdt');
        $pool->increment('current_amount_bdt', $validatedAmount);
        if ($pool->current_amount_bdt >= $pool->target_amount_bdt) {
            $pool->status = 'completed';
            $pool->save();
        }
        ContributionOrPledge::create([
            'pool_id' => $pool->id,
            'contributor_id' => auth()->id(),
            'amount_bdt' => $validatedAmount,
            'is_demo' => true,
            'is_confirmed_transaction' => false,
        ]);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'contribute_funding',
            'model_type' => 'funding_pool',
            'model_id' => $pool->id,
            'new_state' => ['current_amount' => (string) $pool->current_amount_bdt],
        ]);
        return redirect()->route('funding.index')->with('success', 'Simulated pledge recorded.');
    }
}
