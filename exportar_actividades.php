<?php
session_start();
include("conexion.php");
include("funciones_actividades.php");

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

try {
    $usuario_id = $_GET['usuario_id'] ?? '';
    $modulo = $_GET['modulo'] ?? '';
    $accion = $_GET['accion'] ?? '';
    $fecha_desde = $_GET['fecha_desde'] ?? '';
    $fecha_hasta = $_GET['fecha_hasta'] ?? '';

    $filtros = [];
    
    if ($usuario_id) $filtros['usuario_id'] = $usuario_id;
    if ($modulo) $filtros['modulo'] = $modulo;
    if ($accion) $filtros['accion'] = $accion;
    if ($fecha_desde) $filtros['fecha_desde'] = $fecha_desde;
    if ($fecha_hasta) $filtros['fecha_hasta'] = $fecha_hasta;

    $actividades = obtenerActividades($filtros);
    
    // Configurar headers para descarga CSV
    $filename = 'actividades_' . date('Y-m-d_H-i-s') . '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Crear archivo CSV
    $output = fopen('php://output', 'w');
    
    // BOM para UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Headers del CSV
    fputcsv($output, [
        'ID',
        'Fecha',
        'Usuario',
        'Módulo',
        'Acción',
        'Descripción',
        'IP Address',
        'User Agent',
        'Datos Anteriores',
        'Datos Nuevos'
    ]);
    
    // Datos
    foreach ($actividades as $actividad) {
        fputcsv($output, [
            $actividad['id'],
            $actividad['fecha_actividad'],
            $actividad['usuario_nombre'],
            $actividad['modulo'],
            $actividad['accion'],
            $actividad['descripcion'],
            $actividad['ip_address'],
            $actividad['user_agent'],
            $actividad['datos_anteriores'],
            $actividad['datos_nuevos']
        ]);
    }
    
    fclose($output);
    
    // Registrar la exportación
    registrarAccionSistema('exportar', 'actividades', 'Exportó registro de actividades a CSV');
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Error al exportar actividades: " . $e->getMessage();
}
?> 