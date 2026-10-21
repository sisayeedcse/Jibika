<x-layout title="Notifications">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">Notifications</h1>
    <p class="text-slate-500">Stay updated on your requests and activities.</p>
  </div>

  <div class="space-y-3">
    @foreach($notifications as $n)
      <div class="bg-white rounded-3xl p-4 shadow-sm border border-slate-100 {{ !$n->read_at ? 'bg-[#127A4A]/5 border-[#127A4A]/20' : '' }} flex justify-between items-start group">
        <div class="flex-1">
          <div class="font-bold text-slate-dark">{{ $n->title }}</div>
          <div class="text-sm text-slate-500 mt-1">{{ $n->body_en }}</div>
          <div class="text-xs text-slate-400 mt-2">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        @if(!$n->read_at)
          <form action="{{ route('notifications.read', $n) }}" method="POST" class="ml-4">
            @csrf
            <button type="submit" class="text-xs text-[#127A4A] font-medium hover:underline">Mark Read</button>
          </form>
        @endif
      </div>
    @endforeach
  </div>
</x-layout>
