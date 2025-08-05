<?php
header('Content-Type: application/json');
include("../conexion.php");
include("../db_helper.php");

$id_factura = $_GET['id'] ?? 0;

if (!$id_factura) {
    echo json_encode(['error' => 'ID de factura no proporcionado']);
    exit;
}

try {
    // Obtener datos de la factura
    $sql_factura = "
        SELECT 
            f.id,
            f.numero_factura,
            f.fecha_factura,
            f.vendedor,
            f.cliente,
            f.subtotal,
            f.itbis,
            f.total
        FROM facturas f
        WHERE f.id = ?
    ";
    
    $factura = selectOne($conn, $sql_factura, [$id_factura]);
    
    if (!$factura) {
        echo json_encode(['error' => 'Factura no encontrada']);
        exit;
    }
    
    // Obtener productos de la factura
    $sql_productos = "
        SELECT 
            d.nombre_producto as nombre,
            d.precio,
            d.cantidad,
            d.descuento,
            d.aplicar_itbis as itebis,
            d.total_producto as total
        FROM detalles_factura d
        WHERE d.factura_id = ?
    ";
    
    $productos = selectAll($conn, $sql_productos, [$id_factura]);
    
    $factura['productos'] = $productos;
    
    echo json_encode($factura, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener la factura: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

closeConnection($conn);
?> 