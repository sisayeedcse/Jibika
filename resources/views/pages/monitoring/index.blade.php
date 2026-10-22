<x-layout title="Monitoring — Request #{{ $request->id }}">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Livelihood Monitoring</h1>
    <p class="text-[#475569]">Post-delivery records for Request #{{ $request->id }}. Only real stored metrics are shown.</p>
  </div>

  <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8 mb-6" aria-label="Add monitoring record">
    <h2 class="text-base font-extrabold tracking-tight mb-5">Add Monitoring Record</h2>
    <form action="{{ route('monitoring.store', $request) }}" method="POST" class="grid md:grid-cols-3 gap-4" aria-label="Monitoring record form">
      @csrf
      <div>
        <label for="metric_name" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Metric Name</label>
        <input id="metric_name" type="text" name="metric_name" placeholder="e.g. Monthly Income" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required aria-required="true">
      </div>
      <div>
        <label for="metric_value" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Metric Value</label>
        <input id="metric_value" type="text" name="metric_value" placeholder="e.g. BDT 15,000" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required aria-required="true">
      </div>
      <div>
        <label for="notes" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Notes (optional)</label>
        <input id="notes" type="text" name="notes" placeholder="Context or source" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors">
      </div>
      <div class="md:col-span-3">
        <button type="submit" class="inline-flex items-center px-7 py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Add Record</button>
      </div>
    </form>
  </section>

  <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8" aria-label="Monitoring history">
    <h2 class="text-base font-extrabold tracking-tight mb-5">Monitoring History</h2>
    @if($records && $records->count())
      <div class="divide-y divide-[#f1f5f9]">
        @foreach($records as $r)
          <div class="py-4 first:pt-0 last:pb-0 flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="font-bold text-[#0f172a]">{{ $r->metric_name }}</div>
              <div class="text-xs text-[#94a3b8]">{{ $r->created_at->format('M d, Y') }}</div>
              @if($r->notes)
                <div class="text-xs text-[#475569] mt-0.5">{{ $r->notes }}</div>
              @endif
            </div>
            <div class="text-right">
              <div class="font-extrabold text-[#127A4A]">{{ $r->metric_value }}</div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      <p class="text-sm text-[#475569]">No monitoring records yet. Add real operational data only.</p>
    @endif
  </section>
</x-layout>
