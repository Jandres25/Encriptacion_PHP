<?php

namespace App\Controller;

use App\Core\Auth;
use App\Core\Controller;
use App\Middleware\AuthMiddleware;
use App\Service\MailerService;

class AuthController extends Controller
{
    private const REMEMBER_COOKIE = 'remember_me';

    private Auth         $auth;
    private MailerService $mailer;

    public function __construct(\mysqli $connection)
    {
        parent::__construct($connection);
        $this->auth   = new Auth($connection);
        $this->mailer = new MailerService();
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
            $this->redirect('/');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btningresar'])) {
            if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
                $user = $this->auth->verifyCredentials($_POST['usuario'], $_POST['password']);

                if ($user) {
                    $_SESSION['user_id']       = $user['id'];
                    $_SESSION['name']          = $user['first_name'];
                    $_SESSION['is_admin']      = $user['is_admin'];
                    $_SESSION['last_activity'] = time();
                    $_SESSION['message']       = "Welcome, " . $user['first_name'] . "!";
                    $_SESSION['icon']          = 'success';

                    if ($this->rememberEnabled() && !empty($_POST['remember'])) {
                        $token = $this->auth->issueRememberToken($user['id']);
                        setcookie(self::REMEMBER_COOKIE, $token, $this->cookieOptions($this->auth->rememberTtl()));
                    }

                    $this->redirect('/');
                }

                $_SESSION['message'] = 'Incorrect username or password';
                $_SESSION['icon']    = 'error';
                $this->redirect('/login');
            }

            $_SESSION['message'] = 'Please fill in all fields';
            $_SESSION['icon']    = 'warning';
            $this->redirect('/login');
        }

        $this->render('auth/login.php');
    }

    public function logout(): void
    {
        $userId = $_SESSION['user_id'] ?? null;

        if ($userId && isset($_COOKIE[self::REMEMBER_COOKIE])) {
            $this->auth->clearRememberToken((int) $userId);
            setcookie(self::REMEMBER_COOKIE, '', $this->cookieOptions(-3600));
        }

        session_destroy();
        session_start();
        $_SESSION['message'] = 'Logged out successfully';
        $_SESSION['icon']    = 'success';
        $this->redirect('/login');
    }

    public function forgotPassword(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnrecuperar'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $token = $this->auth->createPasswordResetToken($email);

            if (!$token) {
                $_SESSION['message'] = 'Email not registered in the system';
                $_SESSION['icon']    = 'error';
                $this->redirect('/forgot-password');
            }

            $resetUrl = APP_URL . '/reset-password?token=' . urlencode($token);

            if ($this->mailer->sendResetEmail($email, $resetUrl)) {
                $_SESSION['message'] = 'A recovery link has been sent to your email';
                $_SESSION['icon']    = 'success';
                $this->redirect('/');
            }

            $_SESSION['message'] = 'Failed to send recovery email';
            $_SESSION['icon']    = 'error';
            $this->redirect('/forgot-password');
        }

        $this->render('auth/forgot_password.php');
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
                $this->redirect('/reset-password?token=' . urlencode($token));
            }

            $email = $this->auth->consumeResetToken($token, $newPassword);

            if ($email) {
                $_SESSION['message'] = 'Password updated successfully';
                $_SESSION['icon']    = 'success';
                $this->redirect('/login');
            }

            $_SESSION['message'] = 'Invalid or expired token';
            $_SESSION['icon']    = 'error';
            $this->redirect('/reset-password?token=' . urlencode($token));
        }

        $this->render('auth/reset_password.php');
    }
}
