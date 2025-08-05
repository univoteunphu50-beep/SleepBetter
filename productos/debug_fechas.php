<?php
include 'db.php';

echo "<h2>Diagnóstico de Fechas de Expiración</h2>";

// Check the Esponja product specifically
$query = "SELECT id, nombre, expiracion, DATE_FORMAT(expiracion, '%d/%m/%Y') as fecha_formateada FROM productos WHERE nombre = 'Esponja'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<h3>Producto: " . $row['nombre'] . "</h3>";
    echo "<p><strong>ID:</strong> " . $row['id'] . "</p>";
    echo "<p><strong>Fecha original:</strong> '" . $row['expiracion'] . "'</p>";
    echo "<p><strong>Fecha formateada:</strong> '" . $row['fecha_formateada'] . "'</p>";
    echo "<p><strong>¿Está vacía?</strong> " . (empty($row['expiracion']) ? 'SÍ' : 'NO') . "</p>";
    echo "<p><strong>¿Es NULL?</strong> " . (is_null($row['expiracion']) ? 'SÍ' : 'NO') . "</p>";
    echo "<p><strong>Longitud:</strong> " . strlen($row['expiracion']) . "</p>";
    echo "<p><strong>Tipo de dato:</strong> " . gettype($row['expiracion']) . "</p>";
} else {
    echo "<p>No se encontró el producto Esponja</p>";
}

echo "<h3>Todas las fechas de expiración:</h3>";
$query2 = "SELECT id, nombre, expiracion, DATE_FORMAT(expiracion, '%d/%m/%Y') as fecha_formateada FROM productos WHERE expiracion IS NOT NULL AND expiracion != '' ORDER BY id";
$result2 = $conn->query($query2);

if ($result2 && $result2->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Fecha Original</th><th>Fecha Formateada</th></tr>";
    while ($row = $result2->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>'" . $row['expiracion'] . "'</td>";
        echo "<td>'" . $row['fecha_formateada'] . "'</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No hay productos con fechas de expiración</p>";
}

echo "<h3>Estructura de la tabla:</h3>";
$describe = $conn->query("DESCRIBE productos");
if ($describe) {
    echo "<table border='1'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($col = $describe->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "<td>" . $col['Default'] . "</td>";
        echo "<td>" . $col['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?> 