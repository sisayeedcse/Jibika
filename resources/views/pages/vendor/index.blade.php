<x-layout title="Vendor Workspace">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Vendor Workspace</h1>
    <p class="text-[#475569]">Browse funded opportunities, submit quotations, and track procurement status.</p>
  </div>

  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-8">
      <section aria-label="Available opportunities" class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
        <h2 class="text-lg font-extrabold tracking-tight mb-5">Available Opportunities</h2>
        @if($opportunities && $opportunities->count())
          <div class="space-y-5">
            @foreach($opportunities as $opp)
              <article class="p-5 rounded-xl bg-[#f8fafc] border border-[#f1f5f9]">
                <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                  <div>
                    <h3 class="font-bold text-[#0f172a]">#{{ $opp->id }} — {{ $opp->category?->name ?? 'Asset Request' }}</h3>
                    <p class="text-sm text-[#475569]">Requested Amount: <span class="font-extrabold text-[#127A4A]">BDT {{ number_format($opp->requested_amount_bdt, 0) }}</span></p>
                  </div>
                </div>
                <p class="text-sm text-[#475569] mb-4 leading-relaxed">{{ Str::limit($opp->reason ?? 'No reason provided.', 160) }}</p>
                <form action="{{ route('vendor.quote', $opp) }}" method="POST" class="space-y-3" aria-label="Quotation form for opportunity {{ $opp->id }}">
                  @csrf
                  <div class="grid md:grid-cols-2 gap-3">
                    <label for="quoted_amount_bdt_{{ $opp->id }}" class="sr-only">Quoted amount</label>
                    <input id="quoted_amount_bdt_{{ $opp->id }}" type="number" name="quoted_amount_bdt" placeholder="Quoted amount (BDT)" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-white" required min="1000" aria-required="true">
                    <label for="notes_{{ $opp->id }}" class="sr-only">Notes</label>
                    <textarea id="notes_{{ $opp->id }}" name="notes" rows="1" placeholder="Notes (optional)" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-white resize-none"></textarea>
                  </div>
                  <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Submit Quotation</button>
                </form>
              </article>
            @endforeach
          </div>
        @else
          <p class="text-sm text-[#475569]">No funded opportunities available for quotation at this time.</p>
        @endif
      </section>

      <section aria-label="My quotations" class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
        <h2 class="text-lg font-extrabold tracking-tight mb-5">My Quotations</h2>
        @if($quotations && $quotations->count())
          <div class="divide-y divide-[#f1f5f9]">
            @foreach($quotations as $q)
              <div class="py-4 first:pt-0 last:pb-0 flex flex-wrap items-center justify-between gap-3">
                <div>
                  <div class="font-bold text-[#0f172a]">#{{ $q->procurement_order->request->id ?? 'N/A' }}</div>
                  <div class="text-sm text-[#475569]">{{ $q->procurement_order->request->category?->name ?? 'N/A' }}</div>
                </div>
                <div class="text-right">
                  <div class="font-extrabold text-[#0f172a]">BDT {{ number_format($q->quoted_amount_bdt, 0) }}</div>
                  <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[0.7rem] font-extrabold
                    {{ $q->status === 'selected' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                    {{ ucfirst($q->status) }}
                  </span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-sm text-[#475569]">No quotations submitted yet.</p>
        @endif
      </section>
    </div>

    <aside class="lg:col-span-1">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 sticky top-24">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-3">Role</h3>
        <p class="text-sm font-bold text-[#0f172a] mb-2">Vendor</p>
        <p class="text-sm text-[#475569] mb-5 leading-relaxed">Submit quotations for funded asset requests. When selected, you'll manage procurement and delivery tracking.</p>
        <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#f1f5f9]">
          <p class="text-xs text-[#94a3b8]">All quotations are tracked transparently. No fabricated vendor statistics or guarantees are shown.</p>
        </div>
      </div>
    </aside>
  </div>
</x-layout>
