<?php
include("../conexion.php");
$sql = $conexion->query("SELECT * from `usuario`");

// Función para eliminar un usuario
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $sql = "DELETE FROM usuario WHERE ID = $id";
    if ($conexion->query($sql) === TRUE) {
        $mensaje = "Usuario Eliminado Correctamente";
        header('location:index.php?mensaje=' . $mensaje);
    } else {
        $mensaje_error = "Error al Eliminar Usuario";
        header('location:index.php?mensaje_error=' . $mensaje);
    }
}
?>

<?php include("../../templates/header.php"); ?>

<?php if (isset($_GET['mensaje'])) { ?>
    <div class="alert alert-success alert-dismissible fade show text-center m-auto" style="max-width: 50%;" role="alert">
        <strong><?php echo $_GET['mensaje']; ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>
<?php if (isset($_GET['mensaje_error'])) { ?>
    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
        <strong><?php echo $_GET['mensaje']; ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>
<section class="container-fluid mt-4">
    <h2>Lista de Usuarios</h2>
    <div class="card">
        <div class="card-header">
            <a name="" id="" class="btn btn-outline-primary" href="<?php echo $url; ?>model/usuario/crear_usuario.php" role="button"><i class="fas fa-user-plus"></i> Agregar Usuario</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabla_id">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Nombres del usuario</th>
                            <th scope="col">Apellidos del usuario</th>
                            <th scope="col">User</th>
                            <th scope="col">Contraseña</th>
                            <th class="text-center" scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($datos = $sql->fetch_object()) { ?>
                            <tr>
                                <td><?php echo $datos->ID; ?></td>
                                <td><?php echo $datos->Nombres; ?></td>
                                <td><?php echo $datos->Apellidos; ?></td>
                                <td><?php echo $datos->Usuario; ?></td>
                                <td><?php echo $datos->Clave; ?></td>
                                <td class="d-flex justify-content-center">
                                    <a name="btneditar" id="btneditar" class="btn btn-outline-secondary mr-3" href="<?php echo $url; ?>model/usuario/editar_usuario.php?id=<?php echo $datos->ID; ?>" role="button"><i class="fas fa-user-edit"></i> Editar</a>
                                    <button type="button" class="btn btn-outline-danger" data-toggle="modal" data-target="#eliminarModal<?php echo $datos->ID; ?>"><i class="fas fa-user-minus"></i> Eliminar</button>
                                </td>
                            </tr>
                            <!-- Modal -->
                            <div class="modal fade" id="eliminarModal<?php echo $datos->ID; ?>" tabindex="-1" role="dialog" aria-labelledby="eliminarModalLabel<?php echo $datos->ID; ?>" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="eliminarModalLabel<?php echo $datos->ID; ?>">Eliminar Usuario</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>¿Estás seguro de que deseas eliminar este usuario?</h5>
                                            <p><strong>ID:</strong> <?php echo $datos->ID; ?></p>
                                            <p><strong>Nombres:</strong> <?php echo $datos->Nombres; ?></p>
                                            <p><strong>Apellidos:</strong> <?php echo $datos->Apellidos; ?></p>
                                            <p><strong>Usuario:</strong> <?php echo $datos->Usuario; ?></p>
                                            <p><strong>Contraseña:</strong> <?php echo $datos->Clave; ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                            <a href="<?php echo $url; ?>model/usuario/index.php?id=<?php echo $datos->ID; ?>" class="btn btn-danger">Eliminar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted"></div>
    </div>
</section>

<?php include("../../templates/footer.php"); ?>