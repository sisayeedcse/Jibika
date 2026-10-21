<x-layout title="Review Verification">
  <div class="max-w-2xl">
    <div class="mb-6">
      <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Review Request #{{ $verification->request->id }}</h1>
      <p class="text-slate-500">Evaluate the worker's evidence and make a decision.</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mb-6">
      <div class="space-y-4">
        <div>
          <p class="text-sm font-medium text-slate-700">Worker</p>
          <p class="text-lg font-bold">{{ $verification->request->worker->name }}</p>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-700">Asset</p>
          <p class="text-lg font-bold">{{ $verification->request->category?->name ?? 'N/A' }}</p>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-700">Requested Amount</p>
          <p class="text-lg font-bold text-[#127A4A]">BDT {{ number_format($verification->request->requested_amount_bdt, 0) }}</p>
        </div>
        <div>
          <p class="text-sm font-medium text-slate-700">Justification</p>
          <p class="text-slate-600">{{ $verification->request->livelihood_justification }}</p>
        </div>
      </div>
    </div>

    <form action="{{ route('verification.decide', $verification) }}" method="POST" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Decision</label>
        <div class="grid md:grid-cols-2 gap-4">
          <button type="submit" name="status" value="verified" class="px-4 py-3 rounded-xl border border-[#127A4A] text-[#127A4A] font-semibold hover:bg-[#127A4A]/5 transition">
            Approve (Verified)
          </button>
          <button type="submit" name="status" value="rejected" class="px-4 py-3 rounded-xl border border-[#E05D44] text-[#E05D44] font-semibold hover:bg-[#E05D44]/5 transition">
            Reject
          </button>
        </div>
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-700 mb-2">Decision Notes (required)</label>
        <textarea name="decision_notes" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required placeholder="Explain your decision..."></textarea>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="px-8 py-3 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] transition">Record Decision</button>
      </div>
    </form>
  </div>
</x-layout>
