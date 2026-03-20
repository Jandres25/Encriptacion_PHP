<?php

require_once __DIR__ . '/../../model/User.php';

use App\Model\User;

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: ' . APP_URL . '/?page=login');
    exit;
}

$year      = date('Y');
$userModel = new User($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $id   = (int) $_POST['id'];
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name'  => $_POST['last_name'],
        'email'      => $_POST['email'] ?? '',
        'username'   => $_POST['username'],
        'password'   => $_POST['password'] ?? '',
        'is_admin'   => isset($_POST['is_admin']) ? 1 : 0,
    ];

    if ($userModel->update($id, $data)) {
        header('Location: ' . APP_URL . '/?page=users&message=' . urlencode('User updated successfully'));
    } else {
        header('Location: ' . APP_URL . '/?page=users/edit&id=' . $id . '&error=' . urlencode('Failed to update user'));
    }
    exit;
}

$id   = (int) ($_GET['id'] ?? 0);
$user = $userModel->getById($id);

if (!$user) {
    header('Location: ' . APP_URL . '/?page=users&error=' . urlencode('User not found'));
    exit;
}

include __DIR__ . '/../../views/user/edit.php';
