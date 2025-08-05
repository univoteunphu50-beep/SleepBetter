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
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'error' => 'El email ya está registrado']);
        exit;
    }
    
    // Generar nombre de usuario único
    $usuario = strtolower(str_replace(' ', '', $nombre)) . rand(100, 999);
    
    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    while ($stmt->rowCount() > 0) {
        $usuario = strtolower(str_replace(' ', '', $nombre)) . rand(100, 999);
        $stmt->execute([$usuario]);
    }
    
    // Hash de la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insertar nuevo usuario
    $sql = "INSERT INTO usuarios (usuario, nombre, email, password, rol, activo) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usuario, $nombre, $email, $password_hash, $rol, $estado]);
    
    $id = $pdo->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'message' => 'Usuario creado exitosamente',
        'id' => $id
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Error: ' . $e->getMessage()
    ]);
}
?> 