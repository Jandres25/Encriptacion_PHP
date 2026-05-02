<?php

require_once __DIR__ . '/auth/AuthController.php';
(new \App\Controller\Auth\AuthController($connection))->checkSessionTimeout();

if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/login');
    exit;
}

$name    = $_SESSION['name'];
$isAdmin = $_SESSION['is_admin'];
$year    = date('Y');

include __DIR__ . '/../../views/home/index.php';
