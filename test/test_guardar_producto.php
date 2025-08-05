<?php
// Simular datos POST para probar guardar_producto.php
$_POST = array(
    'nombre' => 'Producto de Prueba',
    'stock' => 10,
    'restock' => 5,
    'precio' => 100.00,
    'costo' => 50.00,
    'comentarios' => 'Comentario de prueba',
    'keywords' => 'prueba,test',
    'con_lote' => 1,
    'numero_lote' => 'LOTE-2024-001',
    'fecha_fabricacion' => '2024-01-15',
    'fecha_expiracion' => '2026-01-15',
    'temperatura_almacenamiento' => 25.00,
    'condiciones_especiales' => 'Mantener en lugar seco'
);

$_FILES = array(
    'imagen' => array(
        'name' => '',
        'tmp_name' => '',
        'error' => 4 // UPLOAD_ERR_NO_FILE
    )
);

echo "=== PRUEBA DE GUARDAR PRODUCTO ===\n";
echo "Datos POST simulados:\n";
print_r($_POST);

echo "\nCambiando al directorio productos...\n";
chdir('productos');

echo "\nIncluyendo guardar_producto.php...\n";

// Capturar la salida
ob_start();
include("guardar_producto.php");
$output = ob_get_clean();

echo "Salida del script:\n";
echo $output;
?> 