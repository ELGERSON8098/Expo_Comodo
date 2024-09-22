<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/categoria_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla CATEGORIA.
 */
class CategoriaData extends CategoriaHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;
    private $filename = null;

    /*
     *  Métodos para validar y establecer los datos.
     */
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la categoría es incorrecto';
            return false;
        }
    }
    public function setNombre($value, $min = 2, $max = 50)
    {
        // Verificar si la categoría ya existe en la base de datos, excluyendo el registro actual
        if ($this->id) {
            $checkSql = 'SELECT COUNT(*) as count FROM tb_categorias WHERE nombre_categoria = ? AND id_categoria != ?';
            $checkParams = array($value, $this->id);
        } else {
            $checkSql = 'SELECT COUNT(*) as count FROM tb_categorias WHERE nombre_categoria = ?';
            $checkParams = array($value);
        }
    
        $checkResult = Database::getRow($checkSql, $checkParams);
    
        if ($checkResult['count'] > 0) {
            $this->data_error = 'La categoría ya existe';
            return false;
        }
    
        // Valida si el nombre es alfabético
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre de la categoría debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    
    // Método para asignar la imagen de la categoría.
    public function setImagen($file, $filename = null)
    {
        if (Validator::validateImageFile($file, 1000)) {
            $this->imagen = Validator::getFilename();
            return true;
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
    // Método para establecer el nombre del archivo de imagen desde la base de datos.
    public function setFilename()
    {
        if ($data = $this->readFilename()) {
            $this->filename = $data['imagen'];
            return true;
        } else {
            $this->data_error = 'Categoría inexistente';
            return false;
        }
    }

    /*
     *  Métodos para obtener los atributos adicionales.
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
