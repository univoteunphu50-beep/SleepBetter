<?php
/**
 * Funciones para manejo de stock y alertas
 */

include("conexion.php");

/**
 * Registra un movimiento de stock
 */
function registrarMovimientoStock($id_producto, $tipo_movimiento, $cantidad_movimiento, $motivo = '', $usuario = '', $id_factura = null) {
    global $conn;
    
    try {
        // Obtener stock actual del producto
        $stmt = $conn->prepare("SELECT stock, nombre FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        
        if (!$producto) {
            throw new Exception("Producto no encontrado");
        }
        
        $cantidad_anterior = $producto['stock'];
        $cantidad_nueva = $cantidad_anterior;
        
        // Calcular nueva cantidad según tipo de movimiento
        switch ($tipo_movimiento) {
            case 'venta':
            case 'merma':
                $cantidad_nueva = $cantidad_anterior - $cantidad_movimiento;
                break;
            case 'compra':
            case 'devolucion':
            case 'restock':
                $cantidad_nueva = $cantidad_anterior + $cantidad_movimiento;
                break;
            case 'ajuste':
                $cantidad_nueva = $cantidad_movimiento; // En ajuste, cantidad_movimiento es el nuevo valor
                break;
        }
        
        // Insertar movimiento
        $stmt = $conn->prepare("
            INSERT INTO movimientos_stock 
            (id_producto, tipo_movimiento, cantidad_anterior, cantidad_movimiento, cantidad_nueva, motivo, usuario, id_factura) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("isiiissi", $id_producto, $tipo_movimiento, $cantidad_anterior, $cantidad_movimiento, $cantidad_nueva, $motivo, $usuario, $id_factura);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al registrar movimiento de stock");
        }
        
        // Actualizar stock del producto
        $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id = ?");
        $stmt->bind_param("ii", $cantidad_nueva, $id_producto);
        
        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar stock del producto");
        }
        
        // Verificar si se debe crear alerta de restock
        verificarAlertaRestock($id_producto, $cantidad_nueva);
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error en registrarMovimientoStock: " . $e->getMessage());
        return false;
    }
}

/**
 * Verifica si se debe crear una alerta de restock
 */
function verificarAlertaRestock($id_producto, $stock_actual) {
    global $conn;
    
    try {
        // Obtener información del producto
        $stmt = $conn->prepare("SELECT restock, nombre FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id_producto);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        
        if (!$producto || $producto['restock'] <= 0) {
            return; // No hay restock configurado
        }
        
        // Si el stock actual es menor o igual al restock, crear alerta
        if ($stock_actual <= $producto['restock']) {
            // Verificar si ya existe una alerta activa para este producto
            $stmt = $conn->prepare("SELECT id FROM alertas_restock WHERE id_producto = ? AND estado = 'activa'");
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows == 0) {
                // Crear nueva alerta
                $stmt = $conn->prepare("
                    INSERT INTO alertas_restock (id_producto, stock_actual, restock_limite) 
                    VALUES (?, ?, ?)
                ");
                $stmt->bind_param("iii", $id_producto, $stock_actual, $producto['restock']);
                $stmt->execute();
            }
        } else {
            // Si el stock se recuperó, resolver alertas activas
            $stmt = $conn->prepare("
                UPDATE alertas_restock 
                SET estado = 'resuelta', fecha_resuelta = CURRENT_TIMESTAMP 
                WHERE id_producto = ? AND estado = 'activa'
            ");
            $stmt->bind_param("i", $id_producto);
            $stmt->execute();
        }
        
    } catch (Exception $e) {
        error_log("Error en verificarAlertaRestock: " . $e->getMessage());
    }
}

/**
 * Obtiene el historial de movimientos de un producto
 */
function obtenerHistorialMovimientos($id_producto, $limite = 50) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT 
                ms.*,
                p.nombre as nombre_producto,
                f.id as numero_factura
            FROM movimientos_stock ms
            INNER JOIN productos p ON ms.id_producto = p.id
            LEFT JOIN facturas f ON ms.id_factura = f.id
            WHERE ms.id_producto = ?
            ORDER BY ms.fecha_movimiento DESC
            LIMIT ?
        ");
        $stmt->bind_param("ii", $id_producto, $limite);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
        
    } catch (Exception $e) {
        error_log("Error en obtenerHistorialMovimientos: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene alertas de restock activas
 */
function obtenerAlertasRestock($solo_activas = true) {
    global $conn;
    
    try {
        $sql = "
            SELECT 
                ar.*,
                p.nombre as nombre_producto,
                p.precio
            FROM alertas_restock ar
            INNER JOIN productos p ON ar.id_producto = p.id
        ";
        
        if ($solo_activas) {
            $sql .= " WHERE ar.estado = 'activa'";
        }
        
        $sql .= " ORDER BY ar.fecha_alerta DESC";
        
        $result = $conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
        
    } catch (Exception $e) {
        error_log("Error en obtenerAlertasRestock: " . $e->getMessage());
        return [];
    }
}

/**
 * Resuelve una alerta de restock
 */
function resolverAlertaRestock($id_alerta, $usuario = '') {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            UPDATE alertas_restock 
            SET estado = 'resuelta', usuario_resuelve = ?, fecha_resuelta = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $usuario, $id_alerta);
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error en resolverAlertaRestock: " . $e->getMessage());
        return false;
    }
}

/**
 * Ignora una alerta de restock
 */
function ignorarAlertaRestock($id_alerta, $usuario = '') {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            UPDATE alertas_restock 
            SET estado = 'ignorada', usuario_resuelve = ?, fecha_resuelta = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $usuario, $id_alerta);
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error en ignorarAlertaRestock: " . $e->getMessage());
        return false;
    }
} 