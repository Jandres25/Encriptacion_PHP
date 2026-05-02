# Changelog

All notable changes to this project are documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

> Note: Entries before `1.3.1` may reference legacy paths (`config/`, `controllers/`, `model/`) that were moved to `app/Config/`, `app/Controller/`, and `app/Model/`.

## [1.4.0] — 2026-05-02

### Added

- **Remember Me** — persistent login via secure cookie:
  - Checkbox "Remember me" on the login form (`views/auth/login.php`)
  - On login with checkbox: generates `bin2hex(random_bytes(32))` token, stores SHA-256 hash in `users.remember_token` with expiry, emits `HttpOnly` / `SameSite=Strict` cookie
  - On every request without an active session: `AuthController::restoreFromCookie()` looks up the token hash and silently restores the session
  - On logout or session expiry: token cleared from DB and cookie deleted from client
  - Controlled by `REMEMBER_ME_ENABLED` and `REMEMBER_ME_TTL` env vars
- **Session Timeout** — automatic expiry after inactivity:
  - `$_SESSION['last_activity']` recorded on login and updated on every protected request
  - `AuthController::checkSessionTimeout()` called in `home.php` and `UserController::requireAuth()` — destroys session and redirects to `/login` with a warning toast if `SESSION_TIMEOUT` seconds have elapsed
  - On timeout: remember token also cleared so cookie-based restore does not immediately re-log the user in
  - Controlled by `SESSION_TIMEOUT` env var (default 1800 s = 30 min)
- New columns in `users` table: `remember_token VARCHAR(64) NULL`, `remember_token_expires DATETIME NULL`, index `idx_remember_token`
- New model methods in `App\Model\User`: `setRememberToken()`, `getByRememberToken()`, `clearRememberToken()`
- New env vars: `REMEMBER_ME_ENABLED`, `REMEMBER_ME_TTL`, `SESSION_TIMEOUT`
- Migration script: `database/migrations/2026_05_02_add_remember_me_to_users.sql` (idempotent ALTER TABLE for existing installations)
- `.remember-label` CSS class in `public/css/style.css` for styled checkbox label in auth forms

### Changed

- `session_start()` moved from `public/index.php` to `app/Config/autoload.php` so it runs before `restoreFromCookie()` on every request
- `app/Config/autoload.php` now requires `AuthController.php` and calls `restoreFromCookie()` after session start

---

## [1.3.1] — 2026-04-24

### Changed

- Reorganized project structure under `app/`:
  - `config/` → `app/Config/`
  - `controllers/` → `app/Controller/`
  - `model/` → `app/Model/`
- Updated front controller routing in `public/index.php` to load delegators from `app/Controller/*`.
- Updated relative paths after the directory move (autoload, views, cache path, PHPMailer includes, and model includes).
- Updated project documentation to reflect the new `app/` structure.

## [1.3.0] — 2026-04-23

### Added

- SweetAlert2 toast notification system for all CRUD and authentication actions:
  - Centralized notification logic in `views/layouts/messages.php`
  - Integrated `sweetalert2.all.min.js` in all views (protected and standalone)
  - Welcome toast message upon successful login with user's first name
- Unified session-based notification keys: `$_SESSION['message']` and `$_SESSION['icon']`

### Changed

- Refactored `UserController` and `AuthController` to use the new session-based toast system:
  - Removed reliance on URL query parameters (`?message=`, `?error=`) for feedback
  - Replaced legacy `$_SESSION['flash_error']` / `$_SESSION['flash_message']` with unified keys
- Cleaned up views (`views/user/index.php`, `create.php`, `edit.php`) by removing manual alert display blocks
- Updated `AuthController::logout` to include a success notification
- User delete confirmation in `views/user/index.php` now uses SweetAlert2 via `public/js/users-delete.js` instead of per-row Bootstrap modals

### Fixed

- Improved user feedback consistency across all modules (Login, Reset Password, User Management)

## [1.2.2] — 2026-04-22

### Added

- File-based cache infrastructure:
  - `libs/Cache/FileCache.php` (get/set/forget/remember with TTL)
  - `config/cache.php` (`appCache()` helper)
  - `storage/cache/.gitignore` for runtime cache files
