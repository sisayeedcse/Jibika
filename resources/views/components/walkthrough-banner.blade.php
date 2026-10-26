@if(config('app.demo_mode'))
@php
  $deliveredReq = null;
  $submittedReq = null;
  $openPool     = null;
  $fundedReq    = null;
  try {
      $deliveredReq = \App\Models\AssetRequest::where('status', 'delivered')->first();
      $submittedReq = \App\Models\AssetRequest::where('status', 'submitted')->first();
      $openPool     = \App\Models\FundingPool::where('status', 'open')->first();
      $fundedReq    = \App\Models\AssetRequest::where('status', 'funded')->whereDoesntHave('procurementOrder')->first();
  } catch (\Throwable $e) {
      // In tests or unseeded environments, fail silently
  }
@endphp

<div id="demo-walkthrough-banner" class="bg-gradient-to-r from-emerald-950 via-slate-900 to-emerald-950 text-white border-b border-emerald-700/40 text-xs">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
    <div class="flex items-center justify-between gap-2">
      <div class="flex items-center gap-2 flex-wrap min-w-0">
        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[0.62rem] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 shrink-0">
          Demo Walkthrough
        </span>
        <span class="font-semibold text-emerald-100 text-[0.75rem] truncate">
          Follow the 9-Stage Asset Lifecycle across 5 Personas:
        </span>
      </div>

      <div class="flex items-center gap-1.5 shrink-0">
        <button type="button" onclick="const steps = document.getElementById('walkthrough-steps'); steps.classList.toggle('hidden'); this.querySelector('.lbl').textContent = steps.classList.contains('hidden') ? 'Show Personas' : 'Hide Personas';" class="inline-flex items-center gap-1 text-[0.68rem] text-emerald-200 hover:text-white px-2 py-0.5 rounded bg-emerald-800/40 hover:bg-emerald-800/70 transition-colors">
          <span class="lbl">Hide Personas</span>
        </button>
        <button type="button" onclick="document.getElementById('demo-walkthrough-banner').style.display='none'; sessionStorage.setItem('dismiss_walkthrough', 'true');" title="Dismiss banner" class="text-emerald-400 hover:text-white p-1 leading-none rounded hover:bg-emerald-800/50" aria-label="Dismiss banner">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
      </div>
    </div>

    <div id="walkthrough-steps" class="mt-2.5 pt-2.5 border-t border-emerald-800/50 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2 text-slate-200">
      {{-- Step 1: Delivered Asset (Worker Journey) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/70 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.62rem] font-bold uppercase tracking-wider text-emerald-400 mb-1">
            <span>1. Worker</span>
            <span class="px-1 py-0.2 rounded bg-emerald-500/20 text-emerald-300">Delivered</span>
          </div>
          <div class="font-semibold text-white text-[0.78rem]">Rahim's Machine</div>
          <p class="text-[0.68rem] text-slate-300 mt-0.5 leading-snug">Verified, funded, delivered with active net income impact log.</p>
        </div>
        <div class="mt-2 pt-1.5 border-t border-emerald-800/40 flex items-center justify-between">
          @if($deliveredReq)
            <a href="{{ route('requests.show', $deliveredReq) }}" class="text-[0.68rem] font-bold text-emerald-300 hover:text-white underline">Inspect &rarr;</a>
          @endif
          @guest
            <form action="{{ route('demo.login', 'worker') }}" method="POST">@csrf<button type="submit" class="text-[0.62rem] px-1.5 py-0.5 rounded bg-emerald-700 hover:bg-emerald-600 text-white font-medium">As Worker</button></form>
          @endguest
        </div>
      </div>

      {{-- Step 2: Verification Queue (Community Verifier) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/70 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.62rem] font-bold uppercase tracking-wider text-amber-300 mb-1">
            <span>2. Verifier</span>
            <span class="px-1 py-0.2 rounded bg-amber-500/20 text-amber-300">Submitted</span>
          </div>
          <div class="font-semibold text-white text-[0.78rem]">Anisur's Desk</div>
          <p class="text-[0.68rem] text-slate-300 mt-0.5 leading-snug">Request awaiting physical trade verification and notes.</p>
        </div>
        <div class="mt-2 pt-1.5 border-t border-emerald-800/40 flex items-center justify-between">
          @auth
            @if(auth()->user()->isCommunity())
              <a href="{{ route('verification.index') }}" class="text-[0.68rem] font-bold text-amber-300 hover:text-white underline">Review &rarr;</a>
            @endif
          @endauth
          @guest
            <form action="{{ route('demo.login', 'community') }}" method="POST">@csrf<button type="submit" class="text-[0.62rem] px-1.5 py-0.5 rounded bg-amber-700 hover:bg-amber-600 text-white font-medium">As Verifier</button></form>
          @endguest
        </div>
      </div>

      {{-- Step 3: Open Funding Pool (Contributor) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/70 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.62rem] font-bold uppercase tracking-wider text-sky-300 mb-1">
            <span>3. Contributor</span>
            <span class="px-1 py-0.2 rounded bg-sky-500/20 text-sky-300">Open Pool</span>
          </div>
          <div class="font-semibold text-white text-[0.78rem]">Fatima's Mixer</div>
          <p class="text-[0.68rem] text-slate-300 mt-0.5 leading-snug">20k/45k raised. Simulated pledge sponsors asset directly.</p>
        </div>
        <div class="mt-2 pt-1.5 border-t border-emerald-800/40 flex items-center justify-between">
          @auth
            <a href="{{ route('funding.index') }}" class="text-[0.68rem] font-bold text-sky-300 hover:text-white underline">Pools &rarr;</a>
          @endauth
          @guest
            <form action="{{ route('demo.login', 'contributor') }}" method="POST">@csrf<button type="submit" class="text-[0.62rem] px-1.5 py-0.5 rounded bg-sky-700 hover:bg-sky-600 text-white font-medium">As Sponsor</button></form>
          @endguest
        </div>
      </div>

      {{-- Step 4: Funded Request (Vendor Quotation) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/70 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.62rem] font-bold uppercase tracking-wider text-purple-300 mb-1">
            <span>4. Vendor</span>
            <span class="px-1 py-0.2 rounded bg-purple-500/20 text-purple-300">Funded</span>
          </div>
          <div class="font-semibold text-white text-[0.78rem]">Bengal Machinery</div>
          <p class="text-[0.68rem] text-slate-300 mt-0.5 leading-snug">Funded request ready for competitive supplier quotation.</p>
        </div>
        <div class="mt-2 pt-1.5 border-t border-emerald-800/40 flex items-center justify-between">
          @auth
            @if(auth()->user()->isVendor())
              <a href="{{ route('vendor.index') }}" class="text-[0.68rem] font-bold text-purple-300 hover:text-white underline">Quote &rarr;</a>
            @endif
          @endauth
          @guest
            <form action="{{ route('demo.login', 'vendor') }}" method="POST">@csrf<button type="submit" class="text-[0.62rem] px-1.5 py-0.5 rounded bg-purple-700 hover:bg-purple-600 text-white font-medium">As Vendor</button></form>
          @endguest
        </div>
      </div>

      {{-- Step 5: Administrator (Award Quotation & Governance) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/70 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.62rem] font-bold uppercase tracking-wider text-rose-300 mb-1">
            <span>5. Admin</span>
            <span class="px-1 py-0.2 rounded bg-rose-500/20 text-rose-300">Oversight</span>
          </div>
          <div class="font-semibold text-white text-[0.78rem]">Admin Console</div>
          <p class="text-[0.68rem] text-slate-300 mt-0.5 leading-snug">Award winning vendor tenders, open pools, and inspect audit logs.</p>
        </div>
        <div class="mt-2 pt-1.5 border-t border-emerald-800/40 flex items-center justify-between">
          @auth
            @if(auth()->user()->isAdmin())
              <a href="{{ route('admin.requests') }}" class="text-[0.68rem] font-bold text-rose-300 hover:text-white underline">Tenders &rarr;</a>
              <a href="{{ route('admin.audit') }}" class="text-[0.68rem] text-slate-400 hover:text-white">Audit</a>
            @endif
          @endauth
          @guest
            <form action="{{ route('demo.login', 'admin') }}" method="POST">@csrf<button type="submit" class="text-[0.62rem] px-1.5 py-0.5 rounded bg-rose-700 hover:bg-rose-600 text-white font-medium">As Admin</button></form>
          @endguest
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  if (sessionStorage.getItem('dismiss_walkthrough') === 'true') {
    const el = document.getElementById('demo-walkthrough-banner');
    if (el) el.style.display = 'none';
  }
</script>
@endif
