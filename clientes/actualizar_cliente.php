<?php
include("../conexion.php");

$data = json_decode(file_get_contents("php://input"), true);

$cedula = $data["cedula"];
$cliente = $data["nombre"]; // El campo se llama 'nombre' en el formulario pero 'cliente' en la BD
$telefono = $data["telefono"];
$email = $data["email"];
$direccion = $data["direccion"];
$comentarios = $data["comentarios"];

$sql = "UPDATE clientes SET cliente=?, telefono=?, email=?, direccion=?, comentarios=? WHERE cedula=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $cliente, $telefono, $email, $direccion, $comentarios, $cedula);

if ($stmt->execute()) {
  echo "Cliente actualizado correctamente.";
} else {
  echo "Error al actualizar cliente.";
}
?>
