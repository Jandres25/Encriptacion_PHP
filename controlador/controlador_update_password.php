<?php
require_once('../model/conexion.php');

if (isset($_POST['btnactualizar'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        echo "<script>alert('Las contraseñas no coinciden.');
              window.history.back();</script>";
        exit();
    }

    // Verificar token
    $query = "SELECT email FROM password_resets 
              WHERE token = ? AND expires_at > NOW() 
              AND used = 0 
              ORDER BY created_at DESC LIMIT 1";

    $stmt = mysqli_prepare($conexion, $query);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if ($reset = mysqli_fetch_assoc($resultado)) {
        // Encriptar nueva contraseña
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Actualizar contraseña
        $update_query = "UPDATE usuario SET clave = ? WHERE correo = ?";
        $stmt_update = mysqli_prepare($conexion, $update_query);
        mysqli_stmt_bind_param($stmt_update, "ss", $hashed_password, $reset['email']);
        mysqli_stmt_execute($stmt_update);

        // Marcar token como usado
        $mark_used = "UPDATE password_resets SET used = 1 WHERE token = ?";
        $stmt_mark = mysqli_prepare($conexion, $mark_used);
        mysqli_stmt_bind_param($stmt_mark, "s", $token);
        mysqli_stmt_execute($stmt_mark);

        echo "<script>alert('Contraseña actualizada exitosamente.');
              window.location = '../index.php';</script>";
    } else {
        echo "<script>alert('Token inválido o expirado.');
              window.location = '../index.php';</script>";
    }

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmt_update);
    mysqli_stmt_close($stmt_mark);
}
