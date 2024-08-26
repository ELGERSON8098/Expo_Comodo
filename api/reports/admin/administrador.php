<?php
require_once('../../helpers/report.php');
require_once('../../models/data/administrador_data.php');

$pdf = new Report;

// Definir el margen adicional para las páginas posteriores
$marginBottom = 30; // Ajusta este valor según tus necesidades
$tableTopY = 40; // Posición inicial de la tabla en la primera página

$pdf->startReport('');
$pdf->SetTextColor(0, 0, 0); // Establece el color del texto a negro
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetY(54); // Ajusta el valor según sea necesario para subir el título
$pdf->Cell(0, 0, $pdf->encodeString('Listado de administradores'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Primer salto de línea

$admin = new AdministradorData;

// Función para imprimir el encabezado de la tabla
function printTableHeader($pdf) {
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(38, 15, 189); // Color de fondo
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(70, 10, 'Nombre del usuario', 1, 0, 'C', 1);
    $pdf->Cell(60, 10, $pdf->encodeString('Correo'), 1, 0, 'C', 1);
    $pdf->Cell(60, 10, 'Nivel', 1, 1, 'C', 1);
}

if ($dataAdmin = $admin->obtenerAdministradores()) {
    printTableHeader($pdf);

    foreach ($dataAdmin as $rowAdmin) {
        // Verifica si se necesita una nueva página
        if ($pdf->GetY() > 250) { // Ajusta el valor según el tamaño de la página
            $pdf->AddPage(); // Agrega una nueva página si es necesario
            // Reimprime el título y el encabezado de la tabla
            $pdf->SetFont('Arial', 'B', 15);
            $pdf->Cell(0, 0, $pdf->encodeString('Listado de administradores'), 0, 1, 'C');
            $pdf->Ln(10); // Salto de línea
            printTableHeader($pdf);
        }

        $yStart = $pdf->GetY();
        $xStart = $pdf->GetX();

        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(70, 10, $pdf->encodeString($rowAdmin['usuario']), 1, 'L');
        $multiCellHeightTitulo = $pdf->GetY() - $yStart;

        $pdf->SetXY($xStart + 70, $yStart);

        $pdf->Cell(60, $multiCellHeightTitulo, $rowAdmin['correo'], 1, 0, 'C');

        $pdf->SetXY($xStart + 130, $yStart);
        $pdf->Cell(60, $multiCellHeightTitulo, $rowAdmin['nivel_usuario'], 1, 1, 'C');

        $pdf->SetY($yStart + $multiCellHeightTitulo);
    }
} else {
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(190, 10, 'No hay administradores para mostrar', 1, 1, 'C');
}

$pdf->output('I', 'Administradores.pdf');