# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP web application implementing a secure authentication system with bcrypt password hashing and email-based password recovery via PHPMailer.

- **Server:** XAMPP (Apache + MySQL) at `http://localhost/Encriptacion_PHP/`
- **PHP:** >= 8.2
- **Database:** MySQL/MariaDB (import `database/schema.sql` to initialize)
- **Dependencies:** PHPMailer (bundled in `libs/PHPMailer/`)

## Setup

```bash
# 1. Copy environment config
cp .env.example .env
# Edit .env with your DB credentials and SMTP settings

# 2. Import database schema
mysql -u root -p < database/schema.sql

# 3. (Optional) Load seed data
mysql -u root -p < database/seeds.sql

# 4. Start XAMPP Apache and MySQL services
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

APP_URL=http://localhost/Encriptacion_PHP
APP_TIMEZONE=America/Bogota

CACHE_ENABLED=true
CACHE_TTL_USERS=60
```

The `.env` file is loaded by `loadEnv()` in `app/Config/config.php`, which also defines the `APP_URL` constant and `$url` variable. There is no Composer autoload for dotenv.

## Architecture

### Request Flow

```
Browser → public/index.php (front controller) → app/Controller/auth/*.php or app/Controller/user/*.php
       → AuthController / UserController → views/*.php
```

`public/index.php` supports clean URLs and dispatches to the matching thin delegator file. It first uses `$_GET['page']` when present, otherwise resolves from `REQUEST_URI` (stripping the app base path). Each delegator instantiates the module's controller class and calls the corresponding method, which handles GET (render view) and POST (process form).

### URL Scheme

| URL                         | Controller                             |
| --------------------------- | -------------------------------------- |
| `/login`                    | `app/Controller/auth/login.php`           |
| `/logout`                   | `app/Controller/auth/logout.php`          |
| `/forgot-password`          | `app/Controller/auth/reset.php`           |
| `/reset-password?token=...` | `app/Controller/auth/update_password.php` |
| `/` (default)               | `app/Controller/home.php`                 |
| `/users`                    | `app/Controller/user/index.php`           |
| `/users/create`             | `app/Controller/user/create.php`          |
| `/users/edit?id=X`          | `app/Controller/user/edit.php`            |
| `/users/delete?id=X`        | `app/Controller/user/delete.php`          |

### Key Files

| Path                                                    | Purpose                                                                                                                                   |
| ------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| `public/index.php`                                      | Front controller — loads autoload, starts session, dispatches by path                                                                     |
| `public/.htaccess`                                      | Apache rewrite rules: non-file/non-directory requests route to `public/index.php`                                                         |
| `app/Config/config.php`                                 | Loads `.env` with `loadEnv()` + `env()`; defines `APP_URL` constant and `$url`                                                            |
| `app/Config/database.php`                               | Creates `$connection` MySQLi using `env()` helper                                                                                         |
| `app/Config/view_helpers.php`                           | Shared render helpers: `renderView()` and `renderProtectedView()`                                                                         |
| `app/Config/cache.php`                                  | Cache bootstrap; exposes `appCache()` and graceful fallback when cache directory is not writable                                          |
| `app/Config/autoload.php`                               | Bootstrap entry point; includes view helpers + cache + database                                                                           |
| `app/Model/User.php`                                    | OOP model (`App\Model\User`); all DB queries via prepared statements                                                                      |
| `app/Controller/auth/AuthController.php`                | `App\Controller\Auth\AuthController` — all auth logic: `login()`, `logout()`, `forgotPassword()`, `resetPassword()`                       |
| `app/Controller/auth/login.php`                         | Thin delegator → `AuthController::login()`                                                                                                |
| `app/Controller/auth/logout.php`                        | Thin delegator → `AuthController::logout()`                                                                                               |
| `app/Controller/auth/reset.php`                         | Thin delegator → `AuthController::forgotPassword()`                                                                                       |
| `app/Controller/auth/update_password.php`               | Thin delegator → `AuthController::resetPassword()`                                                                                        |
| `app/Controller/home.php`                               | Auth check, sets `$name`/`$isAdmin`/`$year`, includes dashboard view                                                                      |
| `app/Controller/user/UserController.php`                | `App\Controller\User\UserController` — all user CRUD logic + `requireAuth()` / `requireAdmin()` guards                                    |
| `app/Controller/user/index.php`                         | Thin delegator → `UserController::index()`                                                                                                |
| `app/Controller/user/create.php`                        | Thin delegator → `UserController::create()`                                                                                               |
| `app/Controller/user/edit.php`                          | Thin delegator → `UserController::edit()`                                                                                                 |
| `app/Controller/user/delete.php`                        | Thin delegator → `UserController::delete()`                                                                                               |
| `views/layouts/header.php` / `views/layouts/footer.php` | Shared nav/footer for protected pages (DataTables, SweetAlert2)                                                                           |
| `views/layouts/messages.php`                            | Centralized SweetAlert2 toast notification logic                                                                                          |
| `views/home/index.php`                                  | Dashboard — hero + feature cards; receives `$name`, `$isAdmin`, `$year` from `app/Controller/home.php`                                    |
| `public/css/estilo.css`                                 | Global styles + CSS palette variables (`--color-dark`, `--color-accent`); loaded by `views/layouts/header.php` and `views/home/index.php` |
| `public/css/layout-protected.css`                       | Shared full-height layout styles for protected pages (`body` flex + footer push)                                                          |
| `public/js/users-table.js`                              | DataTables initialization for `views/user/index.php`                                                                                      |
| `public/js/users-delete.js`                             | SweetAlert2 delete confirmation for user actions in `views/user/index.php`                                                                |
| `database/schema.sql`                                   | Current DB schema — `users` + `password_resets` tables                                                                                    |
| `database/seeds.sql`                                    | Sample users with bcrypt-hashed passwords                                                                                                 |

