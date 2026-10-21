<x-layout title="Manage Users">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">User Management</h1>
    <p class="text-slate-500">View and manage platform accounts.</p>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 overflow-x-auto">
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="border-b border-slate-200 text-sm text-slate-500">
          <th class="pb-4 font-medium">Name</th>
          <th class="pb-4 font-medium">Email</th>
          <th class="pb-4 font-medium">Role</th>
          <th class="pb-4 font-medium">Status</th>
          <th class="pb-4 font-medium text-right">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        @foreach($users as $u)
          <tr class="hover:bg-slate-50/50 transition">
            <td class="pb-4 text-sm font-medium text-slate-dark">{{ $u->name }}</td>
            <td class="pb-4 text-sm">{{ $u->email }}</td>
            <td class="pb-4 text-sm">{{ ucfirst($u->role) }}</td>
            <td class="pb-4">
              <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium 
                {{ $u->active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                {{ ucfirst($u->status) }}
              </span>
            </td>
            <td class="pb-4 text-right">
              <form action="{{ route('admin.users.status', $u) }}" method="POST" class="inline">
                @csrf
                <select name="status" class="text-xs px-2 py-1 rounded border border-slate-200 focus:ring-2 focus:ring-[#127A4A]/20" onchange="this.form.submit()">
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
  {{ $users->links() }}
</x-layout>
