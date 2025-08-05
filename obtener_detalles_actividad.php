<?php
header('Content-Type: application/json');
include("conexion.php");

try {
    $id = $_GET['id'] ?? 0;
    
    if (!$id) {
        throw new Exception('ID de actividad no proporcionado');
    }

    $stmt = $conn->prepare("
        SELECT * FROM actividades 
        WHERE id = ?
    ");
    
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $actividad = $result->fetch_assoc();
    
    if (!$actividad) {
        throw new Exception('Actividad no encontrada');
    }
    
    echo json_encode($actividad);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 