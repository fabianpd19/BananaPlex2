<?php
// Incluir archivo de configuración de la base de datos
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../clientes.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);
// Verificar si se proporcionó la cédula del cliente
if (isset($_GET['cedula'])) {
    $cedula = $_GET['cedula'];

    // Consultar la base de datos para obtener la información del cliente
    $query = "SELECT id, nombre1, nombre2, apellido1, apellido2, direccion, telefono FROM clientes WHERE cedula = :cedula";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':cedula', $cedula);
    $stmt->execute();
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente) {
        // Cliente encontrado, devolver datos como JSON
        $response = [
            'encontrado' => true,
            'id' => $cliente['id'],
            'nombre1' => $cliente['nombre1'],
            'nombre2' => $cliente['nombre2'],
            'apellido1' => $cliente['apellido1'],
            'apellido2' => $cliente['apellido2'],
            'direccion' => $cliente['direccion'],
            'telefono' => $cliente['telefono']
        ];
    } else {
        // Cliente no encontrado
        $response = ['encontrado' => false];
    }

    // Devolver respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Si no se proporcionó la cédula, devolver un error o manejar según sea necesario
    $response = ['error' => 'Cédula no proporcionada'];
    header('Content-Type: application/json');
    http_response_code(400); // Bad Request
    echo json_encode($response);
}
