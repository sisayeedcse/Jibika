<x-layout title="Verification Queue — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Community Verification Queue</h1>
      <p class="text-sm text-[#475569]">Review assigned productive asset requests, verify worker claims, and submit justified determinations.</p>
    </div>
  </div>

  @if($verifications && $verifications->count())
    <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden mb-6">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[720px]">
          <thead>
            <tr class="border-b border-[#f1f5f9] text-[0.7rem] uppercase tracking-wider text-[#94a3b8] font-bold bg-[#f8fafc]/50">
              <th class="px-6 py-4">Request</th>
              <th class="px-6 py-4">Worker &amp; Location</th>
              <th class="px-6 py-4">Asset Category</th>
              <th class="px-6 py-4">Requested (BDT)</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f1f5f9] text-xs">
            @foreach($verifications as $v)
              <tr class="hover:bg-[#f8fafc]/60 transition-colors">
                <td class="px-6 py-4 font-mono font-bold text-[#0f172a]">
                  #{{ $v->request->id }}
                </td>
                <td class="px-6 py-4">
                  <div class="font-bold text-[#0f172a]">{{ $v->request->worker->name ?? 'Unknown' }}</div>
                  <div class="text-[#94a3b8] mt-0.5">{{ $v->request->worker->workerProfile?->location ?: 'Location unlisted' }}</div>
                </td>
                <td class="px-6 py-4 text-[#475569]">
                  {{ $v->request->category?->name ?? 'Productive Asset' }}
                </td>
                <td class="px-6 py-4 font-bold text-[#127A4A]">
                  BDT {{ number_format($v->request->requested_amount_bdt, 0) }}
                </td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-wider border
                    {{ $v->status === 'verified' ? 'bg-emerald-50 text-[#127A4A] border-emerald-200' :
                      ($v->status === 'rejected' ? 'bg-red-50 text-[#E05D44] border-red-200' :
                      ($v->status === 'flagged' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-100 text-slate-700 border-slate-200')) }}">
                    {{ ucfirst(str_replace('_', ' ', $v->status)) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-right">
                  <a href="{{ route('verification.review', $v) }}" class="inline-flex items-center px-4 py-1.5 rounded-xl bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">
                    Review Evidence &rarr;
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-4">
      {{ $verifications->links() }}
    </div>
  @else
    <div class="bg-white rounded-2xl p-12 md:p-14 text-center shadow-card border border-[#f1f5f9]">
      <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4 text-[#127A4A] text-2xl" aria-hidden="true">
        ✓
      </div>
      <h3 class="text-base font-extrabold text-[#0f172a] mb-1">Queue is Clear</h3>
      <p class="text-xs text-[#475569] max-w-sm mx-auto">No pending verification assignments in your queue. New submissions from workers in your area will appear here automatically.</p>
    </div>
  @endif
</x-layout>
