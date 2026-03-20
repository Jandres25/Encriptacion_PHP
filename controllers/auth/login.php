<?php

require_once __DIR__ . '/../../model/User.php';

use App\Model\User;

if (!empty($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['btningresar'])) {
    if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
        $userModel = new User($connection);
        $user      = $userModel->getByUsername($_POST['usuario']);

        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['name']     = $user['first_name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header('Location: ' . APP_URL . '/');
            exit;
        }

        header('Location: ' . APP_URL . '/?page=login&error=' . urlencode('Incorrect username or password'));
        exit;
    }

    header('Location: ' . APP_URL . '/?page=login&error=' . urlencode('Please fill in all fields'));
    exit;
}

include __DIR__ . '/../../views/auth/login.php';
