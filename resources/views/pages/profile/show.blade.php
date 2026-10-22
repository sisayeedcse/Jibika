<x-layout title="Profile">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Profile</h1>
    <p class="text-[#475569]">Manage your account details and livelihood information.</p>
  </div>

  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
        <div class="flex items-center gap-4 mb-7">
          <div class="w-14 h-14 rounded-2xl bg-[#127A4A]/10 flex items-center justify-center text-xl font-extrabold text-[#127A4A]">
            {{ strtoupper(substr($user->name, 0, 1)) }}
          </div>
          <div>
            <h2 class="text-xl font-extrabold tracking-tight">{{ $user->name }}</h2>
            <p class="text-sm text-[#475569]">{{ $user->email }} • {{ ucfirst($user->role) }}</p>
          </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6" aria-label="Profile update form">
          @csrf
          @method('PUT')
          <div class="grid md:grid-cols-2 gap-5">
            <div>
              <label for="profile_name" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Full Name</label>
              <input id="profile_name" type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required aria-required="true">
            </div>
            <div>
              <label for="profile_email" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Email</label>
              <input id="profile_email" type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" disabled aria-disabled="true">
            </div>
          </div>

          @if($user->workerProfile)
            <div class="pt-4 border-t border-[#f1f5f9]">
              <h3 class="text-sm font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Livelihood Details</h3>
              <div class="grid md:grid-cols-2 gap-5">
                <div>
                  <label for="occupation" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Occupation</label>
                  <input id="occupation" type="text" name="occupation" value="{{ old('occupation', $user->workerProfile->occupation) }}" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors">
                </div>
                <div>
                  <label for="experience_years" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Experience (Years)</label>
                  <input id="experience_years" type="number" name="experience_years" value="{{ old('experience_years', $user->workerProfile->experience_years) }}" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors">
                </div>
                <div>
                  <label for="location" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Location</label>
                  <input id="location" type="text" name="location" value="{{ old('location', $user->workerProfile->location) }}" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors">
                </div>
                <div class="md:col-span-2">
                  <label for="bio" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Bio / Story</label>
                  <textarea id="bio" name="bio" rows="3" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y">{{ old('bio', $user->workerProfile->bio) }}</textarea>
                </div>
              </div>
            </div>
          @endif

          <button type="submit" class="w-full py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Save Changes</button>
        </form>
      </section>
    </div>

    <aside class="lg:col-span-1">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 sticky top-24">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-3">Account</h3>
        <div class="p-4 rounded-xl bg-[#f1f5f9] border border-[#e2e8f0] text-sm mb-5">
          <div class="font-bold text-[#0f172a] mb-0.5">{{ ucfirst($user->role) }}</div>
          <div class="text-xs text-[#94a3b8]">Status: <span class="font-semibold text-[#127A4A]">{{ $user->isActive() ? 'Active' : 'Suspended' }}</span></div>
        </div>
        <div class="text-xs text-[#94a3b8] leading-relaxed">Account updates are saved server-side. Sensitive changes require re-authentication.</div>
      </div>
    </aside>
  </div>
</x-layout>