### Session Variables

Set on login (only after successful `password_verify()`), required for all protected pages:

- `$_SESSION['user_id']` — user ID
- `$_SESSION['name']` — display name (first_name)
- `$_SESSION['is_admin']` — boolean, controls admin menu visibility

Flash notifications (used by `views/layouts/messages.php`):

- `$_SESSION['message']` — The message text to display in the toast
- `$_SESSION['icon']` — The SweetAlert2 icon type (`success`, `error`, `warning`, `info`)

### Database Tables

- **users**: `id, first_name, last_name, email, username, password` (bcrypt), `is_admin (DEFAULT 0)`
  - `email` and `username` have UNIQUE constraints
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

### DataTables

`public/DataTables/datatables.js` (combined bundle only). `datatables.min.css` and `datatables.min.js` were removed.

### Templates vs standalone views

- **Protected pages** (`views/user/`) use `views/layouts/header.php` + `views/layouts/footer.php` — includes all shared assets and DataTables init script
- **Auth views** (`views/auth/`) and **dashboard** (`views/home/index.php`) are standalone — they include their own `<head>` assets directly

### Cache

- Cache implementation: `libs/Cache/FileCache.php` + `app/Config/cache.php`
- Cached query: users listing (`App\Model\User::getAll()`) with key `users.all`
- TTL control: `CACHE_TTL_USERS` (seconds)
- Enable/disable: `CACHE_ENABLED=true|false`
- Invalidation occurs on `create`, `update`, `delete`, and `updatePassword` in `app/Model/User.php`
- Runtime files are stored in `storage/cache/*.cache`
- If the cache directory is not writable, cache is disabled for the request and a warning is logged (no HTTP 500)

## Security Patterns

- Passwords hashed with `password_hash($pass, PASSWORD_DEFAULT)` (bcrypt)
- Session variables assigned only after successful `password_verify()` — never on failed login
- Reset tokens: `bin2hex(random_bytes(32))` — 256-bit, 1-hour expiry, single-use (`used = 1` after consumption)
- All DB queries in `app/Model/User.php` use MySQLi prepared statements
- Password resets use MySQLi prepared statements
- Email sanitized with `filter_var($email, FILTER_SANITIZE_EMAIL)` before DB queries
- SMTP uses STARTTLS encryption (port 587)

## Notes

- PHPMailer is included directly from `libs/PHPMailer/src/` — not via Composer autoload
- All asset paths (CSS, JS, images) use the `APP_URL` constant via `<?= APP_URL ?>` short-tag syntax
- `AuthController` namespace is `App\Controller\Auth`; `UserController` namespace is `App\Controller\User`
- `use App\Model\User;` inside controller classes is file-scoped and works correctly when the class file is `require_once`'d by the thin delegator
- `views/home/index.php` uses `class="dashboard"` on `<body>` to activate the flex column layout defined in `estilo.css`
- Error/success messages use unified session flash: `$_SESSION['message']` and `$_SESSION['icon']`. Rendered via `views/layouts/messages.php`. Never pass them via URL query params
- Auth views use `<button type="submit">` (not `<input type="submit">`); POST detection uses `isset($_POST['btnXXX'])` — not `!empty()` — since `<button>` without a `value` attribute submits an empty string
- `.btn-anchor` in `public/css/style.css` — apply alongside `.btn` on `<a>` elements for correct vertical centering; do not rely on the `a.btn` selector
- User delete flow in `views/user/index.php` uses `.js-delete-user` buttons with `data-delete-url`, `data-name`, and `data-username`; confirmation is handled in `public/js/users-delete.js` (no Bootstrap delete modals)
