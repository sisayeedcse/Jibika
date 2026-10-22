<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Account Suspended — JIBIKA</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>body { font-family: 'Inter',system-ui,sans-serif; }</style>
</head>
<body class="bg-[#f8fafc] text-[#0f172a] antialiased min-h-screen flex flex-col items-center justify-center p-6">
  <div class="max-w-md w-full text-center">
    <a href="/" class="inline-block text-xl font-extrabold tracking-tight text-[#127A4A] mb-10">JIBIKA</a>

    <div class="w-16 h-16 rounded-2xl bg-[#fef2f2] flex items-center justify-center mx-auto mb-6" aria-hidden="true">
      <svg class="w-8 h-8 text-[#E05D44]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
      </svg>
    </div>

    <h1 class="text-2xl font-extrabold tracking-tight mb-3">Account Suspended</h1>
    <p class="text-[#475569] mb-2">Your account access has been suspended by an administrator.</p>
    <p class="text-sm text-[#94a3b8] mb-8">Please reach out through your organization's support channel or contact the platform administrator for clarification.</p>

    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ url('/') }}"
         class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#127A4A] text-white font-medium hover:bg-[#0e5a36] transition-colors">
        Back to Home
      </a>
      <form action="{{ route('logout') }}" method="POST" class="inline">
        @csrf
        <button type="submit"
                class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-[#e2e8f0] text-[#475569] font-medium hover:bg-[#f1f5f9] transition-colors">
          Sign Out
        </button>
      </form>
    </div>
  </div>
</body>
</html>
