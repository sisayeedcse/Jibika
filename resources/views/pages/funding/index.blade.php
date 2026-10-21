<x-layout title="Funding Opportunities">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Funding Opportunities</h1>
    <p class="text-slate-500">Discover verified asset requests and make simulated pledges.</p>
  </div>

  <div class="space-y-4">
    @foreach($pools as $pool)
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <div class="flex justify-between items-start mb-4">
          <div>
            <h3 class="text-xl font-bold">{{ $pool->request->category?->name ?? 'Asset Request' }}</h3>
            <p class="text-sm text-slate-500">By {{ $pool->request->worker->name }}</p>
          </div>
          <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-bold 
            {{ $pool->status === 'completed' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
            {{ ucfirst($pool->status) }}
          </span>
        </div>
        <div class="mb-4">
          <div class="flex justify-between text-sm mb-1">
            <span class="text-slate-500">Target</span>
            <span class="font-medium">BDT {{ number_format($pool->target_amount_bdt, 0) }}</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-2">
            <div class="bg-brand-green h-2 rounded-full" style="width: {{ min(100, ($pool->current_amount_bdt / $pool->target_amount_bdt) * 100) }}%"></div>
          </div>
          <p class="text-xs text-slate-500 mt-1">
            <span class="text-[#E05D44] font-medium">● Simulated</span> — No real money processed.
          </p>
        </div>
        @if($pool->status === 'open' && auth()->user()->isContributor())
          <form action="{{ route('funding.contribute', $pool) }}" method="POST" class="mt-4">
            @csrf
            <div class="flex gap-2">
              <input type="number" name="amount_bdt" placeholder="Pledge amount (BDT)" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 focus-ring" required min="100">
              <button type="submit" class="px-6 py-3 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] transition">Pledge</button>
            </div>
            <p class="text-xs text-slate-500 mt-2">This is a simulated pledge. No actual payment will be processed.</p>
          </form>
        @endif
      </div>
    @endforeach
  </div>
</x-layout>
