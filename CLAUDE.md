# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Custom PHP MVC authentication system built with Composer, a lightweight router, and role-based access control.

- **Server:** XAMPP (Apache + MySQL) at `http://localhost/Encriptacion_PHP/public`
- **PHP:** >= 8.2
- **Database:** MySQL/MariaDB (import `database/schema.sql` to initialize)
- **Dependencies:** Composer — `phpmailer/phpmailer`, `vlucas/phpdotenv`, `phpunit/phpunit` (dev)

## Setup

```bash
# 1. Install dependencies
composer install

# 2. Copy environment config
cp .env.example .env
# Edit .env with your DB credentials and SMTP settings

# 3. Import database schema
mysql -u root -p < database/schema.sql

# 4. (Optional) Load seed data
mysql -u root -p < database/seeds.sql

# 5. Start XAMPP Apache and MySQL services
```

No build step needed — PHP files are served directly by Apache.

## Environment Variables (.env)

```
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=login

SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your@email.com
SMTP_PASSWORD=your_app_password
SMTP_PORT=587

APP_URL=http://localhost/Encriptacion_PHP/public
APP_TIMEZONE=America/Bogota
APP_VERSION=1.6.0

CACHE_ENABLED=true
CACHE_TTL_USERS=60

REMEMBER_ME_ENABLED=true
REMEMBER_ME_TTL=2592000

SESSION_TIMEOUT=1800
```

The `.env` file is loaded by `vlucas/phpdotenv` in `app/Config/config.php`, which also defines the `APP_URL` constant and the `env()` helper.

- `APP_VERSION` — displayed in the shared footer
- `REMEMBER_ME_ENABLED` — enable/disable persistent login via cookie (default `true`)
- `REMEMBER_ME_TTL` — cookie + token lifetime in seconds (default `2592000` = 30 days)
- `SESSION_TIMEOUT` — inactivity expiry in seconds (default `1800` = 30 min)

## Architecture

### Request Flow

```
Browser → public/index.php → App\Core\Router → Controller::method()
       → App\Core\Controller::render() → views/layouts/header.php + view + views/layouts/footer.php
```

`public/index.php` bootstraps autoload, creates the router with the DB connection, loads `routes/web.php`, and dispatches by HTTP method + URI path.

### URL Scheme

| URL                         | Controller method                  |
| --------------------------- | ---------------------------------- |
| `/`                         | `HomeController::index()`          |
| `/login`                    | `AuthController::login()`          |
| `/logout`                   | `AuthController::logout()`         |
| `/forgot-password`          | `AuthController::forgotPassword()` |
| `/reset-password?token=...` | `AuthController::resetPassword()`  |
| `/users`                    | `UserController::index()`          |
| `/users/create`             | `UserController::create()`         |
| `/users/edit?id=X`          | `UserController::edit()`           |
| `/users/delete?id=X`        | `UserController::delete()`         |

### Key Files

| Path                                | Purpose                                                                                                                        |
| ----------------------------------- | ------------------------------------------------------------------------------------------------------------------------------ |
| `public/index.php`                  | Front controller — bootstraps autoload, router, and dispatches requests                                                        |
| `public/.htaccess`                  | Apache rewrite rules: non-file/non-directory requests route to `public/index.php`                                              |
| `routes/web.php`                    | All route definitions — GET/POST mapped to controller methods                                                                  |
| `app/Config/config.php`             | Loads `.env` via phpdotenv; defines `APP_URL` constant and `env()` helper                                                      |
| `app/Config/database.php`           | `App\Config\Database` singleton — `Database::getConnection()` returns the shared `\mysqli` instance                            |
| `app/Config/cache.php`              | Cache bootstrap; exposes `appCache()` with graceful fallback when cache dir is not writable                                    |
| `app/Config/autoload.php`           | Bootstrap entry point: sets timezone, loads cache + database, starts session, calls `Auth::restoreFromCookie()`                |
| `app/Core/Router.php`               | `App\Core\Router` — registers GET/POST routes, strips APP_URL base path, dispatches to controller                              |
| `app/Core/Controller.php`           | Abstract base — `render(string $view, array $data, bool $protected)` and `redirect(string $path)`                              |
| `app/Core/Model.php`                | Abstract base — holds `protected \mysqli $db`                                                                                  |
| `app/Core/Auth.php`                 | `App\Core\Auth` — credential verify, remember-me token issue/consume/clear, password reset tokens, `restoreFromCookie()`       |
| `app/Middleware/AuthMiddleware.php` | Static guards: `auth()`, `admin()`, `timeout(\mysqli)`                                                                         |
| `app/Controller/AuthController.php` | All auth logic: login, logout, forgotPassword, resetPassword                                                                   |
| `app/Controller/HomeController.php` | Dashboard: applies timeout + auth middleware, renders home view                                                                |
| `app/Controller/UserController.php` | Full user CRUD — guarded by `admin()` middleware                                                                               |
| `app/Model/User.php`                | `App\Model\User` — all DB queries via MySQLi prepared statements                                                               |
| `app/Service/MailerService.php`     | PHPMailer encapsulation — SMTP via STARTTLS                                                                                    |
| `views/layouts/header.php`          | Shared `<head>` + nav for all protected pages; accepts `$pageTitle`, `$favicon`, `$bodyClass`, `$useDataTables`, `$pageStyles` |
| `views/layouts/footer.php`          | Shared footer with version; accepts `$useDataTables`, `$pageScripts`                                                           |
| `views/layouts/messages.php`        | Centralized SweetAlert2 toast notification logic                                                                               |
| `views/home/index.php`              | Dashboard content only (hero + feature cards) — wrapped by shared layout via `protected: true`                                 |
| `views/auth/`                       | Standalone auth views (login, forgot-password, reset-password) — include their own `<head>`                                    |
| `views/user/`                       | Protected user CRUD views — wrapped by shared layout                                                                           |
| `public/css/estilo.css`             | Global styles + CSS palette variables (`--color-dark`, `--color-accent`)                                                       |
| `public/css/layout-protected.css`   | Full-height flex layout for protected pages                                                                                    |
| `public/js/users-table.js`          | DataTables initialization — loaded only in `UserController::index()` via `pageScripts`                                         |
| `public/js/users-delete.js`         | SweetAlert2 delete confirmation — loaded only in `UserController::index()` via `pageScripts`                                   |
| `database/schema.sql`               | Current DB schema — `users` + `password_resets` tables                                                                         |
| `database/seeds.sql`                | Sample users with bcrypt-hashed passwords                                                                                      |

