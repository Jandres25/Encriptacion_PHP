<?php

require_once __DIR__ . '/../../model/User.php';

use App\Model\User;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../libs/PHPMailer/src/Exception.php';
require __DIR__ . '/../../libs/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../libs/PHPMailer/src/SMTP.php';

$smtpHost     = $_ENV['SMTP_HOST']     ?? null;
$smtpUsername = $_ENV['SMTP_USERNAME'] ?? null;
$smtpPassword = $_ENV['SMTP_PASSWORD'] ?? null;
$smtpPort     = $_ENV['SMTP_PORT']     ?? null;

if (!$smtpHost || !$smtpUsername || !$smtpPassword || !$smtpPort) {
    die('Error: SMTP environment variables are not configured.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnrecuperar'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    $userModel = new User($connection);
    $user      = $userModel->getByEmail($email);

    if (!$user) {
        header('Location: ' . APP_URL . '/?page=forgot-password&error=' . urlencode('Email not registered in the system'));
        exit;
    }

    // Generate reset token
    $token   = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt = $connection->prepare(
        "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $email, $token, $expires);
    $stmt->execute();
    $stmt->close();

    // Send email
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

        $resetLink = APP_URL . '/?page=reset-password&token=' . $token;

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
        header('Location: ' . APP_URL . '/?message=' . urlencode('A recovery link has been sent to your email'));
        exit;
    } catch (Exception $e) {
        header('Location: ' . APP_URL . '/?page=forgot-password&error=' . urlencode('Failed to send email: ' . $mail->ErrorInfo));
        exit;
    }
}

include __DIR__ . '/../../views/auth/forgot_password.php';
