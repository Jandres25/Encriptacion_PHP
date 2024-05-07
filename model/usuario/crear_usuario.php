<?php
include('../conexion.php');

if (isset($_POST["agregar_usuario"])) {
    // Obtener los datos del formulario
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];

    // Crear un hash de contraseña utilizando BCRYPT
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar los datos del usuario en la base de datos
    $sql = "INSERT INTO usuario (Nombres, Apellidos, Usuario, Clave) VALUES ('$nombres', '$apellidos', '$usuario', '$passwordHash')";
    if ($conexion->query($sql) === TRUE) {
        $mensaje = "Usuario Agregado Correctamente";
        header('location:index.php?mensaje=' . $mensaje);
    } else {
        $mensaje_error = "Error al Crear el Usuario";
        header('location:index.php?mensaje_error=' . $mensaje_error);
    }
}
?>

<?php include("../../templates/header.php"); ?>
<section class="container m-auto">
    <h2>Crear Usuario</h2>
    <div class="card">
        <div class="card-header">
            Datos del usuario
        </div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label label for="nombres" class="form-label">Nombres del usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="nombres" id="nombres" aria-describedby="helpId" placeholder="Ejemplo: Juan Pablo" required>
                    </div>
                    <small id="helpId" class="form-text text-muted">Ingrese los nombres</small>
                </div>
                <div class="mb-3">
                    <label label for="apellidos" class="form-label">Apellidos del usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="apellidos" id="apellidos" aria-describedby="helpId" placeholder="Ejemplo: Perez" required>
                    </div>
                    <small id="helpId" class="form-text text-muted">Ingrese los apellidos</small>
                </div>
                <label label for="usuario" class="form-label">Usuario</label>
                <div class="mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="usuario" id="usuario" aria-describedby="helpId" placeholder="Ejemplo: Juan10" required>
                    </div>
                    <small id="helpId" class="form-text text-muted">Ingrese el usuario</small>
                </div>
                <label for="password" class="form-label">Password</label>
                <div class="mb-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" id="password" aria-describedby="helpId" placeholder="Ejemplo: password" required>
                    </div>
                    <small id="helpId" class="form-text text-muted">Ingrese la contraseña</small>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" name="agregar_usuario" class="btn btn-outline-success"><i class="fas fa-user-plus"></i> Agregar</button>
                    <a class="btn btn-outline-primary" href="<?php echo $url; ?>model/usuario/" role="button"><i class="fas fa-undo"></i> Cancelar</a>
                </div>
        </div>
        <div class="card-footer text-muted"></div>
    </div>
</section>

<?php include("../../templates/footer.php"); ?>