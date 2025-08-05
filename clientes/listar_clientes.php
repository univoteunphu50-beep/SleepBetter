<?php
header('Content-Type: application/json');

// Conexión a la base de datos
include("../conexion.php");
include("../db_helper.php");

try {
    // Consulta para obtener todos los clientes
    $sql = "SELECT cedula, cliente, telefono, email, direccion FROM clientes ORDER BY cliente ASC";
    $clientes = selectAll($conn, $sql);

    // Retornar los datos como JSON
    echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

closeConnection($conn);
?>
