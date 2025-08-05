<?php
// Archivo: conexion_temp.php
// Configuración temporal para debugging en Render

// Detectar si estamos en Render
$isRender = getenv('PORT') !== false;

if ($isRender) {
    // En Render, mostrar información de debugging
    echo "<h2>🔍 Información de Debugging en Render</h2>";
    echo "<p><strong>Variables de entorno detectadas:</strong></p>";
    echo "<ul>";
    echo "<li>PORT: " . (getenv('PORT') ?: 'No definido') . "</li>";
    echo "<li>DB_HOST: " . (getenv('DB_HOST') ?: 'No definido') . "</li>";
    echo "<li>DB_PORT: " . (getenv('DB_PORT') ?: 'No definido') . "</li>";
    echo "<li>DB_NAME: " . (getenv('DB_NAME') ?: 'No definido') . "</li>";
    echo "<li>DB_USER: " . (getenv('DB_USER') ?: 'No definido') . "</li>";
    echo "<li>DB_PASSWORD: " . (getenv('DB_PASSWORD') ? 'Definido' : 'No definido') . "</li>";
    echo "</ul>";
    
    echo "<p><strong>Estado de la aplicación:</strong> ✅ Desplegada correctamente en Render</p>";
    echo "<p><strong>Próximo paso:</strong> Configurar base de datos PostgreSQL</p>";
    
    // Crear una conexión simulada para evitar errores
    $conn = null;
    
} else {
    // Configuración para desarrollo local
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "sleepbetter_db";
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Conexión MySQL fallida: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
}
?> 