<?php
session_start(); // Inicia la sesión

// Cierra la conexión a la base de datos si la tienes abierta
if (isset($conn)) {
    $conn = null; // Esto es para PDO. Si usas mysqli, usa mysqli_close($conn);
}

// Destruye la sesión
session_unset();
session_destroy();

// Redirige al login
header("Location: login.html");
exit();
?>
