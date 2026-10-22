<x-layout title="Account Suspended">
  <div class="max-w-md mx-auto py-16 md:py-24 text-center">
    <div class="w-16 h-16 rounded-2xl bg-[#fbeae6] flex items-center justify-center mx-auto mb-6 text-2xl text-[#E05D44]" aria-hidden="true">⚠</div>
    <h1 class="text-[1.75rem] font-extrabold tracking-tight mb-3">Account Suspended</h1>
    <p class="text-[#475569] mb-2">Your account access has been suspended by an administrator.</p>
    <p class="text-sm text-[#94a3b8] mb-8">Please reach out through your organization's support channel or contact the platform administrator for clarification.</p>
    <div class="flex flex-col sm:flex-row gap-3 justify-center">
      <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-[#127A4A] text-white font-medium hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Back to Home</a>
      <form action="{{ route('logout') }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-[#e2e8f0] text-[#475569] font-medium hover:bg-[#f8fafc] transition-colors">Sign Out</button>
      </form>
    </div>
  </div>
</x-layout>
