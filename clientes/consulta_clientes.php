<?php
header('Content-Type: application/json; charset=utf-8');
include("../conexion.php");
include("../db_helper.php");

try {
    $clientes = selectAll($conn, "SELECT * FROM clientes ORDER BY cliente ASC");
    echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

closeConnection($conn);
?>

