<x-layout title="Edit Profile">
  <div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight">Edit Profile</h1>
    </div>
    <p class="text-[#475569]">Update your account and livelihood information.</p>
  </div>

  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8">
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6" aria-label="Profile edit form">
          @csrf
          @method('PUT')

          <div class="grid md:grid-cols-2 gap-5">
            <div>
              <label for="name" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Full Name</label>
              <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required aria-required="true">
            </div>
            <div>
              <label for="email" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Email Address</label>
              <input id="email" type="email" value="{{ $user->email }}" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm bg-slate-50 text-slate-500 cursor-not-allowed" disabled>
              <p class="text-[0.7rem] text-[#94a3b8] mt-1">Contact an admin to change your primary email.</p>
            </div>
          </div>

          @if($user->isWorker())
            <div class="pt-6 border-t border-[#f1f5f9]">
              <h3 class="text-sm font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Livelihood Information</h3>
              <div class="grid md:grid-cols-2 gap-5">
                <div>
                  <label for="occupation" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Occupation / Trade</label>
                  <input id="occupation" type="text" name="occupation" value="{{ old('occupation', $user->workerProfile?->occupation) }}" placeholder="e.g. Master Tailor, Carpenter" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors">
                </div>
                <div>
                  <label for="experience_years" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Years of Experience</label>
                  <input id="experience_years" type="number" name="experience_years" value="{{ old('experience_years', $user->workerProfile?->experience_years ?? 0) }}" min="0" max="60" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors">
                </div>
                <div class="md:col-span-2">
                  <label for="location" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Location / City</label>
                  <input id="location" type="text" name="location" value="{{ old('location', $user->workerProfile?->location) }}" placeholder="e.g. Chattogram, Bangladesh" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors">
                </div>
                <div class="md:col-span-2">
                  <label for="bio" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Livelihood Background & Goals</label>
                  <textarea id="bio" name="bio" rows="4" placeholder="Briefly describe your craft, workshop, and livelihood objectives..." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y">{{ old('bio', $user->workerProfile?->bio) }}</textarea>
                </div>
              </div>
            </div>
          @endif

          <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-8 py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Save Changes</button>
            <a href="{{ route('profile.show') }}" class="px-5 py-3 rounded-xl border border-[#e2e8f0] text-sm font-medium text-[#475569] hover:bg-[#f8fafc] transition-colors">Cancel</a>
          </div>
        </form>
      </section>
    </div>

    <aside class="lg:col-span-1">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 sticky top-24">
        <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-4">Account Overview</h3>
        <div class="space-y-3 mb-6">
          <div class="flex justify-between items-center text-sm py-2 border-b border-[#f1f5f9]">
            <span class="text-[#475569]">Role</span>
            <span class="font-bold text-[#0f172a] capitalize">{{ $user->role }}</span>
          </div>
          <div class="flex justify-between items-center text-sm py-2 border-b border-[#f1f5f9]">
            <span class="text-[#475569]">Status</span>
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-semibold {{ $user->isActive() ? 'bg-emerald-50 text-[#127A4A]' : 'bg-red-50 text-[#E05D44]' }}">
              <span class="w-1.5 h-1.5 rounded-full {{ $user->isActive() ? 'bg-[#127A4A]' : 'bg-[#E05D44]' }}"></span>
              {{ ucfirst($user->status) }}
            </span>
          </div>
          <div class="flex justify-between items-center text-sm py-2">
            <span class="text-[#475569]">Member Since</span>
            <span class="text-[#0f172a] font-medium">{{ $user->created_at?->format('M Y') ?? 'N/A' }}</span>
          </div>
        </div>
        <p class="text-xs text-[#94a3b8] leading-relaxed">
          Accurate livelihood information helps community verifiers assess requests faster and with higher confidence.
        </p>
      </div>
    </aside>
  </div>
</x-layout>
