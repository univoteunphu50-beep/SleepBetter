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
    // Incluir configuración de base de datos
    include("db.php");
    
    // Conectar a la base de datos
    $pdo = getDbConnection();
    
    // Obtener ID del usuario a eliminar
    $id = $_POST['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'error' => 'ID de usuario requerido']);
        exit;
    }
    
    // Verificar si el usuario existe
    $stmt = $pdo->prepare("SELECT id, rol FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar si es el último administrador
    if ($usuario['rol'] === 'admin') {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'");
        $stmt->execute();
        $totalAdmins = $stmt->fetchColumn();
        
        if ($totalAdmins <= 1) {
            echo json_encode(['success' => false, 'error' => 'No se puede eliminar el último administrador']);
            exit;
        }
    }
    
    // Eliminar usuario
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario eliminado exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No se pudo eliminar el usuario'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]);
}
?> 