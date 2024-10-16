<?php

require_once('../../helpers/report.php');
require_once('../../models/data/usuariosC_data.php');

$pdf = new Report;

$pdf->startReport('Usuarios registrados activos e inactivos');

$usuario = new UsuariosData;

$pdf->Ln(10); // Primer salto de línea

// Obtener todos los usuarios
$dataUsuario = $usuario->usuariosRegistrados();

if ($dataUsuario) {
    $pdf->SetFillColor(27, 88, 169);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(50, 10, 'Nombre usuario', 1, 0, 'C', 1);
    $pdf->Cell(60, 10, $pdf->encodeString('Correo'), 1, 0, 'C', 1);
    $pdf->Cell(40, 10, $pdf->encodeString('Dui'), 1, 0, 'C', 1);
    $pdf->Cell(40, 10, $pdf->encodeString('Telefono'), 1, 1, 'C', 1);


    // Usar un array para agrupar usuarios por estado
    $usuariosActivos = [];
    $usuariosInactivos = [];

    // Agrupar usuarios por estado
    foreach ($dataUsuario as $rowUsuario) {
        if ($rowUsuario['estado_cliente'] === "Activo") {
            $usuariosActivos[] = $rowUsuario;
        } else {
            $usuariosInactivos[] = $rowUsuario;
        }
    }

    // Imprimir usuarios activos
    if (!empty($usuariosActivos)) {
        $pdf->SetFont('Times', 'B', 11);
        $pdf->SetFillColor(164, 197, 233 );
        $pdf->Cell(190, 10, 'Estado: Activos', 1, 1, 'C', 1);
        foreach ($usuariosActivos as $rowUser) {
            $yStart = $pdf->GetY();
            $xStart = $pdf->GetX();

            // Imprimir nombre de usuario
            $pdf->MultiCell(50, 10, $pdf->encodeString($rowUser['usuario']), 1, 'L');
            $multiCellHeightNombre = $pdf->GetY() - $yStart;

            $pdf->SetXY($xStart + 50, $yStart);

            // Imprimir correo de usuario
            $pdf->MultiCell(60, 10, $pdf->encodeString($rowUser['correo']), 1, 'L');
            $multiCellHeightCorreo = $pdf->GetY() - $yStart;

            $pdf->SetXY($xStart + 110, $yStart); // Ajustar posición X para DUI

            // Imprimir DUI
            $pdf->MultiCell(40, 10, $pdf->encodeString($rowUser['dui_cliente']), 1, 'L');
            $multiCellHeightDui = $pdf->GetY() - $yStart;

            $pdf->SetXY($xStart + 150, $yStart); // Ajustar posición X para Teléfono

            // Imprimir Teléfono
            $pdf->MultiCell(40, 10, $pdf->encodeString($rowUser['telefono']), 1, 'L');
            $multiCellHeightTelefono = $pdf->GetY() - $yStart;

            $pdf->SetXY($xStart + 160, $yStart); // Ajustar posición X para Estado

          
            // Ajustar la posición Y para la siguiente fila
            $pdf->SetY($yStart + max($multiCellHeightNombre, $multiCellHeightCorreo, $multiCellHeightDui, $multiCellHeightTelefono));
        }
    }

    // Imprimir usuarios inactivos
    if (!empty($usuariosInactivos)) {
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(190, 10, 'Estado: Inactivos', 1, 1, 'C', 1);
        foreach ($usuariosInactivos as $rowUser) {
            $yStart = $pdf->GetY();
            $xStart = $pdf->GetX();

           // Imprimir nombre de usuario
           $pdf->MultiCell(50, 10, $pdf->encodeString($rowUser['usuario']), 1, 'L');
           $multiCellHeightNombre = $pdf->GetY() - $yStart;

           $pdf->SetXY($xStart + 50, $yStart);

           // Imprimir correo de usuario
           $pdf->MultiCell(60, 10, $pdf->encodeString($rowUser['correo']), 1, 'L');
           $multiCellHeightCorreo = $pdf->GetY() - $yStart;

           $pdf->SetXY($xStart + 110, $yStart); // Ajustar posición X para DUI

           // Imprimir DUI
           $pdf->MultiCell(40, 10, $pdf->encodeString($rowUser['dui_cliente']), 1, 'L');
           $multiCellHeightDui = $pdf->GetY() - $yStart;

           $pdf->SetXY($xStart + 150, $yStart); // Ajustar posición X para Teléfono

           // Imprimir Teléfono
           $pdf->MultiCell(40, 10, $pdf->encodeString($rowUser['telefono']), 1, 'L');
           $multiCellHeightTelefono = $pdf->GetY() - $yStart;

           $pdf->SetXY($xStart + 160, $yStart); // Ajustar posición X para Estado

            // Ajustar la posición Y para la siguiente fila
            $pdf->SetY($yStart + max($multiCellHeightNombre, $multiCellHeightCorreo, $multiCellHeightDui, $multiCellHeightTelefono));
        }
    }
} else {
    $pdf->SetFont('Arial', '', 10);
    $pdf->Cell(190, 10, 'No hay usuarios para mostrar', 1, 1, 'C');
}

$pdf->output('I', 'Usuario.pdf');