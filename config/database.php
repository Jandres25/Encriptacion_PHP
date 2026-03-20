<?php

require_once __DIR__ . '/config.php';

$conexion = new mysqli(
    env('DB_HOST', 'localhost'),
    env('DB_USERNAME', 'root'),
    env('DB_PASSWORD', ''),
    env('DB_DATABASE')
);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}
