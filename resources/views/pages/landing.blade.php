<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="JIBIKA connects verified workers with productive assets through community verification, explainable assessment, and transparent funding.">
<title>JIBIKA — Productive Asset Financing</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Inter','Noto Sans Bengali',system-ui,sans-serif; }
  .text-brand-green { color: #127A4A; }
  .bg-brand-green { background-color: #127A4A; }
  .hover\:bg-brand-deep:hover { background-color: #0e5a36; }
  ::selection { background: rgba(18,122,74,0.12); }
</style>
</head>
<body class="bg-[#f8fafc] text-[#0f172a] antialiased min-h-screen flex flex-col">

  <!-- Navigation -->
  <nav class="bg-white/85 backdrop-blur-xl border-b border-[#e2e8f0] sticky top-0 z-50" aria-label="Primary navigation">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-[68px]">
        <a href="/" class="text-[1.35rem] font-extrabold tracking-tight text-[#127A4A]" aria-label="JIBIKA Home">JIBIKA</a>
        <div class="hidden md:flex items-center gap-7 text-[0.88rem] font-medium text-[#475569]">
          <a href="/" class="hover:text-[#127A4A] transition-colors">Home</a>
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">How It Works</a>
          <a href="{{ route('register') }}" class="hover:text-[#127A4A] transition-colors">Register</a>
          <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-[#127A4A] text-white text-[0.8rem] font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Sign In</a>
        </div>
        <div class="md:hidden">
          <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-[#475569] hover:text-[#127A4A] p-2" aria-label="Toggle menu" aria-expanded="false" aria-controls="mobile-menu">☰</button>
        </div>
      </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden border-t border-[#e2e8f0] bg-white/95 backdrop-blur-xl">
      <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col gap-3 text-sm font-medium text-[#475569]">
        <a href="/" class="py-1.5 hover:text-[#127A4A]">Home</a>
        <a href="{{ route('how-it-works') }}" class="py-1.5 hover:text-[#127A4A]">How It Works</a>
        <a href="{{ route('register') }}" class="py-1.5 hover:text-[#127A4A]">Register</a>
        <a href="{{ route('login') }}" class="py-1.5 text-[#127A4A] font-semibold">Sign In</a>
      </div>
    </div>
  </nav>

  <main class="flex-grow">
    <!-- Hero -->
    <section class="max-w-6xl mx-auto px-6 lg:px-8 pt-16 md:pt-24 pb-16 md:pb-20">
      <div class="grid md:grid-cols-2 gap-12 lg:gap-16 items-center">
        <div>
          <h1 class="text-[2.6rem] md:text-[3.2rem] lg:text-[3.6rem] font-extrabold leading-[1.1] tracking-tight mb-6">
            <span class="text-[#127A4A]">From Small Capital</span><br>to Productive Livelihood
          </h1>
          <p class="text-lg md:text-xl text-[#475569] mb-8 leading-relaxed max-w-lg">
            JIBIKA connects verified workers with productive assets through community verification, explainable assessment, and transparent funding coordination.
          </p>
          <div class="flex gap-4 flex-wrap">
            <a href="{{ route('register') }}" class="inline-flex items-center px-7 py-3.5 rounded-xl bg-[#127A4A] text-white font-semibold shadow-lg shadow-emerald-900/10 hover:bg-[#0e5a36] hover:-translate-y-0.5 transition-all">Get Started</a>
            <a href="{{ route('how-it-works') }}" class="inline-flex items-center px-7 py-3.5 rounded-xl bg-white border border-[#e2e8f0] text-[#475569] font-semibold hover:border-[#127A4A] hover:text-[#127A4A] transition-all">Learn More</a>
          </div>
          <p class="mt-5 text-xs text-[#94a3b8]">Simulated funding and AI assessment results are clearly labeled. No real payments are processed.</p>
        </div>
        <div class="rounded-[1.5rem] overflow-hidden shadow-2xl shadow-slate-900/5 ring-1 ring-slate-900/5">
          <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?q=80&w=1600&auto=format&fit=crop" alt="Workshop with productive tools" class="w-full h-[26rem] object-cover" loading="eager">
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-16 md:py-20" aria-label="How it works">
      <div class="text-center mb-14">
        <h2 class="text-[1.75rem] md:text-[2.25rem] font-extrabold tracking-tight mb-3">How the Lifecycle Works</h2>
        <p class="text-[#475569] max-w-xl mx-auto">Every step is transparent, verified, and explainable. From request to post-delivery monitoring.</p>
      </div>
      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <article class="bg-white rounded-2xl p-8 shadow-card border border-[#f1f5f9] hover:shadow-card-hover transition-shadow">
          <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-[#127A4A] text-white font-extrabold text-base mb-5 shadow-md shadow-[#127A4A]/20">1</span>
          <h3 class="font-bold text-lg mb-2">Request</h3>
          <p class="text-sm text-[#475569] leading-relaxed">A worker submits a productive asset request with evidence, livelihood justification, and estimated cost.</p>
        </article>
        <article class="bg-white rounded-2xl p-8 shadow-card border border-[#f1f5f9] hover:shadow-card-hover transition-shadow">
          <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-[#E05D44] text-white font-extrabold text-base mb-5 shadow-md shadow-[#E05D44]/20">2</span>
          <h3 class="font-bold text-lg mb-2">Verify</h3>
          <p class="text-sm text-[#475569] leading-relaxed">A community verifier reviews evidence, confirms identity, and authenticates the livelihood claim.</p>
        </article>
        <article class="bg-white rounded-2xl p-8 shadow-card border border-[#f1f5f9] hover:shadow-card-hover transition-shadow">
          <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-[#127A4A] text-white font-extrabold text-base mb-5 shadow-md shadow-[#127A4A]/20">3</span>
          <h3 class="font-bold text-lg mb-2">Assess</h3>
          <p class="text-sm text-[#475569] leading-relaxed">Rules-based or external AI assessment explains suitability with clear rationale. Fallback results are labeled.</p>
        </article>
        <article class="bg-white rounded-2xl p-8 shadow-card border border-[#f1f5f9] hover:shadow-card-hover transition-shadow">
          <span class="inline-flex items-center justify-center w-11 h-11 rounded-xl bg-[#E05D44] text-white font-extrabold text-base mb-5 shadow-md shadow-[#E05D44]/20">4</span>
          <h3 class="font-bold text-lg mb-2">Fund</h3>
          <p class="text-sm text-[#475569] leading-relaxed">Contributors make simulated pledges. The system tracks eligible funding progress transparently.</p>
        </article>
      </div>
    </section>

    <!-- Value Proposition -->
    <section class="bg-white border-y border-[#f1f5f9]" aria-label="Platform benefits">
      <div class="max-w-6xl mx-auto px-6 lg:px-8 py-16 md:py-20">
        <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-start">
          <div>
            <h2 class="text-[1.75rem] md:text-[2.25rem] font-extrabold tracking-tight mb-5">Built for Real Livelihood Impact</h2>
            <p class="text-[#475569] mb-6 leading-relaxed">JIBIKA is designed to support structured livelihood growth through verified requests, explainable assessments, and transparent coordination. Every feature reflects real operational needs.</p>
            <ul class="space-y-3 text-sm text-[#475569]">
              <li class="flex items-start gap-3"><span class="inline-block w-1.5 h-1.5 rounded-full bg-[#127A4A] mt-1.5 shrink-0"></span><span>Verified community assessment before funding consideration</span></li>
              <li class="flex items-start gap-3"><span class="inline-block w-1.5 h-1.5 rounded-full bg-[#127A4A] mt-1.5 shrink-0"></span><span>Transparent assessment with rules-based and external AI provenance</span></li>
              <li class="flex items-start gap-3"><span class="inline-block w-1.5 h-1.5 rounded-full bg-[#127A4A] mt-1.5 shrink-0"></span><span>Simulated funding clearly labeled — no real payments processed</span></li>
              <li class="flex items-start gap-3"><span class="inline-block w-1.5 h-1.5 rounded-full bg-[#127A4A] mt-1.5 shrink-0"></span><span>Post-delivery monitoring and structured procurement tracking</span></li>
            </ul>
          </div>
          <div class="bg-[#f8fafc] rounded-2xl p-8 border border-[#f1f5f9]">
            <h3 class="font-bold text-lg mb-4">Who It Supports</h3>
            <div class="space-y-4">
              <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-lg bg-[#127A4A]/10 text-[#127A4A] flex items-center justify-center shrink-0 font-extrabold text-xs">W</div>
                <div>
                  <h4 class="font-semibold text-sm">Workers</h4>
                  <p class="text-xs text-[#475569]">Submit asset requests, track progress, and receive guided next steps.</p>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-lg bg-[#E05D44]/10 text-[#E05D44] flex items-center justify-center shrink-0 font-extrabold text-xs">V</div>
                <div>
                  <h4 class="font-semibold text-sm">Community Verifiers</h4>
                  <p class="text-xs text-[#475569]">Review evidence, confirm identity, and approve or flag requests.</p>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-lg bg-[#127A4A]/10 text-[#127A4A] flex items-center justify-center shrink-0 font-extrabold text-xs">C</div>
                <div>
                  <h4 class="font-semibold text-sm">Contributors</h4>
                  <p class="text-xs text-[#475569]">Browse verified opportunities and make clearly labeled simulated pledges.</p>
                </div>
              </div>
              <div class="flex items-start gap-4">
                <div class="w-8 h-8 rounded-lg bg-[#E05D44]/10 text-[#E05D44] flex items-center justify-center shrink-0 font-extrabold text-xs">A</div>
                <div>
                  <h4 class="font-semibold text-sm">Administrators</h4>
                  <p class="text-xs text-[#475569]">Oversee operations, manage users, and review audit logs securely.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Feature Highlights -->
    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-16 md:py-20" aria-label="Features">
      <h2 class="text-[1.75rem] md:text-[2.25rem] font-extrabold tracking-tight mb-10">Platform Capabilities</h2>
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <article class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-base mb-2">Asset Requests</h3>
          <p class="text-sm text-[#475569] leading-relaxed">Workers create structured requests with evidence, livelihood justification, and cost estimates for productive assets.</p>
        </article>
        <article class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-base mb-2">Community Verification</h3>
          <p class="text-sm text-[#475569] leading-relaxed">Assigned verifiers review evidence, confirm identity, and provide approval, rejection, or flag notes with required justification.</p>
        </article>
        <article class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-base mb-2">Explainable Assessment</h3>
          <p class="text-sm text-[#475569] leading-relaxed">Rules-based and external AI assessments present suitability categories, scores, and clear rationale with provenance.</p>
        </article>
        <article class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-base mb-2">Funding Coordination</h3>
          <p class="text-sm text-[#475569] leading-relaxed">Contributors view verified opportunities. All pledges are simulated and clearly identified; real payments are not processed.</p>
        </article>
        <article class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-base mb-2">Procurement & Delivery</h3>
          <p class="text-sm text-[#475569] leading-relaxed">Vendors submit quotations. Orders track lifecycle stages from procurement through delivery confirmation or dispute.</p>
        </article>
        <article class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-base mb-2">Post-Delivery Monitoring</h3>
          <p class="text-sm text-[#475569] leading-relaxed">Monitoring records track asset usage over time with real metrics. No fabricated impact claims are shown.</p>
        </article>
      </div>
    </section>
  </main>

  <footer class="border-t border-[#e2e8f0] bg-white" aria-label="Footer">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-10">
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div>
          <a href="/" class="text-xl font-extrabold tracking-tight text-[#127A4A]">JIBIKA</a>
          <p class="text-sm text-[#475569] mt-2 max-w-md leading-relaxed">A productive asset financing platform connecting verified livelihood needs with structured funding coordination.</p>
        </div>
        <div class="flex gap-6 text-sm text-[#475569]">
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">How It Works</a>
          <a href="/" class="hover:text-[#127A4A] transition-colors">Platform</a>
        </div>
      </div>
      <div class="mt-8 pt-6 border-t border-[#f1f5f9] flex flex-col md:flex-row md:items-center justify-between gap-2 text-xs text-[#94a3b8]">
        <span>JIBIKA — Productive Asset Financing Platform</span>
        <span>Simulated funding results and rules-based AI assessments are clearly labeled.</span>
      </div>
    </div>
  </footer>
</body>
</html>
