<?php

namespace App\Config;

class Database
{
    private static ?\mysqli $instance = null;

    public static function getConnection(): \mysqli
    {
        if (self::$instance === null) {
            $conn = new \mysqli(
                env('DB_HOST', 'localhost'),
                env('DB_USERNAME', 'root'),
                env('DB_PASSWORD', ''),
                env('DB_DATABASE')
            );

            if ($conn->connect_error) {
                die("Database connection error: " . $conn->connect_error);
            }

            self::$instance = $conn;
        }

        return self::$instance;
    }
}

$connection = Database::getConnection();
