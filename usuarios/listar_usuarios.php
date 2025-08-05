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
    
    // Crear tabla si no existe
    crearTablaUsuarios($pdo);
    
    // Insertar usuario admin si no existe
    insertarUsuarioAdmin($pdo);
    
    // Consultar usuarios
    $stmt = $pdo->prepare("SELECT id, usuario, nombre, email, rol, activo, fecha_creacion FROM usuarios ORDER BY nombre");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'usuarios' => $usuarios
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]);
}
?> 