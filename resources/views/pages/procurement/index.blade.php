<x-layout title="Procurement &amp; Delivery">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Procurement &amp; Delivery</h1>
    <p class="text-[#475569]">Track asset orders, delivery confirmations, and lifecycle stages.</p>
  </div>

  @if($orders && $orders->count())
    <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[720px]">
          <thead>
            <tr class="border-b border-[#f1f5f9] text-xs uppercase tracking-wider text-[#94a3b8] font-medium">
              <th class="px-6 py-3.5">Order</th>
              <th class="px-6 py-3.5">Request</th>
              <th class="px-6 py-3.5">Vendor</th>
              <th class="px-6 py-3.5">Cost (BDT)</th>
              <th class="px-6 py-3.5">Status</th>
              <th class="px-6 py-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f1f5f9]">
            @foreach($orders as $order)
              <tr class="hover:bg-[#f8fafc]/60 transition-colors">
                <td class="px-6 py-4 text-sm font-semibold text-[#0f172a]">#{{ $order->id }}</td>
                <td class="px-6 py-4 text-sm text-[#475569]">{{ $order->request->category?->name ?? 'N/A' }}</td>
                <td class="px-6 py-4 text-sm text-[#475569]">{{ $order->vendor?->name ?? 'Unassigned' }}</td>
                <td class="px-6 py-4 text-sm font-medium text-[#0f172a]">{{ $order->cost_bdt ? number_format($order->cost_bdt, 0) : '—' }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.7rem] font-bold border
                    {{ $order->status === 'procured' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200' }}">
                    {{ ucfirst($order->status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  @if($order->status === 'pending' && auth()->user()?->isVendor())
                    <form action="{{ route('procurement.confirm', $order) }}" method="POST" aria-label="Confirm delivery for order {{ $order->id }}">
                      @csrf
                      <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">Confirm Delivery</button>
                    </form>
                  @else
                    <span class="text-xs text-[#94a3b8]">—</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  @else
    <div class="bg-white rounded-2xl p-12 md:p-14 text-center shadow-card border border-[#f1f5f9]">
      <h3 class="text-base font-extrabold text-[#0f172a] mb-2">No procurement orders</h3>
      <p class="text-sm text-[#475569]">Orders will be listed here when vendors submit quotations and orders are created.</p>
    </div>
  @endif
</x-layout>
