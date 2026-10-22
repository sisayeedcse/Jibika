<x-layout title="Request #{{ $request->id }}">
  <div class="mb-7">
    <div class="flex flex-wrap items-center gap-3 mb-3">
      <h1 class="text-[1.6rem] md:text-[2rem] font-extrabold tracking-tight">Request #{{ $request->id }}</h1>
      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.7rem] font-bold border
        {{ $request->status === 'draft' ? 'bg-amber-50 text-amber-700 border-amber-200' : (($request->status === 'verified' || $request->status === 'ai_assessed') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-[#475569] border-slate-200') }}">
        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
      </span>
    </div>
    <p class="text-sm text-[#475569]">Submitted by <span class="font-medium text-[#0f172a]">{{ $request->worker->name ?? 'Unknown' }}</span> • Last updated {{ $request->updated_at->diffForHumans() }}</p>
  </div>

  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
      <!-- Details -->
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7" aria-label="Request details">
        <h2 class="text-base font-extrabold tracking-tight mb-5">Request Details</h2>
        <div class="grid sm:grid-cols-2 gap-6 mb-6">
          <div>
            <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Asset Category</p>
            <p class="font-semibold text-[#0f172a]">{{ $request->category?->name ?? 'Uncategorized' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Requested Amount</p>
            <p class="font-extrabold text-[#127A4A]">BDT {{ number_format($request->requested_amount_bdt, 0) }}</p>
          </div>
          <div>
            <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Worker</p>
            <p class="font-medium text-[#0f172a]">{{ $request->worker->name ?? 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Location</p>
            <p class="font-medium text-[#0f172a]">{{ $request->worker->workerProfile?->location ?? 'N/A' }}</p>
          </div>
        </div>
        <div class="space-y-5">
          <div>
            <h3 class="text-sm font-bold text-[#0f172a] mb-1">Reason</h3>
            <p class="text-sm text-[#475569] leading-relaxed">{{ $request->reason ?? 'No reason provided.' }}</p>
          </div>
          <div>
            <h3 class="text-sm font-bold text-[#0f172a] mb-1">Livelihood Justification</h3>
            <p class="text-sm text-[#475569] leading-relaxed">{{ $request->livelihood_justification ?? 'No justification provided.' }}</p>
          </div>
        </div>
      </section>

      <!-- Assessment -->
      @if($request->assessment)
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7" aria-label="Assessment results">
          <div class="flex items-center justify-between mb-5">
            <h2 class="text-base font-extrabold tracking-tight">Assessment Outcome</h2>
            <span class="text-[0.7rem] font-semibold px-2.5 py-1 rounded-full bg-[#f1f5f9] text-[#475569]">{{ $request->assessment->provider_name ?? 'Development Fallback' }}</span>
          </div>

          <div class="grid sm:grid-cols-3 gap-4 mb-6">
            <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#f1f5f9] text-center">
              <p class="text-[0.65rem] uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Suitability</p>
              <p class="text-lg font-extrabold text-[#127A4A]">{{ $request->assessment->suitability_category ?? 'N/A' }}</p>
            </div>
            <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#f1f5f9] text-center">
              <p class="text-[0.65rem] uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Score</p>
              <p class="text-lg font-extrabold text-[#0f172a]">{{ $request->assessment->numerical_score ?? 'N/A' }}</p>
            </div>
            <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#f1f5f9] text-center">
              <p class="text-[0.65rem] uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Method</p>
              <p class="text-xs font-bold text-[#0f172a]">{{ $request->assessment->method_version ?? 'Unknown' }}</p>
            </div>
          </div>

          <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#f1f5f9] text-sm">
            <p class="font-bold text-[#0f172a] mb-3">Rationale</p>
            @if($request->assessment->explanation_json)
              @foreach($request->assessment->explanation_json as $key => $value)
                <div class="mb-2"><span class="font-semibold text-[#0f172a]">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> <span class="text-[#475569]">{{ $value }}</span></div>
              @endforeach
            @else
              <p class="text-[#475569]">No explanation available.</p>
            @endif
          </div>

          <div class="mt-4 flex items-center gap-3 text-xs">
            @if($request->assessment->is_ai_generated)
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200">External AI Result</span>
            @else
              <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold border border-amber-200">Rules-Based Fallback</span>
            @endif
            <span class="text-[#94a3b8]">Assessment provenance clearly labeled. No fabricated accuracy claims.</span>
          </div>
        </section>
      @endif

      <!-- Funding -->
      @if($request->fundingPool)
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7" aria-label="Funding coordination">
          <h2 class="text-base font-extrabold tracking-tight mb-4">Funding Coordination</h2>
          <div class="mb-4">
            <div class="flex justify-between text-sm mb-2">
              <span class="font-medium text-[#475569]">Progress</span>
              <span class="font-extrabold text-[#0f172a]">BDT {{ number_format($request->fundingPool->current_amount_bdt, 0) }} <span class="text-[#94a3b8] font-medium">/</span> {{ number_format($request->fundingPool->target_amount_bdt, 0) }}</span>
            </div>
            <div class="w-full bg-[#f1f5f9] rounded-full h-2.5 overflow-hidden">
              <div class="bg-[#127A4A] h-2.5 rounded-full transition-all" style="width: {{ min(100, ($request->fundingPool->current_amount_bdt / $request->fundingPool->target_amount_bdt) * 100) }}%" aria-valuenow="{{ $request->fundingPool->current_amount_bdt }}" aria-valuemin="0" aria-valuemax="{{ $request->fundingPool->target_amount_bdt }}" aria-label="Funding progress"></div>
            </div>
          </div>
          <div class="flex items-center gap-2">
            @if($request->fundingPool->is_demo)
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#fbeae6] text-[#E05D44] text-xs font-extrabold border border-[#E05D44]/15">Simulated Mode</span>
              <span class="text-xs text-[#94a3b8]">No real money is processed. Pledges are simulated for demonstration.</span>
            @else
              <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-200">Real Funding</span>
            @endif
          </div>

          @if(auth()->user()->isContributor())
            <form action="{{ route('funding.contribute', $request->fundingPool) }}" method="POST" class="mt-5 flex gap-3" aria-label="Make a pledge">
              @csrf
              <label for="pledge-amount" class="sr-only">Pledge amount</label>
              <input id="pledge-amount" type="number" name="amount_bdt" placeholder="Amount (BDT)" class="flex-1 px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required min="100" aria-required="true">
              <button type="submit" class="px-6 py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Pledge</button>
            </form>
          @endif
        </section>
      @endif

      <!-- Procurement -->
      @if($request->procurementOrder)
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7" aria-label="Procurement and delivery">
          <h2 class="text-base font-extrabold tracking-tight mb-4">Procurement &amp; Delivery</h2>
          <div class="grid sm:grid-cols-2 gap-4 text-sm">
            <div>
              <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Order Status</p>
              <p class="font-semibold text-[#0f172a]">{{ ucfirst(str_replace('_', ' ', $request->procurementOrder->status)) }}</p>
            </div>
            @if($request->procurementOrder->delivery)
              <div>
                <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Delivery Status</p>
                <p class="font-semibold text-[#0f172a]">{{ ucfirst($request->procurementOrder->delivery->status) }}</p>
              </div>
            @endif
          </div>
        </section>
      @endif
    </div>

    <!-- Sidebar actions -->
    <aside class="lg:col-span-1">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 sticky top-24" aria-label="Actions">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-5">Actions</h3>
        <div class="space-y-3">
          @if($request->status === 'draft' && auth()->id() === $request->worker_id)
            <form action="{{ route('requests.submit', $request) }}" method="POST" aria-label="Submit request">
              @csrf
              <button type="submit" class="w-full py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Submit for Verification</button>
            </form>
            <a href="{{ route('requests.edit', $request) }}" class="w-full block text-center py-3 rounded-xl border border-[#e2e8f0] text-[#475569] text-sm font-medium hover:bg-[#f8fafc] transition-colors">Edit Request</a>
          @endif

          @if($request->status === 'verified' && (auth()->user()?->isAdmin() || auth()->id() === $request->worker_id))
            <form action="{{ route('assessment.trigger', ['request' => $request]) }}" method="POST" aria-label="Trigger assessment">
              @csrf
              <button type="submit" class="w-full py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Trigger Assessment</button>
            </form>
          @endif

          @if($request->status === 'ai_assessed' && auth()->user()?->isAdmin() && !$request->fundingPool)
            <form action="{{ route('funding.open', $request) }}" method="POST" aria-label="Open funding pool">
              @csrf
              <button type="submit" class="w-full py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Open Funding Pool</button>
            </form>
          @endif

          @if($request->assessment_id)
            <a href="{{ route('assessment.show', ['request' => $request, 'assessment' => $request->assessment_id]) }}" class="w-full block text-center py-3 rounded-xl border border-emerald-300 text-[#127A4A] bg-emerald-50 text-sm font-medium hover:bg-emerald-100 transition-colors">View Assessment Report</a>
          @endif

          @if((auth()->user()?->isAdmin() || auth()->id() === $request->worker_id) && $request->procurementOrder)
            <a href="{{ route('monitoring.index', $request) }}" class="w-full block text-center py-3 rounded-xl border border-[#e2e8f0] text-[#475569] text-sm font-medium hover:bg-[#f8fafc] transition-colors">View Monitoring</a>
          @endif
        </div>
      </div>
    </aside>
  </div>
</x-layout>
