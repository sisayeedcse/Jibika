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
        $this->authorize('create', AssetRequest::class);
        $categories = AssetCategory::all();
        return view('pages.requests.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', AssetRequest::class);

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

    public function update(Request $httpRequest, AssetRequest $request)
    {
        $this->authorize('update', $request);
        $data = $httpRequest->validate([
            'category_id' => 'nullable|exists:asset_categories,id',
            'requested_amount_bdt' => ['required','numeric','min:1000','max:5000000'],
            'reason' => ['required','string','max:2000'],
            'livelihood_justification' => ['required','string','max:2000'],
        ]);
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

    public function submit(Request $httpRequest, AssetRequest $request)
    {
        // Check ownership first before status check to give correct 403 for non-owners
        if (auth()->id() !== $request->worker_id) {
            abort(403, 'You do not own this request.');
        }
        // Status check produces a validation-style error, not a 403
        if ($request->status !== 'draft') {
            return back()->withErrors(['status' => 'Only draft requests can be submitted.']);
        }

        $verifier = \App\Models\User::where('role', 'community')->where('status', 'active')->first();
        $verificationId = null;

        if ($verifier) {
            $verification = \App\Models\CommunityVerification::create([
                'request_id'          => $request->id,
                'community_member_id' => $verifier->id,
                'status'              => 'pending',
            ]);
            $verificationId = $verification->id;

            \App\Models\Notification::create([
                'user_id'  => $verifier->id,
                'type'     => 'new_verification_assigned',
                'title'    => 'New Asset Request to Verify',
                'title_bn' => 'নতুন সম্পদ অনুরোধ যাচাইকরণ',
                'body_en'  => 'Asset request #' . $request->id . ' has been submitted and assigned for community verification.',
                'body_bn'  => 'সম্পদ অনুরোধ #' . $request->id . ' জমা দেওয়া হয়েছে এবং যাচাইয়ের জন্য বরাদ্দ করা হয়েছে।',
            ]);
        }

        $request->update([
            'status'          => 'submitted',
            'verification_id' => $verificationId ?? $request->verification_id,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'submit_request',
            'model_type' => 'asset_request',
            'model_id' => $request->id,
            'new_state' => ['status' => 'submitted', 'verification_id' => $verificationId],
        ]);
        return redirect()->route('requests.show', $request)->with('success', 'Request submitted for verification.');
    }

    public function assess(AssetRequest $request, AIService $ai)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $user->id !== $request->worker_id) {
            abort(403, 'You may not trigger assessment for this request.');
        }
        if ($request->status !== 'verified') {
            return back()->withErrors(['status' => 'Assessment can only be triggered for verified requests.']);
        }
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
