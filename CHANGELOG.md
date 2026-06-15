# Changelog

All notable changes to this project are documented in this file.

Format based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

> Note: Entries before `1.3.1` may reference legacy paths (`config/`, `controllers/`, `model/`) that were moved to `app/Config/`, `app/Controller/`, and `app/Model/`.

## [1.9.0] — 2026-06-14

### Added

- **Perfil de usuario** — nueva sección `/profile` accesible para cualquier usuario autenticado (sin requisito de admin):
  - `ProfileController` con métodos `profile()` (editar info) y `changePassword()` (cambiar contraseña)
  - Vista unificada `views/profile/index.php` con dos formularios independientes, cada uno con su propio token CSRF
  - Form 1 (`POST /profile`): edita `first_name`, `last_name`, `email`, `username`; valida unicidad excluyendo el propio ID; actualiza `$_SESSION['name']` si cambia el nombre
  - Form 2 (`POST /profile/password`): requiere contraseña actual con `password_verify()`; valida coincidencia y mínimo 8 caracteres; usa `updatePasswordProfile()` independiente del flujo de reset por email
  - `User::updateProfile()` — UPDATE limitado a `first_name`, `last_name`, `email`, `username`; sin acceso a `password` ni `is_admin` (previene escalada de privilegios vía IDOR)
  - `User::getPasswordById()` — SELECT solo del hash para verificar la contraseña actual sin cargar la fila completa
  - `User::updatePasswordProfile()` — UPDATE de contraseña por `id` (no por email), independiente de `updatePassword()` que sigue siendo exclusivo del flujo de reset
  - Nombre de usuario en el nav convertido en enlace a `/profile` (visible para todos los usuarios autenticados)
  - Ambas operaciones invalidan la caché `users.all`

---

## [1.8.0] — 2026-06-11

### Security

