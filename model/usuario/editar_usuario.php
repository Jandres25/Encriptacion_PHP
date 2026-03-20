<?php
include('../conexion.php');

if (isset($_GET["id"])) {
    $id = (int)$_GET["id"];
    $stmt = $conexion->prepare("SELECT * FROM usuario WHERE ID = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    } else {
        echo "No se encontró el usuario.";
        exit;
    }
    $stmt->close();
} else {
    echo "ID de usuario no especificado.";
    exit;
}

if (isset($_POST["editar_usuario"])) {
    $nombres   = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $correo    = $_POST["correo"] ?? '';
    $usuarioNombre = $_POST["usuario"];
    $esAdmin   = isset($_POST["esAdmin"]) ? 1 : 0;

    if (!empty($_POST['password'])) {
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    } else {
        $passwordHash = $usuario['Clave'];
    }

    $stmt = $conexion->prepare(
        "UPDATE usuario SET Nombres=?, Apellidos=?, correo=?, Usuario=?, Clave=?, EsAdmin=? WHERE ID=?"
    );
    $stmt->bind_param("sssssii", $nombres, $apellidos, $correo, $usuarioNombre, $passwordHash, $esAdmin, $id);
    $stmt->execute();

    if ($stmt->affected_rows >= 0) {
        $stmt->close();
        header('location:index.php?mensaje=Usuario Actualizado Correctamente');
        exit;
    } else {
        $stmt->close();
        header('location:index.php?mensaje_error=Error al Actualizar el Usuario');
        exit;
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
            <form action="" method="post">
                <div class="mb-3">
                    <label for="nombres" class="form-label">Nombres del usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="nombres" id="nombres" value="<?= htmlspecialchars($usuario['Nombres']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="apellidos" class="form-label">Apellidos del usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="apellidos" id="apellidos" value="<?= htmlspecialchars($usuario['Apellidos']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="correo" class="form-label">Correo electrónico</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" name="correo" id="correo" value="<?= htmlspecialchars($usuario['correo']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="usuario" id="usuario" value="<?= htmlspecialchars($usuario['Usuario']) ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" id="password" placeholder="Dejar vacío para no cambiar">
                    </div>
                    <small class="form-text text-muted">Dejar vacío para mantener la contraseña actual</small>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" name="esAdmin" id="esAdmin" value="1" <?= $usuario['EsAdmin'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="esAdmin">Es Administrador</label>
                </div>
                <button type="submit" name="editar_usuario" class="btn btn-outline-success mr-1"><i class="fas fa-pen"></i> Guardar Cambios</button>
                <a class="btn btn-outline-primary" href="<?= $url ?>model/usuario/" role="button"><i class="fas fa-undo"></i> Cancelar</a>
            </form>
        </div>
        <div class="card-footer text-muted"></div>
    </div>
</section>

<?php include("../../templates/footer.php"); ?>
