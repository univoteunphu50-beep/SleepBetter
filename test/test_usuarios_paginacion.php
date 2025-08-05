<?php
session_start();

echo "<h2>Prueba de Paginación - Módulo de Usuarios</h2>";

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: red;'>❌ No estás autenticado</p>";
    echo "<p><a href='login.php'>Ir al Login</a></p>";
    exit;
}

echo "<p style='color: green;'>✅ Usuario autenticado: " . $_SESSION['usuario_nombre'] . "</p>";

// Verificar permisos
include("auth_check.php");
$user = getCurrentUser();

if ($user['rol'] !== 'admin') {
    echo "<p style='color: red;'>❌ No tienes permisos de administrador</p>";
    echo "<p><a href='index.php'>Ir al Dashboard</a></p>";
    exit;
}

echo "<p style='color: green;'>✅ Tienes permisos de administrador</p>";

// Probar conexión y datos
include("conexion.php");

if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $conn->connect_error . "</p>";
    exit;
}

echo "<p style='color: green;'>✅ Conexión exitosa</p>";

// Verificar tabla usuarios
$result = $conn->query("SHOW TABLES LIKE 'usuarios'");
if ($result->num_rows === 0) {
    echo "<p style='color: red;'>❌ La tabla 'usuarios' NO existe</p>";
    echo "<p><a href='test_db_usuarios.php'>Crear tabla usuarios</a></p>";
    exit;
}

echo "<p style='color: green;'>✅ Tabla 'usuarios' existe</p>";

// Contar usuarios
$result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
$count = $result->fetch_assoc()['total'];
echo "<p><strong>Total de usuarios en la base de datos:</strong> $count</p>";

if ($count === 0) {
    echo "<p style='color: orange;'>⚠️ No hay usuarios en la base de datos</p>";
    echo "<p><a href='usuarios/index.php'>Ir al módulo de usuarios para crear algunos</a></p>";
} else {
    echo "<p style='color: green;'>✅ Hay usuarios para mostrar</p>";
    
    // Mostrar algunos usuarios de ejemplo
    echo "<h3>Usuarios de ejemplo:</h3>";
    $result = $conn->query("SELECT id, usuario, nombre, email, rol, activo, fecha_creacion FROM usuarios ORDER BY fecha_creacion DESC LIMIT 3");
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Rol</th><th>Activo</th><th>Fecha</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['usuario'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['rol'] . "</td>";
        echo "<td>" . $row['activo'] . "</td>";
        echo "<td>" . $row['fecha_creacion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();

// Verificar archivos del módulo
echo "<h3>Verificando archivos del módulo:</h3>";

$files = [
    'usuarios/index.php' => 'Página principal',
    'usuarios/listar_usuarios.php' => 'Listar usuarios',
    'usuarios/usuarios.js' => 'JavaScript del módulo'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $description ($file)</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - NO EXISTE</p>";
    }
}

// Enlaces de prueba
echo "<h3>Enlaces de prueba:</h3>";
echo "<p><a href='usuarios/index.php' target='_blank'>Abrir módulo de usuarios</a></p>";
echo "<p><a href='test_db_usuarios.php' target='_blank'>Verificar base de datos</a></p>";
echo "<p><a href='test_usuarios_modulo.php' target='_blank'>Prueba completa del módulo</a></p>";
echo "<p><a href='index.php'>Ir al Dashboard</a></p>";

// Instrucciones para el usuario
echo "<h3>Instrucciones para probar:</h3>";
echo "<ol>";
echo "<li>Haz clic en 'Abrir módulo de usuarios'</li>";
echo "<li>Verifica que se carguen los usuarios sin errores</li>";
echo "<li>Prueba la búsqueda escribiendo en el campo y presionando Enter</li>";
echo "<li>Si hay más de 10 usuarios, prueba la paginación</li>";
echo "<li>Verifica que los botones de Editar y Eliminar funcionen</li>";
echo "</ol>";
?> 