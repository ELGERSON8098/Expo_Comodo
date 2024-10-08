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
    // Método para asignar el nombre del descuento.
    public function setNombre($value, $min = 2, $max = 50)
    {
        // Verificar si el nombre ya existe en la base de datos, excluyendo el registro actual
        if ($this->id) {
            $checkSql = 'SELECT COUNT(*) as count FROM tb_descuentos WHERE nombre_descuento = ? AND id_descuento != ?';
            $checkParams = array($value, $this->id);
        } else {
            $checkSql = 'SELECT COUNT(*) as count FROM tb_descuentos WHERE nombre_descuento = ?';
            $checkParams = array($value);
        }

        $checkResult = Database::getRow($checkSql, $checkParams);

        if ($checkResult['count'] > 0) {
            $this->data_error = 'El nombre del descuento ya existe';
            return false;
        }
        // Valida si el nombre es alfabético.
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

    // Método para asignar el valor del descuento.
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

    // Método para asignar la descripción del descuento.
    public function setDesc($value, $min = 2, $max = 50)
    {
        // Valida si la descripción contiene solo letras, números y ciertos caracteres especiales permitidos.
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

    public function setPrecioMin($value)
    {
        if (is_numeric($value) && $value >= 0) {
            $this->precioMin = $value;
            return true;
        } else {
            $this->data_error = 'El precio mínimo debe ser un número no negativo.';
            return false;
        }
    }

    public function setPrecioMax($value)
    {
        if (is_numeric($value) && $value > $this->precioMin) {
            $this->precioMax = $value;
            return true;
        } else {
            $this->data_error = 'El precio máximo debe ser un número mayor que el precio mínimo.';
            return false;
        }
    }



    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
