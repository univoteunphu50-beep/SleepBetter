<?php
include '../conexion.php';
include '../db_helper.php';
header('Content-Type: application/json');

try {
    $productos = selectAll($conn, "SELECT * FROM productos ORDER BY fecha_creacion DESC");
    echo json_encode($productos);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
