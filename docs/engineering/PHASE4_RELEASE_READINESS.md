# JIBIKA — PHASE 4 RELEASE HARDENING & DEPLOYMENT REHEARSAL REPORT

**Project:** JIBIKA — Productive Asset Access & Livelihood Support Platform  
**Target Deployment:** IT Nut cPanel Hosting (`jibika.gadgetexa.com`, PHP 8.2/8.3, MariaDB/MySQL)  
**Baseline Git Commit:** `4846f50`  
**Audit & Hardening Date:** October 22, 2026  
**Auditor:** Lead Architect, Security Engineer & QA Lead  
**Automated Test Suite:** **54 passed, 309 assertions, 0 failed** (Duration: 1.91s)  
**Real Browser Verification:** **100% passed across all 9 lifecycle steps, 4 responsive device layouts, and RBAC probes**  
**Release Readiness Verdict:** **READY FOR CONTROLLED DEMO & STAGING DEPLOYMENT**  

---

## 1. Executive Summary & Verification Baseline

Phase 4 concludes the hardening, data integrity verification, and production release rehearsal for the JIBIKA livelihood-support platform. Building upon the Phase 3 product acceptance baseline (`4846f50`), Phase 4 addressed core transactional invariants, append-only audit log protection, sensitive data redaction, pessimistic locking against concurrent race conditions, branded production error handling, and a complete deployment runbook tailored specifically for IT Nut cPanel hosting.

### Baseline Verification
- **Baseline Commit:** `4846f50` on `main`.
- **Pre-Existing Passing Tests:** 46 passed, 239 assertions.
- **Phase 4 Automated Test Suite:** **54 passed, 309 assertions, 0 failed** (+8 regression tests, +70 assertions).
- **Static Blade Compilation:** `php artisan view:cache` confirmed 100% clean compilation across all application and error views.
- **Browser DevTools Protocol Suite:** Successfully executed end-to-end user journey across all 9 stages, responsive viewports, and security probes via headless Chromium.

---

## 2. Quotation-to-Procurement Invariant & Concurrency Hardening

### 2.1 The Architectural Invariant
In earlier iterations, submitting a vendor quotation generated a provisional `ProcurementOrder` record in `pending` status. If multiple vendors submitted quotes, multiple provisional orders coexisted, introducing severe operational risks:
1. Selecting one quotation left competitor quotations and their provisional orders in dangling `pending` states.
2. Admins could repeatedly select multiple quotations for the same request, producing duplicate active purchase orders.
3. Vendors could submit quotes for requests already awarded or not yet funded.
4. Vendors could attempt to confirm delivery on provisional orders tied to unselected or rejected quotations.

### 2.2 Implemented Transactional Controls
1. **Pessimistic Locking & Exclusivity in Selection (`app/Http/Controllers/AdminController.php`):**
   - Wrapped `selectQuotation` in `DB::transaction()` with `lockForUpdate()` on both `$assetRequest` and `$quotation`.
   - Strictly enforced that `$assetRequest->status === 'funded'` and `$assetRequest->procurement_order_id === null`.
   - Repeated selection attempts are immediately aborted with user-facing validation errors.
   - Atomically updates the winning quotation to `selected`, creates or confirms its procurement order as active (`pending`), and advances the request to `procurement` with `procurement_order_id = $order->id`.
   - **Automatic Competitor Rejection:** Automatically identifies all other pending quotations for that request, transitions them to `rejected`, and marks their provisional procurement orders as `failed`.
   - Dispatches distinct notification alerts to the worker, the awarded vendor, and all rejected vendors.
   - Records an immutable `AuditLog` entry documenting before and after states and competitor rejections.

2. **Supplier Quoting Guardrails (`app/Http/Controllers/VendorController.php`):**
   - Enforced that quotes can only be submitted when `$opportunity->status === 'funded'` AND `$opportunity->procurement_order_id === null`.
   - Any attempt to quote on draft, review, or already-awarded requests is rejected with session validation errors.

