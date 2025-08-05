<?php
include '../conexion.php';
include '../db_helper.php';

$busqueda = $_GET['q'] ?? '';
$busqueda = "%" . $busqueda . "%";

try {
    $sql = "SELECT * FROM clientes WHERE 
        cedula LIKE ? OR 
        cliente LIKE ? OR 
        telefono LIKE ? OR 
        email LIKE ? OR 
        direccion LIKE ? OR 
        comentarios LIKE ?
        ORDER BY cliente ASC";

    $params = [$busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda];
    $clientes = selectAll($conn, $sql, $params);

    // Devolver respuesta JSON con headers apropiados
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

closeConnection($conn);
?>
