<x-layout title="Profile">
  <div class="mb-8">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Profile</h1>
    <p class="text-slate-500">Manage your account and livelihood information.</p>
  </div>

  <div class="grid lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center gap-4 mb-6">
          <div class="w-16 h-16 rounded-full bg-[#127A4A]/20 flex items-center justify-center text-2xl font-bold text-[#127A4A]">
            {{ strtoupper(substr($user->name, 0, 1)) }}
          </div>
          <div>
            <h2 class="text-xl font-bold">{{ $user->name }}</h2>
            <p class="text-sm text-slate-500">{{ $user->email }} • {{ ucfirst($user->role) }}</p>
          </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
          @csrf
          @method('PUT')
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
              <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required disabled>
            </div>
          </div>

          @if($user->workerProfile)
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mt-8 mb-4">Livelihood Details</h3>
            <div class="grid md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Occupation</label>
                <input type="text" name="occupation" value="{{ old('occupation', $user->workerProfile->occupation) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Experience (Years)</label>
                <input type="number" name="experience_years" value="{{ old('experience_years', $user->workerProfile->experience_years) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring">
              </div>
              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Location</label>
                <input type="text" name="location" value="{{ old('location', $user->workerProfile->location) }}" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring">
              </div>
              <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Bio / Story</label>
                <textarea name="bio" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring">{{ old('bio', $user->workerProfile->bio) }}</textarea>
              </div>
            </div>
          @endif

          <button type="submit" class="w-full py-4 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] shadow-md shadow-emerald-900/10 transition">
            Save Changes
          </button>
        </form>
      </div>
    </div>

    <div class="lg:col-span-1">
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 sticky top-24">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">Account Role</h3>
        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-sm mb-6">
          <div class="font-medium">{{ ucfirst($user->role) }}</div>
          <div class="text-xs text-slate-400">{{ $user->isActive() ? 'Active' : 'Suspended' }}</div>
        </div>
      </div>
    </div>
  </div>
</x-layout>