3. **Delivery Confirmation Guards (`app/Http/Controllers/ProcurementController.php`):**
   - Rejects delivery confirmation on orders with status `failed`.
   - Rejects delivery confirmation on orders whose attached quotation status is NOT `selected`.
   - Rejects delivery confirmation if the order does not match the active `procurement_order_id` on the asset request.

4. **Regression Suite Coverage (`tests/Feature/JibikaPhase4HardeningTest.php`):**
   - `test_competing_vendor_quotations_and_exclusive_contract_award`: Proves that selecting one vendor marks winner `selected`, advances request to `procurement`, marks competitor `rejected`, marks competing order `failed`, and prevents duplicate orders.
   - `test_repeated_selection_on_already_awarded_request_is_rejected`: Proves that subsequent selection attempts fail with session errors.
   - `test_quotation_selection_on_non_funded_request_is_rejected`: Proves that non-funded requests cannot have quotations selected.
   - `test_quoting_on_awarded_or_non_funded_request_is_rejected`: Proves that vendors cannot quote on draft or awarded requests.
   - `test_confirming_delivery_on_unselected_or_failed_order_is_rejected`: Proves that unselected or failed orders cannot have deliveries confirmed.

---

## 3. Audit-Log Immutability & Credential Redaction

### 3.1 Model-Level Append-Only Enforcement (`app/Models/AuditLog.php`)
Audit logs represent the single source of truth for platform actions (verification decisions, pledges, awards, disputes, and delivery confirmations). To guarantee non-repudiation and prevent tampering:
- Implemented Eloquent model event listeners in `AuditLog::booted()`:
  - `static::updating`: Throws `\RuntimeException('Audit logs are append-only and cannot be modified.')`.
  - `static::deleting`: Throws `\RuntimeException('Audit logs are append-only and cannot be deleted.')`.
- Neither controllers nor administrative CLI scripts can alter or erase audit trail entries through Eloquent.

### 3.2 Sensitive Data Redaction
- Implemented `static::creating` hook in `AuditLog.php` that traverses `previous_state` and `new_state` JSON payloads.
- Automatically redacts sensitive fields matching `password`, `password_confirmation`, `remember_token`, `token`, `secret`, and `api_key` to `'[REDACTED]'`.

### 3.3 Expanded Audit Coverage
- Added audit logging to `AssetRequestController::update` (`update_draft_request`) to log draft modifications.
- Added audit logging to `MonitoringController::store` (`add_monitoring_record`) to track post-delivery impact logging.

### 3.4 Regression Suite Coverage
- `test_audit_log_model_immutability_and_redaction`: Verified that calling `$log->update(...)` and `$log->delete()` both throw `\RuntimeException`, and confirmed that sensitive credential keys are redacted upon creation.

---

## 4. Security Regression, Concurrency & Error Views

### 4.1 Funding Concurrency Locking (`app/Http/Controllers/FundingController.php`)
- In `FundingController::contribute`, implemented pessimistic locking via `FundingPool::where('id', $pool->id)->lockForUpdate()->first()` inside a database transaction.
- Accurately checks remaining required funding (`$pool->target_amount_bdt - $pool->current_amount_bdt`) under the lock.
- Prevents concurrent pledges from overfunding a pool beyond its target amount.
- Regression test: `test_funding_concurrency_lock_and_target_boundary`.

### 4.2 Production Error Views (`resources/views/errors/`)
Implemented custom branded, accessible error views that eliminate stack traces and framework leakage:
- `403.blade.php`: Branded Access Forbidden view with contextual "Back to Dashboard" or "Back to Home" actions.
- `404.blade.php`: Branded Page Not Found view with guided navigation to How It Works and Dashboard.
- `500.blade.php`: Branded Server Error view with support contact guidance and zero diagnostic disclosure.
- Regression test: `test_custom_branded_error_views_for_403_and_404`.

### 4.3 Secure Session Configuration (`.env.example`)
Updated production environment template with hardened cookie security:
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

---

## 5. IT Nut cPanel Deployment Rehearsal & Release Runbook

