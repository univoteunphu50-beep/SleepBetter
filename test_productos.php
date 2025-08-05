<?php
/**
 * Script de prueba para verificar productos en la base de datos
 */

include("conexion.php");
include("db_helper.php");

echo "<h2>🔍 Verificación de Productos en Base de Datos</h2>";

try {
    // Verificar conexión
    if ($conn instanceof PDO) {
        echo "<p>✅ Conexión a PostgreSQL establecida</p>";
    } else {
        echo "<p>✅ Conexión a MySQL establecida</p>";
    }
    
    // Verificar si la tabla productos existe
    $sql_check = "SELECT COUNT(*) as total FROM productos";
    $result = selectOne($conn, $sql_check);
    
    if ($result) {
        echo "<p>✅ Tabla productos existe</p>";
        echo "<p>📊 Total de productos: " . $result['total'] . "</p>";
        
        // Mostrar todos los productos
        $productos = selectAll($conn, "SELECT * FROM productos ORDER BY fecha_creacion DESC");
        
        if (count($productos) > 0) {
            echo "<h3>📋 Productos en la base de datos:</h3>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr><th>ID</th><th>Nombre</th><th>Stock</th><th>Precio</th><th>Fecha Creación</th></tr>";
            
            foreach ($productos as $producto) {
                echo "<tr>";
                echo "<td>" . $producto['id'] . "</td>";
                echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
                echo "<td>" . $producto['stock'] . "</td>";
                echo "<td>" . $producto['precio'] . "</td>";
                echo "<td>" . $producto['fecha_creacion'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>⚠️ No hay productos en la base de datos</p>";
        }
        
    } else {
        echo "<p>❌ Error al verificar tabla productos</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='productos/'>🔗 Ir a Gestión de Productos</a>";
?> 