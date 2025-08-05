<?php
header('Content-Type: application/json');

// Conexión a la base de datos
include("../conexion.php");

// Consulta para obtener todos los clientes
$sql = "SELECT cedula, cliente, telefono, email, direccion FROM clientes";
$resultado = $conn->query($sql);

$clientes = [];

while ($fila = $resultado->fetch_assoc()) {
    $clientes[] = $fila;
}

// Retornar los datos como JSON
echo json_encode($clientes);

$conn->close();
?>
