# PROMPTS.md — Encriptacion_PHP

> Plantillas de prompts para planificar features. Úsalas como base — adapta los bloques
> `[Tarea]` y `[Contexto]` a lo que necesites en cada sesión.
> El `CLAUDE.md` siempre debe estar disponible para el agente como contexto base.

---

## Cómo usar este archivo

Cada plantilla sigue la estructura de 5 ejes del prompt profesional:

| Eje                   | Pregunta          | Para qué sirve                                    |
| --------------------- | ----------------- | ------------------------------------------------- |
| **Rol**               | ¿Quién eres?      | Define el nivel y especialidad que asume la IA    |
| **Contexto**          | ¿Dónde estamos?   | El proyecto, stack y módulo activo                |
| **Tarea exacta**      | ¿Qué necesitas?   | Concreto y específico — nunca genérico            |
| **Restricciones**     | ¿Qué límites hay? | Convenciones del proyecto que NO se pueden romper |
| **Formato de salida** | ¿Cómo lo quieres? | Estructura del output esperado                    |

> **Regla de oro:** Cuanto más específico sea el bloque `[Tarea]`,
> menos correcciones necesitarás después.

**Reglas de uso:**

- **Siempre carga el CLAUDE.md** al inicio de la sesión si la herramienta no lo carga automáticamente.
- **Un prompt por subtarea.** Pedir "el módulo completo" en un solo prompt produce resultados genéricos.
- **Si el output no encaja**, no corrijas manualmente primero — ajusta `[Restricciones]` y repite.
- **El spec antes que el código.** Define qué debe hacer antes de pedir que lo implemente.
- **Guarda los prompts que funcionen bien** en este archivo como nuevas plantillas.

---

## Plantilla base (copia esto y rellena)

```
[Rol]
Actúa como desarrollador PHP Senior especializado en arquitectura MVC
y seguridad web (autenticación, hashing, sesiones).

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: Bootstrap 4, DataTables, SweetAlert2, FontAwesome, MySQL/MariaDB, PHPMailer.
Servidor: XAMPP (Apache + MySQL) en http://localhost/Encriptacion_PHP/public
Módulo activo: _______________

[Tarea]
_______________

[Restricciones]
- Arquitectura MVC: App\Core\Router despacha a Controller::method(); rutas declaradas en routes/web.php
- Controladores en app/Controller/ extienden App\Core\Controller (render + redirect)
- Modelos en app/Model/ extienden App\Core\Model (protected \mysqli $db)
- Conexión DB: App\Config\Database::getConnection() — singleton, MySQLi con prepared statements siempre
- Variables de entorno via env() definido en app/Config/config.php
- Guards: AuthMiddleware::auth(), AuthMiddleware::admin(), AuthMiddleware::timeout() — llamar al inicio de cada método
- Flash notifications: $_SESSION['message'] + $_SESSION['icon'] renderizados en views/layouts/messages.php — nunca pasar mensajes por URL
- Vistas protegidas: Controller::render($view, $data, protected: true) — wrappea con header.php + footer.php
- Assets via APP_URL — nunca rutas relativas
- FontAwesome: solo public/css/all.min.css (CSS) — no re-agregar la versión JS
- Bootstrap: bootstrap.css (antes de estilo.css) + bootstrap.min.js + popper.min.js
- DataTables: pasar useDataTables: true + pageScripts en el render del controller que lo necesite
- No introducir librerías nuevas sin aprobación
- Passwords: password_hash() al guardar, password_verify() al validar — nunca MD5/SHA1

[Formato de salida]
_______________
```

---

## Plantilla 1 — Generar código nuevo (feature)

Usar cuando: implementar un requerimiento nuevo.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en arquitectura MVC
y seguridad web (autenticación, hashing, sesiones).

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: Bootstrap 4, DataTables, SweetAlert2, FontAwesome, MySQL/MariaDB, PHPMailer.
Módulo activo: [nombre del módulo — ej: auth, user, home]

