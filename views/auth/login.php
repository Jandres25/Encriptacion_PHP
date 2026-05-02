<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="<?= APP_URL ?>/css/bootstrap.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/all.min.css">
    <link href="<?= APP_URL ?>/img/candado.png" rel="shortcut icon">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/sweetalert2.min.css">
    <script src="<?= APP_URL ?>/js/sweetalert2.all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>Login</title>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center">
        <img class="wave" src="<?= APP_URL ?>/img/wave.png">
        <div class="login-content">
            <form method="post" action="<?= APP_URL ?>/login">
                <img src="<?= APP_URL ?>/img/avatar.svg">
                <h2 class="title">WELCOME</h2>
                <div class="input-div one">
                    <div class="i"><i class="fas fa-user"></i></div>
                    <div class="div">
                        <h5>Username</h5>
                        <input id="usuario" type="text" class="input" name="usuario" required>
                    </div>
                </div>
                <div class="input-div pass">
                    <div class="i"><i class="fas fa-lock"></i></div>
                    <div class="div">
                        <h5>Password</h5>
                        <input type="password" id="input" class="input" name="password" required>
                    </div>
                </div>
                <div class="view">
                    <div class="fas fa-eye verPassword" onclick="vista()" id="verPassword"></div>
                </div>
                <label class="remember-label" for="remember">
                    <input type="checkbox" id="remember" name="remember" value="1">
                    Remember me
                </label>
                <a href="<?= APP_URL ?>/forgot-password" class="forgot-password">Forgot your password?</a>
                <button name="btningresar" class="btn mt-5" type="submit">LOG IN</button>
            </form>
        </div>
    </div>

    <script src="<?= APP_URL ?>/js/popper.min.js"></script>
    <script src="<?= APP_URL ?>/js/bootstrap.min.js"></script>
    <script src="<?= APP_URL ?>/js/sweetalert2.all.min.js"></script>
    <script src="<?= APP_URL ?>/js/main.js"></script>
    <script src="<?= APP_URL ?>/js/main2.js"></script>

    <?php include __DIR__ . '/../layouts/messages.php'; ?>
</body>

</html>