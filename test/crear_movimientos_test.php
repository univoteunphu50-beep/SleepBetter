<?php
include("conexion.php");
include("funciones_stock.php");

echo "<h2>Creando movimientos de stock de prueba</h2>";

try {
    // Obtener productos
    $result = $conn->query("SELECT id, nombre, stock FROM productos LIMIT 3");
    $productos = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($productos as $producto) {
        $id_producto = $producto['id'];
        $nombre = $producto['nombre'];
        
        echo "<p>Creando movimientos para: <strong>$nombre</strong></p>";
        
        // Movimiento de compra
        if (registrarMovimientoStock($id_producto, 'compra', 50, 'Compra inicial', 'Admin', null)) {
            echo "<p style='color: green;'>✅ Compra de 50 unidades</p>";
        }
        
        // Movimiento de venta
        if (registrarMovimientoStock($id_producto, 'venta', 20, 'Venta de prueba', 'Admin', null)) {
            echo "<p style='color: green;'>✅ Venta de 20 unidades</p>";
        }
        
        // Movimiento de ajuste
        if (registrarMovimientoStock($id_producto, 'ajuste', 100, 'Ajuste de inventario', 'Admin', null)) {
            echo "<p style='color: green;'>✅ Ajuste a 100 unidades</p>";
        }
        
        // Movimiento de merma
        if (registrarMovimientoStock($id_producto, 'merma', 5, 'Merma por daños', 'Admin', null)) {
            echo "<p style='color: green;'>✅ Merma de 5 unidades</p>";
        }
    }
    
    echo "<h3>✅ Movimientos de prueba creados exitosamente</h3>";
    echo "<p><a href='index.php'>Volver al panel principal</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

$conn->close();
?> 