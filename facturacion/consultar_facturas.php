<?php
// Archivo: consultar_facturas.php

header('Content-Type: application/json');
include("../conexion.php");

// Filtros
$cliente = $_GET['cliente'] ?? '';
$producto = $_GET['producto'] ?? '';
$fecha = $_GET['fecha'] ?? '';

// Consulta con JOIN para obtener el nombre real del cliente
$sql = "
    SELECT 
        f.id,
        f.fecha,
        c.cliente AS cliente,  -- Aquí se toma el nombre desde la tabla clientes
        GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos,
        f.total
    FROM facturas f
    INNER JOIN clientes c ON f.cedula_cliente = c.cedula
    INNER JOIN detalle_factura d ON f.id = d.id_factura
    INNER JOIN productos p ON d.id_producto = p.id
    WHERE 1=1
";

// Filtros dinámicos
if (!empty($cliente)) {
    $cliente = $conn->real_escape_string($cliente);
    $sql .= " AND f.cedula_cliente LIKE '%$cliente%'";
}

if (!empty($producto)) {
    $producto = $conn->real_escape_string($producto);
    $sql .= " AND p.nombre LIKE '%$producto%'";
}

if (!empty($fecha)) {
    $fecha = $conn->real_escape_string($fecha);
    $sql .= " AND DATE(f.fecha) = '$fecha'";
}

$sql .= " GROUP BY f.id ORDER BY f.fecha DESC";

$result = $conn->query($sql);

$facturas = [];
while ($row = $result->fetch_assoc()) {
    $facturas[] = $row;
}

echo json_encode($facturas);
?>
