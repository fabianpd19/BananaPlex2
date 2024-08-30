<?php
// Función para obtener todos los productos
// Función para obtener todos los productos
function obtenerProductos($pdo)
{
    $query = "SELECT * FROM obtener_productos_info();";

    try {
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
}
