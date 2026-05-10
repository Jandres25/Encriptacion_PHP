# Plan — Tests de Integración PHPUnit (Auth + User)

_Generado con Claude Opus · 2026-05-10_

---

## Prompt usado

```
[Rol]
Actúa como desarrollador PHP Senior especializado en testing de integración con PHPUnit,
arquitectura MVC y seguridad web (autenticación, hashing, sesiones).

[Contexto]
Proyecto: Encriptacion_PHP — PHP MVC con Composer (sin framework).
Stack: PHP 8.2+, MySQLi, Bootstrap 4, SweetAlert2, FontAwesome, MySQL/MariaDB, PHPMailer.
Servidor: XAMPP (Apache + MySQL) en http://localhost/Encriptacion_PHP/public
PHPUnit ^11.0 ya está en require-dev.

Autoload PSR-4:
- App\  → app/
- App\Lib\ → libs/
- Tests\ → tests/

Clases bajo test:
- app/Core/Auth.php      — verifyCredentials, issueRememberToken, consumeRememberToken,
                           clearRememberToken, createPasswordResetToken, consumeResetToken,
                           restoreFromCookie
- app/Model/User.php     — getAll, getById, getByUsername, getByEmail, create, update,
                           delete, setRememberToken, getByRememberToken, clearRememberToken,
                           updatePassword

Dependencias globales críticas:
- env()      → app/Config/config.php   (NO cargar autoload.php — arranca sesión + cookies)
- appCache() → app/Config/cache.php    (CACHE_ENABLED=false en test)
- Conexión directa mysqli a login_test — NO usar App\Config\Database singleton

[Tarea]
Diseña el plan COMPLETO de implementación de tests de integración con PHPUnit ^11.0
para App\Core\Auth y App\Model\User usando una base de datos MySQL de prueba real
(no mocks de mysqli).

El plan debe cubrir:
1. Infraestructura: bootstrap, phpunit.xml, .env.testing, TestCase base
2. Tests de User (todos los métodos públicos)
3. Tests de Auth (flujos de credenciales, remember-me y password reset)
4. GitHub Actions workflow con servicio MySQL real

[Restricciones]
- NO mocks de mysqli — conexión real a login_test
- NO cargar app/Config/autoload.php en tests
- NO usar Database singleton en tests — mysqli directo en TestCase
- Bootstrap: vendor/autoload.php + config.php + cache.php únicamente
- .env.testing separado; CACHE_ENABLED=false
- Cada test independiente: truncar tablas en setUp()
- PHPUnit 11 con atributos PHP 8 (#[Test], #[DataProvider])
- Salvaguarda: rechazar ejecución si DB_DATABASE === 'login'
- No testear restoreFromCookie (depende de $_COOKIE/$_SESSION globales)

[Formato de salida]
Plan organizado en FASES con: objetivo, archivos, decisiones técnicas y código completo.
```

---

## Decisiones generales

| Decisión                      | Motivo                                                                          |
| ----------------------------- | ------------------------------------------------------------------------------- |
| Integración real contra MySQL | Valida SQL real, índices UNIQUE y constraints — mocks de mysqli ocultarían bugs |
| DB `login_test` separada      | Nunca tocar datos de producción local                                           |
| Bootstrap mínimo              | `autoload.php` arranca sesión y llama `restoreFromCookie` con cookies reales    |
| `CACHE_ENABLED=false`         | Evita que resultados cacheados contaminen tests entre sí                        |
| Nombres de métodos en inglés  | Convención PHPUnit moderna, legible en logs de CI                               |

---

## Fase 1 — Infraestructura

**Objetivo:** andamiaje de testing: bootstrap, configuración PHPUnit, env de pruebas y `TestCase` base.

**Archivos a crear/modificar:**

| Archivo               | Acción                                                 |
| --------------------- | ------------------------------------------------------ |
| `phpunit.xml`         | Crear                                                  |
| `.env.testing`        | Crear                                                  |
| `tests/bootstrap.php` | Crear                                                  |
| `tests/TestCase.php`  | Crear                                                  |
| `composer.json`       | Añadir scripts `test`, `test:unit`, `test:integration` |

**Decisiones:**