Archivos relevantes:
- app/Controller/[Módulo]Controller.php   ← lógica del módulo
- app/Model/[Módulo].php                  ← queries con prepared statements
- views/[modulo]/[vista].php              ← HTML de la vista (solo contenido, sin <html>)
- public/js/[script].js                   ← JS del módulo (si aplica, pasado via pageScripts)
- routes/web.php                          ← registro de rutas GET/POST

[Tarea]
Implementar [nombre exacto del requerimiento].

Descripción: [criterios de aceptación]

[Restricciones]
- Métodos de controlador: manejan GET (renderizar vista) y POST (procesar formulario) en el mismo método
- Toda query DB en el Model, nunca en el Controller
- Flash notifications con $_SESSION['message'] + $_SESSION['icon'] — nunca por URL
- Detección de POST: isset($_POST['btnXXX']) — no !empty() — porque <button> sin value envía string vacío
- Guards al inicio del método: AuthMiddleware::timeout() + AuthMiddleware::admin() o auth()
- CSRF: todos los formularios POST deben incluir `<input type="hidden" name="_csrf" value="<?= \App\Core\Csrf::token() ?>">` y el controller debe llamar `$this->verifyCsrf($redirectPath)` al inicio del bloque POST — el token **se rota tras cada verificación exitosa** (`Csrf::verify()` elimina el token de sesión, forzando regeneración en el siguiente `token()`)
- Invalidar caché en operaciones write: appCache()->delete('users.all') o el key correspondiente
- Vistas protegidas solo tienen contenido (sin <html>/<head>/<body>) — el layout lo pone render()
- No agregar comentarios obvios — solo donde el WHY no sea evidente
- No usar `session_start()` directamente — usar siempre `session_start_secure()` (definido en `app/Config/autoload.php`)
- No cargar assets desde CDN externos — usar siempre archivos self-hosted bajo `APP_URL`
- Logout es POST-only con CSRF — nunca agregar rutas GET para operaciones con side-effects
- Páginas de error: usar `views/errors/404.php`, `403.php`, `500.php` — no `die()` con texto plano ni `echo` del path interno

[Formato de salida]
Devuelve en este orden:
1. Lista de archivos que se crean o modifican
2. SQL si hay cambios en BD (ALTER TABLE o CREATE TABLE)
3. Código de cada archivo
4. Checklist de testing manual (casos exitosos + edge cases)
```

---

## Plantilla 2 — Debuggear un error

Usar cuando: algo no funciona y no está claro por qué.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en debugging
de aplicaciones MVC, sesiones PHP y MySQL.

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: PHP 8.2+, MySQLi, Bootstrap 4, SweetAlert2.
Archivo donde ocurre el error: [ruta completa]
Método/función afectada: [nombre]

[Tarea]
Tengo este error:
[pega el mensaje de error exacto o el comportamiento inesperado]

Código actual:
[pega el bloque de código relevante — no todo el archivo]

Lo que debería hacer:
[describe el comportamiento esperado]

Lo que intenté que no funciona:
[describe lo que ya probaste]

[Restricciones]
- No cambiar la arquitectura del archivo — solo corregir el problema específico
- Mantener naming conventions del proyecto (camelCase métodos, PascalCase clases)
- Si el fix toca más de un archivo, indicarlo antes de proponer código
- No cambiar prepared statements a queries directas como fix rápido

[Formato de salida]
1. Diagnóstico: causa raíz en 2-3 líneas
2. Fix: código corregido con comentario explicando el cambio
3. Por qué pasó: explicación breve para no repetirlo
```

---

## Plantilla 3 — Code review antes del merge

Usar cuando: antes de hacer merge, o cuando el código funciona pero algo "huele mal".

