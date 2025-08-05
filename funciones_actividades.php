<?php
/**
 * Funciones para registro de actividades
 */

include("conexion.php");

/**
 * Registra una actividad en el sistema
 */
function registrarActividad($accion, $modulo, $descripcion = '', $datos_anteriores = null, $datos_nuevos = null, $usuario_id = null, $usuario_nombre = null) {
    global $conn;
    
    try {
        // Obtener información del usuario actual si no se proporciona
        if ($usuario_id === null) {
            $usuario_id = $_SESSION['usuario_id'] ?? 0;
        }
        if ($usuario_nombre === null) {
            $usuario_nombre = $_SESSION['usuario_nombre'] ?? 'Sistema';
        }
        
        // Obtener información del cliente
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Preparar datos JSON
        $datos_anteriores_json = $datos_anteriores ? json_encode($datos_anteriores) : null;
        $datos_nuevos_json = $datos_nuevos ? json_encode($datos_nuevos) : null;
        
        $stmt = $conn->prepare("
            INSERT INTO actividades 
            (usuario_id, usuario_nombre, accion, modulo, descripcion, datos_anteriores, datos_nuevos, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->bind_param("issssssss", 
            $usuario_id, 
            $usuario_nombre, 
            $accion, 
            $modulo, 
            $descripcion, 
            $datos_anteriores_json, 
            $datos_nuevos_json, 
            $ip_address, 
            $user_agent
        );
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error en registrarActividad: " . $e->getMessage());
        return false;
    }
}

/**
 * Registra login de usuario
 */
function registrarLogin($usuario_id, $usuario_nombre, $exito = true) {
    $accion = $exito ? 'login_exitoso' : 'login_fallido';
    $descripcion = $exito ? "Usuario inició sesión" : "Intento fallido de inicio de sesión";
    
    return registrarActividad($accion, 'autenticacion', $descripcion);
}

/**
 * Registra logout de usuario
 */
function registrarLogout($usuario_id, $usuario_nombre) {
    return registrarActividad('logout', 'autenticacion', "Usuario cerró sesión");
}

/**
 * Registra creación de entidad
 */
function registrarCreacion($modulo, $descripcion, $datos_nuevos = null) {
    return registrarActividad('crear', $modulo, $descripcion, null, $datos_nuevos);
}

/**
 * Registra actualización de entidad
 */
function registrarActualizacion($modulo, $descripcion, $datos_anteriores = null, $datos_nuevos = null) {
    return registrarActividad('actualizar', $modulo, $descripcion, $datos_anteriores, $datos_nuevos);
}

/**
 * Registra eliminación de entidad
 */
function registrarEliminacion($modulo, $descripcion, $datos_anteriores = null) {
    return registrarActividad('eliminar', $modulo, $descripcion, $datos_anteriores);
}

/**
 * Registra consulta/visualización
 */
function registrarConsulta($modulo, $descripcion) {
    return registrarActividad('consultar', $modulo, $descripcion);
}

/**
 * Registra acciones específicas del sistema
 */
function registrarAccionSistema($accion, $modulo, $descripcion, $datos_adicionales = null) {
    return registrarActividad($accion, $modulo, $descripcion, null, $datos_adicionales);
}

/**
 * Obtiene el historial de actividades con filtros
 */
function obtenerActividades($filtros = []) {
    global $conn;
    
    try {
        $sql = "
            SELECT 
                a.*,
                u.nombre as nombre_usuario
            FROM actividades a
            LEFT JOIN usuarios u ON a.usuario_id = u.id
            WHERE 1=1
        ";
        
        $params = [];
        $types = "";
        
        // Aplicar filtros
        if (isset($filtros['usuario_id']) && $filtros['usuario_id']) {
            $sql .= " AND a.usuario_id = ?";
            $params[] = $filtros['usuario_id'];
            $types .= "i";
        }
        
        if (isset($filtros['modulo']) && $filtros['modulo']) {
            $sql .= " AND a.modulo = ?";
            $params[] = $filtros['modulo'];
            $types .= "s";
        }
        
        if (isset($filtros['accion']) && $filtros['accion']) {
            $sql .= " AND a.accion = ?";
            $params[] = $filtros['accion'];
            $types .= "s";
        }
        
        if (isset($filtros['fecha_desde']) && $filtros['fecha_desde']) {
            $sql .= " AND DATE(a.fecha_actividad) >= ?";
            $params[] = $filtros['fecha_desde'];
            $types .= "s";
        }
        
        if (isset($filtros['fecha_hasta']) && $filtros['fecha_hasta']) {
            $sql .= " AND DATE(a.fecha_actividad) <= ?";
            $params[] = $filtros['fecha_hasta'];
            $types .= "s";
        }
        
        $sql .= " ORDER BY a.fecha_actividad DESC";
        
        if (isset($filtros['limite'])) {
            $sql .= " LIMIT " . intval($filtros['limite']);
        }
        
        $stmt = $conn->prepare($sql);
        
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
        
    } catch (Exception $e) {
        error_log("Error en obtenerActividades: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene estadísticas de actividades
 */
function obtenerEstadisticasActividades($filtros = []) {
    global $conn;
    
    try {
        $sql = "
            SELECT 
                COUNT(*) as total_actividades,
                COUNT(DISTINCT usuario_id) as usuarios_activos,
                COUNT(CASE WHEN accion = 'login_exitoso' THEN 1 END) as logins_exitosos,
                COUNT(CASE WHEN accion = 'login_fallido' THEN 1 END) as logins_fallidos,
                COUNT(CASE WHEN accion = 'crear' THEN 1 END) as creaciones,
                COUNT(CASE WHEN accion = 'actualizar' THEN 1 END) as actualizaciones,
                COUNT(CASE WHEN accion = 'eliminar' THEN 1 END) as eliminaciones
            FROM actividades
            WHERE 1=1
        ";
        
        $params = [];
        $types = "";
        
        // Aplicar filtros de fecha si existen
        if (isset($filtros['fecha_desde']) && $filtros['fecha_desde']) {
            $sql .= " AND DATE(fecha_actividad) >= ?";
            $params[] = $filtros['fecha_desde'];
            $types .= "s";
        }
        
        if (isset($filtros['fecha_hasta']) && $filtros['fecha_hasta']) {
            $sql .= " AND DATE(fecha_actividad) <= ?";
            $params[] = $filtros['fecha_hasta'];
            $types .= "s";
        }
        
        $stmt = $conn->prepare($sql);
        
        if ($params) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
        
    } catch (Exception $e) {
        error_log("Error en obtenerEstadisticasActividades: " . $e->getMessage());
        return [];
    }
}

/**
 * Limpia actividades antiguas (mantenimiento)
 */
function limpiarActividadesAntiguas($dias = 90) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            DELETE FROM actividades 
            WHERE fecha_actividad < DATE_SUB(NOW(), INTERVAL ? DAY)
        ");
        
        $stmt->bind_param("i", $dias);
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error en limpiarActividadesAntiguas: " . $e->getMessage());
        return false;
    }
} 