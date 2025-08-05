<?php
// Archivo: imprimir_todas_facturas.php
// Genera un PDF con todas las facturas filtradas

require_once('../TCPDF-main/tcpdf.php');
include("../conexion.php");

// Filtros
$cliente = $_GET['cliente'] ?? '';
$producto = $_GET['producto'] ?? '';
$fecha = $_GET['fecha'] ?? '';

// Crear nuevo documento PDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Configurar información del documento
$pdf->SetCreator('Sleep Better');
$pdf->SetAuthor('Sleep Better');
$pdf->SetTitle('Reporte de Facturas');

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

// Título del reporte
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'REPORTE DE FACTURAS - SLEEP BETTER', 0, 1, 'C');
$pdf->Ln(5);

// Información de filtros aplicados
$pdf->SetFont('helvetica', '', 10);
$filtros = [];
if (!empty($cliente)) $filtros[] = "Cliente: $cliente";
if (!empty($producto)) $filtros[] = "Producto: $producto";
if (!empty($fecha)) $filtros[] = "Fecha: $fecha";

if (!empty($filtros)) {
    $pdf->Cell(0, 8, 'Filtros aplicados: ' . implode(', ', $filtros), 0, 1);
    $pdf->Ln(5);
}

// Fecha del reporte
$pdf->Cell(0, 8, 'Fecha del reporte: ' . date('d/m/Y H:i:s'), 0, 1);
$pdf->Ln(5);

// Consulta con JOIN para obtener el nombre real del cliente
$sql = "
    SELECT 
        f.id,
        f.fecha,
        c.cliente AS cliente,
        GROUP_CONCAT(p.nombre SEPARATOR ', ') AS productos,
        f.total
    FROM facturas f
    INNER JOIN clientes c ON f.cedula_cliente = c.cedula
    INNER JOIN detalle_factura d ON f.id = d.id_factura
    INNER JOIN productos p ON d.id_producto = p.id
    WHERE 1=1
";

// Filtros dinámicos
if (!empty($cliente)) {
    $cliente = $conn->real_escape_string($cliente);
    $sql .= " AND f.cedula_cliente LIKE '%$cliente%'";
}

if (!empty($producto)) {
    $producto = $conn->real_escape_string($producto);
    $sql .= " AND p.nombre LIKE '%$producto%'";
}

if (!empty($fecha)) {
    $fecha = $conn->real_escape_string($fecha);
    $sql .= " AND DATE(f.fecha) = '$fecha'";
}

$sql .= " GROUP BY f.id ORDER BY f.fecha DESC";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    $pdf->SetFont('helvetica', 'I', 12);
    $pdf->Cell(0, 10, 'No se encontraron facturas con los filtros especificados.', 0, 1, 'C');
} else {
    // Encabezados de la tabla
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(102, 126, 234);
    $pdf->SetTextColor(255, 255, 255);
    
    $pdf->Cell(20, 8, 'ID', 1, 0, 'C', true);
    $pdf->Cell(30, 8, 'Fecha', 1, 0, 'C', true);
    $pdf->Cell(50, 8, 'Cliente', 1, 0, 'C', true);
    $pdf->Cell(60, 8, 'Productos', 1, 0, 'C', true);
    $pdf->Cell(25, 8, 'Total', 1, 1, 'C', true);
    
    // Datos de las facturas
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(245, 245, 245);
    
    $totalGeneral = 0;
    $contador = 0;
    
    while ($row = $result->fetch_assoc()) {
        $contador++;
        $fill = ($contador % 2 == 0) ? true : false;
        
        $pdf->Cell(20, 8, $row['id'], 1, 0, 'C', $fill);
        $pdf->Cell(30, 8, date('d/m/Y', strtotime($row['fecha'])), 1, 0, 'C', $fill);
        $pdf->Cell(50, 8, substr($row['cliente'], 0, 20), 1, 0, 'L', $fill);
        $pdf->Cell(60, 8, substr($row['productos'], 0, 25), 1, 0, 'L', $fill);
        $pdf->Cell(25, 8, '$' . number_format($row['total'], 2), 1, 1, 'R', $fill);
        
        $totalGeneral += $row['total'];
    }
    
    // Total general
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(102, 126, 234);
    $pdf->SetTextColor(255, 255, 255);
    
    $pdf->Cell(160, 8, 'TOTAL GENERAL:', 1, 0, 'R', true);
    $pdf->Cell(25, 8, '$' . number_format($totalGeneral, 2), 1, 1, 'R', true);
    
    // Resumen
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(0, 8, "Total de facturas: $contador", 0, 1);
    $pdf->Cell(0, 8, "Total general: $" . number_format($totalGeneral, 2), 0, 1);
}

// Pie de página
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 8, 'Reporte generado el ' . date('d/m/Y H:i:s') . ' por Sleep Better', 0, 1, 'C');

// Generar el PDF
$pdf->Output('reporte_facturas_' . date('Y-m-d_H-i-s') . '.pdf', 'D');
?> 