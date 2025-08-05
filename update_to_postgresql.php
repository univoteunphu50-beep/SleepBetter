<?php
/**
 * Script para actualizar archivos de MySQLi a PostgreSQL
 * Este script actualiza los archivos más críticos automáticamente
 */

// Lista de archivos que necesitan ser actualizados
$files_to_update = [
    'productos/actualizar_producto.php',
    'productos/eliminar_producto.php',
    'productos/buscar_clientes.php',
    'clientes/guardar_cliente.php',
    'clientes/actualizar_cliente.php',
    'clientes/eliminar_cliente.php',
    'clientes/buscar_clientes.php',
    'clientes/editar_cliente.php',
    'facturacion/facturar.php',
    'facturacion/ver_factura.php',
    'facturacion/imprimir_factura.php',
    'obtener_actividades.php',
    'obtener_estadisticas_actividades.php',
    'obtener_historial_stock.php',
    'obtener_alertas_restock.php',
    'verificar_alertas_restock.php',
    'resolver_alerta_restock.php',
    'crear_admin_secreto.php'
];

echo "🔄 Iniciando actualización de archivos a PostgreSQL...\n";

foreach ($files_to_update as $file) {
    if (file_exists($file)) {
        echo "📝 Actualizando: $file\n";
        
        // Leer el contenido del archivo
        $content = file_get_contents($file);
        
        // Reemplazos para compatibilidad con PostgreSQL
        $replacements = [
            // Incluir db_helper.php
            'include("conexion.php");' => 'include("conexion.php");' . "\n" . 'include("db_helper.php");',
            'include("../conexion.php");' => 'include("../conexion.php");' . "\n" . 'include("../db_helper.php");',
            
            // Reemplazar bind_param con parámetros
            '/\$stmt->bind_param\("([^"]+)",\s*([^)]+)\);/' => '// bind_param replaced with parameters array',
            
            // Reemplazar fetch_all(MYSQLI_ASSOC)
            '/->fetch_all\(MYSQLI_ASSOC\)/' => '',
            
            // Reemplazar get_result()->fetch_assoc()
            '/->get_result\(\)->fetch_assoc\(\)/' => '',
            
            // Reemplazar num_rows
            '/->num_rows/' => '',
            
            // Reemplazar close()
            '/->close\(\)/' => '',
            
            // Reemplazar real_escape_string
            '/->real_escape_string\(/' => '',
        ];
        
        // Aplicar reemplazos
        foreach ($replacements as $pattern => $replacement) {
            $content = preg_replace($pattern, $replacement, $content);
        }
        
        // Guardar el archivo actualizado
        file_put_contents($file, $content);
        
        echo "✅ Actualizado: $file\n";
    } else {
        echo "⚠️  Archivo no encontrado: $file\n";
    }
}

echo "🎉 Actualización completada!\n";
echo "📋 Recuerda revisar manualmente los archivos actualizados para asegurar compatibilidad.\n";
?> 