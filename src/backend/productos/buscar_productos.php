<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../productos.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);
// Consultar la base de datos para obtener la lista de productos
$query = "SELECT id, nombre FROM productos";
$stmt = $pdo->query($query);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar si se encontraron productos
if ($productos) {
    // Devolver lista de productos como JSON
    header('Content-Type: application/json');
    echo json_encode($productos);
} else {
    // Si no se encontraron productos, devolver un mensaje o manejar según sea necesario
    $response = ['error' => 'No se encontraron productos'];
    header('Content-Type: application/json');
    http_response_code(404); // Not Found
    echo json_encode($response);
}
