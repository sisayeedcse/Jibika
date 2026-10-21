<x-layout title="Admin Dashboard">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Administration</h1>
    <p class="text-slate-500">Manage platform users, requests, and operations.</p>
  </div>

  <div class="grid md:grid-cols-3 gap-6 mb-8">
    @foreach($stats as $key => $value)
      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <div class="text-slate-400 text-sm font-medium uppercase tracking-wider mb-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
        <div class="text-3xl font-bold">{{ number_format($value) }}</div>
      </div>
    @endforeach
  </div>

  <div class="grid lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
      <h2 class="text-xl font-bold mb-4">Quick Links</h2>
      <div class="space-y-3">
        <a href="{{ route('admin.users') }}" class="block px-4 py-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition">Manage Users</a>
        <a href="{{ route('admin.requests') }}" class="block px-4 py-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition">Review Requests</a>
        <a href="{{ route('admin.audit') }}" class="block px-4 py-3 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 transition">Audit Log</a>
      </div>
    </div>
  </div>
</x-layout>
