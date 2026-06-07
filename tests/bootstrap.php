<?php

declare(strict_types=1);

// Poblar $_ENV/$_SERVER con .env.testing ANTES de que Composer ejecute config.php
// (config.php está en autoload.files y se dispara al hacer require del autoloader).
// parse_ini_file es suficiente aquí — evita la dependencia circular con phpdotenv.
$envFile = __DIR__ . '/../.env.testing';
if (file_exists($envFile)) {
    foreach (parse_ini_file($envFile) ?: [] as $key => $value) {
        $_ENV[$key]    = $value;
        $_SERVER[$key] = $value;
        putenv("{$key}={$value}");
    }
}

// El autoload ejecuta config.php (safeLoad no sobreescribe vars ya presentes) y
// registra env(), APP_URL y los namespaces de App\ y Tests\.
require __DIR__ . '/../vendor/autoload.php';

// cache.php NO está en autoload.files — hay que cargarlo explícitamente.
require __DIR__ . '/../app/Config/cache.php';

// NO cargar app/Config/autoload.php — arranca sesión, lee cookies y conecta el singleton DB.
