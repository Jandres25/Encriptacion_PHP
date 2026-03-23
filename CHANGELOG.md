# Changelog

All notable changes to this project are documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

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
