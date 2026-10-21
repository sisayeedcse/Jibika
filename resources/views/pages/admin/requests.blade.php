<x-layout title="All Requests">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">All Requests</h1>
    <p class="text-slate-500">Moderate all asset requests across the platform.</p>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 text-sm text-slate-500">
          <th class="pb-4 font-medium">ID</th>
          <th class="pb-4 font-medium">Worker</th>
          <th class="pb-4 font-medium">Asset</th>
          <th class="pb-4 font-medium">Amount (BDT)</th>
          <th class="pb-4 font-medium">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($requests as $req)
          <tr class="hover:bg-slate-50/50 transition">
            <td class="pb-4 text-sm font-medium text-slate-dark">#{{ $req->id }}</td>
            <td class="pb-4 text-sm">{{ $req->worker->name }}</td>
            <td class="pb-4 text-sm">{{ $req->category?->name ?? 'N/A' }}</td>
            <td class="pb-4 text-sm">{{ number_format($req->requested_amount_bdt, 0) }}</td>
            <td class="pb-4">
              <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium 
                {{ $req->status === 'draft' ? 'bg-amber-50 text-amber-700' : ($req->status === 'verified' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-50 text-slate-700') }}">
                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  {{ $requests->links() }}
</x-layout>
