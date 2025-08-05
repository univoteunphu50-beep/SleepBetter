<?php
// Archivo: conexion.php
// Configuración automática para Docker, Render y desarrollo local

// Detectar si estamos en Render (PostgreSQL)
$isRender = getenv('DB_HOST') !== false;
// Detectar si estamos en Docker (MySQL)
$isDocker = getenv('MYSQL_HOST') !== false;

if ($isRender) {
    // Configuración para Render (PostgreSQL)
    $host = getenv('DB_HOST') ?: 'localhost';
    $user = getenv('DB_USER') ?: 'postgres';
    $pass = getenv('DB_PASSWORD') ?: '';
    $db = getenv('DB_NAME') ?: 'sleepbetter_db';
    $port = getenv('DB_PORT') ?: '5432';
    
    // Usar PDO para PostgreSQL
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db";
        $conn = new PDO($dsn, $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Conexión PostgreSQL fallida: " . $e->getMessage());
    }
    
} elseif ($isDocker) {
    // Configuración para Docker (MySQL)
    $host = getenv('MYSQL_HOST') ?: 'mysql';
    $user = getenv('MYSQL_USER') ?: 'sleepbetter';
    $pass = getenv('MYSQL_PASSWORD') ?: 'sleepbetter123';
    $db = getenv('MYSQL_DATABASE') ?: 'sleepbetter_db';
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Verifica si hay error de conexión
    if ($conn->connect_error) {
        die("Conexión MySQL fallida: " . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset("utf8mb4");
    
} else {
    // Configuración para desarrollo local (XAMPP - MySQL)
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "sleepbetter_db";
    
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Verifica si hay error de conexión
    if ($conn->connect_error) {
        die("Conexión MySQL fallida: " . $conn->connect_error);
    }
    
    // Configurar charset
    $conn->set_charset("utf8mb4");
}
?>
