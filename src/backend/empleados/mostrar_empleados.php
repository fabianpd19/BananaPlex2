<?php
// Función para obtener todos los empleados con información extendida usando el procedimiento almacenado
function obtenerEmpleados($pdo)
{
    $query = "SELECT * FROM obtener_empleados();";

    try {
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
}
