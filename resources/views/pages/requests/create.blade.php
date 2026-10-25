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

    <!-- AI Natural-Language Request Builder -->
    <div class="bg-white rounded-2xl shadow-card border border-[#f1f5f9] p-6 md:p-7 mb-8">
      <label class="block text-sm font-bold text-[#0f172a] mb-1" for="draft_text">Describe your need in your own words (বাংলা or English)</label>
      <textarea id="draft_text" rows="3" maxlength="800" placeholder="e.g. আমি কুমিল্লার একজন দর্জি, পুরাতন মেশিনে কাজ হয় না, একটা শিল্প সেলাই মেশিন দরকার..." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors mt-2"></textarea>
      <p class="text-xs text-[#64748b] mt-1.5">Do not enter phone, NID or bank numbers. AI suggestions are only a draft: check everything before saving.</p>
      <div class="mt-3 flex items-center gap-2">
        <button type="button" id="draft_btn" class="px-4 py-2 rounded-xl bg-[#127A4A] text-white text-xs font-semibold hover:bg-[#0e5a36] transition-colors shadow-sm">Suggest draft</button>
        <button type="button" id="voice_btn" class="hidden px-4 py-2 rounded-xl border border-[#e2e8f0] text-xs font-semibold text-[#0f172a] hover:bg-[#f8fafc] transition-colors">🎤 Speak (বাংলা)</button>
      </div>
      <p id="draft_msg" class="text-xs mt-2.5 font-medium"></p>
    </div>

    <form action="{{ route('requests.store') }}" method="POST" class="space-y-6">
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
                <option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }} — {{ number_format($cat->estimated_cost_bdt, 0) }} BDT</option>
              @endforeach
            </select>
            @error('category_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="requested_amount_bdt" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Estimated Cost (BDT)</label>
            <input id="requested_amount_bdt" type="number" name="requested_amount_bdt" value="{{ old('requested_amount_bdt') }}" placeholder="e.g. 35000" class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors" required min="1000" max="5000000" aria-required="true">
            @error('requested_amount_bdt') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
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
            <textarea id="reason" name="reason" rows="4" placeholder="Describe how this asset will help you generate income..." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y" required aria-required="true">{{ old('reason') }}</textarea>
            @error('reason') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label for="livelihood_justification" class="block text-sm font-semibold text-[#0f172a] mb-1.5">Expected Productivity Benefit</label>
            <textarea id="livelihood_justification" name="livelihood_justification" rows="4" placeholder="How will this improve your income? Provide specific expectations." class="w-full px-4 py-3 rounded-xl border border-[#e2e8f0] text-sm focus-ring bg-[#f8fafc] focus:bg-white transition-colors resize-y" required aria-required="true">{{ old('livelihood_justification') }}</textarea>
            @error('livelihood_justification') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
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

  <script>
  const csrf = "{{ csrf_token() }}";
  document.getElementById('draft_btn').onclick = async () => {
    const msg = document.getElementById('draft_msg');
    const text = document.getElementById('draft_text').value.trim();
    if (!text) {
      msg.className = 'text-xs mt-2.5 font-medium text-amber-600';
      msg.textContent = 'Please describe your equipment need first.';
      return;
    }

    const filled = ['reason','livelihood_justification','requested_amount_bdt']
      .some(id => document.getElementById(id)?.value);
    if (filled && !confirm('Replace what you already wrote with the AI draft?')) return;

    msg.className = 'text-xs mt-2.5 font-medium text-[#127A4A]';
    msg.textContent = 'Generating draft suggestion…';
    try {
      const r = await fetch("{{ route('requests.draft') }}", {
        method: 'POST',
        headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
        body: JSON.stringify({text: text})
      });
      if (!r.ok) throw new Error();
      const d = await r.json();
      const set = (id, v) => { const el = document.getElementById(id); if (el && v != null) el.value = v; };
      set('category_id', d.category_id);
      set('requested_amount_bdt', d.requested_amount_bdt);
      set('reason', d.reason);
      set('livelihood_justification', d.livelihood_justification);

      msg.className = 'text-xs mt-2.5 font-medium text-[#127A4A]';
      msg.textContent = (d.source === 'llm' ? 'AI-assisted draft generated. ' : 'Basic draft (AI unavailable). ')
        + 'Please review and edit. ' + (d.missing_info || []).join(' ');
    } catch (e) {
      msg.className = 'text-xs mt-2.5 font-medium text-[#E05D44]';
      msg.textContent = 'Could not draft automatically. You can fill the form fields manually below.';
    }
  };

  const SR = window.SpeechRecognition || window.webkitSpeechRecognition;
  if (SR) {
    const b = document.getElementById('voice_btn');
    b.classList.remove('hidden');
    b.onclick = () => {
      const s = new SR();
      s.lang = 'bn-BD';
      s.onresult = e => {
        document.getElementById('draft_text').value = e.results[0][0].transcript;
      };
      s.start();
    };
  }
  </script>
</x-layout>
