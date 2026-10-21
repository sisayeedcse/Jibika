<x-layout title="Asset Requests">
  <div class="flex justify-between items-end mb-6">
    <div>
      <h1 class="text-3xl font-extrabold text-slate-dark mb-2">My Requests</h1>
      <p class="text-slate-500">Track your productive asset requests.</p>
    </div>
    <a href="{{ route('requests.create') }}" class="px-6 py-3 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] shadow-md transition">
      New Request
    </a>
  </div>

  @if($requests->count())
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="border-b border-slate-200 text-sm text-slate-500">
            <th class="pb-4 font-medium">Reference</th>
            <th class="pb-4 font-medium">Asset</th>
            <th class="pb-4 font-medium">Amount (BDT)</th>
            <th class="pb-4 font-medium">Status</th>
            <th class="pb-4 font-medium">Updated</th>
            <th class="pb-4 font-medium text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          @foreach($requests as $req)
            <tr class="hover:bg-slate-50/50 transition">
              <td class="pb-4 text-sm font-medium text-slate-dark">#{{ $req->id }}</td>
              <td class="pb-4 text-sm">{{ $req->category?->name ?? 'Uncategorized' }}</td>
              <td class="pb-4 text-sm">{{ number_format($req->requested_amount_bdt, 0) }}</td>
              <td class="pb-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium 
                  {{ $req->status === 'draft' ? 'bg-amber-50 text-amber-700' : ($req->status === 'verified' || $req->status === 'ai_assessed' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-50 text-slate-700') }}">
                  {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                </span>
              </td>
              <td class="pb-4 text-sm text-slate-500">{{ $req->updated_at->diffForHumans() }}</td>
              <td class="pb-4 text-right">
                <a href="{{ route('requests.show', $req) }}" class="text-sm text-[#127A4A] hover:underline font-medium">View</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    {{ $requests->links() }}
  @else
    <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-slate-100">
      <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4 text-slate-400">📄</div>
      <h3 class="text-lg font-bold text-slate-dark mb-2">No requests yet</h3>
      <p class="text-slate-500 text-sm max-w-xs mx-auto">Create your first productive asset request to get started.</p>
      <a href="{{ route('requests.create') }}" class="mt-6 inline-block px-6 py-3 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] transition">Create Request</a>
    </div>
  @endif
</x-layout>
