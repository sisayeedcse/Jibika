<x-layout title="Review Verification">
  <div class="max-w-2xl">
    <div class="mb-7">
      <h1 class="text-[1.6rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Review Request #{{ $verification->request->id }}</h1>
      <p class="text-[#475569]">Evaluate evidence and record a clear, justified decision.</p>
    </div>

    <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8 mb-6" aria-label="Request summary">
      <h2 class="text-sm font-extrabold uppercase tracking-wider text-[#94a3b8] mb-5">Request Summary</h2>
      <div class="grid sm:grid-cols-2 gap-6">
        <div>
          <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Worker</p>
          <p class="font-semibold text-[#0f172a]">{{ $verification->request->worker->name ?? 'Unknown' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Asset</p>
          <p class="font-semibold text-[#0f172a]">{{ $verification->request->category?->name ?? 'N/A' }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Requested Amount</p>
          <p class="font-extrabold text-[#127A4A]">BDT {{ number_format($verification->request->requested_amount_bdt, 0) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Status</p>
          <p class="font-bold text-[#0f172a]">{{ ucfirst(str_replace('_', ' ', $verification->request->status)) }}</p>
        </div>
      </div>
      <div class="mt-5 pt-5 border-t border-[#f1f5f9]">
        <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Justification</p>
        <p class="text-sm text-[#475569] leading-relaxed">{{ $verification->request->livelihood_justification ?? 'No justification provided.' }}</p>
      </div>
    </section>

    <form action="{{ route('verification.decide', $verification) }}" method="POST" class="space-y-5" aria-label="Verification decision">
      @csrf
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
        <h2 class="text-base font-extrabold tracking-tight mb-5">Decision</h2>
        <p class="text-sm text-[#475569] mb-5">Select the outcome of this verification. Required notes will be recorded with your decision.</p>
        <div class="grid sm:grid-cols-2 gap-4 mb-6">
          <button type="submit" name="status" value="verified" class="w-full py-3.5 rounded-xl border-2 border-[#127A4A]/20 bg-[#127A4A]/5 text-[#127A4A] font-bold hover:bg-[#127A4A]/10 transition-colors text-sm focus:outline-none focus:ring-2 focus:ring-[#127A4A]/20">
            Approve — Verified
          </button>
          <button type="submit" name="status" value="rejected" class="w-full py-3.5 rounded-xl border-2 border-[#E05D44]/20 bg-[#E05D44]/5 text-[#E05D44] font-bold hover:bg-[#E05D44]/10 transition-colors text-sm focus:outline-none focus:ring-2 focus:ring-[#E05D44]/20">
            Reject
          </button>
        </div>
        <div>
          <label for="decision_notes" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Decision Notes (required)</label>
          <textarea id="decision_notes" name="decision_notes" rows="4" placeholder="Explain your decision with reference to evidence..." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y" required aria-required="true"></textarea>
          <p class="text-xs text-[#94a3b8] mt-1">Notes must clearly explain the basis for approval, rejection, or any flags raised.</p>
        </div>
      </section>
      <div class="flex justify-end">
        <button type="submit" class="inline-flex items-center px-8 py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Record Decision</button>
      </div>
    </form>
  </div>
</x-layout>