- `.env.testing` separado: evita que un `.env` de desarrollo apunte a `login`.
- Schema aplicado una sola vez por proceso (flag estático), truncate por test — más rápido que recrear tablas.
- Salvaguarda en `TestCase::createConnection()`: lanza excepción si `DB_DATABASE === 'login'`.

### `phpunit.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true"
         cacheDirectory=".phpunit.cache"
         executionOrder="random"
         failOnWarning="true"
         failOnRisky="true"
         beStrictAboutOutputDuringTests="true"
         displayDetailsOnTestsThatTriggerWarnings="true">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory>app</directory>
            <directory>libs</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV"       value="testing" force="true"/>
        <env name="CACHE_ENABLED" value="false"   force="true"/>
    </php>
</phpunit>
```

### `.env.testing`

```
DB_HOST=127.0.0.1
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=login_test

SMTP_HOST=smtp.example.com
SMTP_USERNAME=test@example.com
SMTP_PASSWORD=secret
SMTP_PORT=587

APP_URL=http://localhost/Encriptacion_PHP/public
APP_TIMEZONE=UTC
APP_VERSION=test

CACHE_ENABLED=false
CACHE_TTL_USERS=1

REMEMBER_ME_ENABLED=true
REMEMBER_ME_TTL=2592000

SESSION_TIMEOUT=1800
```

### `tests/bootstrap.php`

```php
<?php
declare(strict_types=1);

// 1. Composer autoload
require __DIR__ . '/../vendor/autoload.php';

// 2. Cargar .env.testing (antes de config.php para que env() lea los valores correctos)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', '.env.testing');
$dotenv->load();

// 3. Helpers globales
require __DIR__ . '/../app/Config/config.php';
require __DIR__ . '/../app/Config/cache.php';

// NO cargar app/Config/autoload.php (arranca sesión, lee cookies, conecta singleton DB)
```

> **Riesgo a verificar:** si `app/Config/config.php` llama `Dotenv::createImmutable(...)->load()` apuntando a `.env`, fallará en CI donde no hay `.env`. Solución: usar `safeLoad()` en `config.php` — las vars ya cargadas por el bootstrap tienen precedencia con `createImmutable`.

### `tests/TestCase.php`

```php
<?php
declare(strict_types=1);

namespace Tests;

use mysqli;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected static ?mysqli $db = null;
    private static bool $schemaApplied = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$db === null) {
            self::$db = self::createConnection();
        }
        if (!self::$schemaApplied) {
            self::applySchema(self::$db);
            self::$schemaApplied = true;
        }

        $this->truncateTables();
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_COOKIE  = [];
        parent::tearDown();
    }

    private static function createConnection(): mysqli
    {
        $host = env('DB_HOST',     '127.0.0.1');
        $user = env('DB_USERNAME', 'root');
        $pass = env('DB_PASSWORD', '');
        $name = env('DB_DATABASE', 'login_test');

        if ($name === 'login') {
            throw new \RuntimeException(
                'Refuso ejecutar tests contra la DB de producción "login". ' .
                'Configura DB_DATABASE=login_test en .env.testing'
            );
        }

        $mysqli = new mysqli($host, $user, $pass, $name);
        if ($mysqli->connect_errno) {
            throw new \RuntimeException("No se pudo conectar a {$name}: {$mysqli->connect_error}");
        }
        $mysqli->set_charset('utf8mb4');
        return $mysqli;
    }

    private static function applySchema(mysqli $db): void
    {
        $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
        if ($sql === false) {
            throw new \RuntimeException('No se pudo leer database/schema.sql');
        }
        if ($db->multi_query($sql)) {
            do {
                if ($result = $db->store_result()) {
                    $result->free();
                }
            } while ($db->more_results() && $db->next_result());
        }
        if ($db->errno) {
            throw new \RuntimeException("Error aplicando schema: {$db->error}");
        }
    }

    protected function truncateTables(): void
    {
        self::$db->query('SET FOREIGN_KEY_CHECKS=0');
        self::$db->query('TRUNCATE TABLE users');
        self::$db->query('TRUNCATE TABLE password_resets');
        self::$db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    protected function createUser(array $overrides = []): array
    {
        $defaults = [
            'first_name' => 'Test',
            'last_name'  => 'User',
            'email'      => 'test@example.com',
            'username'   => 'testuser',
            'password'   => 'secret123',
            'is_admin'   => 0,
        ];
        $data = array_merge($defaults, $overrides);
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt = self::$db->prepare(
            'INSERT INTO users (first_name, last_name, email, username, password, is_admin)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->bind_param(
            'sssssi',
            $data['first_name'], $data['last_name'], $data['email'],
            $data['username'], $hash, $data['is_admin']
        );
        $stmt->execute();
        $data['id']            = $stmt->insert_id;
        $data['password_hash'] = $hash;
        $stmt->close();
        return $data;
    }
}
```

### `composer.json` (scripts a añadir)

```json
"scripts": {
    "test":             "phpunit",
    "test:unit":        "phpunit --testsuite=Unit",
    "test:integration": "phpunit --testsuite=Integration"
}
```

---

## Fase 2 — Tests de `User`

**Objetivo:** validar todas las queries de `App\Model\User` contra MySQL real.

**Archivos a crear:** `tests/Unit/UserTest.php`

**Decisiones:**

- Carpeta `Unit/` aunque toca DB: nombre de convención para "una clase bajo test".
- Verificar hash bcrypt: leer `password` con SQL directo y aplicar `password_verify()`.
- Verificar que el remember token en DB es SHA-256 del raw, no el raw.

### `tests/Unit/UserTest.php`

```php
<?php
declare(strict_types=1);

