<x-layout title="Community Verifier Workspace — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <h1 class="text-[1.75rem] md:text-[2.2rem] font-extrabold tracking-tight text-[#0f172a]">Community Verification Desk</h1>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-coral-50 text-[#E05D44] border border-[#E05D44]/30" style="background-color: #fbeae6;">
          Community Verifier
        </span>
      </div>
      <p class="text-sm text-[#475569]">Welcome, <span class="font-semibold text-[#0f172a]">{{ $user->name }}</span>. Review local worker applications, verify livelihood claims, and uphold community trust.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('verification.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
        </svg>
        Open Review Queue
      </a>
    </div>
  </div>

  <!-- Key Metrics Row -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Awaiting Review</p>
      <div class="text-3xl font-black text-[#E05D44]">{{ $stats['pending_queue'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Requests in community jurisdiction</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Approved by You</p>
      <div class="text-3xl font-black text-[#127A4A]">{{ $stats['verified_by_me'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Worker identities authenticated</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Flagged / Rejected</p>
      <div class="text-3xl font-black text-slate-700">{{ $stats['flagged_by_me'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Required additional verification</p>
    </div>
  </div>

  <!-- Main Content Grid -->
  <div class="grid lg:grid-cols-3 gap-8">
    <!-- Verification Queue -->
    <div class="lg:col-span-2 space-y-6">
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
        <div class="p-6 md:p-7 border-b border-[#f1f5f9] flex items-center justify-between">
          <div>
            <h2 class="text-base font-extrabold tracking-tight text-[#0f172a]">Active Verification Queue</h2>
            <p class="text-xs text-[#475569] mt-0.5">Worker requests needing identity &amp; trade authentication</p>
          </div>
          <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
            {{ $pendingVerifications->count() }} Pending
          </span>
        </div>

        @if($pendingVerifications->count())
          <div class="divide-y divide-[#f1f5f9]">
            @foreach($pendingVerifications as $item)
              <div class="p-6 hover:bg-[#f8fafc] transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-xs font-mono font-bold text-[#94a3b8]">REQ #{{ $item->request_id }}</span>
                    <h3 class="text-sm font-bold text-[#0f172a] truncate">{{ $item->request->category?->name ?? 'Productive Asset' }}</h3>
                  </div>
                  <p class="text-xs text-[#0f172a] font-medium mb-1">
                    Worker: <span class="text-[#127A4A]">{{ $item->request->worker->name }}</span>
                    @if($item->request->worker->workerProfile?->location)
                      &bull; <span class="text-[#475569]">{{ $item->request->worker->workerProfile->location }}</span>
                    @endif
                  </p>
                  <p class="text-xs text-[#475569] line-clamp-1 mb-2">{{ $item->request->reason }}</p>
                  <div class="flex items-center gap-3 text-xs text-[#94a3b8]">
                    <span class="font-bold text-[#0f172a]">BDT {{ number_format($item->request->requested_amount_bdt, 0) }}</span>
                    <span>&bull;</span>
                    <span>Submitted {{ $item->created_at->diffForHumans() }}</span>
                  </div>
                </div>

                <div class="shrink-0">
                  <a href="{{ route('verification.review', $item) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">
                    Review Evidence &rarr;
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="p-12 text-center">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center mx-auto mb-4 text-[#127A4A]">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
            </div>
            <h3 class="text-sm font-bold text-[#0f172a] mb-1">Queue is Clear</h3>
            <p class="text-xs text-[#475569] max-w-sm mx-auto">No pending verification requests at this time. New applications from your region will appear here.</p>
          </div>
        @endif
      </section>

      <!-- Recent Decisions History -->
      @if($myDecisions->count())
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6 md:p-7">
          <h2 class="text-base font-extrabold tracking-tight text-[#0f172a] mb-4">My Recent Decisions</h2>
          <div class="space-y-3">
            @foreach($myDecisions as $decision)
              <div class="p-4 rounded-xl bg-[#f8fafc] border border-[#e2e8f0] flex items-center justify-between gap-4 text-xs">
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <span class="font-bold text-[#0f172a]">{{ $decision->request->worker->name }}</span>
                    <span class="text-[#94a3b8]">Request #{{ $decision->request_id }}</span>
                  </div>
                  <p class="text-[#475569] italic">"{{ Str::limit($decision->decision_notes, 80) }}"</p>
                </div>
                <div class="text-right shrink-0">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider
                    {{ $decision->status === 'verified' ? 'bg-emerald-50 text-[#127A4A] border border-emerald-200' : 'bg-red-50 text-[#E05D44] border border-red-200' }}">
                    {{ $decision->status }}
                  </span>
                  <p class="text-[0.65rem] text-[#94a3b8] mt-1">{{ $decision->verified_at ? $decision->verified_at->format('M d, Y') : '' }}</p>
                </div>
              </div>
            @endforeach
          </div>
        </section>
      @endif
    </div>

    <!-- Verifier Principles Guide -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Community Verifier Duties</h3>
        <ul class="space-y-3 text-xs text-[#475569]">
          <li class="flex items-start gap-2.5">
            <span class="w-5 h-5 rounded-full bg-emerald-50 text-[#127A4A] flex items-center justify-center font-bold text-[0.65rem] shrink-0 mt-0.5">1</span>
            <span><strong>Confirm Real Identity:</strong> Ensure the applicant lives and works within the specified community.</span>
          </li>
          <li class="flex items-start gap-2.5">
            <span class="w-5 h-5 rounded-full bg-emerald-50 text-[#127A4A] flex items-center justify-center font-bold text-[0.65rem] shrink-0 mt-0.5">2</span>
            <span><strong>Authenticate Craft/Trade:</strong> Check that the worker actually practices the profession stated in their profile.</span>
          </li>
          <li class="flex items-start gap-2.5">
            <span class="w-5 h-5 rounded-full bg-emerald-50 text-[#127A4A] flex items-center justify-center font-bold text-[0.65rem] shrink-0 mt-0.5">3</span>
            <span><strong>Verify Asset Necessity:</strong> Assess whether the productive asset will realistically increase household earnings.</span>
          </li>
          <li class="flex items-start gap-2.5">
            <span class="w-5 h-5 rounded-full bg-emerald-50 text-[#127A4A] flex items-center justify-center font-bold text-[0.65rem] shrink-0 mt-0.5">4</span>
            <span><strong>Record Objective Notes:</strong> Document specific reasons for approval or flag issues with clarity.</span>
          </li>
        </ul>
      </div>

      <div class="p-5 rounded-2xl bg-[#fbeae6] border border-[#E05D44]/20 text-xs text-[#E05D44]">
        <h4 class="font-bold mb-1">Audit Trail &amp; Accountability</h4>
        <p class="leading-relaxed">All verification decisions are cryptographically logged in the immutable audit registry to prevent fraud and preserve community trust.</p>
      </div>
    </div>
  </div>
</x-layout>
