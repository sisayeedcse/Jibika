<x-layout title="Profile — {{ $user->name }}">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">My Profile</h1>
      <p class="text-[#475569]">Manage your account details and livelihood credentials.</p>
    </div>
    <a href="{{ route('profile.edit') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
      </svg>
      Edit Profile
    </a>
  </div>

  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
      <!-- Identity Card -->
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
        <div class="flex items-center gap-5 pb-6 border-b border-[#f1f5f9]">
          <div class="w-16 h-16 rounded-2xl bg-[#127A4A]/10 flex items-center justify-center text-2xl font-black text-[#127A4A] border border-[#127A4A]/20">
            {{ strtoupper(substr($user->name, 0, 1)) }}
          </div>
          <div>
            <div class="flex items-center gap-3 mb-1">
              <h2 class="text-xl font-extrabold tracking-tight text-[#0f172a]">{{ $user->name }}</h2>
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-[#127A4A] border border-emerald-200">
                {{ $user->role }}
              </span>
            </div>
            <p class="text-sm text-[#475569]">{{ $user->email }}</p>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-6 pt-6">
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Account Role</p>
            <p class="text-sm font-bold text-[#0f172a] capitalize">{{ $user->role }}</p>
          </div>
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Account Status</p>
            <span class="inline-flex items-center gap-1.5 text-sm font-semibold {{ $user->isActive() ? 'text-[#127A4A]' : 'text-[#E05D44]' }}">
              <span class="w-2 h-2 rounded-full {{ $user->isActive() ? 'bg-[#127A4A]' : 'bg-[#E05D44]' }}"></span>
              {{ ucfirst($user->status) }}
            </span>
          </div>
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Registered Since</p>
            <p class="text-sm font-medium text-[#0f172a]">{{ $user->created_at?->format('F d, Y') ?? 'N/A' }}</p>
          </div>
          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Unread Alerts</p>
            <p class="text-sm font-medium text-[#0f172a]">{{ $user->unreadNotifications()->count() }}</p>
          </div>
        </div>
      </section>

      <!-- Worker Profile Section -->
      @if($user->isWorker())
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
          <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#f1f5f9]">
            <h3 class="text-base font-extrabold tracking-tight text-[#0f172a]">Livelihood Credentials</h3>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $user->workerProfile?->verified ? 'bg-emerald-50 text-[#127A4A] border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
              {{ $user->workerProfile?->verified ? 'Verified Worker' : 'Self-declared / Unverified' }}
            </span>
          </div>

          <div class="grid sm:grid-cols-2 gap-6 mb-6">
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Occupation / Trade</p>
              <p class="text-sm font-bold text-[#0f172a]">{{ $user->workerProfile?->occupation ?: 'Not specified' }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Experience</p>
              <p class="text-sm font-bold text-[#0f172a]">{{ $user->workerProfile?->experience_years ? $user->workerProfile->experience_years . ' Years' : 'Not specified' }}</p>
            </div>
            <div class="sm:col-span-2">
              <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Operating Location</p>
              <p class="text-sm font-medium text-[#0f172a]">{{ $user->workerProfile?->location ?: 'Not specified' }}</p>
            </div>
          </div>

          <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">Livelihood Background</p>
            <p class="text-sm text-[#475569] leading-relaxed bg-[#f8fafc] p-4 rounded-xl border border-[#e2e8f0]">
              {{ $user->workerProfile?->bio ?: 'No bio provided yet. Add your livelihood background to strengthen your productive asset requests.' }}
            </p>
          </div>
        </section>
      @endif

      @if($user->isCommunity() && $user->communityProfile)
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
          <h3 class="text-base font-extrabold tracking-tight text-[#0f172a] mb-4 pb-4 border-b border-[#f1f5f9]">Community Jurisdiction</h3>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Verifier Name</p>
              <p class="text-sm font-bold text-[#0f172a]">{{ $user->communityProfile->full_name }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Assigned Region</p>
              <p class="text-sm font-bold text-[#0f172a]">{{ $user->communityProfile->location }}</p>
            </div>
          </div>
        </section>
      @endif

      @if($user->isVendor() && $user->vendorProfile)
        <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
          <h3 class="text-base font-extrabold tracking-tight text-[#0f172a] mb-4 pb-4 border-b border-[#f1f5f9]">Vendor Enterprise Information</h3>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Company / Enterprise</p>
              <p class="text-sm font-bold text-[#0f172a]">{{ $user->vendorProfile->company_name }}</p>
            </div>
            <div>
              <p class="text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1">Supplier Type</p>
              <p class="text-sm font-bold text-[#0f172a]">{{ $user->vendorProfile->supplier_type ?: 'General Machinery / Tools' }}</p>
            </div>
          </div>
        </section>
      @endif
    </div>

    <!-- Sidebar summary -->
    <aside class="lg:col-span-1 space-y-6">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Quick Links</h3>
        <div class="space-y-2">
          <a href="{{ route('dashboard') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-[#475569] hover:bg-[#f8fafc] hover:text-[#127A4A] transition-colors">
            Workspace Dashboard
          </a>
          <a href="{{ route('notifications.index') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-[#475569] hover:bg-[#f8fafc] hover:text-[#127A4A] transition-colors">
            Notifications Center
          </a>
          <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 rounded-xl text-sm font-medium text-[#475569] hover:bg-[#f8fafc] hover:text-[#127A4A] transition-colors">
            Edit Information
          </a>
        </div>
      </div>

      <div class="bg-emerald-50/50 rounded-2xl border border-emerald-100 p-6">
        <h4 class="text-sm font-bold text-[#127A4A] mb-2">Verified Credibility</h4>
        <p class="text-xs text-[#475569] leading-relaxed">
          JIBIKA verifies all identities and productive claims with local community representatives before funding pools are opened.
        </p>
      </div>
    </aside>
  </div>
</x-layout>
