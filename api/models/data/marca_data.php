<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/marca_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class marcaData extends marcaHandler
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
            $this->data_error = 'El identificador de la marca es incorrecto';
            return false;
        }
    }
     // Método para validar y asignar el nombre de la marca.
    public function setNombre($value, $min = 2, $max = 50)
    {
        // Verificar si la marca ya existe en la base de datos
        $checkSql = 'SELECT COUNT(*) as count FROM tb_marcas WHERE marca = ?';
        $checkParams = array($value);
        $checkResult = Database::getRow($checkSql, $checkParams);
    
        if ($checkResult['count'] > 0) {
            $this->data_error = 'La marca ya existe';
            return false;
        }
    
        // Validar el valor y la longitud del nombre de la marca
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El nombre de la marca debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'El nombre de la marca debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    
    

    public function setFechaInicio($value)
    {
        if (Validator::validateDate($value)) {
            $this->fecha_inicio = $value;
            return true;
        } else {
            $this->data_error = 'El formato de la fecha de inicio es incorrecto.';
            return false;
        }
    }

    public function setFechaFin($value)
    {
        if (Validator::validateDate($value)) {
            if ($this->fecha_inicio !== null) {
                if ($value >= $this->fecha_inicio) {
                    $this->fecha_fin = $value;
                    return true;
                } else {
                    $this->data_error = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
                    return false;
                }
            } else {
                $this->data_error = 'La fecha de inicio debe ser asignada antes de validar la fecha de fin.';
                return false;
            }
        } else {
            $this->data_error = 'El formato de la fecha de fin es incorrecto.';
            return false;
        }
    }


    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}

