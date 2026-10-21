<x-layout title="Monitoring">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Livelihood Monitoring</h1>
    <p class="text-slate-500">Track post-delivery impact for request #{{ $request->id }}.</p>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-6">
    <h2 class="text-xl font-bold mb-4">Add Monitoring Record</h2>
    <form action="{{ route('monitoring.store', $request) }}" method="POST" class="grid md:grid-cols-3 gap-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Metric Name</label>
        <input type="text" name="metric_name" placeholder="e.g. Monthly Income" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Metric Value</label>
        <input type="text" name="metric_value" placeholder="e.g. BDT 15,000" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required>
      </div>
      <div class="flex items-end">
        <button type="submit" class="px-6 py-3.5 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] transition">Add Record</button>
      </div>
    </form>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
    <h2 class="text-xl font-bold mb-4">Monitoring History</h2>
    @if($records->count())
      <div class="space-y-3">
        @foreach($records as $r)
          <div class="flex justify-between items-center p-3 rounded-xl border border-slate-100 bg-slate-50">
            <div>
              <div class="font-bold text-slate-dark">{{ $r->metric_name }}</div>
              <div class="text-sm text-slate-500">{{ $r->created_at->format('M d, Y') }}</div>
            </div>
            <div class="text-right">
              <div class="font-bold text-[#127A4A]">{{ $r->metric_value }}</div>
              @if($r->notes)
                <div class="text-xs text-slate-500">{{ $r->notes }}</div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-slate-500 text-sm">No monitoring records yet.</p>
    @endif
  </div>
</x-layout>
