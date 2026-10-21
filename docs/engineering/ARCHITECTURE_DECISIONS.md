# Architecture Decisions

- Laravel 12.12.2 (PHP 8.2 compatible) — verified installed
- Blade + Tailwind CSS (no separate React server)
- SQLite for local/test; MySQL for production
- Synchronous AI service (no queue dependency)
- Development fallback explicitly says `NOT AN LLM RESPONSE`
- All monetary values use `DECIMAL(10,2)`
- Role-based authorization via policies and middleware
- Original Next.js preserved at `archive/nextjs-original/`
- Deployment designed for cPanel without SSH or queue workers
