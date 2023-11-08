<?php
require('../fpdf/fpdf.php');

// Crear una instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Configurar la fuente y el tamaño de la fuente
$pdf->SetFont('Arial', 'B', 16);

// Agregar un título al PDF
$pdf->Cell(0, 10, 'Ejemplo de FPDF', 0, 1, 'C');

// Agregar contenido al PDF
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Este es un ejemplo simple de FPDF.', 0, 1, 'L');
$pdf->Cell(0, 10, 'Puedes agregar más contenido y personalizarlo como desees.', 0, 1, 'L');

// Generar el PDF
$pdf->Output();

// NOTA: No incluyas ninguna salida HTML (como echo) antes o después de Output().

?>