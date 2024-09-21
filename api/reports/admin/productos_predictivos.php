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
$pdf->Cell(0, 0, $pdf->encodeString('Proyección de ventas mensuales'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Primer salto de línea

function printTableHeader2($pdf)
{
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(38, 15, 189); 
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(40, 10, 'Nombre de la categoria', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, $pdf->encodeString('Total vendido'), 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Mes', 1, 0, 'C', 1);
    $pdf->Cell(20, 10,  $pdf->encodeString('Año'), 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Porcentaje de ventas (%)', 1, 1, 'C', 1);
}

$productos = new ProductoData;

// Imprimir el encabezado de la tabla en la primera página
printTableHeader2($pdf);

if ($dataProductos = $productos->PrediccionCate()) {
    foreach ($dataProductos as $rowProductos) {
         // Verifica si la posición actual en Y más 15 supera el límite permitido de la página (279.4 es la altura de la página en mm).
        // 30 es el margen inferior, por lo que si la altura actual más 15 excede el espacio disponible, se añade una nueva página.
        if ($pdf->GetY() + 15 > 279.4 - 30) { // Ajusta el valor según el tamaño de la página
            $pdf->AddPage('p', 'letter'); // Agrega una nueva página si es necesario
             // Establece la fuente del texto para el título de la nueva página.
            $pdf->SetFont('Arial', '', 15);
             // Imprime el título centrado en la nueva página.
            $pdf->Cell(0, 20, $pdf->encodeString('Proyección de ventas mensuales'), 0, 1, 'C');
            $pdf->Ln(80); // Aumentar el salto de línea para separar el título de la tabla
            printTableHeader2($pdf);
        }
        // Guarda la posición actual en Y y X para poder realizar ajustes a la altura de las celdas.
        $yStart = $pdf->GetY();
        $xStart = $pdf->GetX();

        // Establece el color del texto a negro.
        $pdf->SetTextColor(0, 0, 0);

        // Imprime el nombre de la categoría con formato de celda múltiple (MultiCell), lo que permite el texto de más de una línea.
        $pdf->MultiCell(40, 10, $pdf->encodeString($rowProductos['nombre_categoria']), 1, 'L');
        // Calcula la altura de la celda basada en la cantidad de texto impreso.
        $multiCellHeightTitulo = $pdf->GetY() - $yStart;

        $pdf->SetXY($xStart + 40, $yStart);
        // Imprime el total vendido en la columna siguiente, usando la misma altura calculada de la celda múltiple.
        $pdf->Cell(40, $multiCellHeightTitulo, $rowProductos['total_vendido'], 1, 0, 'C');

        // Imprime el mes en la siguiente columna.
        $pdf->Cell(40, $multiCellHeightTitulo, $rowProductos['mes'], 1, 0, 'C');
        
        // Imprime el año en la siguiente columna.
        $pdf->Cell(20, $multiCellHeightTitulo, $rowProductos['anio'], 1, 0, 'C');

        // Imprime el porcentaje de ventas redondeado a dos decimales en la última columna.
        $pdf->Cell(50, $multiCellHeightTitulo, round($rowProductos['porcentaje_ventas'], 2), 1, 1, 'C');

        // Establece la posición en Y justo después de la última celda impresa.
        $pdf->SetY($yStart + $multiCellHeightTitulo);
    }
} else {
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(190, 10, $pdf->encodeString('No hay productos para esta categoría'), 1, 1, 'C');
}


$pdf->Ln(120); // Primer salto de línea

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
        if ($pdf->GetY() + 15 > 279.4 - 30) { // Ajusta el valor según el tamaño de la página
            $pdf->AddPage('p', 'letter'); // Agrega una nueva página si es necesario
            // Reimprime el título y el encabezado de la tabla
            $pdf->SetFont('Arial', 'B', 15);
            printTableHeader($pdf);
        }

        $yStart = $pdf->GetY();
        $xStart = $pdf->GetX();

        $pdf->SetTextColor(0, 0, 0);
        $pdf->MultiCell(40, 10, $pdf->encodeString($rowProductos['nombre_categoria']), 1, 'L');
        $multiCellHeightTitulo = $pdf->GetY() - $yStart;

        $pdf->SetXY($xStart + 40, $yStart);

        $pdf->Cell(40, $multiCellHeightTitulo, round($rowProductos['promedio_mensual'],2), 1, 0, 'C');
        $pdf->Cell(40, $multiCellHeightTitulo, $rowProductos['mes_proyectado'], 1, 0, 'C');
        $pdf->Cell(20, $multiCellHeightTitulo, $rowProductos['año_proyectado'], 1, 0, 'C');
        $pdf->Cell(50, $multiCellHeightTitulo, round($rowProductos['ventas_proyectadas'],2), 1, 1, 'C');

        $pdf->SetY($yStart + $multiCellHeightTitulo);
    }
} else {
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(190, 10, $pdf->encodeString('No hay productos para esta categoría'), 1, 1, 'C');
}


$pdf->output('I', 'categoriasPredictivas.pdf');