<?php
// Incluir verificación de autenticación primero
include("../auth_check.php");

// Verificar que el usuario sea administrador
$user = getCurrentUser();
if ($user['rol'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Acceso denegado. Solo administradores pueden acceder a este módulo.']);
    exit;
}

// Configurar headers para JSON
header('Content-Type: application/json');

try {
    // Incluir configuración de base de datos unificada
    include("../conexion.php");
    include("../db_helper.php");
    
    // Obtener ID del usuario a eliminar
    $id = $_POST['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
        exit;
    }
    
    // Verificar si el usuario existe
    $usuario = selectOne($conn, "SELECT id, rol FROM usuarios WHERE id = ?", [$id]);
    
    if (!$usuario) {
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar si es el último administrador
    if ($usuario['rol'] === 'admin') {
        $totalAdmins = selectOne($conn, "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'");
        
        if ($totalAdmins['total'] <= 1) {
            echo json_encode(['success' => false, 'error' => 'No se puede eliminar el último administrador']);
            exit;
        }
    }
    
    // Eliminar usuario
    $sql = "DELETE FROM usuarios WHERE id = ?";
    $affected = executeUpdate($conn, $sql, [$id]);
    
    if ($affected > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No se pudo eliminar el usuario'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

closeConnection($conn);
?> 