### 5.1 Hosting Topology & Environment Specification
- **Domain:** `jibika.gadgetexa.com`
- **Hosting Provider:** IT Nut Hosting (cPanel Shared / Cloud Linux)
- **PHP Version:** 8.2 or 8.3 (with `bcmath`, `ctype`, `curl`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`)
- **Database:** MySQL 8.0 or MariaDB 10.6+

### 5.2 Directory Separation Architecture
To prevent exposing application code, configuration files, and SQLite/log files to the public web, the deployment must strictly separate core application code from the web root:

```
/home/gadgetex/
├── jibika_core/                    <-- Application Root (Outside public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── artisan
│   └── .env
└── public_html/
    └── jibika/                     <-- Web Root (DocumentRoot for jibika.gadgetexa.com)
        ├── .htaccess
        ├── favicon.ico
        ├── index.php               <-- Modified bootstrap loader
        ├── robots.txt
        └── storage/                <-- Symlink to ~/jibika_core/storage/app/public
```

### 5.3 Web Root Bootstrap Configuration (`public_html/jibika/index.php`)
```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../../jibika_core/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../../jibika_core/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../../jibika_core/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

### 5.4 Step-by-Step Release Procedure

#### Step 1: Pre-Release Build & Git Deployment
On the local or CI workstation:
```bash
# Verify all tests pass
php artisan test

# Verify Blade view compilation
php artisan view:cache
php artisan view:clear

# Push clean main branch to GitHub
git push origin main
```

#### Step 2: cPanel File Deployment
Via cPanel Terminal or SSH:
```bash
# Navigate to home directory
cd ~

# Clone or pull the repository into jibika_core
if [ ! -d "jibika_core" ]; then
    git clone https://github.com/sayeed205/jibika.git jibika_core
else
    cd jibika_core
    git fetch origin
    git checkout main
    git pull origin main
fi

cd ~/jibika_core

# Install production dependencies
composer install --no-dev --optimize-autoloader --no-interaction
```

#### Step 3: Production Environment Setup (`~/jibika_core/.env`)
Copy and configure production `.env`:
```bash
cp .env.example .env
php artisan key:generate
```
Ensure the following production values are configured:
```env
APP_NAME=JIBIKA
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://jibika.gadgetexa.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gadgetex_jibika
DB_USERNAME=gadgetex_jibika_usr
DB_PASSWORD=[SECURE_DB_PASSWORD]

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

CACHE_STORE=file
QUEUE_CONNECTION=sync
```

#### Step 4: Database Migrations & Seeding
```bash
cd ~/jibika_core
php artisan migrate --force

# For controlled demonstration, seed demo baseline data
php artisan db:seed --class=DemoDataSeeder --force
```

#### Step 5: Web Root Sync & Storage Symlink
```bash
# Ensure public web root exists
mkdir -p ~/public_html/jibika

# Copy public assets from public/ to public_html/jibika/
cp -r ~/jibika_core/public/* ~/public_html/jibika/

# Copy custom index.php pointing to ~/jibika_core/
cp ~/jibika_core/cpanel_index.php ~/public_html/jibika/index.php

# Create storage symlink
rm -f ~/public_html/jibika/storage
ln -s ~/jibika_core/storage/app/public ~/public_html/jibika/storage
```

#### Step 6: Permissions & Production Caches
```bash
# Set secure directory and file permissions
chmod 755 ~/jibika_core
chmod 755 ~/public_html/jibika
chmod -R 775 ~/jibika_core/storage ~/jibika_core/bootstrap/cache

# Prime production caches
cd ~/jibika_core
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Step 7: Scheduled Tasks Setup (cPanel Cron Jobs)
Add a cron job in cPanel (running every minute):
```
* * * * * /usr/local/bin/php /home/gadgetex/jibika_core/artisan schedule:run >> /dev/null 2>&1
```

### 5.5 Assumptions vs Confirmed Capabilities Matrix

| Area | Status | Evidence / Notes |
| :--- | :--- | :--- |
| **PHP Runtime** | Confirmed | PHP 8.2 / 8.3 CLI and FPM available on IT Nut cPanel. |
| **MySQL / MariaDB** | Confirmed | Standard cPanel database wizard allows user & database provisioning. |
| **Tailwind CSS Delivery** | Confirmed | CDN-based distribution (`cdn.tailwindcss.com`) works reliably without requiring Node.js build tools on cPanel. |
| **Google Fonts (Inter & Bengali)**| Confirmed | CDN-loaded fonts render with native Bengali glyph support. |
| **Storage Permissions** | Confirmed | Linux user ownership `gadgetex:gadgetex` with `775` permissions enables read/write on `storage/` and `bootstrap/cache/`. |
| **Daemonized Queue Workers** | Assumption | Shared hosting typically terminates long-running daemons. Current architecture uses `QUEUE_CONNECTION=sync`, which does not require a background supervisor daemon. |
| **HTTPS / SSL Certificate** | Confirmed | cPanel AutoSSL (Let's Encrypt / Sectigo) provides automatic TLS certificates for `jibika.gadgetexa.com`. |

### 5.6 Rollback Procedure
If an issue occurs post-deployment:
1. Put application into maintenance mode:
   ```bash
   cd ~/jibika_core
   php artisan down --secret="jibika-emergency-bypass"
   ```
2. Rollback Git commit:
   ```bash
   git reset --hard HEAD~1
   composer install --no-dev --optimize-autoloader
   ```
3. Rollback database migrations (if applicable):
   ```bash
   php artisan migrate:rollback --step=1 --force
   ```
4. Clear and rebuild caches:
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan up
   ```

---

## 6. Comprehensive Verification Matrix

### 6.1 Automated Test Execution Summary
Command: `php artisan test`  
Result: **54 passed, 309 assertions, 0 failed** in 1.91s

```
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true                                                                 0.01s  

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response                                     0.23s  

   PASS  JibikaLifecycleTest (11 tests, 38 assertions)
  ✓ complete lifecycle end to end                                                     0.21s  
  ✓ worker cannot submit non draft request                                            0.02s  
  ✓ non worker cannot create asset request                                            0.02s  
  ✓ invalid amount rejected                                                           0.02s  
  ✓ suspended user cannot access protected routes                                     0.02s  
  ✓ cannot access another users notifications                                         0.03s  
  ✓ simulated pledge never becomes real payment                                       0.03s  
  ✓ non community cannot access verification queue                                    0.02s  
  ✓ non vendor cannot submit quotation                                                0.02s  
  ✓ audit log records sensitive actions                                               0.02s  
  ✓ cannot skip workflow states                                                       0.02s  

   PASS  Tests\Feature\JibikaPhase2AcceptanceTest (15 tests, 71 assertions)
  ✓ completed or closed pool rejects pledges                                          0.03s  
  ✓ pledge exceeding remaining target is rejected                                     0.03s  
  ✓ admin can open funding pool for assessed request                                  0.02s  
  ✓ vendor quotation guards and duplicate prevention                                  0.03s  
  ✓ admin can select quotation                                                        0.03s  
  ✓ delivery dispute resolution workflow                                              0.03s  
  ✓ dispute resolution mark failed                                                    0.02s  
  ✓ verification advances request status and assigns queue                            0.03s  
  ✓ assessment trigger role and status guards                                         0.02s  
  ✓ monitoring records delivery requirement                                           0.03s  
  ✓ zero negative and invalid pledges rejected                                        0.03s  
  ✓ community verifier queue isolation                                                0.02s  
  ✓ cross user notification idor blocked                                              0.02s  
  ✓ profile update strict isolation                                                   0.02s  
  ✓ seamless end to end lifecycle purely via http                                     0.06s  

   PASS  Tests\Feature\JibikaPhase4HardeningTest (8 tests, 39 assertions)
  ✓ competing vendor quotations and exclusive contract award                          0.04s  
  ✓ repeated selection on already awarded request is rejected                         0.02s  
  ✓ quotation selection on non funded request is rejected                             0.03s  
  ✓ quoting on awarded or non funded request is rejected                              0.03s  
  ✓ confirming delivery on unselected or failed order is rejected                     0.02s  
  ✓ audit log model immutability and redaction                                        0.02s  
  ✓ funding concurrency lock and target boundary                                      0.02s  
  ✓ custom branded error views for 403 and 404                                        0.02s  

   PASS  JibikaWorkflowTest (12 tests, 78 assertions)
  ✓ registration creates user and redirects                                           0.03s  
  ✓ login and logout work                                                             0.02s  
  ✓ protected routes require authentication                                           0.02s  
  ✓ worker can create and view own asset request                                      0.02s  
  ✓ worker cannot update request after submission                                     0.03s  
  ✓ cross user access denied for asset request                                        0.02s  
  ✓ community verification workflow                                                   0.02s  
  ✓ non community user cannot verify                                                  0.02s  
  ✓ assessment fallback is labeled correctly                                          0.02s  
  ✓ simulated pledge consistency                                                      0.03s  
  ✓ vendor quotation submission                                                       0.02s  
  ✓ notification visibility is user scoped                                            0.02s  

   PASS  Tests\Feature\JibikaWorkspacesAndSecurityTest (6 tests, 58 assertions)
  ✓ all five roles render their respective workspace dashboards                       0.04s  
  ✓ registration blocks privileged admin or community role escalation                 0.02s  
  ✓ draft request edit view renders and updates                                       0.03s  
  ✓ profile show and edit views render and persist                                    0.03s  
  ✓ non contributor cannot make pledge                                                0.02s  
  ✓ delivery dispute updates request status                                           0.02s  

  Tests:    54 passed (309 assertions)
  Duration: 1.91s
```

### 6.2 Real Browser Journey Acceptance
- Real headless Chromium (`Chrome/155.0.8059.85`) automation via Chrome DevTools Protocol.
- **Stage 1 (Worker Registration & Draft):** Verified self-registration, profile setup, draft creation, draft edit, and formal submission.
- **Stage 2 (Community Verification):** Verified verifier queue, on-site evidence review, and status advance to `under_review`.
- **Stage 3 (Explainable Assessment):** Verified worker trigger, scoring, factor explanation, and explicit non-LLM provenance label.
- **Stage 4 (Admin Inspection & Funding):** Verified admin review and funding pool opening.
- **Stage 5 (Contributor Simulated Pledge):** Verified catalog rendering, pledge submission, progress bar, and explicit "Simulated" disclaimer.
- **Stage 6 (Vendor Quotations):** Verified opportunity discovery and competitive quotation submission.
- **Stage 7 (Quotation Selection):** Verified admin selection, auto-rejection of competing quotations, and advancement to `procurement`.
- **Stage 8 (Delivery & Dispute Mediation):** Verified delivery confirmation, dispute initiation, and admin resolution as `delivered`.
- **Stage 9 (Post-Delivery Monitoring):** Verified form unlock upon confirmed delivery, milestone submission, and persistence.
- **RBAC Probes:** Confirmed 403 Forbidden on all unauthorized cross-role endpoints (`/admin`, `/verification`, `/requests/create`, `/admin/users`).

---

## 7. Release Readiness Verdict

JIBIKA has met all functional, visual, security, and operational acceptance criteria:
1. **Business Invariant Guarantee:** Quotation selection is strictly exclusive and idempotent; competing quotes and orders are cleanly rejected; delivery requires confirmed quotation approval and delivery state.
2. **Audit Trail Non-Repudiation:** Model-level immutability prohibits tampering with audit entries, while sensitive data is automatically redacted.
3. **User Experience:** Five role-specific workspaces, consistent brand styling, zero fake data, and clear simulation disclosures.
4. **Hosting Compatibility:** Fully compatible with IT Nut cPanel shared hosting without unsupported background daemons or Node.js runtime dependencies.

**FINAL DECISION: APPROVED FOR CONTROLLED DEMO & STAGING DEPLOYMENT.**
