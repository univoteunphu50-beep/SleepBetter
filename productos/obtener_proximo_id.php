<?php
header('Content-Type: application/json');

include '../conexion.php';

$resultado = $conn->query("SELECT MAX(id) + 1 AS proximo_id FROM productos");
$fila = $resultado->fetch_assoc();
$proximo_id = $fila['proximo_id'] ?? 1;

echo json_encode(["proximo_id" => $proximo_id]);
?> 