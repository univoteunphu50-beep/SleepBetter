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
    
    // Consultar usuarios
    $sql = "SELECT id, nombre, email, rol, fecha_creacion FROM usuarios ORDER BY nombre";
    $usuarios = selectAll($conn, $sql);
    
    echo json_encode([
        'success' => true,
        'usuarios' => $usuarios
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

closeConnection($conn);
?> 