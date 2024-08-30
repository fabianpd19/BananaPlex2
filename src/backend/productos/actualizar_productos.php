<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../productos.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    try {
        $sql = "UPDATE productos SET nombre = :nombre, descripcion = :descripcion, precio = :precio WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio' => $precio,
        ]);
        header("Location: ../../productos.php"); // Redirige a la página principal después de actualizar
    } catch (PDOException $e) {
        die("Error al actualizar producto: " . $e->getMessage());
    }
}
