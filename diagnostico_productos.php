<?php
include("conexion.php");

echo "<h2>Diagnóstico de la tabla productos</h2>";

// Verificar conexión
if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $conn->connect_error . "</p>";
    exit;
}

echo "<p style='color: green;'>✅ Conexión exitosa</p>";

// Verificar si la tabla existe
$result = $conn->query("SHOW TABLES LIKE 'productos'");
if ($result->num_rows === 0) {
    echo "<p style='color: red;'>❌ La tabla 'productos' NO existe</p>";
    echo "<h3>Soluciones:</h3>";
    echo "<ol>";
    echo "<li><a href='crear_tabla_productos_completa.sql'>Ejecutar script completo de creación</a></li>";
    echo "<li><a href='crear_tabla_productos.sql'>Ejecutar script básico de creación</a></li>";
    echo "</ol>";
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

// Verificar columnas requeridas
$columnas_requeridas = [
    'con_lote',
    'numero_lote', 
    'fecha_fabricacion',
    'fecha_expiracion',
    'temperatura_almacenamiento',
    'condiciones_especiales'
];

$result = $conn->query("SHOW COLUMNS FROM productos");
$columnas_existentes = [];
while ($row = $result->fetch_assoc()) {
    $columnas_existentes[] = $row['Field'];
}

echo "<h3>Verificación de columnas requeridas:</h3>";

$columnas_faltantes = [];
foreach ($columnas_requeridas as $columna) {
    if (!in_array($columna, $columnas_existentes)) {
        echo "<p style='color: red;'>❌ Falta columna: '$columna'</p>";
        $columnas_faltantes[] = $columna;
    } else {
        echo "<p style='color: green;'>✅ Columna '$columna' existe</p>";
    }
}

if (count($columnas_faltantes) > 0) {
    echo "<h3>Problema identificado:</h3>";
    echo "<p style='color: red;'>❌ Faltan las siguientes columnas: " . implode(', ', $columnas_faltantes) . "</p>";
    echo "<p>Esto causa el error al intentar crear productos.</p>";
    
    echo "<h3>Soluciones:</h3>";
    echo "<ol>";
    echo "<li><a href='actualizar_estructura_productos.php'>Ejecutar actualización automática</a></li>";
    echo "<li><a href='actualizar_tabla_productos.sql'>Ejecutar script SQL manual</a></li>";
    echo "</ol>";
} else {
    echo "<p style='color: green;'>✅ Todas las columnas requeridas existen</p>";
}

// Verificar si hay columnas antiguas que puedan causar conflictos
$columnas_antiguas = ['expiracion', 'lote', 'tiene_lote'];
$conflictos = [];

foreach ($columnas_antiguas as $columna) {
    if (in_array($columna, $columnas_existentes)) {
        echo "<p style='color: orange;'>⚠️ Columna antigua encontrada: '$columna'</p>";
        $conflictos[] = $columna;
    }
}

if (count($conflictos) > 0) {
    echo "<h3>Columnas antiguas detectadas:</h3>";
    echo "<p>Estas columnas pueden causar conflictos. Se recomienda actualizarlas.</p>";
    echo "<p><a href='actualizar_estructura_productos.php'>Actualizar estructura</a></p>";
}

// Contar productos existentes
$result = $conn->query("SELECT COUNT(*) as total FROM productos");
$count = $result->fetch_assoc()['total'];
echo "<p><strong>Total de productos en la base de datos:</strong> $count</p>";

$conn->close();

echo "<h3>Enlaces útiles:</h3>";
echo "<ul>";
echo "<li><a href='actualizar_estructura_productos.php'>Actualizar estructura automáticamente</a></li>";
echo "<li><a href='crear_tabla_productos_completa.sql'>Script completo de creación</a></li>";
echo "<li><a href='productos/index.html'>Ir al módulo de productos</a></li>";
echo "<li><a href='index.php'>Ir al Dashboard</a></li>";
echo "</ul>";

echo "<h3>Instrucciones para tu amigo:</h3>";
echo "<ol>";
echo "<li>Ejecuta este diagnóstico primero</li>";
echo "<li>Si faltan columnas, ejecuta 'actualizar_estructura_productos.php'</li>";
echo "<li>Si la tabla no existe, ejecuta 'crear_tabla_productos_completa.sql'</li>";
echo "<li>Prueba crear un producto después de la actualización</li>";
echo "</ol>";
?> 