<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/estilo.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/bootstrap.css">
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/fontawesome.min.css">
    <link href="<?= APP_URL ?>/public/img/boton-de-inicio.png" rel="shortcut icon">
</head>

<body>
    <div class="bd-example mb-0 mt-5" style="height: 100vh">
        <div id="carouselExampleCaptions" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                <li data-target="#carouselExampleCaptions" data-slide-to="0" class="active"></li>
                <li data-target="#carouselExampleCaptions" data-slide-to="1"></li>
                <li data-target="#carouselExampleCaptions" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active" style="height: 100vh">
                    <img src="<?= APP_URL ?>/public/img/imagen1.jpg" class="d-block w-100 h-100 img-fluid" alt="">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 class="display-4 mb-4 font-weight-bold">BOOTSTRAP</h5>
                        <p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
                    </div>
                </div>
                <div class="carousel-item" style="height: 100vh">
                    <img src="<?= APP_URL ?>/public/img/imagen2.jpg" class="d-block w-100 h-100 img-fluid" alt="">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 class="display-4 mb-4 font-weight-bold">BOOTSTRAP</h5>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    </div>
                </div>
                <div class="carousel-item" style="height: 100vh">
                    <img src="<?= APP_URL ?>/public/img/imagen3.jpg" class="d-block w-100 h-100 img-fluid" alt="">
                    <div class="carousel-caption d-none d-md-block">
                        <h5 class="display-4 mb-4 font-weight-bold">BOOTSTRAP</h5>
                        <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#carouselExampleCaptions" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleCaptions" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    <nav class="navbar navbar-dark bg-dark navbar-expand-lg fixed-top">
        <div class="text-white p-2">
            Welcome: <?= htmlspecialchars($name) ?>
        </div>
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <div class="navbar-nav mr-auto">
                <div class="offset-md-1 mr-auto text-center"></div>
                <a class="nav-item nav-link text-justify active ml-3 hover-primary" href="<?= APP_URL ?>/">Home</a>
                <a class="nav-item nav-link text-justify ml-3 hover-primary" href="<?= APP_URL ?>/">About</a>
                <?php if ($isAdmin): ?>
                    <a class="nav-item nav-link text-nowrap ml-3 hover-primary" href="<?= APP_URL ?>/?page=users">Users</a>
                <?php endif; ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-justify ml-3" href="" id="navbarDropdownMenuLink"
                       role="button" data-toggle="dropdown">
                        Services
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <a class="dropdown-item" href="#">FAQ</a>
                        <a class="dropdown-item" href="#">Shop</a>
                        <a class="dropdown-item" href="#">Other</a>
                    </div>
                </li>
                <a class="nav-item nav-link text-justify ml-3 hover-primary"
                   href="<?= APP_URL ?>/?page=logout">Logout</a>
            </div>
            <div class="text-center justify-content-center">
                <a class="btn btn-primary mr-1" target="_blank" href="https://www.facebook.com">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a class="btn btn-danger" target="_blank" href="https://www.youtube.com">
                    <i class="fab fa-youtube"></i> Youtube
                </a>
            </div>
        </div>
    </nav>

    <div class="p-5 mb-4 bg-light rounded-3">
        <div class="container-fluid py-5">
            <h1 class="display-5 fw-bold">Custom jumbotron</h1>
            <p class="col-md-8 fs-4">
                Using a series of utilities, you can create this jumbotron, just
                like the one in previous versions of Bootstrap. Check out the
                examples below for how you can remix and restyle it to your liking.
            </p>
            <button class="btn btn-primary btn-lg" type="button">Example button</button>
        </div>
    </div>

    <footer>
        <div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 70px;">
            <b5>&copy; All Rights Reserved UPDS | <?= $year ?>
                <b5 />
        </div>
    </footer>

    <script src="<?= APP_URL ?>/public/js/jquery-3.3.1.slim.min.js"></script>
    <script src="<?= APP_URL ?>/public/js/popper.min.js"></script>
    <script src="<?= APP_URL ?>/public/js/bootstrap.min.js"></script>
    <script src="<?= APP_URL ?>/public/js/fontawesome.js"></script>
</body>

</html>
