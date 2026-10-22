<x-layout title="All Requests">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">All Requests</h1>
    <p class="text-[#475569]">Moderate all asset requests across the platform.</p>
  </div>

  <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[720px]">
        <thead>
          <tr class="border-b border-[#f1f5f9] text-xs uppercase tracking-wider text-[#94a3b8] font-medium">
            <th class="px-6 py-3.5">ID</th>
            <th class="px-6 py-3.5">Worker</th>
            <th class="px-6 py-3.5">Asset</th>
            <th class="px-6 py-3.5">Amount (BDT)</th>
            <th class="px-6 py-3.5">Status</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#f1f5f9]">
          @foreach($requests as $req)
            <tr class="hover:bg-[#f8fafc]/60 transition-colors">
              <td class="px-6 py-4 text-sm font-semibold text-[#0f172a]">#{{ $req->id }}</td>
              <td class="px-6 py-4 text-sm text-[#475569]">{{ $req->worker->name ?? 'Unknown' }}</td>
              <td class="px-6 py-4 text-sm text-[#475569]">{{ $req->category?->name ?? 'N/A' }}</td>
              <td class="px-6 py-4 text-sm font-medium text-[#0f172a]">{{ number_format($req->requested_amount_bdt, 0) }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.7rem] font-bold border
                  {{ $req->status === 'draft' ? 'bg-amber-50 text-amber-700 border-amber-200' : (($req->status === 'verified' || $req->status === 'ai_assessed') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-[#475569] border-slate-200') }}">
                  {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-[#f1f5f9] bg-[#f8fafc]/40">
      {{ $requests->links() }}
    </div>
  </div>
</x-layout>
