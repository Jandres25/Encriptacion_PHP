<?php
include('../conexion.php');

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    // Obtener los datos del usuario a editar
    $sql = "SELECT * FROM usuario WHERE ID = $id";
    $result = $conexion->query($sql);
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    } else {
        echo "No se encontró el usuario.";
        exit;
    }
} else {
    echo "ID de usuario no especificado.";
    exit;
}

if (isset($_POST["editar_usuario"])) {
    // Obtener los datos del formulario
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $usuario = $_POST["usuario"];
    $password = $_POST["password"];
    // Crear un hash de contraseña utilizando BCRYPT
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Actualizar los datos del usuario en la base de datos
    $sql = "UPDATE usuario SET Nombres='$nombres', Apellidos='$apellidos', Usuario='$usuario', Clave='$passwordHash' WHERE ID=$id";
    if ($conexion->query($sql) === TRUE) {
        $mensaje = "Usuario Actualizado Correctamente";
        header('location:index.php?mensaje=' . $mensaje);
    } else {
        $mensaje_error = "Error al Actualizar el Usuario";
        header('location:index.php?mensaje_error=' . $mensaje_error);
    }
}
?>

<?php include("../../templates/header.php"); ?>
<section class="container mt-50">
    <h2>Editar Usuario</h2>
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
                        <input type="text" class="form-control" name="nombres" id="nombres" aria-describedby="helpId" value="<?php echo $usuario["Nombres"]; ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label label for="apellidos" class="form-label">Apellidos del usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="apellidos" id="apellidos" aria-describedby="helpId" value="<?php echo $usuario["Apellidos"]; ?>">
                    </div>
                </div>
                <label label for="usuario" class="form-label">Usuario</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control" name="usuario" id="usuario" aria-describedby="helpId" value="<?php echo $usuario["Usuario"]; ?>">
                </div>
                <div class="mb-3 mt-3">
                    <label for="password" class="form-label">Password:</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" id="password" aria-describedby="helpId" value="<?php echo $usuario['Clave']; ?>">
                    </div>
                </div>
                <button type="submit" name="editar_usuario" class="btn btn-outline-success mr-1"><i class="fas fa-pen"></i> Guardar Cambios</button>
                <a name="" id="" class="btn btn-outline-primary" href="<?php echo $url; ?>model/usuario/" role="button"><i class="fas fa-undo"></i> Cancelar</a>
            </form>
        </div>
        <div class="card-footer text-muted"></div>
    </div>
</section>

<?php include("../../templates/footer.php"); ?>