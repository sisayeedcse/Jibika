# JIBIKA — Vercel Architecture & Deployment Assessment

**Project:** JIBIKA — Productive Asset Access & Livelihood Support Platform  
**Target:** Vercel Hosting  
**Application Type:** Full-Stack Laravel 12 (PHP) with Blade Templates  

---

## 1. Hosting Compatibility Findings

### Architecture Verdict
- **Direct Vercel Deployment:** Vercel natively executes Node.js, Next.js, and static frontend bundles in an ephemeral serverless execution runtime. A standard Laravel PHP application with session state, file storage, and persistent database connections is not natively hosted on Vercel without a third-party serverless PHP runtime wrapper (such as `vercel-php`).
- **Archival Next.js Asset:** The original prototype in `archive/nextjs-original/` is compatible with Vercel for previewing legacy interfaces.
- **Production Recommendation:** As decided in Phase 0 Architecture Evaluation (Option A), full-stack Laravel hosted on IT Nut cPanel is the canonical deployment target.

---

## 2. Optional Vercel Preview Deployment (Static or Split Frontend)

If a static marketing preview or frontend demo is required on Vercel:

### Step 1: Deploy Archive Next.js on Vercel
1. In Vercel Dashboard, click **Add New Project**.
2. Link the repository `https://github.com/sisayeedcse/Jibika.git`.
3. Set **Root Directory** to `archive/nextjs-original`.
4. Framework Preset: **Next.js**.
5. Build Command: `next build`.
6. Output Directory: `.next`.
7. Click **Deploy**.

---

## 3. Split Architecture Considerations (Why Option A was selected over Option B)

If JIBIKA were split into Next.js on Vercel + Laravel API on cPanel:
1. **Cross-Origin Authentication:** Requires cross-origin HTTPS cookies (`SameSite=None; Secure`) or stateless Bearer tokens (Laravel Sanctum).
2. **CORS Headers:** Complex configuration across domains `jibika.vercel.app` and `api.jibika.gadgetexa.com`.
3. **Double Latency:** Every user interaction on Vercel would perform an outbound HTTP round-trip to the cPanel hosting server.
4. **Maintenance Overhead:** Two separate repositories/build pipelines instead of one cohesive, self-contained Laravel 12 application.

**Conclusion:** Deploying full-stack Laravel on IT Nut cPanel (with MySQL and database session storage) is the most robust, performant, and maintainable architecture for JIBIKA.
