<?php
header('Content-Type: application/json');

include '../conexion.php';
include '../db_helper.php';

try {
    $resultado = selectOne($conn, "SELECT COALESCE(MAX(id), 0) + 1 AS proximo_id FROM productos");
    $proximo_id = $resultado['proximo_id'] ?? 1;
    
    echo json_encode(["proximo_id" => $proximo_id]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?> 