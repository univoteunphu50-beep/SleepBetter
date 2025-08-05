<?php
header('Content-Type: application/json');
include("conexion.php");
include("funciones_stock.php");

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
    $types = "";
    
    // Aplicar filtros
    if ($estado) {
        $sql .= " AND ar.estado = ?";
        $params[] = $estado;
        $types .= "s";
    }
    
    if ($producto) {
        $sql .= " AND ar.id_producto = ?";
        $params[] = $producto;
        $types .= "i";
    }
    
    if ($fecha) {
        $sql .= " AND DATE(ar.fecha_alerta) = ?";
        $params[] = $fecha;
        $types .= "s";
    }
    
    $sql .= " ORDER BY ar.fecha_alerta DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $alertas = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode($alertas);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 