<!DOCTYPE html>
<html lang="en">

<head>
    <title>User Management</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/estilo.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/bootstrap.css">
    <link rel="preload" href="<?= APP_URL ?>/public/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= APP_URL ?>/public/webfonts/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/all.min.css">
    <link href="<?= APP_URL ?>/public/img/usuario.png" rel="shortcut icon">
    <link href="<?= APP_URL ?>/public/DataTables/datatables.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/layout-protected.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
            <a class="navbar-brand ml-2" href="<?= APP_URL ?>/">
                <i class="fas fa-shield-alt mr-1"></i> User Management
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav ml-auto mr-2">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= APP_URL ?>/">
                            <i class="fas fa-home mr-1"></i> Back to Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?= APP_URL ?>/logout">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main class="mt-3">