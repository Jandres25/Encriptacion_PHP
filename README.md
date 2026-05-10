<div align="center">

# SecureAuth — PHP MVC Authentication System

[![Version](https://img.shields.io/badge/version-1.6.0-blue.svg?style=flat-square)](https://github.com/Jandres25/Encriptacion_PHP/releases/tag/1.6.0)
[![Tests](https://github.com/Jandres25/Encriptacion_PHP/actions/workflows/tests.yml/badge.svg)](https://github.com/Jandres25/Encriptacion_PHP/actions/workflows/tests.yml)
[![PHP Version](https://img.shields.io/badge/PHP->=8.2-777BB4.svg?style=flat-square&logo=php)](https://php.net/)
[![PHPMailer](https://img.shields.io/badge/PHPMailer-^6.9-1F3B5F.svg?style=flat-square)](https://github.com/PHPMailer/PHPMailer)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)

Custom PHP MVC authentication system built with Composer, a lightweight router, and role-based access control.

</div>

## Features

- Custom MVC architecture — `App\Core\Router`, abstract `Controller` and `Model`, PSR-4 autoloading via Composer
- Secure login with bcrypt password hashing (`password_hash()` / `password_verify()`)
- Persistent login via **Remember Me** — `HttpOnly` / `SameSite=Strict` cookie; token stored as SHA-256 hash in DB
- Automatic **session timeout** on inactivity with remember cookie cleanup
- Password recovery via email with expiring single-use tokens (PHPMailer + STARTTLS)
- Admin user management — full CRUD with role-based access control (`AuthMiddleware`)
- `App\Config\Database` singleton — single `\mysqli` connection per request
- File-based cache for the users listing with automatic invalidation on writes
- **Integration test suite** — 28 PHPUnit tests against a real MySQL DB; CI via GitHub Actions
- SweetAlert2 toast notifications for all CRUD and authentication actions
- Per-page asset injection — `$pageStyles` / `$pageScripts` arrays in shared layouts
- Shared layout system — `header.php` / `footer.php` accept `$pageTitle`, `$favicon`, `$bodyClass`, `$useDataTables`
- App version displayed in footer via `APP_VERSION` env var

## Requirements

- PHP >= 8.2
- MySQL / MariaDB
- Apache with `mod_rewrite` (XAMPP recommended)
- Composer
- Gmail account with an App Password (or any SMTP provider)

## Installation

1. Clone the repository:

```bash
git clone https://github.com/Jandres25/Encriptacion_PHP.git
cd Encriptacion_PHP
```

2. Install dependencies:

```bash
composer install
```

3. Copy and configure the environment file:

```bash
cp .env.example .env
```

Edit `.env` with your credentials:

```
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=login

SMTP_HOST=smtp.gmail.com
SMTP_USERNAME=your@gmail.com
SMTP_PASSWORD=your_app_password
SMTP_PORT=587

APP_URL=http://localhost/Encriptacion_PHP/public
APP_TIMEZONE=America/Bogota
APP_VERSION=1.5.0

CACHE_ENABLED=true
CACHE_TTL_USERS=60

REMEMBER_ME_ENABLED=true
REMEMBER_ME_TTL=2592000

SESSION_TIMEOUT=1800
```

4. Import the database schema:

```bash
mysql -u root -p < database/schema.sql
```

5. (Optional) Load sample data:

```bash
mysql -u root -p < database/seeds.sql
```

6. Place the project in your server's web root (e.g. `htdocs/` in XAMPP) and open `APP_URL` in your browser.

## Project Structure

```
├── app/
│   ├── Config/
│   │   ├── autoload.php       # Bootstrap: timezone, cache, DB, session, restoreFromCookie()
│   │   ├── cache.php          # Cache bootstrap + appCache() helper
│   │   ├── config.php         # Loads .env via phpdotenv; defines APP_URL + env()
│   │   └── database.php       # Database singleton — Database::getConnection()
│   ├── Controller/
│   │   ├── AuthController.php  # login, logout, forgotPassword, resetPassword
│   │   ├── HomeController.php  # Dashboard — applies timeout + auth middleware
│   │   └── UserController.php  # Full user CRUD — guarded by admin middleware
│   ├── Core/
│   │   ├── Auth.php            # Credential verify, remember-me tokens, password reset tokens
│   │   ├── Controller.php      # Abstract base — render() and redirect()
│   │   ├── Model.php           # Abstract base — holds protected \mysqli $db
│   │   └── Router.php          # GET/POST route registration and dispatch
│   ├── Middleware/
│   │   └── AuthMiddleware.php  # Static guards: auth(), admin(), timeout()
│   ├── Model/
│   │   └── User.php            # All DB queries via MySQLi prepared statements
│   └── Service/
│       └── MailerService.php   # PHPMailer encapsulation — SMTP via STARTTLS
├── database/
│   ├── schema.sql              # Table definitions (users + password_resets)
│   ├── schema_test.sql         # Table-only schema for test DB (no CREATE DATABASE)
│   └── seeds.sql               # Sample data with bcrypt-hashed passwords
├── libs/
│   └── Cache/                  # File-based cache implementation
├── public/
│   ├── css/                    # bootstrap.css, estilo.css, all.min.css, layout-protected.css
│   ├── DataTables/             # DataTables JS bundle + Bootstrap 4 skin
│   ├── img/                    # Images and icons
│   ├── js/                     # jQuery, Bootstrap JS, Popper, SweetAlert2, users-*.js
│   ├── webfonts/               # FontAwesome webfonts
│   ├── .htaccess               # Apache rewrite rules for clean URLs
│   └── index.php               # Front controller
├── routes/
│   └── web.php                 # All route definitions
├── storage/
│   └── cache/                  # Runtime cache files (*.cache)
├── views/
│   ├── auth/                   # login, forgot_password, reset_password (standalone)
│   ├── home/                   # index.php — dashboard content (wrapped by shared layout)
│   ├── layouts/                # header.php, footer.php, messages.php
│   └── user/                   # index, create, edit (wrapped by shared layout)
├── tests/
│   ├── bootstrap.php           # Test bootstrap — loads .env.testing, never starts session
│   ├── TestCase.php            # Abstract base — DB connection, truncate, createUser()
│   ├── Unit/
│   │   └── UserTest.php        # 14 integration tests for App\Model\User
│   └── Integration/
│       └── AuthTest.php        # 14 integration tests for App\Core\Auth
├── .env.example                # Environment variable template
├── phpunit.xml                 # PHPUnit 11 configuration
└── composer.json               # Composer dependencies and PSR-4 autoload
```

## Usage

1. Open `http://localhost/Encriptacion_PHP/public/` in your browser
2. Log in with a seeded user (e.g. username `Admin`, password `Admin1234`)
3. Admin users (`is_admin = 1`) see the **Users** link in the nav → full CRUD
4. To recover a password, click "Forgot your password?" on the login page

## URL Routing

All routes are declared in `routes/web.php` and dispatched by `App\Core\Router`:

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

## Security

- Passwords hashed with bcrypt (`PASSWORD_DEFAULT`)
- Session set only after successful `password_verify()`
- Reset tokens: 256-bit (`bin2hex(random_bytes(32))`), 1-hour expiry, single-use
- All DB queries via MySQLi prepared statements
- Email validated with `filter_var()` before DB lookup
- SMTP with STARTTLS (port 587)
- Remember-me: raw token in cookie, SHA-256 hash in DB — cookie is `HttpOnly`, `SameSite=Strict`, `Secure` on HTTPS
- Session timeout enforced on every protected request; clears remember cookie to prevent silent re-login

## Cache

- Cached endpoint: `/users` listing (`App\Model\User::getAll()`)
- Cache key: `users.all`
- Invalidation: on create, edit, delete, and password update
- Controls: `CACHE_ENABLED=true|false`, `CACHE_TTL_USERS=<seconds>`
- Storage: `storage/cache/*.cache`
- If the directory is not writable, cache is disabled for the request and a warning is logged (no HTTP 500)

## Testing

The project includes an integration test suite (PHPUnit 11) that runs against a real MySQL database.

### Local setup

```bash
# 1. Create the test database
mysql -u root -p -e "CREATE DATABASE login_test;"
mysql -u root -p login_test < database/schema_test.sql

# 2. Copy and configure the test environment
cp .env.testing.example .env.testing   # or create it manually from .env.testing section in docs
# Set DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE=login_test

# 3. Run all tests
composer test

# Run by suite
composer test:unit         # App\Model\User — 14 tests
composer test:integration  # App\Core\Auth  — 14 tests
```

### CI

Tests run automatically on every push and PR to `master` via GitHub Actions (`.github/workflows/tests.yml`).

## Contributing

1. Fork the project
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes following [Conventional Commits](https://www.conventionalcommits.org/)
4. Push and open a Pull Request

## License

MIT License — see the `LICENSE` file for details.

## Contact

Jandres25 — jandrespb4@gmail.com

Project link: [https://github.com/Jandres25/Encriptacion_PHP](https://github.com/Jandres25/Encriptacion_PHP)
