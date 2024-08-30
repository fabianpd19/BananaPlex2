<?php
// Función para obtener todas las empresas con información extendida
function obtenerEmpresas($pdo)
{
    $query = "SELECT * FROM obtener_empresas_info();";

    try {
        $stmt = $pdo->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
}