```
[Rol]
Actúa como Tech Lead PHP con experiencia en code review de sistemas MVC,
seguridad web (OWASP Top 10) y patrones de diseño.

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer.
Rama revisada: feature/[nombre]
Feature implementada: [descripción]

[Tarea]
Revisa el siguiente código antes del merge.

[pega el código o el diff]

[Restricciones]
Evalúa específicamente:
- Seguridad: SQL injection (¿prepared statements?), XSS (¿htmlspecialchars en output? ¿json_encode en contexto JS?),
  CSRF (¿campo _csrf en formularios POST? ¿verifyCsrf() al inicio del bloque POST?),
  session fixation (¿session_regenerate_id() tras login?), tokens (¿bin2hex(random_bytes(32))? ¿hash SHA-256 en DB?),
  cookies (¿HttpOnly + Secure + SameSite?)
- Arquitectura: queries solo en Model, lógica solo en Controller, guards al inicio de cada método
- Sesiones: flash messages via $_SESSION['message']+['icon'], nunca por URL params
- Assets: APP_URL usado, no rutas relativas; bootstrap.css antes de estilo.css
- Edge cases que podrían fallar en producción

[Formato de salida]
OK  - Lo que está bien (al menos 2 puntos)
OBS - Observaciones no críticas con sugerencia
FIX - Problemas a corregir antes del merge (con código corregido)
```

---

## Plantilla 4 — Consulta de arquitectura

Usar cuando: hay una decisión técnica importante antes de implementar.

```
[Rol]
Actúa como arquitecto de software PHP con experiencia en sistemas MVC
custom, seguridad de autenticación y diseño de base de datos.

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
BD implementada: users (id, first_name, last_name, email, username, password, is_admin,
                        remember_token, remember_token_expires),
                 password_resets (id, email, token, created_at, expires_at, used).
Core: App\Core\Router, App\Core\Controller, App\Core\Model, App\Core\Auth
Middleware: App\Middleware\AuthMiddleware (auth, admin, timeout)

[Tarea]
Necesito decidir: [describe la decisión técnica]

Opciones que estoy considerando:
- Opción A: [describe]
- Opción B: [describe]

[Restricciones]
- No introducir frameworks (ni Laravel, ni Symfony, ni Slim)
- Mantener el Router en App\Core\Router y las rutas en routes/web.php
- Conexión DB via App\Config\Database::getConnection() — no crear conexiones adicionales
- Cualquier solución debe integrarse con el autoload de Composer (PSR-4 App\ → app/)
- Considerar impacto en el sistema de caché (libs/Cache/FileCache.php)

[Formato de salida]
1. Recomendación directa (cuál opción y por qué en 3 líneas)
2. Trade-offs de cada opción
3. Impacto en el resto del sistema
4. Primeros pasos concretos para implementar la opción recomendada
```

---

## Ejemplo real — Remember Me (implementado en v1.4.0)

> Ejemplo de prompt de feature bien estructurado.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en seguridad de autenticación
y manejo de sesiones/cookies.

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: PHP 8.2+, MySQLi, Bootstrap 4, SweetAlert2.
Módulo: auth (login).

BD relevante:
- users (id, first_name, last_name, email, username, password bcrypt, is_admin,
         remember_token VARCHAR(64) NULL, remember_token_expires DATETIME NULL)

[Tarea]
Implementar "Recuérdame" en el login.

Criterios de aceptación:
- Checkbox "Recuérdame" en el formulario de login
- Si marcado: genera token con bin2hex(random_bytes(32)), guarda hash SHA-256 en users,
  emite cookie 'remember_me' (HttpOnly, Secure, SameSite=Strict, 30 días)
- En cada request sin sesión activa: app/Config/autoload.php valida cookie y restaura sesión
- Logout: limpia remember_token en DB y elimina la cookie
- Token se regenera en cada login con "Recuérdame"

[Restricciones]
- Token almacenado como hash('sha256') — nunca el token en claro
- Cookie con HttpOnly=true, Secure=true, SameSite=Strict
- Validación en App\Core\Auth::restoreFromCookie() — llamado desde autoload.php
- Limpiar cookie en AuthMiddleware::timeout() cuando la sesión expira

