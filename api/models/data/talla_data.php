<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/talla_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class tallaData extends tallaHandler
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
            $this->data_error = 'El identificador del administrador es incorrecto';
            return false;
        }
    }
    
    public function setNombre($value, $min = 1, $max = 3)
    {
         // Verificar si la talla ya existe en la base de datos
         $checkSql = 'SELECT COUNT(*) as count FROM tb_tallas WHERE nombre_talla = ?';
         $checkParams = array($value);
         $checkResult = Database::getRow($checkSql, $checkParams);
     
         if ($checkResult['count'] > 0) {
             $this->data_error = 'La talla ya existe';
             return false;
         }

         if (Validator::validateMoney($value)) {
            $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'La talla debe ser un número positivo';
            return false;
        }

        // Validar la longitud del nombre de la talla
        if (Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'La talla debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    


    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}

