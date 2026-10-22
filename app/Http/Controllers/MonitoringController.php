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

    /**
     * The route is: POST /requests/{request}/monitoring
     * Laravel binds {request} → $assetRequest via explicit binding in the route definition.
     * We use a type-hinted Illuminate\Http\Request named $httpRequest to avoid collision.
     */
    public function store(Request $httpRequest, AssetRequest $request)
    {
        // The owning worker and admins may add monitoring records.
        $user = auth()->user();
        if (!$user->isAdmin() && $user->id !== $request->worker_id) {
            abort(403, 'You may not add monitoring records for this request.');
        }

        $allowedStatuses = ['procured', 'delivered', 'monitoring', 'completed'];
        if (!in_array($request->status, $allowedStatuses, true)) {
            return back()->withErrors(['status' => 'Monitoring records can only be recorded after asset delivery.']);
        }

        $validated = $httpRequest->validate([
            'metric_name'  => ['required','string','max:255'],
            'metric_value' => ['required','string','max:255'],
            'notes'        => ['nullable','string','max:1000'],
        ]);

        MonitoringRecord::create([
            'request_id'   => $request->id,
            'metric_name'  => $validated['metric_name'],
            'metric_value' => $validated['metric_value'],
            'notes'        => $validated['notes'] ?? null,
            'recorded_at'  => now(),
        ]);

        if ($request->status === 'delivered') {
            $request->update(['status' => 'monitoring']);
        }

        return back()->with('success', 'Monitoring record added.');
    }
}
