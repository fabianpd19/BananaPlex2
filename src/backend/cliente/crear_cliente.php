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
// Configurar la zona horaria adecuada para Ecuador
date_default_timezone_set('America/Guayaquil');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre1 = $_POST['nombre1'];
    $nombre2 = $_POST['nombre2'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $cedula = $_POST['cedula'];
    $empresa_id = $_POST['empresa'];
    $provincia_id = $_POST['provincia'];

    // Validar cédula solo números y exactamente 10 dígitos
    if (!preg_match("/^\d{10}$/", $cedula)) {
        die("Error: La cédula debe contener solo números y tener exactamente 10 dígitos.");
    }

    // Obtener la fecha y hora actual del servidor PHP
    $fecha_registro = date('Y-m-d H:i:s');

    $sql = "INSERT INTO clientes (nombre1, nombre2, apellido1, apellido2, direccion, telefono, cedula, empresa_id, provincia_id, fecha_registro) 
            VALUES (:nombre1, :nombre2, :apellido1, :apellido2, :direccion, :telefono, :cedula, :empresa_id, :provincia_id, :fecha_registro)";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre1' => $nombre1,
            ':nombre2' => $nombre2,
            ':apellido1' => $apellido1,
            ':apellido2' => $apellido2,
            ':direccion' => $direccion,
            ':telefono' => $telefono,
            ':cedula' => $cedula,
            ':empresa_id' => $empresa_id,
            ':provincia_id' => $provincia_id,
            ':fecha_registro' => $fecha_registro
        ]);
        header("Location: ../../index.html"); // Redirige a la página principal después de crear el cliente
        exit();
    } catch (PDOException $e) {
        die("Error al crear cliente: " . $e->getMessage());
    }
}
