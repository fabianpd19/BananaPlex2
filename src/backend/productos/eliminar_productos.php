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

    try {
        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        header("Location: ../../productos.php"); // Redirige a la página principal después de eliminar
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Acción denegada, no posee los permisos para realizar esta acción.";
        header('Location: ../../productos.php');
        exit(); // Asegura que el script se detenga aquí
    }
}
