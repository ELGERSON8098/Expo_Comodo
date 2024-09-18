<?php
require_once('../../helpers/report.php');
require_once('../../models/data/categoria_data.php');
require_once('../../models/data/producto_data.php');

$pdf = new Report;

// Definir el margen adicional para las páginas posteriores
$marginBottom = 30; // Ajusta este valor según tus necesidades
$tableTopY = 40; // Posición inicial de la tabla en la primera página

$pdf->startReport('');
$pdf->SetTextColor(0, 0, 0); // Establece el color del texto a blanco
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetY(54); // Ajusta el valor según sea necesario para subir el título
$pdf->Cell(0, 0, $pdf->encodeString('Productos por categoría'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Primer salto de línea


$categoria = new CategoriaData;

// Función para imprimir el encabezado de la tabla
function printTableHeader($pdf) {
    
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(38, 15, 189); 
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(70, 10, 'Nombre del producto', 1, 0, 'C', 1);
    $pdf->Cell(60, 10, $pdf->encodeString('Código'), 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Existencias', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Precio (US$)', 1, 1, 'C', 1);
}

if ($dataCategoria = $categoria->readAll()) {
    printTableHeader($pdf);

    foreach ($dataCategoria as $rowCategoria) {
        $pdf->SetFont('Times', 'B', 11);
        $pdf->SetFillColor(164, 197, 233 );
        $pdf->SetTextColor(0, 0, 0 );
        $pdf->Cell(190, 10, $pdf->encodeString('Nombre de la categoría: ') . $pdf->encodeString($rowCategoria['nombre_categoria']), 1, 1, 'C', 1);

        $productos = new ProductoData;

        if ($productos->setCategoria($rowCategoria['id_categoria'])) {
            if ($dataProductos = $productos->productosCategoria()) {
                foreach ($dataProductos as $rowProductos) {
                    // Verifica si se necesita una nueva página
                    if ($pdf->GetY() > 250) { // Ajusta el valor según el tamaño de la página
                        $pdf->AddPage('p', 'letter'); // Agrega una nueva página si es necesario
                        // Reimprime el título y el encabezado de la tabla
                        $pdf->SetFont('Arial', '', 15);
                        $pdf->Cell(0, 10, $pdf->encodeString('Productos por categoría'), 0, 1, 'C');
                        $pdf->Ln(10); // Salto de línea
                        printTableHeader($pdf);

                        // Establece la posición Y más abajo para las páginas siguientes
                        $pdf->SetY($tableTopY + $marginBottom); // Ajusta 40 al valor inicial del espacio deseado
                    } else {
                        // Asegúrate de que la posición Y no se sobreponga con el final de la página
                        if ($pdf->GetY() > 250) {
                            $pdf->SetY($tableTopY + $marginBottom); // Ajusta la posición Y para evitar sobreposiciones
                        }
                    }

                    $yStart = $pdf->GetY();
                    $xStart = $pdf->GetX();

                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->MultiCell(70, 10, $pdf->encodeString($rowProductos['nombre_producto']), 1, 'L');
                    $multiCellHeightTitulo = $pdf->GetY() - $yStart;

                    $pdf->SetXY($xStart + 70, $yStart);

                    $pdf->Cell(60, $multiCellHeightTitulo, $rowProductos['codigo_interno'], 1, 0, 'C');
                    $pdf->Cell(20, $multiCellHeightTitulo, $rowProductos['existencias'], 1, 0, 'C');
                    $pdf->Cell(40, $multiCellHeightTitulo, $rowProductos['precio'], 1, 1, 'C');

                    $pdf->SetY($yStart + $multiCellHeightTitulo);
                }
            } else {
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10, $pdf->encodeString('No hay productos para esta categoría'), 1, 1, 'C');
            }
        } else {
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(190, 10, 'Categoría incorrecta o inexistente', 1, 1, 'C');
        }
    }
} else {
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(190, 10, 'No hay categorías para mostrar', 1, 1, 'C');
}

$pdf->output('I', 'categorias.pdf');
