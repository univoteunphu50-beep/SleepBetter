<?php
include("../conexion.php");
include("../db_helper.php");

$nombre = $_POST['nombre'];
$stock = $_POST['stock'];
$restock = $_POST['restock'];
$precio = $_POST['precio'];
$costo = $_POST['costo'];
$comentarios = $_POST['comentarios'];
$keywords = isset($_POST['keywords']) ? $_POST['keywords'] : "";
$con_lote = isset($_POST['con_lote']) ? 1 : 0;

// Campos de lote y expiración
$numero_lote = isset($_POST['numero_lote']) ? $_POST['numero_lote'] : "";
$fecha_fabricacion = isset($_POST['fecha_fabricacion']) && $_POST['fecha_fabricacion'] != "" ? $_POST['fecha_fabricacion'] : null;
$fecha_expiracion = isset($_POST['fecha_expiracion']) && $_POST['fecha_expiracion'] != "" ? $_POST['fecha_expiracion'] : null;
$temperatura_almacenamiento = isset($_POST['temperatura_almacenamiento']) ? $_POST['temperatura_almacenamiento'] : "";
$condiciones_especiales = isset($_POST['condiciones_especiales']) ? $_POST['condiciones_especiales'] : "";

$nombreImagen = "";
if ($_FILES['imagen']['name'] != "") {
    $nombreImagen = basename($_FILES['imagen']['name']);
    $rutaDestino = "imagenes/" . $nombreImagen;

    if (!is_dir("imagenes")) {
        mkdir("imagenes", 0755, true);
    }

    move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
}

// Construir la consulta SQL dinámicamente para manejar NULL correctamente
$sql = "INSERT INTO productos (nombre, stock, restock, precio, costo, comentarios, palabras_clave, imagen, con_lote, numero_lote, fecha_fabricacion, fecha_expiracion, temperatura_almacenamiento, condiciones_especiales) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$params = [
    $nombre, 
    $stock, 
    $restock, 
    $precio, 
    $costo, 
    $comentarios, 
    $keywords, 
    $nombreImagen, 
    $con_lote, 
    $numero_lote, 
    $fecha_fabricacion, 
    $fecha_expiracion, 
    $temperatura_almacenamiento, 
    $condiciones_especiales
];

if (executeInsert($conn, $sql, $params)) {
  echo "<script>alert('✅ Producto guardado correctamente'); window.location.href='index.html';</script>";
} else {
  echo "Error al guardar el producto";
}

closeConnection($conn);
?>
