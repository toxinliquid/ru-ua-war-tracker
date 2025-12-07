# HeyMap Situation Dashboard

Public-facing Laravel 12 + Livewire app to view frontline reports, West News feeds, control dashboards, and visitor stats.

## Requirements
- PHP 8.2+
- Composer
- Node.js 18+
- SQLite/MySQL/Postgres (default uses SQLite) with a database configured in `.env`

## Quick start
```bash
# Install dependencies
composer install
npm install

# Copy env and set app key
cp .env.example .env
php artisan key:generate

# Configure DB in .env, then migrate
php artisan migrate

# Build assets
npm run build

# Serve
php artisan serve
```

## Dev mode with hot reload
```
npm run dev
php artisan serve
```
(or run `composer dev` to start serve/queue/logs/vite concurrently if you have npm/npx.)

## Admin access
- Auth-protected admin routes under `/admin/*` (cities, regions, war-posts, west-news, visits).
- Public pages: `/`, `/articles`, `/war-posts`, `/war-posts/{id}`, `/cities-control`, `/regions-control`, `/dashboard`.

## GeoIP
Visits are logged via `torann/geoip`; ensure `config/geoip.php` has a working driver. Localhost (`127.0.0.1`) is skipped.

## Tests
```
php artisan test
```
