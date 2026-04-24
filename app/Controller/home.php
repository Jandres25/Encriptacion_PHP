<?php

if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/login');
    exit;
}

$name    = $_SESSION['name'];
$isAdmin = $_SESSION['is_admin'];
$year    = date('Y');

include __DIR__ . '/../../views/home/index.php';
