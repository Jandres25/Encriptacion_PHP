# Changelog

Todos los cambios notables a este proyecto se documentan en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/es/1.0.0/).

## [Unreleased]

### Added
- Directorio `config/` con separación de responsabilidades:
  - `config/config.php` — carga `.env` con `loadEnv()` + `env()`, define constante `APP_URL` y `$url`
  - `config/database.php` — conexión MySQLi usando `env()`
  - `config/autoload.php` — punto de entrada único que incluye `database.php`
- Variables de entorno `APP_URL` y `APP_TIMEZONE` en `.env` y `.env.example`
- Campos `correo` (opcional) y `EsAdmin` (checkbox) en el formulario de crear usuario
- Campos `correo` y `EsAdmin` pre-poblados en el formulario de editar usuario
- Restricciones `UNIQUE` en columnas `correo` y `Usuario` de la tabla `usuario`

### Changed
- `model/conexion.php` ahora delega a `config/autoload.php` para mantener compatibilidad con includes existentes
- Todas las rutas de assets (CSS, JS, imágenes) en las vistas usan la constante `APP_URL` en lugar de rutas relativas frágiles
- Todos los redirects en controladores usan `APP_URL` en lugar de rutas relativas (`../`, `./`)
- CRUD de usuarios (`crear_usuario.php`, `editar_usuario.php`, `index.php`) convertido de interpolación de strings a MySQLi prepared statements
- Al editar un usuario, dejar el campo contraseña vacío conserva la contraseña actual
- `login.sql`: columnas `correo` y `EsAdmin` ahora tienen `DEFAULT ''` y `DEFAULT 0` respectivamente

### Fixed
- **Email de recuperación no llegaba**: `controlador_reset.php` usaba `ENCRYPTION_SMTPS` (SSL/465) con el puerto 587 (STARTTLS) — corregido a `ENCRYPTION_STARTTLS`
- **Vulnerabilidad de sesión en login**: las variables de sesión se asignaban antes de `password_verify()`, permitiendo acceso con contraseña incorrecta — ahora se asignan solo tras autenticación exitosa
- **`reset_password.php` mostraba error PHP**: `$_GET['token']` se accedía sin `isset()`, causando `E_WARNING` en PHP 8 — corregido con operador `??`
- **Crear usuario fallaba silenciosamente**: columnas `correo` y `EsAdmin` son `NOT NULL` sin default, y el INSERT no las incluía — resuelto con defaults en schema y campos en el formulario
- **Mensaje de error incorrecto**: `model/usuario/index.php` mostraba `$_GET['mensaje']` en lugar de `$_GET['mensaje_error']` en la alerta de error de eliminación
- **Falta `exit` tras redirects**: todos los `header('location:...')` en el módulo de usuarios ahora incluyen `exit` para detener la ejecución
