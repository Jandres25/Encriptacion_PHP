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
        $pass = (string) env('DB_PASSWORD', '');
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
        $sql = file_get_contents(__DIR__ . '/../database/schema_test.sql');
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
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['username'],
            $hash,
            $data['is_admin']
        );
        $stmt->execute();
        $data['id']            = $stmt->insert_id;
        $data['password_hash'] = $hash;
        $stmt->close();
        return $data;
    }
}
