<x-layout title="My Requests">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
    <div>
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-1">My Requests</h1>
      <p class="text-[#475569]">Track your productive asset requests through the lifecycle.</p>
    </div>
    <a href="{{ route('requests.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">New Request</a>
  </div>

  @if($requests && $requests->count())
    <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[640px]">
          <thead>
            <tr class="border-b border-[#f1f5f9] text-xs uppercase tracking-wider text-[#94a3b8] font-medium">
              <th class="px-6 py-3.5">Reference</th>
              <th class="px-6 py-3.5">Asset</th>
              <th class="px-6 py-3.5">Amount (BDT)</th>
              <th class="px-6 py-3.5">Status</th>
              <th class="px-6 py-3.5">Updated</th>
              <th class="px-6 py-3.5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#f1f5f9]">
            @foreach($requests as $req)
              <tr class="hover:bg-[#f8fafc]/60 transition-colors">
                <td class="px-6 py-4 text-sm font-semibold text-[#0f172a]">#{{ $req->id }}</td>
                <td class="px-6 py-4 text-sm text-[#475569]">{{ $req->category?->name ?? 'Uncategorized' }}</td>
                <td class="px-6 py-4 text-sm font-medium text-[#0f172a]">{{ number_format($req->requested_amount_bdt, 0) }}</td>
                <td class="px-6 py-4">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[0.7rem] font-semibold
                    {{ $req->status === 'draft' ? 'bg-amber-50 text-amber-700' : (($req->status === 'verified' || $req->status === 'ai_assessed') ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-50 text-[#475569]') }}">
                    {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                  </span>
                </td>
                <td class="px-6 py-4 text-xs text-[#94a3b8]">{{ $req->updated_at?->diffForHumans() }}</td>
                <td class="px-6 py-4 text-right">
                  <a href="{{ route('requests.show', $req) }}" class="text-sm text-[#127A4A] hover:text-[#0e5a36] font-medium">View</a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-6">
      {{ $requests->links() }}
    </div>
  @else
    <div class="bg-white rounded-2xl p-12 md:p-14 text-center shadow-card border border-[#f1f5f9]">
      <div class="w-16 h-16 rounded-2xl bg-[#f1f5f9] flex items-center justify-center mx-auto mb-5 text-[#94a3b8] text-2xl" aria-hidden="true">📄</div>
      <h3 class="text-lg font-extrabold text-[#0f172a] mb-2">No requests yet</h3>
      <p class="text-sm text-[#475569] max-w-sm mx-auto mb-6">Create your first productive asset request to begin the lifecycle from request to assessment.</p>
      <a href="{{ route('requests.create') }}" class="inline-flex items-center px-6 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Create Request</a>
    </div>
  @endif
</x-layout>
