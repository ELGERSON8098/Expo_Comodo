<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/descuento_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class descuentoData extends descuentoHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;
    private $codigo_interno = null;

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

    public function setNombre($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre del descuento debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }


    public function setvalor($value, $min = 1, $max = 200)
    {
        if (Validator::validateMoney($value)) {
            $this->valor = $value;
            return true;
        } else {
            $this->data_error = 'El valor debe ser un número positivo';
            return false;
        }
    }
    

    public function setDesc($value, $min = 2, $max = 50)
    {
        if (!preg_match('/^[a-zA-Z0-9\s\-áéíóúÁÉÍÓÚñÑ.,;:()¿?¡!&%$€£@#]*$/', $value)) {
            $this->data_error = 'La descripción debe contener solo letras, números y algunos caracteres especiales';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->descripcion = $value;
            return true;
        } else {
            $this->data_error = 'La descripción debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    


    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
