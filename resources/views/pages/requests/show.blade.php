<x-layout title="Request #{{ $request->id }}">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Request #{{ $request->id }}</h1>
    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold 
      {{ $request->status === 'draft' ? 'bg-amber-50 text-amber-700 border border-amber-200' : ($request->status === 'verified' || $request->status === 'ai_assessed' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-50 text-slate-700 border border-slate-200') }}">
      {{ ucfirst(str_replace('_', ' ', $request->status)) }}
    </span>
  </div>

  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-6">
        <div class="grid md:grid-cols-2 gap-6 mb-6">
          <div>
            <p class="text-sm text-slate-500">Asset Category</p>
            <p class="font-medium">{{ $request->category?->name ?? 'N/A' }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Requested Amount</p>
            <p class="font-medium text-[#127A4A]">BDT {{ number_format($request->requested_amount_bdt, 0) }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Worker</p>
            <p class="font-medium">{{ $request->worker->name }}</p>
          </div>
          <div>
            <p class="text-sm text-slate-500">Location</p>
            <p class="font-medium">{{ $request->worker->workerProfile?->location ?? 'N/A' }}</p>
          </div>
        </div>
        <div class="space-y-4">
          <div>
            <p class="text-sm font-medium text-slate-700 mb-2">Reason</p>
            <p class="text-slate-600">{{ $request->reason }}</p>
          </div>
          <div>
            <p class="text-sm font-medium text-slate-700 mb-2">Livelihood Justification</p>
            <p class="text-slate-600">{{ $request->livelihood_justification }}</p>
          </div>
        </div>
      </div>

      @if($request->assessment)
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-6">
          <h2 class="text-xl font-bold mb-4">AI-Assisted Assessment</h2>
          <div class="grid md:grid-cols-3 gap-4 mb-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
              <div class="text-xs text-slate-500 uppercase tracking-wider">Suitability</div>
              <div class="text-xl font-bold text-[#127A4A]">{{ $request->assessment->suitability_category }}</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
              <div class="text-xs text-slate-500 uppercase tracking-wider">Score</div>
              <div class="text-xl font-bold">{{ $request->assessment->numerical_score ?? 'N/A' }}</div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
              <div class="text-xs text-slate-500 uppercase tracking-wider">Provider</div>
              <div class="text-xs font-medium">{{ $request->assessment->provider_name ?? 'Development Fallback' }}</div>
            </div>
          </div>
          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-sm">
            <p class="font-medium text-slate-700 mb-2">Explanation</p>
            @foreach($request->assessment->explanation_json as $key => $value)
              <div class="mb-2"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</div>
            @endforeach
            <p class="mt-4 text-xs text-slate-500">
              <strong>Method:</strong> {{ $request->assessment->method_version }} •
              @if(!$request->assessment->is_ai_generated)
                <span class="text-[#E05D44]">NOT AN LLM RESPONSE — Development Fallback</span>
              @else
                Real AI Provider Response
              @endif
            </p>
          </div>
        </div>
      @endif

      @if($request->fundingPool)
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-6">
          <h2 class="text-xl font-bold mb-4">Funding Coordination</h2>
          <div class="mb-4">
            <div class="flex justify-between text-sm mb-1">
              <span class="text-slate-500">Progress</span>
              <span class="font-medium">{{ number_format($request->fundingPool->current_amount_bdt, 0) }} / {{ number_format($request->fundingPool->target_amount_bdt, 0) }} BDT</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
              <div class="bg-brand-green h-2 rounded-full" style="width: {{ min(100, ($request->fundingPool->current_amount_bdt / $request->fundingPool->target_amount_bdt) * 100) }}%"></div>
            </div>
            <p class="text-xs text-slate-500 mt-2">
              @if($request->fundingPool->is_demo)
                <span class="text-[#E05D44] font-medium">● Simulated Mode</span> — No real money processed.
              @else
                Real funding mode.
              @endif
            </p>
          </div>
          @if(auth()->user()->isContributor())
            <form action="{{ route('funding.contribute', $request->fundingPool) }}" method="POST" class="mt-4">
              @csrf
              <div class="flex gap-2">
                <input type="number" name="amount_bdt" placeholder="Amount (BDT)" class="flex-1 px-4 py-3 rounded-xl border border-slate-200 focus-ring" required min="100">
                <button type="submit" class="px-6 py-3 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] transition">Pledge</button>
              </div>
            </form>
          @endif
        </div>
      @endif

      @if($request->procurementOrder)
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-6">
          <h2 class="text-xl font-bold mb-4">Procurement &amp; Delivery</h2>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-sm text-slate-600">Status</span>
              <span class="text-sm font-medium">{{ ucfirst(str_replace('_', ' ', $request->procurementOrder->status)) }}</span>
            </div>
            @if($request->procurementOrder->delivery)
              <div class="flex items-center justify-between">
                <span class="text-sm text-slate-600">Delivery</span>
                <span class="text-sm font-medium">{{ ucfirst($request->procurementOrder->delivery->status) }}</span>
              </div>
            @endif
          </div>
        </div>
      @endif
    </div>

    <div class="lg:col-span-1">
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 sticky top-24">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Actions</h3>
        @if($request->status === 'draft' && auth()->id() === $request->worker_id)
          <form action="{{ route('requests.submit', $request) }}" method="POST" class="mb-4">
            @csrf
            <button type="submit" class="w-full py-3.5 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] shadow-md transition">Submit for Verification</button>
          </form>
          <a href="{{ route('requests.edit', $request) }}" class="w-full py-3.5 rounded-xl border border-slate-200 text-slate-700 font-medium text-center hover:bg-slate-50 transition block">Edit Request</a>
        @endif
        @if($request->status === 'verified' && auth()->user()->isAdmin())
          <form action="{{ route('assessment.trigger', ['request' => $request]) }}" method="POST" class="mb-4">
            @csrf
            <button type="submit" class="w-full py-3.5 rounded-xl bg-[#127A4A] text-white font-semibold hover:bg-[#0e5a36] shadow-md transition">Trigger AI Assessment</button>
          </form>
        @endif
        @if(auth()->user()->isAdmin() || auth()->id() === $request->worker_id)
          <a href="{{ route('monitoring.index', $request) }}" class="w-full py-3.5 rounded-xl border border-slate-200 text-slate-700 font-medium text-center hover:bg-slate-50 transition block">View Monitoring</a>
        @endif
      </div>
    </div>
  </div>
</x-layout>
