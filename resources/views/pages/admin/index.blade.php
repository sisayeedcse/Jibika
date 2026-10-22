<x-layout title="Admin Dashboard">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Administration</h1>
    <p class="text-[#475569]">Oversee users, requests, audit logs, and platform operations.</p>
  </div>

  <div class="grid md:grid-cols-3 gap-5 mb-8">
    @foreach($stats as $key => $value)
      <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6">
        <div class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
        <div class="text-3xl font-extrabold text-[#0f172a]">{{ number_format($value) }}</div>
      </div>
    @endforeach
  </div>

  <div class="grid lg:grid-cols-2 gap-6">
    <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7" aria-label="Quick links">
      <h2 class="text-base font-extrabold tracking-tight mb-5">Quick Links</h2>
      <div class="space-y-3">
        <a href="{{ route('admin.users') }}" class="flex items-center justify-between px-4 py-3.5 rounded-xl border border-[#e2e8f0] text-[#475569] hover:bg-[#f8fafc] hover:border-[#127A4A]/30 transition-colors font-medium">
          <span>Manage Users</span>
          <span class="text-[#94a3b8]">→</span>
        </a>
        <a href="{{ route('admin.requests') }}" class="flex items-center justify-between px-4 py-3.5 rounded-xl border border-[#e2e8f0] text-[#475569] hover:bg-[#f8fafc] hover:border-[#127A4A]/30 transition-colors font-medium">
          <span>Review Requests</span>
          <span class="text-[#94a3b8]">→</span>
        </a>
        <a href="{{ route('admin.audit') }}" class="flex items-center justify-between px-4 py-3.5 rounded-xl border border-[#e2e8f0] text-[#475569] hover:bg-[#f8fafc] hover:border-[#127A4A]/30 transition-colors font-medium">
          <span>Audit Log</span>
          <span class="text-[#94a3b8]">→</span>
        </a>
      </div>
    </section>
  </div>
</x-layout>
