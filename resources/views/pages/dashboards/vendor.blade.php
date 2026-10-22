<x-layout title="Vendor Workspace — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <h1 class="text-[1.75rem] md:text-[2.2rem] font-extrabold tracking-tight text-[#0f172a]">Vendor &amp; Supplier Desk</h1>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
          Vendor
        </span>
      </div>
      <p class="text-sm text-[#475569]">Welcome, <span class="font-semibold text-[#0f172a]">{{ $user->name }}</span>. Browse funded productive asset opportunities, submit competitive quotations, and fulfill procurement orders.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('vendor.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Quotation Portal
      </a>
    </div>
  </div>

  <!-- Key Metrics Row -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Open for Quotation</p>
      <div class="text-3xl font-black text-blue-600">{{ $stats['open_quotes'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Funded requests awaiting vendor bids</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">My Submitted Quotes</p>
      <div class="text-3xl font-black text-[#0f172a]">{{ $stats['my_quotes'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Active &amp; reviewed bids</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Procurement Orders Awarded</p>
      <div class="text-3xl font-black text-[#127A4A]">{{ $stats['orders_won'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Selected for delivery &amp; fulfillment</p>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="grid lg:grid-cols-3 gap-8">
    <!-- Opportunities for Quotation -->
    <div class="lg:col-span-2 space-y-6">
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
        <div class="p-6 md:p-7 border-b border-[#f1f5f9] flex items-center justify-between">
          <div>
            <h2 class="text-base font-extrabold tracking-tight text-[#0f172a]">Opportunities Open for Quotation</h2>
            <p class="text-xs text-[#475569] mt-0.5">Fully funded worker asset requests ready for vendor procurement</p>
          </div>
          <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
            {{ $opportunities->count() }} Available
          </span>
        </div>

        @if($opportunities->count())
          <div class="divide-y divide-[#f1f5f9]">
            @foreach($opportunities as $opp)
              <div class="p-6 hover:bg-[#f8fafc] transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-mono font-bold text-[#94a3b8]">REQ #{{ $opp->id }}</span>
                    <h3 class="text-sm font-bold text-[#0f172a]">{{ $opp->category?->name ?? 'Equipment / Asset' }}</h3>
                  </div>
                  <p class="text-xs text-[#475569] mb-2 line-clamp-1">
                    Worker: <span class="font-semibold text-[#0f172a]">{{ $opp->worker->name }}</span> &bull; {{ $opp->reason }}
                  </p>
                  <p class="text-xs font-bold text-[#127A4A]">Budget: BDT {{ number_format($opp->requested_amount_bdt, 0) }}</p>
                </div>

                <div class="shrink-0">
                  <a href="{{ route('vendor.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">
                    Submit Quotation &rarr;
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="p-12 text-center">
            <h3 class="text-sm font-bold text-[#0f172a] mb-1">No Open Opportunities</h3>
            <p class="text-xs text-[#475569] max-w-sm mx-auto">There are currently no funded requests without pending procurement orders. Check back soon.</p>
          </div>
        @endif
      </section>

      <!-- Submitted Quotes -->
      @if($myQuotes->count())
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6 md:p-7">
          <h2 class="text-base font-extrabold tracking-tight text-[#0f172a] mb-4">My Submitted Quotations</h2>
          <div class="divide-y divide-[#f1f5f9]">
            @foreach($myQuotes as $q)
              <div class="py-3.5 flex items-center justify-between text-xs">
                <div>
                  <span class="font-bold text-[#0f172a]">Quotation #{{ $q->id }} &bull; BDT {{ number_format($q->quoted_amount_bdt, 0) }}</span>
                  <p class="text-[#94a3b8] mt-0.5">{{ $q->notes ? Str::limit($q->notes, 60) : 'No extra notes' }}</p>
                </div>
                <div>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider
                    {{ $q->status === 'selected' ? 'bg-emerald-50 text-[#127A4A] border border-emerald-200' :
                      ($q->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-red-50 text-[#E05D44] border border-red-200') }}">
                    {{ $q->status }}
                  </span>
                </div>
              </div>
            @endforeach
          </div>
        </section>
      @endif
    </div>

    <!-- Vendor Fulfillment Rules -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Procurement &amp; Delivery Rules</h3>
        <ul class="space-y-3 text-xs text-[#475569]">
          <li class="flex items-start gap-2.5">
            <span class="w-5 h-5 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-[0.65rem] shrink-0 mt-0.5">1</span>
            <span><strong>Transparent Bidding:</strong> Submit accurate prices inclusive of local delivery and initial equipment setup.</span>
          </li>
          <li class="flex items-start gap-2.5">
            <span class="w-5 h-5 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-[0.65rem] shrink-0 mt-0.5">2</span>
            <span><strong>Physical Handover:</strong> Machinery must be handed over in operational condition directly to the verified worker.</span>
          </li>
          <li class="flex items-start gap-2.5">
            <span class="w-5 h-5 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-[0.65rem] shrink-0 mt-0.5">3</span>
            <span><strong>Delivery Proof:</strong> Upload delivery receipt or photo proof to trigger delivery confirmation in JIBIKA.</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</x-layout>