namespace Tests\Unit;

use App\Model\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class UserTest extends TestCase
{
    private User $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->users = new User(self::$db);
    }

    #[Test]
    public function it_finds_user_by_username(): void
    {
        $u = $this->createUser(['username' => 'alice']);
        $found = $this->users->getByUsername('alice');
        $this->assertNotNull($found);
        $this->assertSame($u['id'], (int) $found['id']);
    }

    #[Test]
    public function it_returns_null_for_unknown_username(): void
    {
        $this->assertNull($this->users->getByUsername('ghost'));
    }

    #[Test]
    public function it_finds_user_by_email(): void
    {
        $u = $this->createUser(['email' => 'a@b.com']);
        $found = $this->users->getByEmail('a@b.com');
        $this->assertSame($u['id'], (int) $found['id']);
    }

    #[Test]
    public function it_returns_null_for_unknown_email(): void
    {
        $this->assertNull($this->users->getByEmail('nobody@x.com'));
    }

    #[Test]
    public function it_finds_user_by_id(): void
    {
        $u = $this->createUser();
        $this->assertNotNull($this->users->getById($u['id']));
    }

    #[Test]
    public function it_returns_null_for_unknown_id(): void
    {
        $this->assertNull($this->users->getById(999999));
    }

    #[Test]
    public function it_creates_user_with_hashed_password(): void
    {
        $ok = $this->users->create([
            'first_name' => 'Bob',
            'last_name'  => 'Smith',
            'email'      => 'bob@x.com',
            'username'   => 'bob',
            'password'   => 'plaintext123',
            'is_admin'   => 0,
        ]);
        $this->assertTrue($ok);

        $row = self::$db->query("SELECT password FROM users WHERE username='bob'")->fetch_assoc();
        $this->assertNotSame('plaintext123', $row['password']);
        $this->assertTrue(password_verify('plaintext123', $row['password']));
    }

    #[Test]
    public function it_updates_user_without_password_change(): void
    {
        $u = $this->createUser();
        $this->users->update($u['id'], [
            'first_name' => 'Updated',
            'last_name'  => 'Name',
            'email'      => $u['email'],
            'username'   => $u['username'],
            'is_admin'   => 1,
        ]);
        $row = $this->users->getById($u['id']);
        $this->assertSame('Updated', $row['first_name']);
        $this->assertSame(1, (int) $row['is_admin']);
        $this->assertTrue(password_verify($u['password'], $row['password']));
    }

    #[Test]
    public function it_updates_password(): void
    {
        $u = $this->createUser();
        $this->users->updatePassword($u['email'], 'newpass456');
        $row = $this->users->getById($u['id']);
        $this->assertTrue(password_verify('newpass456', $row['password']));
        $this->assertFalse(password_verify($u['password'], $row['password']));
    }

    #[Test]
    public function it_deletes_existing_user(): void
    {
        $u = $this->createUser();
        $this->assertTrue($this->users->delete($u['id']));
        $this->assertNull($this->users->getById($u['id']));
    }

    #[Test]
    public function it_handles_delete_of_unknown_user(): void
    {
        $result = $this->users->delete(999999);
        $this->assertIsBool($result);
    }

    #[Test]
    public function it_sets_and_retrieves_remember_token(): void
    {
        $u = $this->createUser();
        $rawToken = bin2hex(random_bytes(32));
        $hash     = hash('sha256', $rawToken);
        $expires  = date('Y-m-d H:i:s', time() + 3600);

        $this->users->setRememberToken($u['id'], $hash, $expires);
        $found = $this->users->getByRememberToken($hash);
        $this->assertNotNull($found);
        $this->assertSame($u['id'], (int) $found['id']);
    }

    #[Test]
    public function it_clears_remember_token(): void
    {
        $u = $this->createUser();
        $hash = hash('sha256', 'raw');
        $this->users->setRememberToken($u['id'], $hash, date('Y-m-d H:i:s', time() + 3600));
        $this->users->clearRememberToken($u['id']);
        $this->assertNull($this->users->getByRememberToken($hash));

        $row = self::$db->query("SELECT remember_token, remember_token_expires FROM users WHERE id={$u['id']}")->fetch_assoc();
        $this->assertNull($row['remember_token']);
        $this->assertNull($row['remember_token_expires']);
    }

    #[Test]
    public function get_all_returns_all_users(): void
    {
        $this->createUser(['username' => 'a', 'email' => 'a@x.com']);
        $this->createUser(['username' => 'b', 'email' => 'b@x.com']);
        $this->assertCount(2, $this->users->getAll());
    }
}
```

---

## Fase 3 — Tests de `Auth`

**Objetivo:** validar el flujo completo de autenticación, remember-me y password reset incluyendo expiración y uso único.

**Archivos a crear:** `tests/Integration/AuthTest.php`

**Decisiones:**

- Simular tokens expirados con `UPDATE ... SET expires = <pasado>` vía SQL directo — determinista, sin mockear `time()`.
- Verificar que en DB se guarda el SHA-256, nunca el raw token.
- `restoreFromCookie` no se testea — depende de `$_COOKIE`/`$_SESSION` globales.

### `tests/Integration/AuthTest.php`

```php
<?php
declare(strict_types=1);

