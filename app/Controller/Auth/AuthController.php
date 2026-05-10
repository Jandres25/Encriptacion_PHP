<?php

namespace App\Controller\Auth;

use App\Service\AuthService;
use App\Service\MailerService;

class AuthController
{
    private const REMEMBER_COOKIE = 'remember_me';

    private AuthService   $authService;
    private MailerService $mailerService;

    public function __construct(private \mysqli $connection)
    {
        $this->authService   = new AuthService($connection);
        $this->mailerService = new MailerService();
    }

    private function rememberEnabled(): bool
    {
        return filter_var(env('REMEMBER_ME_ENABLED', false), FILTER_VALIDATE_BOOLEAN);
    }

    private function cookieOptions(int $maxAge): array
    {
        return [
            'expires'  => time() + $maxAge,
            'path'     => '/',
            'secure'   => str_starts_with(env('APP_URL', ''), 'https'),
            'httponly' => true,
            'samesite' => 'Strict',
        ];
    }

    public function login(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btningresar'])) {
            if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
                $user = $this->authService->verifyCredentials($_POST['usuario'], $_POST['password']);

                if ($user) {
                    $_SESSION['user_id']       = $user['id'];
                    $_SESSION['name']          = $user['first_name'];
                    $_SESSION['is_admin']      = $user['is_admin'];
                    $_SESSION['last_activity'] = time();
                    $_SESSION['message']       = "Welcome, " . $user['first_name'] . "!";
                    $_SESSION['icon']          = "success";

                    if ($this->rememberEnabled() && !empty($_POST['remember'])) {
                        $token = $this->authService->issueRememberToken($user['id']);
                        setcookie(self::REMEMBER_COOKIE, $token, $this->cookieOptions($this->authService->rememberTtl()));
                    }

                    header('Location: ' . APP_URL . '/');
                    exit;
                }

                $_SESSION['message'] = 'Incorrect username or password';
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/login');
                exit;
            }

            $_SESSION['message'] = 'Please fill in all fields';
            $_SESSION['icon']    = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        include __DIR__ . '/../../../views/auth/login.php';
    }

    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId && isset($_COOKIE[self::REMEMBER_COOKIE])) {
            $this->authService->clearRememberToken((int) $userId);
            setcookie(self::REMEMBER_COOKIE, '', $this->cookieOptions(-3600));
        }

        session_destroy();
        session_start();
        $_SESSION['message'] = 'Logged out successfully';
        $_SESSION['icon']    = 'success';
        header('Location: ' . APP_URL . '/login');
        exit;
    }

    public function restoreFromCookie(): void
    {
        if (!empty($_SESSION['user_id'])) {
            return;
        }

        if (!$this->rememberEnabled() || empty($_COOKIE[self::REMEMBER_COOKIE])) {
            return;
        }

        $user = $this->authService->consumeRememberToken($_COOKIE[self::REMEMBER_COOKIE]);

        if (!$user) {
            setcookie(self::REMEMBER_COOKIE, '', $this->cookieOptions(-3600));
            return;
        }

        $_SESSION['user_id']       = $user['id'];
        $_SESSION['name']          = $user['first_name'];
        $_SESSION['is_admin']      = $user['is_admin'];
        $_SESSION['last_activity'] = time();
    }

    public function checkSessionTimeout(): void
    {
        if (empty($_SESSION['user_id'])) {
            return;
        }

        $timeout = (int) env('SESSION_TIMEOUT', 1800);

        if (time() - ($_SESSION['last_activity'] ?? time()) > $timeout) {
            $this->authService->clearRememberToken((int) $_SESSION['user_id']);
            if (isset($_COOKIE[self::REMEMBER_COOKIE])) {
                setcookie(self::REMEMBER_COOKIE, '', $this->cookieOptions(-3600));
            }
            session_destroy();
            session_start();
            $_SESSION['message'] = 'Your session has expired due to inactivity';
            $_SESSION['icon']    = 'warning';
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $_SESSION['last_activity'] = time();
    }

    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnrecuperar'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $token = $this->authService->createPasswordResetToken($email);

            if (!$token) {
                $_SESSION['message'] = 'Email not registered in the system';
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/forgot-password');
                exit;
            }

            $resetUrl = APP_URL . '/reset-password?token=' . urlencode($token);
            $sent     = $this->mailerService->sendResetEmail($email, $resetUrl);

            if ($sent) {
                $_SESSION['message'] = 'A recovery link has been sent to your email';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/');
            } else {
                $_SESSION['message'] = 'Failed to send recovery email';
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/forgot-password');
            }
            exit;
        }

        include __DIR__ . '/../../../views/auth/forgot_password.php';
    }

    public function resetPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnactualizar'])) {
            $token           = $_POST['token'];
            $newPassword     = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($newPassword !== $confirmPassword) {
                $_SESSION['message'] = 'Passwords do not match';
                $_SESSION['icon']    = 'warning';
                header('Location: ' . APP_URL . '/reset-password?token=' . urlencode($token));
                exit;
            }

            $email = $this->authService->consumeResetToken($token, $newPassword);

            if ($email) {
                $_SESSION['message'] = 'Password updated successfully';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/login');
            } else {
                $_SESSION['message'] = 'Invalid or expired token';
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/reset-password?token=' . urlencode($token));
            }
            exit;
        }

        include __DIR__ . '/../../../views/auth/reset_password.php';
    }
}
