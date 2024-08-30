<?php
session_start();
require_once 'backend/config.php';

if (!isset($_SESSION['email'])) {
    header('Location: login.html');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Banana Plex - Productos</title>
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
                <li><a href="index.php">Principal</a></li>
                <li><a href="empleados.php">Empleados</a></li>
                <li><a href="clientes.php">Clientes</a></li>
                <li><a href="solicitudes.php">Compra/Venta</a></li>
                <li><a href="empresas.php">Empresas</a></li>
                <li><a href="productos.php">Productos</a></li>
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
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <h3 class="my-3" id="titulo">Productos</h3>
                    </div>
                </div>
            </nav>

            <div class="d-flex flex-column h-100">
                <!-- Begin page content -->
                <main class="flex-shrink-0">
                    <div class="container">
                        <h3 class="my-3">Nuevo Producto</h3>
                        <form action="backend/productos/crear_productos.php" class="row g-3" method="post" autocomplete="off" onsubmit="return validateForm();">
                            <div class="col-md-12">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="col-md-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                            </div>

                            <div class="col-md-6">
                                <label for="precio" class="form-label">Precio</label>
                                <input type="text" class="form-control" id="precio" name="precio" required pattern="[0-9]+(\.[0-9]+)?" title="Ingrese un número válido (puede incluir decimales)">
                            </div>

                            <div class="col-12">
                                <a href="productos.php" class="btn btn-secondary">Regresar</a>
                                <button type="submit" class="btn btn-primary">Guardar</button>
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
        const eliminaModal = document.getElementById('eliminaModal');
        if (eliminaModal) {
            eliminaModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-bs-id');
                const form = eliminaModal.querySelector('#form-elimina');
                form.setAttribute('action', 'elimina_productos?id=' + id); // Asegúrate de definir la URL correctamente
            });
        }
    </script>

    <script>
        function validateForm() {
            var nombre = document.getElementById("nombre").value;
            var descripcion = document.getElementById("descripcion").value;
            var precio = document.getElementById("precio").value;
            var stock = document.getElementById("stock").value;

            if (!nombre || !descripcion || !precio || !stock) {
                alert("Por favor, complete todos los campos.");
                return false;
            }

            // Validaciones adicionales según tus necesidades

            return true;
        }
    </script>
</body>

</html>