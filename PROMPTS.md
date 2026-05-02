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
Proyecto: Encriptacion_PHP — PHP MVC custom (sin framework).
Stack: Bootstrap 5, DataTables, SweetAlert2, FontAwesome, MySQL/MariaDB, PHPMailer.
Servidor: XAMPP (Apache + MySQL) en http://localhost/Encriptacion_PHP/
Módulo activo: _______________

[Tarea]
_______________

[Restricciones]
- Seguir el patrón MVC existente: thin delegators en app/Controller/ + clases Controller + Model en app/Model/
- Front controller en public/index.php — rutas via $_GET['page'] o REQUEST_URI
- Variables de entorno via env() definido en app/Config/config.php — nunca $_ENV directamente
- Conexión DB: $connection de app/Config/database.php — MySQLi con prepared statements siempre
- Flash notifications: $_SESSION['message'] + $_SESSION['icon'] renderizados en views/layouts/messages.php
- Assets via <?= APP_URL ?> — nunca rutas relativas
- FontAwesome: solo public/css/all.min.css (CSS) — no re-agregar la versión JS
- Bootstrap: bootstrap.css + bootstrap.min.js + popper.min.js — no usar bootstrap.bundle.js
- DataTables: solo public/DataTables/datatables.js — no datatables.min.js ni datatables.min.css
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
Proyecto: Encriptacion_PHP — PHP MVC custom (sin framework).
Stack: Bootstrap 5, DataTables, SweetAlert2, FontAwesome, MySQL/MariaDB, PHPMailer.
Módulo activo: [nombre del módulo — ej: auth, user, home]

Estructura de archivos relevante:
- app/Controller/[modulo]/[Módulo]Controller.php  ← lógica del módulo
- app/Controller/[modulo]/[accion].php            ← thin delegator
- app/Model/[Módulo].php                          ← queries con prepared statements
- views/[modulo]/[vista].php                      ← HTML de la vista
- public/js/[script].js                           ← JS del módulo (si aplica)

[Tarea]
Implementar [nombre exacto del requerimiento].

Descripción: [criterios de aceptación]

[Restricciones]
- Thin delegators solo instancian el Controller y llaman un método — sin lógica
- Métodos de controlador: manejan GET (renderizar vista) y POST (procesar formulario) en el mismo método
- Toda query DB en el Model, nunca en el Controller
- Flash notifications con $_SESSION['message'] + $_SESSION['icon'] — nunca pasar mensajes por URL
- Detección de POST: isset($_POST['btnXXX']) — no !empty() — porque <button> sin value envía string vacío
- Guards de autenticación: requireAuth() y requireAdmin() en UserController como referencia
- Invalidar caché en operaciones write: llamar appCache()->delete('users.all') o el key correspondiente
- No agregar comentarios obvios — solo donde el WHY no sea evidente

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
Proyecto: Encriptacion_PHP — PHP MVC custom (sin framework).
Stack: PHP 8.2+, MySQLi, Bootstrap 5, SweetAlert2.
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
Proyecto: Encriptacion_PHP — PHP MVC custom.
Rama revisada: feature/[nombre]
Feature implementada: [descripción]

[Tarea]
Revisa el siguiente código antes del merge.

[pega el código o el diff]

[Restricciones]
Evalúa específicamente:
- Seguridad: SQL injection (¿prepared statements?), XSS (¿htmlspecialchars en output?),
  session fixation (¿session_regenerate_id() tras login?), tokens (¿bin2hex(random_bytes(32))?),
  cookies (¿HttpOnly + Secure + SameSite?)
- Arquitectura: thin delegators sin lógica, queries solo en Model, lógica solo en Controller
- Sesiones: flash messages via $_SESSION['message']+['icon'], nunca por URL params
- Assets: APP_URL usado, no rutas relativas
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
Proyecto: Encriptacion_PHP — PHP MVC custom (sin framework).
BD implementada: users (id, first_name, last_name, email, username, password, is_admin,
                        remember_token, remember_token_expires),
                 password_resets (id, email, token, created_at, expires_at, used).

[Tarea]
Necesito decidir: [describe la decisión técnica]

Opciones que estoy considerando:
- Opción A: [describe]
- Opción B: [describe]

[Restricciones]
- No introducir frameworks (ni Laravel, ni Symfony)
- Mantener el front controller en public/index.php — no crear un router independiente
- Cualquier solución debe funcionar con el autoload actual (app/Config/autoload.php)
- No introducir Composer ni PSR-4 autoload — PHPMailer se incluye desde libs/ directamente
- Considerar impacto en el sistema de caché (libs/Cache/FileCache.php)

[Formato de salida]
1. Recomendación directa (cuál opción y por qué en 3 líneas)
2. Trade-offs de cada opción
3. Impacto en el resto del sistema
4. Primeros pasos concretos para implementar la opción recomendada
```

---

## Ejemplo real — Remember Me (implementado)

> Ejemplo de prompt de feature bien estructurado.
> La feature de "Recuérdame" está implementada — úsalo como referencia.

```
[Rol]
Actúa como desarrollador PHP Senior especializado en seguridad de autenticación
y manejo de sesiones/cookies.

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC custom (sin framework).
Stack: PHP 8.2+, MySQLi, Bootstrap 5, SweetAlert2.
Módulo: auth (login).

BD relevante:
- users (id, first_name, last_name, email, username, password bcrypt, is_admin,
         remember_token VARCHAR(64) NULL, remember_token_expires DATETIME NULL)

Módulo de referencia para patrones: password_resets (mismo patrón de token seguro).

[Tarea]
Implementar "Recuérdame" en el login.

Criterios de aceptación:
- Checkbox "Recuérdame" en el formulario de login
- Si marcado: genera token con bin2hex(random_bytes(32)), guarda hash en users,
  emite cookie 'remember_me' (HttpOnly, Secure, SameSite=Strict, 30 días)
- En cada request sin sesión activa: autoload.php valida cookie contra DB y restaura sesión
- Logout: limpia remember_token/remember_token_expires en DB y elimina la cookie
- Token se regenera en cada login con "Recuérdame" para evitar reutilización

[Restricciones]
- Token almacenado como hash (password_hash o hash('sha256')) — nunca el token en claro
- Cookie con HttpOnly=true, Secure=true, SameSite=Strict
- Validación del token en autoload.php antes del dispatch — no en cada Controller
- Limpiar tokens expirados al validar (no tarea separada de cron)
- Flash notifications para sesión restaurada: solo en casos de error, no en éxito silencioso

[Formato de salida]
1. SQL: ALTER TABLE para las dos columnas nuevas
2. app/Config/autoload.php — lógica de validación de cookie
3. app/Controller/auth/AuthController.php — cambios en login() y logout()
4. views/auth/login.php — checkbox en el formulario
5. app/Model/User.php — métodos nuevos
6. Checklist de testing manual
```

---

_Última actualización: 2026-05-02 — v1.4.0_
_Mantener sincronizado con CLAUDE.md al agregar features nuevas._
