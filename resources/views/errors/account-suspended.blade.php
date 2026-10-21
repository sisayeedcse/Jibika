<x-layout title="Account Suspended">
  <div class="max-w-md mx-auto py-20 text-center">
    <div class="w-20 h-20 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-6 text-3xl text-rose-600">⚠</div>
    <h1 class="text-3xl font-extrabold text-slate-dark mb-4">Account Suspended</h1>
    <p class="text-slate-500 mb-8">Your account has been suspended by an administrator. Please contact support for assistance.</p>
    <a href="{{ route('logout') }}" class="px-6 py-3 rounded-xl bg-slate-100 text-slate-700 font-medium hover:bg-slate-200 transition">Sign Out</a>
  </div>
</x-layout>
