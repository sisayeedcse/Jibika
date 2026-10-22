<x-layout title="New Asset Request">
  <div class="max-w-3xl">
    <div class="mb-8">
      <h1 class="text-[1.75rem] md:text-[2rem] font-extrabold tracking-tight mb-2">New Asset Request</h1>
      <p class="text-[#475569]">Describe the productive asset you need to grow your livelihood. All fields are required.</p>
    </div>

    <!-- Progress -->
    <div class="flex gap-2 mb-8" aria-label="Form progress">
      <div class="flex-1 h-1 rounded-full bg-[#127A4A]"></div>
      <div class="flex-1 h-1 rounded-full bg-[#f1f5f9]"></div>
      <div class="flex-1 h-1 rounded-full bg-[#f1f5f9]"></div>
    </div>

    <form action="{{ route('requests.store') }}" method="POST" class="space-y-6" novalidate>
      @csrf

      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8" aria-label="Step 1: Asset details">
        <div class="flex items-center gap-3 mb-6">
          <span class="w-9 h-9 rounded-xl bg-[#127A4A] text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-emerald-900/15">1</span>
          <h2 class="text-lg font-extrabold tracking-tight">Asset Details</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-6">
          <div>
            <label for="category_id" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Asset Category</label>
            <select id="category_id" name="category_id" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required>
              <option value="">Select a category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }} — {{ number_format($cat->estimated_cost_bdt, 0) }} BDT</option>
              @endforeach
            </select>
          </div>
          <div>
            <label for="requested_amount_bdt" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Estimated Cost (BDT)</label>
            <input id="requested_amount_bdt" type="number" name="requested_amount_bdt" placeholder="e.g. 35000" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required min="1" aria-required="true">
          </div>
        </div>
      </section>

      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8" aria-label="Step 2: Livelihood justification">
        <div class="flex items-center gap-3 mb-6">
          <span class="w-9 h-9 rounded-xl bg-[#127A4A] text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-emerald-900/15">2</span>
          <h2 class="text-lg font-extrabold tracking-tight">Livelihood Justification</h2>
        </div>
        <div class="space-y-5">
          <div>
            <label for="reason" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Why do you need this asset?</label>
            <textarea id="reason" name="reason" rows="4" placeholder="Describe how this asset will help you generate income..." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y" required aria-required="true"></textarea>
          </div>
          <div>
            <label for="livelihood_justification" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Expected Productivity Benefit</label>
            <textarea id="livelihood_justification" name="livelihood_justification" rows="4" placeholder="How will this improve your income? Provide specific expectations." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y" required aria-required="true"></textarea>
          </div>
        </div>
      </section>

      <section class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-7 md:p-8" aria-label="Step 3: Review and submit">
        <div class="flex items-center gap-3 mb-6">
          <span class="w-9 h-9 rounded-xl bg-[#127A4A] text-white flex items-center justify-center font-extrabold text-sm shadow-md shadow-emerald-900/15">3</span>
          <h2 class="text-lg font-extrabold tracking-tight">Review</h2>
        </div>
        <p class="text-sm text-[#475569] mb-6">Before submitting, confirm that your details are accurate. Once submitted, the request enters verification.</p>
        <div class="flex items-center justify-between gap-4">
          <a href="{{ route('requests.index') }}" class="inline-flex items-center px-5 py-2.5 rounded-xl border border-[#e2e8f0] text-sm font-medium text-[#475569] hover:bg-[#f8fafc] transition-colors">Cancel</a>
          <button type="submit" class="inline-flex items-center px-8 py-3 rounded-xl bg-[#127A4A] text-white text-sm font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Save as Draft</button>
        </div>
      </section>
    </form>
  </div>
</x-layout>
