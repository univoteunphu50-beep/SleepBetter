<?php
include("conexion.php");

echo "<h2>Actualizando estructura de la tabla productos</h2>";

// Verificar si la tabla existe
$result = $conn->query("SHOW TABLES LIKE 'productos'");
if ($result->num_rows === 0) {
    echo "<p style='color: red;'>❌ La tabla 'productos' NO existe</p>";
    echo "<p><a href='crear_tabla_productos.sql'>Crear tabla productos</a></p>";
    exit;
}

echo "<p style='color: green;'>✅ La tabla 'productos' existe</p>";

// Mostrar estructura actual
echo "<h3>Estructura actual de la tabla:</h3>";
$result = $conn->query("DESCRIBE productos");
echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th><th>Extra</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['Field'] . "</td>";
    echo "<td>" . $row['Type'] . "</td>";
    echo "<td>" . $row['Null'] . "</td>";
    echo "<td>" . $row['Key'] . "</td>";
    echo "<td>" . $row['Default'] . "</td>";
    echo "<td>" . $row['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Columnas que deben existir
$columnas_requeridas = [
    'con_lote' => 'BOOLEAN DEFAULT 0',
    'numero_lote' => 'VARCHAR(50)',
    'fecha_fabricacion' => 'DATE',
    'fecha_expiracion' => 'DATE',
    'temperatura_almacenamiento' => 'VARCHAR(100)',
    'condiciones_especiales' => 'TEXT'
];

// Obtener columnas existentes
$result = $conn->query("SHOW COLUMNS FROM productos");
$columnas_existentes = [];
while ($row = $result->fetch_assoc()) {
    $columnas_existentes[] = $row['Field'];
}

echo "<h3>Verificando columnas faltantes:</h3>";

$columnas_agregadas = 0;

foreach ($columnas_requeridas as $columna => $tipo) {
    if (!in_array($columna, $columnas_existentes)) {
        echo "<p style='color: orange;'>⚠️ Agregando columna '$columna'...</p>";
        
        $sql = "ALTER TABLE productos ADD COLUMN $columna $tipo";
        
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>✅ Columna '$columna' agregada exitosamente</p>";
            $columnas_agregadas++;
        } else {
            echo "<p style='color: red;'>❌ Error al agregar columna '$columna': " . $conn->error . "</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ Columna '$columna' ya existe</p>";
    }
}

// Verificar si hay columnas antiguas que necesiten renombrarse
if (in_array('expiracion', $columnas_existentes) && !in_array('fecha_expiracion', $columnas_existentes)) {
    echo "<p style='color: orange;'>⚠️ Renombrando columna 'expiracion' a 'fecha_expiracion'...</p>";
    
    // Crear nueva columna y copiar datos
    $sql = "ALTER TABLE productos ADD COLUMN fecha_expiracion DATE";
    if ($conn->query($sql) === TRUE) {
        $sql = "UPDATE productos SET fecha_expiracion = expiracion WHERE expiracion IS NOT NULL";
        if ($conn->query($sql) === TRUE) {
            $sql = "ALTER TABLE productos DROP COLUMN expiracion";
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>✅ Columna 'expiracion' renombrada a 'fecha_expiracion'</p>";
            }
        }
    }
}

if (in_array('lote', $columnas_existentes) && !in_array('numero_lote', $columnas_existentes)) {
    echo "<p style='color: orange;'>⚠️ Renombrando columna 'lote' a 'numero_lote'...</p>";
    
    // Crear nueva columna y copiar datos
    $sql = "ALTER TABLE productos ADD COLUMN numero_lote VARCHAR(50)";
    if ($conn->query($sql) === TRUE) {
        $sql = "UPDATE productos SET numero_lote = lote WHERE lote IS NOT NULL";
        if ($conn->query($sql) === TRUE) {
            $sql = "ALTER TABLE productos DROP COLUMN lote";
            if ($conn->query($sql) === TRUE) {
                echo "<p style='color: green;'>✅ Columna 'lote' renombrada a 'numero_lote'</p>";
            }
        }
    }
}

if ($columnas_agregadas > 0) {
    echo "<h3>Estructura actualizada:</h3>";
    $result = $conn->query("DESCRIBE productos");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Llave</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: green;'>✅ La tabla ya tiene todas las columnas necesarias</p>";
}

$conn->close();

echo "<h3>Enlaces:</h3>";
echo "<p><a href='productos/index.html'>Ir al módulo de productos</a></p>";
echo "<p><a href='index.php'>Ir al Dashboard</a></p>";
?> 