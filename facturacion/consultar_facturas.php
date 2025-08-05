<?php
// Archivo: consultar_facturas.php

header('Content-Type: application/json');
include("../conexion.php");
include("../db_helper.php");

try {
    // Filtros
    $cliente = $_GET['cliente'] ?? '';
    $producto = $_GET['producto'] ?? '';
    $fecha = $_GET['fecha'] ?? '';

    // Consulta con JOIN para obtener el nombre real del cliente
    $sql = "
        SELECT 
            f.id,
            f.numero_factura,
            f.fecha_factura,
            f.cliente,
            STRING_AGG(p.nombre, ', ') AS productos,
            f.total
        FROM facturas f
        LEFT JOIN detalles_factura d ON f.id = d.factura_id
        LEFT JOIN productos p ON d.producto_id = p.id
        WHERE 1=1
    ";

    $params = [];

    // Filtros dinámicos
    if (!empty($cliente)) {
        $sql .= " AND f.cliente LIKE ?";
        $params[] = "%$cliente%";
    }

    if (!empty($producto)) {
        $sql .= " AND p.nombre LIKE ?";
        $params[] = "%$producto%";
    }

    if (!empty($fecha)) {
        $sql .= " AND DATE(f.fecha_factura) = ?";
        $params[] = $fecha;
    }

    $sql .= " GROUP BY f.id, f.numero_factura, f.fecha_factura, f.cliente, f.total ORDER BY f.fecha_factura DESC";

    $facturas = selectAll($conn, $sql, $params);

    echo json_encode($facturas, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

closeConnection($conn);
?>
