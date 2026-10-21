<x-layout title="Audit Log">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Audit Log</h1>
    <p class="text-slate-500">Review platform activity and workflow changes.</p>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 text-sm text-slate-500">
          <th class="pb-4 font-medium">User</th>
          <th class="pb-4 font-medium">Action</th>
          <th class="pb-4 font-medium">Model</th>
          <th class="pb-4 font-medium">Model ID</th>
          <th class="pb-4 font-medium">Time</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($logs as $log)
          <tr class="hover:bg-slate-50/50 transition">
            <td class="pb-4 text-sm">{{ $log->user?->name ?? 'System' }}</td>
            <td class="pb-4 text-sm font-medium text-slate-dark">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td>
            <td class="pb-4 text-sm">{{ ucfirst(str_replace('_', ' ', $log->model_type)) }}</td>
            <td class="pb-4 text-sm">{{ $log->model_id }}</td>
            <td class="pb-4 text-sm text-slate-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{ $logs->links() }}
</x-layout>
