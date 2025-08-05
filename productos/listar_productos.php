<?php
include '../conexion.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT * FROM productos");
$productos = [];
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}
echo json_encode($productos);
?>