[Formato de salida]
1. SQL: ALTER TABLE para las columnas nuevas
2. app/Core/Auth.php — métodos de token
3. app/Config/autoload.php — llamada a restoreFromCookie()
4. app/Controller/AuthController.php — cambios en login() y logout()
5. views/auth/login.php — checkbox en el formulario
6. app/Model/User.php — métodos nuevos
7. Checklist de testing manual
```

---

---

## Plantilla 5 — Diseñar tests de integración PHPUnit

Usar cuando: añadir tests a un módulo nuevo o ampliar la suite existente.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en testing de integración con PHPUnit,
arquitectura MVC y seguridad web (autenticación, hashing, sesiones).

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: PHP 8.2+, MySQLi, PHPUnit ^11.0.
DB de prueba: login_test (nunca login).

Infraestructura de tests ya existente:
- tests/bootstrap.php   — carga .env.testing antes del autoload, nunca session_start()
- tests/TestCase.php    — conexión mysqli directa, truncate por test, createUser() helper
- phpunit.xml           — suites Unit + Integration, failOnWarning=true, random order
- database/schema_test.sql — schema sin CREATE DATABASE / USE

Clases ya cubiertas: App\Model\User (tests/Unit/UserTest.php),
                     App\Core\Auth (tests/Integration/AuthTest.php)

[Tarea]
Diseña los tests de integración para [Clase/módulo].

Métodos a cubrir: [lista]

[Restricciones]
- NO mocks de mysqli — conexión real a login_test
- NO cargar app/Config/autoload.php
- Extender Tests\TestCase, no PHPUnit\Framework\TestCase directamente
- PHPUnit 11: usar #[Test] y #[DataProvider] (atributos PHP 8, no anotaciones @)
- Comparaciones de fechas contra MySQL: usar DATE_SUB(NOW(), INTERVAL X HOUR) — no timestamps PHP
- Cada test independiente: no depender del orden de ejecución
- Ubicar en tests/Unit/ si cubre una clase aislada, tests/Integration/ si orquesta varias

[Formato de salida]
1. Archivo tests/[Suite]/[Clase]Test.php completo
2. Casos cubiertos (tabla: método → escenario → aserción clave)
3. Edge cases que podrían fallar en producción
```

---

## Ejemplo real — Tests de Auth + User (implementado en v1.6.0)

> Prompt de infraestructura de tests bien estructurado — ver docs/plan-tests-integracion.md para el plan completo generado con Claude Opus.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en testing de integración con PHPUnit,
arquitectura MVC y seguridad web.

[Contexto]
Proyecto: Encriptacion_PHP. PHPUnit ^11.0 en require-dev.
Clases: app/Core/Auth.php + app/Model/User.php
DB de prueba: login_test (MySQL real, no mocks).

[Tarea]
Diseña el plan COMPLETO de infraestructura de tests de integración:
bootstrap, phpunit.xml, .env.testing, TestCase base, tests de User y Auth,
y GitHub Actions workflow.

[Restricciones]
- NO mocks de mysqli, NO cargar autoload.php, NO usar Database singleton en tests
- Bootstrap: parse_ini_file(.env.testing) → $_ENV antes del autoload de Composer
- CACHE_ENABLED=false; salvaguarda si DB_DATABASE === 'login'
- PHPUnit 11 con atributos #[Test]

[Formato de salida]
Plan en 4 fases con objetivo, archivos, decisiones técnicas y código completo.
```

---

---

## Ejemplo real — Perfil de usuario (implementado en v1.9.0)

> Prompt de feature con vista unificada y métodos de modelo independientes.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en arquitectura MVC
y seguridad web (autenticación, hashing, sesiones).

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: PHP 8.2+, MySQLi, Bootstrap 4, SweetAlert2.
Módulo: profile (nuevo).

[Tarea]
Implementar perfil de usuario con dos sub-features en una sola vista:

1. profile() — Editar información del perfil
   - GET /profile muestra la vista; POST /profile procesa
   - Campos: first_name, last_name, email, username
   - Cualquier usuario autenticado (no requiere admin)
   - Validar unicidad excluyendo el propio ID
   - Actualizar $_SESSION['name'] si cambia first_name
   - Invalidar caché users.all

2. changePassword() — Cambiar contraseña
   - POST /profile/password (sin GET propio — form en la vista de perfil)
   - Campos: current_password, new_password, confirm_password
   - password_verify() para verificar la actual
   - password_hash() para guardar la nueva

[Restricciones]
- Vista unificada views/profile/index.php con dos <form> independientes,
  cada uno con su propio _csrf token
- Controlador ProfileController — guard usa auth(), no admin()
- $id siempre de $_SESSION['user_id'] — nunca de $_POST/$_GET (IDOR imposible)
- Métodos nuevos en User.php:
    updateProfile(int $id, array $data): bool — solo info, sin password/is_admin
    getPasswordById(int $id): ?string — solo el hash para verificar
    updatePasswordProfile(int $id, string $new): bool — por id, independiente de
      updatePassword() que sigue siendo exclusivo del flujo de reset por email
- Errores de changePassword() redirigen a /profile (misma vista, mismo toast)

[Formato de salida]
Plan en fases atómicas: 1-Model, 2-Rutas, 3-Controller, 4-Vista, 5-Nav.
Cada fase: objetivo, archivos, cambios técnicos, criterio de done.
```

