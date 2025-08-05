<?php
include("../conexion.php");

$cedula = $_POST['cedula'];
$nombre = $_POST['nombre'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$direccion = $_POST['direccion'];
$comentarios = $_POST['comentarios'];

$sql = "UPDATE clientes SET 
  nombre = ?, 
  telefono = ?, 
  email = ?, 
  direccion = ?, 
  comentarios = ?
  WHERE cedula = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $nombre, $telefono, $email, $direccion, $comentarios, $cedula);

if ($stmt->execute()) {
  echo "Cliente actualizado correctamente.";
} else {
  echo "Error al actualizar: " . $stmt->error;
}

$conn->close();
?>
