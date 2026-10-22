<x-layout title="All Asset Requests — Admin — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Platform Asset Requests Oversight</h1>
      <p class="text-sm text-[#475569]">Monitor and moderate all worker asset requests across every stage of the livelihood pipeline.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-[#127A4A] hover:underline">&larr; Back to Admin Panel</a>
  </div>

  <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden mb-6">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[760px]">
        <thead>
          <tr class="border-b border-[#f1f5f9] text-[0.7rem] uppercase tracking-wider text-[#94a3b8] font-bold bg-[#f8fafc]/50">
            <th class="px-6 py-4">ID</th>
            <th class="px-6 py-4">Worker &amp; Location</th>
            <th class="px-6 py-4">Asset Category</th>
            <th class="px-6 py-4">Requested (BDT)</th>
            <th class="px-6 py-4">Current Status</th>
            <th class="px-6 py-4">Updated</th>
            <th class="px-6 py-4 text-right">Details</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#f1f5f9] text-xs">
          @foreach($requests as $req)
            <tr class="hover:bg-[#f8fafc]/60 transition-colors">
              <td class="px-6 py-4 font-mono font-bold text-[#0f172a]">#{{ $req->id }}</td>
              <td class="px-6 py-4">
                <div class="font-bold text-[#0f172a]">{{ $req->worker->name ?? 'Unknown' }}</div>
                <div class="text-[#94a3b8]">{{ $req->worker->email ?? '' }}</div>
              </td>
              <td class="px-6 py-4 text-[#475569]">{{ $req->category?->name ?? 'Uncategorized' }}</td>
              <td class="px-6 py-4 font-bold text-[#127A4A]">{{ number_format($req->requested_amount_bdt, 0) }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.65rem] font-bold uppercase tracking-wider border
                  {{ in_array($req->status, ['verified', 'ai_assessed', 'funding_open', 'funded', 'delivered']) ? 'bg-emerald-50 text-[#127A4A] border-emerald-200' :
                    ($req->status === 'draft' ? 'bg-amber-50 text-amber-700 border-amber-200' : 'bg-slate-50 text-[#475569] border-slate-200') }}">
                  {{ str_replace('_', ' ', $req->status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-[#94a3b8]">{{ $req->updated_at?->diffForHumans() }}</td>
              <td class="px-6 py-4 text-right">
                <a href="{{ route('requests.show', $req) }}" class="inline-flex items-center px-3 py-1 rounded-lg bg-[#127A4A]/10 text-[#127A4A] font-semibold hover:bg-[#127A4A] hover:text-white transition-colors">
                  Inspect &rarr;
                </a>
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
