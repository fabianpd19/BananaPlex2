<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validación de credenciales
    if (($email == 'admin@example.com' && $password == 'admin_password') || 
        ($email == 'empleado@example.com' && $password == 'empleado_password')) {

        $_SESSION['email'] = $email;
        $_SESSION['role'] = ($email == 'admin@example.com') ? 'admin2' : 'empleado1';

        header('Location: ../index.php');
        exit();
    } else {
        echo "Credenciales incorrectas";
    }
}
?>
