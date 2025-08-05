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
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol = $_POST['rol'] ?? '';
    $estado = $_POST['estado'] ?? 1;
    
    if (empty($nombre) || empty($email) || empty($password) || empty($rol)) {
        echo json_encode(['success' => false, 'error' => 'Todos los campos son obligatorios']);
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
    
    // Verificar si el email ya existe
    $usuarioExistente = selectOne($conn, "SELECT id FROM usuarios WHERE email = ?", [$email]);
    if ($usuarioExistente) {
        echo json_encode(['success' => false, 'error' => 'El email ya está registrado']);
        exit;
    }
    
    // Generar nombre de usuario único
    $usuario = strtolower(str_replace(' ', '', $nombre)) . rand(100, 999);
    
    // Verificar si el usuario ya existe
    $usuarioExistente = selectOne($conn, "SELECT id FROM usuarios WHERE nombre = ?", [$usuario]);
    while ($usuarioExistente) {
        $usuario = strtolower(str_replace(' ', '', $nombre)) . rand(100, 999);
        $usuarioExistente = selectOne($conn, "SELECT id FROM usuarios WHERE nombre = ?", [$usuario]);
    }
    
    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar nuevo usuario
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)";
    $params = [$nombre, $email, $password_hash, $rol];
    
    $id = executeInsert($conn, $sql, $params);
    
    echo json_encode([
        'success' => true,
        'message' => 'Usuario creado exitosamente',
        'id' => $id
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

closeConnection($conn);
?> 