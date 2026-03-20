<?php require_once __DIR__ . '/config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/css/bootstrap.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/fontawesome.min.css">
    <link href="<?= APP_URL ?>/img/candado.png" rel="shortcut icon">

    <!-- Primero jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <title>Recuperar Contraseña</title>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center">
        <img class="wave" src="<?= APP_URL ?>/img/wave.png">
        <div class="login-content">
            <form method="post" action="<?= APP_URL ?>/controlador/controlador_reset.php">
                <img src="<?= APP_URL ?>/img/avatar.svg">
                <h2 class="title">Recuperar Contraseña</h2>
                <div class="input-div one">
                    <div class="i">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="div">
                        <h5>Correo Electrónico</h5>
                        <input type="email" class="input" name="email" required>
                    </div>
                </div>
                <input name="btnrecuperar" class="btn mt-5" type="submit" value="ENVIAR ENLACE">
                <a href="<?= APP_URL ?>/" class="btn mt-3">Volver al login</a>
            </form>
        </div>
    </div>

    <!-- Luego Popper.js -->
    <script src="<?= APP_URL ?>/js/popper.min.js"></script>

    <!-- Después Bootstrap -->
    <script src="<?= APP_URL ?>/js/bootstrap.bundle.js"></script>
    <script src="<?= APP_URL ?>/js/bootstrap.js"></script>

    <!-- Finalmente tus scripts -->
    <script src="<?= APP_URL ?>/js/main.js"></script>
    <script src="<?= APP_URL ?>/js/main2.js"></script>
    <script src="<?= APP_URL ?>/js/fontawesome.js"></script>
</body>

</html>