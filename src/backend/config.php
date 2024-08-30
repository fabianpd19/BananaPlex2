<?php
// Configuración de la conexión a la base de datos
function connect_db($role)
{
    $host = 'db';
    $port = '5432';
    $dbname = 'BananaPlex';

    if ($role == 'admin2') {
        $user = 'admin2';
        $password = 'admin_password';
    } elseif ($role == 'empleado1') {
        $user = 'empleado1';
        $password = 'empleado_password';
    } else {
        die('Rol no válido');
    }

    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$password";
    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Error en la conexión: " . $e->getMessage());
    }
}
