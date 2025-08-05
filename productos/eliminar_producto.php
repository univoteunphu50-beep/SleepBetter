
<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Buscar el siguiente producto antes de eliminar el actual
    $res = $conn->query("SELECT id FROM productos WHERE id > $id ORDER BY id ASC LIMIT 1");
    $siguiente_id = null;
    if ($res && $row = $res->fetch_assoc()) {
        $siguiente_id = $row['id'];
    } else {
        // Si no hay siguiente, buscar el anterior
        $res = $conn->query("SELECT id FROM productos WHERE id < $id ORDER BY id DESC LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            $siguiente_id = $row['id'];
        }
    }

    // Eliminar imagen asociada si existe
    $res = $conn->query("SELECT imagen FROM productos WHERE id = $id");
    if ($res && $row = $res->fetch_assoc()) {
        $imagen = $row['imagen'];
        if ($imagen && file_exists($imagen)) {
            unlink($imagen); // Borra el archivo de imagen
        }
    }

    // Eliminar producto
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $redir = "Location: index.html?mensaje=Producto eliminado&tipo=success";
        if ($siguiente_id) {
            $redir .= "&resaltar_id=" . $siguiente_id;
        }
        header($redir);
        exit();
    } else {
        echo "Error al eliminar: " . $stmt->error;
    }
} else {
    echo "ID no especificado.";
}
?>
