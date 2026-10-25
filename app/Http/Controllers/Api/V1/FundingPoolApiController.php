<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FundingPoolResource;
use App\Models\FundingPool;
use Illuminate\Http\Request;

class FundingPoolApiController extends Controller
{
    public function index(Request $request)
    {
        $query = FundingPool::with(['request.category', 'request.worker']);

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        } else {
            $query->where('status', 'open');
        }

        $pools = $query->latest()->paginate(15);
        return FundingPoolResource::collection($pools);
    }

    public function show(FundingPool $pool)
    {
        $pool->load(['request.category', 'request.worker']);
        return new FundingPoolResource($pool);
    }
}
