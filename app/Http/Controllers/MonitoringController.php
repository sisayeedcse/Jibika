<?php

namespace App\Http\Controllers;

use App\Actions\Monitoring\AddMonitoringRecordAction;
use App\Http\Requests\Monitoring\StoreMonitoringRecordRequest;
use App\Models\AssetRequest;
use App\Models\MonitoringRecord;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(AssetRequest $request)
    {
        $this->authorize('view', $request);
        $records = MonitoringRecord::where('request_id', $request->id)->latest()->get();
        return view('pages.monitoring.index', compact('request', 'records'));
    }

    public function store(StoreMonitoringRecordRequest $formRequest, AssetRequest $request, AddMonitoringRecordAction $action)
    {
        $action->execute($request, $formRequest->validated(), auth()->user());
        return back()->with('success', 'Monitoring record added.');
    }
}
