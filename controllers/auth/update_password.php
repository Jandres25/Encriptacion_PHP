<?php

require_once __DIR__ . '/../../model/User.php';

use App\Model\User;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnactualizar'])) {
    $token           = $_POST['token'];
    $newPassword     = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        header('Location: ' . APP_URL . '/?page=reset-password&token=' . urlencode($token) . '&error=' . urlencode('Passwords do not match'));
        exit;
    }

    // Validate token
    $stmt = $connection->prepare(
        "SELECT email FROM password_resets
         WHERE token = ? AND expires_at > NOW() AND used = 0
         ORDER BY created_at DESC LIMIT 1"
    );
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $reset = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($reset) {
        $userModel = new User($connection);
        $userModel->updatePassword($reset['email'], $newPassword);

        $stmtMark = $connection->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
        $stmtMark->bind_param("s", $token);
        $stmtMark->execute();
        $stmtMark->close();

        header('Location: ' . APP_URL . '/?page=login&message=' . urlencode('Password updated successfully'));
        exit;
    }

    header('Location: ' . APP_URL . '/?page=reset-password&token=' . urlencode($token) . '&error=' . urlencode('Invalid or expired token'));
    exit;
}

include __DIR__ . '/../../views/auth/reset_password.php';
