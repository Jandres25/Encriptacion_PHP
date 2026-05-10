<?php

namespace App\Controller\User;

use App\Service\UserService;

class UserController
{
    private UserService $userService;

    public function __construct(private \mysqli $connection)
    {
        $this->userService = new UserService($connection);
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

        renderProtectedView('user/index.php', [
            'users' => $this->userService->getAll(),
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

            $result = $this->userService->create($data);

            if ($result === true) {
                $_SESSION['message'] = 'User added successfully';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/users');
            } else {
                $_SESSION['message'] = $result;
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

            $result = $this->userService->update($id, $data);

            if ($result === true) {
                $_SESSION['message'] = 'User updated successfully';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/users');
            } else {
                $_SESSION['message'] = $result;
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/users/edit?id=' . $id);
            }
            exit;
        }

        $id   = (int) ($_GET['id'] ?? 0);
        $user = $this->userService->getById($id);

        if (!$user) {
            $_SESSION['message'] = 'User not found';
            $_SESSION['icon']    = 'error';
            header('Location: ' . APP_URL . '/users');
            exit;
        }

        renderProtectedView('user/edit.php', ['user' => $user]);
    }

    public function delete(): void
    {
        $this->requireAdmin();

        if (isset($_GET['id'])) {
            $id     = (int) $_GET['id'];
            $result = $this->userService->delete($id);

            $_SESSION['message'] = $result === true ? 'User deleted successfully' : $result;
            $_SESSION['icon']    = $result === true ? 'success' : 'error';
            header('Location: ' . APP_URL . '/users');
            exit;
        }

        header('Location: ' . APP_URL . '/users');
        exit;
    }
}
