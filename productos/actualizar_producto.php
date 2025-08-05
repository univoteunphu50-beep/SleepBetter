<?php
include('../conexion.php');

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$stock = $_POST['stock'];
$restock = $_POST['restock'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
$comentarios = $_POST['comentarios'];
$keywords = $_POST['keywords'];

// Campos de lote y expiración
$numero_lote = isset($_POST['numero_lote']) ? $_POST['numero_lote'] : '';
$fecha_fabricacion = isset($_POST['fecha_fabricacion']) && $_POST['fecha_fabricacion'] != "" ? $_POST['fecha_fabricacion'] : null;
$fecha_expiracion = isset($_POST['fecha_expiracion']) && $_POST['fecha_expiracion'] != "" ? $_POST['fecha_expiracion'] : null;
$temperatura_almacenamiento = isset($_POST['temperatura_almacenamiento']) ? $_POST['temperatura_almacenamiento'] : '';
$condiciones_especiales = isset($_POST['condiciones_especiales']) ? $_POST['condiciones_especiales'] : '';

// Subida de imagen nueva si se selecciona
$imagen = '';
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $imagen = $_FILES['imagen']['name'];
    $ruta = "imagenes/" . $imagen;
    move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta);
}

// Construir la consulta SQL dinámicamente para manejar NULL correctamente
$fecha_fabricacion_sql = $fecha_fabricacion ? "'" . $conn->real_escape_string($fecha_fabricacion) . "'" : "NULL";
$fecha_expiracion_sql = $fecha_expiracion ? "'" . $conn->real_escape_string($fecha_expiracion) . "'" : "NULL";

$sql = "UPDATE productos SET
        nombre=?,
        stock=?,
        restock=?,
        precio=?,
        costo=?,
        comentarios=?,
        palabras_clave=?,
        numero_lote=?,
        fecha_fabricacion=$fecha_fabricacion_sql,
        fecha_expiracion=$fecha_expiracion_sql,
        temperatura_almacenamiento=?,
        condiciones_especiales=?";

if ($imagen !== '') {
    $sql .= ", imagen=?";
}

$sql .= " WHERE id=?";

$stmt = $conn->prepare($sql);

if ($imagen !== '') {
    $stmt->bind_param("sddddsssssss", $nombre, $stock, $restock, $precio, $costo, $comentarios, $keywords, $numero_lote, $temperatura_almacenamiento, $condiciones_especiales, $imagen, $id);
} else {
    $stmt->bind_param("sddddssssss", $nombre, $stock, $restock, $precio, $costo, $comentarios, $keywords, $numero_lote, $temperatura_almacenamiento, $condiciones_especiales, $id);
}

if ($stmt->execute()) {
    // Mostrar popup de confirmación y luego redirigir
    echo "<script>
        alert('✅ Producto actualizado correctamente');
        window.location.href='index.html';
    </script>";
} else {
    echo "<script>
        alert('❌ Error al actualizar producto: " . $stmt->error . "');
        window.history.back();
    </script>";
}

$stmt->close();
$conn->close();
?>
