<?php
header('Content-Type: application/json');
include("conexion.php");
include("funciones_stock.php");

try {
    // Obtener todos los productos que tienen restock configurado
    $stmt = $conn->prepare("
        SELECT id, stock, restock, nombre 
        FROM productos 
        WHERE restock > 0
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $productos = $result->fetch_all(MYSQLI_ASSOC);
    
    $alertasCreadas = 0;
    $alertasResueltas = 0;
    
    foreach ($productos as $producto) {
        $stockActual = $producto['stock'];
        $restockLimite = $producto['restock'];
        
        // Verificar si debe crear alerta
        if ($stockActual <= $restockLimite) {
            // Verificar si ya existe una alerta activa
            $stmtCheck = $conn->prepare("
                SELECT id FROM alertas_restock 
                WHERE id_producto = ? AND estado = 'activa'
            ");
            $stmtCheck->bind_param("i", $producto['id']);
            $stmtCheck->execute();
            
            if ($stmtCheck->get_result()->num_rows == 0) {
                // Crear nueva alerta
                $stmtInsert = $conn->prepare("
                    INSERT INTO alertas_restock (id_producto, stock_actual, restock_limite) 
                    VALUES (?, ?, ?)
                ");
                $stmtInsert->bind_param("iii", $producto['id'], $stockActual, $restockLimite);
                $stmtInsert->execute();
                $alertasCreadas++;
            } else {
                // Actualizar stock_actual de alerta existente
                $stmtUpdate = $conn->prepare("
                    UPDATE alertas_restock 
                    SET stock_actual = ? 
                    WHERE id_producto = ? AND estado = 'activa'
                ");
                $stmtUpdate->bind_param("ii", $stockActual, $producto['id']);
                $stmtUpdate->execute();
            }
        } else {
            // Si el stock se recuperó, resolver alertas activas
            $stmtUpdate = $conn->prepare("
                UPDATE alertas_restock 
                SET estado = 'resuelta', fecha_resuelta = CURRENT_TIMESTAMP 
                WHERE id_producto = ? AND estado = 'activa'
            ");
            $stmtUpdate->bind_param("i", $producto['id']);
            $stmtUpdate->execute();
            
            if ($stmtUpdate->affected_rows > 0) {
                $alertasResueltas++;
            }
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => "Verificación completada",
        'alertas_creadas' => $alertasCreadas,
        'alertas_resueltas' => $alertasResueltas
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?> 