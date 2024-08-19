<?php

require_once('../../helpers/report.php');
require_once('../../models/data/usuariosC_data.php');

$pdf = new Report;

// Definir el margen adicional para las páginas posteriores
$marginBottom = 30; // Ajusta este valor según tus necesidades
$tableTopY = 40; // Posición inicial de la tabla en la primera página

$pdf->startReport('');
$pdf->SetTextColor(0, 0, 0); // Establece el color del texto a blanco
$pdf->SetFont('Arial', '', 15);
$pdf->SetY(45); // Ajusta el valor según sea necesario para subir el título
$pdf->Cell(0, 3, $pdf->encodeString('Usuarios registrados'), 0, 1, 'C'); // Imprime el título en la posición ajustada

$pdf->Ln(10); // Salto de línea para espacio

// Crear una instancia del modelo de usuarios
$usuario = new UsuariosData;

// Función para imprimir el encabezado de la tabla
function printTableHeader($pdf)
{

    $pdf->SetTextColor(225, 225, 225);
    $pdf->SetFillColor(27, 88, 169);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(40, 10, 'Nombre del usuario', 1, 0, 'C', 1);
    $pdf->Cell(50, 10, 'Correo', 1, 0, 'C', 1);
    $pdf->Cell(40, 10, 'Dui', 1, 0, 'C', 1);

    $pdf->Cell(40, 10, 'Telefono', 1, 1, 'C', 1);
}

// Obtener todos los usuarios
if ($dataClientes = $usuario->readAll()) {
    printTableHeader($pdf); // Imprimir el encabezado de la tabla

    foreach ($dataClientes as $rowClientes) {
        // Verifica si se necesita una nueva página
        if ($pdf->GetY() > 250) { // Ajusta el valor según el tamaño de la página
            $pdf->AddPage(); // Agrega una nueva página si es necesario
            // Reimprime el título y el encabezado de la tabla
            $pdf->SetFont('Arial', '', 15);
            $pdf->Cell(0, 10, $pdf->encodeString('Usuarios registrados'), 0, 1, 'C');
            $pdf->Ln(10); // Salto de línea
            printTableHeader($pdf); // Imprimir el encabezado de la tabla nuevamente
        }

        // Guardar posición Y actual
        $yStart = $pdf->GetY();
        $xStart = $pdf->GetX();

        // Imprimir nombre de usuario
        $pdf->SetTextColor(0, 0, 0); // Establecer color del texto a negro
        $pdf->Cell(40, 10, $pdf->encodeString($rowClientes['usuario']), 1, 0, 'L');

        // Imprimir correo de usuario
        $pdf->Cell(50, 10, $pdf->encodeString($rowClientes['correo']), 1, 0, 'L');

        // Imprimir DUI
        $pdf->Cell(40, 10, $pdf->encodeString($rowClientes['dui_cliente']), 1, 0, 'L');


        // Imprimir teléfono
        $pdf->Cell(40, 10, $pdf->encodeString($rowClientes['telefono']), 1, 1, 'L');


        // Asegurarse de que la posición Y no se sobreponga con el final de la página
        if ($pdf->GetY() > 250) {
            $pdf->AddPage(); // Agrega una nueva página si es necesario
            printTableHeader($pdf); // Imprimir el encabezado de la tabla nuevamente
        }
    }
} else {
    // Mensaje si no hay usuarios para mostrar
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(0, 0, 0); // Establecer color del texto a negro
    $pdf->Cell(190, 10, 'No hay usuarios para mostrar', 1, 1, 'C');
}

// Generar el PDF
$pdf->output('I', 'Usuario.pdf');
