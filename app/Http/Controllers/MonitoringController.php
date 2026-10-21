<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\MonitoringRecord;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(AssetRequest $request)
    {
        $this->authorize('view', $request);
        $records = MonitoringRecord::where('request_id', $request->id)->latest()->get();
        return view('pages.monitoring.index', compact('request','records'));
    }

    public function store(Request $request, AssetRequest $assetRequest)
    {
        $this->authorize('update', $assetRequest);
        $validated = $request->validate([
            'metric_name' => ['required','string','max:255'],
            'metric_value' => ['required','string','max:255'],
            'notes' => ['nullable','string','max:1000'],
        ]);
        MonitoringRecord::create([
            'request_id' => $assetRequest->id,
            'metric_name' => $validated['metric_name'],
            'metric_value' => $validated['metric_value'],
            'notes' => $validated['notes'],
            'recorded_at' => now(),
        ]);
        return back()->with('success', 'Monitoring record added.');
    }
}
