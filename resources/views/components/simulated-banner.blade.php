@props(['compact' => false])

@if($compact)
<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold']) }}>
  <svg class="w-3.5 h-3.5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
  </svg>
  <span>Simulated Pledge &bull; No Real Payment Processed</span>
</div>
@else
<div {{ $attributes->merge(['class' => 'p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3']) }}>
  <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 font-bold text-sm">
    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
  </div>
  <div>
    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900 mb-0.5">Simulated Demonstration Environment</h4>
    <p class="text-xs text-amber-800 leading-relaxed">
      All pledges and capital figures in this platform are conducted under a simulated model for transparency and audit testing. No real banking or MFS transactions occur.
    </p>
  </div>
</div>
@endif
