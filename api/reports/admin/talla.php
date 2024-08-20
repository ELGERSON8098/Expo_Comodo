<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se verifica si existe un valor para la marca, de lo contrario se muestra un mensaje.
if (isset($_GET['idTalla'])) {
    // Se incluyen las clases para la transferencia y acceso a datos.
    require_once('../../models/data/talla_data.php');
    require_once('../../models/data/producto_data.php');
    // Se instancian las entidades correspondientes.
    $talla = new tallaData;
    $producto = new ProductoData;
    // Se establece el valor de la marca, de lo contrario se muestra un mensaje.
    if ($talla->setId($_GET['idTalla']) && $producto->setTalla($_GET['idTalla'])) {
        // Se verifica si la marca existe, de lo contrario se muestra un mensaje.
        if ($rowTalla = $talla->readOne()) {
            // Se inicia el reporte con el encabezado del documento.
            $pdf->startReport('Productos que contiene la talla ' .  $pdf->encodeString($rowTalla['nombre_talla']));

            // Establecer color de fondo y texto para encabezados
            $pdf->SetFillColor(38, 15, 189); // Color de fondo
            $pdf->SetTextColor(0, 0, 0); // Negro para el texto de los encabezados
            $pdf->SetFont('Arial', 'B', 15);

            // Baja el cursor en el eje Y para dar más espacio arriba
            $pdf->SetY(65); // Ajusta el valor para bajar o subir más el título

            // Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
            if ($dataProductos = $producto->productosTalla()) {
                // Establecer el color de fondo para las celdas de encabezado
                $pdf->SetFillColor(38, 15, 189);
                $pdf->SetTextColor(255, 255, 255); // Blanco para el texto de los encabezados
                $pdf->SetFont('Times', 'B', 11);

                // Ancho total de la tabla
                $tableWidth = 70 + 60 + 30; // Suma de los anchos de las celdas

                // Calcula el inicio de la tabla para que quede centrada
                $xStart = ($pdf->GetPageWidth() - $tableWidth) / 2;

                // Mueve el cursor a la posición inicial calculada
                $pdf->SetX($xStart);

                // Imprimir encabezados centrados
                // Definir el ancho total de la tabla
                $tableWidth = 50 + 40 + 30 + 30; // Suma de los anchos de las celdas

                // Calcula el inicio de la tabla para que quede centrada
                $xStart = ($pdf->GetPageWidth() - $tableWidth) / 2;

                // Mueve el cursor a la posición inicial calculada
                $pdf->SetX($xStart);

                // Establecer el color de fondo y texto para los encabezados
                $pdf->SetFillColor(38, 15, 189);
                $pdf->SetTextColor(255, 255, 255); // Blanco para el texto de los encabezados
                $pdf->SetFont('Times', 'B', 11);

                // Imprimir encabezados centrados
                $pdf->Cell(60, 10, 'Nombre del producto', 1, 0, 'C', 1);
                $pdf->Cell(40, 10, $pdf->encodeString('Código interno'), 1, 0, 'C', 1);
                $pdf->Cell(30, 10, $pdf->encodeString('Código externo'), 1, 0, 'C', 1);
                $pdf->Cell(30, 10, $pdf->encodeString('Existencias'), 1, 1, 'C', 1);

                // Se establece la fuente para los datos de los productos y color de texto negro
                $pdf->SetTextColor(0, 0, 0); // Negro para el texto de los datos
                $pdf->SetFont('Times', '', 11);

                // Se recorren los registros fila por fila
                foreach ($dataProductos as $rowProducto) {
                    // Mueve el cursor a la posición inicial calculada
                    $pdf->SetX($xStart);

                    // Imprimir datos del producto
                    $pdf->Cell(60, 10, $pdf->encodeString($rowProducto['nombre_producto']), 1, 0, 'L');
                    $pdf->Cell(40, 10, $rowProducto['codigo_interno'], 1, 0, 'C');
                    $pdf->Cell(30, 10, $rowProducto['codigo_externo'], 1, 0, 'C');
                    $pdf->Cell(30, 10, $rowProducto['existencias'], 1, 1, 'C');
                }
            } else {
                // No hay productos para mostrar
                $pdf->SetFont('Arial', '', 10);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(190, 10, $pdf->encodeString('No hay productos para la talla'), 1, 1, 'C');
            }

            // Se llama implícitamente al método footer() y se envía el documento al navegador web.
            $pdf->output('I', 'Talla.pdf');
        } else {
            // Marca inexistente
            print('Talla inexistente');
        }
    } else {
        // Marca incorrecta
        print('Talla incorrecta');
    }
} else {
    // Debe seleccionar una marca
    print('Debe seleccionar una Talla');
}
