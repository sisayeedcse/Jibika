<x-layout title="Worker Workspace — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <h1 class="text-[1.75rem] md:text-[2.2rem] font-extrabold tracking-tight text-[#0f172a]">Worker Workspace</h1>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-[#127A4A] border border-emerald-200">
          Worker
        </span>
      </div>
      <p class="text-sm text-[#475569]">Welcome back, <span class="font-semibold text-[#0f172a]">{{ $user->name }}</span>. Track your productive asset requests and verification progress.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('requests.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Asset Request
      </a>
    </div>
  </div>

  <!-- Key Metrics Row -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Total Requests</p>
      <div class="text-2xl md:text-3xl font-black text-[#0f172a]">{{ $stats['total'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">All submitted &amp; draft</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Draft Requests</p>
      <div class="text-2xl md:text-3xl font-black text-amber-600">{{ $stats['drafts'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Ready to edit &amp; submit</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">In Verification</p>
      <div class="text-2xl md:text-3xl font-black text-blue-600">{{ $stats['in_progress'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Community &amp; AI review</p>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <p class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Funded / Active</p>
      <div class="text-2xl md:text-3xl font-black text-[#127A4A]">{{ $stats['funded'] }}</div>
      <p class="text-[0.75rem] text-[#475569] mt-2">Procurement &amp; monitoring</p>
    </div>
  </div>

  <!-- Profile Verification Notice if unverified -->
  @if(!$user->workerProfile || !$user->workerProfile->verified)
    <div class="mb-8 p-5 rounded-2xl bg-amber-50/70 border border-amber-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center text-amber-800 shrink-0 font-bold text-sm">!</div>
        <div>
          <h4 class="text-sm font-bold text-amber-900">Complete your Livelihood Profile</h4>
          <p class="text-xs text-amber-800 mt-0.5">Adding your craft experience and location helps community verifiers approve your requests without delay.</p>
        </div>
      </div>
      <a href="{{ route('profile.edit') }}" class="shrink-0 px-4 py-2 rounded-xl bg-amber-600 text-white text-xs font-semibold hover:bg-amber-700 transition-colors">
        Update Profile
      </a>
    </div>
  @endif

  <!-- Main Content Grid -->
  <div class="grid lg:grid-cols-3 gap-8">
    <!-- Left Column: Requests List -->
    <div class="lg:col-span-2 space-y-6">
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
        <div class="p-6 md:p-7 border-b border-[#f1f5f9] flex items-center justify-between">
          <div>
            <h2 class="text-base font-extrabold tracking-tight text-[#0f172a]">My Asset Requests</h2>
            <p class="text-xs text-[#475569] mt-0.5">Real-time status of your livelihood requests</p>
          </div>
          <a href="{{ route('requests.index') }}" class="text-xs font-semibold text-[#127A4A] hover:text-[#0e5a36] hover:underline">
            View All &rarr;
          </a>
        </div>

        @if($requests->count())
          <div class="divide-y divide-[#f1f5f9]">
            @foreach($requests as $req)
              <div class="p-6 hover:bg-[#f8fafc] transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="min-w-0">
                  <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                    <span class="text-xs font-mono font-bold text-[#94a3b8]">#{{ $req->id }}</span>
                    <h3 class="text-sm font-bold text-[#0f172a] truncate">{{ $req->category?->name ?? 'Custom Asset' }}</h3>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider
                      {{ $req->status === 'draft' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
                        (in_array($req->status, ['verified', 'ai_assessed', 'funding_open', 'funded']) ? 'bg-emerald-50 text-[#127A4A] border border-emerald-200' :
                        (in_array($req->status, ['delivered', 'monitoring', 'completed']) ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-50 text-[#475569] border border-slate-200')) }}">
                      {{ str_replace('_', ' ', $req->status) }}
                    </span>
                  </div>
                  <p class="text-xs text-[#475569] line-clamp-1 mb-2">{{ $req->reason }}</p>
                  <div class="flex items-center gap-4 text-xs text-[#94a3b8]">
                    <span class="font-bold text-[#127A4A]">BDT {{ number_format($req->requested_amount_bdt, 0) }}</span>
                    <span>&bull;</span>
                    <span>Updated {{ $req->updated_at->diffForHumans() }}</span>
                  </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                  @if($req->status === 'draft')
                    <a href="{{ route('requests.edit', $req) }}" class="px-3.5 py-1.5 rounded-xl border border-[#e2e8f0] text-xs font-medium text-[#475569] hover:bg-white hover:text-[#0f172a] transition-colors">
                      Edit
                    </a>
                  @endif
                  <a href="{{ route('requests.show', $req) }}" class="px-3.5 py-1.5 rounded-xl bg-[#127A4A]/10 text-[#127A4A] text-xs font-semibold hover:bg-[#127A4A] hover:text-white transition-colors">
                    Details
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="p-12 text-center">
            <div class="w-12 h-12 rounded-2xl bg-[#127A4A]/10 flex items-center justify-center mx-auto mb-4 text-[#127A4A]">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
              </svg>
            </div>
            <h3 class="text-sm font-bold text-[#0f172a] mb-1">No asset requests yet</h3>
            <p class="text-xs text-[#475569] max-w-sm mx-auto mb-5">Create your first productive asset request to gain tools, machinery, or equipment through community-backed funding.</p>
            <a href="{{ route('requests.create') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors">
              Submit Asset Request
            </a>
          </div>
        @endif
      </section>
    </div>

    <!-- Right Column: Quick Actions & Workflow Timeline Guide -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Quick Actions</h3>
        <div class="space-y-2.5">
          <a href="{{ route('requests.create') }}" class="flex items-center justify-between p-3 rounded-xl border border-[#f1f5f9] hover:border-[#127A4A]/30 hover:bg-[#f8fafc] text-xs font-semibold text-[#0f172a] transition-colors">
            <span>New Asset Request</span>
            <span class="text-[#127A4A]">&rarr;</span>
          </a>
          <a href="{{ route('requests.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-[#f1f5f9] hover:border-[#127A4A]/30 hover:bg-[#f8fafc] text-xs font-semibold text-[#0f172a] transition-colors">
            <span>Browse All My Requests</span>
            <span class="text-[#127A4A]">&rarr;</span>
          </a>
          <a href="{{ route('profile.edit') }}" class="flex items-center justify-between p-3 rounded-xl border border-[#f1f5f9] hover:border-[#127A4A]/30 hover:bg-[#f8fafc] text-xs font-semibold text-[#0f172a] transition-colors">
            <span>Update Livelihood Profile</span>
            <span class="text-[#127A4A]">&rarr;</span>
          </a>
          <a href="{{ route('notifications.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-[#f1f5f9] hover:border-[#127A4A]/30 hover:bg-[#f8fafc] text-xs font-semibold text-[#0f172a] transition-colors">
            <span>Notifications ({{ $unreadCount }} unread)</span>
            <span class="text-[#127A4A]">&rarr;</span>
          </a>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Productive Asset Lifecycle</h3>
        <ol class="relative border-l border-[#e2e8f0] ml-3 space-y-4 text-xs">
          <li class="pl-5 relative">
            <span class="absolute -left-1.5 top-0.5 w-3 h-3 rounded-full bg-[#127A4A] ring-4 ring-white"></span>
            <p class="font-bold text-[#0f172a]">1. Submit Request</p>
            <p class="text-[#475569] mt-0.5">Describe your tool/machine and expected livelihood benefits.</p>
          </li>
          <li class="pl-5 relative">
            <span class="absolute -left-1.5 top-0.5 w-3 h-3 rounded-full bg-slate-300 ring-4 ring-white"></span>
            <p class="font-bold text-[#0f172a]">2. Community Verification</p>
            <p class="text-[#475569] mt-0.5">Local verifier visits or calls to authenticate identity.</p>
          </li>
          <li class="pl-5 relative">
            <span class="absolute -left-1.5 top-0.5 w-3 h-3 rounded-full bg-slate-300 ring-4 ring-white"></span>
            <p class="font-bold text-[#0f172a]">3. Explainable AI Assessment</p>
            <p class="text-[#475569] mt-0.5">Objective evaluation of productive potential.</p>
          </li>
          <li class="pl-5 relative">
            <span class="absolute -left-1.5 top-0.5 w-3 h-3 rounded-full bg-slate-300 ring-4 ring-white"></span>
            <p class="font-bold text-[#0f172a]">4. Funding &amp; Procurement</p>
            <p class="text-[#475569] mt-0.5">Community contributors pledge; vendor delivers asset.</p>
          </li>
          <li class="pl-5 relative">
            <span class="absolute -left-1.5 top-0.5 w-3 h-3 rounded-full bg-slate-300 ring-4 ring-white"></span>
            <p class="font-bold text-[#0f172a]">5. Post-Delivery Monitoring</p>
            <p class="text-[#475569] mt-0.5">Record income milestones to demonstrate progress.</p>
          </li>
        </ol>
      </div>
    </div>
  </div>
</x-layout>
