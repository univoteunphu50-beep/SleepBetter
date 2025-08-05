<?php
include '../conexion.php';

$busqueda = $_GET['q'] ?? '';
$busqueda = "%" . $conn->real_escape_string($busqueda) . "%";

$sql = "SELECT * FROM clientes WHERE 
    cedula LIKE ? OR 
    cliente LIKE ? OR 
    telefono LIKE ? OR 
    email LIKE ? OR 
    direccion LIKE ? OR 
    comentarios LIKE ?
    ORDER BY cliente ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
$stmt->execute();
$result = $stmt->get_result();

$clientes = [];
while ($row = $result->fetch_assoc()) {
    $clientes[] = $row;
}

// Devolver respuesta JSON con headers apropiados
header('Content-Type: application/json; charset=utf-8');
echo json_encode($clientes, JSON_UNESCAPED_UNICODE);
?>
