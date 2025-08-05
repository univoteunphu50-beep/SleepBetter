<?php
include 'conexion.php';

echo "=== Test de Productos ===\n";

$result = $conn->query("SELECT * FROM productos");
if ($result) {
    echo "Productos encontrados: " . $result->num_rows . "\n";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Nombre: " . $row['nombre'] . "\n";
    }
} else {
    echo "Error en la consulta: " . $conn->error . "\n";
}

$conn->close();
?> 