- **HTTP Security Headers** — `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `X-XSS-Protection: 1; mode=block`, `Permissions-Policy` y `Content-Security-Policy` base (`default-src 'self'`, `form-action 'self'`, `frame-ancestors 'none'`) agregados en `public/.htaccess` via `mod_headers`; HSTS comentado listo para activar en HTTPS
- **Eliminación de dependencias externas en vistas auth** — jQuery y Google Fonts cargaban desde CDN externo (`code.jquery.com`, `fonts.googleapis.com`) sin SRI en las vistas de login, forgot-password y reset-password; reemplazados por assets self-hosted para eliminar vector de supply-chain y fuga de token de reset via cabecera `Referer`
- **Cookie de sesión segura** — `session_start()` centralizado en helper `session_start_secure()` (en `app/Config/autoload.php`) que aplica `httponly=true`, `samesite=Strict` y `secure` condicional (HTTPS) en todos los puntos donde se inicia sesión: bootstrap inicial, logout y timeout de inactividad
- **Logout cambiado de GET a POST con CSRF** — la ruta `/logout` era un `GET` sin protección, explotable con `<img src="...">` para cerrar sesión ajena; ahora es `POST` con token CSRF verificado en `AuthController::logout()`; el link en `header.php` fue reemplazado por un formulario con botón estilizado
- **Eliminación de user enumeration en forgot-password** — `AuthController::forgotPassword()` retornaba mensajes distintos según si el email existía o no; ahora responde siempre con el mismo mensaje genérico independientemente del resultado, enviando el email silenciosamente si el token se creó
- **Rotación de token CSRF** — `Csrf::verify()` ahora invalida el token de sesión tras cada verificación exitosa (`unset($_SESSION['csrf_token'])`), forzando regeneración en el siguiente request
- **Protección contra auto-eliminación y auto-degradación de admin** — `UserController::delete()` bloquea la eliminación del propio usuario autenticado; `UserController::edit()` bloquea quitar el propio rol `is_admin`
- **Error de conexión DB no expone detalles internos** — `Database::getConnection()` registra el error via `error_log()` y muestra la vista de error 500 en lugar del mensaje crudo de MySQLi con host/puerto

### Added

- **Páginas de error personalizadas** — nuevas vistas en `views/errors/`: `404.php` (ruta no encontrada), `403.php` (acceso denegado), `500.php` (error de servidor); comparten `layout.php` standalone (sin depender del layout de la app ni de la DB) con el gradiente y paleta de colores del proyecto
- `Router::dispatch()` renderiza `views/errors/404.php` en lugar de imprimir el path interno
- `AuthMiddleware::admin()` devuelve HTTP 403 con la vista de error en lugar de redirigir silenciosamente al home

### Fixed

- Alineación del botón Logout en la navbar — reemplazado inline styles por clase CSS `.btn-logout-nav` en `estilo.css`
- `storage/.htaccess` con `Require all denied` para proteger explícitamente los archivos de caché

---

## [1.7.0] — 2026-06-08

### Security

- **Account lockout** — bloqueo automático de cuenta tras 5 intentos de login fallidos consecutivos (configurable):
  - Nueva tabla `login_attempts` con `identifier` como `PRIMARY KEY` (sin surrogate id) — sin enumeración de usuarios: solo se registran intentos para usernames que existen en DB
  - Nuevo modelo `App\Model\LoginAttempt` — `registerFailure()` atómico via `INSERT ... ON DUPLICATE KEY UPDATE` en SQL (sin race conditions), `lockedSecondsRemaining()` y `clear()` con operaciones temporales en MySQL (`NOW()`, `DATE_ADD`, `TIMESTAMPDIFF`) para evitar drift PHP/MySQL
  - `App\Core\Auth` — 4 métodos nuevos: `lockedSecondsRemaining()`, `registerFailedAttempt()`, `clearFailedAttempts()`, `userExists()`; limpieza de lockout integrada en `consumeResetToken()` (limpia por email y por username)
  - `AuthController::login()` — check de bloqueo antes de `verifyCredentials()`: contraseña correcta no levanta el bloqueo durante la ventana; mensaje con minutos restantes (`ceil`)
  - Login exitoso elimina la fila de intentos (`DELETE`); reset de contraseña exitoso limpia lockout por ambos identificadores posibles
  - Controlado por `LOGIN_LOCKOUT_ENABLED`, `LOGIN_MAX_ATTEMPTS` (default 5), `LOGIN_LOCKOUT_MINUTES` (default 15)
- 7 nuevos tests en `tests/Unit/LoginAttemptTest.php` y 4 nuevos casos en `tests/Integration/AuthTest.php` — 40 tests en total

---

## [1.6.1] — 2026-06-07

### Security

- **CSRF protection** — nueva clase `App\Core\Csrf` con métodos estáticos `token()` / `verify()`; todos los formularios POST incluyen un campo oculto `_csrf` validado en cada controlador via el nuevo helper `Controller::verifyCsrf()`; el token se almacena en `$_SESSION['csrf_token']` y se compara con `hash_equals()` para evitar timing attacks
- **XSS en mensajes flash** — `$icon` y `$message` en `views/layouts/messages.php` se interpolaban directamente en un string JavaScript; reemplazados con `json_encode()` para que comillas, barras o saltos de línea no puedan romper el contexto JS
- **Delete de usuario cambiado de GET a POST** — la ruta `/users/delete` y `UserController::delete()` ahora requieren POST; `users-delete.js` crea y envía un form dinámicamente con el token CSRF al confirmar, en lugar de hacer `window.location.href`; elimina explotación CSRF con `<img>` o un solo clic
- **Session fixation en login** — `session_regenerate_id(true)` se llama inmediatamente después de `password_verify()` exitoso, antes de escribir variables de sesión
- **Tokens de reset de contraseña hasheados en DB** — `Auth::createPasswordResetToken()` ahora almacena `hash('sha256', $token)` en la tabla `password_resets` (mismo patrón que los tokens de remember-me); `Auth::consumeResetToken()` hashea el token entrante antes de la búsqueda en DB; el token raw solo viaja en la URL del email

### Fixed

- **Null dereference en `User::update()`** — cuando `getById($id)` retornaba `null`, acceder a `['password']` en el resultado causaba un fatal TypeError en PHP 8.x; `update()` ahora llama `getById()` una vez, retorna `false` temprano si el usuario no existe, y reutiliza el resultado para el fallback de contraseña
- **`User::update()` éxito falso** — `affected_rows >= 0` trataba un UPDATE sin filas coincidentes (ID no encontrado) como éxito; cambiado a `affected_rows !== -1` para distinguir correctamente un error de DB (`-1`) de una actualización idempotente (`0` filas cambiadas)
- **`FileCache::remember()` no cacheaba null** — `get()` retorna `null` tanto para un cache miss como para una entrada expirada/corrupta; `remember()` ahora verifica `is_file()` primero para distinguir un miss real de un valor null cacheado
- **`$favicon` sin escapar en header** — `views/layouts/header.php` ahora pasa `$favicon` por `htmlspecialchars()`, consistente con `$pageTitle` y `$bodyClass`

---

## [1.6.0] — 2026-05-10

### Added

- **Integration test suite** — PHPUnit ^11.0 against a real MySQL test database (`login_test`):
  - `tests/Unit/UserTest.php` — 14 tests covering all `App\Model\User` public methods (CRUD, remember token, password hashing)
  - `tests/Integration/AuthTest.php` — 14 tests covering `App\Core\Auth` (credential verification, remember-me token lifecycle, password reset token lifecycle)
  - `tests/TestCase.php` — abstract base with direct `\mysqli` connection, schema bootstrap, per-test table truncation, and `createUser()` helper
  - `tests/bootstrap.php` — minimal bootstrap: populates `$_ENV` from `.env.testing` before Composer autoload, never starts session
- `phpunit.xml` — PHPUnit 11 config with `Unit` and `Integration` suites, `failOnWarning=true`, random execution order
- `database/schema_test.sql` — table-only schema for test DB (no `CREATE DATABASE` / `USE` statements)
- `.github/workflows/tests.yml` — GitHub Actions CI: MySQL 8.0 service with health check, `setup-php@v2`, Composer cache, PHPUnit run on push/PR to `master`
- `composer.json` scripts: `test`, `test:unit`, `test:integration`

### Fixed

- `libs/Cache/FileCache::forget()` now respects the `$enabled` flag — previously attempted `unlink()` even when cache was disabled, causing permission errors in test environments
- `app/Config/cache.php` — `appCache()` short-circuits immediately when `CACHE_ENABLED=false`, skipping directory writability checks that triggered warnings in CI
- `app/Config/config.php` — changed `->load()` to `->safeLoad()` so the app boots without a `.env` file present (required for CI where `.env.testing` is injected at runtime)

---

## [1.5.0] — 2026-05-10

### Added

- `App\Config\Database` singleton class — `Database::getConnection()` returns the same `\mysqli` instance across the entire request; `$connection` variable preserved for backward compatibility
- `APP_VERSION` environment variable displayed in the shared footer (`views/layouts/footer.php`)
- Per-page asset injection in shared layouts:
  - `$pageStyles` — array of CSS paths injected in `<head>` (after DataTables CSS)
  - `$pageScripts` — array of JS paths injected in footer (after DataTables JS)
- `$pageTitle`, `$favicon`, `$bodyClass` variables accepted by `views/layouts/header.php`
- `$bodyClass` suppresses `mt-3` on `<main>` when set (used by dashboard's hero section)

### Changed

- `views/home/index.php` migrated from standalone HTML file to shared layout (`protected: true`) — contains only content markup now
- `views/layouts/header.php` generalized: accepts `$pageTitle`, `$favicon`, `$bodyClass`; nav now shared (Home, Users if admin, username, Logout) using `$_SESSION` directly
- `$useDataTables` defaults to `false` — opt-in per controller; DataTables CSS/JS only loads on `UserController::index()`
- `users-table.js` and `users-delete.js` moved from `footer.php` to `UserController::index()` via `$pageScripts`
- `bootstrap.css` now loads before `estilo.css` in `header.php` so `.btn-app-primary` correctly overrides Bootstrap defaults
- `HomeController` passes `bodyClass: 'dashboard'`, `favicon`, and `pageTitle` explicitly
- `UserController` passes descriptive `pageTitle` for each action (Users, Create User, Edit User)
- Dashboard feature cards updated to reflect current MVC architecture (Router, Middleware, Composer, remember-me, session timeout)
- `composer.json` — removed stale `app/Config/view_helpers.php` from `files` autoload array

---

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
