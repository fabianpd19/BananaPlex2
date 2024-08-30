<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../empresa.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = $_POST['id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM empresas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success_message'] = 'Empresa eliminada con éxito.';
        } catch (PDOException $e) {
            // Guarda el mensaje de error en la sesión
            $_SESSION['error_message'] = 'Acción denegada, no posee los permisos para realizar esta acción.';
        }
    } else {
        $_SESSION['error_message'] = 'ID de empresa no proporcionado.';
    }
    
    // Redirige a empresas.php
    header('Location: ../../empresas.php');
    exit();
}
