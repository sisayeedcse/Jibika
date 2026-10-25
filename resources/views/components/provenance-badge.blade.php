@props(['record' => null])

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold']) }}>
  <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse" aria-hidden="true"></span>
  <span>Rule-Based Engine (v1.0-rules)</span>
  <span class="text-emerald-400">&bull;</span>
  <span class="font-bold text-emerald-900">NOT AN LLM RESPONSE</span>
</div>
