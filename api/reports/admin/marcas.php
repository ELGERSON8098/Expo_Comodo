<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se verifica si existe un valor para la marca, de lo contrario se muestra un mensaje.
if (isset($_GET['idMarca'])) {
    // Se incluyen las clases para la transferencia y acceso a datos.
    require_once('../../models/data/marca_data.php');
    require_once('../../models/data/producto_data.php');
    // Se instancian las entidades correspondientes.
    $marca = new marcaData;
    $producto = new ProductoData;
    // Se establece el valor de la marca, de lo contrario se muestra un mensaje.
    if ($marca->setId($_GET['idMarca']) && $producto->setMarca($_GET['idMarca'])) {
        // Se verifica si la marca existe, de lo contrario se muestra un mensaje.
        if ($rowMarca = $marca->readOne()) {
            // Se inicia el reporte con el encabezado del documento.
            $pdf->startReport('Productos de la marca: ' .  $pdf->encodeString($rowMarca['marca']));

            // Establecer color de fondo y texto para encabezados
            $pdf->SetFillColor(38, 15, 189); // Color de fondo
            $pdf->SetTextColor(0, 0, 0); // Blanco para el texto de los encabezados
            $pdf->SetFont('Arial', 'B', 15);

            // Baja el cursor en el eje Y para dar más espacio arriba
            $pdf->SetY(65); // Ajusta el valor para bajar o subir más el título

            //$pdf->Cell(0, 10, $pdf->encodeString('Productos de la marca ' . $rowMarca['marca']), 0, 1, 'C'); // Título centrado

            // Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
            if ($dataProductos = $producto->productosMarca()) {
                // Establecer el color de fondo para las celdas de encabezado
                $pdf->SetFillColor(38, 15, 189);
                $pdf->SetTextColor(255, 255, 255); // Blanco para el texto de los encabezados
                $pdf->SetFont('Times', 'B', 11);
                $pdf->Cell(70, 10, 'Nombre del producto', 1, 0, 'C', 1);
                $pdf->Cell(60, 10, $pdf->encodeString('Código interno'), 1, 0, 'C', 1);
                $pdf->Cell(30, 10, $pdf->encodeString('Código externo'), 1, 0, 'C', 1);
                $pdf->Cell(30, 10, 'Existencia', 1, 1, 'C', 1);

                // Se establece la fuente para los datos de los productos y color de texto negro.
                $pdf->SetTextColor(0, 0, 0); // Negro para el texto de los datos
                $pdf->SetFont('Times', '', 11);

                // Se recorren los registros fila por fila.
                foreach ($dataProductos as $rowProducto) {
                    // Verifica si se necesita una nueva página
                    if ($pdf->GetY() > 250) {
                        $pdf->AddPage('p', 'letter');
                        $pdf->Ln(10);
                        // Reimprimir encabezado
                        $pdf->SetFillColor(38, 15, 189);
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->SetFont('Times', 'B', 11);
                        $pdf->Cell(70, 10, 'Nombre del producto', 1, 0, 'C', 1);
                        $pdf->Cell(60, 10, 'Código interno', 1, 0, 'C', 1);
                        $pdf->Cell(30, 10, 'Código externo', 1, 0, 'C', 1);
                        $pdf->Cell(30, 10, 'Existencia', 1, 1, 'C', 1);
                    }

                    $yStart = $pdf->GetY();
                    $xStart = $pdf->GetX();

                    // Imprimir datos del producto
                    $pdf->MultiCell(70, 10, $pdf->encodeString($rowProducto['nombre_producto']), 1, 'L');
                    $multiCellHeightTitulo = $pdf->GetY() - $yStart;

                    $pdf->SetXY($xStart + 70, $yStart);
                    $pdf->Cell(60, $multiCellHeightTitulo, $rowProducto['codigo_interno'], 1, 0, 'C');
                    $pdf->Cell(30, $multiCellHeightTitulo, $rowProducto['referencia_proveedor'], 1, 0, 'C');
                    $pdf->Cell(30, $multiCellHeightTitulo, $rowProducto['existencias'], 1, 1, 'C');
                }
            } else {
                // No hay productos para mostrar
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10, $pdf->encodeString('No hay productos para la marca'), 1, 1, 'C');
            }

            // Se llama implícitamente al método footer() y se envía el documento al navegador web.
            $pdf->output('I', 'Marca.pdf');
        } else {
            // Marca inexistente
            print('Marca inexistente');
        }
    } else {
        // Marca incorrecta
        print('Marca incorrecta');
    }
} else {
    // Debe seleccionar una marca
    print('Debe seleccionar una marca');
}
