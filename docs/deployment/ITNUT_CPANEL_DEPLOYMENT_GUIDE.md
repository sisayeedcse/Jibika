# JIBIKA — Production Deployment Guide for IT Nut cPanel

**Application:** JIBIKA — Productive Asset Access & Livelihood Support Platform  
**Target Domain:** `https://jibika.gadgetexa.com`  
**Host Target:** IT Nut Hosting (cPanel with CloudLinux / Apache / MySQL)  
**PHP Target:** PHP 8.2+ (PHP 8.3 recommended via cPanel MultiPHP Manager)  
**Database:** MySQL / MariaDB via cPanel MySQL Databases & phpMyAdmin  

---

## 1. Architecture Topology on cPanel

On shared cPanel hosting without root or persistent terminal daemons:
- **Web Server:** Apache / LiteSpeed serving `public/` as DocumentRoot
- **PHP Version:** 8.2 or 8.3 with extensions: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `ctype`, `fileinfo`, `json`, `tokenizer`, `xml`
- **Application Files:** Placed outside of `public_html` (e.g. `/home/cpaneluser/jibika`) for security
- **DocumentRoot:** The subdomain `jibika.gadgetexa.com` should point directly to `/home/cpaneluser/jibika/public`
- **Session & Cache Driver:** `database` (avoids Redis requirements)
- **Queue Driver:** `sync` (runs jobs synchronously without supervisor/daemon workers)

```
/home/cpaneluser/
├── jibika/                      <-- Laravel root (NOT accessible via browser)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/                 <-- Must be chmod 775 / writable
│   ├── vendor/                  <-- Uploaded from local composer install
│   ├── .env                     <-- Private configuration (chmod 600)
│   └── public/                  <-- Web root for subdomain
│       ├── index.php
│       ├── .htaccess
│       └── favicon.svg
```

---

## 2. Pre-Deployment: Local Build & Preparation

Before uploading, prepare the production bundle locally:

```bash
# 1. Ensure test suite passes 100%
php artisan test

# 2. Install production-only dependencies (excludes development/testing packages)
composer install --no-dev --optimize-autoloader

# 3. Generate production application key (if not already set)
php artisan key:generate --show
```

---

## 3. Step-by-Step cPanel Setup

### Step A: Configure Subdomain in cPanel
1. Log in to your IT Nut cPanel.
2. Navigate to **Domains** &rarr; **Domains** (or **Subdomains**).
3. Create subdomain: `jibika.gadgetexa.com`.
4. Set the **Document Root** field to:  
   `/home/YOUR_CPANEL_USER/jibika/public`  
   *(Replace `YOUR_CPANEL_USER` with your actual cPanel username).*

### Step B: Create MySQL Database & User
1. In cPanel, navigate to **Databases** &rarr; **MySQL Database Wizard**.
2. **Step 1:** Create database name: `cpaneluser_jibika`.
3. **Step 2:** Create user: `cpaneluser_jibikau` with a strong random password.
4. **Step 3:** Assign **ALL PRIVILEGES** to the user on the database.
5. Record the database name, username, and password for `.env`.

### Step C: Deploy Application Files
**Option 1: Using cPanel Git Version Control**
1. In cPanel, open **Git Version Control**.
2. Click **Create Repository**.
3. Enter Clone URL: `https://github.com/sisayeedcse/Jibika.git`
4. Set Repository Path: `/home/YOUR_CPANEL_USER/jibika`
5. Click **Create**.
6. After cloning, use cPanel File Manager to upload the local `vendor` folder into `/home/YOUR_CPANEL_USER/jibika/vendor` (since `vendor` is gitignored).

**Option 2: Using cPanel File Manager (Direct ZIP Upload)**
1. Locally compress the project folder (excluding `.git`, `node_modules`, `tests`, `archive`) into `jibika-deploy.zip`.
2. In cPanel **File Manager**, create folder `/home/YOUR_CPANEL_USER/jibika`.
3. Upload and extract `jibika-deploy.zip` inside that directory.

### Step D: Configure `.env` in cPanel
1. In cPanel File Manager, open `/home/YOUR_CPANEL_USER/jibika`.
2. Create or edit `.env`:
   ```dotenv
   APP_NAME=JIBIKA
   APP_ENV=production
   APP_KEY=base64:YOUR_GENERATED_APP_KEY
   APP_DEBUG=false
   APP_URL=https://jibika.gadgetexa.com

   LOG_CHANNEL=stack
   LOG_LEVEL=error

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cpaneluser_jibika
   DB_USERNAME=cpaneluser_jibikau
   DB_PASSWORD=YOUR_DB_PASSWORD

   SESSION_DRIVER=database
   SESSION_LIFETIME=120
   CACHE_STORE=database
   QUEUE_CONNECTION=sync
   FILESYSTEM_DISK=local

   AI_PROVIDER=rules_based
   AI_API_KEY=
   ```
3. Set `.env` file permissions to `600` (Read/Write owner only).

### Step E: Initialize Database Tables
If cPanel Terminal is available:
```bash
cd /home/YOUR_CPANEL_USER/jibika
php artisan migrate --force
php artisan db:seed --force
```

If Terminal access is NOT available:
1. Run locally with MySQL output:
   `php artisan migrate:fresh --seed` against a local MySQL database.
2. Export the SQL dump via phpMyAdmin or `mysqldump`.
3. In cPanel, open **phpMyAdmin**, select `cpaneluser_jibika`, and click **Import**.
4. Choose the SQL file and execute.

### Step F: Verify Directory Permissions
In cPanel File Manager:
- `/home/YOUR_CPANEL_USER/jibika/storage` &rarr; `775` (Recursive)
- `/home/YOUR_CPANEL_USER/jibika/bootstrap/cache` &rarr; `775` (Recursive)

### Step G: Enable HTTPS SSL
1. In cPanel, open **SSL/TLS Status**.
2. Select `jibika.gadgetexa.com` and click **Run AutoSSL**.
3. Ensure HTTPS redirect is enabled.

---

## 4. Verification & Health Check

Once deployed, verify:
1. Visit `https://jibika.gadgetexa.com` &rarr; Landing page loads cleanly.
2. Visit `https://jibika.gadgetexa.com/login` &rarr; Sign in with `admin@demo.local` / `password`.
3. Admin workspace renders real statistics without errors.
4. Test worker draft creation and community verification workflow.
5. Check `/home/YOUR_CPANEL_USER/jibika/storage/logs/laravel.log` to confirm zero exceptions.

---

## 5. Rollback & Backup Procedure

- **Database Backup:** Before updates, use cPanel **Backup** or **phpMyAdmin** Export to download a snapshot of `cpaneluser_jibika`.
- **Code Rollback:** In cPanel Git Version Control, pull the previous stable commit SHA, or restore previous zip backup.
