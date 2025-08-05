
<?php
// Archivo: eliminar_producto.php
// Elimina un producto de la base de datos

include("../conexion.php");
include("../db_helper.php");

// Obtener ID del producto (POST o GET)
$id = null;
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
} elseif (isset($_GET['id'])) {
    $id = intval($_GET['id']);
}

if (!$id) {
    echo "ID del producto no especificado.";
    exit();
}

try {
    // Verificar que el producto existe
    $sql_verificar = "SELECT id, nombre, imagen FROM productos WHERE id = ?";
    $producto = selectOne($conn, $sql_verificar, [$id]);
    
    if (!$producto) {
        echo "Producto no encontrado.";
        exit();
    }
    
    // Eliminar imagen asociada si existe
    if (!empty($producto['imagen'])) {
        $ruta_imagen = $producto['imagen'];
        if (file_exists($ruta_imagen)) {
            unlink($ruta_imagen);
        }
    }
    
    // Eliminar el producto
    $sql_eliminar = "DELETE FROM productos WHERE id = ?";
    $resultado = executeUpdate($conn, $sql_eliminar, [$id]);
    
    if ($resultado) {
        echo "Producto '" . $producto['nombre'] . "' eliminado exitosamente.";
    } else {
        echo "Error al eliminar el producto.";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

closeConnection($conn);
?>
