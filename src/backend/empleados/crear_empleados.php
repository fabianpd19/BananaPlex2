<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../login.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);

// Configurar la zona horaria adecuada para Ecuador
date_default_timezone_set('America/Guayaquil');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Datos del usuario
    $nombreUsuario = $_POST['nombreUsuario'];
    $correo = $_POST['correo'];
    $contraseña = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);
    $rol_id = $_POST['rol_id'];

    // Datos del empleado
    $nombre1 = $_POST['nombre1'];
    $nombre2 = $_POST['nombre2'];
    $apellido1 = $_POST['apellido1'];
    $apellido2 = $_POST['apellido2'];
    $cedula = $_POST['cedula'];
    $direccion = $_POST['direccion'];
    $provincia_id = $_POST['provincia'];

    // Obtener la fecha y hora actual del servidor PHP
    $fecha_registro = date('Y-m-d H:i:s');

    // Iniciar la transacción
    $pdo->beginTransaction();

    try {
        // Insertar el nuevo usuario
        $sqlUsuario = "INSERT INTO usuarios (nombre, correo, contraseña, rol_id) 
                       VALUES (:nombre, :correo, :contrasena, :rol_id) RETURNING id";
        $stmtUsuario = $pdo->prepare($sqlUsuario);
        $stmtUsuario->execute([
            ':nombre' => $nombreUsuario,
            ':correo' => $correo,
            ':contrasena' => $contraseña,
            ':rol_id' => $rol_id
        ]);

        // Obtener el ID del usuario insertado
        $usuario_id = $stmtUsuario->fetchColumn();

        // Insertar el nuevo empleado relacionado con el usuario
        $sqlEmpleado = "INSERT INTO empleados (usuario_id, nombre1, nombre2, apellido1, apellido2, cedula, direccion, provincia_id, fecha_registro) 
                        VALUES (:usuario_id, :nombre1, :nombre2, :apellido1, :apellido2, :cedula, :direccion, :provincia_id, :fecha_registro)";
        $stmtEmpleado = $pdo->prepare($sqlEmpleado);
        $stmtEmpleado->execute([
            ':usuario_id' => $usuario_id,
            ':nombre1' => $nombre1,
            ':nombre2' => $nombre2,
            ':apellido1' => $apellido1,
            ':apellido2' => $apellido2,
            ':cedula' => $cedula,
            ':direccion' => $direccion,
            ':provincia_id' => $provincia_id,
            ':fecha_registro' => $fecha_registro
        ]);

        // Confirmar la transacción
        $pdo->commit();
        header("Location: ../../index.php");
        exit(); // Asegura que el script se detenga aquí
    } catch (PDOException $e) {
        // Revertir la transacción si ocurre un error
        $pdo->rollBack();

        // Configurar mensaje de error en la sesión
        $_SESSION['error_message'] = "Acción denegada, no posee los permisos para realizar esta acción.";
        header('Location: ../../empleados.php');
        exit(); // Asegura que el script se detenga aquí
    }
}
