<?php

require_once __DIR__ . '/../../model/User.php';

use App\Model\User;

if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/?page=login');
    exit;
}

if (empty($_SESSION['is_admin'])) {
    header('Location: ' . APP_URL . '/');
    exit;
}

$year      = date('Y');
$userModel = new User($connection);
$users     = $userModel->getAll();

include __DIR__ . '/../../views/user/index.php';
