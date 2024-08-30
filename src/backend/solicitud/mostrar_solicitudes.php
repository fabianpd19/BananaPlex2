<?php
function obtenerSolicitudes($pdo, $estado = '')
{
    $query = "SELECT * FROM obtener_solicitudes(:estado);";
    try {
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
}
