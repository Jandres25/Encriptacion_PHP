# Changelog

All notable changes to this project are documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

## [Unreleased]

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

## [1.0.0] — Previous release

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
