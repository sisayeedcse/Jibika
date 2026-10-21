<x-layout title="Verification Queue">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Community Verification</h1>
    <p class="text-slate-500">Review and verify worker asset requests.</p>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 text-sm text-slate-500">
          <th class="pb-4 font-medium">Request</th>
          <th class="pb-4 font-medium">Worker</th>
          <th class="pb-4 font-medium">Amount (BDT)</th>
          <th class="pb-4 font-medium">Status</th>
          <th class="pb-4 font-medium text-right">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($verifications as $v)
          <tr class="hover:bg-slate-50/50 transition">
            <td class="pb-4 text-sm font-medium text-slate-dark">#{{ $v->request->id }}</td>
            <td class="pb-4 text-sm">{{ $v->request->worker->name }}</td>
            <td class="pb-4 text-sm">{{ number_format($v->request->requested_amount_bdt, 0) }}</td>
            <td class="pb-4">
              <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium 
                {{ $v->status === 'verified' ? 'bg-emerald-50 text-emerald-700' : ($v->status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                {{ ucfirst($v->status) }}
              </span>
            </td>
            <td class="pb-4 text-right">
              <a href="{{ route('verification.review', $v) }}" class="text-sm text-[#127A4A] hover:underline font-medium">Review</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if($verifications->isEmpty())
    <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-slate-100">
      <div class="text-slate-400 text-4xl mb-4">✓</div>
      <p class="text-slate-500 text-sm">No verification requests assigned.</p>
    </div>
  @endif
  {{ $verifications->links() }}
</x-layout>
