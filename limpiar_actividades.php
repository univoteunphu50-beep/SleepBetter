<?php
header('Content-Type: application/json');
include("conexion.php");
include("funciones_actividades.php");

try {
    $dias = $_POST['dias'] ?? 90;
    
    $success = limpiarActividadesAntiguas($dias);
    
    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Actividades antiguas limpiadas exitosamente']);
    } else {
        throw new Exception('Error al limpiar actividades');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 