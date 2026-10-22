<?php
namespace App\Http\Controllers;

use App\Models\AssetRequest;
use App\Models\AuditLog;
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

        $allowedStatuses = ['delivered', 'monitoring', 'completed'];
        if (!in_array($request->status, $allowedStatuses, true)) {
            return back()->withErrors(['status' => 'Monitoring records can only be recorded after confirmed physical delivery of the asset.']);
        }

        $validated = $httpRequest->validate([
            'metric_name'  => ['required','string','max:255'],
            'metric_value' => ['required','string','max:255'],
            'notes'        => ['nullable','string','max:1000'],
        ]);

        $previousStatus = $request->status;

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

        AuditLog::create([
            'user_id'        => auth()->id(),
            'action'         => 'add_monitoring_record',
            'model_type'     => 'asset_request',
            'model_id'       => $request->id,
            'previous_state' => ['status' => $previousStatus],
            'new_state'      => [
                'status'       => $request->status,
                'metric_name'  => $validated['metric_name'],
                'metric_value' => $validated['metric_value'],
            ],
        ]);

        return back()->with('success', 'Monitoring record added.');
    }
}
