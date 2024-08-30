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
    $nombre1 = strtoupper($_POST['nombre1']);
    $nombre2 = strtoupper($_POST['nombre2']);
    $apellido1 = strtoupper($_POST['apellido1']);
    $apellido2 = strtoupper($_POST['apellido2']);
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $empresa = $_POST['empresa'];
    $provincia = $_POST['provincia'];

    try {
        $sql = "UPDATE clientes SET nombre1 = :nombre1, nombre2 = :nombre2, apellido1 = :apellido1, apellido2 = :apellido2, direccion = :direccion, telefono = :telefono, empresa_id = :empresa, provincia_id = :provincia WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'nombre1' => $nombre1,
            'nombre2' => $nombre2,
            'apellido1' => $apellido1,
            'apellido2' => $apellido2,
            'direccion' => $direccion,
            'telefono' => $telefono,
            'empresa' => $empresa,
            'provincia' => $provincia
        ]);
        header("Location: ../../clientes.php"); // Redirige a la página principal después de actualizar
    } catch (PDOException $e) {
        die("Error al actualizar cliente: " . $e->getMessage());
    }
}
