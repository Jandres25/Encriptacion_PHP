<?php

namespace App\Controller\User;

use App\Model\User;

require_once __DIR__ . '/../../model/User.php';

class UserController
{
    private User $userModel;

    public function __construct(private \mysqli $connection)
    {
        $this->userModel = new User($connection);
    }

    private function requireAuth(): void
    {
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

        $users   = $this->userModel->getAll();
        $message = $_GET['message'] ?? '';
        $error   = $_GET['error'] ?? '';

        renderProtectedView('user/index.php', [
            'users' => $users,
            'message' => $message,
            'error' => $error,
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
                header('Location: ' . APP_URL . '/users?message=' . urlencode('User added successfully'));
            } else {
                header('Location: ' . APP_URL . '/users/create?error=' . urlencode('Failed to create user'));
            }
            exit;
        }

        renderProtectedView('user/create.php', [
            'error' => $_GET['error'] ?? '',
        ]);
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
                header('Location: ' . APP_URL . '/users?message=' . urlencode('User updated successfully'));
            } else {
                header('Location: ' . APP_URL . '/users/edit?id=' . $id . '&error=' . urlencode('Failed to update user'));
            }
            exit;
        }

        $id   = (int) ($_GET['id'] ?? 0);
        $user = $this->userModel->getById($id);

        if (!$user) {
            header('Location: ' . APP_URL . '/users?error=' . urlencode('User not found'));
            exit;
        }

        renderProtectedView('user/edit.php', [
            'user' => $user,
            'error' => $_GET['error'] ?? '',
        ]);
    }

    public function delete(): void
    {
        $this->requireAdmin();

        if (isset($_GET['id'])) {
            $id = (int) $_GET['id'];

            if ($this->userModel->delete($id)) {
                header('Location: ' . APP_URL . '/users?message=' . urlencode('User deleted successfully'));
            } else {
                header('Location: ' . APP_URL . '/users?error=' . urlencode('Failed to delete user'));
            }
            exit;
        }

        header('Location: ' . APP_URL . '/users');
        exit;
    }
}