### Session Variables

Set on login (only after successful `password_verify()`), required for all protected pages:

- `$_SESSION['user_id']` — user ID
- `$_SESSION['name']` — display name (first_name)
- `$_SESSION['is_admin']` — boolean, controls admin menu visibility
- `$_SESSION['last_activity']` — Unix timestamp; updated on every request; used by `AuthMiddleware::timeout()` to enforce inactivity expiry

Flash notifications (rendered by `views/layouts/messages.php`):

- `$_SESSION['message']` — toast message text
- `$_SESSION['icon']` — SweetAlert2 icon type (`success`, `error`, `warning`, `info`)

### Database Tables

- **users**: `id, first_name, last_name, email, username, password` (bcrypt), `is_admin (DEFAULT 0)`, `remember_token` (sha256 hash, nullable), `remember_token_expires` (datetime, nullable)
  - `email` and `username` have UNIQUE constraints; `remember_token` has an index (`idx_remember_token`)
- **password_resets**: `id, email, token, created_at, expires_at, used`

## Frontend / Assets

### Color Palette

Defined as CSS variables in `public/css/estilo.css`:

- `--color-accent: #04a1fc` — blue (buttons, gradient end, hover)
- `--color-dark: #142e3d` — navy (navbars, card headers, gradient start)

Utility classes: `.btn-app-primary` (accent button with hover), `.hero` (full-height gradient section), `.feature-icon` (circular icon container), `body.dashboard` (flex column layout for dashboard).

### FontAwesome

Use only `public/css/all.min.css` (CSS + webfonts in `public/webfonts/`). The JS version (`fontawesome.js`) was removed — do not re-add it.

### Bootstrap

`public/css/bootstrap.css` + `public/js/bootstrap.min.js` + `public/js/popper.min.js`. `bootstrap.bundle.js` and `bootstrap.js` were removed.

**Load order:** `bootstrap.css` must be loaded before `estilo.css` so that `.btn-app-primary` overrides Bootstrap's `.btn` defaults correctly.

### DataTables

Loaded **only** on `views/user/index.php` via `$useDataTables = true` (CSS in header) and the DataTables JS bundle in footer. App-specific scripts (`users-table.js`, `users-delete.js`) are passed via `$pageScripts` in `UserController::index()`.

### Per-page assets in layouts

`header.php` and `footer.php` support opt-in per-page assets:

- `$pageStyles` — array of CSS paths (relative to `APP_URL`) injected in `<head>` after DataTables CSS
- `$pageScripts` — array of JS paths (relative to `APP_URL`) injected in footer after DataTables JS
- `$useDataTables` — bool (default `false`) — enables DataTables CSS + JS bundle
- `$pageTitle` — string (default `'SecureAuth'`) — browser tab title
- `$favicon` — filename in `public/img/` (default `'usuario.png'`)
- `$bodyClass` — string added to `<body class="...">` (e.g. `'dashboard'`); also suppresses `mt-3` on `<main>`

### Cache

- Implementation: `libs/Cache/FileCache.php` + `app/Config/cache.php`
- Cached query: users listing (`App\Model\User::getAll()`) with key `users.all`
- TTL: `CACHE_TTL_USERS` (seconds)
- Enable/disable: `CACHE_ENABLED=true|false`
- Invalidation: on `create`, `update`, `delete`, `updatePassword` in `app/Model/User.php`
- Runtime files: `storage/cache/*.cache`
- If the cache directory is not writable, cache is disabled for the request and a warning is logged (no HTTP 500)

