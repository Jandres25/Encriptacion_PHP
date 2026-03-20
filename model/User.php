<?php

namespace App\Model;

use mysqli;

class User
{
    public function __construct(private mysqli $connection) {}

    public function getAll(): array
    {
        $result = $this->connection->query("SELECT * FROM users");
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function getByUsername(string $username): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function getByEmail(string $email): ?array
    {
        $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $user ?: null;
    }

    public function create(array $data): bool
    {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->connection->prepare(
            "INSERT INTO users (first_name, last_name, email, username, password, is_admin)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssssi",
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['username'],
            $hashedPassword,
            $data['is_admin']
        );
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }

    public function update(int $id, array $data): bool
    {
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            $current = $this->getById($id);
            $hashedPassword = $current['password'];
        }
        $stmt = $this->connection->prepare(
            "UPDATE users
             SET first_name=?, last_name=?, email=?, username=?, password=?, is_admin=?
             WHERE id=?"
        );
        $stmt->bind_param(
            "sssssii",
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['username'],
            $hashedPassword,
            $data['is_admin'],
            $id
        );
        $stmt->execute();
        $success = $stmt->affected_rows >= 0;
        $stmt->close();
        return $success;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }

    public function updatePassword(string $email, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->connection->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashedPassword, $email);
        $stmt->execute();
        $success = $stmt->affected_rows > 0;
        $stmt->close();
        return $success;
    }
}
