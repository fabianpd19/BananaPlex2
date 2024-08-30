<?php
session_start();
require_once 'backend/config.php';

if (!isset($_SESSION['email'])) {
    header('Location: solicitudes.php');
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
                    <div class="collapse navbar-collapse">
                        <h3 class="my-3" id="titulo">Solicitudes</h3>
                    </div>
                </div>
            </nav>

            <div class="d-flex flex-column h-100">
                <!-- Begin page content -->
                <main class="flex-shrink-0">
                    <div class="container">
                        <a href="solicitud.php" class="btn btn-success">Agregar</a>

                        <!-- Mostrar mensajes de estado -->
                        <?php
                        if (isset($_GET['message']) && isset($_GET['type'])) {
                            $message = htmlspecialchars($_GET['message']);
                            $type = htmlspecialchars($_GET['type']); // "success" o "error"
                            echo '<div class="' . $type . '">' . $message . '</div>';
                        }
                        ?>

                        <div class="my-3">
                            <label for="filtroEstado" class="form-label">Filtrar por estado:</label>
                            <select id="filtroEstado" class="form-control" onchange="filtrarSolicitudes()">
                                <option value="">Todas</option>
                                <option value="pendiente">Pendientes</option>
                                <option value="aprobado">Aceptadas</option>
                                <option value="rechazado">Rechazadas</option>
                            </select>
                        </div>

                        <table class="table table-hover table-bordered my-3" aria-describedby="titulo">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Precio Ofrecido</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                require_once 'backend/config.php';
                                require_once 'backend/solicitud/mostrar_solicitudes.php';

                                $estado = isset($_GET['estado']) ? $_GET['estado'] : '';

                                try {
                                    $solicitudes = obtenerSolicitudes($pdo, $estado);
                                    foreach ($solicitudes as $solicitud) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($solicitud['cliente_nombre']) . "</td>";
                                        echo "<td>" . htmlspecialchars($solicitud['producto_nombre']) . "</td>";
                                        echo "<td>" . htmlspecialchars($solicitud['cantidad']) . "</td>";
                                        echo "<td>" . htmlspecialchars($solicitud['precio_ofrecido']) . "</td>";
                                        echo "<td>" . htmlspecialchars($solicitud['tipo']) . "</td>";
                                        echo '<td>';
                                        if ($solicitud['estado'] == 'pendiente') {
                                            echo '<a href="backend/solicitud/aceptar_solicitud.php?id=' . htmlspecialchars($solicitud['id']) . '" class="btn btn-success btn-sm me-2">Aceptar</a>';
                                            echo '<a href="backend/solicitud/rechazar_solicitud.php?id=' . htmlspecialchars($solicitud['id']) . '" class="btn btn-danger btn-sm">Rechazar</a>';
                                        }
                                        echo '</td>';
                                        echo "</tr>";
                                    }
                                } catch (PDOException $e) {
                                    die("Error en la consulta: " . $e->getMessage());
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </main>
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

        function filtrarSolicitudes() {
            const estado = document.getElementById('filtroEstado').value;
            window.location.href = `solicitudes.php?estado=${estado}`;
        }
    </script>
</body>

</html>