<?php
namespace App\Http\Controllers;

use App\Models\AssetCategory;
use App\Models\AssetRequest;
use App\Models\AssessmentRecord;
use App\Models\AuditLog;
use App\Services\AIService;
use Illuminate\Http\Request;

class AssetRequestController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $query = AssetRequest::with(['category','worker','verification','assessment','fundingPool','procurementOrder']);
        if ($user->isWorker()) {
            $query->where('worker_id', $user->id);
        }
        $requests = $query->latest()->paginate(15);
        return view('pages.requests.index', compact('requests'));
    }

    public function create()
    {
        $categories = AssetCategory::all();
        return view('pages.requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:asset_categories,id',
            'requested_amount_bdt' => ['required','numeric','min:1000','max:5000000'],
            'reason' => ['required','string','max:2000'],
            'livelihood_justification' => ['required','string','max:2000'],
        ]);

        $data['worker_id'] = auth()->id();
        $data['status'] = 'draft';
        $record = AssetRequest::create($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_request',
            'model_type' => 'asset_request',
            'model_id' => $record->id,
            'new_state' => ['status' => 'draft'],
        ]);

        return redirect()->route('requests.show', $record)->with('success', 'Draft saved.');
    }

    public function show(AssetRequest $request)
    {
        $request->load(['category','worker','verification','assessment','fundingPool.contributions','procurementOrder.vendor','procurementOrder.delivery','monitoringRecords']);
        $this->authorize('view', $request);
        return view('pages.requests.show', compact('request'));
    }

    public function edit(AssetRequest $request)
    {
        $this->authorize('update', $request);
        $categories = AssetCategory::all();
        return view('pages.requests.edit', compact('request','categories'));
    }

    public function update(Request $request, AssetRequest $requestModel)
    {
        $this->authorize('update', $requestModel);
        $data = $request->validate([
            'category_id' => 'nullable|exists:asset_categories,id',
            'requested_amount_bdt' => ['required','numeric','min:1000','max:5000000'],
            'reason' => ['required','string','max:2000'],
            'livelihood_justification' => ['required','string','max:2000'],
        ]);
        $requestModel->update($data);
        return redirect()->route('requests.show', $requestModel)->with('success', 'Request updated.');
    }

    public function submit(Request $request, AssetRequest $requestModel)
    {
        $this->authorize('update', $requestModel);
        if ($requestModel->status !== 'draft') {
            return back()->withErrors(['status' => 'Only drafts can be submitted.']);
        }
        $requestModel->update(['status' => 'submitted']);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'submit_request',
            'model_type' => 'asset_request',
            'model_id' => $requestModel->id,
            'new_state' => ['status' => 'submitted'],
        ]);
        return redirect()->route('requests.show', $requestModel)->with('success', 'Request submitted for verification.');
    }

    public function assess(AssetRequest $request, AIService $ai)
    {
        $this->authorize('view', $request);
        $assessment = $ai->assess($request);
        $request->update(['status' => 'ai_assessed', 'assessment_id' => $assessment->id]);
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'assess_request',
            'model_type' => 'asset_request',
            'model_id' => $request->id,
            'new_state' => ['status' => 'ai_assessed', 'assessment_id' => $assessment->id],
        ]);
        return redirect()->route('requests.show', $request)->with('success', 'Assessment completed.');
    }
}