## Security Patterns

- Passwords hashed with `password_hash($pass, PASSWORD_DEFAULT)` (bcrypt)
- Session variables assigned only after successful `password_verify()` — never on failed login
- Reset tokens: `bin2hex(random_bytes(32))` — 256-bit, 1-hour expiry, single-use (`used = 1` after consumption)
- All DB queries in `app/Model/User.php` use MySQLi prepared statements
- Email sanitized with `filter_var($email, FILTER_SANITIZE_EMAIL)` before DB queries
- SMTP uses STARTTLS encryption (port 587)
- Remember-me tokens: `bin2hex(random_bytes(32))` stored raw in cookie; SHA-256 hash stored in DB. Cookie is `HttpOnly`, `SameSite=Strict`, `Secure` on HTTPS. TTL controlled by `REMEMBER_ME_TTL`. Cleared on logout and on session expiry
- Session timeout: `AuthMiddleware::timeout()` called on every protected request; destroys session + clears remember cookie if `SESSION_TIMEOUT` seconds of inactivity exceeded

## Testing

### Stack

- **PHPUnit ^11.0** — integration tests against a real MySQL database (`login_test`)
- **28 tests total:** 14 in `tests/Unit/UserTest.php` (User model), 14 in `tests/Integration/AuthTest.php` (Auth class)
- **CI:** `.github/workflows/tests.yml` — runs on push/PR to `master` with a MySQL 8.0 service

### Running tests locally

```bash
# Prerequisites: create login_test DB and import schema
mysql -u root -p -e "CREATE DATABASE login_test;"
mysql -u root -p login_test < database/schema_test.sql

# Create .env.testing with DB_DATABASE=login_test (never login)
# Then run:
composer test              # full suite
composer test:unit         # User model only
composer test:integration  # Auth class only
```

### Key conventions

- **Never mock `\mysqli`** — all tests hit real MySQL
- **Never load `app/Config/autoload.php`** in tests — it starts a session, reads cookies, and connects the DB singleton
- `tests/bootstrap.php` populates `$_ENV` via `parse_ini_file('.env.testing')` before Composer autoload (so `config.php` picks up test vars when it runs as part of `autoload.files`)
- `tests/TestCase.php` creates a direct `\mysqli` connection — does NOT use `App\Config\Database` singleton
- Tables are truncated in `setUp()` per test; schema is applied once per process via a static flag
- **Safeguard:** `TestCase` throws if `DB_DATABASE === 'login'` to prevent running against production DB
- Timezone-sensitive date comparisons use `DATE_SUB(NOW(), INTERVAL X HOUR)` in SQL — never PHP-computed timestamps — to avoid PHP/MySQL timezone drift
- `CACHE_ENABLED=false` in `.env.testing` and forced via `phpunit.xml` `<env>` — `appCache()` short-circuits before checking directory writability

### Files

| Path | Purpose |
|---|---|
| `phpunit.xml` | PHPUnit 11 config — suites, bootstrap, env overrides |
| `.env.testing` | Test environment vars (gitignored) |
| `database/schema_test.sql` | Table-only schema for `login_test` (no `CREATE DATABASE`) |
| `tests/bootstrap.php` | Minimal bootstrap — `.env.testing` → `$_ENV`, then autoload |
| `tests/TestCase.php` | Abstract base — connection, schema, truncate, `createUser()` |
| `tests/Unit/UserTest.php` | 14 tests for `App\Model\User` |
| `tests/Integration/AuthTest.php` | 14 tests for `App\Core\Auth` |
| `.github/workflows/tests.yml` | GitHub Actions CI workflow |

## Notes

- PHPMailer is loaded via Composer autoload (`phpmailer/phpmailer`)
- All asset paths use the `APP_URL` constant via `<?= APP_URL ?>` short-tag syntax
- `App\Config\Database::getConnection()` is a singleton — the same `\mysqli` instance is reused across all controllers and models within a request
- `views/home/index.php` contains only content markup (no `<html>`/`<head>`/`<body>`) — it is wrapped by the shared layout via `Controller::render(..., protected: true)`
- Error/success messages use unified session flash: `$_SESSION['message']` and `$_SESSION['icon']`. Rendered via `views/layouts/messages.php`. Never pass them via URL query params
- Auth views use `<button type="submit">` (not `<input type="submit">`); POST detection uses `isset($_POST['btnXXX'])` — not `!empty()` — since `<button>` without a `value` attribute submits an empty string
- User delete flow uses `.js-delete-user` buttons with `data-delete-url`, `data-name`, `data-username`; confirmation handled in `public/js/users-delete.js`
- `session_start()` is called in `app/Config/autoload.php`; `Auth::restoreFromCookie()` runs immediately after on every request
