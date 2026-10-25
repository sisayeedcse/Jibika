<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssetRequestResource;
use App\Models\AssetRequest;
use Illuminate\Http\Request;

class AssetRequestApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = AssetRequest::with(['category', 'verification', 'assessment']);

        if ($user && $user->isWorker()) {
            $query->where('worker_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $requests = $query->latest()->paginate(15);
        return AssetRequestResource::collection($requests);
    }

    public function show(Request $request, AssetRequest $assetRequest)
    {
        $user = $request->user();
        if ($user && !$user->isAdmin() && $user->id !== $assetRequest->worker_id) {
            abort(403, 'Unauthorized access to asset request.');
        }

        $assetRequest->load(['category', 'verification', 'assessment']);
        return new AssetRequestResource($assetRequest);
    }
}
