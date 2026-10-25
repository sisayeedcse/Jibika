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

<div id="demo-walkthrough-banner" class="bg-gradient-to-r from-emerald-900 via-slate-900 to-emerald-950 text-white border-b border-emerald-700/40 text-xs">
  <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-2.5">
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div class="flex items-center gap-2">
        <span class="inline-flex items-center px-2 py-0.5 rounded text-[0.65rem] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/30">
          Demo Walkthrough
        </span>
        <span class="font-semibold text-emerald-100 hidden sm:inline">Follow the 9-Stage Asset Lifecycle:</span>
      </div>

      <div class="flex items-center gap-2 ml-auto">
        <button type="button" onclick="document.getElementById('walkthrough-steps').classList.toggle('hidden'); this.querySelector('.arrow').classList.toggle('rotate-180');" class="inline-flex items-center gap-1 text-[0.7rem] text-emerald-200 hover:text-white px-2 py-0.5 rounded bg-emerald-800/40 hover:bg-emerald-800/70 transition-colors">
          <span>Explore Persona Milestones</span>
          <svg class="w-3.5 h-3.5 arrow transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </button>
      </div>
    </div>

    <div id="walkthrough-steps" class="mt-3 pt-3 border-t border-emerald-800/50 grid grid-cols-1 md:grid-cols-4 gap-2 text-slate-200">
      {{-- Step 1: Delivered Asset (Worker Journey) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/60 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.65rem] font-bold uppercase tracking-wider text-emerald-400 mb-1">
            <span>Stage 1: Completed</span>
            <span class="px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300">Delivered</span>
          </div>
          <div class="font-semibold text-white text-[0.8rem]">Rahim's Sewing Machine</div>
          <p class="text-[0.7rem] text-slate-300 mt-0.5">Verified, 100% funded, delivered, with active net income impact record.</p>
        </div>
        <div class="mt-2 pt-2 border-t border-emerald-800/40 flex items-center justify-between">
          @if($deliveredReq)
            <a href="{{ route('requests.show', $deliveredReq) }}" class="text-[0.7rem] font-bold text-emerald-300 hover:text-white underline">Inspect Request &rarr;</a>
          @endif
          @guest
            <form action="{{ route('demo.login', 'worker') }}" method="POST">@csrf<button type="submit" class="text-[0.65rem] px-2 py-0.5 rounded bg-emerald-700 hover:bg-emerald-600 text-white font-medium">As Worker</button></form>
          @endguest
        </div>
      </div>

      {{-- Step 2: Verification Queue (Community Verifier) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/60 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.65rem] font-bold uppercase tracking-wider text-amber-300 mb-1">
            <span>Stage 2: Verification</span>
            <span class="px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300">Submitted</span>
          </div>
          <div class="font-semibold text-white text-[0.8rem]">Anisur's Review Desk</div>
          <p class="text-[0.7rem] text-slate-300 mt-0.5">Request awaiting on-ground community physical verification &amp; recommendation.</p>
        </div>
        <div class="mt-2 pt-2 border-t border-emerald-800/40 flex items-center justify-between">
          @auth
            @if(auth()->user()->isCommunity())
              <a href="{{ route('verification.index') }}" class="text-[0.7rem] font-bold text-amber-300 hover:text-white underline">Open Desk &rarr;</a>
            @endif
          @endauth
          @guest
            <a href="{{ route('login') }}" class="text-[0.7rem] text-slate-400">Login to verify</a>
            <form action="{{ route('demo.login', 'community') }}" method="POST">@csrf<button type="submit" class="text-[0.65rem] px-2 py-0.5 rounded bg-amber-700 hover:bg-amber-600 text-white font-medium">As Verifier</button></form>
          @endguest
        </div>
      </div>

      {{-- Step 3: Open Funding Pool (Contributor) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/60 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.65rem] font-bold uppercase tracking-wider text-sky-300 mb-1">
            <span>Stage 3: Crowdfund</span>
            <span class="px-1.5 py-0.2 rounded bg-sky-500/20 text-sky-300">Open Pool</span>
          </div>
          <div class="font-semibold text-white text-[0.8rem]">Fatima's Dough Mixer</div>
          <p class="text-[0.7rem] text-slate-300 mt-0.5">20k of 45k raised. Simulated contributor pledge directly sponsors asset unit.</p>
        </div>
        <div class="mt-2 pt-2 border-t border-emerald-800/40 flex items-center justify-between">
          @auth
            <a href="{{ route('funding.index') }}" class="text-[0.7rem] font-bold text-sky-300 hover:text-white underline">View Pools &rarr;</a>
          @endauth
          @guest
            <form action="{{ route('demo.login', 'contributor') }}" method="POST">@csrf<button type="submit" class="text-[0.65rem] px-2 py-0.5 rounded bg-sky-700 hover:bg-sky-600 text-white font-medium">As Contributor</button></form>
          @endguest
        </div>
      </div>

      {{-- Step 4: Funded Request (Vendor Quotation) --}}
      <div class="p-2.5 rounded-lg bg-emerald-950/60 border border-emerald-700/30 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between text-[0.65rem] font-bold uppercase tracking-wider text-purple-300 mb-1">
            <span>Stage 4: Procurement</span>
            <span class="px-1.5 py-0.2 rounded bg-purple-500/20 text-purple-300">Funded</span>
          </div>
          <div class="font-semibold text-white text-[0.8rem]">Bengal Machinery Tender</div>
          <p class="text-[0.7rem] text-slate-300 mt-0.5">Fully funded pump request ready for competitive vendor quotation &amp; admin award.</p>
        </div>
        <div class="mt-2 pt-2 border-t border-emerald-800/40 flex items-center justify-between">
          @auth
            @if(auth()->user()->isVendor())
              <a href="{{ route('vendor.index') }}" class="text-[0.7rem] font-bold text-purple-300 hover:text-white underline">Vendor Desk &rarr;</a>
            @elseif(auth()->user()->isAdmin())
              <a href="{{ route('admin.requests') }}" class="text-[0.7rem] font-bold text-purple-300 hover:text-white underline">Admin Desk &rarr;</a>
            @endif
          @endauth
          @guest
            <form action="{{ route('demo.login', 'vendor') }}" method="POST">@csrf<button type="submit" class="text-[0.65rem] px-2 py-0.5 rounded bg-purple-700 hover:bg-purple-600 text-white font-medium">As Vendor</button></form>
          @endguest
        </div>
      </div>
    </div>
  </div>
</div>
@endif
