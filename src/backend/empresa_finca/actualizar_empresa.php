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
    $id = $_POST['id'];
    $nombre = strtoupper($_POST['nombre']); // Convertir el nombre a mayúsculas
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    try {
        $sql = "UPDATE empresas SET nombre = :nombre, direccion = :direccion, telefono = :telefono, email = :email WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'nombre' => $nombre,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'email' => $email
        ]);
        header("Location: ../../empresas.php"); // Redirige a la página principal después de actualizar
    } catch (PDOException $e) {
       // Configurar mensaje de error en la sesión
       $_SESSION['error_message'] = "Acción denegada, no posee los permisos para realizar esta acción.";
       header('Location: ../../empresas.php');
       exit(); // Asegura que el script se detenga aquí
    }
}
?>
