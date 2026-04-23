<div align="center">

# Authentication and Password Recovery System

[![Version](https://img.shields.io/badge/version-1.2.2-blue.svg?style=flat-square)](https://github.com/Jandres25/Encriptacion_PHP/releases/tag/1.2.2)
[![PHP Version](https://img.shields.io/badge/PHP->=8.2-777BB4.svg?style=flat-square&logo=php)](https://php.net/)
[![PHPMailer](https://img.shields.io/badge/PHPMailer-^6.0-1F3B5F.svg?style=flat-square)](https://github.com/PHPMailer/PHPMailer)
[![License](https://img.shields.io/badge/license-MIT-green.svg?style=flat-square)](LICENSE)

PHP web application implementing a secure authentication system with bcrypt password hashing and email-based password recovery.

</div>

## Features

- Secure login with bcrypt password hashing (`password_hash()` / `password_verify()`)
- Password recovery via email with expiring single-use tokens
- Admin user management (create, edit, delete)
- SweetAlert2 toast notifications for all CRUD and authentication actions
- SweetAlert2 confirmation dialog for user deletion in `/users`
- Front controller architecture with clean URL routing
- OOP controllers (`AuthController`, `UserController`) with thin delegator pattern
- OOP model layer with MySQLi prepared statements
- Shared view helpers (`renderView`, `renderProtectedView`) for consistent template rendering
- PHPMailer integration for transactional email (STARTTLS)
- Consistent color palette via CSS variables (`--color-dark`, `--color-accent`)
- File-based cache for admin user listing with automatic invalidation on create/edit/delete
- Graceful cache fallback: if `storage/cache` is not writable, the app continues without cache and logs a warning

## Requirements

- PHP >= 8.2
- MySQL / MariaDB
- Apache (XAMPP recommended)
- Gmail account with an App Password (or any SMTP provider)

## Installation

1. Clone the repository:

```bash
git clone https://github.com/Jandres25/Encriptacion_PHP.git
cd Encriptacion_PHP
```

2. Copy and configure the environment file:

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

APP_URL=http://localhost/Encriptacion_PHP
APP_TIMEZONE=America/Bogota

CACHE_ENABLED=true
CACHE_TTL_USERS=60
```

`CACHE_TTL_USERS` defines (in seconds) how long the `/users` list stays cached.
The `storage/cache` directory must be writable by your web server user.

3. Import the database schema:

```bash
mysql -u root -p < database/schema.sql
```

4. (Optional) Load sample data:

```bash
mysql -u root -p < database/seeds.sql
```

5. Place the project in your server's web root (e.g. `htdocs/` in XAMPP) and open `APP_URL` in your browser.

## Project Structure

```
├── config/
│   ├── autoload.php       # Bootstrap entry point
│   ├── cache.php          # Cache bootstrap + appCache() helper
│   ├── config.php         # Loads .env, defines APP_URL
│   ├── database.php       # MySQLi connection ($connection)
│   └── view_helpers.php   # renderView() / renderProtectedView()
├── controllers/
│   ├── auth/
│   │   ├── AuthController.php   # App\Controller\Auth\AuthController — all auth logic
│   │   ├── login.php            # Thin delegator → AuthController::login()
│   │   ├── logout.php           # Thin delegator → AuthController::logout()
│   │   ├── reset.php            # Thin delegator → AuthController::forgotPassword()
│   │   └── update_password.php  # Thin delegator → AuthController::resetPassword()
│   ├── user/
│   │   ├── UserController.php   # App\Controller\User\UserController — all user CRUD logic
│   │   ├── index.php            # Thin delegator → UserController::index()
│   │   ├── create.php           # Thin delegator → UserController::create()
│   │   ├── edit.php             # Thin delegator → UserController::edit()
│   │   └── delete.php           # Thin delegator → UserController::delete()
│   └── home.php                 # Dashboard controller
├── database/
│   ├── schema.sql         # Table definitions (users + password_resets)
│   └── seeds.sql          # Sample data
├── libs/
│   ├── Cache/             # File-based cache implementation
│   └── PHPMailer/         # PHPMailer (no Composer)
├── model/
│   └── User.php           # App\Model\User — OOP model with prepared statements
├── public/
│   ├── css/               # Bootstrap, all.min.css (FontAwesome), estilo.css, layout-protected.css
│   ├── DataTables/        # DataTables combined bundle (datatables.js)
│   ├── img/               # Images and icons
│   ├── js/                # jQuery, Bootstrap JS, Popper, sweetalert2.all.min.js, users-table.js, users-delete.js
│   └── webfonts/          # FontAwesome webfonts (used by all.min.css)
├── storage/
│   └── cache/             # Runtime cache files (*.cache)
├── views/
│   ├── auth/              # login, forgot_password, reset_password
│   ├── layouts/           # shared header/footer and messages.php for notifications
│   ├── user/              # index, create, edit
│   ├── home/              # home/index.php
│   │   └── index.php      # Dashboard view
├── index.php              # Front controller — routes by path (/login, /users, ...)
├── .htaccess              # Apache rewrite rules for clean URLs
├── .env.example           # Environment variable template
└── database/schema.sql    # Source of truth for DB schema
```

## Usage

1. Open `http://localhost/Encriptacion_PHP/` in your browser
2. Log in with a seeded user (e.g. username `Admin`, password `123456`)
3. Admin users (`is_admin = 1`) see the **Users** link in the nav → full CRUD
4. To recover a password, click "Forgot your password?" on the login page

## URL Routing

The app uses a single front controller (`index.php`) with clean URL paths:

| URL                         | Page                   |
| --------------------------- | ---------------------- |
| `/`                         | Dashboard              |
| `/login`                    | Login                  |
| `/forgot-password`          | Forgot password        |
| `/reset-password?token=...` | Reset password         |
| `/users`                    | User list (admin only) |
| `/users/create`             | Create user            |
| `/users/edit?id=X`          | Edit user              |
| `/users/delete?id=X`        | Delete user            |

## Security

- Passwords hashed with bcrypt (`PASSWORD_DEFAULT`)
- Session set only after successful `password_verify()`
- Reset tokens: 256-bit, 1-hour expiry, single-use
- All DB queries via MySQLi prepared statements
- Email validated with `filter_var()` before DB lookup
- SMTP with STARTTLS (port 587)

## Cache

- Cached endpoint: `/users` user listing (`App\Model\User::getAll()`)
- Cache key: fixed key (`users.all`), so requests rewrite the same cache file instead of creating unlimited files
- Invalidation: cache is cleared after create, edit, delete, and password update operations
- Controls:
  - `CACHE_ENABLED=true|false`
  - `CACHE_TTL_USERS=<seconds>` (e.g., `600` for 10 minutes)
- Storage: `storage/cache/*.cache`
- If the directory is not writable, the app disables cache for the request and logs a warning (no HTTP 500)

### How to verify cache performance

1. Open Firefox DevTools (`F12`) → **Red** tab.
2. Open `/users` and note the request **Duración**.
3. Reload several times within TTL: the first request is usually slower; the next ones should be faster.
4. Set `CACHE_ENABLED=false`, reload again, and compare durations.

## Rendering and Layout

- Protected pages use `renderProtectedView()` from `config/view_helpers.php`.
- Shared protected layout lives in `views/layouts/header.php` and `views/layouts/footer.php`.
- DataTables initialization for the users table was moved to `public/js/users-table.js`.
- User deletion confirmation in `views/user/index.php` is handled by `public/js/users-delete.js` (SweetAlert2).
- Full-height protected layout behavior is in `public/css/layout-protected.css`.

## Contributing

1. Fork the project
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add my feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License — see the `LICENSE` file for details.

## Contact

Jandres25 - jandrespb4@gmail.com

Project link: [https://github.com/Jandres25/Encriptacion_PHP](https://github.com/Jandres25/Encriptacion_PHP)
