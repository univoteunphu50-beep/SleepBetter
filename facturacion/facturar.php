<?php
include("../conexion.php");
include("../db_helper.php");
include("../funciones_stock.php");

// Obtener datos del formulario
$cliente = $_POST['cliente'];
$vendedor = $_POST['vendedor'];
$fecha = $_POST['fecha'];
$productos = json_decode($_POST['productos'], true);

// Validar datos
if (empty($cliente) || empty($vendedor) || empty($fecha) || empty($productos)) {
    echo "Error: Datos incompletos";
    exit;
}

try {
    // PASO 1: VALIDAR STOCK DISPONIBLE
    $productosSinStock = [];
    
    foreach ($productos as $producto) {
        $nombre = $producto['nombre'];
        $cantidadSolicitada = intval($producto['cantidad']);
        
        // Obtener stock actual del producto
        $productoData = selectOne($conn, "SELECT id, stock, nombre FROM productos WHERE nombre = ?", [$nombre]);
        
        if (!$productoData) {
            throw new Exception("Producto no encontrado: " . $nombre);
        }
        
        $stockActual = intval($productoData['stock']);
        
        // Validar si hay suficiente stock
        if ($stockActual < $cantidadSolicitada) {
            $productosSinStock[] = [
                'nombre' => $nombre,
                'stock_disponible' => $stockActual,
                'cantidad_solicitada' => $cantidadSolicitada
            ];
        }
    }
    
    // Si hay productos sin stock suficiente, cancelar la operación
    if (!empty($productosSinStock)) {
        $mensajeError = "Stock insuficiente para los siguientes productos:\n";
        foreach ($productosSinStock as $producto) {
            $mensajeError .= "- " . $producto['nombre'] . ": Disponible " . $producto['stock_disponible'] . ", Solicitado " . $producto['cantidad_solicitada'] . "\n";
        }
        throw new Exception($mensajeError);
    }
    
    // PASO 2: CALCULAR TOTALES
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

    // PASO 3: INSERTAR FACTURA PRINCIPAL
    $sql_factura = "INSERT INTO facturas (fecha, vendedor, cedula_cliente, subtotal, itbis, total) VALUES (?, ?, ?, ?, ?, ?)";
    $params_factura = [$fecha, $vendedor, $cliente, $subtotal, $itbis, $total];
    
    $id_factura = executeInsert($conn, $sql_factura, $params_factura);
    
    if (!$id_factura) {
        throw new Exception("Error al insertar factura");
    }
    
    // PASO 4: INSERTAR DETALLE DE PRODUCTOS Y ACTUALIZAR STOCK
    foreach ($productos as $producto) {
        $precio = floatval($producto['precio']);
        $cantidad = intval($producto['cantidad']);
        $descuento = floatval($producto['descuento']);
        $aplicarItbis = $producto['itebis'];
        
        // Calcular total del producto
        $subtotalProducto = $precio * $cantidad * (1 - $descuento / 100);
        $itbisProducto = $aplicarItbis ? $subtotalProducto * 0.18 : 0;
        $totalProducto = $subtotalProducto + $itbisProducto;
        
        // Buscar el ID del producto por nombre
        $productoData = selectOne($conn, "SELECT id FROM productos WHERE nombre = ?", [$producto['nombre']]);
        $id_producto = $productoData ? $productoData['id'] : 0;
        
        // Insertar detalle con la estructura correcta
        $sql_detalle = "INSERT INTO detalle_factura (id_factura, id_producto, nombre, precio, itebis, descuento, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params_detalle = [$id_factura, $id_producto, $producto['nombre'], $precio, $aplicarItbis, $descuento, $cantidad, $precio, $totalProducto];
        
        $result_detalle = executeInsert($conn, $sql_detalle, $params_detalle);
        
        if (!$result_detalle) {
            throw new Exception("Error al insertar detalle para: " . $producto['nombre']);
        }
        
        // ACTUALIZAR STOCK DEL PRODUCTO Y REGISTRAR MOVIMIENTO
        $usuario = $_SESSION['usuario_nombre'] ?? 'Sistema';
        $motivo = "Venta - Factura #" . $id_factura;
        
        // Registrar movimiento de stock usando la función
        if (!registrarMovimientoStock($id_producto, 'venta', $cantidad, $motivo, $usuario, $id_factura)) {
            throw new Exception("Error al registrar movimiento de stock para: " . $producto['nombre']);
        }
    }
    
    echo "Factura guardada exitosamente. ID: " . $id_factura;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

closeConnection($conn);
?>

