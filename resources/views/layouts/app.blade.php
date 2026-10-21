<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'JIBIKA' }}</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Inter','Noto Sans Bengali',sans-serif; }
  .brand-green { color: #127A4A; }
  .bg-brand-green { background-color: #127A4A; }
  .border-brand-green { border-color: #127A4A; }
  .bg-brand-warm { background-color: #E05D44; }
  .text-brand-warm { color: #E05D44; }
  .bg-slate-bg { background-color: #f8fafc; }
  .text-slate-dark { color: #0f172a; }
  .focus-ring { @apply focus:outline-none focus:ring-2 focus:ring-[#127A4A]/20 rounded-xl; }
</style>
@stack('styles')
</head>
<body class="bg-slate-bg text-slate-dark antialiased">
  <nav class="bg-white/80 backdrop-blur border-b border-slate-100 sticky top-0 z-50">
    <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
      <a href="/" class="text-xl font-extrabold tracking-tight text-[#127A4A]">JIBIKA</a>
      <div class="flex items-center gap-4">
        <div class="hidden md:flex gap-6 text-sm font-medium text-slate-600">
          <a href="/" class="hover:text-[#127A4A]">Home</a>
          <a href="/how-it-works" class="hover:text-[#127A4A]">How It Works</a>
          @auth
            <a href="{{ route('dashboard') }}" class="hover:text-[#127A4A]">Dashboard</a>
            @if(auth()->user()->isAdmin())
              <a href="{{ route('admin.dashboard') }}" class="hover:text-[#127A4A]">Admin</a>
            @endif
            <a href="{{ route('notifications.index') }}" class="relative hover:text-[#127A4A]">
              Notifications
              <span class="absolute top-1 right-1 w-2 h-2 bg-[#E05D44] rounded-full"></span>
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="hover:text-[#127A4A]">Sign Out</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
          @endauth
          @guest
            <a href="{{ route('login') }}" class="hover:text-[#127A4A]">Sign In</a>
            <a href="{{ route('register') }}" class="px-4 py-2 rounded-xl bg-[#127A4A] text-white hover:bg-[#0e5a36] transition">Register</a>
          @endguest
        </div>
        <div class="md:hidden">
          <button class="text-slate-600">☰</button>
        </div>
      </div>
    </div>
  </nav>

  <main class="max-w-6xl mx-auto px-6 py-8">
    @if(session('success'))
      <div class="mb-6 p-4 rounded-xl bg-[#127A4A]/10 border border-[#127A4A]/20 text-[#127A4A] text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="mb-6 p-4 rounded-xl bg-[#E05D44]/10 border border-[#E05D44]/20 text-[#E05D44] text-sm font-medium">
        <ul class="list-disc list-inside space-y-1">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    {{ $slot }}
  </main>

  <footer class="border-t border-slate-100 bg-white py-6 mt-12">
    <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-500">
      <div>JIBIKA — Productive Asset Financing Platform</div>
      <div class="flex gap-4 text-xs uppercase tracking-wider">
        <span>Built for YUNet AIgnite Buildathon 2026</span>
        <a href="/how-it-works" class="hover:text-[#127A4A]">How It Works</a>
      </div>
    </div>
  </footer>

  @stack('scripts')
</body>
</html>
