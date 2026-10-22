<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Create your JIBIKA account.">
<title>Register — JIBIKA</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Inter','Noto Sans Bengali',system-ui,sans-serif; }
  .focus-ring:focus { outline: none; box-shadow: 0 0 0 2px rgba(18,122,74,0.18); border-radius: 0.75rem; }
</style>
</head>
<body class="bg-[#f8fafc] text-[#0f172a] antialiased min-h-screen flex flex-col justify-between">

  <nav class="bg-white/85 backdrop-blur-xl border-b border-[#e2e8f0] sticky top-0 z-50" aria-label="Primary navigation">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-[68px]">
        <a href="/" class="text-[1.35rem] font-extrabold tracking-tight text-[#127A4A]" aria-label="JIBIKA Home">JIBIKA</a>
        <div class="flex items-center gap-7 text-[0.88rem] font-medium text-[#475569]">
          <a href="/" class="hover:text-[#127A4A] transition-colors">Home</a>
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">How It Works</a>
          <a href="{{ route('login') }}" class="hover:text-[#127A4A] transition-colors">Sign In</a>
        </div>
      </div>
    </div>
  </nav>

  <main class="flex-grow flex items-center justify-center px-6 py-12 md:py-16">
    <div class="w-full max-w-[480px]">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-8 md:p-10">
        <div class="mb-7">
          <h1 class="text-[1.6rem] font-extrabold tracking-tight mb-1 text-[#0f172a]">Create Account</h1>
          <p class="text-sm text-[#475569]">Join JIBIKA to request productive assets or participate in funding and supply.</p>
        </div>

        @if($errors->any())
          <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs font-medium text-[#E05D44]">
            <ul class="list-disc list-inside space-y-1">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4" novalidate>
          @csrf

          <!-- Role Selection Grid -->
          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-2">I am registering as</label>
            <div class="grid grid-cols-3 gap-2">
              <label class="p-2.5 rounded-xl border border-[#e2e8f0] text-center cursor-pointer hover:border-[#127A4A] transition-colors has-[:checked]:border-[#127A4A] has-[:checked]:bg-emerald-50/50">
                <input type="radio" name="role" value="worker" class="sr-only" {{ old('role', 'worker') === 'worker' ? 'checked' : '' }}>
                <span class="block text-xs font-bold text-[#0f172a]">Worker</span>
                <span class="block text-[0.65rem] text-[#94a3b8]">Request Asset</span>
              </label>
              <label class="p-2.5 rounded-xl border border-[#e2e8f0] text-center cursor-pointer hover:border-[#127A4A] transition-colors has-[:checked]:border-[#127A4A] has-[:checked]:bg-emerald-50/50">
                <input type="radio" name="role" value="contributor" class="sr-only" {{ old('role') === 'contributor' ? 'checked' : '' }}>
                <span class="block text-xs font-bold text-[#0f172a]">Contributor</span>
                <span class="block text-[0.65rem] text-[#94a3b8]">Pledge Support</span>
              </label>
              <label class="p-2.5 rounded-xl border border-[#e2e8f0] text-center cursor-pointer hover:border-[#127A4A] transition-colors has-[:checked]:border-[#127A4A] has-[:checked]:bg-emerald-50/50">
                <input type="radio" name="role" value="vendor" class="sr-only" {{ old('role') === 'vendor' ? 'checked' : '' }}>
                <span class="block text-xs font-bold text-[#0f172a]">Vendor</span>
                <span class="block text-[0.65rem] text-[#94a3b8]">Supply Assets</span>
              </label>
            </div>
            <p class="text-[0.65rem] text-[#94a3b8] mt-1.5">Note: Community verifier and admin accounts cannot be self-selected and are designated by platform administrators.</p>
          </div>

          <div>
            <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Your full name" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="name" autofocus>
          </div>

          <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="email">
          </div>

          <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">Password</label>
            <input id="password" name="password" type="password" placeholder="At least 8 characters" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="new-password">
          </div>

          <div>
            <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat your password" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="new-password">
          </div>

          <button type="submit" class="w-full py-3 rounded-xl bg-[#127A4A] text-white font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10 mt-2">
            Create Account
          </button>
        </form>

        <div class="mt-6 pt-6 border-t border-[#f1f5f9] text-center text-xs text-[#475569]">
          Already registered? <a href="{{ route('login') }}" class="text-[#127A4A] font-bold hover:underline">Sign in</a>
        </div>
      </div>

      <p class="text-center text-[0.7rem] text-[#94a3b8] mt-6">All funding is simulated and clearly labeled. No real payments are processed.</p>
    </div>
  </main>

  <footer class="border-t border-[#e2e8f0] bg-white py-6 text-center text-xs text-[#94a3b8]">
    JIBIKA — Productive Asset Access &amp; Livelihood Support Platform
  </footer>

</body>
</html>
