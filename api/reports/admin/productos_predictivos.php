<?php
require_once('../../helpers/report.php');
require_once('../../models/data/categoria_data.php');
require_once('../../models/data/producto_data.php');

$pdf = new Report;

// Definir el margen adicional para las páginas posteriores
$marginBottom = 30; // Ajusta este valor según tus necesidades
$tableTopY = 40; // Posición inicial de la tabla en la primera página

$pdf->startReport('');
$pdf->SetTextColor(0, 0, 0); // Establece el color del texto a negro
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetY(54); // Ajusta el valor según sea necesario para subir el título
$pdf->Cell(0, 0, $pdf->encodeString('Proyección de ventas mensuales por categoría'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Primer salto de línea

// Función para imprimir el encabezado de la tabla
function printTableHeader($pdf)
{
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(38, 15, 189); 
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(40, 10, 'Nombre de la categoria', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, $pdf->encodeString('Promedio mensual'), 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Mes', 1, 0, 'C', 1);
    $pdf->Cell(20, 10,  $pdf->encodeString('Año'), 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Porcentaje de ventas (%)', 1, 1, 'C', 1);
}

$productos = new ProductoData;

// Imprimir el encabezado de la tabla en la primera página
printTableHeader($pdf);

if ($dataProductos = $productos->PredictivoProductosCategoria()) {
    foreach ($dataProductos as $rowProductos) {
        // Verifica si se necesita una nueva página
        if ($pdf->GetY() > 250) { // Ajusta el valor según el tamaño de la página
            $pdf->AddPage('p', 'letter'); // Agrega una nueva página si es necesario
            // Reimprime el título y el encabezado de la tabla
            $pdf->SetFont('Arial', '', 15);
            $pdf->Cell(0, 10, $pdf->encodeString('Proyección de ventas mensuales por categoría'), 0, 1, 'C');
            $pdf->Ln(10); // Salto de línea
            printTableHeader($pdf);
        }

        $yStart = $pdf->GetY();
        $xStart = $pdf->GetX();

        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(40, 10, $pdf->encodeString($rowProductos['nombre_categoria']), 1, 'L');
        $multiCellHeightTitulo = $pdf->GetY() - $yStart;

        $pdf->SetXY($xStart + 40, $yStart);

        $pdf->Cell(40, $multiCellHeightTitulo, $rowProductos['promedio_mensual'], 1, 0, 'C');
        $pdf->Cell(40, $multiCellHeightTitulo, $rowProductos['mes_proyectado'], 1, 0, 'C');
        $pdf->Cell(20, $multiCellHeightTitulo, $rowProductos['año_proyectado'], 1, 0, 'C');
        $pdf->Cell(50, $multiCellHeightTitulo, $rowProductos['ventas_proyectadas'], 1, 1, 'C');

        $pdf->SetY($yStart + $multiCellHeightTitulo);
    }
} else {
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(190, 10, $pdf->encodeString('No hay productos para esta categoría'), 1, 1, 'C');
}

$pdf->output('I', 'categoriasPredictivas.pdf');
?>