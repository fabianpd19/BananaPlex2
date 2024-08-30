<?php
// Función para obtener todos los clientes con información extendida
function obtenerClientes($pdo)
{
    $query = "SELECT * FROM obtener_clientes_info();";

    try {
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
}
