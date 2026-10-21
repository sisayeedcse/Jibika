<x-layout title="Dashboard">
  <div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Dashboard</h1>
    <p class="text-slate-500">Welcome back, {{ auth()->user()->name }}.</p>
  </div>

  <div class="grid md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
      <div class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-2">Role</div>
      <div class="text-2xl font-bold">{{ ucfirst(auth()->user()->role) }}</div>
    </div>
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
      <div class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-2">Status</div>
      <div class="text-2xl font-bold {{ auth()->user()->isActive() ? 'text-[#127A4A]' : 'text-[#E05D44]' }}">
        {{ ucfirst(auth()->user()->status) }}
      </div>
    </div>
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
      <div class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-2">Unread Notifications</div>
      <div class="text-2xl font-bold">—</div>
    </div>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
    <h2 class="text-xl font-bold mb-4">Quick Actions</h2>
    <div class="grid md:grid-cols-3 gap-4">
      <a href="{{ route('requests.index') }}" class="p-4 rounded-xl bg-[#127A4A]/5 border border-[#127A4A]/20 text-[#127A4A] font-medium hover:bg-[#127A4A]/10 transition">
        View My Requests
      </a>
      @if(auth()->user()->isWorker())
        <a href="{{ route('requests.create') }}" class="p-4 rounded-xl bg-brand-green border border-transparent text-white font-medium hover:bg-[#0e5a36] shadow-md shadow-emerald-900/10 transition">
          New Asset Request
        </a>
      @endif
      @if(auth()->user()->isCommunity())
        <a href="{{ route('verification.index') }}" class="p-4 rounded-xl bg-[#E05D44]/10 border border-[#E05D44]/20 text-[#E05D44] font-medium hover:bg-[#E05D44]/20 transition">
          Verification Queue
        </a>
      @endif
      @if(auth()->user()->isContributor())
        <a href="{{ route('funding.index') }}" class="p-4 rounded-xl bg-[#127A4A]/5 border border-[#127A4A]/20 text-[#127A4A] font-medium hover:bg-[#127A4A]/10 transition">
          Browse Opportunities
        </a>
      @endif
      @if(auth()->user()->isVendor())
        <a href="{{ route('vendor.index') }}" class="p-4 rounded-xl bg-[#127A4A]/5 border border-[#127A4A]/20 text-[#127A4A] font-medium hover:bg-[#127A4A]/10 transition">
          Vendor Dashboard
        </a>
      @endif
      @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="p-4 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 font-medium hover:bg-slate-200 transition">
          Admin Panel
        </a>
      @endif
    </div>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 mt-6">
    <h2 class="text-xl font-bold mb-4">Recent Activity</h2>
    <p class="text-slate-400 text-sm">No recent activity. Start by creating an asset request or exploring opportunities.</p>
  </div>
</x-layout>
