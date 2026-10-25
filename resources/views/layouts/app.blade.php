<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ $title ?? 'JIBIKA' }}</title>
<meta name="description" content="JIBIKA connects workers with productive assets through verified community assessment and transparent funding coordination.">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/design-system.css') }}">
<style>
  body { font-family: 'Inter','Noto Sans Bengali',system-ui,sans-serif; }
  .font-brand { font-family: 'Inter','Noto Sans Bengali',system-ui,sans-serif; }
  .text-brand-green { color: #127A4A; }
  .bg-brand-green { background-color: #127A4A; }
  .hover\:bg-brand-deep:hover { background-color: #0e5a36; }
  .bg-brand-coral-soft { background-color: #fbeae6; }
  .text-brand-coral { color: #E05D44; }
  .bg-surface { background-color: #f8fafc; }
  .text-primary { color: #0f172a; }
  .text-secondary { color: #475569; }
  .text-muted { color: #94a3b8; }
  .border-subtle { border-color: #e2e8f0; }
  .shadow-card { box-shadow: 0 1px 3px rgba(15,23,42,0.05), 0 1px 2px rgba(15,23,42,0.03); }
  .shadow-card-hover:hover { box-shadow: 0 10px 25px rgba(15,23,42,0.06), 0 4px 10px rgba(15,23,42,0.03); }
  .focus-ring:focus { outline: none; box-shadow: 0 0 0 2px rgba(18,122,74,0.18); border-radius: 0.75rem; }
  ::selection { background: rgba(18,122,74,0.12); }
</style>
@stack('styles')
</head>
<body class="bg-surface text-primary antialiased font-brand min-h-screen flex flex-col">

  <nav class="bg-white/85 backdrop-blur-xl border-b border-subtle sticky top-0 z-50" aria-label="Primary navigation">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-[68px]">
        <a href="{{ url('/') }}" class="text-[1.35rem] font-extrabold tracking-[0.02em] text-[#127A4A] leading-none" aria-label="JIBIKA Home">JIBIKA</a>
        <div class="hidden md:flex items-center gap-7 text-[0.88rem] font-medium text-secondary">
          <a href="{{ url('/') }}" class="hover:text-[#127A4A] transition-colors">{{ __('Home') }}</a>
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">{{ __('How It Works') }}</a>
          @auth
            <a href="{{ route('dashboard') }}" class="hover:text-[#127A4A] transition-colors">{{ __('Dashboard') }}</a>
            @if(auth()->user()?->isAdmin())
              <a href="{{ route('admin.dashboard') }}" class="hover:text-[#127A4A] transition-colors">{{ __('Admin') }}</a>
            @endif
            <a href="{{ route('notifications.index') }}" class="relative hover:text-[#127A4A] transition-colors" aria-label="Notifications">
              {{ __('Notifications') }}
              @php $unread = auth()->user()?->unreadNotifications?->count() ?? 0; @endphp
              @if($unread > 0)
                <span class="absolute -top-1 -right-2.5 inline-flex items-center justify-center w-[1.1rem] h-[1.1rem] text-[0.6rem] font-bold text-white bg-[#E05D44] rounded-full">{{ $unread }}</span>
              @else
                <span class="absolute -top-1 -right-2.5 w-2 h-2 bg-[#E05D44] rounded-full" aria-hidden="true"></span>
              @endif
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="hover:text-[#127A4A] transition-colors">{{ __('Sign Out') }}</a>
          @endauth
          @guest
            <a href="{{ route('login') }}" class="hover:text-[#127A4A] transition-colors">{{ __('Sign In') }}</a>
            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#127A4A] text-white text-[0.8rem] font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">{{ __('Register') }}</a>
          @endguest

          {{-- Language Switcher --}}
          <div class="inline-flex items-center rounded-lg border border-slate-200 p-0.5 text-xs font-semibold bg-slate-50 ml-2">
            <form action="{{ route('locale.switch', 'en') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="px-2 py-0.5 rounded {{ app()->getLocale() === 'en' ? 'bg-white text-[#127A4A] shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">EN</button>
            </form>
            <span class="text-slate-300">|</span>
            <form action="{{ route('locale.switch', 'bn') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="px-2 py-0.5 rounded {{ app()->getLocale() === 'bn' ? 'bg-white text-[#127A4A] shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">বাংলা</button>
            </form>
          </div>
        </div>
        <div class="md:hidden flex items-center gap-3">
          {{-- Mobile Language Switcher --}}
          <div class="inline-flex items-center rounded-lg border border-slate-200 p-0.5 text-xs font-semibold bg-slate-50">
            <form action="{{ route('locale.switch', 'en') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="px-1.5 py-0.5 rounded {{ app()->getLocale() === 'en' ? 'bg-white text-[#127A4A]' : 'text-slate-500' }}">EN</button>
            </form>
            <span class="text-slate-300">|</span>
            <form action="{{ route('locale.switch', 'bn') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="px-1.5 py-0.5 rounded {{ app()->getLocale() === 'bn' ? 'bg-white text-[#127A4A]' : 'text-slate-500' }}">বাং</button>
            </form>
          </div>
          <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-secondary hover:text-[#127A4A] p-2" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">☰</button>
        </div>
      </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden border-t border-subtle bg-white/95 backdrop-blur-xl">
      <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col gap-3 text-sm font-medium text-secondary">
        <a href="{{ url('/') }}" class="py-1.5 hover:text-[#127A4A]">{{ __('Home') }}</a>
        <a href="{{ route('how-it-works') }}" class="py-1.5 hover:text-[#127A4A]">{{ __('How It Works') }}</a>
        @auth
          <a href="{{ route('dashboard') }}" class="py-1.5 hover:text-[#127A4A]">{{ __('Dashboard') }}</a>
          @if(auth()->user()?->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="py-1.5 hover:text-[#127A4A]">{{ __('Admin') }}</a>
          @endif
          <a href="{{ route('notifications.index') }}" class="py-1.5 hover:text-[#127A4A]">{{ __('Notifications') }}</a>
          <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="py-1.5 hover:text-[#127A4A]">{{ __('Sign Out') }}</a>
        @endauth
        @guest
          <a href="{{ route('login') }}" class="py-1.5 hover:text-[#127A4A]">{{ __('Sign In') }}</a>
          <a href="{{ route('register') }}" class="py-1.5 text-[#127A4A] font-semibold">{{ __('Register') }}</a>
        @endguest
      </div>
    </div>
  </nav>

  <x-walkthrough-banner />

  <main class="max-w-6xl mx-auto px-6 lg:px-8 py-8 md:py-10 flex-grow">
    @if(session('success'))
      <div class="mb-6 p-4 rounded-xl bg-[#127A4A]/8 border border-[#127A4A]/15 text-[#127A4A] text-sm font-medium" role="status">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
      <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm font-medium" role="status">{{ session('warning') }}</div>
    @endif
    @if($errors->any())
      <div class="mb-6 p-4 rounded-xl bg-[#fbeae6] border border-[#E05D44]/20 text-[#E05D44] text-sm font-medium" role="alert">
        <ul class="list-disc list-inside space-y-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    {{ $slot }}
  </main>

  <footer class="border-t border-subtle bg-white" aria-label="Footer">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-8 md:py-10">
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div>
          <a href="{{ url('/') }}" class="text-xl font-extrabold tracking-[0.02em] text-[#127A4A]">JIBIKA</a>
          <p class="text-sm text-secondary mt-2 max-w-md leading-relaxed">A productive asset financing platform connecting verified livelihood needs with community-backed funding coordination.</p>
        </div>
        <div class="flex gap-6 text-sm text-secondary">
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">How It Works</a>
          <a href="{{ url('/') }}" class="hover:text-[#127A4A] transition-colors">Platform</a>
        </div>
      </div>
      <div class="mt-8 pt-6 border-t border-subtle flex flex-col md:flex-row md:items-center justify-between gap-2 text-xs text-muted">
        <span>JIBIKA — Productive Asset Financing Platform</span>
        <span>All simulated funding and AI assessment results are clearly labeled.</span>
      </div>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>