- Environment settings for cache control:
  - `CACHE_ENABLED`
  - `CACHE_TTL_USERS`
- Apache rewrite support for clean URLs via `.htaccess`
- Shared rendering helpers in `config/view_helpers.php`:
  - `renderView()`
  - `renderProtectedView()`
- New protected-layout assets:
  - `public/css/layout-protected.css`
  - `public/js/users-table.js`

### Changed

- `model/User.php` now caches `getAll()` user listing with key `users.all`
- Cache invalidation added on user writes (`create`, `update`, `delete`, `updatePassword`)
- `config/autoload.php` now loads cache bootstrap before DB usage
- `.gitignore` updated to ignore runtime cache files (`storage/cache/*.cache`)
- `index.php` route resolution prioritizes clean path-based URLs (fallback from `REQUEST_URI`) instead of relying only on `?page=`
- Protected views now render through `renderProtectedView()` in `UserController` (centralized header/footer include)
- Shared templates moved from project-root `templates/` to `views/templates/`
- DataTables setup for users list moved from inline footer script to `public/js/users-table.js`

### Fixed

- Prevented HTTP 500 on `/users` when cache directory is not writable:
  - cache now falls back to disabled mode for the request
  - warning is logged instead of throwing a fatal runtime exception

---

## [1.2.1] — 2026-03-23

### Fixed

- Login POST check: changed `!empty($_POST['btningresar'])` to `isset()` — `<button>` without a `value` attribute submits an empty string, which `!empty()` rejects
- Error and success messages now use session flash (`$_SESSION['flash_error']` / `$_SESSION['flash_message']`) instead of URL query params — messages disappear on page refresh and the URL stays clean
- Flash message blocks moved inside `<form>` in all auth views so they render within the form's 360px width instead of beside it as flex siblings

### Changed

- `<input type="submit">` replaced with `<button type="submit">` in `login.php`, `forgot_password.php`, and `reset_password.php`
- Added `.btn-anchor` class in `public/css/style.css` for `<a>` elements styled as buttons — provides `line-height: 40px` and `text-align: center` without affecting native `<button>` elements
- Seed passwords corrected to known values: Admin/Luca/Martins/Gus → `123456`; Juan/Sofy/Mary → `0000`
- Default admin credentials documented in `README.md` and `database/seeds.sql`

---

## [1.2.0] — 2026-03-23

### Added

- CSS variables `--color-dark` (`#142e3d`) and `--color-accent` (`#04a1fc`) in `public/css/estilo.css` for a consistent color palette across all views
- Utility classes in `estilo.css`: `.btn-app-primary`, `.hero`, `.feature-icon`, `body.dashboard`

### Changed

- Dashboard (`views/index.php`) redesigned: replaced carousel and placeholder content with a hero section and three feature cards describing the project's security capabilities
- Hero gradient simplified to use only palette tokens (`--color-dark` → `--color-accent`), eliminating the off-palette intermediate color
- Navbar and card headers now render in navy `#142e3d` instead of Bootstrap's default `#343a40` via CSS override
- Body background changed from `rgb(218,216,216)` to `#f8f9fa` (Bootstrap light gray)
- FontAwesome migrated from SVG/JS bundle (`fontawesome.js`) to CSS + webfonts (`all.min.css`)
- Dashboard inline `<style>` block extracted to `estilo.css`; `<body>` gets `class="dashboard"` to scope the flex layout

### Removed

- Unused public assets: `public/css/fontawesome.min.css`, `public/js/fontawesome.js`, `public/js/bootstrap.bundle.js`, `public/js/bootstrap.js`, `public/DataTables/datatables.min.css`, `public/DataTables/datatables.min.js`, `public/img/1.jpg`, `public/img/bg.svg`

---

## [1.1.0] — 2026-03-22

### Changed

- Introduced `AuthController` (`controllers/auth/AuthController.php`, namespace `App\Controller\Auth`) with methods `login()`, `logout()`, `forgotPassword()`, `resetPassword()`
- Introduced `UserController` (`controllers/user/UserController.php`, namespace `App\Controller\User`) with methods `index()`, `create()`, `edit()`, `delete()` and private guards `requireAuth()` / `requireAdmin()`
- Individual action files (`login.php`, `reset.php`, etc.) are now thin delegators that instantiate the module controller and call the corresponding method — all logic lives in the controller class