namespace Tests\Integration;

use App\Core\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    private Auth $auth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = new Auth(self::$db);
    }

    // --- verifyCredentials ---

    #[Test]
    public function it_verifies_correct_credentials(): void
    {
        $u = $this->createUser(['username' => 'alice', 'password' => 'secret']);
        $result = $this->auth->verifyCredentials('alice', 'secret');
        $this->assertNotNull($result);
        $this->assertSame($u['id'], (int) $result['id']);
    }

    #[Test]
    public function it_rejects_wrong_password(): void
    {
        $this->createUser(['username' => 'alice', 'password' => 'secret']);
        $this->assertNull($this->auth->verifyCredentials('alice', 'wrong'));
    }

    #[Test]
    public function it_rejects_unknown_user(): void
    {
        $this->assertNull($this->auth->verifyCredentials('ghost', 'secret'));
    }

    // --- remember token ---

    #[Test]
    public function it_issues_remember_token_and_stores_hash(): void
    {
        $u   = $this->createUser();
        $raw = $this->auth->issueRememberToken($u['id']);

        $this->assertNotEmpty($raw);
        $row = self::$db->query("SELECT remember_token, remember_token_expires FROM users WHERE id={$u['id']}")->fetch_assoc();
        $this->assertSame(hash('sha256', $raw), $row['remember_token']);
        $this->assertNotSame($raw, $row['remember_token']);
        $this->assertGreaterThan(time(), strtotime($row['remember_token_expires']));
    }

    #[Test]
    public function it_consumes_valid_remember_token(): void
    {
        $u     = $this->createUser();
        $raw   = $this->auth->issueRememberToken($u['id']);
        $found = $this->auth->consumeRememberToken($raw);
        $this->assertNotNull($found);
        $this->assertSame($u['id'], (int) $found['id']);
    }

    #[Test]
    public function it_rejects_invalid_remember_token(): void
    {
        $this->assertNull($this->auth->consumeRememberToken('deadbeef'));
    }

    #[Test]
    public function it_rejects_expired_remember_token(): void
    {
        $u    = $this->createUser();
        $raw  = $this->auth->issueRememberToken($u['id']);
        $past = date('Y-m-d H:i:s', time() - 3600);
        self::$db->query("UPDATE users SET remember_token_expires='{$past}' WHERE id={$u['id']}");
        $this->assertNull($this->auth->consumeRememberToken($raw));
    }

    #[Test]
    public function it_clears_remember_token(): void
    {
        $u = $this->createUser();
        $this->auth->issueRememberToken($u['id']);
        $this->auth->clearRememberToken($u['id']);
        $row = self::$db->query("SELECT remember_token, remember_token_expires FROM users WHERE id={$u['id']}")->fetch_assoc();
        $this->assertNull($row['remember_token']);
        $this->assertNull($row['remember_token_expires']);
    }

    // --- password reset ---

    #[Test]
    public function it_creates_reset_token_for_existing_email(): void
    {
        $this->createUser(['email' => 'reset@x.com']);
        $token = $this->auth->createPasswordResetToken('reset@x.com');
        $this->assertNotEmpty($token);

        $row = self::$db->query("SELECT * FROM password_resets WHERE email='reset@x.com'")->fetch_assoc();
        $this->assertNotNull($row);
        $this->assertSame($token, $row['token']);
        $this->assertSame(0, (int) $row['used']);
        $this->assertGreaterThan(time(), strtotime($row['expires_at']));
    }

    #[Test]
    public function it_returns_null_for_reset_with_unknown_email(): void
    {
        $this->assertNull($this->auth->createPasswordResetToken('nobody@x.com'));
    }

    #[Test]
    public function it_consumes_valid_reset_token_and_changes_password(): void
    {
        $u     = $this->createUser(['email' => 'reset@x.com', 'password' => 'old']);
        $token = $this->auth->createPasswordResetToken('reset@x.com');
        $email = $this->auth->consumeResetToken($token, 'newpass');

        $this->assertSame('reset@x.com', $email);

        $row = self::$db->query("SELECT password FROM users WHERE id={$u['id']}")->fetch_assoc();
        $this->assertTrue(password_verify('newpass', $row['password']));

        $reset = self::$db->query("SELECT used FROM password_resets WHERE token='{$token}'")->fetch_assoc();
        $this->assertSame(1, (int) $reset['used']);
    }

    #[Test]
    public function it_rejects_expired_reset_token(): void
    {
        $this->createUser(['email' => 'reset@x.com']);
        $token = $this->auth->createPasswordResetToken('reset@x.com');
        $past  = date('Y-m-d H:i:s', time() - 7200);
        self::$db->query("UPDATE password_resets SET expires_at='{$past}' WHERE token='{$token}'");
        $this->assertNull($this->auth->consumeResetToken($token, 'whatever'));
    }

    #[Test]
    public function it_rejects_already_used_reset_token(): void
    {
        $this->createUser(['email' => 'reset@x.com']);
        $token = $this->auth->createPasswordResetToken('reset@x.com');
        $this->auth->consumeResetToken($token, 'first');
        $this->assertNull($this->auth->consumeResetToken($token, 'second'));
    }

    #[Test]
    public function it_rejects_unknown_reset_token(): void
    {
        $this->assertNull($this->auth->consumeResetToken('not-a-real-token', 'x'));
    }
}
```

---

## Fase 4 — GitHub Actions Workflow

**Objetivo:** correr la suite en CI contra MySQL real en cada push/PR a `master`.

**Archivos a crear:** `.github/workflows/tests.yml`

**Decisiones:**

- `services: mysql` con health check evita race conditions de "DB not ready".
- Schema importado explícitamente después de crear `login_test` — no asumir que el servicio lo crea con datos.
- Cache de Composer por `composer.lock` para acelerar builds.

### `.github/workflows/tests.yml`

```yaml
name: Tests

