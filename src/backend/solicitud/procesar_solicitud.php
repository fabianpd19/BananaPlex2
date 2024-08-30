<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../solicitudes.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);
// Verificar que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Incluir archivo de configuración de la base de datos
    require_once '../config.php';

    // Obtener los datos del formulario
    $cliente_id = $_POST['cliente_id'];
    $producto_id = $_POST['producto_id'];
    $cantidad = $_POST['cantidad'];
    $precio_ofrecido = $_POST['precio_ofrecido'];
    $tipo = $_POST['tipo'];
    $empleado_id = $_POST['empleado_id'];

    try {
        // Preparar la consulta SQL para insertar la solicitud
        $query = "INSERT INTO solicitudes (cliente_id, producto_id, cantidad, precio_ofrecido, estado, tipo, empleado_id)
                  VALUES (:cliente_id, :producto_id, :cantidad, :precio_ofrecido, 'pendiente', :tipo, :empleado_id)";
        $stmt = $pdo->prepare($query);

        // Bind de parámetros
        $stmt->bindParam(':cliente_id', $cliente_id);
        $stmt->bindParam(':producto_id', $producto_id);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':precio_ofrecido', $precio_ofrecido);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':empleado_id', $empleado_id);

        // Ejecutar consulta
        if ($stmt->execute()) {
            echo "Solicitud enviada correctamente.";
        } else {
            http_response_code(500);
            echo "Error al enviar la solicitud.";
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Error al procesar la solicitud: " . $e->getMessage();
    }
} else {
    http_response_code(400);
    echo "Método de solicitud incorrecto.";
}
