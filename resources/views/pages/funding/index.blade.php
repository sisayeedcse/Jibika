<x-layout title="Funding Opportunities">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Funding Opportunities</h1>
    <p class="text-[#475569]">Browse verified asset requests. Pledges shown are simulated — no real payments are processed.</p>
  </div>

  @if($pools && $pools->count())
    <div class="space-y-5">
      @foreach($pools as $pool)
        <article class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8 hover:shadow-card-hover transition-shadow">
          <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
            <div>
              <h3 class="text-lg font-extrabold tracking-tight text-[#0f172a]">{{ $pool->request->category?->name ?? 'Asset Request' }}</h3>
              <p class="text-sm text-[#475569]">Worker: <span class="font-medium text-[#0f172a]">{{ $pool->request->worker->name ?? 'Unknown' }}</span></p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.7rem] font-extrabold border
              {{ $pool->status === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
              {{ ucfirst($pool->status) }}
            </span>
          </div>

          <div class="mb-5">
            <div class="flex justify-between text-sm mb-2">
              <span class="font-medium text-[#475569]">Progress</span>
              <span class="font-extrabold text-[#0f172a]">BDT {{ number_format($pool->current_amount_bdt, 0) }} <span class="text-[#94a3b8] font-medium">/</span> {{ number_format($pool->target_amount_bdt, 0) }}</span>
            </div>
            <div class="w-full bg-[#f1f5f9] rounded-full h-2.5 overflow-hidden">
              <div class="bg-[#127A4A] h-2.5 rounded-full" style="width: {{ min(100, ($pool->current_amount_bdt / $pool->target_amount_bdt) * 100) }}%"></div>
            </div>
          </div>

          <div class="flex items-center gap-2 mb-4">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#fbeae6] text-[#E05D44] text-xs font-extrabold border border-[#E05D44]/15">Simulated</span>
            <span class="text-xs text-[#94a3b8]">No real payments are processed.</span>
          </div>

          @if($pool->status === 'open' && auth()->user()?->isContributor())
            <form action="{{ route('funding.contribute', $pool) }}" method="POST" aria-label="Simulated pledge form">
              @csrf
              <div class="flex gap-3">
                <label for="pledge-amount-{{ $pool->id }}" class="sr-only">Pledge amount</label>
                <input id="pledge-amount-{{ $pool->id }}" type="number" name="amount_bdt" placeholder="Amount (BDT)" class="flex-1 px-4 py-2.5 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required min="100" aria-required="true">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Pledge</button>
              </div>
              <p class="text-xs text-[#94a3b8] mt-2">This is a simulated pledge. No actual payment will be processed.</p>
            </form>
          @endif
        </article>
      @endforeach
    </div>
  @else
    <div class="bg-white rounded-2xl p-12 md:p-14 text-center shadow-card border border-[#f1f5f9]">
      <h3 class="text-base font-extrabold text-[#0f172a] mb-2">No open funding opportunities</h3>
      <p class="text-sm text-[#475569]">Verified requests that reach the funding stage will appear here.</p>
    </div>
  @endif
</x-layout>
