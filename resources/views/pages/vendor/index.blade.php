<x-layout title="Vendor Dashboard">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Vendor Dashboard</h1>
    <p class="text-slate-500">Submit quotations and manage procurement.</p>
  </div>

  <div class="grid lg:grid-cols-3 gap-8 mb-8">
    <div class="lg:col-span-2">
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-6">
        <h2 class="text-xl font-bold mb-4">Available Opportunities</h2>
        @if($opportunities->count())
          <div class="space-y-3">
            @foreach($opportunities as $opp)
              <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                <div class="flex justify-between items-start mb-2">
                  <h4 class="font-bold text-slate-dark">#{{ $opp->id }} — {{ $opp->category?->name }}</h4>
                  <span class="text-xs font-bold text-[#127A4A]">BDT {{ number_format($opp->requested_amount_bdt, 0) }}</span>
                </div>
                <p class="text-sm text-slate-600 mb-3">{{ Str::limit($opp->reason, 100) }}</p>
                <form action="{{ route('vendor.quote', $opp) }}" method="POST" class="space-y-3">
                  @csrf
                  <input type="number" name="quoted_amount_bdt" placeholder="Your quoted amount (BDT)" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required min="1000">
                  <textarea name="notes" rows="2" placeholder="Notes about your quotation" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring"></textarea>
                  <button type="submit" class="w-full py-3 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] transition">Submit Quotation</button>
                </form>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-slate-500 text-sm">No funded opportunities available for quotation.</p>
        @endif
      </div>

      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <h2 class="text-xl font-bold mb-4">My Quotations</h2>
        @if($quotations->count())
          <div class="space-y-3">
            @foreach($quotations as $q)
              <div class="p-4 rounded-xl border border-slate-200 flex justify-between items-center">
                <div>
                  <div class="font-bold text-slate-dark">#{{ $q->procurement_order->request->id }}</div>
                  <div class="text-sm text-slate-500">{{ $q->procurement_order->request->category?->name }}</div>
                </div>
                <div class="text-right">
                  <div class="font-bold">BDT {{ number_format($q->quoted_amount_bdt, 0) }}</div>
                  <span class="text-xs font-medium 
                    {{ $q->status === 'selected' ? 'text-emerald-600 bg-emerald-50' : 'text-amber-600 bg-amber-50' }}"
                    class="px-2 py-1 rounded-full">{{ ucfirst($q->status) }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-slate-500 text-sm">No quotations submitted yet.</p>
        @endif
      </div>
    </div>

    <div class="lg:col-span-1">
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 sticky top-24">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Role: Vendor</h3>
        <p class="text-sm text-slate-500 mb-6">Submit quotations for funded asset requests. When selected, you'll handle procurement and delivery.</p>
      </div>
    </div>
  </div>
</x-layout>