---

## [1.0.0] — 2026-03-20

### Added

- Front controller (`index.php`) routing all pages via `?page=` query parameter — no more scattered entry-point files at root
- `controllers/auth/` — login, logout, reset, update_password (each handles GET + POST)
- `controllers/user/` — index, create, edit, delete (admin-only CRUD)
- `controllers/home.php` — dashboard controller
- `model/User.php` — OOP model (`App\Model\User` namespace) with MySQLi prepared statements for all user operations
- `database/schema.sql` — canonical DB schema with English table/column names (`users`, `password_resets`)
- `database/seeds.sql` — sample data with bcrypt-hashed passwords
- `public/` directory consolidating all static assets (CSS, JS, images, DataTables, webfonts)
- `libs/PHPMailer/` — PHPMailer moved from `PHPMailer-master/` to `libs/`
- `views/auth/` — login, forgot_password, reset_password (pure HTML, no logic)
- `views/user/` — index, create, edit (pure HTML, no logic)
- `views/index.php` — dashboard view

### Changed

- Translated entire codebase to English: directories, filenames, PHP variables, session keys, HTML text, and DB schema
- Session keys: `$_SESSION["ID"]` → `$_SESSION['user_id']`, `$_SESSION["Nombre"]` → `$_SESSION['name']`, `$_SESSION["EsAdmin"]` → `$_SESSION['is_admin']`
- DB table `usuario` → `users`; columns `Nombres/Apellidos/correo/Usuario/Clave/EsAdmin` → `first_name/last_name/email/username/password/is_admin`
- `$conexion` → `$connection` in `config/database.php`
- PHPMailer reset link now points to `/?page=reset-password&token=...` instead of `reset_password.php?token=...`
- `templates/header.php` no longer calls `session_start()` (front controller handles it); redirect updated to `/?page=login`
- All form actions and nav links updated to use `/?page=...` URLs

### Fixed

- SQL injection in login: replaced string interpolation with MySQLi prepared statement (via `User::getByUsername()`)
- `window.location` JS redirects in password reset replaced with `header()` + `exit`
- Bug in `update_password`: `$stmt->close()` was called on variables that didn't exist in the `else` branch — fixed by scoping `close()` inside each branch

### Removed

- `login.php`, `forgot_password.php`, `reset_password.php` from project root (logic moved to `controllers/auth/`, views to `views/auth/`)
- `controlador/` directory (all Spanish legacy controllers)
- `model/conexion.php` (replaced by `config/database.php` + `model/User.php`)
- `model/usuario/` directory (replaced by `controllers/user/` + `views/user/` + `model/User.php`)
- `login.sql` from project root (replaced by `database/schema.sql`)
- `PHPMailer-master/` (moved to `libs/PHPMailer/`)
- `css/`, `js/`, `img/`, `webfonts/`, `DataTables/` from root (moved to `public/`)

---

## [0.1.0] — Previous release

### Added

- `config/` directory with separation of concerns:
  - `config/config.php` — loads `.env` with `loadEnv()` + `env()`, defines `APP_URL` and `$url`
  - `config/database.php` — MySQLi connection using `env()`
  - `config/autoload.php` — single bootstrap entry point
- Environment variables `APP_URL` and `APP_TIMEZONE` in `.env` and `.env.example`
- `email` and `is_admin` fields in create/edit user forms
- UNIQUE constraints on `email` and `username` columns

### Changed

- All asset paths use `APP_URL` constant instead of fragile relative paths
- All redirects in controllers use `APP_URL`
- User CRUD converted to MySQLi prepared statements
- Editing a user with a blank password field keeps the current password

### Fixed

- Password recovery email not sending: `ENCRYPTION_SMTPS` on port 587 corrected to `ENCRYPTION_STARTTLS`
- Session security vulnerability: session variables now set only after successful `password_verify()`
- `reset_password.php` PHP warning: `$_GET['token']` accessed without `isset()` — fixed with `??` operator
- Silent failure on user creation: missing `email`/`is_admin` columns in INSERT
- Missing `exit` after `header()` redirects in user module
