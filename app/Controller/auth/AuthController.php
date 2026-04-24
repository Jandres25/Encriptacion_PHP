<?php

namespace App\Controller\Auth;

use App\Model\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../../libs/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../../../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../../../libs/PHPMailer/src/SMTP.php';

require_once __DIR__ . '/../../Model/User.php';

class AuthController
{
    private User $userModel;

    public function __construct(private \mysqli $connection)
    {
        $this->userModel = new User($connection);
    }

    public function login(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . APP_URL . '/');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btningresar'])) {
            if (!empty($_POST['usuario']) && !empty($_POST['password'])) {
                $user = $this->userModel->getByUsername($_POST['usuario']);

                if ($user && password_verify($_POST['password'], $user['password'])) {
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['name']     = $user['first_name'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    $_SESSION['message']  = "Welcome, " . $user['first_name'] . "!";
                    $_SESSION['icon']     = "success";
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
        session_destroy();
        session_start();
        $_SESSION['message'] = 'Logged out successfully';
        $_SESSION['icon']    = 'success';
        header('Location: ' . APP_URL . '/login');
        exit;
    }

    public function forgotPassword(): void
    {
        $smtpHost     = $_ENV['SMTP_HOST']     ?? null;
        $smtpUsername = $_ENV['SMTP_USERNAME'] ?? null;
        $smtpPassword = $_ENV['SMTP_PASSWORD'] ?? null;
        $smtpPort     = $_ENV['SMTP_PORT']     ?? null;

        if (!$smtpHost || !$smtpUsername || !$smtpPassword || !$smtpPort) {
            die('Error: SMTP environment variables are not configured.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnrecuperar'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $user  = $this->userModel->getByEmail($email);

            if (!$user) {
                $_SESSION['message'] = 'Email not registered in the system';
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/forgot-password');
                exit;
            }

            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $this->connection->prepare(
                "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)"
            );
            $stmt->bind_param("sss", $email, $token, $expires);
            $stmt->execute();
            $stmt->close();

            $mail = new PHPMailer(true);

            try {
                $mail->SMTPDebug = SMTP::DEBUG_OFF;
                $mail->isSMTP();
                $mail->Host       = $smtpHost;
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtpUsername;
                $mail->Password   = $smtpPassword;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = (int) $smtpPort;
                $mail->CharSet    = 'UTF-8';

                $mail->setFrom($smtpUsername, 'Password Recovery');
                $mail->addAddress($email);

                $resetLink = APP_URL . '/reset-password?token=' . urlencode($token);

                $mail->isHTML(true);
                $mail->Subject = 'Password Recovery';
                $mail->Body    = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; border-radius: 10px; }
                            .header { background: #2d3748; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                            .content { background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                            .button { display: inline-block; padding: 12px 24px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
                            .warning { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 10px; border-radius: 5px; margin-top: 20px; font-size: 13px; }
                            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'><h1 style='margin:0;'>Password Recovery</h1></div>
                            <div class='content'>
                                <p>Hello,</p>
                                <p>We received a request to reset your account password. If you did not make this request, you can ignore this email.</p>
                                <div style='text-align:center;'>
                                    <a href='{$resetLink}' class='button' style='color:white;'>Reset Password</a>
                                </div>
                                <p>Or copy and paste the following link in your browser:</p>
                                <p style='word-break:break-all; font-size:14px; color:#666;'>{$resetLink}</p>
                                <div class='warning'>
                                    <strong>Important!</strong>
                                    <p style='margin:5px 0 0 0;'>This link will expire in 1 hour for security reasons.</p>
                                </div>
                            </div>
                            <div class='footer'>
                                <p>This is an automated email, please do not reply.</p>
                                <p>&copy; " . date('Y') . " Your System. All rights reserved.</p>
                            </div>
                        </div>
                    </body>
                    </html>";

                $mail->send();
                $_SESSION['message'] = 'A recovery link has been sent to your email';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/');
                exit;
            } catch (Exception $e) {
                $_SESSION['message'] = 'Failed to send email: ' . $mail->ErrorInfo;
                $_SESSION['icon']    = 'error';
                header('Location: ' . APP_URL . '/forgot-password');
                exit;
            }
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

            $stmt = $this->connection->prepare(
                "SELECT email FROM password_resets
                 WHERE token = ? AND expires_at > NOW() AND used = 0
                 ORDER BY created_at DESC LIMIT 1"
            );
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $reset = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($reset) {
                $this->userModel->updatePassword($reset['email'], $newPassword);

                $stmtMark = $this->connection->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
                $stmtMark->bind_param("s", $token);
                $stmtMark->execute();
                $stmtMark->close();

                $_SESSION['message'] = 'Password updated successfully';
                $_SESSION['icon']    = 'success';
                header('Location: ' . APP_URL . '/login');
                exit;
            }

            $_SESSION['message'] = 'Invalid or expired token';
            $_SESSION['icon']    = 'error';
            header('Location: ' . APP_URL . '/reset-password?token=' . urlencode($token));
            exit;
        }

        include __DIR__ . '/../../../views/auth/reset_password.php';
    }
}
