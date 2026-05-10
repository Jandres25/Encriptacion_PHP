<?php

namespace App\Service;

use App\Model\User;

class AuthService
{
    private User $userModel;

    public function __construct(private \mysqli $connection)
    {
        $this->userModel = new User($connection);
    }

    public function verifyCredentials(string $username, string $password): ?array
    {
        $user = $this->userModel->getByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }

        return $user;
    }

    public function issueRememberToken(int $userId): string
    {
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + $this->rememberTtl());
        $this->userModel->setRememberToken($userId, $this->hashToken($token), $expires);
        return $token;
    }

    public function consumeRememberToken(string $rawToken): ?array
    {
        $user = $this->userModel->getByRememberToken($this->hashToken($rawToken));
        return $user ?: null;
    }

    public function clearRememberToken(int $userId): void
    {
        $this->userModel->clearRememberToken($userId);
    }

    public function createPasswordResetToken(string $email): ?string
    {
        $user = $this->userModel->getByEmail($email);

        if (!$user) {
            return null;
        }

        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $this->connection->prepare(
            "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $email, $token, $expires);
        $stmt->execute();
        $stmt->close();

        return $token;
    }

    public function consumeResetToken(string $token, string $newPassword): ?string
    {
        $stmt = $this->connection->prepare(
            "SELECT email FROM password_resets
             WHERE token = ? AND expires_at > NOW() AND used = 0
             ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $reset = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$reset) {
            return null;
        }

        $this->userModel->updatePassword($reset['email'], $newPassword);

        $stmtMark = $this->connection->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        $stmtMark->bind_param("s", $token);
        $stmtMark->execute();
        $stmtMark->close();

        return $reset['email'];
    }

    public function rememberTtl(): int
    {
        return (int) env('REMEMBER_ME_TTL', 2592000);
    }

    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
}
