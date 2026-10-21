<x-layout title="New Asset Request">
  <div class="max-w-3xl">
    <div class="mb-8">
      <h1 class="text-3xl font-extrabold text-slate-dark mb-2">New Asset Request</h1>
      <p class="text-slate-500">Describe the productive asset you need to grow your livelihood.</p>
    </div>

    <form action="{{ route('requests.store') }}" method="POST" class="space-y-6">
      @csrf

      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <h2 class="text-xl font-bold mb-6">1. Asset Details</h2>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Asset Category</label>
            <select name="category_id" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring">
              <option value="">Select category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }} — {{ number_format($cat->estimated_cost_bdt, 0) }} BDT</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Estimated Cost (BDT)</label>
            <input type="number" name="requested_amount_bdt" placeholder="e.g. 35000" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100">
        <h2 class="text-xl font-bold mb-6">2. Livelihood Justification</h2>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Why do you need this asset?</label>
            <textarea name="reason" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required placeholder="Describe how this asset will help you generate income..."></textarea>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Expected Productivity Benefit</label>
            <textarea name="livelihood_justification" rows="4" class="w-full px-4 py-3 rounded-xl border border-slate-200 focus-ring" required placeholder="How will this improve your income?"></textarea>
          </div>
        </div>
      </div>

      <div class="flex justify-end">
        <button type="submit" class="px-8 py-3.5 rounded-xl bg-brand-green text-white font-semibold hover:bg-[#0e5a36] shadow-md shadow-emerald-900/10 transition">
          Save as Draft
        </button>
      </div>
    </form>
  </div>
</x-layout>
