<?php
// Archivo de prueba para diagnosticar problemas con usuarios
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== PRUEBA DE GESTIÓN DE USUARIOS ===\n";

// 1. Probar conexión a la base de datos
echo "1. Probando conexión a la base de datos...\n";
include("conexion.php");
if ($conn) {
    echo "✅ Conexión exitosa\n";
} else {
    echo "❌ Error de conexión\n";
    exit;
}

// 2. Probar consulta de usuarios
echo "2. Probando consulta de usuarios...\n";
$sql = "SELECT id, usuario, nombre, email, rol, activo, fecha_creacion FROM usuarios LIMIT 5";
$result = $conn->query($sql);

if ($result) {
    echo "✅ Consulta exitosa\n";
    $count = $result->num_rows;
    echo "📊 Usuarios encontrados: $count\n";
    
    while ($row = $result->fetch_assoc()) {
        echo "   - ID: {$row['id']}, Usuario: {$row['usuario']}, Rol: {$row['rol']}\n";
    }
} else {
    echo "❌ Error en consulta: " . $conn->error . "\n";
}

// 3. Probar auth_check.php
echo "3. Probando auth_check.php...\n";
if (file_exists("auth_check.php")) {
    echo "✅ Archivo auth_check.php existe\n";
    
    // Simular sesión
    session_start();
    $_SESSION['usuario_id'] = 1;
    $_SESSION['usuario_nombre'] = 'Admin';
    $_SESSION['usuario_rol'] = 'admin';
    $_SESSION['usuario_email'] = 'admin@test.com';
    
    include("auth_check.php");
    echo "✅ auth_check.php cargado correctamente\n";
    
    // Probar función hasPermission
    if (function_exists('hasPermission')) {
        $hasAdmin = hasPermission('admin');
        echo "✅ Función hasPermission existe, admin: " . ($hasAdmin ? 'SÍ' : 'NO') . "\n";
    } else {
        echo "❌ Función hasPermission no existe\n";
    }
} else {
    echo "❌ Archivo auth_check.php no existe\n";
}

// 4. Probar listar_usuarios.php
echo "4. Probando listar_usuarios.php...\n";
ob_start();
include("usuarios/listar_usuarios.php");
$output = ob_get_clean();

echo "📄 Salida de listar_usuarios.php:\n";
echo substr($output, 0, 500) . "\n";

$conn->close();
echo "=== FIN DE PRUEBA ===\n";
?> 