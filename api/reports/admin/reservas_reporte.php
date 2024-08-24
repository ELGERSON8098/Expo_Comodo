<?php
require_once('../../helpers/report.php');
require_once('../../models/data/reserva_data.php');
require_once('../../models/data/usuariosC_data.php'); // Asegúrate de incluir el modelo de usuarios

$pdf = new Report;

// Definir el margen adicional para las páginas posteriores
$marginBottom = 30; // Ajusta este valor según tus necesidades
$tableTopY = 40; // Posición inicial de la tabla en la primera página

// Obtiene los datos de pedidos aceptados
$pedidoData = new reservaData;
$pedidos = $pedidoData->reportePedido(); // Asegúrate de que este método retorne todos los pedidos aceptados

// Obtiene los datos de usuarios
$usuarioData = new UsuariosData;
$usuarios = $usuarioData->readAll(); // Asegúrate de que este método exista y funcione

$pdf->startReport('');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 15);
$pdf->SetY(54); // Ajusta el valor según sea necesario para subir el título
$pdf->Cell(0, 0, $pdf->encodeString('Reservas aceptadas'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Primer salto de línea

// Función para imprimir el encabezado de la tabla
function printTableHeader($pdf) {
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(7, 81, 161);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(60, 10, 'Nombre del producto', 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Fecha Reserva', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Cantidad', 1, 0, 'C', 1);
    $pdf->Cell(30, 10, 'Precio Unitario', 1, 0, 'C', 1);
    $pdf->Cell(20, 10, 'Subtotal', 1, 1, 'C', 1);
}

printTableHeader($pdf);

if ($pedidos && count($pedidos) > 0) {
    // Agrupar los pedidos por usuario
    $usuariosConReservas = array_unique(array_column($pedidos, 'id_usuario'));

    foreach ($usuariosConReservas as $usuarioId) {
        // Busca el nombre del usuario basado en el id_usuario
        $usuarioNombre = '';
        foreach ($usuarios as $usuario) {
            if ($usuario['id_usuario'] == $usuarioId) {
                $usuarioNombre = $usuario['nombre'];
                break; // Salir del bucle una vez que se encuentra el usuario
            }
        }

        // Imprimir el nombre del usuario en una fila completa
        $pdf->SetFont('Times', 'B', 11);
        $pdf->SetFillColor(164, 197, 233);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(190, 10, $pdf->encodeString('Nombre del usuario: ') . $pdf->encodeString($usuarioNombre), 1, 1, 'C', 1);

        // Imprimir los detalles de las reservas del usuario
        foreach ($pedidos as $pedido) {
            if ($pedido['id_usuario'] == $usuarioId) {
                if ($pdf->GetY() > 250) {
                    $pdf->AddPage();
                    $pdf->Ln(10);
                    printTableHeader($pdf);
                }

                $pdf->SetTextColor(0, 0, 0);
                
                // Cambiar Cell a MultiCell para el nombre del producto
                $yStart = $pdf->GetY();
                $pdf->MultiCell(60, 10, $pedido['Producto'], 1, 'L');
                $multiCellHeightTitulo = $pdf->GetY() - $yStart; // Obtener la altura ocupada por el nombre del producto

                // Ajustar la posición X para las celdas siguientes
                $pdf->SetXY($pdf->GetX() + 60, $yStart); // Ajustar la posición X después de MultiCell

                // Imprimir los otros campos usando la misma altura que el nombre del producto
                $pdf->Cell(50, $multiCellHeightTitulo, $pedido['FechaReserva'], 1);
                $pdf->Cell(30, $multiCellHeightTitulo, $pedido['CantidadLibros'], 1, 0, 'C');
                $pdf->Cell(30, $multiCellHeightTitulo, number_format($pedido['PrecioUnitario'], 2), 1, 0, 'R');
                $pdf->Cell(20, $multiCellHeightTitulo, number_format($pedido['Subtotal'], 2), 1, 1, 'R');

                // Ajustar la posición Y para la siguiente fila
                $pdf->SetY($yStart + $multiCellHeightTitulo);
            }
        }

        // Salto de línea entre usuarios
        $pdf->Ln(0);
    }
} else {
    // Mensaje si no hay reservas aceptadas
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(190, 10, 'No hay reservas aceptadas para este usuario', 1, 1, 'C');
}

$pdf->output('I', 'pedidos_aceptados.pdf');
?>