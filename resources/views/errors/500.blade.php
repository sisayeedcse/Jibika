<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>500 — Server Error — JIBIKA</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Inter',system-ui,sans-serif; }</style>
</head>
<body class="bg-[#f8fafc] text-[#0f172a] antialiased min-h-screen flex flex-col items-center justify-center p-6">
  <div class="max-w-md w-full text-center">
    <a href="/" class="inline-block text-xl font-extrabold tracking-tight text-[#127A4A] mb-8">JIBIKA</a>

    <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-6 text-[#E05D44]" aria-hidden="true">
      <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
      </svg>
    </div>

    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-red-50 text-[#E05D44] border border-red-200 mb-3">500 Server Error</span>
    <h1 class="text-2xl font-extrabold tracking-tight mb-3">Something Went Wrong</h1>
    <p class="text-[#475569] mb-6 text-sm leading-relaxed">An unexpected error occurred while processing your request. The issue has been logged for administrative review.</p>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ auth()->check() ? route('dashboard') : url('/') }}"
         class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">
        {{ auth()->check() ? 'Back to Dashboard' : 'Back to Home' }}
      </a>
      <a href="{{ url('/') }}"
         class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-[#e2e8f0] text-[#475569] text-sm font-medium hover:bg-[#f1f5f9] transition-colors">
        Home
      </a>
    </div>
  </div>
</body>
</html>
