<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>403 — Access Forbidden — JIBIKA</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Inter',system-ui,sans-serif; }</style>
</head>
<body class="bg-[#f8fafc] text-[#0f172a] antialiased min-h-screen flex flex-col items-center justify-center p-6">
  <div class="max-w-md w-full text-center">
    <a href="/" class="inline-block text-xl font-extrabold tracking-tight text-[#127A4A] mb-8">JIBIKA</a>

    <div class="w-16 h-16 rounded-2xl bg-[#fff7ed] flex items-center justify-center mx-auto mb-6" aria-hidden="true">
      <svg class="w-8 h-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
      </svg>
    </div>

    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 mb-3">403 Forbidden</span>
    <h1 class="text-2xl font-extrabold tracking-tight mb-3">Access Denied</h1>
    <p class="text-[#475569] mb-6 text-sm leading-relaxed">{{ $exception->getMessage() ?: 'You do not have permission to access this resource or perform this action with your current role.' }}</p>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ auth()->check() ? route('dashboard') : url('/') }}"
         class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">
        {{ auth()->check() ? 'Back to Dashboard' : 'Back to Home' }}
      </a>
      @if(auth()->check())
        <a href="{{ route('profile.show') }}"
           class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-[#e2e8f0] text-[#475569] text-sm font-medium hover:bg-[#f1f5f9] transition-colors">
          View Profile
        </a>
      @endif
    </div>
  </div>
</body>
</html>
