# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP web application implementing a secure authentication system with bcrypt password hashing and email-based password recovery via PHPMailer.

- **Server:** XAMPP (Apache + MySQL) at `http://localhost/Encriptacion_PHP/`
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
```

The `.env` file is loaded by `loadEnv()` in `config/config.php`, which also defines the `APP_URL` constant and `$url` variable. There is no Composer autoload for dotenv.

## Architecture

### Request Flow

```
Browser → index.php (front controller) → controllers/*.php → views/*.php
```

`index.php` reads `$_GET['page']` and dispatches to the matching controller. Each controller handles both GET (render view) and POST (process form), then includes its view or redirects.

### URL Scheme

| URL | Controller |
|-----|-----------|
| `/?page=login` | `controllers/auth/login.php` |
| `/?page=logout` | `controllers/auth/logout.php` |
| `/?page=forgot-password` | `controllers/auth/reset.php` |
| `/?page=reset-password&token=...` | `controllers/auth/update_password.php` |
| `/` (default) | `controllers/home.php` |
| `/?page=users` | `controllers/user/index.php` |
| `/?page=users/create` | `controllers/user/create.php` |
| `/?page=users/edit&id=X` | `controllers/user/edit.php` |
| `/?page=users/delete&id=X` | `controllers/user/delete.php` |

### Key Files

| Path | Purpose |
|------|---------|
| `index.php` | Front controller — loads autoload, starts session, dispatches by `?page=` |
| `config/config.php` | Loads `.env` with `loadEnv()` + `env()`; defines `APP_URL` constant and `$url` |
| `config/database.php` | Creates `$connection` MySQLi using `env()` helper |
| `config/autoload.php` | Bootstrap entry point; includes `config.php` + `database.php` |
| `model/User.php` | OOP model (`App\Model\User`); all DB queries via prepared statements |
| `controllers/auth/login.php` | GET: show form. POST: verify credentials, set session |
| `controllers/auth/logout.php` | Destroys session, redirects to login |
| `controllers/auth/reset.php` | GET: show forgot-password form. POST: generate token, send PHPMailer email |
| `controllers/auth/update_password.php` | GET: show reset form. POST: validate token, hash + save new password |
| `controllers/home.php` | Auth check, sets `$name`/`$isAdmin`/`$year`, includes dashboard view |
| `controllers/user/index.php` | Admin-only: fetches all users, includes user list view |
| `controllers/user/create.php` | GET: show create form. POST: create user via `User::create()` |
| `controllers/user/edit.php` | GET: fetch user + show edit form. POST: update user via `User::update()` |
| `controllers/user/delete.php` | Deletes user by `?id=`, redirects to user list |
| `templates/header.php` / `templates/footer.php` | Shared nav/footer for protected pages (DataTables) |
| `database/schema.sql` | Current DB schema — `users` + `password_resets` tables |
| `database/seeds.sql` | Sample users with bcrypt-hashed passwords |

### Session Variables

Set on login (only after successful `password_verify()`), required for all protected pages:

- `$_SESSION['user_id']` — user ID
- `$_SESSION['name']` — display name (first_name)
- `$_SESSION['is_admin']` — boolean, controls admin menu visibility

### Database Tables

- **users**: `id, first_name, last_name, email, username, password` (bcrypt), `is_admin (DEFAULT 0)`
  - `email` and `username` have UNIQUE constraints
- **password_resets**: `id, email, token, created_at, expires_at, used`

## Security Patterns

- Passwords hashed with `password_hash($pass, PASSWORD_DEFAULT)` (bcrypt)
- Session variables assigned only after successful `password_verify()` — never on failed login
- Reset tokens: `bin2hex(random_bytes(32))` — 256-bit, 1-hour expiry, single-use (`used = 1` after consumption)
- All DB queries in `model/User.php` use MySQLi prepared statements
- Password resets use MySQLi prepared statements
- Email sanitized with `filter_var($email, FILTER_SANITIZE_EMAIL)` before DB queries
- SMTP uses STARTTLS encryption (port 587)

## Notes

- PHPMailer is included directly from `libs/PHPMailer/src/` — not via Composer autoload
- All asset paths (CSS, JS, images) use the `APP_URL` constant via `<?= APP_URL ?>` short-tag syntax
- `use App\Model\User;` declarations in controllers are file-scoped and work correctly when `require`'d by the front controller
