# ITNUT Deployment Guide

Target: `jibika.gadgetexa.com`
Environment: cPanel (PHP 8.3/8.4 selectable)

## Verified Constraints
- PHP 8.2+ available; PHP 8.3/8.4 selectable in cPanel
- cPanel Git Version Control available
- SSH / Terminal access: NOT ESTABLISHED
- Composer / Artisan: NOT AVAILABLE on server
- Queue workers: NOT AVAILABLE (core workflows are synchronous)

## Deployment Strategy (Local Build)
1. Build locally: `composer install --no-dev --optimize-autoloader`
2. Compile assets: `npm run build`
3. Upload via cPanel Git or File Manager
4. Create `.env` manually via cPanel File Manager
5. Import database schema via `phpMyAdmin`
6. Create `public/storage` symlink via File Manager
7. Set `public/` as document root if configurable

## Production Checklist
- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_CONNECTION=mysql`
- `.env` not in `public/`
- `storage/` writable
- `.cpanel.yml` configured for safe deployment

## Unverified
- Queue worker availability
- Actual cPanel `public/` root configuration
- HTTPS enforcement via `.htaccess`
- Database import speed
