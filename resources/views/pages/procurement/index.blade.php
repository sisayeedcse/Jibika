<x-layout title="Procurement &amp; Delivery">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Procurement &amp; Delivery</h1>
    <p class="text-slate-500">Track asset procurement and delivery status.</p>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 text-sm text-slate-500">
          <th class="pb-4 font-medium">Order</th>
          <th class="pb-4 font-medium">Request</th>
          <th class="pb-4 font-medium">Vendor</th>
          <th class="pb-4 font-medium">Cost (BDT)</th>
          <th class="pb-4 font-medium">Status</th>
          <th class="pb-4 font-medium text-right">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($orders as $order)
          <tr class="hover:bg-slate-50/50 transition">
            <td class="pb-4 text-sm font-medium text-slate-dark">#{{ $order->id }}</td>
            <td class="pb-4 text-sm">{{ $order->request->category?->name }}</td>
            <td class="pb-4 text-sm">{{ $order->vendor?->name ?? 'Unassigned' }}</td>
            <td class="pb-4 text-sm">{{ $order->cost_bdt ? number_format($order->cost_bdt, 0) : '—' }}</td>
            <td class="pb-4">
              <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium 
                {{ $order->status === 'procured' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                {{ ucfirst($order->status) }}
              </span>
            </td>
            <td class="pb-4 text-right">
              @if($order->status === 'pending' && auth()->user()->isVendor())
                <button class="px-3 py-1 rounded-lg bg-[#127A4A]/10 text-[#127A4A] text-xs font-medium hover:bg-[#127A4A]/20 transition">Confirm Delivery</button>
              @else
                <a href="#" class="text-sm text-slate-500">—</a>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-layout>
