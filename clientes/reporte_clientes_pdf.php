<?php
// Archivo: reporte_clientes_pdf.php
// Genera un PDF con todos los clientes

require_once('../TCPDF-main/tcpdf.php');
include("../conexion.php");
include("../db_helper.php");

try {
    // Crear nuevo documento PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    // Configurar información del documento
    $pdf->SetCreator('Sleep Better');
    $pdf->SetAuthor('Sleep Better');
    $pdf->SetTitle('Reporte de Clientes');

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
    $pdf->Cell(0, 10, 'REPORTE DE CLIENTES - SLEEP BETTER', 0, 1, 'C');
    $pdf->Ln(5);

    // Fecha del reporte
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 8, 'Fecha del reporte: ' . date('d/m/Y H:i:s'), 0, 1);
    $pdf->Ln(5);

    // Consultar todos los clientes
    $sql = "SELECT cedula, cliente, telefono, email, direccion, comentarios, fecha_registro FROM clientes ORDER BY cliente ASC";
    $clientes = selectAll($conn, $sql);

    if (empty($clientes)) {
        $pdf->SetFont('helvetica', 'I', 12);
        $pdf->Cell(0, 10, 'No se encontraron clientes registrados.', 0, 1, 'C');
    } else {
        // Encabezados de la tabla
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(102, 126, 234);
        $pdf->SetTextColor(255, 255, 255);

        $pdf->Cell(30, 8, 'Cédula', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Nombre', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Teléfono', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'Email', 1, 0, 'C', true);
        $pdf->Cell(25, 8, 'Registro', 1, 1, 'C', true);

        // Datos de los clientes
        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(245, 245, 245);

        $contador = 0;
        $totalClientes = count($clientes);

        foreach ($clientes as $cliente) {
            $contador++;
            $fill = ($contador % 2 == 0) ? true : false;

            // Cédula
            $pdf->Cell(30, 8, $cliente['cedula'] ?? 'N/A', 1, 0, 'C', $fill);
            
            // Nombre (truncado si es muy largo)
            $nombre = substr($cliente['cliente'], 0, 20);
            if (strlen($cliente['cliente']) > 20) {
                $nombre .= '...';
            }
            $pdf->Cell(50, 8, $nombre, 1, 0, 'L', $fill);
            
            // Teléfono
            $pdf->Cell(30, 8, $cliente['telefono'] ?? 'N/A', 1, 0, 'C', $fill);
            
            // Email (truncado si es muy largo)
            $email = substr($cliente['email'] ?? 'N/A', 0, 20);
            if (strlen($cliente['email'] ?? '') > 20) {
                $email .= '...';
            }
            $pdf->Cell(50, 8, $email, 1, 0, 'L', $fill);
            
            // Fecha de registro
            $fecha = $cliente['fecha_registro'] ? date('d/m/Y', strtotime($cliente['fecha_registro'])) : 'N/A';
            $pdf->Cell(25, 8, $fecha, 1, 1, 'C', $fill);
        }

        // Resumen
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(102, 126, 234);
        $pdf->SetTextColor(255, 255, 255);

        $pdf->Cell(185, 8, 'TOTAL DE CLIENTES:', 1, 0, 'R', true);
        $pdf->Cell(25, 8, $totalClientes, 1, 1, 'C', true);

        // Información adicional
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(0, 0, 0);

        // Contar clientes con email
        $clientesConEmail = 0;
        $clientesConTelefono = 0;
        foreach ($clientes as $cliente) {
            if (!empty($cliente['email'])) $clientesConEmail++;
            if (!empty($cliente['telefono'])) $clientesConTelefono++;
        }

        $pdf->Cell(0, 8, "Total de clientes: $totalClientes", 0, 1);
        $pdf->Cell(0, 8, "Clientes con email: $clientesConEmail", 0, 1);
        $pdf->Cell(0, 8, "Clientes con teléfono: $clientesConTelefono", 0, 1);
    }

    // Pie de página
    $pdf->Ln(15);
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 8, 'Reporte generado el ' . date('d/m/Y H:i:s') . ' por Sleep Better', 0, 1, 'C');

    // Generar el PDF
    $pdf->Output('reporte_clientes_' . date('Y-m-d_H-i-s') . '.pdf', 'D');

} catch (Exception $e) {
    die('Error al generar el reporte: ' . $e->getMessage());
}

closeConnection($conn);
?> 