<x-layout title="Monitoring — Request #{{ $request->id }}">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('requests.show', $request) }}" class="text-xs font-semibold text-[#127A4A] hover:underline">&larr; Back to Request #{{ $request->id }}</a>
      </div>
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-1">Post-Delivery Livelihood Monitoring</h1>
      <p class="text-sm text-[#475569]">Asset: <strong class="text-[#0f172a]">{{ $request->category?->name ?? 'Productive Asset' }}</strong> &bull; Worker: <strong class="text-[#0f172a]">{{ $request->worker->name }}</strong></p>
    </div>
    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
      Verified Evidence Only
    </span>
  </div>

  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
      <!-- Add Record Section -->
      @if(auth()->user()->isAdmin() || auth()->id() === $request->worker_id)
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8" aria-label="Add monitoring record">
          <div class="flex items-center gap-3 mb-5">
            <div class="w-8 h-8 rounded-xl bg-[#127A4A]/10 text-[#127A4A] flex items-center justify-center font-bold text-sm">
              +
            </div>
            <div>
              <h2 class="text-base font-extrabold tracking-tight text-[#0f172a]">Record Productivity Milestone</h2>
              <p class="text-xs text-[#475569]">Enter actual verified household or enterprise metrics.</p>
            </div>
          </div>

          <form action="{{ route('monitoring.store', $request) }}" method="POST" class="space-y-4" aria-label="Monitoring record form">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
              <div>
                <label for="metric_name" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">Metric Name</label>
                <input id="metric_name" type="text" name="metric_name" placeholder="e.g. Monthly Revenue, Daily Output" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required aria-required="true">
              </div>
              <div>
                <label for="metric_value" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">Metric Value</label>
                <input id="metric_value" type="text" name="metric_value" placeholder="e.g. BDT 18,500 / month, 12 shirts/day" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required aria-required="true">
              </div>
            </div>

            <div>
              <label for="notes" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">Observational Notes (Optional)</label>
              <textarea id="notes" name="notes" rows="2" placeholder="e.g. Sewing speed increased; took on 2 additional bulk orders from local school." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y"></textarea>
            </div>

            <button type="submit" class="inline-flex items-center px-6 py-2.5 rounded-xl bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
              Save Monitoring Record
            </button>
          </form>
        </section>
      @endif

      <!-- Monitoring History Section -->
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8" aria-label="Monitoring history">
        <h2 class="text-base font-extrabold tracking-tight text-[#0f172a] mb-5">Recorded Livelihood Milestones</h2>

        @if($records && $records->count())
          <div class="divide-y divide-[#f1f5f9]">
            @foreach($records as $r)
              <div class="py-4 first:pt-0 last:pb-0 flex items-start justify-between gap-4">
                <div>
                  <h3 class="text-sm font-bold text-[#0f172a]">{{ $r->metric_name }}</h3>
                  <p class="text-xs text-[#94a3b8] mt-0.5">
                    Recorded {{ $r->recorded_at ? $r->recorded_at->format('F d, Y') : 'Recently' }}
                  </p>
                  @if($r->notes)
                    <p class="text-xs text-[#475569] mt-2 bg-[#f8fafc] p-3 rounded-xl border border-[#f1f5f9]">
                      {{ $r->notes }}
                    </p>
                  @endif
                </div>
                <div class="text-right shrink-0">
                  <span class="inline-block px-3 py-1 rounded-xl bg-emerald-50 text-[#127A4A] font-extrabold text-sm border border-emerald-100">
                    {{ $r->metric_value }}
                  </span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="p-8 text-center bg-[#f8fafc] rounded-xl border border-[#f1f5f9]">
            <p class="text-xs text-[#475569]">No monitoring records logged yet. As the worker uses the asset, periodic observations and income records will be saved here.</p>
          </div>
        @endif
      </section>
    </div>

    <!-- Integrity Sidebar -->
    <aside class="space-y-6">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-3">Integrity Standard</h3>
        <p class="text-xs text-[#475569] leading-relaxed mb-4">
          JIBIKA never manufactures fake income gains, simulated percentages, or estimated carbon savings. All values displayed reflect verified records submitted by workers and corroborated during monitoring visits.
        </p>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[0.7rem] text-[#475569]">
          <strong>Audit Reference:</strong> Request ID #{{ $request->id }} &bull; Status: {{ strtoupper(str_replace('_', ' ', $request->status)) }}
        </div>
      </div>
    </aside>
  </div>
</x-layout>
