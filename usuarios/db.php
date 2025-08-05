<?php
// Configuración de base de datos para el módulo de usuarios
// Optimizada para Docker - Versión Docker-first

function getDbConnection() {
    // Configuración específica para Docker
    $host = getenv('MYSQL_HOST') ?: 'mysql';
    $dbname = getenv('MYSQL_DATABASE') ?: 'sleepbetter_db';
    $username = getenv('MYSQL_USER') ?: 'sleepbetter';
    $password = getenv('MYSQL_PASSWORD') ?: 'sleepbetter123';
    
    // Detectar si estamos en Docker
    $isDocker = getenv('MYSQL_HOST') !== false || file_exists('/.dockerenv');
    
    if (!$isDocker) {
        // Fallback para desarrollo local (XAMPP)
        $host = 'localhost';
        $dbname = 'sleepbetter_db';
        $username = 'root';
        $password = '';
    }
    
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión Docker: " . $e->getMessage());
        throw new Exception("Error de conexión a la base de datos");
    }
}

function crearTablaUsuarios($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario VARCHAR(50) UNIQUE NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        rol ENUM('admin', 'vendedor', 'usuario') DEFAULT 'usuario',
        activo TINYINT(1) DEFAULT 1,
        fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Error creando tabla usuarios: " . $e->getMessage());
        return false;
    }
}

function insertarUsuarioAdmin($pdo) {
    // Verificar si ya existe un usuario admin
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE rol = 'admin'");
    $stmt->execute();
    
    if ($stmt->fetchColumn() == 0) {
        $sql = "INSERT INTO usuarios (usuario, nombre, email, password, rol, activo) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt->execute(['admin', 'Administrador', 'admin@sleepbetter.com', $password_hash, 'admin', 1]);
            return true;
        } catch (PDOException $e) {
            error_log("Error insertando usuario admin: " . $e->getMessage());
            return false;
        }
    }
    return true;
}
?> 