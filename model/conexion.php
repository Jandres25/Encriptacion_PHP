<?php

// Incluye la función para cargar las variables de entorno (si aún no lo has hecho)
function cargarVariablesEntorno($ruta)
{
    if (!file_exists($ruta)) {
        return false;
    }

    $archivo = fopen($ruta, 'r');
    if ($archivo) {
        while (($linea = fgets($archivo)) !== false) {
            $linea = trim($linea);
            if (empty($linea) || strpos($linea, '#') === 0) {
                continue; // Ignorar líneas vacías y comentarios
            }

            list($nombre, $valor) = explode('=', $linea, 2);
            $nombre = trim($nombre);
            $valor = trim($valor);

            if (!array_key_exists($nombre, $_SERVER) && !array_key_exists($nombre, $_ENV)) {
                putenv(sprintf('%s=%s', $nombre, $valor));
                $_ENV[$nombre] = $valor;
                $_SERVER[$nombre] = $valor;
            }
        }
        fclose($archivo);
        return true;
    }

    return false;
}

$rutaArchivoEnv = __DIR__ . '/../.env'; // Ajusta la ruta si es necesario
cargarVariablesEntorno($rutaArchivoEnv);

// Lee las variables de entorno para la configuración de la base de datos
$db_host = $_ENV["DB_HOST"];
$db_username = $_ENV["DB_USERNAME"];
$db_password = $_ENV["DB_PASSWORD"];
$db_database = $_ENV["DB_DATABASE"];

// Crea la conexión a la base de datos usando las variables de entorno
$conexion = new mysqli($db_host, $db_username, $db_password, $db_database);

// Verifica si la conexión fue exitosa
if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}
