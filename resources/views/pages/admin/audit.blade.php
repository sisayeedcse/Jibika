<x-layout title="Audit Log">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Audit Log</h1>
    <p class="text-[#475569]">Review platform activity, workflow changes, and security-sensitive actions.</p>
  </div>

  <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[720px]">
        <thead>
          <tr class="border-b border-[#f1f5f9] text-xs uppercase tracking-wider text-[#94a3b8] font-medium">
            <th class="px-6 py-3.5">User</th>
            <th class="px-6 py-3.5">Action</th>
            <th class="px-6 py-3.5">Model</th>
            <th class="px-6 py-3.5">Model ID</th>
            <th class="px-6 py-3.5">Time</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#f1f5f9]">
          @foreach($logs as $log)
            <tr class="hover:bg-[#f8fafc]/60 transition-colors">
              <td class="px-6 py-4 text-sm text-[#475569]">{{ $log->user?->name ?? 'System' }}</td>
              <td class="px-6 py-4 text-sm font-semibold text-[#0f172a]">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</td>
              <td class="px-6 py-4 text-sm text-[#475569]">{{ ucfirst(str_replace('_', ' ', $log->model_type)) }}</td>
              <td class="px-6 py-4 text-sm text-[#475569]">{{ $log->model_id }}</td>
              <td class="px-6 py-4 text-xs text-[#94a3b8]">{{ $log->created_at->format('M d, Y H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-[#f1f5f9] bg-[#f8fafc]/40">
      {{ $logs->links() }}
    </div>
  </div>
</x-layout>
