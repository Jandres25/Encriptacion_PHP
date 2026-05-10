<?php

namespace App\Middleware;

use App\Core\Auth;

class AuthMiddleware
{
    public static function auth(): void
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    public static function admin(): void
    {
        self::auth();
        if (empty($_SESSION['is_admin'])) {
            header('Location: ' . APP_URL . '/');
            exit;
        }
    }

    public static function timeout(\mysqli $connection): void
    {
        if (empty($_SESSION['user_id'])) {
            return;
        }

        $timeout = (int) env('SESSION_TIMEOUT', 1800);

        if ((time() - ($_SESSION['last_activity'] ?? time())) > $timeout) {
            (new Auth($connection))->clearRememberToken((int) $_SESSION['user_id']);
            if (isset($_COOKIE['remember_me'])) {
                setcookie('remember_me', '', [
                    'expires'  => time() - 3600,
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Strict',
                ]);
            }
            session_destroy();
            session_start();
            $_SESSION['message'] = 'Your session has expired due to inactivity';
            $_SESSION['icon']    = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $_SESSION['last_activity'] = time();
    }
}
