<x-layout title="Verification Queue">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Community Verification</h1>
    <p class="text-[#475569]">Review assigned asset requests, confirm evidence, and record decisions with required notes.</p>
  </div>

  @if($verifications && $verifications->count())
    <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[640px]">
          <thead>
            <tr class="border-b border-[#f1f5f9] text-xs uppercase tracking-wider text-[#94a3b8] font-medium">
              <th class="px-6 py-3.5">Request</th>
              <th class="px-6 py-3.5">Worker</th>
              <th class="px-6 py-3.5">Amount (BDT)</th>
              <th class="px-6 py-3.5">Status</th>
              <th class="px-6 py-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f1f5f9]">
            @foreach($verifications as $v)
              <tr class="hover:bg-[#f8fafc]/60 transition-colors">
                <td class="px-6 py-4 text-sm font-semibold text-[#0f172a]">#{{ $v->request->id }}</td>
                <td class="px-6 py-4 text-sm text-[#475569]">{{ $v->request->worker->name ?? 'Unknown' }}</td>
                <td class="px-6 py-4 text-sm font-medium text-[#0f172a]">{{ number_format($v->request->requested_amount_bdt, 0) }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.7rem] font-bold border
                    {{ $v->status === 'verified' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($v->status === 'rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200') }}">
                    {{ ucfirst($v->status) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <a href="{{ route('verification.review', $v) }}" class="text-sm text-[#127A4A] hover:text-[#0e5a36] font-medium">Review</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="px-6 py-4 border-t border-[#f1f5f9]">
        {{ $verifications->links() }}
      </div>
    </div>
  @else
    <div class="bg-white rounded-2xl p-12 md:p-14 text-center shadow-card border border-[#f1f5f9]">
      <div class="w-14 h-14 rounded-2xl bg-[#f1f5f9] flex items-center justify-center mx-auto mb-5 text-2xl text-[#94a3b8]" aria-hidden="true">✓</div>
      <h3 class="text-base font-extrabold text-[#0f172a] mb-2">No verification requests assigned</h3>
      <p class="text-sm text-[#475569] max-w-sm mx-auto">When assignments become available, they will appear here for review and decision.</p>
    </div>
  @endif
</x-layout>
