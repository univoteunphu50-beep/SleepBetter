<?php
// Archivo: imprimir_factura.php
// Genera un PDF de una factura específica

require_once('../TCPDF-main/tcpdf.php');
include("../conexion.php");
include("../db_helper.php");

$id_factura = $_GET['id'] ?? 0;

if (!$id_factura) {
    die('ID de factura no proporcionado');
}

try {
    // Obtener datos de la factura
    $sql_factura = "
        SELECT 
            f.id,
            f.numero_factura,
            f.fecha_factura,
            f.vendedor,
            f.cliente,
            f.subtotal,
            f.itbis,
            f.total
        FROM facturas f
        WHERE f.id = ?
    ";
    
    $factura = selectOne($conn, $sql_factura, [$id_factura]);
    
    if (!$factura) {
        die('Factura no encontrada');
    }
    
    // Obtener productos de la factura
    $sql_productos = "
        SELECT 
            d.nombre_producto as nombre,
            d.precio,
            d.cantidad,
            d.descuento,
            d.aplicar_itbis as itebis,
            d.total_producto as total
        FROM detalles_factura d
        WHERE d.factura_id = ?
    ";
    
    $productos = selectAll($conn, $sql_productos, [$id_factura]);
    
    $factura['productos'] = $productos;
    
} catch (Exception $e) {
    die('Error al obtener la factura: ' . $e->getMessage());
}

// Crear nuevo documento PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar información del documento
$pdf->SetCreator('Sleep Better');
$pdf->SetAuthor('Sleep Better');
$pdf->SetTitle('Factura #' . ($factura['numero_factura'] ?? $factura['id']));

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

// Encabezado de la factura
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 10, 'SLEEP BETTER', 0, 1, 'C');
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 8, 'FACTURA #' . ($factura['numero_factura'] ?? $factura['id']), 0, 1, 'C');
$pdf->Ln(5);

// Información de la factura
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 8, 'Fecha: ' . date('d/m/Y', strtotime($factura['fecha_factura'])), 0, 1);
$pdf->Cell(0, 8, 'Vendedor: ' . $factura['vendedor'], 0, 1);
$pdf->Ln(5);

// Datos del cliente
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'DATOS DEL CLIENTE', 0, 1);
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(0, 6, 'Nombre: ' . $factura['cliente'], 0, 1);
$pdf->Ln(10);

// Tabla de productos
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 8, 'PRODUCTOS', 0, 1);
$pdf->Ln(2);

// Encabezados de la tabla
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetFillColor(102, 126, 234);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(80, 8, 'Producto', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Precio', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Cant.', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Desc.', 1, 0, 'C', true);
$pdf->Cell(15, 8, 'ITBIS', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Total', 1, 1, 'C', true);

// Datos de los productos
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFillColor(245, 245, 245);

$totalProductos = 0;
$contador = 0;

foreach ($factura['productos'] as $producto) {
    $contador++;
    $fill = ($contador % 2 == 0) ? true : false;
    
    $pdf->Cell(80, 8, substr($producto['nombre'], 0, 35), 1, 0, 'L', $fill);
    $pdf->Cell(20, 8, '$' . number_format($producto['precio'], 2), 1, 0, 'R', $fill);
    $pdf->Cell(20, 8, $producto['cantidad'], 1, 0, 'C', $fill);
    $pdf->Cell(20, 8, $producto['descuento'] . '%', 1, 0, 'C', $fill);
    $pdf->Cell(15, 8, $producto['itebis'] ? 'Sí' : 'No', 1, 0, 'C', $fill);
    $pdf->Cell(25, 8, '$' . number_format($producto['total'], 2), 1, 1, 'R', $fill);
    
    $totalProductos += $producto['total'];
}

// Totales
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(102, 126, 234);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(150, 8, 'SUBTOTAL:', 1, 0, 'R', true);
$pdf->Cell(30, 8, '$' . number_format($factura['subtotal'], 2), 1, 1, 'R', true);

$pdf->Cell(150, 8, 'ITBIS (18%):', 1, 0, 'R', true);
$pdf->Cell(30, 8, '$' . number_format($factura['itbis'], 2), 1, 1, 'R', true);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(150, 10, 'TOTAL:', 1, 0, 'R', true);
$pdf->Cell(30, 10, '$' . number_format($factura['total'], 2), 1, 1, 'R', true);

// Pie de página
$pdf->Ln(15);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 8, 'Factura generada el ' . date('d/m/Y H:i:s') . ' por Sleep Better', 0, 1, 'C');

// Generar el PDF
$pdf->Output('factura_' . ($factura['numero_factura'] ?? $factura['id']) . '_' . date('Y-m-d_H-i-s') . '.pdf', 'D');

closeConnection($conn);
?> 