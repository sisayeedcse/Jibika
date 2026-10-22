<x-layout title="Dashboard">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Dashboard</h1>
    <p class="text-[#475569]">Welcome back, <span class="font-semibold text-[#0f172a]">{{ auth()->user()->name }}</span>.</p>
  </div>

  <div class="grid md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <div class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-2">Role</div>
      <div class="text-xl font-extrabold text-[#0f172a]">{{ ucfirst(auth()->user()->role) }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <div class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-2">Status</div>
      <div class="text-xl font-extrabold {{ auth()->user()->isActive() ? 'text-[#127A4A]' : 'text-[#E05D44]' }}">{{ ucfirst(auth()->user()->status) }}</div>
    </div>
    <div class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9]">
      <div class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-2">Unread Notifications</div>
      <div class="text-xl font-extrabold text-[#0f172a]">—</div>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
    <h2 class="text-lg font-extrabold tracking-tight mb-5">Quick Actions</h2>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <a href="{{ route('requests.index') }}" class="p-5 rounded-xl bg-[#127A4A]/5 border border-[#127A4A]/15 text-[#127A4A] font-semibold hover:bg-[#127A4A]/10 transition-colors">View My Requests</a>
      @if(auth()->user()->isWorker())
        <a href="{{ route('requests.create') }}" class="p-5 rounded-xl bg-[#127A4A] text-white font-semibold hover:bg-[#0e5a36] shadow-md shadow-emerald-900/10 transition-colors">New Asset Request</a>
      @endif
      @if(auth()->user()->isCommunity())
        <a href="{{ route('verification.index') }}" class="p-5 rounded-xl bg-[#fbeae6] border border-[#E05D44]/15 text-[#E05D44] font-semibold hover:bg-[#fbeae6]/80 transition-colors">Verification Queue</a>
      @endif
      @if(auth()->user()->isContributor())
        <a href="{{ route('funding.index') }}" class="p-5 rounded-xl bg-[#127A4A]/5 border border-[#127A4A]/15 text-[#127A4A] font-semibold hover:bg-[#127A4A]/10 transition-colors">Browse Opportunities</a>
      @endif
      @if(auth()->user()->isVendor())
        <a href="{{ route('vendor.index') }}" class="p-5 rounded-xl bg-[#127A4A]/5 border border-[#127A4A]/15 text-[#127A4A] font-semibold hover:bg-[#127A4A]/10 transition-colors">Vendor Dashboard</a>
      @endif
      @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="p-5 rounded-xl bg-[#f1f5f9] border border-[#e2e8f0] text-[#475569] font-semibold hover:bg-[#eaeaea] transition-colors">Admin Panel</a>
      @endif
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8 mt-8">
    <h2 class="text-lg font-extrabold tracking-tight mb-2">Recent Activity</h2>
    <p class="text-sm text-[#475569]">No recent activity. Start by creating an asset request or exploring opportunities.</p>
  </div>
</x-layout>
