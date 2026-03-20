# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP web application implementing a secure authentication system with bcrypt password hashing and email-based password recovery via PHPMailer.

- **Server:** XAMPP (Apache + MySQL) at `http://localhost/Encriptacion_PHP/`
- **Database:** MySQL (import `login.sql` to initialize)
- **Dependencies:** PHPMailer (bundled in `PHPMailer-master/`)

## Setup

```bash
# 1. Copy environment config
cp .env.example .env
# Edit .env with your DB credentials and SMTP settings

# 2. Import database schema
mysql -u root -p < login.sql

# 3. Start XAMPP Apache and MySQL services
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
```

The `.env` file is loaded by `loadEnv()` in `config/config.php`, which also defines the `APP_URL` constant and `$url` variable used across all views and controllers. There is no Composer autoload for dotenv.

## Architecture

### Request Flow

```
Browser → PHP view file → include controlador/*.php (on POST) → config/autoload.php
```

The app follows a simplified MVC pattern without a router — each view file directly includes its controller on form submission.

### Key Files

| Path | Purpose |
|------|---------|
| `config/config.php` | Loads `.env` with `loadEnv()` + `env()`; defines `APP_URL` constant and `$url` |
| `config/database.php` | Creates `$conexion` MySQLi using `env()` helper |
| `config/autoload.php` | Single bootstrap entry point; includes `database.php` |
| `model/conexion.php` | Delegates to `config/autoload.php` for backward compatibility with existing includes |
| `controlador/controlador_login.php` | Validates credentials with `password_verify()`, sets session only on success |
| `controlador/controlador_reset.php` | Generates 64-char hex token, stores in `password_resets`, sends email via PHPMailer |
| `controlador/controlador_update_password.php` | Validates token expiry/used flag, hashes new password with `password_hash()` |
| `controlador/controlador_cerrar_session.php` | Destroys session, redirects to login |
| `model/usuario/index.php` | Admin-only user list (DataTables) with delete |
| `model/usuario/crear_usuario.php` | User creation with bcrypt hashing, correo and EsAdmin fields |
| `model/usuario/editar_usuario.php` | User editing; password field optional (keep current if blank) |
| `templates/header.php` / `templates/footer.php` | Shared nav/footer included in all protected pages |

### Session Variables

Set on login (only after successful `password_verify()`), required for all protected pages:

- `$_SESSION["ID"]` — user ID
- `$_SESSION["Nombre"]` — display name
- `$_SESSION["EsAdmin"]` — boolean, controls admin menu visibility

### Database Tables

- **usuario**: `ID, Nombres, Apellidos, correo (DEFAULT ''), Usuario, Clave` (bcrypt), `EsAdmin (DEFAULT 0)`
  - `correo` and `Usuario` have UNIQUE constraints
- **password_resets**: `id, email, token, created_at, expires_at, used`

## Security Patterns

- Passwords hashed with `password_hash($pass, PASSWORD_DEFAULT)` (bcrypt)
- Session variables assigned only after successful `password_verify()` — never on failed login
- Reset tokens: `bin2hex(random_bytes(32))` — 256-bit, 1-hour expiry, single-use (`used = 1` after consumption)
- All CRUD queries in `model/usuario/` use MySQLi prepared statements
- Password resets and updates use MySQLi prepared statements
- Email sanitized with `filter_var($email, FILTER_SANITIZE_EMAIL)` before DB queries
- SMTP uses STARTTLS encryption (port 587)

## Notes

- PHPMailer is included directly from `PHPMailer-master/src/` — not via Composer autoload
- All asset paths (CSS, JS, images) use the `APP_URL` constant via `<?= APP_URL ?>` short-tag syntax
