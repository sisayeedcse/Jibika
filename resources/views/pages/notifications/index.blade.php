<x-layout title="Notifications">
  <div class="mb-8">
    <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Notifications</h1>
    <p class="text-[#475569]">Stay updated on request progress, verification decisions, and funding updates.</p>
  </div>

  @if($notifications && $notifications->count())
    <div class="space-y-3">
      @foreach($notifications as $n)
        <article class="bg-white rounded-2xl p-6 shadow-card border border-[#f1f5f9] {{ !$n->read_at ? 'bg-[#f8fafc]' : '' }} transition-shadow hover:shadow-card-hover">
          <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
              <h3 class="font-extrabold text-[#0f172a] mb-1">{{ $n->title }}</h3>
              <p class="text-sm text-[#475569] leading-relaxed">{{ $n->body_en }}</p>
              <p class="text-xs text-[#94a3b8] mt-3">{{ $n->created_at->diffForHumans() }}</p>
            </div>
            @if(!$n->read_at)
              <form action="{{ route('notifications.read', $n) }}" method="POST" aria-label="Mark notification as read">
                @csrf
                <button type="submit" class="text-xs font-semibold text-[#127A4A] hover:text-[#0e5a36] hover:underline whitespace-nowrap">Mark as Read</button>
              </form>
            @endif
          </div>
        </article>
      @endforeach
    </div>
  @else
    <div class="bg-white rounded-2xl p-12 md:p-14 text-center shadow-card border border-[#f1f5f9]">
      <h3 class="text-base font-extrabold text-[#0f172a] mb-2">No notifications</h3>
      <p class="text-sm text-[#475569]">You'll see updates here when there is activity on your requests or account.</p>
    </div>
  @endif
</x-layout>
