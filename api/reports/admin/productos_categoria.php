<?php
require_once('../../helpers/report.php');
require_once('../../models/data/categoria_data.php');
require_once('../../models/data/producto_data.php');

$pdf = new Report;

$pdf->startReport('');
$pdf->SetTextColor(255, 255, 255); // Establece el color del texto a blanco
$pdf->SetFont('Arial', '', 15);
$pdf->Cell(0, 10, $pdf->encodeString('Productos por categoría'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Primer salto de línea
$pdf->Ln(10); // Segundo salto de línea

$categoria = new CategoriaData;
if ($dataCategoria = $categoria->readAll()) {
    $pdf->SetFillColor(27, 88, 169);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(70, 10, 'Nombre del producto', 1, 0, 'C', 1);
    $pdf->Cell(60, 10, 'Codigo', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Existencias', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Precio (US$)', 1, 1, 'C', 1);

    foreach ($dataCategoria as $rowCategoria) {
        $pdf->SetFont('Times', 'B', 11);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(190, 10, $pdf->encodeString('Nombre de la categoría: ') . $pdf->encodeString($rowCategoria['nombre_categoria']), 1, 1, 'C', 1);

        $productos = new ProductoData;

        if ($productos->setCategoria($rowCategoria['id_categoria'])) {
            if ($dataProductos = $productos->productosCategoria()) {
                foreach ($dataProductos as $rowProductos) {
                    // Verifica si se necesita una nueva página
                    if ($pdf->GetY() > 250) { // Ajusta el valor según el tamaño de la página
                        $pdf->AddPage(); // Agrega una nueva página si es necesario
                        // Reimprime el encabezado de la tabla
                        $pdf->SetFillColor(27, 88, 169);
                        $pdf->SetFont('Times', 'B', 11);
                        $pdf->Cell(70, 10, 'Nombre del producto', 1, 0, 'C', 1);
                        $pdf->Cell(60, 10, 'Codigo', 1, 0, 'C', 1);
                        $pdf->Cell(20, 10, 'Existencias', 1, 0, 'C', 1);
                        $pdf->Cell(40, 10, 'Precio (US$)', 1, 1, 'C', 1);
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
?>
