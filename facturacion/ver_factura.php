<?php
header('Content-Type: application/json');
include("../conexion.php");

$id_factura = $_GET['id'] ?? 0;

if (!$id_factura) {
    echo json_encode(['error' => 'ID de factura no proporcionado']);
    exit;
}

try {
    // Obtener datos de la factura
    $stmt = $conn->prepare("
        SELECT 
            f.id,
            f.fecha,
            f.vendedor,
            f.subtotal,
            f.itbis,
            f.total,
            c.cliente,
            c.cedula,
            c.telefono,
            c.email,
            c.direccion
        FROM facturas f
        INNER JOIN clientes c ON f.cedula_cliente = c.cedula
        WHERE f.id = ?
    ");
    
    $stmt->bind_param("i", $id_factura);
    $stmt->execute();
    $result = $stmt->get_result();
    $factura = $result->fetch_assoc();
    
    if (!$factura) {
        echo json_encode(['error' => 'Factura no encontrada']);
        exit;
    }
    
    // Obtener productos de la factura
    $stmt = $conn->prepare("
        SELECT 
            d.nombre,
            d.precio,
            d.cantidad,
            d.descuento,
            d.itebis,
            d.total as total_producto
        FROM detalle_factura d
        WHERE d.id_factura = ?
    ");
    
    $stmt->bind_param("i", $id_factura);
    $stmt->execute();
    $result = $stmt->get_result();
    $productos = [];
    
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
    
    $factura['productos'] = $productos;
    
    echo json_encode($factura);
    
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al obtener la factura: ' . $e->getMessage()]);
}

$conn->close();
?> 