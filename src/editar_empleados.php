<?php
session_start();
require_once 'backend/config.php';

if (!isset($_SESSION['email'])) {
    header('Location: login.html');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);

// Verificar si se ha pasado el parámetro ID en la URL
if (!isset($_GET['id'])) {
    header("Location: empleados.php"); // Redirigir si no se proporciona ID
    exit();
}

$id = $_GET['id'];

// Obtener los datos del empleado a editar
$stmt = $pdo->prepare("SELECT * FROM empleados WHERE id = :id");
$stmt->execute(['id' => $id]);
$empleado = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$empleado) {
    die("Empleado no encontrado");
}

// Función para obtener todas las provincias
function obtenerProvincias($pdo)
{
    $query = "SELECT * FROM provincias";
    $stmt = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$provincias = obtenerProvincias($pdo);

// Definir valores iniciales para evitar errores de índice no definido
$nombre1 = htmlspecialchars($empleado['nombre1']);
$nombre2 = htmlspecialchars($empleado['nombre2']);
$apellido1 = htmlspecialchars($empleado['apellido1']);
$apellido2 = htmlspecialchars($empleado['apellido2']);
$direccion = htmlspecialchars($empleado['direccion']);
$cedula = htmlspecialchars($empleado['cedula']);
$fecha_registro = htmlspecialchars($empleado['fecha_registro']);
$provincia_id = $empleado['provincia_id'];
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
                        <h3 class="my-3" id="titulo"> Editar Empleado</h3>
                    </div>
                </div>
            </nav>

            <div class="d-flex flex-column h-100">

                <!-- Begin page content -->
                <main class="flex-shrink-0">
                    <div class="container">

                        <form action="backend/empleados/actualizar_empleados.php" method="post" autocomplete="off">
                            <input type="hidden" name="id" value="<?php echo $empleado['id']; ?>">

                            <div class="col-md-6">
                                <label for="nombre1" class="form-label">Primer Nombre</label>
                                <input type="text" class="form-control" id="nombre1" name="nombre1" pattern="^[^\d]*$" title="El nombre no debe contener números" value="<?php echo $nombre1; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="nombre2" class="form-label">Segundo Nombre</label>
                                <input type="text" class="form-control" id="nombre2" name="nombre2" pattern="^[^\d]*$" title="El nombre no debe contener números" value="<?php echo $nombre2; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="apellido1" class="form-label">Primer Apellido</label>
                                <input type="text" class="form-control" id="apellido1" name="apellido1" pattern="^[^\d]*$" title="El apellido no debe contener números" value="<?php echo $apellido1; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="apellido2" class="form-label">Segundo Apellido</label>
                                <input type="text" class="form-control" id="apellido2" name="apellido2" pattern="^[^\d]*$" title="El apellido no debe contener números" value="<?php echo $apellido2; ?>" required>
                            </div>

                            <div class="col-md-12">
                                <label for="direccion" class="form-label">Cedula</label>
                                <input type="text" class="form-control" id="cedula" name="cedula" pattern="\d{10}" title="Debe contener exactamente 10 dígitos numéricos" value="<?php echo $cedula; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $direccion; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_registro" class="form-label">Fecha de Registro</label>
                                <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" value="<?php echo $fecha_registro; ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="provincia" class="form-label">Provincia</label>
                                <select class="form-select" id="provincia" name="provincia" required>
                                    <option value="">Seleccionar</option>
                                    <?php foreach ($provincias as $provincia) : ?>
                                        <option value="<?php echo $provincia['id']; ?>" <?php if ($provincia['id'] == $provincia_id) echo 'selected'; ?>><?php echo htmlspecialchars($provincia['nombre']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <a href="empleados.php" class="btn btn-secondary">Regresar</a>
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                            </div>

                        </form>
                    </div>
                </main>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
            </div>
        </div>
    </div>

    <!-- jQuery CDN - Slim version (=without AJAX) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <!-- Popper.JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js" integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm" crossorigin="anonymous"></script>
    <!-- jQuery Custom Scroller CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#sidebar").mCustomScrollbar({
                theme: "minimal"
            });

            $('#sidebarCollapse').on('click', function() {
                $('#sidebar, #content').toggleClass('active');
                $('.collapse.in').toggleClass('in');
                $('a[aria-expanded=true]').attr('aria-expanded', 'false');
            });
        });
    </script>

    <script>
        const eliminaModal = document.getElementById('eliminaModal')
        if (eliminaModal) {
            eliminaModal.addEventListener('show.bs.modal', event => {
                // Button that triggered the modal
                const button = event.relatedTarget
                // Extract info from data-bs-* attributes
                const id = button.getAttribute('data-bs-id')

                // Update the modal's content.
                const form = eliminaModal.querySelector('#form-elimina')
                form.setAttribute('action', 'eliminar_empleado.php?id=' + id)
            })
        }
    </script>
</body>

</html>