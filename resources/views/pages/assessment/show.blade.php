<x-layout title="Assessment — Request #{{ $request->id }}">
  <div class="mb-8">
    <h1 class="text-[1.6rem] md:text-[2rem] font-extrabold tracking-tight mb-2">Assessment Outcome</h1>
    <p class="text-[#475569]">Request #{{ $request->id }} — {{ $request->category?->name ?? 'Uncategorized' }}</p>
  </div>

  <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8 mb-6" aria-label="Assessment metrics">
    <div class="grid sm:grid-cols-3 gap-4 mb-6">
      <div class="p-5 rounded-xl bg-[#f8fafc] border border-[#f1f5f9] text-center">
        <p class="text-[0.65rem] uppercase tracking-wider text-[#94a3b8] font-bold mb-2">Suitability</p>
        <p class="text-xl font-extrabold text-[#127A4A]">{{ $assessment->suitability_category ?? 'N/A' }}</p>
      </div>
      <div class="p-5 rounded-xl bg-[#f8fafc] border border-[#f1f5f9] text-center">
        <p class="text-[0.65rem] uppercase tracking-wider text-[#94a3b8] font-bold mb-2">Score</p>
        <p class="text-xl font-extrabold text-[#0f172a]">{{ $assessment->numerical_score ?? 'N/A' }}</p>
      </div>
      <div class="p-5 rounded-xl bg-[#f8fafc] border border-[#f1f5f9] text-center">
        <p class="text-[0.65rem] uppercase tracking-wider text-[#94a3b8] font-bold mb-2">Method</p>
        <p class="text-sm font-bold text-[#0f172a]">{{ $assessment->method_version ?? 'Unknown' }}</p>
      </div>
    </div>

    <h2 class="text-base font-extrabold tracking-tight mb-3">Explainable Reasoning</h2>
    <div class="p-5 rounded-xl bg-[#f8fafc] border border-[#f1f5f9] text-sm">
      @if($assessment->explanation_json)
        @foreach($assessment->explanation_json as $key => $value)
          <div class="flex justify-between items-start gap-4 mb-3 last:mb-0">
            <span class="font-semibold text-[#0f172a] shrink-0">{{ __(ucfirst(str_replace('_', ' ', $key))) }}</span>
            <span class="text-[#475569] text-right">{{ __($value) }}</span>
          </div>
        @endforeach
      @else
        <p class="text-[#475569]">No explanation available.</p>
      @endif
    </div>
  </section>

  <section class="bg-[#f8fafc] rounded-2xl border border-[#f1f5f9] p-7 md:p-8" aria-label="Provenance and limitations">
    <h2 class="text-base font-extrabold tracking-tight mb-4">Provenance &amp; Limitations</h2>
    <ul class="space-y-2 text-sm text-[#475569]">
      <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-[#127A4A] mt-1.5 shrink-0"></span><span><strong>Provider:</strong> {{ $assessment->provider_name ?? 'Development Fallback' }}</span></li>
      <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-[#127A4A] mt-1.5 shrink-0"></span><span><strong>Real AI Generated:</strong> @if($assessment->is_ai_generated) Yes @else <span class="font-bold text-amber-700">No — this is a rules-based fallback result</span> @endif</span></li>
      <li class="flex items-start gap-3"><span class="w-1.5 h-1.5 rounded-full bg-[#127A4A] mt-1.5 shrink-0"></span><span>This assessment explains suitability clearly but is not a legally binding decision. Admin review is required for workflow advancement.</span></li>
    </ul>
  </section>

  <div class="mt-6">
    <a href="{{ route('requests.show', $request) }}" class="inline-flex items-center text-sm text-[#127A4A] font-semibold hover:text-[#0e5a36] hover:underline">Back to Request</a>
  </div>
</x-layout>
