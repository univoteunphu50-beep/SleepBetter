<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    include("conexion_api.php");
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }
    
    // Probar consulta simple
    $result = $conn->query("SELECT 1 as test");
    if (!$result) {
        throw new Exception("Error en consulta de prueba: " . $conn->error);
    }
    
    // Probar consulta de usuarios
    $result = $conn->query("SELECT COUNT(*) as total FROM usuarios");
    if (!$result) {
        throw new Exception("Error en consulta de usuarios: " . $conn->error);
    }
    
    $row = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'connection' => 'OK',
        'test_query' => 'OK',
        'users_count' => $row['total']
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

if (isset($conn)) {
    $conn->close();
}
?> 