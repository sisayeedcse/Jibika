<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
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
<body class="bg-[#f8fafc] text-[#0f172a] antialiased min-h-screen flex flex-col">

  <nav class="bg-white/85 backdrop-blur-xl border-b border-[#e2e8f0] sticky top-0 z-50" aria-label="Primary navigation">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-[68px]">
        <a href="/" class="text-[1.35rem] font-extrabold tracking-tight text-[#127A4A]" aria-label="JIBIKA Home">JIBIKA</a>
        <div class="hidden md:flex items-center gap-7 text-[0.88rem] font-medium text-[#475569]">
          <a href="/" class="hover:text-[#127A4A] transition-colors">Home</a>
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">How It Works</a>
          <a href="{{ route('login') }}" class="hover:text-[#127A4A] transition-colors">Sign In</a>
        </div>
      </div>
    </div>
  </nav>

  <main class="flex-grow flex items-center justify-center px-6 py-12 md:py-16">
    <div class="w-full max-w-[460px]">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-8 md:p-10">
        <div class="mb-7">
          <h1 class="text-[1.6rem] font-extrabold tracking-tight mb-1">Create Account</h1>
          <p class="text-sm text-[#475569]">Join JIBIKA to request productive assets or participate in verification and funding.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
          @csrf

          <div>
            <label for="name" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Full name</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Your full name" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="name" autofocus>
          </div>

          <div>
            <label for="email" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Email address</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="email">
          </div>

          <div>
            <label for="password" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Password</label>
            <input id="password" name="password" type="password" placeholder="At least 8 characters" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="new-password">
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Repeat your password" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="new-password">
          </div>

          <button type="submit" class="w-full py-3 rounded-xl bg-[#127A4A] text-white font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">
            Create Account
          </button>
        </form>

        <div class="mt-6 pt-6 border-t border-[#f1f5f9] text-center text-sm text-[#475569]">
          Already registered? <a href="{{ route('login') }}" class="text-[#127A4A] font-semibold hover:underline">Sign in</a>
        </div>
      </div>

      <p class="text-center text-xs text-[#94a3b8] mt-6">This is a demonstration platform. All funding is simulated and clearly labeled.</p>
    </div>
  </main>

</body>
</html>
