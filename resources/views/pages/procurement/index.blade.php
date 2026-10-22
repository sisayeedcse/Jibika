<x-layout title="Procurement &amp; Deliveries — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Procurement &amp; Asset Delivery</h1>
      <p class="text-[#475569]">Monitor asset procurement, supplier handovers, and physical delivery confirmations.</p>
    </div>
    @if(auth()->user()->isVendor())
      <a href="{{ route('vendor.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
        Browse Funded Opportunities
      </a>
    @endif
  </div>

  @if($orders && $orders->count())
    <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden mb-6">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[760px]">
          <thead>
            <tr class="border-b border-[#f1f5f9] text-[0.7rem] uppercase tracking-wider text-[#94a3b8] font-bold bg-[#f8fafc]/50">
              <th class="px-6 py-4">Order ID</th>
              <th class="px-6 py-4">Asset &amp; Worker</th>
              <th class="px-6 py-4">Selected Supplier</th>
              <th class="px-6 py-4">Procurement Cost</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f1f5f9] text-xs">
            @foreach($orders as $order)
              <tr class="hover:bg-[#f8fafc]/60 transition-colors">
                <td class="px-6 py-4 font-mono font-bold text-[#0f172a]">
                  #PO-{{ $order->id }}
                </td>
                <td class="px-6 py-4">
                  <div class="font-bold text-[#0f172a]">{{ $order->request->category?->name ?? 'Productive Asset' }}</div>
                  <div class="text-[#475569] mt-0.5">Worker: <span class="font-semibold">{{ $order->request->worker->name }}</span></div>
                </td>
                <td class="px-6 py-4">
                  <span class="font-semibold text-[#0f172a]">{{ $order->vendor?->name ?? 'Direct Assignment' }}</span>
                </td>
                <td class="px-6 py-4 font-bold text-[#127A4A]">
                  {{ $order->cost_bdt ? 'BDT ' . number_format($order->cost_bdt, 0) : '—' }}
                </td>
                <td class="px-6 py-4">
                  @php
                    $delStatus = $order->delivery?->status ?? 'pending';
                  @endphp
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-wider border
                    {{ $delStatus === 'delivered' ? 'bg-emerald-50 text-[#127A4A] border-emerald-200' :
                      ($delStatus === 'disputed' ? 'bg-red-50 text-[#E05D44] border-red-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $delStatus === 'delivered' ? 'bg-[#127A4A]' : ($delStatus === 'disputed' ? 'bg-[#E05D44]' : 'bg-amber-500') }}"></span>
                    {{ $delStatus }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    @if($order->delivery && $order->delivery->evidence_image_path)
                      <span class="text-[0.65rem] font-mono text-[#94a3b8] px-2 py-1 rounded bg-[#f1f5f9]" title="{{ $order->delivery->evidence_image_path }}">Evidence Logged</span>
                    @endif

                    @if(auth()->user()->isAdmin() || auth()->id() === $order->vendor_id || auth()->id() === $order->request->worker_id)
                      @if(!$order->delivery || $order->delivery->status === 'pending')
                        <form action="{{ route('procurement.confirm', $order) }}" method="POST" class="inline-flex items-center gap-1.5">
                          @csrf
                          <input type="hidden" name="status" value="delivered">
                          <input type="hidden" name="evidence_image_path" value="deliveries/handover_{{ $order->id }}.jpg">
                          <button type="submit" class="px-3 py-1.5 rounded-lg bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">
                            Confirm Delivery
                          </button>
                        </form>
                      @endif

                      @if($order->delivery && $order->delivery->status === 'delivered')
                        <form action="{{ route('procurement.confirm', $order) }}" method="POST" class="inline">
                          @csrf
                          <input type="hidden" name="status" value="disputed">
                          <button type="submit" class="px-3 py-1.5 rounded-lg border border-[#E05D44]/30 text-[#E05D44] text-xs font-medium hover:bg-red-50 transition-colors">
                            Dispute
                          </button>
                        </form>
                      @endif

                      @if($order->delivery && $order->delivery->status === 'disputed' && auth()->user()->isAdmin())
                        <div class="inline-flex items-center gap-1">
                          <form action="{{ route('procurement.resolve-dispute', $order) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="resolution" value="confirm_delivered">
                            <input type="hidden" name="notes" value="Resolved by admin: verified delivered.">
                            <button type="submit" class="px-2.5 py-1 rounded bg-[#127A4A] text-white text-[0.65rem] font-semibold hover:bg-[#0e5a36] transition-colors" title="Resolve as delivered">
                              Resolve Delivered
                            </button>
                          </form>
                          <form action="{{ route('procurement.resolve-dispute', $order) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="resolution" value="mark_failed">
                            <input type="hidden" name="notes" value="Resolved by admin: failed delivery.">
                            <button type="submit" class="px-2.5 py-1 rounded bg-[#E05D44] text-white text-[0.65rem] font-semibold hover:bg-red-700 transition-colors" title="Mark procurement failed">
                              Fail Order
                            </button>
                          </form>
                        </div>
                      @endif
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-4">
      {{ $orders->links() }}
    </div>
  @else
    <div class="bg-white rounded-2xl p-12 md:p-14 text-center shadow-card border border-[#f1f5f9]">
      <div class="w-14 h-14 rounded-2xl bg-[#f1f5f9] flex items-center justify-center mx-auto mb-4 text-[#94a3b8]">
        <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
      </div>
      <h3 class="text-base font-extrabold text-[#0f172a] mb-1">No Procurement Orders Yet</h3>
      <p class="text-xs text-[#475569] max-w-sm mx-auto">When asset funding targets are met and vendor quotations are approved, procurement orders will be created here for tracking and delivery confirmation.</p>
    </div>
  @endif
</x-layout>
