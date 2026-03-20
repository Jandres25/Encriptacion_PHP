<?php

/**
 * Carga las variables de entorno desde un archivo .env
 * @throws Exception
 */
function loadEnv($path): void
{
    if (!file_exists($path)) {
        throw new Exception(".env file not found. Please create one based on .env.example");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {

        // Ignorar comentarios
        if (str_starts_with(trim($line), '#')) {
            continue;
        }

        // Dividir en nombre y valor
        list($name, $value) = explode('=', $line, 2);
        $name  = trim($name);
        $value = trim($value);

        // Eliminar comentarios inline (ej: valor # comentario)
        if (str_contains($value, ' #')) {
            $value = trim(explode(' #', $value, 2)[0]);
        }

        // Eliminar comillas si existen
        if (!empty($value)) {
            $value = trim($value, '"');
            $value = trim($value, "'");
        }

        // Establecer la variable de entorno
        putenv("$name=$value");
        $_ENV[$name] = $value;
    }
}

/**
 * Obtiene el valor de una variable de entorno
 *
 * @param string $key     Nombre de la variable
 * @param mixed  $default Valor por defecto si la variable no existe
 * @return mixed
 */
function env(string $key, mixed $default = null): mixed
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    return match (strtolower($value)) {
        'true',  '(true)'  => true,
        'false', '(false)' => false,
        'null',  '(null)'  => null,
        'empty', '(empty)' => '',
        default            => $value,
    };
}

// Cargar variables de entorno
try {
    loadEnv(__DIR__ . '/../.env');
} catch (Exception $e) {
    die('Error loading .env file: ' . $e->getMessage());
}

date_default_timezone_set(env('APP_TIMEZONE', 'America/Bogota'));

define('APP_URL', rtrim(env('APP_URL', 'http://localhost/Encriptacion_PHP'), '/'));

$url = APP_URL . '/';
