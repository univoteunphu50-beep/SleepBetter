<?php
include '../conexion.php';

$codigo = $_POST['codigo'] ?? '';
$cedula = $_POST['cedula'] ?? '';

if ($codigo !== '1211') {
    header("Location: index.html?mensaje=Código de seguridad incorrecto&tipo=error");
    exit();
}

$stmt = $conn->prepare("DELETE FROM clientes WHERE cedula = ?");
$stmt->bind_param("s", $cedula);
if ($stmt->execute()) {
    header("Location: index.html?mensaje=Cliente eliminado correctamente&tipo=success");
} else {
    header("Location: index.html?mensaje=Error al eliminar cliente&tipo=error");
}
$stmt->close();
$conn->close();
exit();
?>
