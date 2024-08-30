<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../productos.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);
// Configurar la zona horaria adecuada para Ecuador
date_default_timezone_set('America/Guayaquil');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // Validar precio y stock según tus requerimientos específicos
    if (!is_numeric($precio)) {
        die("Error: El precio deben ser números.");
    }

    $fecha_registro = date('Y-m-d H:i:s');

    $sql = "INSERT INTO productos (nombre, descripcion, precio) 
            VALUES (:nombre, :descripcion, :precio)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':descripcion' => $descripcion,
            ':precio' => $precio,
        ]);
        header("Location: ../../productos.php"); // Redirige a la página principal después de crear el producto
        exit();
    } catch (PDOException $e) {
        die("Error al crear producto: " . $e->getMessage());
    }
}
