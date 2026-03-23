<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/bootstrap.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/all.min.css">
    <link href="<?= APP_URL ?>/public/img/candado.png" rel="shortcut icon">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>Forgot Password</title>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center">
        <img class="wave" src="<?= APP_URL ?>/public/img/wave.png">
        <div class="login-content">
            <form method="post" action="<?= APP_URL ?>/?page=forgot-password">
                <img src="<?= APP_URL ?>/public/img/avatar.svg">
                <h2 class="title">Forgot Password</h2>
                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['flash_error']) ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>
                <div class="input-div one">
                    <div class="i"><i class="fas fa-lock"></i></div>
                    <div class="div">
                        <h5>Email Address</h5>
                        <input type="email" class="input" name="email" required>
                    </div>
                </div>
                <button name="btnrecuperar" class="btn mt-5" type="submit">SEND LINK</button>
                <a href="<?= APP_URL ?>/?page=login" class="btn btn-anchor">Back to Login</a>
            </form>
        </div>
    </div>

    <script src="<?= APP_URL ?>/public/js/popper.min.js"></script>
    <script src="<?= APP_URL ?>/public/js/bootstrap.min.js"></script>
    <script src="<?= APP_URL ?>/public/js/main.js"></script>
    <script src="<?= APP_URL ?>/public/js/main2.js"></script>
</body>

</html>