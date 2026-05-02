<?php

namespace App\Controller\User;

use App\Model\User;

require_once __DIR__ . '/../../Model/User.php';
require_once __DIR__ . '/../auth/AuthController.php';

class UserController
{
    private User $userModel;

    public function __construct(private \mysqli $connection)
    {
        $this->userModel = new User($connection);
    }

    private function requireAuth(): void
    {
        (new \App\Controller\Auth\AuthController($this->connection))->checkSessionTimeout();
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    private function requireAdmin(): void
    {
        $this->requireAuth();
        if (empty($_SESSION['is_admin'])) {
            header('Location: ' . APP_URL . '/');
            exit;
        }
    }

    public function index(): void
    {
        $this->requireAdmin();

        $users = $this->userModel->getAll();

        renderProtectedView('user/index.php', [
            'users' => $users,
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
            $data = [
                'first_name' => $_POST['first_name'],
                'last_name'  => $_POST['last_name'],
                'email'      => $_POST['email'] ?? '',
                'username'   => $_POST['username'],
                'password'   => $_POST['password'],
                'is_admin'   => isset($_POST['is_admin']) ? 1 : 0,
            ];

            if ($this->userModel->create($data)) {
                $_SESSION['message'] = 'User added successfully';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/users');
            } else {
                $_SESSION['message'] = 'Failed to create user';
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/users/create');
            }
            exit;
        }

        renderProtectedView('user/create.php');
    }

    public function edit(): void
    {
        $this->requireAdmin();

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

            if ($this->userModel->update($id, $data)) {
                $_SESSION['message'] = 'User updated successfully';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/users');
            } else {
                $_SESSION['message'] = 'Failed to update user';
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/users/edit?id=' . $id);
            }
            exit;
        }

        $id   = (int) ($_GET['id'] ?? 0);
        $user = $this->userModel->getById($id);

        if (!$user) {
            $_SESSION['message'] = 'User not found';
            $_SESSION['icon']    = 'error';
            header('Location: ' . APP_URL . '/users');
            exit;
        }

        renderProtectedView('user/edit.php', [
            'user' => $user,
        ]);
    }

    public function delete(): void
    {
        $this->requireAdmin();

        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];

            if ($this->userModel->delete($id)) {
                $_SESSION['message'] = 'User deleted successfully';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/users');
            } else {
                $_SESSION['message'] = 'Failed to delete user';
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/users');
            }
            exit;
        }

        header('Location: ' . APP_URL . '/users');
        exit;
    }
}
