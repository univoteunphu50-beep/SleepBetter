<?php
// Archivo de prueba para verificar la conexión
include("conexion.php");

echo "=== PRUEBA DE CONEXIÓN ===\n";
echo "Host: " . $host . "\n";
echo "Usuario: " . $user . "\n";
echo "Base de datos: " . $db . "\n";

if ($conn->connect_error) {
    echo "Error de conexión: " . $conn->connect_error . "\n";
} else {
    echo "Conexión exitosa!\n";
    
    // Verificar si la tabla productos existe
    $result = $conn->query("SHOW TABLES LIKE 'productos'");
    if ($result->num_rows > 0) {
        echo "La tabla 'productos' existe.\n";
        
        // Verificar las columnas
        $result = $conn->query("DESCRIBE productos");
        echo "Columnas en la tabla productos:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "La tabla 'productos' NO existe.\n";
    }
}

$conn->close();
?> 