on:
  push:
    branches: [master]
  pull_request:
    branches: [master]

jobs:
  phpunit:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ALLOW_EMPTY_PASSWORD: "yes"
          MYSQL_DATABASE: login_test
        ports:
          - 3306:3306
        options: >-
          --health-cmd="mysqladmin ping -h 127.0.0.1"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=10

    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Setup PHP 8.2
        uses: shivammathur/setup-php@v2
        with:
          php-version: "8.2"
          extensions: mbstring, mysqli, intl
          coverage: none
          tools: composer:v2

      - name: Cache Composer dependencies
        uses: actions/cache@v4
        with:
          path: ~/.composer/cache
          key: ${{ runner.os }}-composer-${{ hashFiles('**/composer.lock') }}
          restore-keys: ${{ runner.os }}-composer-

      - name: Install dependencies
        run: composer install --no-interaction --prefer-dist --no-progress

      - name: Wait for MySQL
        run: |
          for i in {1..30}; do
            if mysqladmin ping -h 127.0.0.1 -u root --silent; then
              echo "MySQL ready"; exit 0
            fi
            sleep 2
          done
          echo "MySQL never came up"; exit 1

      - name: Create test DB and import schema
        run: |
          mysql -h 127.0.0.1 -u root -e "CREATE DATABASE IF NOT EXISTS login_test;"
          mysql -h 127.0.0.1 -u root login_test < database/schema.sql

      - name: Create .env.testing
        run: |
          cat > .env.testing <<EOF
          DB_HOST=127.0.0.1
          DB_USERNAME=root
          DB_PASSWORD=
          DB_DATABASE=login_test
          SMTP_HOST=smtp.example.com
          SMTP_USERNAME=test@example.com
          SMTP_PASSWORD=secret
          SMTP_PORT=587
          APP_URL=http://localhost
          APP_TIMEZONE=UTC
          APP_VERSION=ci
          CACHE_ENABLED=false
          CACHE_TTL_USERS=1
          REMEMBER_ME_ENABLED=true
          REMEMBER_ME_TTL=2592000
          SESSION_TIMEOUT=1800
          EOF

      - name: Run PHPUnit
        run: vendor/bin/phpunit --colors=always
