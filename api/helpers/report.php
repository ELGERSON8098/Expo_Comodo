<?php
// Se incluye la clase para generar archivos PDF.
require_once('../../libraries/fpdf185/fpdf.php');

/*
*   Clase para definir las plantillas de los reportes del sitio privado.
*   Para más información http://www.fpdf.org/
*/
class Report extends FPDF
{
    // Constante para definir la ruta de las vistas del sitio privado.
    const CLIENT_URL = 'https://comodosv.site/Expo_Comodo/views/admin/';
    // Propiedad para guardar el título del reporte.
    private $title = null;

    /*
    *   Método para iniciar el reporte con el encabezado del documento.
    *   Parámetros: $title (título del reporte).
    *   Retorno: ninguno.
    */
    public function startReport($title)
    {
        // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en los reportes.
        session_start();
        // Se verifica si un administrador ha iniciado sesión para generar el documento, de lo contrario se direcciona a la página web principal.
        if (isset($_SESSION['idAdministrador'])) {
            // Se asigna el título del documento a la propiedad de la clase.
            $this->title = $title;
            // Se establece el título del documento (true = utf-8).
            $this->setTitle('Expo_Comodo - Reporte', true);
            // Se establecen los margenes del documento (izquierdo, superior y derecho).
            $this->setMargins(15, 15, 15);
            // Se añade una nueva página al documento con orientación vertical y formato carta, llamando implícitamente al método header()
            $this->addPage('p', 'letter');
            // Se define un alias para el número total de páginas que se muestra en el pie del documento.
            $this->aliasNbPages();
        } else {
            header('location:' . self::CLIENT_URL);
        }
    }

    /*
    *   Método para codificar una cadena de alfabeto español a UTF-8.
    *   Parámetros: $string (cadena).
    *   Retorno: cadena convertida.
    */
    public function encodeString($string)
    {
        return mb_convert_encoding($string, 'ISO-8859-1', 'utf-8');
    }

    /*
    *   Se sobrescribe el método de la librería para establecer la plantilla del encabezado de los reportes.
    *   Se llama automáticamente en el método addPage()
    */
    public function header()
    {
        // Se establece el logo.
        $this->image('../../images/ReporteComodo$.png', 0, 0, 215.9, 279.4);
        $this->image('../../images/logoComodos.png', 5, 3, 42);

        // Establecer fuente y tamaño para el texto debajo de la imagen.
        $this->SetFont('Arial', 'B', 20);
        $this->SetTextColor(255, 255, 255);
        $this->Cell(0, -5, 'COMODO$', 0, 1, 'C'); // Agrega el texto
        // Se agrega un salto de línea para mostrar el contenido principal del documento.
        $this->ln(30);
        // Se ubica la fecha y hora del servidor.
        $this->setFont('Arial', 'B', 10); // Cambiado a negrita ('B')
        $this->SetTextColor(0, 0, 0); // Establece el color del texto a negro
        // Centrar la fecha y hora
        $this->cell(0, 10, 'Fecha/Hora: ' . date('d-m-Y H:i:s'), 0, 1, 'C'); // Cambiado a negrita y centrado
        // Se ubica el título.
        $this->setFont('Arial', 'B', 15);
        $this->cell(0, 5, $this->encodeString($this->title), 0, 1, 'C');
    }

    /*
    *   Se sobrescribe el método de la librería para establecer la plantilla del pie de los reportes.
    *   Se llama automáticamente en el método output()
    */
    public function footer()
    {
        $this->setFont('Arial', 'I', 10);
        $this->setY(-15);

        $this->SetTextColor(255, 255, 255); // Establece el color del texto a negro
        $this->Cell(120, 18, "Reporte generado por el usuario : ' " . $this->encodeString($_SESSION['aliasAdministrador']) . " ' ", 0, 0, 'C');

        // Se establece la posición para el número de página (a 15 milímetros del final).
        $this->SetY(-15);
        // Establece el color del texto a negro
        $this->SetTextColor(0, 0, 0); // Color negro en formato RGB

        // Se establece la fuente para el número de página.
        $this->setFont('Arial', 'I', 8);

        // Se imprime una celda con el número de página.
        $this->SetTextColor(255, 255, 255); // Establece el color del texto a negro
        $this->cell(380, 17, $this->encodeString('Página ') . $this->pageNo() . '/{nb}', 0, 0, 'C');
    }
}
