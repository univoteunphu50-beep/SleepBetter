<?php
header('Content-Type: application/json');
include("conexion.php");
include("db_helper.php");

try {
    $estado = $_GET['estado'] ?? '';
    $producto = $_GET['producto'] ?? '';
    $fecha = $_GET['fecha'] ?? '';
    
    // Construir la consulta base
    $sql = "
        SELECT 
            ar.*,
            p.nombre as nombre_producto,
            p.precio
        FROM alertas_restock ar
        INNER JOIN productos p ON ar.id_producto = p.id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Aplicar filtros
    if ($estado) {
        $sql .= " AND ar.estado = ?";
        $params[] = $estado;
    }
    
    if ($producto) {
        $sql .= " AND ar.id_producto = ?";
        $params[] = $producto;
    }
    
    if ($fecha) {
        $sql .= " AND DATE(ar.fecha_alerta) = ?";
        $params[] = $fecha;
    }
    
    $sql .= " ORDER BY ar.fecha_alerta DESC";
    
    $alertas = selectAll($conn, $sql, $params);
    
    echo json_encode($alertas);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 