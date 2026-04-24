<?php

require_once __DIR__ . '/config.php';

$connection = new mysqli(
    env('DB_HOST', 'localhost'),
    env('DB_USERNAME', 'root'),
    env('DB_PASSWORD', ''),
    env('DB_DATABASE')
);

if ($connection->connect_error) {
    die("Database connection error: " . $connection->connect_error);
}
