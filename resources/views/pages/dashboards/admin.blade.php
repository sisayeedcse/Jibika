<x-layout title="Admin Command Center — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <h1 class="text-[1.75rem] md:text-[2.2rem] font-extrabold tracking-tight text-[#0f172a]">Platform Command Center</h1>
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-50 text-purple-700 border border-purple-200">
          Super Admin
        </span>
      </div>
      <p class="text-sm text-[#475569]">System-wide oversight of workers, verifiers, contributors, quotations, and immutable audit logs.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="{{ route('admin.users') }}" class="px-4 py-2 rounded-xl bg-white border border-[#e2e8f0] text-xs font-semibold text-[#0f172a] hover:bg-[#f8fafc] transition-colors">
        Manage Users
      </a>
      <a href="{{ route('admin.audit') }}" class="px-4 py-2 rounded-xl bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">
        Audit Registry
      </a>
    </div>
  </div>

  <!-- Key Metrics Row -->
  <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 md:gap-5 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-card border border-[#f1f5f9]">
      <p class="text-[0.65rem] font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Registered Users</p>
      <div class="text-2xl font-black text-[#0f172a]">{{ $stats['users'] }}</div>
      <a href="{{ route('admin.users') }}" class="text-[0.7rem] text-[#127A4A] hover:underline mt-1 block">View directory &rarr;</a>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-card border border-[#f1f5f9]">
      <p class="text-[0.65rem] font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Total Requests</p>
      <div class="text-2xl font-black text-[#0f172a]">{{ $stats['requests'] }}</div>
      <a href="{{ route('admin.requests') }}" class="text-[0.7rem] text-[#127A4A] hover:underline mt-1 block">All requests &rarr;</a>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-card border border-[#f1f5f9]">
      <p class="text-[0.65rem] font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Pending Verification</p>
      <div class="text-2xl font-black text-[#E05D44]">{{ $stats['pending_verification'] }}</div>
      <p class="text-[0.7rem] text-[#475569] mt-1">Awaiting Somitte</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-card border border-[#f1f5f9]">
      <p class="text-[0.65rem] font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Fully Funded</p>
      <div class="text-2xl font-black text-blue-600">{{ $stats['funded'] }}</div>
      <p class="text-[0.7rem] text-[#475569] mt-1">Ready for vendor quote</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-card border border-[#f1f5f9]">
      <p class="text-[0.65rem] font-extrabold uppercase tracking-wider text-[#94a3b8] mb-1">Delivered Assets</p>
      <div class="text-2xl font-black text-[#127A4A]">{{ $stats['delivered'] }}</div>
      <p class="text-[0.7rem] text-[#475569] mt-1">Active in monitoring</p>
    </div>
  </div>

  <!-- Main Grid -->
  <div class="grid lg:grid-cols-3 gap-8">
    <!-- Recent Requests Oversight -->
    <div class="lg:col-span-2 space-y-6">
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
        <div class="p-6 border-b border-[#f1f5f9] flex items-center justify-between">
          <div>
            <h2 class="text-base font-extrabold tracking-tight text-[#0f172a]">Recent Asset Requests</h2>
            <p class="text-xs text-[#475569] mt-0.5">Platform requests across all lifecycle phases</p>
          </div>
          <a href="{{ route('admin.requests') }}" class="text-xs font-semibold text-[#127A4A] hover:underline">
            View All &rarr;
          </a>
        </div>

        @if($recentRequests->count())
          <div class="divide-y divide-[#f1f5f9]">
            @foreach($recentRequests as $req)
              <div class="p-5 hover:bg-[#f8fafc] transition-colors flex items-center justify-between gap-4 text-xs">
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <span class="font-mono font-bold text-[#94a3b8]">#{{ $req->id }}</span>
                    <span class="font-bold text-[#0f172a]">{{ $req->category?->name ?? 'Custom Asset' }}</span>
                    <span class="text-[#475569]">by <strong>{{ $req->worker->name }}</strong></span>
                  </div>
                  <div class="text-[#94a3b8]">
                    BDT {{ number_format($req->requested_amount_bdt, 0) }} &bull; {{ $req->created_at->diffForHumans() }}
                  </div>
                </div>

                <div class="flex items-center gap-3">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider
                    {{ in_array($req->status, ['verified', 'ai_assessed', 'funding_open', 'funded', 'delivered']) ? 'bg-emerald-50 text-[#127A4A] border border-emerald-200' : 'bg-slate-50 text-[#475569] border border-slate-200' }}">
                    {{ str_replace('_', ' ', $req->status) }}
                  </span>
                  <a href="{{ route('requests.show', $req) }}" class="text-xs font-semibold text-[#127A4A] hover:underline">
                    Inspect &rarr;
                  </a>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="p-8 text-center text-xs text-[#475569]">No asset requests recorded yet.</div>
        @endif
      </section>

      <!-- Recent Audit Logs -->
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-base font-extrabold tracking-tight text-[#0f172a]">Security &amp; Audit Trail</h2>
          <a href="{{ route('admin.audit') }}" class="text-xs font-semibold text-[#127A4A] hover:underline">Full Log &rarr;</a>
        </div>

        <div class="space-y-2.5">
          @foreach($recentAuditLogs as $log)
            <div class="p-3 rounded-xl bg-[#f8fafc] border border-[#e2e8f0] flex items-center justify-between text-xs">
              <div>
                <span class="font-bold text-[#0f172a] font-mono text-[0.7rem]">{{ $log->action }}</span>
                <span class="text-[#475569] ml-2">by {{ $log->user?->name ?? 'System' }}</span>
              </div>
              <span class="text-[#94a3b8] text-[0.7rem]">{{ $log->created_at->diffForHumans() }}</span>
            </div>
          @endforeach
        </div>
      </section>
    </div>

    <!-- Quick Navigation -->
    <div class="space-y-6">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Admin Shortcuts</h3>
        <div class="space-y-2 text-xs">
          <a href="{{ route('admin.users') }}" class="flex items-center justify-between p-3 rounded-xl border border-[#f1f5f9] hover:bg-[#f8fafc] font-semibold text-[#0f172a]">
            <span>User Accounts &amp; Suspension</span>
            <span class="text-[#127A4A]">&rarr;</span>
          </a>
          <a href="{{ route('admin.requests') }}" class="flex items-center justify-between p-3 rounded-xl border border-[#f1f5f9] hover:bg-[#f8fafc] font-semibold text-[#0f172a]">
            <span>Request Management</span>
            <span class="text-[#127A4A]">&rarr;</span>
          </a>
          <a href="{{ route('admin.audit') }}" class="flex items-center justify-between p-3 rounded-xl border border-[#f1f5f9] hover:bg-[#f8fafc] font-semibold text-[#0f172a]">
            <span>Audit Trail Registry</span>
            <span class="text-[#127A4A]">&rarr;</span>
          </a>
          <a href="{{ route('procurement.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-[#f1f5f9] hover:bg-[#f8fafc] font-semibold text-[#0f172a]">
            <span>Procurement &amp; Deliveries</span>
            <span class="text-[#127A4A]">&rarr;</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</x-layout>
