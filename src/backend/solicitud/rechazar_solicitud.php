<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['email'])) {
    header('Location: ../../solicitudes.php');
    exit();
}

$role = $_SESSION['role'];
$pdo = connect_db($role);

if (isset($_GET['id'])) {
    $solicitud_id = $_GET['id'];

    // Iniciar la transacción
    $pdo->beginTransaction();

    try {
        // Rechazar solicitud
        $query = "SELECT rechazar_solicitud(:id)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $solicitud_id);
        $stmt->execute();

        // Confirmar la transacción
        $pdo->commit();
        $message = "Solicitud rechazada correctamente.";
        $message_type = "success";
    } catch (PDOException $e) {
        // Revertir la transacción si ocurre un error
        $pdo->rollBack();

        // Manejar error específico
        if (strpos($e->getMessage(), 'Solicitud no encontrada o ya procesada') !== false) {
            $message = "Solicitud no encontrada o ya procesada.";
        } else {
            $message = "Error al procesar la solicitud.";
        }
        $message_type = "error";
    }
} else {
    $message = "Solicitud no válida.";
    $message_type = "error";
}
?>
<script>
    alert("<?php echo addslashes($message); ?>");
    window.location.href = '../../solicitudes.php'; // Redirigir después de mostrar el mensaje
</script>