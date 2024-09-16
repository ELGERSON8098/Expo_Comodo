<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/genero_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla CATEGORIA.
 */
class GeneroData extends GeneroHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;
    private $filename = null;

    /*
     *  Métodos para validar y establecer los datos.
     */

     // Método para validar y asignar el identificador del género.
    public function setId($value)
    {
        // Se valida que el valor sea un número natural (entero positivo).
        if (Validator::validateNaturalNumber($value)) {
            $this->id_genero = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del género es incorrecto';
            return false;
        }
    }

    // Método para validar y asignar el nombre del género.
    public function setNombre($value, $min = 2, $max = 50)
    {

        // Verificar si la talla ya existe en la base de datos
        $checkSql = 'SELECT COUNT(*) as count FROM tb_generos_zapatos WHERE nombre_genero = ?';
        $checkParams = array($value);
        $checkResult = Database::getRow($checkSql, $checkParams);
    
        if ($checkResult['count'] > 0) {
            $this->data_error = 'El género  ya existe';
            return false;
        }
        // Se valida que el nombre contenga solo caracteres alfabéticos.
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    // Método para validar y asignar la imagen del género.
    public function setImagen($file, $filename = null)
    {
        if (Validator::validateImageFile($file, 1000)) {
             // Se asigna el nombre del archivo generado por el validador.
            $this->imagen = Validator::getFilename();
            return true;
            // Se guarda un mensaje de error si la validación del archivo falla.
        } elseif (Validator::getFileError()) {
            $this->data_error = Validator::getFileError();
            return false;
        } elseif ($filename) {
            $this->imagen = $filename;
            return true;
        } else {
            $this->imagen = 'default.png';
            return true;
        }
    }

    // Método para leer y establecer el nombre del archivo desde la base de datos.
    public function setFilename()
    {
          // Se intenta leer el nombre del archivo del género desde la base de datos.
        if ($data = $this->readFilename()) {
            $this->filename = $data['imagen_genero'];
            return true;
        } else {
            $this->data_error = 'Género inexistente';
            return false;
        }
    }
    /*
     *  Métodos para obtener el valor de los atributos adicionales.
     */
    public function getDataError()
    {
        return $this->data_error;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}