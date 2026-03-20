<?php

require_once __DIR__ . '/../../model/User.php';

use App\Model\User;

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: ' . APP_URL . '/?page=login');
    exit;
}

if (isset($_GET['id'])) {
    $id        = (int) $_GET['id'];
    $userModel = new User($connection);

    if ($userModel->delete($id)) {
        header('Location: ' . APP_URL . '/?page=users&message=' . urlencode('User deleted successfully'));
    } else {
        header('Location: ' . APP_URL . '/?page=users&error=' . urlencode('Failed to delete user'));
    }
    exit;
}

header('Location: ' . APP_URL . '/?page=users');
exit;
