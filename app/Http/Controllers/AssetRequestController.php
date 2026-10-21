<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssetRequest;
use App\Models\AssessmentRecord;
use App\Services\AIService;

class AssetRequestController extends Controller
{
    public function index()
    {
        $requests = AssetRequest::with('category','worker')->latest()->get();
        return view('pages.requests.index', compact('requests'));
    }

    public function show(AssetRequest $request)
    {
        $request->load('category','documents');
        return view('pages.requests.show', compact('request'));
    }

    public function create()
    {
        return view('pages.requests.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|exists:asset_categories,id',
            'requested_amount_bdt' => 'required|numeric|min:1000',
            'reason' => 'required|string|max:2000',
        ]);
        $data['worker_id'] = auth()->id();
        $data['status'] = 'draft';
        $record = AssetRequest::create($data);
        return redirect()->route('requests.show', $record)->with('success', 'Draft saved.');
    }

    public function assess(AssetRequest $request, AIService $ai)
    {
        $assessment = $ai->assess($request);
        $request->update(['status' => 'ai_assessed', 'assessment_id' => $assessment->id]);
        return redirect()->route('requests.show', $request)->with('success', 'Assessment completed.');
    }
}
