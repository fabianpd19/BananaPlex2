<?php
session_start();
require_once 'backend/config.php';

if (!isset($_SESSION['email'])) {
    header('Location: solicitud.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible"="IE=edge">
    <title>Banana Plex - Compra/Venta</title>

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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

    <div class="wrapper">
        <!-- Sidebar  -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3>Banana Plex</h3>
            </div>

            <ul class="list-unstyled components">
                <!-- <li class="active">
                    <a href="#homeSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Home</a>
                    <ul class="collapse list-unstyled" id="homeSubmenu">
                        <li>
                            <a href="#">Home 1</a>
                        </li>
                        <li>
                            <a href="#">Home 2</a>
                        </li>
                        <li>
                            <a href="#">Home 3</a>
                        </li>
                    </ul>
                </li> -->
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
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <h3 class="my-3" id="titulo">Solicitudes</h3>
                    </div>
                </div>
            </nav>

            <div class="d-flex flex-column h-100">

                <!-- Begin page content -->
                <main class="flex-shrink-0">
                    <div class="container">
                        <h3 class="my-3">Formulario de Solicitud</h3>

                        <form id="formularioSolicitud" class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label for="cedula" class="form-label">Cédula del Cliente:</label>
                                <input type="text" id="cedula" name="cedula" class="form-control" required>
                                <button type="button" id="buscarCliente" class="btn btn-primary mt-2">Buscar</button>
                            </div>

                            <div class="col-md-12">
                                <label for="nombreCliente" class="form-label">Nombre del Cliente:</label>
                                <input type="text" id="nombreCliente" name="nombreCliente" class="form-control" readonly>
                            </div>

                            <div class="col-md-12">
                                <label for="direccionCliente" class="form-label">Dirección del Cliente:</label>
                                <input type="text" id="direccionCliente" name="direccionCliente" class="form-control" readonly>
                            </div>

                            <div class="col-md-12">
                                <label for="telefonoCliente" class="form-label">Teléfono del Cliente:</label>
                                <input type="text" id="telefonoCliente" name="telefonoCliente" class="form-control" readonly>
                            </div>

                            <div class="col-md-12">
                                <label for="producto" class="form-label">Producto:</label>
                                <select id="producto" name="producto_id" class="form-select">
                                    <!-- Opciones de productos se generarán dinámicamente -->
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="cantidad" class="form-label">Cantidad:</label>
                                <input type="number" id="cantidad" name="cantidad" class="form-control" min="1" required>
                            </div>

                            <div class="col-md-6">
                                <label for="precio_ofrecido" class="form-label">Precio ofrecido:</label>
                                <input type="number" id="precio_ofrecido" name="precio_ofrecido" class="form-control" step="0.01" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Tipo:</label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="compra" name="tipo" value="compra" required>
                                    <label class="form-check-label" for="compra">Compra</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" id="venta" name="tipo" value="venta" required>
                                    <label class="form-check-label" for="venta">Venta</label>
                                </div>
                            </div>

                            <!-- Campos ocultos para almacenar el ID del cliente y del empleado -->
                            <input type="hidden" id="cliente_id" name="cliente_id">
                            <input type="hidden" id="empleado_id" name="empleado_id" value="1">

                            <div class="col-12">
                                <a href="solicitudes.php" class="btn btn-secondary">Regresar</a>
                                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                            </div>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- jQuery CDN - Slim version (=without AJAX) -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
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

            cargarProductos();

            $('#buscarCliente').click(function() {
                buscarCliente();
            });

            $('#formularioSolicitud').submit(function(e) {
                e.preventDefault();
                enviarSolicitud();
            });
        });

        function cargarProductos() {
            $.ajax({
                url: 'backend/productos/buscar_productos.php',
                type: 'GET',
                dataType: 'json',
                success: function(productos) {
                    if (productos.length > 0) {
                        var options = productos.map(function(producto) {
                            return '<option value="' + producto.id + '">' + producto.nombre + '</option>';
                        }).join('');
                        $('#producto').html(options);
                    } else {
                        alert('No se encontraron productos.');
                    }
                },
                error: function() {
                    alert('Error al cargar los productos.');
                }
            });
        }

        function buscarCliente() {
            var cedula = $('#cedula').val().trim();
            if (cedula === '') {
                alert('Por favor ingrese la cédula del cliente.');
                return;
            }

            $.ajax({
                url: 'backend/cliente/buscar_cliente.php',
                type: 'GET',
                data: {
                    cedula: cedula
                },
                dataType: 'json',
                success: function(response) {
                    if (response.encontrado) {
                        $('#cliente_id').val(response.id);
                        $('#nombreCliente').val(response.nombre1 + ' ' + response.nombre2);
                        $('#direccionCliente').val(response.direccion);
                        $('#telefonoCliente').val(response.telefono);
                    } else {
                        alert('Cliente no encontrado.');
                    }
                },
                error: function() {
                    alert('Error al buscar el cliente.');
                }
            });
        }

        function enviarSolicitud() {
            var formData = $('#formularioSolicitud').serialize();

            $.ajax({
                url: 'backend/solicitud/procesar_solicitud.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert(response);
                    $('#formularioSolicitud')[0].reset();
                },
                error: function(xhr, status, error) {
                    alert('Error al enviar la solicitud: ' + xhr.responseText);
                }
            });
        }
    </script>
</body>

</html>