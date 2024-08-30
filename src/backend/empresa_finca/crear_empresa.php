<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../empresas.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    $sql = "INSERT INTO empresas (nombre, direccion, telefono, email) 
            VALUES (:nombre, :direccion, :telefono, :email)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre' => $nombre,
            ':direccion' => $direccion,
            ':telefono' => $telefono,
            ':email' => $email
        ]);
        header("Location: ../../index.php");
    } catch (PDOException $e) {
        // Configurar mensaje de error en la sesión
        $_SESSION['error_message'] = "Acción denegada, no posee los permisos para realizar esta acción.";
        header('Location: ../../empresas.php');
        exit(); // Asegura que el script se detenga aquí
    }
}
?>