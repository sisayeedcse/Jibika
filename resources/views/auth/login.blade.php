<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Sign in to JIBIKA — your productive asset financing platform.">
<title>Sign In — JIBIKA</title>
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
        <div class="flex items-center gap-5 text-[0.88rem] font-medium text-[#475569]">
          <a href="/" class="hover:text-[#127A4A] transition-colors">{{ __('Home') }}</a>
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">{{ __('How It Works') }}</a>
          <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#127A4A] text-white text-[0.8rem] font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">{{ __('Register') }}</a>
          <div class="inline-flex items-center rounded-lg border border-slate-200 p-0.5 text-xs font-semibold bg-slate-50">
            <form action="{{ route('locale.switch', 'en') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="px-1.5 py-0.5 rounded {{ app()->getLocale() === 'en' ? 'bg-white text-[#127A4A]' : 'text-slate-500' }}">EN</button>
            </form>
            <span class="text-slate-300">|</span>
            <form action="{{ route('locale.switch', 'bn') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="px-1.5 py-0.5 rounded {{ app()->getLocale() === 'bn' ? 'bg-white text-[#127A4A]' : 'text-slate-500' }}">বাংলা</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <main class="flex-grow flex items-center justify-center px-6 py-12 md:py-16">
    <div class="w-full max-w-[420px]">
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-8 md:p-10">
        <div class="mb-7">
          <h1 class="text-[1.6rem] font-extrabold tracking-tight mb-1 text-[#0f172a]">{{ __('Sign In') }}</h1>
          <p class="text-sm text-[#475569]">{{ __('Access your role workspace and request progress.') }}</p>
        </div>

        @if(session('success'))
          <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-[#127A4A]">
            {{ session('success') }}
          </div>
        @endif

        @if($errors->any())
          <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs font-medium text-[#E05D44]">
            <ul class="list-disc list-inside space-y-1">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4" novalidate>
          @csrf

          <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">{{ __('Email address') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="you@example.com" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="email" autofocus>
          </div>

          <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-[#94a3b8] mb-1.5">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" placeholder="Enter your password" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required autocomplete="current-password">
          </div>

          <div class="flex items-center justify-between text-xs pt-1">
            <label class="flex items-center gap-2 text-[#475569] cursor-pointer select-none">
              <input type="checkbox" name="remember" class="w-4 h-4 rounded border-[#e2e8f0] text-[#127A4A] focus:ring-[#127A4A]/20" {{ old('remember') ? 'checked' : '' }}>
              {{ __('Remember me') }}
            </label>
          </div>

          <button type="submit" class="w-full py-3 rounded-xl bg-[#127A4A] text-white font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10 mt-2">
            {{ __('Sign In') }}
          </button>
        </form>

        <div class="mt-6 pt-6 border-t border-[#f1f5f9] text-center text-xs text-[#475569]">
          {{ __('New to JIBIKA?') }} <a href="{{ route('register') }}" class="text-[#127A4A] font-bold hover:underline">{{ __('Create an account') }}</a>
        </div>

        @if(config('app.demo_mode'))
          <div class="mt-6 pt-6 border-t border-[#f1f5f9]">
            <div class="flex items-center justify-between mb-3">
              <span class="text-xs font-bold uppercase tracking-wider text-[#94a3b8]">{{ __('Quick Demo Personas') }}</span>
              <span class="text-[0.65rem] font-mono px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold">DEMO MODE ACTIVE</span>
            </div>
            <p class="text-xs text-[#64748b] mb-3">Authenticate directly as a seeded simulation persona (CSRF &amp; rate-limit protected):</p>
            <div class="grid grid-cols-2 gap-2">
              @foreach([
                'worker'      => 'Worker (Rahim)',
                'community'   => 'Verifier (Anisur)',
                'contributor' => 'Sponsor (Dr. Tariqul)',
                'vendor'      => 'Vendor (Bengal Machinery)',
                'admin'       => 'Admin Console'
              ] as $roleKey => $roleLabel)
                <form action="{{ route('demo.login', $roleKey) }}" method="POST" class="{{ $loop->last ? 'col-span-2' : '' }}">
                  @csrf
                  <button type="submit" class="w-full text-left p-2.5 rounded-xl border border-[#e2e8f0] hover:border-[#127A4A] hover:bg-[#127A4A]/5 transition-all text-xs font-semibold text-[#0f172a] hover:text-[#127A4A]">
                    {{ $roleLabel }} &rarr;
                  </button>
                </form>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      <p class="text-center text-[0.7rem] text-[#94a3b8] mt-6">{{ __('All funding is simulated and clearly labeled. No real payments are processed.') }}</p>
    </div>
  </main>

  <footer class="border-t border-[#e2e8f0] bg-white py-6 text-center text-xs text-[#94a3b8]">
    JIBIKA — Productive Asset Access &amp; Livelihood Support Platform
  </footer>

</body>
</html>
