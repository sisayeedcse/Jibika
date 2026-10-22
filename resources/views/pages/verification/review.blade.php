<x-layout title="Review Verification — Request #{{ $verification->request->id }}">
  <div class="max-w-3xl">
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-1">
        <a href="{{ route('verification.index') }}" class="text-xs font-semibold text-[#127A4A] hover:underline">&larr; Back to Verification Queue</a>
      </div>
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Community Review &bull; Request #{{ $verification->request->id }}</h1>
      <p class="text-sm text-[#475569]">Authenticate worker credentials, evaluate livelihood necessity, and record an objective decision.</p>
    </div>

    <!-- Request Details Summary Card -->
    <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8 mb-6" aria-label="Request summary">
      <h2 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-5">Applicant &amp; Asset Dossier</h2>
      <div class="grid sm:grid-cols-2 gap-6">
        <div>
          <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Applicant Worker</p>
          <p class="text-sm font-bold text-[#0f172a]">{{ $verification->request->worker->name }}</p>
          <p class="text-xs text-[#475569] mt-0.5">{{ $verification->request->worker->email }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Asset Category</p>
          <p class="text-sm font-bold text-[#0f172a]">{{ $verification->request->category?->name ?? 'Custom Asset' }}</p>
          <p class="text-xs text-[#475569] mt-0.5">Estimated BDT {{ number_format($verification->request->category?->estimated_cost_bdt ?? 0, 0) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Requested Funding</p>
          <p class="text-base font-black text-[#127A4A]">BDT {{ number_format($verification->request->requested_amount_bdt, 0) }}</p>
        </div>
        <div>
          <p class="text-xs uppercase tracking-wider text-[#94a3b8] font-semibold mb-1">Worker Location</p>
          <p class="text-sm font-semibold text-[#0f172a]">{{ $verification->request->worker->workerProfile?->location ?: 'Not specified' }}</p>
          <p class="text-xs text-[#475569] mt-0.5">Experience: {{ $verification->request->worker->workerProfile?->experience_years ? $verification->request->worker->workerProfile->experience_years . ' yrs' : 'N/A' }}</p>
        </div>
      </div>

      <div class="mt-6 pt-5 border-t border-[#f1f5f9] space-y-4">
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Statement of Need</h3>
          <p class="text-sm text-[#475569] leading-relaxed bg-[#f8fafc] p-3.5 rounded-xl border border-[#e2e8f0]">
            {{ $verification->request->reason }}
          </p>
        </div>
        <div>
          <h3 class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Expected Productivity &amp; Income Impact</h3>
          <p class="text-sm text-[#475569] leading-relaxed bg-[#f8fafc] p-3.5 rounded-xl border border-[#e2e8f0]">
            {{ $verification->request->livelihood_justification }}
          </p>
        </div>
      </div>
    </section>

    <!-- Decision Form -->
    <form action="{{ route('verification.decide', $verification) }}" method="POST" class="space-y-6" aria-label="Verification decision">
      @csrf
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
        <h2 class="text-base font-extrabold tracking-tight text-[#0f172a] mb-2">Verification Decision</h2>
        <p class="text-xs text-[#475569] mb-6">Select your verification determination. This action is permanently logged with your user signature.</p>

        <!-- Status Radio Selection Grid -->
        <div class="grid sm:grid-cols-2 gap-3 mb-6">
          <label class="relative flex items-start p-4 rounded-xl border-2 border-[#e2e8f0] cursor-pointer hover:border-[#127A4A] transition-colors has-[:checked]:border-[#127A4A] has-[:checked]:bg-emerald-50/40">
            <input type="radio" name="status" value="verified" class="mt-1 text-[#127A4A] focus:ring-[#127A4A]" required checked>
            <div class="ml-3">
              <span class="block text-sm font-bold text-[#0f172a]">Approve &bull; Verified</span>
              <span class="block text-xs text-[#475569] mt-0.5">Worker identity and productive asset necessity are authenticated.</span>
            </div>
          </label>

          <label class="relative flex items-start p-4 rounded-xl border-2 border-[#e2e8f0] cursor-pointer hover:border-amber-400 transition-colors has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50/40">
            <input type="radio" name="status" value="flagged" class="mt-1 text-amber-600 focus:ring-amber-500">
            <div class="ml-3">
              <span class="block text-sm font-bold text-[#0f172a]">Flag for Secondary Review</span>
              <span class="block text-xs text-[#475569] mt-0.5">Discrepancy detected or additional local verification needed.</span>
            </div>
          </label>

          <label class="relative flex items-start p-4 rounded-xl border-2 border-[#e2e8f0] cursor-pointer hover:border-blue-400 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50/40">
            <input type="radio" name="status" value="needs_info" class="mt-1 text-blue-600 focus:ring-blue-500">
            <div class="ml-3">
              <span class="block text-sm font-bold text-[#0f172a]">Needs More Information</span>
              <span class="block text-xs text-[#475569] mt-0.5">Request additional trade history or proof of workspace.</span>
            </div>
          </label>

          <label class="relative flex items-start p-4 rounded-xl border-2 border-[#e2e8f0] cursor-pointer hover:border-[#E05D44] transition-colors has-[:checked]:border-[#E05D44] has-[:checked]:bg-red-50/40">
            <input type="radio" name="status" value="rejected" class="mt-1 text-[#E05D44] focus:ring-[#E05D44]">
            <div class="ml-3">
              <span class="block text-sm font-bold text-[#0f172a]">Reject Request</span>
              <span class="block text-xs text-[#475569] mt-0.5">Worker does not practice trade or claim cannot be substantiated.</span>
            </div>
          </label>
        </div>

        <div>
          <label for="decision_notes" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Verification Notes &amp; Evidence Basis (Required)</label>
          <textarea id="decision_notes" name="decision_notes" rows="4" placeholder="Detail your physical or phone verification: who did you speak with, what documents were inspected, and what led to this determination..." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y" required aria-required="true"></textarea>
          <p class="text-xs text-[#94a3b8] mt-1.5">Notes are visible to platform administrators and included in community transparency logs.</p>
        </div>
      </section>

      <div class="flex items-center justify-between gap-4">
        <a href="{{ route('verification.index') }}" class="px-5 py-2.5 rounded-xl border border-[#e2e8f0] text-sm font-medium text-[#475569] hover:bg-[#f8fafc] transition-colors">
          Cancel &amp; Return
        </a>
        <button type="submit" class="px-8 py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
          Submit Official Decision
        </button>
      </div>
    </form>
  </div>
</x-layout>