```

---

## Checklist de verificación

### Local

- [ ] `composer install` sin errores; `phpunit/phpunit ^11.0` instalado
- [ ] `composer dump-autoload` reconoce `Tests\` PSR-4
- [ ] DB local `login_test` creada: `mysql -u root -e "CREATE DATABASE login_test"`
- [ ] Schema importado: `mysql -u root login_test < database/schema.sql`
- [ ] `.env.testing` presente con `DB_DATABASE=login_test` (NO `login`)
- [ ] `vendor/bin/phpunit --testsuite=Unit` pasa (13 tests de User)
- [ ] `vendor/bin/phpunit --testsuite=Integration` pasa (13 tests de Auth)
- [ ] `vendor/bin/phpunit` full pasa con `failOnWarning="true"`
- [ ] Salvaguarda activa: cambiar `DB_DATABASE=login` → excepción "Refuso ejecutar tests..."
- [ ] DB `login` local intacta tras los tests

### CI

- [ ] Workflow aparece en pestaña Actions de GitHub
- [ ] Job `phpunit` verde en push a `master`
- [ ] Job `phpunit` verde en PR
- [ ] Logs muestran "MySQL ready" antes del paso de schema
- [ ] PHPUnit imprime conteo > 0 tests

### Cobertura funcional

- [ ] `User`: getByUsername ×2, getByEmail ×2, getById ×2, create, update, updatePassword, delete ×2, remember token ×2, getAll → **13 tests**
- [ ] `Auth`: verifyCredentials ×3, remember token ×5, password reset ×6 → **14 tests**
- [ ] Verificado explícitamente: hash bcrypt en DB, hash SHA-256 de remember token en DB, `used=1` tras consumir reset, expiración rechazada

### Riesgo conocido

> Si `app/Config/config.php` llama `Dotenv::load()` apuntando a `.env` (no `safeLoad`), fallará en CI.
> **Fix:** cambiar a `safeLoad()` en `config.php` — phpdotenv `createImmutable` no sobrescribe vars ya presentes en el entorno, así que las del bootstrap tendrán precedencia.

---

_Plan generado por Claude Opus · Proyecto Encriptacion_PHP · 2026-05-10_
