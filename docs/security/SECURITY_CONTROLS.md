# JIBIKA — Security Controls & Risk Mitigation

## 1. Authentication & Session Management

- **Session Security:**
  - Cookies are configured with `SESSION_SECURE_COOKIE=true` for HTTPS enforcement.
  - `SESSION_HTTP_ONLY=true` prevents client-side JavaScript access (`document.cookie`), mitigating XSS session theft.
  - `SESSION_SAME_SITE=lax` defends against Cross-Site Request Forgery (CSRF).
- **Session Invalidation:**
  - `LoginController::logout` invalidates the active session and regenerates CSRF tokens (`$request->session()->invalidate()`).
- **Account Status Guard:**
  - `App\Http\Middleware\Active` intercepts every authenticated request.
  - Suspended accounts are immediately halted and shown [`resources/views/errors/account-suspended.blade.php`](file:///d:/Important%20Files/HTML/Systems/Jibika/resources/views/errors/account-suspended.blade.php) with an HTTP 403 status.

---

## 2. Role-Based Access Control (RBAC) & Authorization

JIBIKA isolates 5 distinct roles: `worker`, `community`, `contributor`, `vendor`, and `admin`.

### 2.1 Privilege Escalation Prevention
- Public self-registration (`RegisterController::store`) strictly limits the selectable role to `['worker', 'contributor', 'vendor']`.
- The `role` column is omitted from `$fillable` in `User.php` to prevent mass-assignment injection.
- Privileged roles (`admin` and `community`) can only be provisioned through administrative action or database seeding.

### 2.2 IDOR & Cross-User Data Scoping
- **Asset Requests:** Workers are prevented from viewing or modifying requests belonging to others via `AssetRequestPolicy`.
- **Notifications:** Notifications are strictly scoped: `Notification::where('user_id', auth()->id())`. Accessing another user's notification returns `403 Forbidden`.
- **Community Queue:** Community verifiers can only review and decide on verifications assigned to their profile or district somitte.
- **Vendor Quotations:** Suppliers can only view and update their own submitted bids.

---

## 3. Data Integrity & Append-Only Audit Logging

### 3.1 Model-Level Immutability
`App\Models\AuditLog` enforces non-repudiation at the Eloquent lifecycle layer:
```php
protected static function booted()
{
    static::updating(function () {
        throw new \RuntimeException('Audit logs are append-only and cannot be modified.');
    });

    static::deleting(function () {
        throw new \RuntimeException('Audit logs are append-only and cannot be deleted.');
    });
}
```
Neither controllers, jobs, nor CLI scripts can alter or erase historical records.

### 3.2 Credential & Token Redaction
Before any audit record is persisted, `AuditLog` sanitizes `previous_state` and `new_state` payloads, replacing keys matching `password`, `token`, `secret`, `remember_token`, or `api_key` with `'[REDACTED]'`.

---

## 4. Concurrency Protection & Transaction Safety

1. **Pessimistic Locking on Quotation Selection (`SelectQuotationAction`):**
   - Both the quotation and the target request are locked with `lockForUpdate()`.
   - Prevents race conditions where two administrators or concurrent API calls might attempt to award conflicting quotations simultaneously.
2. **Pessimistic Locking on Funding Pledges (`ContributePledgeAction`):**
   - The funding pool row is locked with `lockForUpdate()` during pledge processing.
   - Accurately checks remaining required funding and increments the balance atomically, preventing race conditions where multiple pledges exceed the required total.

---

## 5. Diagnostic Leakage & Production Error Handling

- Production error views ([`403.blade.php`](file:///d:/Important%20Files/HTML/Systems/Jibika/resources/views/errors/403.blade.php), [`404.blade.php`](file:///d:/Important%20Files/HTML/Systems/Jibika/resources/views/errors/404.blade.php), [`500.blade.php`](file:///d:/Important%20Files/HTML/Systems/Jibika/resources/views/errors/500.blade.php)) provide clean, branded UI while completely suppressing stack traces, SQL queries, directory paths, and framework versions.
- `APP_DEBUG=false` is enforced in the production `.env.example`.
- `config/services.php` decouples all secrets from application code.
