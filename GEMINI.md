# GEMINI.md

This file provides guidance to Gemini (or any AI Agent) when working with code in this repository.

## Project Overview

A multi-tenant-style village government CMS ("Portal Resmi Website Desa"). Each deployment serves one village (`desa`) and combines a public-facing informational site with a Filament admin panel for content and micro-population data management (Regsosek/SDGs Desa). A single codebase is deployed per-village on shared hosting (Hostinger/cPanel); see `CRON.md` for the per-village server layout.

**Stack:** Laravel 12 (PHP 8.2+), Filament v4 admin, Tailwind CSS v4 + Vite, Alpine.js / ApexCharts / Leaflet.js on the frontend, Spatie Permission (RBAC via filament-shield), Spatie Backup with dual local + Google Drive storage.

## Commands

```bash
composer dev          # Run server + queue + logs (pail) + vite concurrently — primary dev command
composer test         # Clears config then runs the full PHPUnit suite
composer setup        # First-time: install deps, generate key, migrate, build assets

php artisan test --filter=StatisticDashboardTest   # Run a single test class
php artisan test tests/Feature/SEOTest.php          # Run a single test file
php artisan migrate:fresh --seed                    # Reset DB with seed data

npm run dev           # Vite dev server only
npm run build         # Compile production assets (run before committing frontend changes)
./vendor/bin/pint     # Laravel Pint code formatter
```

Tests run against an in-memory SQLite DB (see `phpunit.xml`) — no local DB needed for the suite.

## Architecture

### Two-surface application
- **Public site** (`routes/web.php` → `app/Http/Controllers/*`): thin controllers rendering Blade views. Indonesian URL slugs (`/berita`, `/pengumuman`, `/apbdes`, `/peta`, `/layanan`). SEO routes (`/sitemap.xml`, `/robots.txt`) via `SitemapController`. `/init` is a super-admin-only route that physically copies `storage/app/public` into `public/storage` — a workaround for shared hosts where `symlink()` is disabled.
- **Admin panel** (`app/Filament/`): the `admin` panel (`AdminPanelProvider`) auto-discovers Resources/Pages/Widgets. Navigation is organized into fixed groups: Kependudukan, Profil, Informasi, Transparansi, Layanan, Peta, Master, Sistem.

### Global request pipeline
Three custom middleware are appended to the `web` group in `bootstrap/app.php`, applied to every response: `SecurityHeaders`, `TrackVisitor` (visitor logging → `VisitorLog`), `MinifyHtml`. Exceptions in production (excluding 404/validation/auth) are pushed to Telegram via `SystemMonitorNotification`.

### AppServiceProvider is the wiring hub
`app/Providers/AppServiceProvider.php::boot()` does a lot of cross-cutting setup — read it before changing global behavior:
- **Settings → global view data:** all `Setting` key/value rows are loaded once and shared to every view as `$site_settings` (JSON-array values are auto-decoded). `village_name` also dynamically drives the backup filename prefix. `$visitor_stats` is likewise shared globally.
- **Home cache invalidation:** a `$clearHomeCache` closure forgets ~20 `home_*` cache keys and is bound to `saved`/`deleted` on Post, Announcement, Official, StatisticData, BudgetRealization, Publication, Gallery, Dusun, Citizen, Family. **If you add caching to the home/profil pages, register invalidation here.**
- **Audit + Telegram listeners:** Login/Logout/Failed auth and User/Setting mutations emit `AuditLog` rows and Telegram alerts (super_admin actions are intentionally skipped to reduce noise).
- **Google Drive driver:** the `google` storage disk is registered here (wraps `masbug/flysystem-google-drive-ext` via `App\Services\GoogleDriveAdapterWrapper`).
- **Global Filament tweak:** all `Select` components are forced to `->native(false)`.

### Data model
Population hierarchy: `Dusun` → `Family` → `Citizen`. Statistics are a separate configurable system: `StatisticCategory` → `StatisticIndicator` → `StatisticData`, aggregated through `App\Services\StatisticService`. Content models (Post, Announcement, Gallery, etc.) plus transparency (BudgetCategory/BudgetRealization for APBDes, Document, Dataset, Publication) and services (Service, ServiceRequest with ticket tracking, Complaint, GuestBook).

