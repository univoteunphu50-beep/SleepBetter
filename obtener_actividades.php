<?php
header('Content-Type: application/json');
include("conexion.php");
include("funciones_actividades.php");

try {
    $usuario_id = $_GET['usuario_id'] ?? '';
    $modulo = $_GET['modulo'] ?? '';
    $accion = $_GET['accion'] ?? '';
    $fecha_desde = $_GET['fecha_desde'] ?? '';
    $fecha_hasta = $_GET['fecha_hasta'] ?? '';
    $limite = $_GET['limite'] ?? 100;

    $filtros = [];
    
    if ($usuario_id) $filtros['usuario_id'] = $usuario_id;
    if ($modulo) $filtros['modulo'] = $modulo;
    if ($accion) $filtros['accion'] = $accion;
    if ($fecha_desde) $filtros['fecha_desde'] = $fecha_desde;
    if ($fecha_hasta) $filtros['fecha_hasta'] = $fecha_hasta;
    if ($limite) $filtros['limite'] = $limite;

    $actividades = obtenerActividades($filtros);
    
    echo json_encode($actividades);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 