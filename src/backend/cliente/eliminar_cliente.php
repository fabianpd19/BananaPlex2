<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../clientes.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    try {
        $sql = "DELETE FROM clientes WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        header("Location: ../../clientes.php"); // Redirige a la página principal después de eliminar
    } catch (PDOException $e) {// Configurar mensaje de error en la sesión
        $_SESSION['error_message'] = "Acción denegada, no posee los permisos para realizar esta acción.";
        header('Location: ../../empleados.php');
        exit(); // Asegura que el script se detenga aquídie("Error al eliminar cliente: " . $e->getMessage());
    }
}
