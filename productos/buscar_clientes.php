<?php
include 'db.php';

$busqueda = $_GET['q'] ?? '';
$busqueda = "%" . $conn->real_escape_string($busqueda) . "%";

$sql = "SELECT * FROM clientes WHERE 
    cedula LIKE ? OR 
    nombre LIKE ? OR 
    telefono LIKE ? OR 
    email LIKE ? OR 
    direccion LIKE ? OR 
    comentarios LIKE ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $busqueda, $busqueda, $busqueda, $busqueda, $busqueda, $busqueda);
$stmt->execute();
$result = $stmt->get_result();

$clientes = [];
while ($row = $result->fetch_assoc()) {
    $clientes[] = $row;
}
echo json_encode($clientes);
?>
