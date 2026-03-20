<?php
require_once __DIR__ . '/../config/config.php';
$year = date('Y');

if (empty($_SESSION['user_id'])) {
    header('Location: ' . APP_URL . '/?page=login');
    exit;
}

$year = date('Y');

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>User List</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/estilo.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/bootstrap.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/fontawesome.min.css">
    <link href="<?= APP_URL ?>/public/img/usuario.png" rel="shortcut icon">
    <link href="<?= APP_URL ?>/public/DataTables/datatables.css" rel="stylesheet">

    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex-grow: 1;
        }
    </style>
</head>

<body>
    <header>
        <!-- place navbar here -->
        <nav class="navbar navbar-dark bg-dark navbar-expand-lg">
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <div class="navbar-nav mr-auto">
                    <div class="mr-auto text-center"></div>
                    <a class="nav-item nav-link text-nowrap active ml-3 hover-primary" href="<?= APP_URL ?>/">Back to Home</a>
                </div>
                <div class="text-center justify-content-center">
                    <a class="btn btn-primary mr-1" target="_blank" href="https://www.facebook.com"><i class="fab fa-facebook"></i> Facebook</a>
                    <a class="btn btn-danger" target="_blank" href="https://www.youtube.com"><i class="fab fa-youtube"></i> Youtube</a>
                </div>
            </div>
        </nav>
    </header>
    <main class="mt-5">