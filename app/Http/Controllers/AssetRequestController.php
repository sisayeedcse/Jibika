<?php

namespace App\Http\Controllers;

use App\Actions\Assessments\AssessAssetRequestAction;
use App\Actions\AssetRequests\SubmitAssetRequestAction;
use App\Http\Requests\AssetRequest\StoreAssetRequestRequest;
use App\Http\Requests\AssetRequest\UpdateAssetRequestRequest;
use App\Models\AssetCategory;
use App\Models\AssetRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AssetRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = AssetRequest::with(['category', 'worker', 'verification', 'assessment', 'fundingPool', 'procurementOrder']);
        if ($user->isWorker()) {
            $query->where('worker_id', $user->id);
        }
        $requests = $query->latest()->paginate(15);
        return view('pages.requests.index', compact('requests'));
    }

    public function create()
    {
        $this->authorize('create', AssetRequest::class);
        $categories = AssetCategory::all();
        return view('pages.requests.create', compact('categories'));
    }

    public function store(StoreAssetRequestRequest $request)
    {
        $data = $request->validated();
        $data['worker_id'] = auth()->id();
        $data['status'] = 'draft';

        $record = AssetRequest::create($data);

        AuditLog::create([
            'user_id'    => auth()->id(),
            'action'     => 'create_request',
            'model_type' => 'asset_request',
            'model_id'   => $record->id,
            'new_state'  => ['status' => 'draft'],
        ]);

        return redirect()->route('requests.show', $record)->with('success', 'Draft saved.');
    }

    public function show(AssetRequest $request)
    {
        $request->load([
            'category',
            'worker',
            'verification',
            'assessment',
            'fundingPool.contributions',
            'procurementOrder.vendor',
            'procurementOrder.delivery',
            'monitoringRecords',
        ]);
        $this->authorize('view', $request);
        return view('pages.requests.show', compact('request'));
    }

    public function edit(AssetRequest $request)
    {
        $this->authorize('update', $request);
        $categories = AssetCategory::all();
        return view('pages.requests.edit', compact('request', 'categories'));
    }

    public function update(UpdateAssetRequestRequest $httpRequest, AssetRequest $request)
    {
        $data = $httpRequest->validated();
        $previous = [
            'requested_amount_bdt'     => (string) $request->requested_amount_bdt,
            'reason'                   => $request->reason,
            'livelihood_justification' => $request->livelihood_justification,
        ];

        $request->update($data);

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'update_draft_request',
            'model_type'     => 'asset_request',
            'model_id'       => $request->id,
            'previous_state' => $previous,
            'new_state'      => [
                'requested_amount_bdt'     => (string) $request->requested_amount_bdt,
                'reason'                   => $request->reason,
                'livelihood_justification' => $request->livelihood_justification,
            ],
        ]);

        return redirect()->route('requests.show', $request)->with('success', 'Request updated.');
    }

    public function submit(Request $httpRequest, AssetRequest $request, SubmitAssetRequestAction $action)
    {
        $action->execute($request, auth()->user());
        return redirect()->route('requests.show', $request)->with('success', 'Request submitted for verification.');
    }

    public function assess(AssetRequest $request, AssessAssetRequestAction $action)
    {
        $action->execute($request, auth()->user());
        return redirect()->route('requests.show', $request)->with('success', 'Assessment completed.');
    }
}
