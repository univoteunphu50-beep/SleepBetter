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
    
    // Validar datos requeridos
    $id = $_POST['id'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';
    
    if (empty($id) || empty($nombre) || empty($email) || empty($rol)) {
        echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios excepto la contraseña']);
        exit;
    }
    
    // Validar email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Email no válido']);
        exit;
    }
    
    // Validar rol
    $rolesPermitidos = ['admin', 'vendedor', 'usuario'];
    if (!in_array($rol, $rolesPermitidos)) {
        echo json_encode(['success' => false, 'error' => 'Rol no válido']);
        exit;
    }
    
    // Verificar si el usuario existe
    $usuarioExistente = selectOne($conn, "SELECT id FROM usuarios WHERE id = ?", [$id]);
    if (!$usuarioExistente) {
        echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar si el email ya existe en otro usuario
    $emailExistente = selectOne($conn, "SELECT id FROM usuarios WHERE email = ? AND id != ?", [$email, $id]);
    if ($emailExistente) {
        echo json_encode(['success' => false, 'error' => 'El email ya está registrado por otro usuario']);
        exit;
    }
    
    // Actualizar usuario
    if (!empty($password)) {
        // Si se proporciona contraseña, actualizar con ella
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, password = ?, rol = ? WHERE id = ?";
        $params = [$nombre, $email, $password_hash, $rol, $id];
    } else {
        // Si no se proporciona contraseña, mantener la actual
        $sql = "UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?";
        $params = [$nombre, $email, $rol, $id];
    }
    
    $affected = executeUpdate($conn, $sql, $params);
    
    if ($affected > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente'
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'error' => 'No se realizaron cambios'
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