<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="JIBIKA connects verified workers with productive assets through community verification, explainable assessment, and transparent funding coordination.">
<title>JIBIKA — Productive Asset Access &amp; Livelihood Support Platform</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Bengali:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body { font-family: 'Inter','Noto Sans Bengali',system-ui,sans-serif; }
  .text-brand-green { color: #127A4A; }
  .bg-brand-green { background-color: #127A4A; }
  .hover\:bg-brand-deep:hover { background-color: #0e5a36; }
  .text-brand-coral { color: #E05D44; }
  .bg-brand-coral-soft { background-color: #fbeae6; }
  ::selection { background: rgba(18,122,74,0.12); }
</style>
</head>
<body class="bg-[#f8fafc] text-[#0f172a] antialiased min-h-screen flex flex-col justify-between">

  <!-- Navigation Bar -->
  <nav class="bg-white/90 backdrop-blur-xl border-b border-[#e2e8f0] sticky top-0 z-50" aria-label="Primary navigation">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-[68px]">
        <div class="flex items-center gap-3">
          <a href="/" class="text-[1.4rem] font-black tracking-[0.02em] text-[#127A4A]" aria-label="JIBIKA Home">JIBIKA</a>
          <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-50 text-[#127A4A] font-bold border border-emerald-200">জীবিকা</span>
        </div>
        <div class="hidden md:flex items-center gap-7 text-[0.88rem] font-medium text-[#475569]">
          <a href="/" class="hover:text-[#127A4A] transition-colors">Home</a>
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">How It Works</a>
          @auth
            <a href="{{ route('dashboard') }}" class="hover:text-[#127A4A] transition-colors">Workspace</a>
            <form action="{{ route('logout') }}" method="POST" class="inline">
              @csrf
              <button type="submit" class="hover:text-[#127A4A] transition-colors">Sign Out</button>
            </form>
          @else
            <a href="{{ route('login') }}" class="hover:text-[#127A4A] transition-colors">Sign In</a>
            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-[#127A4A] text-white text-[0.82rem] font-semibold hover:bg-[#0e5a36] transition-colors shadow-md shadow-emerald-900/10">Register</a>
          @endauth
        </div>
        <div class="md:hidden">
          <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="text-[#475569] hover:text-[#127A4A] p-2" aria-label="Toggle menu">☰</button>
        </div>
      </div>
    </div>
    <div id="mobile-menu" class="hidden md:hidden border-t border-[#e2e8f0] bg-white">
      <div class="max-w-6xl mx-auto px-6 py-4 flex flex-col gap-3 text-sm font-medium text-[#475569]">
        <a href="/" class="py-1.5 hover:text-[#127A4A]">Home</a>
        <a href="{{ route('how-it-works') }}" class="py-1.5 hover:text-[#127A4A]">How It Works</a>
        @auth
          <a href="{{ route('dashboard') }}" class="py-1.5 hover:text-[#127A4A]">Workspace</a>
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="py-1.5 text-left text-[#E05D44]">Sign Out</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="py-1.5 hover:text-[#127A4A]">Sign In</a>
          <a href="{{ route('register') }}" class="py-1.5 text-[#127A4A] font-bold">Register</a>
        @endauth
      </div>
    </div>
  </nav>

  <main class="flex-grow">
    <!-- Hero Section -->
    <section class="max-w-6xl mx-auto px-6 lg:px-8 pt-16 md:pt-24 pb-16 md:pb-20">
      <div class="grid md:grid-cols-2 gap-12 lg:gap-16 items-center">
        <div>
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-[#127A4A] text-xs font-bold border border-emerald-200 mb-6">
            <span class="w-2 h-2 rounded-full bg-[#127A4A]"></span>
            Productive Asset Access Platform
          </div>

          <h1 class="text-[2.6rem] md:text-[3.2rem] lg:text-[3.6rem] font-extrabold leading-[1.1] tracking-tight mb-6">
            <span class="text-[#127A4A]">From Small Capital</span><br>to Sustainable Livelihood
          </h1>
          <p class="text-base md:text-lg text-[#475569] mb-8 leading-relaxed max-w-lg">
            JIBIKA connects verified craftspeople, artisans, and micro-entrepreneurs with productive equipment through community verification, explainable AI assessment, and transparent funding coordination.
          </p>
          <div class="flex gap-4 flex-wrap">
            <a href="{{ route('register') }}" class="inline-flex items-center px-7 py-3.5 rounded-xl bg-[#127A4A] text-white font-semibold shadow-lg shadow-emerald-900/15 hover:bg-[#0e5a36] hover:-translate-y-0.5 transition-all">
              Join Platform &rarr;
            </a>
            <a href="{{ route('how-it-works') }}" class="inline-flex items-center px-7 py-3.5 rounded-xl bg-white border border-[#e2e8f0] text-[#475569] font-semibold hover:border-[#127A4A] hover:text-[#127A4A] transition-all">
              Explore Lifecycle
            </a>
          </div>
          <p class="mt-5 text-xs text-[#94a3b8]">Simulated funding and AI assessment results are clearly labeled. No real payments are processed.</p>
        </div>

        <!-- Hero Diagram Card -->
        <div class="rounded-3xl bg-white p-7 md:p-8 shadow-xl shadow-slate-900/5 border border-[#e2e8f0]">
          <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#94a3b8] mb-5">End-to-End Productive Pipeline</h3>

          <div class="space-y-4">
            <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
              <span class="w-8 h-8 rounded-xl bg-[#127A4A] text-white flex items-center justify-center font-bold text-xs shrink-0">1</span>
              <div>
                <h4 class="text-xs font-bold text-[#0f172a]">Worker Asset Application</h4>
                <p class="text-[0.7rem] text-[#475569]">Self-declared need, trade experience, and livelihood impact plan.</p>
              </div>
            </div>

            <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-coral-50/50 border border-[#E05D44]/20" style="background-color: #fbeae6;">
              <span class="w-8 h-8 rounded-xl bg-[#E05D44] text-white flex items-center justify-center font-bold text-xs shrink-0">2</span>
              <div>
                <h4 class="text-xs font-bold text-[#0f172a]">Community Somitte Verification</h4>
                <p class="text-[0.7rem] text-[#475569]">Local verifier authenticates applicant identity and trade practice.</p>
              </div>
            </div>

            <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
              <span class="w-8 h-8 rounded-xl bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0">3</span>
              <div>
                <h4 class="text-xs font-bold text-[#0f172a]">Explainable AI Assessment</h4>
                <p class="text-[0.7rem] text-[#475569]">Objective suitability scores and rationale with verified provenance.</p>
              </div>
            </div>

            <div class="flex items-center gap-4 p-3.5 rounded-2xl bg-emerald-50/50 border border-emerald-100">
              <span class="w-8 h-8 rounded-xl bg-[#127A4A] text-white flex items-center justify-center font-bold text-xs shrink-0">4</span>
              <div>
                <h4 class="text-xs font-bold text-[#0f172a]">Direct Supplier Procurement &amp; Handover</h4>
                <p class="text-[0.7rem] text-[#475569]">Capital paid directly to equipment vendor with proof of delivery.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Five Roles Section -->
    <section class="bg-white border-y border-[#f1f5f9] py-16 md:py-20" aria-label="Platform roles">
      <div class="max-w-6xl mx-auto px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
          <h2 class="text-[1.75rem] md:text-[2.25rem] font-extrabold tracking-tight mb-3">Five Distinct Ecosystem Workspaces</h2>
          <p class="text-[#475569]">Purpose-built operational workspaces connecting every participant in the livelihood ecosystem.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <!-- Worker -->
          <div class="p-6 rounded-2xl bg-[#f8fafc] border border-[#f1f5f9] flex flex-col justify-between">
            <div>
              <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#127A4A] flex items-center justify-center font-bold text-sm mb-4 border border-emerald-100">
                W
              </div>
              <h3 class="font-bold text-sm text-[#0f172a] mb-1">Workers</h3>
              <p class="text-xs text-[#475569] leading-relaxed">Submit asset requests, save drafts, view timeline, and log post-delivery income.</p>
            </div>
          </div>

          <!-- Community Verifier -->
          <div class="p-6 rounded-2xl bg-[#f8fafc] border border-[#f1f5f9] flex flex-col justify-between">
            <div>
              <div class="w-10 h-10 rounded-xl bg-coral-50 text-[#E05D44] flex items-center justify-center font-bold text-sm mb-4 border border-[#E05D44]/20" style="background-color: #fbeae6;">
                V
              </div>
              <h3 class="font-bold text-sm text-[#0f172a] mb-1">Verifiers</h3>
              <p class="text-xs text-[#475569] leading-relaxed">Review localized queues, inspect evidence, and approve or flag applications.</p>
            </div>
          </div>

          <!-- Contributor -->
          <div class="p-6 rounded-2xl bg-[#f8fafc] border border-[#f1f5f9] flex flex-col justify-between">
            <div>
              <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold text-sm mb-4 border border-blue-100">
                C
              </div>
              <h3 class="font-bold text-sm text-[#0f172a] mb-1">Contributors</h3>
              <p class="text-xs text-[#475569] leading-relaxed">Browse verified requests and record simulated pledges to coordinate funding.</p>
            </div>
          </div>

          <!-- Vendor -->
          <div class="p-6 rounded-2xl bg-[#f8fafc] border border-[#f1f5f9] flex flex-col justify-between">
            <div>
              <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold text-sm mb-4 border border-purple-100">
                S
              </div>
              <h3 class="font-bold text-sm text-[#0f172a] mb-1">Suppliers</h3>
              <p class="text-xs text-[#475569] leading-relaxed">Submit item quotations, manage procurement orders, and confirm physical delivery.</p>
            </div>
          </div>

          <!-- Admin -->
          <div class="p-6 rounded-2xl bg-[#f8fafc] border border-[#f1f5f9] flex flex-col justify-between">
            <div>
              <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold text-sm mb-4 border border-slate-200">
                A
              </div>
              <h3 class="font-bold text-sm text-[#0f172a] mb-1">Admins</h3>
              <p class="text-xs text-[#475569] leading-relaxed">Control user statuses, oversee requests, and inspect immutable audit logs.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Platform Capabilities Grid -->
    <section class="max-w-6xl mx-auto px-6 lg:px-8 py-16 md:py-20" aria-label="Capabilities">
      <div class="text-center max-w-xl mx-auto mb-14">
        <h2 class="text-[1.75rem] md:text-[2.25rem] font-extrabold tracking-tight mb-3">Engineered for Data Integrity</h2>
        <p class="text-[#475569]">Built on rigorous server-side authorization, immutable audit logging, and honest disclosures.</p>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-sm text-[#0f172a] mb-2">Role-Based Authorization</h3>
          <p class="text-xs text-[#475569] leading-relaxed">Strict server-side policy enforcement prevents privilege escalation, unauthorized state changes, and cross-user data leakage.</p>
        </div>

        <div class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-sm text-[#0f172a] mb-2">Simulated Funding Honesty</h3>
          <p class="text-xs text-[#475569] leading-relaxed">All pledges in demonstration mode are clearly identified as simulated commitments. No fabricated payment transactions or fake card debits.</p>
        </div>

        <div class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-sm text-[#0f172a] mb-2">Explainable AI Provenance</h3>
          <p class="text-xs text-[#475569] leading-relaxed">Suitability assessments clearly disclose whether they were produced by an external model or the deterministic rules fallback.</p>
        </div>

        <div class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-sm text-[#0f172a] mb-2">Direct Asset Handover</h3>
          <p class="text-xs text-[#475569] leading-relaxed">Capital is routed directly to vetted equipment vendors rather than unencumbered cash, eliminating diversion risks.</p>
        </div>

        <div class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-sm text-[#0f172a] mb-2">Post-Delivery Tracking</h3>
          <p class="text-xs text-[#475569] leading-relaxed">Periodic livelihood monitoring logs actual verified household income and output records over time.</p>
        </div>

        <div class="bg-white rounded-2xl p-7 shadow-card border border-[#f1f5f9]">
          <h3 class="font-bold text-sm text-[#0f172a] mb-2">Immutable Audit Trail</h3>
          <p class="text-xs text-[#475569] leading-relaxed">Every verification decision, status transition, user suspension, and quotation is captured in the database audit registry.</p>
        </div>
      </div>
    </section>
  </main>

  <footer class="border-t border-[#e2e8f0] bg-white" aria-label="Footer">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 py-10">
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div>
          <a href="/" class="text-xl font-black tracking-tight text-[#127A4A]">JIBIKA</a>
          <p class="text-xs text-[#475569] mt-2 max-w-md leading-relaxed">Productive Asset Access &amp; Livelihood Support Platform &bull; Connecting verified workers with productive equipment.</p>
        </div>
        <div class="flex gap-6 text-xs text-[#475569]">
          <a href="{{ route('how-it-works') }}" class="hover:text-[#127A4A] transition-colors">How It Works</a>
          <a href="{{ route('login') }}" class="hover:text-[#127A4A] transition-colors">Sign In</a>
          <a href="{{ route('register') }}" class="hover:text-[#127A4A] transition-colors">Register</a>
        </div>
      </div>
      <div class="mt-8 pt-6 border-t border-[#f1f5f9] flex flex-col md:flex-row md:items-center justify-between gap-2 text-[0.7rem] text-[#94a3b8]">
        <span>JIBIKA — Production-Oriented Web Application</span>
        <span>Simulated funding mode &bull; Rules-based and external AI assessment provenance clearly stated.</span>
      </div>
    </div>
  </footer>

</body>
</html>
