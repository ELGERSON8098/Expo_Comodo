<?php
require_once('../../helpers/report.php');
require_once('../../models/data/marca_data.php');


$pdf = new Report;

// Definir el margen adicional para las páginas posteriores
$marginBottom = 30; // Ajusta este valor según tus necesidades
$tableTopY = 40; // Posición inicial de la tabla en la primera página
$leftMargin = 7; // Ajusta este valor para mover la tabla a la izquierda

// Obtiene los datos de ventas predictivas
$marcaData = new marcaData;
$ventasPredictivas = $marcaData->ReportePredictivo(); // Método que ejecuta la consulta SQL para la primera tabla

// Obtiene los datos de ventas actuales
$ventasActuales = $marcaData->ReportePredictivo(); // Método diferente para la segunda tabla

$pdf->startReport('');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetY(54); // Ajusta el valor según sea necesario para subir el título
$pdf->Cell(0, 10, $pdf->encodeString('Reporte predictivo de productos más vendidos por marca'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Primer salto de línea

// Función para imprimir el encabezado de la tabla
function printTableHeader($pdf, $leftMargin, $columnHeaders)
{
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(7, 81, 161);
    $pdf->SetFont('Times', 'B', 11);

    $pdf->SetX($leftMargin);
    foreach ($columnHeaders as $header) {
        $pdf->Cell($header['width'], 10, $pdf->encodeString($header['name']), 1, 0, 'C', 1);
    }
    $pdf->Ln();
}

// Función para imprimir la tabla de ventas predictivas
function printTableOne($pdf, $data, $leftMargin, $tableTopY)
{
    // Define los encabezados de la tabla
    $columnHeaders = [
        ['name' => 'Producto', 'width' => 70],
        ['name' => 'Reservas', 'width' => 35],
        ['name' => 'Mes reserva', 'width' => 40],
        ['name' => 'Ganancias obtenidas', 'width' => 55],
    ];

    // Imprime el encabezado de la tabla
    printTableHeader($pdf, $leftMargin, $columnHeaders);

    $foundData = false; // Variable para verificar si hay datos para alguna marca

    // Agrupa los datos por marca
    $marcas = [];
    foreach ($data as $venta) {
        $marca = $pdf->encodeString($venta['NombreMarca']);
        if (!isset($marcas[$marca])) {
            $marcas[$marca] = [];
        }
        $marcas[$marca][] = $venta;
    }

    foreach ($marcas as $marca => $productos) {
        // Verifica si se necesita una nueva página
        if ($pdf->GetY() > 250) { // Ajusta el valor según el tamaño de la página
            $pdf->AddPage('p', 'letter'); // Agrega una nueva página si es necesario
            $pdf->SetY($tableTopY); // Ajusta la posición Y para la nueva página
            printTableHeader($pdf, $leftMargin, $columnHeaders); // Reimprime el encabezado de la tabla
        }

        if (empty($productos)) {
            // Si no hay productos para esta marca, muestra el mensaje correspondiente
            $pdf->SetFillColor(255, 255, 255); // Fondo blanco
            $pdf->SetTextColor(0, 0, 0); // Texto negro
            $pdf->SetFont('Times', '', 10);
            $pdf->SetX($leftMargin); // Ajusta la posición X para el mensaje de "no hay datos"
            $pdf->Cell(190, 10, 'La marca ' . $marca . ' no contiene ninguna reserva actualmente', 1, 1, 'C');
        } else {
            $foundData = true; // Hay datos para al menos una marca
            // Marca como fila
            $pdf->SetFillColor(164, 197, 233); // Color de fondo para la marca
            $pdf->SetTextColor(0, 0, 0); // Color del texto
            $pdf->SetFont('Times', 'B', 12);
            $pdf->SetX($leftMargin); // Ajusta la posición X para la fila de la marca
            $pdf->Cell(200, 10, 'Nombre de la marca: ' . $marca, 1, 1, 'C', 1);

            // Restablece el color de fondo y el color del texto para las filas siguientes
            $pdf->SetFillColor(255, 255, 255); // Fondo blanco para las filas de productos
            $pdf->SetTextColor(0, 0, 0); // Texto negro para las filas de productos
            $pdf->SetFont('Times', '', 10);

            foreach ($productos as $producto) {
                // Verifica si se necesita una nueva página
                if ($pdf->GetY() + 10 > 250) { // Ajusta el valor según el tamaño de la página
                    $pdf->AddPage(); // Agrega una nueva página si es necesario
                    printTableHeader($pdf, $leftMargin, $columnHeaders); // Reimprime el encabezado de la tabla
                }

                $pdf->SetX($leftMargin); // Ajusta la posición X para las filas de productos
                $pdf->Cell(70, 10, $pdf->encodeString($producto['NombreProducto']), 1);
                $pdf->Cell(35, 10, number_format($producto['CantidadReservada']), 1, 0, 'C');
                $pdf->Cell(40, 10, $producto['MesActual'], 1, 0, 'C');
                $pdf->Cell(55, 10, '$' . number_format($producto['TotalVentasMarca'], 2), 1, 1, 'R');
            }
        }
    }

    // Mensaje si no hay datos en general
    if (!$foundData) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX($leftMargin); // Ajusta la posición X para el mensaje de "no hay datos"
        $pdf->Cell(190, 10, 'No hay datos disponibles para el reporte', 1, 1, 'C');
    }
}

// Función para imprimir la tabla de ventas actuales (con el mismo diseño que la primera)
function printTableTwo($pdf, $data, $leftMargin, $tableTopY)
{
    // Ajuste del margen izquierdo para mover la tabla hacia la izquierda
    $leftMargin = 5; // Ajusta este valor según cuánto deseas mover la tabla a la izquierda

    // Define los encabezados de la tabla con anchos fijos
    $columnHeaders = [
        ['name' => 'Producto', 'width' => 65],
        ['name' => 'Porcentaje mes actual', 'width' => 38],
        ['name' => 'Predicción de ganancias', 'width' => 42],
        ['name' => 'Conclusión', 'width' => 60] // Ancho ajustado para la celda 'Conclusión'
    ];

    // Imprime el encabezado de la tabla
    printTableHeader($pdf, $leftMargin, $columnHeaders);

    $foundData = false; // Variable para verificar si hay datos para alguna marca

    // Agrupa los datos por marca
    $marcas = [];
    foreach ($data as $venta) {
        $marca = $pdf->encodeString($venta['NombreMarca']);
        if (!isset($marcas[$marca])) {
            $marcas[$marca] = [];
        }
        $marcas[$marca][] = $venta;
    }

    foreach ($marcas as $marca => $productos) {
        // Verifica si se necesita una nueva página
        if ($pdf->GetY() > 220) { // Ajusta el valor según el tamaño de la página
            $pdf->AddPage('p', 'letter'); // Agrega una nueva página si es necesario
            $pdf->SetY($tableTopY); // Ajusta la posición Y para la nueva página
            printTableHeader($pdf, $leftMargin, $columnHeaders); // Reimprime el encabezado de la tabla
        }

        if (empty($productos)) {
            // Si no hay productos para esta marca, muestra el mensaje correspondiente
            $pdf->SetFillColor(255, 255, 255); // Fondo blanco
            $pdf->SetTextColor(0, 0, 0); // Texto negro
            $pdf->SetFont('Times', '', 10);
            $pdf->SetX($leftMargin); // Ajusta la posición X para el mensaje de "no hay datos"
            $pdf->Cell(array_sum(array_column($columnHeaders, 'width')), 10, 'La marca ' . $marca . ' no contiene ninguna reserva actualmente', 1, 1, 'C');
        } else {
            $foundData = true; // Hay datos para al menos una marca
            // Marca como fila
            $pdf->SetFillColor(164, 197, 233); // Color de fondo para la marca
            $pdf->SetTextColor(0, 0, 0); // Color del texto
            $pdf->SetFont('Times', 'B', 12);
            $pdf->SetX($leftMargin); // Ajusta la posición X para la fila de la marca
            $pdf->Cell(array_sum(array_column($columnHeaders, 'width')), 10, 'Nombre de la marca: ' . $marca, 1, 1, 'C', 1);

            // Restablece el color de fondo y el color del texto para las filas siguientes
            $pdf->SetFillColor(255, 255, 255); // Fondo blanco para las filas de productos
            $pdf->SetTextColor(0, 0, 0); // Texto negro para las filas de productos
            $pdf->SetFont('Times', '', 10);

            foreach ($productos as $producto) {
                // Verifica si se necesita una nueva página
                if ($pdf->GetY() + 10 > 250) { // Ajusta el valor según el tamaño de la página
                    $pdf->AddPage(); // Agrega una nueva página si es necesario
                    printTableHeader($pdf, $leftMargin, $columnHeaders); // Reimprime el encabezado de la tabla
                }

                $pdf->SetX($leftMargin); // Ajusta la posición X para las filas de productos
                
                // Imprimir las celdas con ancho fijo
                $pdf->Cell($columnHeaders[0]['width'], 10, $pdf->encodeString($producto['NombreProducto']), 1);
                $pdf->Cell($columnHeaders[1]['width'], 10, number_format($producto['PorcentajeVentasMarca'], 2) . '%', 1, 0, 'R');
                $pdf->Cell($columnHeaders[2]['width'], 10, '$' . number_format($producto['PrediccionVentasSiguienteMes'], 2), 1, 0, 'R');
                $pdf->Cell($columnHeaders[3]['width'], 10, $pdf->encodeString($producto['PorcentajeYMensaje']), 1, 1);
            }
        }
    }

    // Mensaje si no hay datos en general
    if (!$foundData) {
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetX($leftMargin); // Ajusta la posición X para el mensaje de "no hay datos"
        $pdf->Cell(190, 10, 'No hay datos disponibles para el reporte', 1, 1, 'C');
    }
}



// Imprime la primera tabla
printTableOne($pdf, $ventasPredictivas, $leftMargin, $tableTopY);

// Agrega un salto de página para la segunda tabla
$pdf->AddPage('p', 'letter');
$pdf->SetY($tableTopY);

// Imprime la segunda tabla
printTableTwo($pdf, $ventasActuales, $leftMargin, $tableTopY);

$pdf->Output('I', 'reporte.pdf');
