<?php
/**
 * Script de debug para verificar productos y próximo ID
 */

include("conexion.php");
include("db_helper.php");

echo "<h2>🔍 Debug de Productos</h2>";

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
        
        // Obtener el máximo ID
        $max_id_result = selectOne($conn, "SELECT MAX(id) as max_id FROM productos");
        $max_id = $max_id_result['max_id'] ?? 0;
        echo "<p>🔢 Máximo ID actual: " . $max_id . "</p>";
        
        // Calcular próximo ID
        $proximo_id = $max_id + 1;
        echo "<p>🔢 Próximo ID debería ser: " . $proximo_id . "</p>";
        
        // Mostrar todos los productos
        $productos = selectAll($conn, "SELECT * FROM productos ORDER BY id ASC");
        
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
        
        // Probar el endpoint de próximo ID
        echo "<h3>🧪 Test del endpoint obtener_proximo_id.php:</h3>";
        $proximo_id_test = selectOne($conn, "SELECT COALESCE(MAX(id), 0) + 1 AS proximo_id FROM productos");
        echo "<p>Próximo ID desde endpoint: " . ($proximo_id_test['proximo_id'] ?? 'ERROR') . "</p>";
        
    } else {
        echo "<p>❌ Error al verificar tabla productos</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<br><a href='productos/'>🔗 Ir a Gestión de Productos</a>";
?> 