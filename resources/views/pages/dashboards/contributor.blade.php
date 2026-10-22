<x-layout title="Contributor Workspace — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <h1 class="text-[1.75rem] md:text-[2.2rem] font-extrabold tracking-tight text-[#0f172a]">Funding Coordination Desk</h1>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-[#127A4A] border border-emerald-200">
          Contributor
        </span>
      </div>
      <p class="text-sm text-[#475569]">Welcome, <span class="font-semibold text-[#0f172a]">{{ $user->name }}</span>. Browse community-verified worker opportunities and allocate simulated pledges to empower local livelihoods.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('funding.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        Explore Open Pools
      </a>
    </div>
  </div>

  <!-- Mandatory Disclosure Banner -->
  <div class="mb-8 p-4 rounded-2xl bg-amber-50/80 border border-amber-200 flex items-center gap-3 text-xs text-amber-900">
    <div class="w-6 h-6 rounded-lg bg-amber-100 flex items-center justify-center shrink-0 font-bold text-amber-800">i</div>
    <p><strong>Simulation Mode Notice:</strong> Pledges in JIBIKA coordinate collective capital intention. In current simulation mode, no real banking transactions or credit cards are debited. All pledges are clearly recorded as simulated commitments.</p>
  </div>

  <!-- Key Metrics Row -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Total Pledged (Simulated)</p>
      <div class="text-3xl font-black text-[#127A4A]">BDT {{ number_format($stats['total_pledged'], 0) }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Collective funding allocated</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Livelihoods Supported</p>
      <div class="text-3xl font-black text-[#0f172a]">{{ $stats['projects_backed'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Distinct worker asset pools backed</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Open Opportunities</p>
      <div class="text-3xl font-black text-blue-600">{{ $stats['open_opportunities'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Currently seeking funding</p>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="grid lg:grid-cols-3 gap-8">
    <!-- Open Pools -->
    <div class="lg:col-span-2 space-y-6">
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
        <div class="p-6 md:p-7 border-b border-[#f1f5f9] flex items-center justify-between">
          <div>
            <h2 class="text-base font-extrabold tracking-tight text-[#0f172a]">Verified Productive Opportunities</h2>
            <p class="text-xs text-[#475569] mt-0.5">Asset pools authenticated by local community verifiers</p>
          </div>
          <a href="{{ route('funding.index') }}" class="text-xs font-semibold text-[#127A4A] hover:underline">
            View All Pools &rarr;
          </a>
        </div>

        @if($openPools->count())
          <div class="grid sm:grid-cols-2 gap-5 p-6">
            @foreach($openPools as $pool)
              @php
                $pct = $pool->target_amount_bdt > 0 ? min(100, round(($pool->current_amount_bdt / $pool->target_amount_bdt) * 100)) : 0;
              @endphp
              <div class="rounded-xl border border-[#e2e8f0] p-5 hover:border-[#127A4A]/40 transition-colors flex flex-col justify-between bg-white">
                <div>
                  <div class="flex items-center justify-between gap-2 mb-2">
                    <span class="text-[0.65rem] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md bg-emerald-50 text-[#127A4A]">
                      {{ $pool->request->category?->name ?? 'Productive Asset' }}
                    </span>
                    <span class="text-xs font-bold text-[#127A4A]">{{ $pct }}%</span>
                  </div>

                  <h3 class="text-sm font-bold text-[#0f172a] mb-1">
                    Worker: {{ $pool->request->worker->name }}
                  </h3>
                  <p class="text-xs text-[#475569] line-clamp-2 mb-4">
                    {{ $pool->request->reason }}
                  </p>
                </div>

                <div>
                  <!-- Progress Bar -->
                  <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden mb-3">
                    <div class="bg-[#127A4A] h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                  </div>

                  <div class="flex items-center justify-between text-xs mb-4">
                    <span class="font-bold text-[#0f172a]">BDT {{ number_format($pool->current_amount_bdt, 0) }}</span>
                    <span class="text-[#94a3b8]">Goal: BDT {{ number_format($pool->target_amount_bdt, 0) }}</span>
                  </div>

                  <form action="{{ route('funding.contribute', $pool) }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="number" name="amount_bdt" placeholder="Amount (BDT)" min="100" class="w-full px-3 py-1.5 text-xs rounded-lg border border-[#e2e8f0] focus-ring" required>
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-[#127A4A] text-white text-xs font-bold hover:bg-[#0e5a36] shrink-0 transition-colors">
                      Pledge
                    </button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="p-12 text-center">
            <h3 class="text-sm font-bold text-[#0f172a] mb-1">No Open Funding Pools</h3>
            <p class="text-xs text-[#475569] max-w-sm mx-auto">All verified requests have met their current targets. New opportunities will appear after community verification.</p>
          </div>
        @endif
      </section>

      <!-- My Pledge History -->
      @if($myPledges->count())
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6 md:p-7">
          <h2 class="text-base font-extrabold tracking-tight text-[#0f172a] mb-4">My Pledge History</h2>
          <div class="divide-y divide-[#f1f5f9]">
            @foreach($myPledges as $pledge)
              <div class="py-3.5 flex items-center justify-between text-xs">
                <div>
                  <span class="font-bold text-[#0f172a]">Pool #{{ $pledge->pool_id }} — {{ $pledge->pool->request->worker->name ?? 'Worker' }}</span>
                  <p class="text-[#94a3b8] mt-0.5">Recorded on {{ $pledge->created_at->format('M d, Y') }} &bull; Simulated commitment</p>
                </div>
                <div class="text-right">
                  <span class="font-bold text-[#127A4A] text-sm">+BDT {{ number_format($pledge->amount_bdt, 0) }}</span>
                  <p class="text-[0.65rem] text-[#94a3b8] mt-0.5">Simulated Pledge</p>
                </div>
              </div>
            @endforeach
          </div>
        </section>
      @endif
    </div>

    <!-- Impact Guidelines -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">How JIBIKA Capital Works</h3>
        <div class="space-y-3.5 text-xs text-[#475569]">
          <div class="p-3 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
            <p class="font-bold text-[#0f172a] mb-1">No Intermediate Cash</p>
            <p>Funds never go to workers as unencumbered cash. Capital is paid directly to verified suppliers upon equipment delivery.</p>
          </div>
          <div class="p-3 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
            <p class="font-bold text-[#0f172a] mb-1">Post-Delivery Tracking</p>
            <p>Every funded asset is tracked through periodic monitoring records to document household productivity improvements.</p>
          </div>
          <div class="p-3 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
            <p class="font-bold text-[#0f172a] mb-1">Community Governance</p>
            <p>Requests must be endorsed by local Somitte verifiers before appearing in this catalogue.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-layout>
