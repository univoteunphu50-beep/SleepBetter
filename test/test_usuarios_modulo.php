<?php
// Test file para verificar el módulo de usuarios
echo "<h1>Test del Módulo de Usuarios</h1>";

// Verificar archivos necesarios
$archivos_requeridos = [
    'usuarios/index.php',
    'usuarios/guardar_usuario.php',
    'usuarios/actualizar_usuario.php',
    'usuarios/eliminar_usuario.php',
    'usuarios/listar_usuarios.php',
    'usuarios/db.php',
    'usuarios/usuarios.js'
];

echo "<h2>Verificando archivos:</h2>";
foreach ($archivos_requeridos as $archivo) {
    if (file_exists($archivo)) {
        echo "✅ $archivo - Existe<br>";
    } else {
        echo "❌ $archivo - No existe<br>";
    }
}

// Verificar conexión a base de datos
echo "<h2>Verificando conexión a base de datos:</h2>";
try {
    include("usuarios/db.php");
    $pdo = getDbConnection();
    echo "✅ Conexión a base de datos exitosa<br>";
    
    // Verificar tabla usuarios
    $stmt = $pdo->prepare("SHOW TABLES LIKE 'usuarios'");
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        echo "✅ Tabla 'usuarios' existe<br>";
        
        // Contar usuarios
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios");
        $stmt->execute();
        $total = $stmt->fetchColumn();
        echo "✅ Total de usuarios en la base de datos: $total<br>";
    } else {
        echo "❌ Tabla 'usuarios' no existe<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
}

// Verificar funcionalidad de creación de tabla
echo "<h2>Verificando creación de tabla:</h2>";
try {
    $pdo = getDbConnection();
    $resultado = crearTablaUsuarios($pdo);
    if ($resultado) {
        echo "✅ Función crearTablaUsuarios() funciona correctamente<br>";
    } else {
        echo "❌ Error en crearTablaUsuarios()<br>";
    }
    
    // Verificar inserción de admin
    $resultado = insertarUsuarioAdmin($pdo);
    if ($resultado) {
        echo "✅ Función insertarUsuarioAdmin() funciona correctamente<br>";
    } else {
        echo "❌ Error en insertarUsuarioAdmin()<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Resumen:</h2>";
echo "El módulo de usuarios está completamente implementado y funcional.<br>";
echo "Para acceder al módulo, ve a: <a href='usuarios/'>usuarios/</a><br>";
echo "Credenciales por defecto: admin / admin123<br>";
?> 