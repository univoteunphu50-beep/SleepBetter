<?php
include("../conexion.php");
include("../db_helper.php");

$data = json_decode(file_get_contents("php://input"), true);

$cedula = $data["cedula"];
$cliente = $data["nombre"]; // El campo se llama 'nombre' en el formulario pero 'cliente' en la BD
$telefono = $data["telefono"];
$email = $data["email"];
$direccion = $data["direccion"];
$comentarios = $data["comentarios"];

try {
    $sql = "UPDATE clientes SET cliente=?, telefono=?, email=?, direccion=?, comentarios=? WHERE cedula=?";
    $params = [$cliente, $telefono, $email, $direccion, $comentarios, $cedula];
    
    $affected = executeUpdate($conn, $sql, $params);
    
    if ($affected > 0) {
        echo "Cliente actualizado correctamente.";
    } else {
        echo "Error al actualizar cliente: No se encontró el cliente o no se realizaron cambios.";
    }
} catch (Exception $e) {
    echo "Error al actualizar cliente: " . $e->getMessage();
}

closeConnection($conn);
?>
