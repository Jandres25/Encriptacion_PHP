<?php
require_once('../model/conexion.php');

//Importar clases PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Cargar autoloader de PHPMailer
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

if (isset($_POST['btnrecuperar'])) {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Verificar si el email existe
    $consulta = "SELECT * FROM usuario WHERE correo = ?";
    $stmt = mysqli_prepare($conexion, $consulta);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        // Generar token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Guardar token
        $insert_query = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
        $stmt_insert = mysqli_prepare($conexion, $insert_query);
        mysqli_stmt_bind_param($stmt_insert, "sss", $email, $token, $expires);
        mysqli_stmt_execute($stmt_insert);

        // Configurar PHPMailer
        $mail = new PHPMailer(true); // true habilita excepciones

        try {
            //Configuración del servidor
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // 0 = off (producción), 1 = client messages, 2 = client and server messages
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jandrespb4@gmail.com'; // Tu correo Gmail
            $mail->Password = 'uaif xibx siaa pgtc'; // Tu contraseña de aplicación de Gmail
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Cambiar a SMTPS
            $mail->Port = 465; // Puerto para SMTPS
            $mail->CharSet = 'UTF-8';

            //Destinatarios
            $mail->setFrom('jandrespb4@gmail.com', 'Sistema de Recuperación de Contraseñas');
            $mail->addAddress($email);

            //Contenido
            $resetLink = "http://localhost/login/reset_password.php?token=" . $token;

            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de Contraseña';
            $mail->Body = "
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            color: #333333;
                        }
                        .email-container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            background-color: #f9f9f9;
                            border-radius: 10px;
                        }
                        .header {
                            background-color: #2d3748;
                            color: white;
                            padding: 20px;
                            text-align: center;
                            border-radius: 10px 10px 0 0;
                        }
                        .content {
                            background-color: white;
                            padding: 30px;
                            border-radius: 0 0 10px 10px;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        }
                        .button {
                            display: inline-block;
                            padding: 12px 24px;
                            background-color: #4CAF50;
                            color: white;
                            text-decoration: none;
                            border-radius: 5px;
                            margin: 20px 0;
                            font-weight: bold;
                        }
                        .footer {
                            text-align: center;
                            margin-top: 20px;
                            font-size: 12px;
                            color: #666;
                        }
                        .warning {
                            background-color: #fff3cd;
                            border: 1px solid #ffeeba;
                            color: #856404;
                            padding: 10px;
                            border-radius: 5px;
                            margin-top: 20px;
                            font-size: 13px;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='header'>
                            <h1 style='margin:0;'>Recuperación de Contraseña</h1>
                        </div>
                        <div class='content'>
                            <p>Hola,</p>
                            <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. 
                            Si no realizaste esta solicitud, puedes ignorar este correo.</p>
                            
                            <div style='text-align: center;'>
                                <a href='{$resetLink}' class='button' style='color: white;'>
                                    Restablecer Contraseña
                                </a>
                            </div>
                            
                            <p>O copia y pega el siguiente enlace en tu navegador:</p>
                            <p style='word-break: break-all; font-size: 14px; color: #666;'>
                                {$resetLink}
                            </p>
                            
                            <div class='warning'>
                                <strong>¡Importante!</strong>
                                <p style='margin: 5px 0 0 0;'>
                                    Este enlace expirará en 1 hora por razones de seguridad.
                                    Si el enlace ha expirado, deberás solicitar uno nuevo.
                                </p>
                            </div>
                        </div>
                        
                        <div class='footer'>
                            <p>Este es un correo automático, por favor no responder.</p>
                            <p>&copy; " . date('Y') . " Tu Sistema. Todos los derechos reservados.</p>
                        </div>
                    </div>
                </body>
                </html>
                ";

            $mail->send();
            echo "<script>alert('Se ha enviado un enlace de recuperación a tu correo.');
                  window.location = '../index.php'</script>";
        } catch (Exception $e) {
            echo "<script>alert('Error al enviar el correo: " . $mail->ErrorInfo . "');
                  window.location = '../forgot_password.php'</script>";
        }
    } else {
        echo "<script>alert('El correo no está registrado en el sistema.');
              window.location = '../forgot_password.php'</script>";
    }

    mysqli_stmt_close($stmt);
    if (isset($stmt_insert)) mysqli_stmt_close($stmt_insert);
}
