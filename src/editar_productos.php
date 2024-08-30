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
    header("Location: productos.php"); // Redirigir si no se proporciona ID
    exit();
}

$id = $_GET['id'];

// Obtener los datos del producto a editar
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
$stmt->execute(['id' => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    die("Producto no encontrado");
}

// Definir valores iniciales para evitar errores de índice no definido
$nombre = htmlspecialchars($producto['nombre']);
$descripcion = htmlspecialchars($producto['descripcion']);
$precio = htmlspecialchars($producto['precio']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Banana Plex - Editar Producto</title>
    <!-- Bootstrap CSS CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <!-- Our Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <!-- Scrollbar Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
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
                <li> <a href="productos.php">Productos</a> </li>
                <li> <a href="productos.php">Productos</a></li>
            </ul>
        </nav>

        <!-- Page Content  -->
        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-info">
                        <i class="fas fa-align-left"></i>
                    </button>
                    <h3 class="my-3">Editar Producto</h3>
                </div>
            </nav>

            <main class="flex-shrink-0">
                <div class="container">
                    <form action="backend/productos/actualizar_productos.php" method="post" autocomplete="off">
                        <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">

                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion"><?php echo $descripcion; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="precio">Precio</label>
                            <input type="text" class="form-control" id="precio" name="precio" value="<?php echo $precio; ?>" required pattern="[0-9]+(\.[0-9]+)?" title="Ingrese un número válido (puede incluir decimales)">
                        </div>

                        <div class="form-group">
                            <a href="productos.php" class="btn btn-secondary">Regresar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </main>
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
            });
        });
    </script>
</body>

</html>