<?php
// Archivo: conexion.php
// Configuración automática para Docker y desarrollo local

// Detectar si estamos en Docker
$isDocker = getenv('MYSQL_HOST') !== false;

if ($isDocker) {
    // Configuración para Docker
    $host = getenv('MYSQL_HOST') ?: 'mysql';
    $user = getenv('MYSQL_USER') ?: 'sleepbetter';
    $pass = getenv('MYSQL_PASSWORD') ?: 'sleepbetter123';
    $db = getenv('MYSQL_DATABASE') ?: 'sleepbetter_db';
} else {
    // Configuración para desarrollo local (XAMPP)
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db = "sleepbetter_db";
}

$conn = new mysqli($host, $user, $pass, $db);

// Verifica si hay error de conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Configurar charset
$conn->set_charset("utf8mb4");
?>
