<?php

use App\Core\Auth;

date_default_timezone_set(env('APP_TIMEZONE', 'America/La_Paz'));

require_once __DIR__ . '/cache.php';
require_once __DIR__ . '/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

(new Auth($connection))->restoreFromCookie();
