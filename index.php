<?php
session_start();
$year = date('Y');
$nombre = isset($_SESSION['Nombre']) ? $_SESSION['Nombre'] : '';
$esAdmin = isset($_SESSION['EsAdmin']) ? $_SESSION['EsAdmin'] : '';
$url = "http://localhost/login/";

if (empty($_SESSION["ID"])) {
	header("location:" . $url . "login.php");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
	<meta charset="UTF-8">
	<title>Pagina Principal</title>
	<link rel="stylesheet" href="css/estilo.css">
	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/fontawesome.min.css">
	<link href="img/boton-de-inicio.png" rel="shortcut icon">
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
					<img src="img/imagen1.jpg" class="d-block w-100 h-100 img-fluid" alt="...">
					<div class="carousel-caption d-none d-md-block">
						<h5 class="display-4 mb-4 font-weight-bold">BOOTSTRAP</h5>
						<p>Nulla vitae elit libero, a pharetra augue mollis interdum.</p>
					</div>
				</div>
				<div class="carousel-item" style="height: 100vh">
					<img src="img/imagen2.jpg" class="d-block w-100 h-100 img-fluid" alt="...">
					<div class="carousel-caption d-none d-md-block">
						<h5 class="display-4 mb-4 font-weight-bold">BOOTSTRAP</h5>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
					</div>
				</div>
				<div class="carousel-item" style="height: 100vh">
					<img src="img/imagen3.jpg" class="d-block w-100 h-100 img-fluid" alt="...">
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

	<nav class="navbar navbar-dark bg-dark  navbar-expand-lg fixed-top">
		<div class="text-white p-2">
			Bienvenido:
			<?php
			echo $nombre;
			?>
		</div>
		<div class="collapse navbar-collapse" id="navbarTogglerDemo01">
			<div class="navbar-nav mr-auto">
				<div class="offset-md-1 mr-auto text-center"></div>
				<a class="nav-item nav-link text-justify active ml-3 hover-primary" href="<?php echo $url ?>">Inicio</a>
				<a class="nav-item nav-link text-justify ml-3 hover-primary" href="<?php echo $url ?>">Nosotros</a>
				<?php if ($esAdmin == true) { ?>
					<a class="nav-item nav-link text-nowrap ml-3 hover-primary" href="<?php echo $url ?>model/usuario/">Usuarios</a>
				<?php } ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle text-justify ml-3" href="" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Servicios
					</a>
					<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
						<a class="dropdown-item" href="#">Preguntas Frecuentes</a>
						<a class="dropdown-item" href="#">Compras</a>
						<a class="dropdown-item" href="#">Otros</a>
					</div>
				</li>
				<a class="nav-item nav-link text-justify ml-3 hover-primary" href="<?php echo $url; ?>controlador/controlador_cerrar_session.php">Salir</a>
			</div>
			<div class="text-center justify-content-center">
				<a class="btn btn-primary mr-1" target="_blank" href="https://www.facebook.com"><i class="fab fa-facebook"></i> Facebook</a>
				<a class="btn btn-danger" target="_blank" href="https://www.youtube.com"><i class="fab fa-youtube"></i> Youtube</a>
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
			<button class="btn btn-primary btn-lg" type="button">
				Example button
			</button>
		</div>
	</div>

	<footer>
		<div class="d-flex align-items-center justify-content-center bg-dark text-white" style="height: 70px;">
			<b5>&copy; Derechos Reservados UPDS | <?php echo $year ?>
				<b5 />
		</div>
	</footer>

	<script src="js/jquery-3.3.1.slim.min.js"></script>
	<script src="js/popper.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/fontawesome.js"></script>
</body>

</html>