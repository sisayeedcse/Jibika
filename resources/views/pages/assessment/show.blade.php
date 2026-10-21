<x-layout title="AI Assessment">
  <div class="mb-6">
    <h1 class="text-3xl font-extrabold text-slate-dark mb-2">AI-Assisted Assessment</h1>
    <p class="text-slate-500">Request #{{ $request->id }} — {{ $request->category?->name }}</p>
  </div>

  <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
    <div class="grid md:grid-cols-3 gap-6 mb-6">
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
        <div class="text-xs text-slate-500 uppercase tracking-wider">Suitability</div>
        <div class="text-2xl font-bold text-[#127A4A]">{{ $assessment->suitability_category }}</div>
      </div>
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
        <div class="text-xs text-slate-500 uppercase tracking-wider">Score</div>
        <div class="text-2xl font-bold">{{ $assessment->numerical_score ?? 'N/A' }}</div>
      </div>
      <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-center">
        <div class="text-xs text-slate-500 uppercase tracking-wider">Version</div>
        <div class="text-sm font-medium">{{ $assessment->method_version }}</div>
      </div>
    </div>

    <div class="space-y-4">
      <div>
        <p class="text-sm font-medium text-slate-700 mb-2">Explainable Reasoning</p>
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-2">
          @foreach($assessment->explanation_json as $key => $value)
            <div class="flex justify-between items-start">
              <span class="text-sm text-slate-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
              <span class="text-sm text-slate-700">{{ $value }}</span>
            </div>
          @endforeach
        </div>
      </div>

      <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
        <p class="text-sm text-amber-700 font-medium mb-1">Provenance &amp; Limitations</p>
        <ul class="text-xs text-amber-600 list-disc list-inside space-y-1">
          <li><strong>Provider:</strong> {{ $assessment->provider_name ?? 'Development Fallback' }}</li>
          <li><strong>Real AI:</strong> @if($assessment->is_ai_generated) Yes @else No — this is NOT an LLM response @endif</li>
          <li>This assessment is explainable and rules-based, not a legally binding decision.</li>
          <li>Admin review is required before any workflow advancement.</li>
        </ul>
      </div>
    </div>

    <div class="mt-6">
      <a href="{{ route('requests.show', $request) }}" class="text-[#127A4A] font-medium hover:underline">Back to Request</a>
    </div>
  </div>
</x-layout>
