<x-layout title="User Directory &amp; Status — JIBIKA">
  <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">User Access Directory</h1>
      <p class="text-sm text-[#475569]">Manage platform participants, roles, and account operational status.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="text-xs font-semibold text-[#127A4A] hover:underline">&larr; Back to Admin Panel</a>
  </div>

  <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden mb-6">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[700px]">
        <thead>
          <tr class="border-b border-[#f1f5f9] text-[0.7rem] uppercase tracking-wider text-[#94a3b8] font-bold bg-[#f8fafc]/50">
            <th class="px-6 py-4">Name &amp; ID</th>
            <th class="px-6 py-4">Email Address</th>
            <th class="px-6 py-4">Role</th>
            <th class="px-6 py-4">Status</th>
            <th class="px-6 py-4">Registered</th>
            <th class="px-6 py-4 text-right">Access Control</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#f1f5f9] text-xs">
          @foreach($users as $u)
            <tr class="hover:bg-[#f8fafc]/60 transition-colors">
              <td class="px-6 py-4">
                <div class="font-bold text-[#0f172a]">{{ $u->name }}</div>
                <div class="text-[0.65rem] font-mono text-[#94a3b8]">UID #{{ $u->id }}</div>
              </td>
              <td class="px-6 py-4 font-mono text-[#475569]">{{ $u->email }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider
                  {{ $u->isAdmin() ? 'bg-purple-50 text-purple-700 border border-purple-200' :
                    ($u->isCommunity() ? 'bg-coral-50 text-[#E05D44] border border-[#E05D44]/20' :
                    ($u->isVendor() ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-emerald-50 text-[#127A4A] border border-emerald-200')) }}">
                  {{ $u->role }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[0.65rem] font-bold uppercase tracking-wider border
                  {{ $u->isActive() ? 'bg-emerald-50 text-[#127A4A] border-emerald-200' : 'bg-red-50 text-[#E05D44] border-red-200' }}">
                  <span class="w-1.5 h-1.5 rounded-full {{ $u->isActive() ? 'bg-[#127A4A]' : 'bg-[#E05D44]' }}"></span>
                  {{ ucfirst($u->status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-[#94a3b8]">
                {{ $u->created_at?->format('M d, Y') ?? 'N/A' }}
              </td>
              <td class="px-6 py-4 text-right">
                <form action="{{ route('admin.users.status', $u) }}" method="POST" class="inline" aria-label="Update status for user {{ $u->name }}">
                  @csrf
                  <select name="status" class="text-xs px-3 py-1.5 rounded-xl border border-[#e2e8f0] bg-[#f8fafc] focus:ring-2 focus:ring-[#127A4A]/20 transition-colors" onchange="this.form.submit()" aria-label="Select status">
                    <option value="active" {{ $u->isActive() ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ !$u->isActive() ? 'selected' : '' }}>Suspended</option>
                  </select>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="px-6 py-4 border-t border-[#f1f5f9] bg-[#f8fafc]/40">
      {{ $users->links() }}
    </div>
  </div>
</x-layout>
