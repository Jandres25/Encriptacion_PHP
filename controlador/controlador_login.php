<?php
session_start();
if (!empty($_POST["btningresar"])) {
    if (!empty($_POST["usuario"]) and !empty($_POST["password"])) {
        $usuario = $_POST["usuario"];
        $password = $_POST["password"];
        $sql = $conexion->query("SELECT * FROM `usuario` WHERE Usuario = '$usuario'");
        if ($datos = $sql->fetch_object()) {
            $_SESSION["ID"] = $datos->ID;
            $_SESSION["Nombre"] = $datos->Nombres;
            $_SESSION["EsAdmin"] = $datos->EsAdmin;
            $passwordEncriptado = $datos->Clave;

            if (password_verify($password, $passwordEncriptado)) {
                header("location:./");
            } else {
                echo "<div class='alert alert-danger'>Contraseña Incorrecta</div>";
            }
        } else {
            // El usuario no existe en la base de datos
            echo "<div class='alert alert-danger'>Usuario incorrecto</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Complete todos los campos</div>";
    }
}
