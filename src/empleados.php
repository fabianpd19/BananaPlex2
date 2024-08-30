<?php
session_start();
require_once 'backend/config.php';

if (!isset($_SESSION['email'])) {
    header('Location: empresas.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>Banana Plex</title>

    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    <link rel="icon" type="image/x-icon" href="assets/favicon.png">

</head>

<body>

    <div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Banana Plex</h3>
            </div>

            <ul class="list-unstyled components">
                <li> <a href="index.php">Principal</a> </li>
                <li> <a href="empleados.php">Empleados</a> </li>
                <li> <a href="clientes.php">Clientes</a> </li>
                <li> <a href="solicitudes.php">Compra/Venta</a> </li>
                <li> <a href="empresas.php">Empresas</a> </li>
                <li> <a href="productos.php">Productos</a></li>
            </ul>
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                        <span><i class="fa-solid fa-bars"></i></span>
                    </button>
                    <button class="btn btn-dark d-inline-block d-lg-none ml-auto" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="fas fa-align-justify"></i>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <h3 class="my-3" id="titulo">Empleados</h3>
                    </div>
                </div>
            </nav>

            <div class="d-flex flex-column h-100">
                <!-- Begin page content -->
                <main class="flex-shrink-0">
                    <div class="container">
                        <a href="nuevo_empleado.php" class="btn btn-success">Agregar</a>
                        <table class="table table-hover table-bordered my-3" aria-describedby="titulo">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Usuario ID</th>
                                    <th scope="col">Primer Nombre</th>
                                    <th scope="col">Segundo Nombre</th>
                                    <th scope="col">Primer Apellido</th>
                                    <th scope="col">Segundo Apellido</th>
                                    <th scope="col">Dirección</th>
                                    <th scope="col">Fecha de Registro</th>
                                    <th scope="col">Provincia</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Incluir el archivo de configuración y las consultas
                                require_once 'backend/config.php';
                                require_once 'backend/empleados/mostrar_empleados.php';

                                // Obtener los empleados usando la función definida en mostrar_empleado.php
                                $empleados = obtenerEmpleados($pdo);

                                // Mostrar los empleados en la tabla
                                foreach ($empleados as $empleado) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($empleado['usuario_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($empleado['nombre1']) . "</td>";
                                    echo "<td>" . htmlspecialchars($empleado['nombre2']) . "</td>";
                                    echo "<td>" . htmlspecialchars($empleado['apellido1']) . "</td>";
                                    echo "<td>" . htmlspecialchars($empleado['apellido2']) . "</td>";
                                    echo "<td>" . htmlspecialchars($empleado['direccion']) . "</td>";
                                    echo "<td>" . htmlspecialchars($empleado['fecha_registro']) . "</td>";
                                    echo "<td>" . htmlspecialchars($empleado['nombre_provincia']) . "</td>";
                                    echo '<td> <a href="editar_empleados.php?id=' . htmlspecialchars($empleado['id']) . '" class="btn btn-warning btn-sm me-2">Editar</a>';
                                    echo '<button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#eliminaModal" data-bs-id="' . htmlspecialchars($empleado['id']) . '">Eliminar</button> </td>';
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </main>

                <!-- Modal de confirmación de eliminación -->
                <div class="modal fade" id="eliminaModal" tabindex="-1" aria-labelledby="eliminaModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="eliminaModalLabel">Aviso</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>¿Desea eliminar este registro?</p>
                            </div>
                            <div class="modal-footer">
                                <form id="form-elimina" action="backend/empleados/eliminar_empleados.php" method="post">
                                    <input type="hidden" name="id" id="id-eliminar">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-danger">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de error -->
                <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="modalMessage">
                                <!-- Mensaje de error -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
            </div>
        </div>
    </div>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UOa5w8yVt4k6aQ0PEKuM7oPUuF3b2FQvvS8Wg14zzC7b05ORtI9JfQ3g9sA+7JvX" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-ChfqqG3gJ6K2Vb9D15iA5DjtE3Fj+7AA3zZJb3M4xB02s44gO1VVdp6zJ5z0zBz" crossorigin="anonymous"></script>
    <!-- Custom Scrollbar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <!-- Custom Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var eliminaModal = document.getElementById('eliminaModal');
            eliminaModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-bs-id');
                var inputIdEliminar = eliminaModal.querySelector('#id-eliminar');
                inputIdEliminar.value = id;
            });

            <?php if (isset($_SESSION['error_message'])) : ?>
                var errorMessage = "<?php echo $_SESSION['error_message'];
                                    unset($_SESSION['error_message']); ?>";
                var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {});
                document.getElementById('modalMessage').textContent = errorMessage;
                errorModal.show();
            <?php endif; ?>
        });
    </script>
</body>

</html>