<?php
include '../conexion.php';
include '../db_helper.php';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="catalogo_productos.xls"');
header('Cache-Control: max-age=0');

// Get column information for PostgreSQL
$existingColumns = [];
try {
    if ($conn instanceof PDO) {
        // PostgreSQL way to get column information
        $sql = "SELECT column_name FROM information_schema.columns WHERE table_name = 'productos' ORDER BY ordinal_position";
        $columns = selectAll($conn, $sql);
        foreach ($columns as $col) {
            $existingColumns[] = $col['column_name'];
        }
    } else {
        // MySQL way
        $checkColumns = $conn->query("DESCRIBE productos");
        if ($checkColumns) {
            while ($col = $checkColumns->fetch_assoc()) {
                $existingColumns[] = $col['Field'];
            }
        }
    }
} catch (Exception $e) {
    // Fallback to known columns if schema query fails
    $existingColumns = ['id', 'nombre', 'stock', 'restock', 'precio', 'costo', 'comentarios', 'palabras_clave', 'imagen', 'con_lote', 'numero_lote', 'fecha_creacion'];
}

// Build the SELECT query based on existing columns
$selectFields = [];

// Always include basic fields if they exist
if (in_array('id', $existingColumns)) {
    $selectFields[] = 'id';
}
if (in_array('nombre', $existingColumns)) {
    $selectFields[] = 'nombre';
}
if (in_array('stock', $existingColumns)) {
    $selectFields[] = 'stock';
}
if (in_array('restock', $existingColumns)) {
    $selectFields[] = 'restock';
}
if (in_array('precio', $existingColumns)) {
    $selectFields[] = 'precio';
}
if (in_array('costo', $existingColumns)) {
    $selectFields[] = 'costo';
}
if (in_array('comentarios', $existingColumns)) {
    $selectFields[] = 'comentarios';
}
if (in_array('palabras_clave', $existingColumns)) {
    $selectFields[] = 'palabras_clave';
}
if (in_array('imagen', $existingColumns)) {
    $selectFields[] = 'imagen';
}
if (in_array('numero_lote', $existingColumns)) {
    $selectFields[] = 'numero_lote';
}
if (in_array('con_lote', $existingColumns)) {
    $selectFields[] = 'con_lote';
}

// Build the query
$query = "SELECT " . implode(', ', $selectFields) . " FROM productos ORDER BY id";
$productos = selectAll($conn, $query);

// Start HTML table that Excel can read
echo '<!DOCTYPE html>';
echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<style>';
echo 'table { border-collapse: collapse; width: 100%; }';
echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
echo 'th { background-color: #f2f2f2; font-weight: bold; }';
echo '</style>';
echo '</head>';
echo '<body>';

// Create table
echo '<table>';
echo '<tr>';

// Write headers based on existing columns
if (in_array('id', $existingColumns)) {
    echo '<th>ID</th>';
}
if (in_array('nombre', $existingColumns)) {
    echo '<th>Nombre</th>';
}
if (in_array('stock', $existingColumns)) {
    echo '<th>Stock</th>';
}
if (in_array('restock', $existingColumns)) {
    echo '<th>Restock</th>';
}
if (in_array('precio', $existingColumns)) {
    echo '<th>Precio</th>';
}
if (in_array('costo', $existingColumns)) {
    echo '<th>Costo</th>';
}
if (in_array('comentarios', $existingColumns)) {
    echo '<th>Comentarios</th>';
}
if (in_array('palabras_clave', $existingColumns)) {
    echo '<th>Palabras Clave</th>';
}
if (in_array('imagen', $existingColumns)) {
    echo '<th>Imagen</th>';
}
if (in_array('numero_lote', $existingColumns)) {
    echo '<th>Número de Lote</th>';
}
if (in_array('con_lote', $existingColumns)) {
    echo '<th>Con Lote</th>';
}

echo '</tr>';

// Write data rows
foreach ($productos as $row) {
    echo '<tr>';
    
    if (in_array('id', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['id']) . '</td>';
    }
    if (in_array('nombre', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['nombre']) . '</td>';
    }
    if (in_array('stock', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['stock']) . '</td>';
    }
    if (in_array('restock', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['restock']) . '</td>';
    }
    if (in_array('precio', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['precio']) . '</td>';
    }
    if (in_array('costo', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['costo']) . '</td>';
    }
    if (in_array('comentarios', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['comentarios']) . '</td>';
    }
    if (in_array('palabras_clave', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['palabras_clave']) . '</td>';
    }
    if (in_array('imagen', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['imagen']) . '</td>';
    }
    if (in_array('numero_lote', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['numero_lote']) . '</td>';
    }
    if (in_array('con_lote', $existingColumns)) {
        echo '<td>' . htmlspecialchars($row['con_lote'] ? 'Sí' : 'No') . '</td>';
    }
    
    echo '</tr>';
}

echo '</table>';
echo '</body>';
echo '</html>';
?>
