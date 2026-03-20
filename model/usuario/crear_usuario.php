<?php
include('../conexion.php');

if (isset($_POST["agregar_usuario"])) {
    $nombres  = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $correo   = $_POST["correo"] ?? '';
    $usuario  = $_POST["usuario"];
    $password = $_POST["password"];
    $esAdmin  = isset($_POST["esAdmin"]) ? 1 : 0;

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conexion->prepare(
        "INSERT INTO usuario (Nombres, Apellidos, correo, Usuario, Clave, EsAdmin) VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssssi", $nombres, $apellidos, $correo, $usuario, $passwordHash, $esAdmin);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $stmt->close();
        header('location:index.php?mensaje=Usuario Agregado Correctamente');
        exit;
    } else {
        $stmt->close();
        header('location:index.php?mensaje_error=Error al Crear el Usuario');
        exit;
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
            <form action="" method="post">
                <div class="mb-3">
                    <label for="nombres" class="form-label">Nombres del usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="nombres" id="nombres" placeholder="Ejemplo: Juan Pablo" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="apellidos" class="form-label">Apellidos del usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="apellidos" id="apellidos" placeholder="Ejemplo: Perez" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" name="correo" id="correo" placeholder="Ejemplo: juan@gmail.com">
                    </div>
                    <small class="form-text text-muted">Requerido para recuperación de contraseña</small>
                </div>
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Ejemplo: Juan10" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Contraseña" required>
                    </div>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="esAdmin" id="esAdmin" value="1">
                    <label class="form-check-label" for="esAdmin">Es Administrador</label>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" name="agregar_usuario" class="btn btn-outline-success"><i class="fas fa-user-plus"></i> Agregar</button>
                    <a class="btn btn-outline-primary" href="<?= $url ?>model/usuario/" role="button"><i class="fas fa-undo"></i> Cancelar</a>
                </div>
            </form>
        </div>
        <div class="card-footer text-muted"></div>
    </div>
</section>

<?php include("../../templates/footer.php"); ?>