### Shared model traits (`app/Traits/`)
- **`HasSlug`** — auto-generates a unique slug from `title` or `name` on save; dedupes against soft-deleted rows with `withTrashed()`. Use this instead of hand-rolling slug logic.
- **`Auditable`** — hooks create/update/delete to write `AuditLog` entries automatically.

### Scheduling & queue (`routes/console.php`)
Scheduled via `schedule:run` (one cron per village, every minute — see `CRON.md`). Notably, `queue:work --stop-when-empty` runs every minute `withoutOverlapping()` because shared hosts have no Supervisor daemon. Backups run daily (clean 01:00, run 02:00); old statistic data is pruned yearly. There is also a custom `app:compress-post-images` command (GD-based, compresses post images >300KB and reconciles a legacy `photo`→`featured_image` column rename).

## Development Standards

### Quality principles
Prioritize **quality** (security, stability, readability, performance) over speed. Write simple, secure, consistent, maintainable code. Follow PSR standards for PHP, SOLID principles, and Clean Code practices.

### Code modification protocol
- **Analyze first:** understand context before editing. Make minimal changes. Ask if uncertain.
- **Type safety:** use type hints and return types consistently.
- **Separation of concerns:** keep business logic out of Controllers. Controllers should be thin — delegate to Services/Models.
- **Frontend:** build reusable components. Never execute queries directly from Blade views. All data retrieval happens in Controllers or Services.
- **Performance:** avoid N+1 queries (use eager loading), unnecessary loops, and missing cache/index opportunities. Check `composer dev` logs (Pail) for query performance during development.
- **Testing:** write or update tests (Unit/Feature) for every feature addition or bug fix. Run `composer test` before committing.

### Security requirements
- **Input validation:** whitelist approach. Never trust raw client data. Use Laravel's validation rules and Form Requests.
- **File uploads:** strict MIME type and extension validation, randomize filenames, reject executable extensions. See existing `ImageHelper` for the pattern.
- **Path traversal:** never allow `../` in file path operations. Validate and sanitize all filesystem operations.
- **SQL injection:** already handled by Eloquent ORM — avoid raw queries. If you must use `DB::raw()`, use parameter binding.
- **Stack traces:** production mode already hides them (via `APP_DEBUG=false` and exception handler).

### Hard prohibitions
Do **not** modify these without explicit instruction:
- `.env` (secrets/config)
- `vendor/`, `node_modules/` (dependencies)
- Already-executed migrations (create new ones instead)
- `composer.json` / `package.json` (dependency manifests)

### Pre-push checklist (only when explicitly asked to push)
1. **Format Code**: Run `./vendor/bin/pint`
2. **Compile CSS**: If CSS/Tailwind classes changed, run `npm run build` exactly once. (Do NOT run build repeatedly during the `npm run dev` coding phase). Skip this if no CSS changes.
3. **Documentation**: Update `CHANGELOG.md` (Keep a Changelog format, bump version, in Indonesian) and `README.md` (if config changed).
4. **Deploy**: `git commit` and `git push`.

### Language & format conventions
- **Code identifiers** (classes, methods, variables, DB tables): **English** only
- **Comments, commit messages, CHANGELOG**: **Indonesian** only
- **Commit messages:** Conventional Commits in Indonesian — `Fitur:`, `Perbaikan:`, `Dokumentasi:`, `Perombakan:`

### UI/Frontend (Tailwind CSS — see DESIGN.md for full spec)
- **Mobile-first:** start with base utilities, add breakpoints (`sm:`, `md:`, `lg:`) for larger screens
- **Theme colors:** use `bg-primary`, `text-primary` — never hardcode hex colors like `bg-[#1abc9c]`
- **Spacing:** use Tailwind's scale (`p-4`, `gap-6`) — no arbitrary values like `p-[17px]`
- **Dark mode:** support `dark:` variants on every visual element
- **Chart colors:** deliberate exception — use **fixed static palettes** (e.g., `emerald` for dataset A, `sky` for B) so data identity survives theme changes
- **Brand/social colors:** deliberate exception — use **official brand colors** (e.g., Facebook blue, YouTube red) for instant recognition

### RBAC
Two effective roles:
- `super_admin` — developer; has backup panel access, can use `/init`, sees fewer Telegram alerts
- `admin_desa` — village operator; normal admin panel access

Gate sensitive operations on `auth()->user()?->hasRole('super_admin')`.
