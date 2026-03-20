<?php

require_once __DIR__ . '/../../model/User.php';

use App\Model\User;

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: ' . APP_URL . '/?page=login');
    exit;
}

$year = date('Y');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $userModel = new User($connection);

    $data = [
        'first_name' => $_POST['first_name'],
        'last_name'  => $_POST['last_name'],
        'email'      => $_POST['email'] ?? '',
        'username'   => $_POST['username'],
        'password'   => $_POST['password'],
        'is_admin'   => isset($_POST['is_admin']) ? 1 : 0,
    ];

    if ($userModel->create($data)) {
        header('Location: ' . APP_URL . '/?page=users&message=' . urlencode('User added successfully'));
    } else {
        header('Location: ' . APP_URL . '/?page=users/create&error=' . urlencode('Failed to create user'));
    }
    exit;
}

include __DIR__ . '/../../views/user/create.php';
