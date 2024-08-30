<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Solicitud</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div id="content" class="container">
        <h2 class="my-4">Formulario de Solicitud</h2>

        <form id="formularioSolicitud" class="mb-4">
            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula del Cliente:</label>
                <input type="text" id="cedula" name="cedula" class="form-control" required>
                <button type="button" id="buscarCliente" class="btn btn-primary mt-2">Buscar</button>
            </div>

            <div class="mb-3">
                <label for="nombreCliente" class="form-label">Nombre del Cliente:</label>
                <input type="text" id="nombreCliente" name="nombreCliente" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="direccionCliente" class="form-label">Dirección del Cliente:</label>
                <input type="text" id="direccionCliente" name="direccionCliente" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="telefonoCliente" class="form-label">Teléfono del Cliente:</label>
                <input type="text" id="telefonoCliente" name="telefonoCliente" class="form-control" readonly>
            </div>

            <div class="mb-3">
                <label for="producto" class="form-label">Producto:</label>
                <select id="producto" name="producto" class="form-select">
                    <!-- Opciones de productos se generarán dinámicamente -->
                </select>
            </div>

            <div class="mb-3">
                <label for="cantidad" class="form-label">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" class="form-control" min="1" required>
            </div>

            <div class="mb-3">
                <label for="precio_ofrecido" class="form-label">Precio ofrecido:</label>
                <input type="number" id="precio_ofrecido" name="precio_ofrecido" class="form-control" step="0.01" required>
            </div>

            <div class="mb-3">
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

            <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
        </form>
    </div>

    <!-- jQuery y Bootstrap JS al final del cuerpo para mejor rendimiento -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            cargarProductos();

            $('#buscarCliente').click(function() {
                buscarCliente();
            });

            $('#formularioSolicitud').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'backend/solicitud/procesar_solicitud.php',
                    type: 'POST',
                    data: $('#formularioSolicitud').serialize(),
                    dataType: 'text',
                    success: function(response) {
                        alert(response);
                        $('#formularioSolicitud')[0].reset(); // Limpiar formulario
                        // Puedes agregar aquí redireccionamiento o acciones adicionales
                    },
                    error: function() {
                        alert('Error al enviar la solicitud.');
                    }
                });
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
    </script>
</body>

</html>