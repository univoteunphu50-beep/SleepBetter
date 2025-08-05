<?php
/**
 * Funciones para manejo de stock y alertas
 */

include("conexion.php");
include("db_helper.php");

/**
 * Registra un movimiento de stock
 */
function registrarMovimientoStock($id_producto, $tipo_movimiento, $cantidad_movimiento, $motivo = '', $usuario = '', $id_factura = null) {
    global $conn;
    
    try {
        // Obtener stock actual del producto
        $producto = selectOne($conn, "SELECT stock, nombre FROM productos WHERE id = ?", [$id_producto]);
        
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
        $sql = "
            INSERT INTO movimientos_stock 
            (id_producto, tipo_movimiento, cantidad_anterior, cantidad_movimiento, cantidad_nueva, motivo, usuario, id_factura) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ";
        $params = [$id_producto, $tipo_movimiento, $cantidad_anterior, $cantidad_movimiento, $cantidad_nueva, $motivo, $usuario, $id_factura];
        
        if (executeUpdate($conn, $sql, $params) <= 0) {
            throw new Exception("Error al registrar movimiento de stock");
        }
        
        // Actualizar stock del producto
        $sql = "UPDATE productos SET stock = ? WHERE id = ?";
        $params = [$cantidad_nueva, $id_producto];
        
        if (executeUpdate($conn, $sql, $params) <= 0) {
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
        $producto = selectOne($conn, "SELECT restock, nombre FROM productos WHERE id = ?", [$id_producto]);
        
        if (!$producto || $producto['restock'] <= 0) {
            return; // No hay restock configurado
        }
        
        // Si el stock actual es menor o igual al restock, crear alerta
        if ($stock_actual <= $producto['restock']) {
            // Verificar si ya existe una alerta activa para este producto
            $alerta_existente = selectOne($conn, "SELECT id FROM alertas_restock WHERE id_producto = ? AND estado = 'activa'", [$id_producto]);
            
            if (!$alerta_existente) {
                // Crear nueva alerta
                $sql = "
                    INSERT INTO alertas_restock (id_producto, stock_actual, restock_limite) 
                    VALUES (?, ?, ?)
                ";
                $params = [$id_producto, $stock_actual, $producto['restock']];
                executeInsert($conn, $sql, $params);
            }
        } else {
            // Si el stock se recuperó, resolver alertas activas
            $sql = "
                UPDATE alertas_restock 
                SET estado = 'resuelta', fecha_resuelta = CURRENT_TIMESTAMP 
                WHERE id_producto = ? AND estado = 'activa'
            ";
            executeUpdate($conn, $sql, [$id_producto]);
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
        $sql = "
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
        ";
        $params = [$id_producto, $limite];
        
        return selectAll($conn, $sql, $params);
        
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
        
        $params = [];
        
        if ($solo_activas) {
            $sql .= " WHERE ar.estado = 'activa'";
        }
        
        $sql .= " ORDER BY ar.fecha_alerta DESC";
        
        return selectAll($conn, $sql, $params);
        
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
        $sql = "
            UPDATE alertas_restock 
            SET estado = 'resuelta', usuario_resuelve = ?, fecha_resuelta = CURRENT_TIMESTAMP 
            WHERE id = ?
        ";
        $params = [$usuario, $id_alerta];
        
        return executeUpdate($conn, $sql, $params) > 0;
        
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
        $sql = "
            UPDATE alertas_restock 
            SET estado = 'ignorada', usuario_resuelve = ?, fecha_resuelta = CURRENT_TIMESTAMP 
            WHERE id = ?
        ";
        $params = [$usuario, $id_alerta];
        
        return executeUpdate($conn, $sql, $params) > 0;
        
    } catch (Exception $e) {
        error_log("Error en ignorarAlertaRestock: " . $e->getMessage());
        return false;
    }
} 