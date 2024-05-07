<?php
session_start();
$year = date('Y');
$url = "http://localhost/login/";

if (empty($_SESSION["ID"])) {
    header("location:" . $url . "login.php");
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <title>Lista de usuarios</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="stylesheet" href="../../css/estilo.css">
    <link rel="stylesheet" href="../../css/bootstrap.css">
    <link rel="stylesheet" href="../../css/fontawesome.min.css">
    <link href="../../img/usuario.png" rel="shortcut icon">
    <link href="../../DataTables/datatables.css" rel="stylesheet">

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
                    <a class="nav-item nav-link text-nowrap active ml-3 hover-primary" href="<?php echo $url ?>">Volver al inicio</a>
                </div>
                <div class="text-center justify-content-center">
                    <a class="btn btn-primary mr-1" target="_blank" href="https://www.facebook.com"><i class="fab fa-facebook"></i> Facebook</a>
                    <a class="btn btn-danger" target="_blank" href="https://www.youtube.com"><i class="fab fa-youtube"></i> Youtube</a>
                </div>
            </div>
        </nav>
    </header>
    <main class="mt-5">