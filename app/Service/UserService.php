<?php

namespace App\Service;

use App\Model\User;

class UserService
{
    private User $userModel;

    public function __construct(\mysqli $connection)
    {
        $this->userModel = new User($connection);
    }

    public function getAll(): array
    {
        return $this->userModel->getAll();
    }

    public function getById(int $id): ?array
    {
        return $this->userModel->getById($id);
    }

    /** @return true|string True on success, error message string on failure. */
    public function create(array $data): true|string
    {
        if (strlen($data['password']) < 8) {
            return 'Password must be at least 8 characters';
        }

        if ($this->userModel->getByEmail($data['email'])) {
            return 'Email is already registered';
        }

        if ($this->userModel->getByUsername($data['username'])) {
            return 'Username is already taken';
        }

        return $this->userModel->create($data) ? true : 'Failed to create user';
    }

    /** @return true|string True on success, error message string on failure. */
    public function update(int $id, array $data): true|string
    {
        if (!empty($data['password']) && strlen($data['password']) < 8) {
            return 'Password must be at least 8 characters';
        }

        $existing = $this->userModel->getByEmail($data['email']);
        if ($existing && (int) $existing['id'] !== $id) {
            return 'Email is already registered by another user';
        }

        $existingUsername = $this->userModel->getByUsername($data['username']);
        if ($existingUsername && (int) $existingUsername['id'] !== $id) {
            return 'Username is already taken by another user';
        }

        return $this->userModel->update($id, $data) ? true : 'Failed to update user';
    }

    /** @return true|string True on success, error message string on failure. */
    public function delete(int $id): true|string
    {
        return $this->userModel->delete($id) ? true : 'Failed to delete user';
    }
}
