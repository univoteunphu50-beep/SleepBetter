<?php
session_start();

echo "<h2>Prueba de Exportación PDF - Módulo de Productos</h2>";

// Verificar autenticación
if (!isset($_SESSION['usuario_id'])) {
    echo "<p style='color: red;'>❌ No estás autenticado</p>";
    echo "<p><a href='login.php'>Ir al Login</a></p>";
    exit;
}

echo "<p style='color: green;'>✅ Usuario autenticado: " . $_SESSION['usuario_nombre'] . "</p>";

// Verificar conexión a la base de datos
include("conexion.php");

if ($conn->connect_error) {
    echo "<p style='color: red;'>❌ Error de conexión: " . $conn->connect_error . "</p>";
    exit;
}

echo "<p style='color: green;'>✅ Conexión exitosa</p>";

// Verificar tabla productos
$result = $conn->query("SHOW TABLES LIKE 'productos'");
if ($result->num_rows === 0) {
    echo "<p style='color: red;'>❌ La tabla 'productos' NO existe</p>";
    echo "<p><a href='diagnostico_productos.php'>Ejecutar diagnóstico</a></p>";
    exit;
}

echo "<p style='color: green;'>✅ Tabla 'productos' existe</p>";

// Contar productos
$result = $conn->query("SELECT COUNT(*) as total FROM productos");
$count = $result->fetch_assoc()['total'];
echo "<p><strong>Total de productos en la base de datos:</strong> $count</p>";

if ($count === 0) {
    echo "<p style='color: orange;'>⚠️ No hay productos para exportar</p>";
    echo "<p><a href='productos/index.html'>Ir al módulo de productos para crear algunos</a></p>";
} else {
    echo "<p style='color: green;'>✅ Hay productos para exportar</p>";
    
    // Mostrar algunos productos de ejemplo
    echo "<h3>Productos de ejemplo:</h3>";
    $result = $conn->query("SELECT id, nombre, stock, precio, con_lote FROM productos ORDER BY id DESC LIMIT 5");
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Stock</th><th>Precio</th><th>Con Lote</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['nombre'] . "</td>";
        echo "<td>" . $row['stock'] . "</td>";
        echo "<td>$" . number_format($row['precio'], 2) . "</td>";
        echo "<td>" . ($row['con_lote'] ? 'Sí' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

$conn->close();

// Verificar archivos de exportación
echo "<h3>Verificando archivos de exportación:</h3>";

$files = [
    'productos/exportar_pdf.php' => 'Exportador PDF Simple',
    'productos/exportar_pdf_avanzado.php' => 'Exportador PDF Avanzado',
    'TCPDF-main/tcpdf.php' => 'Librería TCPDF'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $description ($file)</p>";
    } else {
        echo "<p style='color: red;'>❌ $description ($file) - NO EXISTE</p>";
    }
}

// Enlaces de prueba
echo "<h3>Enlaces de prueba:</h3>";
echo "<ul>";
echo "<li><a href='productos/exportar_pdf_avanzado.php' target='_blank'>📄 Exportar Catálogo PDF Avanzado</a></li>";
echo "<li><a href='productos/exportar_pdf.php' target='_blank'>📄 Exportar Lista PDF Simple</a></li>";
echo "<li><a href='productos/index.html' target='_blank'>🏪 Ir al módulo de productos</a></li>";
echo "<li><a href='index.php'>🏠 Ir al Dashboard</a></li>";
echo "</ul>";

// Estadísticas adicionales
echo "<h3>Estadísticas del inventario:</h3>";

include("conexion.php");
$sql_stats = "SELECT 
    COUNT(*) as total_productos,
    COUNT(CASE WHEN con_lote = 1 THEN 1 END) as productos_lotes,
    COUNT(CASE WHEN stock <= restock THEN 1 END) as stock_bajo,
    SUM(stock * precio) as valor_total,
    AVG(precio) as precio_promedio
FROM productos";
$result_stats = $conn->query($sql_stats);
$stats = $result_stats->fetch_assoc();

echo "<ul>";
echo "<li><strong>Total de productos:</strong> " . $stats['total_productos'] . "</li>";
echo "<li><strong>Productos con control de lotes:</strong> " . $stats['productos_lotes'] . "</li>";
echo "<li><strong>Productos con stock bajo:</strong> " . $stats['stock_bajo'] . "</li>";
echo "<li><strong>Valor total del inventario:</strong> $" . number_format($stats['valor_total'], 2) . "</li>";
echo "<li><strong>Precio promedio:</strong> $" . number_format($stats['precio_promedio'], 2) . "</li>";
echo "</ul>";

$conn->close();

// Instrucciones
echo "<h3>Instrucciones para probar:</h3>";
echo "<ol>";
echo "<li>Haz clic en 'Exportar Catálogo PDF Avanzado' para generar el catálogo completo</li>";
echo "<li>El PDF incluirá: lista completa, productos con lotes, productos con stock bajo y estadísticas</li>";
echo "<li>El archivo se descargará automáticamente con fecha y hora en el nombre</li>";
echo "<li>Si hay errores, verifica que TCPDF esté instalado correctamente</li>";
echo "</ol>";

echo "<h3>Características del catálogo avanzado:</h3>";
echo "<ul>";
echo "<li>📊 Resumen del inventario con estadísticas</li>";
echo "<li>📋 Lista completa de productos con colores según stock</li>";
echo "<li>🏷️ Sección especial para productos con control de lotes</li>";
echo "<li>⚠️ Alerta de productos con stock bajo</li>";
echo "<li>📄 Múltiples páginas automáticas</li>";
echo "<li>🎨 Diseño profesional con colores</li>";
echo "</ul>";
?> 