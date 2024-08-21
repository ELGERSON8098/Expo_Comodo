<?php
require_once('../../helpers/report.php');
require_once('../../models/data/marca_data.php');

$pdf = new Report;

// Definir el margen adicional para las páginas posteriores
$marginBottom = 30; // Ajusta este valor según tus necesidades
$tableTopY = 40; // Posición inicial de la tabla en la primera página
$leftMargin = 5; // Ajusta este valor para mover la tabla a la izquierda

// Obtiene los datos de ventas predictivas
$marcaData = new marcaData;
$ventasPredictivas = $marcaData->ReportePredictivo(); // Método que ejecuta la consulta SQL

$pdf->startReport('');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetY(54); // Ajusta el valor según sea necesario para subir el título
$pdf->Cell(0, 10, $pdf->encodeString('Reporte Predictivo de Productos Más Vendidos por Marca'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Primer salto de línea

// Función para imprimir el encabezado de la tabla
function printTableHeader($pdf, $leftMargin) {
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(7, 81, 161);
    $pdf->SetFont('Times', 'B', 11);

    $pdf->SetX($leftMargin);
    $pdf->Cell(25, 10, 'Producto', 1, 0, 'C', 1);
    $pdf->Cell(25, 10, 'Reservas', 1, 0, 'C', 1);
    $pdf->Cell(25, 10, 'Mes Reserva', 1, 0, 'C', 1);
    $pdf->Cell(25, 10, 'Ganancias', 1, 0, 'C', 1);
    $pdf->Cell(52, 10, 'Porcentaje de ventas en el mes', 1, 0, 'C', 1);
    $pdf->Cell(52, 10, $pdf->encodeString('Predicción anual mismo mes'), 1, 1, 'C', 1);
}

// Imprime el encabezado de la tabla inicial
printTableHeader($pdf, $leftMargin);

// Agrupa los datos por marca
$marcas = [];
foreach ($ventasPredictivas as $venta) {
    $marca = $venta['NombreMarca'];
    if (!isset($marcas[$marca])) {
        $marcas[$marca] = [];
    }
    $marcas[$marca][] = $venta;
}

foreach ($marcas as $marca => $productos) {
    // Verifica si se necesita una nueva página
    if ($pdf->GetY() > 250) { // Ajusta el valor según el tamaño de la página
        $pdf->AddPage(); // Agrega una nueva página si es necesario
        $pdf->SetY($tableTopY); // Ajusta la posición Y para la nueva página
        printTableHeader($pdf, $leftMargin); // Reimprime el encabezado de la tabla
    }

    // Marca como fila
    $pdf->SetFillColor(164, 197, 233); // Color de fondo para la marca
    $pdf->SetTextColor(0, 0, 0); // Color del texto
    $pdf->SetFont('Times', 'B', 12);
    $pdf->SetX($leftMargin); // Ajusta la posición X para la fila de la marca
    $pdf->Cell(204, 10, 'Nombre de la marca: ' . $marca, 1, 1, 'C', 1);
    
    // Restablece el color de fondo y el color del texto para las filas siguientes
    $pdf->SetFillColor(255, 255, 255); // Fondo blanco para las filas de productos
    $pdf->SetTextColor(0, 0, 0); // Texto negro para las filas de productos
    $pdf->SetFont('Times', '', 10);

    foreach ($productos as $producto) {
        // Verifica si se necesita una nueva página
        if ($pdf->GetY() + 10 > 250) { // Ajusta el valor según el tamaño de la página
            $pdf->AddPage(); // Agrega una nueva página si es necesario
            printTableHeader($pdf, $leftMargin); // Reimprime el encabezado de la tabla
        }

        $pdf->SetX($leftMargin); // Ajusta la posición X para las filas de productos
        $pdf->Cell(25, 10, $pdf->encodeString($producto['NombreProducto']), 1);
        $pdf->Cell(25, 10, number_format($producto['CantidadReservada']), 1, 0, 'C');
        $pdf->Cell(25, 10, $producto['MesActual'], 1, 0, 'C');
        $pdf->Cell(25, 10, '$' . number_format($producto['TotalVentasMarca'], 2), 1, 0, 'R');
        $pdf->Cell(52, 10, number_format($producto['PorcentajeVentasMarca'], 2) . '%', 1, 0, 'R');
        $pdf->Cell(52, 10, number_format($producto['PrediccionVentasSiguienteAno'], 2), 1, 1, 'R');
    }
}

// Mensaje si no hay datos
if (empty($ventasPredictivas)) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetX($leftMargin); // Ajusta la posición X para el mensaje de "no hay datos"
    $pdf->Cell(190, 10, 'No hay datos disponibles para el reporte', 1, 1, 'C');
}

$pdf->output('I', 'marcas_predictivo.pdf');
?>
