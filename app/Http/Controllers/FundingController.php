<?php

namespace App\Http\Controllers;

use App\Actions\Funding\ContributePledgeAction;
use App\Actions\Funding\OpenFundingPoolAction;
use App\Http\Requests\Funding\ContributePledgeRequest;
use App\Models\AssetRequest;
use App\Models\FundingPool;
use Illuminate\Http\Request;

class FundingController extends Controller
{
    public function index()
    {
        $pools = FundingPool::with('request.worker')->where('status', 'open')->latest()->paginate(15);
        return view('pages.funding.index', compact('pools'));
    }

    public function openPool(Request $httpRequest, AssetRequest $request, OpenFundingPoolAction $action)
    {
        $action->execute($request, auth()->user());
        return redirect()->route('funding.index')->with('success', 'Funding pool opened successfully.');
    }

    public function contribute(ContributePledgeRequest $request, FundingPool $pool, ContributePledgeAction $action)
    {
        $validated = $request->validated();
        $action->execute($pool, (float) $validated['amount_bdt'], auth()->user());

        return redirect()->route('funding.index')->with('success', 'Simulated pledge recorded. This is not a real payment.');
    }
}
