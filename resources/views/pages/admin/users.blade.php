<x-layout title="Manage Users">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">User Management</h1>
    <p class="text-[#475569]">Review accounts, roles, and activation status. Status changes are applied immediately.</p>
  </div>

  <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse min-w-[640px]">
        <thead>
          <tr class="border-b border-[#f1f5f9] text-xs uppercase tracking-wider text-[#94a3b8] font-medium">
            <th class="px-6 py-3.5">Name</th>
            <th class="px-6 py-3.5">Email</th>
            <th class="px-6 py-3.5">Role</th>
            <th class="px-6 py-3.5">Status</th>
            <th class="px-6 py-3.5 text-right">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-[#f1f5f9]">
          @foreach($users as $u)
            <tr class="hover:bg-[#f8fafc]/60 transition-colors">
              <td class="px-6 py-4 text-sm font-semibold text-[#0f172a]">{{ $u->name }}</td>
              <td class="px-6 py-4 text-sm text-[#475569]">{{ $u->email }}</td>
              <td class="px-6 py-4 text-sm text-[#475569]">{{ ucfirst($u->role) }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[0.7rem] font-bold border
                  {{ $u->active ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200' }}">
                  {{ $u->active ? 'Active' : 'Suspended' }}
                </span>
              </td>
              <td class="px-6 py-4 text-right">
                <form action="{{ route('admin.users.status', $u) }}" method="POST" class="inline" aria-label="Update status for user {{ $u->name }}">
                  @csrf
                  <select name="status" class="text-xs px-3 py-1.5 rounded-lg border border-[#e2e8f0] bg-[#f8fafc] focus:ring-2 focus:ring-[#127A4A]/20 transition-colors" onchange="this.form.submit()" aria-label="Select status">
                    <option value="active" {{ $u->active ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ !$u->active ? 'selected' : '' }}>Suspended</option>
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
