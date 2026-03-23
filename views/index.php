<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard — SecureAuth</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/bootstrap.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/all.min.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/estilo.css">
    <link href="<?= APP_URL ?>/public/img/boton-de-inicio.png" rel="shortcut icon">
</head>

<body class="dashboard">

    <nav class="navbar navbar-dark bg-dark navbar-expand-lg fixed-top">
        <a class="navbar-brand ml-2" href="<?= APP_URL ?>/">
            <i class="fas fa-shield-alt mr-1"></i> SecureAuth
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link ml-2" href="<?= APP_URL ?>/">
                        <i class="fas fa-home mr-1"></i> Home
                    </a>
                </li>
                <?php if ($isAdmin): ?>
                <li class="nav-item">
                    <a class="nav-link ml-2" href="<?= APP_URL ?>/?page=users">
                        <i class="fas fa-users mr-1"></i> Users
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav mr-2">
                <li class="nav-item">
                    <span class="nav-link text-secondary">
                        <i class="fas fa-user mr-1"></i> <?= htmlspecialchars($name) ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="<?= APP_URL ?>/?page=logout">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <main>
        <!-- Hero -->
        <section class="hero text-white text-center">
            <div class="container py-5">
                <i class="fas fa-user-circle fa-4x mb-3 text-light"></i>
                <h1 class="display-4 font-weight-bold mb-2">
                    Welcome, <?= htmlspecialchars($name) ?>
                </h1>
                <p class="lead mb-4 text-light">
                    Secure authentication system with bcrypt password hashing<br>
                    and email-based password recovery.
                </p>
                <?php if ($isAdmin): ?>
                <a href="<?= APP_URL ?>/?page=users" class="btn btn-app-primary btn-lg px-4">
                    <i class="fas fa-users mr-2"></i> Manage Users
                </a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Feature cards -->
        <section class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm text-center">
                        <div class="card-body py-4">
                            <div class="feature-icon text-white mx-auto" style="background-color: var(--color-dark);">
                                <i class="fas fa-lock"></i>
                            </div>
                            <h5 class="card-title font-weight-bold">Secure Login</h5>
                            <p class="card-text text-muted">
                                Passwords hashed with bcrypt (<code>PASSWORD_DEFAULT</code>).
                                Session variables set only after successful <code>password_verify()</code>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm text-center">
                        <div class="card-body py-4">
                            <div class="feature-icon text-white mx-auto" style="background-color: var(--color-dark);">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title font-weight-bold">User Management</h5>
                            <p class="card-text text-muted">
                                Full CRUD for users with role-based access control.
                                Only administrators can create, edit, or delete accounts.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm text-center">
                        <div class="card-body py-4">
                            <div class="feature-icon text-white mx-auto" style="background-color: var(--color-dark);">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h5 class="card-title font-weight-bold">Password Recovery</h5>
                            <p class="card-text text-muted">
                                Token-based reset via PHPMailer with STARTTLS.
                                Tokens expire after 1 hour and are single-use.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 60px;">
            <span>&copy; <?= $year ?> All Rights Reserved — UPDS</span>
        </div>
    </footer>

    <script src="<?= APP_URL ?>/public/js/jquery-3.3.1.slim.min.js"></script>
    <script src="<?= APP_URL ?>/public/js/popper.min.js"></script>
    <script src="<?= APP_URL ?>/public/js/bootstrap.min.js"></script>
</body>

</html>