---

---

## Ejemplo real — Audit log (implementado en v1.10.0)

> Prompt de feature con modelo estático, helper testeable y vista DataTables.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en arquitectura MVC
y seguridad web (autenticación, hashing, sesiones).

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: Bootstrap 4, DataTables, SweetAlert2, FontAwesome, MySQL/MariaDB, PHPMailer.
Módulo: audit log (nuevo).

[Tarea]
Diseña el plan COMPLETO de implementación del módulo de audit log.

Criterios de aceptación:
- Tabla activity_logs: id, user_id (nullable), event, description, ip_address, created_at
- Clase App\Model\ActivityLog con log() estático invocable desde cualquier controller
- Vista /activity-logs solo visible para admins (AuthMiddleware::admin())
- Tabla con DataTables client-side — misma infraestructura que /users
- Columnas visibles: fecha, usuario, evento, descripción, IP
- Esquema simple — sin campos JSON ni extras
- Tests PHPUnit: log con user, log anónimo, getAll()

[Restricciones]
- Arquitectura MVC estándar, MySQLi prepared statements siempre
- ActivityLog::log() llamable sin instanciar (usa Database::getConnection())
- user_id nullable: eventos de login fallido no tienen usuario autenticado
- ip_address: $_SERVER['REMOTE_ADDR'] — no X-Forwarded-For
- NO paginación server-side — DataTables client-side es suficiente
- Tests: NO mocks de mysqli, NO cargar autoload.php, extender Tests\TestCase, PHPUnit 11 #[Test]
- El logging nunca debe romper el flujo principal (try/catch + error_log en fallo)
- Separar logTo(\mysqli $db) como helper privado para inyección en tests sin singleton

[Formato de salida]
Plan en fases atómicas. Para cada fase: objetivo, archivos, cambios técnicos, criterio de done.
Al final: tabla de archivos afectados, consideraciones de seguridad, edge cases para tests.
```

---

---

## Ejemplo real — DataTables Buttons + ColVis (implementado en v1.11.0)

> Prompt de feature de exportación con assets self-hosted y configuración por flag de layout.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en arquitectura MVC
y seguridad web (autenticación, hashing, sesiones).

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: Bootstrap 4, DataTables, SweetAlert2, FontAwesome, MySQL/MariaDB, PHPMailer.
Módulo activo: DataTables — exportación y visibilidad de columnas

Archivos relevantes:
- views/layouts/header.php               ← <head> compartido; soporta $useDataTables, $pageStyles
- views/layouts/footer.php               ← footer compartido; soporta $useDataTables, $pageScripts
- app/Controller/UserController.php      ← pasa useDataTables:true + pageScripts a render()
- app/Controller/ActivityLogController.php ← pasa useDataTables:true + pageScripts a render()
- views/user/index.php                   ← tabla Bootstrap con DataTables
- views/activity-log/index.php           ← tabla Bootstrap con DataTables
- public/js/users-table.js               ← inicialización DataTables para /users
- public/js/activity-logs-table.js       ← inicialización DataTables para /activity-logs
- public/DataTables/                     ← assets self-hosted de DataTables ya existentes

[Tarea]
Agregar botones de exportación (Copy, CSV, Excel, PDF, Print) y selector de columnas (ColVis)
a las dos tablas existentes: /users y /activity-logs.

Criterios de aceptación:
- Botones agrupados en colección "Reports"; ColVis separado con texto "Columns"
- PDF con customize: título bold centrado, subtítulo italic, fecha de generación, footer por página
- Excel con messageTop, messageBottom y filename con fecha ISO
- Print con table-striped y font-size vía customize
- Columna Actions de /users excluida de todos los exports y del ColVis (clase no-export)
- Assets self-hosted en public/DataTables/ — nunca CDN externo
- Carga integrada en el flag $useDataTables existente — header.php y footer.php manejan los assets automáticamente

[Restricciones]
- NO introducir nuevas variables de layout — extender el bloque $useDataTables en header/footer
- Assets via APP_URL — nunca rutas relativas
- Orden de carga JS: dataTables.buttons → buttons.bootstrap4 → jszip → pdfmake → vfs_fonts
  → buttons.html5 → buttons.print → buttons.colVis → init JS de la página
- Versión Buttons 2.4.2 (compatible con DataTables 1.11.x — NO usar 3.x)
- No agregar comentarios obvios — solo donde el WHY no sea evidente

[Formato de salida]
Plan en fases atómicas. Para cada fase: objetivo, archivos, cambios técnicos, criterio de done.
Al final: orden de carga JS, edge cases a verificar manualmente.
```

