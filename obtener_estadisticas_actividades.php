<?php
header('Content-Type: application/json');
include("conexion.php");
include("funciones_actividades.php");

try {
    $fecha_desde = $_GET['fecha_desde'] ?? '';
    $fecha_hasta = $_GET['fecha_hasta'] ?? '';

    $filtros = [];
    
    if ($fecha_desde) $filtros['fecha_desde'] = $fecha_desde;
    if ($fecha_hasta) $filtros['fecha_hasta'] = $fecha_hasta;

    $estadisticas = obtenerEstadisticasActividades($filtros);
    
    echo json_encode($estadisticas);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 