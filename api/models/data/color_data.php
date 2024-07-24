<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/color_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class colorData extends colorHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
     *  Métodos para validar y asignar valores de los atributos.
     */
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del color es incorrecto';
            return false;
        }
    }

    public function setNombre($value, $min = 2, $max = 50)
    {
        // Verificar si el color ya existe en la base de datos
        $checkSql = 'SELECT COUNT(*) as count FROM tb_colores WHERE color = ?';
        $checkParams = array($value);
        $checkResult = Database::getRow($checkSql, $checkParams);
    
        if ($checkResult['count'] > 0) {
            $this->data_error = 'El color ya existe';
            return false;
        }
    
        // Validar el valor y la longitud del nombre
        if (!Validator::validateColor($value)) {
            $this->data_error = 'El nombre debe ser un valor hola';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}