---

---

## Ejemplo real — Dashboard con métricas (implementado en v1.12.0)

> Prompt de feature que agrega queries de agregación a modelos existentes y rediseña la vista home.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en arquitectura MVC
y seguridad web (autenticación, hashing, sesiones).

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: Bootstrap 4, DataTables, SweetAlert2, FontAwesome, MySQL/MariaDB, PHPMailer.
Módulo activo: home (dashboard)

Archivos relevantes:
- app/Controller/HomeController.php           ← renderiza views/home/index.php con protected:true
- app/Model/User.php                          ← queries con prepared statements; getAll(), create(), etc.
- app/Model/ActivityLog.php                   ← log() estático, logTo(), getAll() con LEFT JOIN users
- app/Model/LoginAttempt.php                  ← registerFailure(), lockedSecondsRemaining(), clear()
- views/home/index.php                        ← contenido del dashboard (sin <html>)
- public/css/estilo.css                       ← variables CSS: --color-accent #04a1fc, --color-dark #142e3d

[Tarea]
Diseña el plan COMPLETO de implementación del dashboard con métricas reales para la home.

Criterios de aceptación:
- Reemplazar las feature cards genéricas con 4 stat-cards de datos reales:
  1. Total de usuarios registrados (COUNT en users)
  2. Logins exitosos hoy (activity_logs WHERE event = EVENT_LOGIN_SUCCESS AND DATE(created_at) = CURDATE())
  3. Intentos fallidos hoy (activity_logs WHERE event = EVENT_LOGIN_FAILED AND DATE(created_at) = CURDATE())
  4. Cuentas bloqueadas ahora (login_attempts WHERE locked_until > NOW())
- Tabla de últimos 5 eventos del audit log (LEFT JOIN users, ORDER BY created_at DESC LIMIT 5)
- "Anónimo" para eventos sin user_id; enlace "Ver todo" visible solo a admins

Métodos nuevos:
- User::getTotalCount(): int
- ActivityLog::getCountTodayByEvent(string $event): int
- ActivityLog::getRecentEvents(int $limit = 5): array
- LoginAttempt::getLockedCount(): int

[Restricciones]
- MySQLi prepared statements siempre; sin interpolación en SQL
- Guards: AuthMiddleware::timeout() + AuthMiddleware::auth() al inicio de HomeController::index()
- NO DataTables en el dashboard — tabla simple Bootstrap
- htmlspecialchars() en todos los outputs de BD; contadores como (int)
- CURDATE() / NOW() calculados en MySQL — sin drift PHP/MySQL
- No introducir librerías nuevas

[Formato de salida]
Plan en fases atómicas. Para cada fase: objetivo, archivos, cambios técnicos, criterio de done.
Al final: queries SQL finales, consideraciones de seguridad, checklist de testing.
```

---

_Última actualización: 2026-06-23 — v1.12.0_
_Mantener sincronizado con CLAUDE.md al agregar features nuevas._
