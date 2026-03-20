<?php

require_once __DIR__ . '/config/autoload.php';

session_start();

$page = $_GET['page'] ?? 'home';

$routes = [
    'login'           => __DIR__ . '/controllers/auth/login.php',
    'logout'          => __DIR__ . '/controllers/auth/logout.php',
    'forgot-password' => __DIR__ . '/controllers/auth/reset.php',
    'reset-password'  => __DIR__ . '/controllers/auth/update_password.php',
    'users'           => __DIR__ . '/controllers/user/index.php',
    'users/create'    => __DIR__ . '/controllers/user/create.php',
    'users/edit'      => __DIR__ . '/controllers/user/edit.php',
    'users/delete'    => __DIR__ . '/controllers/user/delete.php',
];

$controller = $routes[$page] ?? __DIR__ . '/controllers/home.php';
require $controller;
