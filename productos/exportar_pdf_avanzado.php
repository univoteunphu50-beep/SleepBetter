<?php
// Limpiar cualquier salida previa de manera segura
if (ob_get_level()) {
    ob_end_clean();
}
ob_start();

require_once('../TCPDF-main/tcpdf.php');
include("../conexion.php");
include("../db_helper.php");

// Verificar si hay productos
$productos = selectAll($conn, "SELECT * FROM productos ORDER BY nombre ASC");

if (count($productos) === 0) {
    die("No hay productos para exportar");
}

// Crear nuevo documento PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar información del documento
$pdf->SetCreator('Sleep Better');
$pdf->SetAuthor('Sleep Better');
$pdf->SetTitle('Catálogo de Productos - Sleep Better');
$pdf->SetSubject('Catálogo de Productos');

// Configurar márgenes
$pdf->SetMargins(15, 15, 15);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(10);

// Configurar saltos de página automáticos
$pdf->SetAutoPageBreak(TRUE, 25);

// Configurar fuente
$pdf->SetFont('helvetica', '', 10);

// Agregar página
$pdf->AddPage();

// Logo y título
$pdf->SetFont('helvetica', 'B', 24);
$pdf->Cell(0, 15, 'SLEEP BETTER', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 10, 'Catálogo de Productos', 0, 1, 'C');
$pdf->Ln(5);

// Información de la empresa
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Especialistas en equipos CPAP y productos para el sueño', 0, 1, 'C');
$pdf->Cell(0, 6, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
$pdf->Ln(10);

// Contador de productos
$total_productos = count($productos);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Resumen del Inventario', 0, 1, 'L');
$pdf->Ln(5);

// Estadísticas rápidas
$pdf->SetFont('helvetica', '', 10);

// Calcular estadísticas
$sql_stats = "SELECT 
    COUNT(*) as total_productos,
    COUNT(CASE WHEN con_lote = true THEN 1 END) as productos_lotes,
    COUNT(CASE WHEN stock <= restock THEN 1 END) as stock_bajo,
    SUM(stock * precio) as valor_total,
    AVG(precio) as precio_promedio
FROM productos";
$stats = selectOne($conn, $sql_stats);

$pdf->Cell(0, 6, '• Total de productos: ' . $stats['total_productos'], 0, 1, 'L');
$pdf->Cell(0, 6, '• Productos con control de lotes: ' . $stats['productos_lotes'], 0, 1, 'L');
$pdf->Cell(0, 6, '• Productos con stock bajo: ' . $stats['stock_bajo'], 0, 1, 'L');
$pdf->Cell(0, 6, '• Valor total del inventario: $' . number_format($stats['valor_total'], 2), 0, 1, 'L');
$pdf->Cell(0, 6, '• Precio promedio: $' . number_format($stats['precio_promedio'], 2), 0, 1, 'L');

$pdf->Ln(10);

// Tabla principal de productos
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Catálogo Completo de Productos', 0, 1, 'L');
$pdf->Ln(5);

// Encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(70, 130, 180);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
$pdf->Cell(70, 8, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'STOCK', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'PRECIO', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'COSTO', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'LOTE', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 8);

// Datos de productos
$fill = false;
$row_count = 0;
foreach ($productos as $row) {
    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
    
    // ID
    $pdf->Cell(20, 8, $row['id'], 1, 0, 'C', $fill);
    
    // Nombre (con salto de línea si es muy largo)
    $nombre = $row['nombre'];
    if (strlen($nombre) > 30) {
        $nombre = substr($nombre, 0, 27) . '...';
    }
    $pdf->Cell(70, 8, $nombre, 1, 0, 'L', $fill);
    
    // Stock con color según nivel
    $stock_color = '';
    if ($row['stock'] <= $row['restock']) {
        $pdf->SetTextColor(255, 0, 0); // Rojo para stock bajo
    } elseif ($row['stock'] <= 10) {
        $pdf->SetTextColor(255, 165, 0); // Naranja para stock medio
    } else {
        $pdf->SetTextColor(0, 128, 0); // Verde para stock alto
    }
    $pdf->Cell(20, 8, $row['stock'], 1, 0, 'C', $fill);
    $pdf->SetTextColor(0, 0, 0); // Restaurar color
    
    // Precio
    $pdf->Cell(25, 8, '$' . number_format($row['precio'], 2), 1, 0, 'R', $fill);
    
    // Costo
    $pdf->Cell(25, 8, '$' . number_format($row['costo'], 2), 1, 0, 'R', $fill);
    
    // Lote
    $lote = $row['con_lote'] ? ($row['numero_lote'] ?: 'Sí') : 'No';
    $pdf->Cell(30, 8, $lote, 1, 1, 'C', $fill);
    
    $fill = !$fill;
    $row_count++;
    
    // Agregar nueva página si hay muchos productos
    if ($row_count >= 25) {
        $pdf->AddPage();
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Catálogo de Productos (Continuación)', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Repetir encabezados
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(70, 130, 180);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
        $pdf->Cell(70, 8, 'NOMBRE', 1, 0, 'C', true);
        $pdf->Cell(20, 8, 'STOCK', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'PRECIO', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'COSTO', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'LOTE', 1, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('helvetica', '', 8);
        $row_count = 0;
    }
}

$pdf->Ln(10);

// Sección de productos con lotes (nueva página)
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'PRODUCTOS CON CONTROL DE LOTES', 0, 1, 'L');
$pdf->Ln(5);

$productos_lotes = selectAll($conn, "SELECT * FROM productos WHERE con_lote = true ORDER BY nombre ASC");

if (count($productos_lotes) > 0) {
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(70, 130, 180);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(60, 8, 'NOMBRE', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'LOTE', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'FABRICACIÓN', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'EXPIRACIÓN', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'TEMPERATURA', 1, 1, 'C', true);
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $fill = false;
    
    foreach ($productos_lotes as $row) {
        $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
        
        // Nombre
        $nombre = $row['nombre'];
        if (strlen($nombre) > 25) {
            $nombre = substr($nombre, 0, 22) . '...';
        }
        $pdf->Cell(60, 8, $nombre, 1, 0, 'L', $fill);
        
        // Número de lote
        $pdf->Cell(30, 8, $row['numero_lote'] ?: 'N/A', 1, 0, 'C', $fill);
        
        // Fecha de fabricación
        $fecha_fab = $row['fecha_fabricacion'] ? date('d/m/Y', strtotime($row['fecha_fabricacion'])) : 'N/A';
        $pdf->Cell(30, 8, $fecha_fab, 1, 0, 'C', $fill);
        
        // Fecha de expiración con color según proximidad
        $fecha_exp = $row['fecha_expiracion'] ? date('d/m/Y', strtotime($row['fecha_expiracion'])) : 'N/A';
        if ($row['fecha_expiracion']) {
            $dias_restantes = (strtotime($row['fecha_expiracion']) - time()) / (60 * 60 * 24);
            if ($dias_restantes <= 30) {
                $pdf->SetTextColor(255, 0, 0); // Rojo si expira pronto
            } elseif ($dias_restantes <= 90) {
                $pdf->SetTextColor(255, 165, 0); // Naranja si expira en 3 meses
            }
        }
        $pdf->Cell(30, 8, $fecha_exp, 1, 0, 'C', $fill);
        $pdf->SetTextColor(0, 0, 0); // Restaurar color
        
        // Temperatura
        $pdf->Cell(40, 8, $row['temperatura_almacenamiento'] ?: 'N/A', 1, 1, 'C', $fill);
        
        $fill = !$fill;
    }
} else {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 8, 'No hay productos con control de lotes', 0, 1, 'L');
}

