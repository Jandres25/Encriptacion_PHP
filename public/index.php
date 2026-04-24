<?php

require_once __DIR__ . '/../app/Config/autoload.php';

session_start();

$page = $_GET['page'] ?? null;

if ($page === null) {
    $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $requestPath = trim($requestPath, '/');

    $appPath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
    $appPath = trim($appPath, '/');

    if ($appPath !== '' && str_starts_with($requestPath, $appPath)) {
        $requestPath = trim(substr($requestPath, strlen($appPath)), '/');
    }

    $page = ($requestPath === '' || $requestPath === 'index.php') ? 'home' : $requestPath;
}

$routes = [
    'login'           => __DIR__ . '/../app/Controller/auth/login.php',
    'logout'          => __DIR__ . '/../app/Controller/auth/logout.php',
    'forgot-password' => __DIR__ . '/../app/Controller/auth/reset.php',
    'reset-password'  => __DIR__ . '/../app/Controller/auth/update_password.php',
    'users'           => __DIR__ . '/../app/Controller/user/index.php',
    'users/create'    => __DIR__ . '/../app/Controller/user/create.php',
    'users/edit'      => __DIR__ . '/../app/Controller/user/edit.php',
    'users/delete'    => __DIR__ . '/../app/Controller/user/delete.php',
];

$controller = $routes[$page] ?? __DIR__ . '/../app/Controller/home.php';
require $controller;
