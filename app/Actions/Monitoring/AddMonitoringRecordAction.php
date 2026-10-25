<?php

namespace App\Actions\Monitoring;

use App\Models\AssetRequest;
use App\Models\AuditLog;
use App\Models\MonitoringRecord;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AddMonitoringRecordAction
{
    public function execute(AssetRequest $request, array $data, User $actor): MonitoringRecord
    {
        if (!$actor->isAdmin() && $actor->id !== $request->worker_id) {
            abort(403, 'You may not add monitoring records for this request.');
        }

        $allowedStatuses = ['delivered', 'monitoring', 'completed'];
        if (!in_array($request->status, $allowedStatuses, true)) {
            throw ValidationException::withMessages([
                'status' => 'Monitoring records can only be recorded after confirmed physical delivery of the asset.',
            ]);
        }

        $previousStatus = $request->status;

        $record = MonitoringRecord::create([
            'request_id'   => $request->id,
            'metric_name'  => $data['metric_name'],
            'metric_value' => $data['metric_value'],
            'notes'        => $data['notes'] ?? null,
            'recorded_at'  => now(),
        ]);

        if ($request->status === 'delivered') {
            $request->update(['status' => 'monitoring']);
        }

        AuditLog::create([
            'user_id'        => $actor->id,
            'action'         => 'add_monitoring_record',
            'model_type'     => 'asset_request',
            'model_id'       => $request->id,
            'previous_state' => ['status' => $previousStatus],
            'new_state'      => [
                'status'       => $request->status,
                'metric_name'  => $data['metric_name'],
                'metric_value' => $data['metric_value'],
            ],
        ]);

        return $record;
    }
}
