<?php
include("conexion.php");

// Datos de prueba
$cliente = "12345678901";
$vendedor = "Vendedor de Prueba";
$fecha = date('Y-m-d');
$productos = [
    [
        'nombre' => 'Producto de Prueba',
        'precio' => 100.00,
        'cantidad' => 2,
        'descuento' => 0,
        'itebis' => true
    ]
];

// Calcular totales
$subtotal = 0;
$itbis = 0;

foreach ($productos as $producto) {
    $precio = floatval($producto['precio']);
    $cantidad = intval($producto['cantidad']);
    $descuento = floatval($producto['descuento']);
    $aplicarItbis = $producto['itebis'];
    
    // Calcular subtotal del producto
    $subtotalProducto = $precio * $cantidad * (1 - $descuento / 100);
    $subtotal += $subtotalProducto;
    
    // Calcular ITBIS si aplica
    if ($aplicarItbis) {
        $itbis += $subtotalProducto * 0.18;
    }
}

$total = $subtotal + $itbis;

echo "=== PRUEBA DE FACTURACIÓN ===\n";
echo "Cliente: $cliente\n";
echo "Vendedor: $vendedor\n";
echo "Fecha: $fecha\n";
echo "Subtotal: $subtotal\n";
echo "ITBIS: $itbis\n";
echo "Total: $total\n\n";

try {
    // Insertar factura principal
    $stmt = $conn->prepare("INSERT INTO facturas (fecha, vendedor, cedula_cliente, subtotal, itbis, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssddd", $fecha, $vendedor, $cliente, $subtotal, $itbis, $total);
    
    if (!$stmt->execute()) {
        throw new Exception("Error al insertar factura: " . $stmt->error);
    }
    
    $id_factura = $stmt->insert_id;
    echo "✅ Factura creada exitosamente. ID: $id_factura\n";
    
    // Insertar detalle de productos
    foreach ($productos as $producto) {
        $precio = floatval($producto['precio']);
        $cantidad = intval($producto['cantidad']);
        $descuento = floatval($producto['descuento']);
        $aplicarItbis = $producto['itebis'];
        
        // Calcular total del producto
        $subtotalProducto = $precio * $cantidad * (1 - $descuento / 100);
        $itbisProducto = $aplicarItbis ? $subtotalProducto * 0.18 : 0;
        $totalProducto = $subtotalProducto + $itbisProducto;
        
        // Insertar detalle con la estructura correcta
        $stmtDetalle = $conn->prepare("INSERT INTO detalle_factura (id_factura, id_producto, nombre, precio, itebis, descuento, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtDetalle->bind_param("iisdddddd", $id_factura, 1, $producto['nombre'], $precio, $aplicarItbis, $descuento, $cantidad, $precio, $totalProducto);
        
        if (!$stmtDetalle->execute()) {
            throw new Exception("Error al insertar detalle: " . $stmtDetalle->error);
        }
    }
    
    echo "✅ Detalles de factura creados exitosamente\n";
    echo "✅ PRUEBA COMPLETADA - La facturación funciona correctamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

$conn->close();
?> 