$pdf->Ln(10);

// Productos con stock bajo (nueva página)
$pdf->AddPage();
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'PRODUCTOS CON STOCK BAJO', 0, 1, 'L');
$pdf->Ln(5);

$productos_stock_bajo = selectAll($conn, "SELECT * FROM productos WHERE stock <= restock ORDER BY stock ASC");

if (count($productos_stock_bajo) > 0) {
    $pdf->SetFont('helvetica', 'B', 9);
    $pdf->SetFillColor(255, 0, 0);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->Cell(80, 8, 'NOMBRE', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'STOCK', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'RESTOCK', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'PRECIO', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'URGENCIA', 1, 1, 'C', true);
    
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('helvetica', '', 8);
    $fill = false;
    
    foreach ($productos_stock_bajo as $row) {
        $pdf->SetFillColor($fill ? 255 : 255, $fill ? 240 : 240, $fill ? 240 : 240);
        
        // Nombre
        $nombre = $row['nombre'];
        if (strlen($nombre) > 35) {
            $nombre = substr($nombre, 0, 32) . '...';
        }
        $pdf->Cell(80, 8, $nombre, 1, 0, 'L', $fill);
        
        // Stock
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(25, 8, $row['stock'], 1, 0, 'C', $fill);
        $pdf->SetTextColor(0, 0, 0);
        
        // Restock
        $pdf->Cell(25, 8, $row['restock'], 1, 0, 'C', $fill);
        
        // Precio
        $pdf->Cell(30, 8, '$' . number_format($row['precio'], 2), 1, 0, 'R', $fill);
        
        // Urgencia
        $urgencia = '';
        if ($row['stock'] == 0) {
            $urgencia = 'CRÍTICO';
            $pdf->SetTextColor(255, 0, 0);
        } elseif ($row['stock'] <= 2) {
            $urgencia = 'ALTO';
            $pdf->SetTextColor(255, 165, 0);
        } else {
            $urgencia = 'MEDIO';
            $pdf->SetTextColor(255, 165, 0);
        }
        $pdf->Cell(30, 8, $urgencia, 1, 1, 'C', $fill);
        $pdf->SetTextColor(0, 0, 0);
        
        $fill = !$fill;
    }
} else {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 8, 'No hay productos con stock bajo', 0, 1, 'L');
}

$pdf->Ln(10);

// Pie de página
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 8, 'Este catálogo fue generado automáticamente por el sistema Sleep Better', 0, 1, 'C');
$pdf->Cell(0, 8, 'Para más información, contacte a Sleep Better', 0, 1, 'C');
$pdf->Cell(0, 8, 'Página ' . $pdf->getAliasNumPage() . ' de ' . $pdf->getAliasNbPages(), 0, 1, 'C');

// Cerrar conexión
closeConnection($conn);

// Limpiar buffer y generar PDF
ob_end_clean();
$pdf->Output('catalogo_productos_avanzado_' . date('Y-m-d_H-i-s') . '.pdf', 'I');
?> 