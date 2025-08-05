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

// Título del catálogo
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 10, 'CATÁLOGO DE PRODUCTOS', 0, 1, 'C');
$pdf->Ln(5);

// Información de la empresa
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 8, 'Sleep Better', 0, 1, 'C');
$pdf->Cell(0, 8, 'Fecha de generación: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
$pdf->Ln(10);

// Contador de productos
$total_productos = count($productos);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Total de productos: ' . $total_productos, 0, 1, 'L');
$pdf->Ln(5);

// Tabla de productos
$pdf->SetFont('helvetica', 'B', 10);

// Encabezados de la tabla
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(25, 8, 'ID', 1, 0, 'C', true);
$pdf->Cell(60, 8, 'NOMBRE', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'STOCK', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'PRECIO', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'COSTO', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'LOTE', 1, 1, 'C', true);

$pdf->SetFont('helvetica', '', 9);

// Datos de productos
$fill = false;
foreach ($productos as $row) {
    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
    
    // ID
    $pdf->Cell(25, 8, $row['id'], 1, 0, 'C', $fill);
    
    // Nombre (con salto de línea si es muy largo)
    $nombre = $row['nombre'];
    if (strlen($nombre) > 25) {
        $nombre = substr($nombre, 0, 22) . '...';
    }
    $pdf->Cell(60, 8, $nombre, 1, 0, 'L', $fill);
    
    // Stock
    $pdf->Cell(25, 8, $row['stock'], 1, 0, 'C', $fill);
    
    // Precio
    $pdf->Cell(25, 8, '$' . number_format($row['precio'], 2), 1, 0, 'R', $fill);
    
    // Costo
    $pdf->Cell(25, 8, '$' . number_format($row['costo'], 2), 1, 0, 'R', $fill);
    
    // Lote
    $lote = $row['con_lote'] ? ($row['numero_lote'] ?: 'Sí') : 'No';
    $pdf->Cell(30, 8, $lote, 1, 1, 'C', $fill);
    
    $fill = !$fill;
}

$pdf->Ln(10);

// Sección de productos con lotes
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'PRODUCTOS CON CONTROL DE LOTES', 0, 1, 'L');
$pdf->Ln(5);

$productos_lotes = selectAll($conn, "SELECT * FROM productos WHERE con_lote = true ORDER BY nombre ASC");

if (count($productos_lotes) > 0) {
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(240, 240, 240);
    $pdf->Cell(60, 8, 'NOMBRE', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'LOTE', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'FABRICACIÓN', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'EXPIRACIÓN', 1, 0, 'C', true);
    $pdf->Cell(40, 8, 'TEMPERATURA', 1, 1, 'C', true);
    
    $pdf->SetFont('helvetica', '', 9);
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
        
        // Fecha de expiración
        $fecha_exp = $row['fecha_expiracion'] ? date('d/m/Y', strtotime($row['fecha_expiracion'])) : 'N/A';
        $pdf->Cell(30, 8, $fecha_exp, 1, 0, 'C', $fill);
        
        // Temperatura
        $pdf->Cell(40, 8, $row['temperatura_almacenamiento'] ?: 'N/A', 1, 1, 'C', $fill);
        
        $fill = !$fill;
    }
} else {
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 8, 'No hay productos con control de lotes', 0, 1, 'L');
}

$pdf->Ln(10);

// Estadísticas
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'ESTADÍSTICAS', 0, 1, 'L');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 10);

// Contar productos con lotes
$total_lotes_result = selectOne($conn, "SELECT COUNT(*) as total FROM productos WHERE con_lote = true");
$total_lotes = $total_lotes_result['total'];

// Calcular valor total del inventario
$valor_total_result = selectOne($conn, "SELECT SUM(stock * precio) as valor_total FROM productos");
$valor_total = $valor_total_result['valor_total'];

// Productos con stock bajo
$stock_bajo_result = selectOne($conn, "SELECT COUNT(*) as total FROM productos WHERE stock <= restock");
$stock_bajo = $stock_bajo_result['total'];

$pdf->Cell(0, 6, '• Total de productos: ' . $total_productos, 0, 1, 'L');
$pdf->Cell(0, 6, '• Productos con control de lotes: ' . $total_lotes, 0, 1, 'L');
$pdf->Cell(0, 6, '• Productos con stock bajo: ' . $stock_bajo, 0, 1, 'L');
$pdf->Cell(0, 6, '• Valor total del inventario: $' . number_format($valor_total, 2), 0, 1, 'L');

$pdf->Ln(10);

// Pie de página
$pdf->SetFont('helvetica', '', 8);
$pdf->Cell(0, 8, 'Este catálogo fue generado automáticamente por el sistema Sleep Better', 0, 1, 'C');
$pdf->Cell(0, 8, 'Para más información, contacte a Sleep Better', 0, 1, 'C');

// Cerrar conexión
closeConnection($conn);

// Limpiar buffer y generar PDF
ob_end_clean();
$pdf->Output('catalogo_productos_' . date('Y-m-d_H-i-s') . '.pdf', 'I');
?>
