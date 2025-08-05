<?php
header('Content-Type: application/json; charset=utf-8');
include("../conexion.php");

$sql = "SELECT * FROM clientes ORDER BY cliente ASC";
$resultado = $conn->query($sql);

$clientes = [];

while ($fila = $resultado->fetch_assoc()) {
    $clientes[] = $fila;
}

echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
$conn->close();
?>

