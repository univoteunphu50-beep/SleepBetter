<?php
header('Content-Type: application/json');
include("conexion.php");
include("db_helper.php");

try {
    $producto = $_GET['producto'] ?? '';
    $tipo = $_GET['tipo'] ?? '';
    $fecha = $_GET['fecha'] ?? '';
    
    // Construir la consulta base
    $sql = "
        SELECT 
            ms.*,
            p.nombre as nombre_producto,
            f.id as numero_factura
        FROM movimientos_stock ms
        INNER JOIN productos p ON ms.id_producto = p.id
        LEFT JOIN facturas f ON ms.id_factura = f.id
        WHERE 1=1
    ";
    
    $params = [];
    
    // Aplicar filtros
    if ($producto) {
        $sql .= " AND ms.id_producto = ?";
        $params[] = $producto;
    }
    
    if ($tipo) {
        $sql .= " AND ms.tipo_movimiento = ?";
        $params[] = $tipo;
    }
    
    if ($fecha) {
        $sql .= " AND DATE(ms.fecha_movimiento) = ?";
        $params[] = $fecha;
    }
    
    $sql .= " ORDER BY ms.fecha_movimiento DESC LIMIT 100";
    
    $movimientos = selectAll($conn, $sql, $params);
    
    echo json_encode($movimientos);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 