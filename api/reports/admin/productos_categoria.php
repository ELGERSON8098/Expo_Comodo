<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');
require_once('../../models/data/categoria_data.php');
require_once('../../models/data/producto_data.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se inicia el reporte con el título correspondiente.

$pdf->startReport('');
$pdf->SetTextColor(255, 255, 255); // Establece el color del texto a blanco
$pdf->SetFont('Arial', '', 15);
$pdf->Cell(0, 10, $pdf->encodeString('Libros por clasificación'), 0, 1, 'C'); // Imprime el título en la posición ajustada


// Se verifica si existe un valor para la categoría, de lo contrario se muestra un mensaje.
if (isset($_GET['idCategoria'])) {
    // Se instancian las entidades correspondientes.
    $categoria = new CategoriaData;
    $producto = new ProductoData;

    // Se establece el valor de la categoría.
    if ($categoria->setId($_GET['idCategoria'])) {
        // Se verifica si la categoría existe.
        if ($rowCategoria = $categoria->readOne()) {
            // Se establece la fuente y color para los encabezados.
            $pdf->SetFillColor(27, 88, 169);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(126, 10, 'Nombre', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Descripción', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Existencias', 1, 0, 'C', 1);
            $pdf->Cell(30, 10, 'Precio', 1, 1, 'C', 1);

            // Se establece la fuente para los datos de los productos.
            $pdf->SetFont('Arial', '', 11);

            // Se obtienen los productos de la categoría.
            if ($dataProductos = $producto->productosCategoria($rowCategoria['id_categoria'])) {
                // Se recorren los registros fila por fila.
                foreach ($dataProductos as $rowProducto) {
                    // Se imprimen las celdas con los datos de los productos.
                    $pdf->Cell(126, 10, $pdf->encodeString($rowProducto['nombre_producto']), 1, 0);
                    $pdf->Cell(30, 10, $rowProducto['descripcion'], 1, 0);
                    $pdf->Cell(30, 10, $rowProducto['existencias'], 1, 0);
                    $pdf->Cell(30, 10, $rowProducto['precio'], 1, 1);
                }
            } else {
                // Mensaje si no hay productos para la categoría
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(0, 10, $pdf->encodeString('No hay productos para la categoría'), 1, 1);
            }
        } else {
            // Mensaje si la categoría no existe
            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(190, 10, 'Categoría inexistente', 1, 1, 'C');
        }
    } else {
        // Mensaje si la categoría es incorrecta
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(190, 10, 'Categoría incorrecta', 1, 1, 'C');
    }
} 
else {
    // Mensaje si la categoría no existe
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0); 
    $pdf->Cell(190, 10, 'No existen categorias', 1, 1, 'C');
}
// Se llama implícitamente al método footer() y se envía el documento al navegador web.
$pdf->output('I', 'categoria.pdf');
?>