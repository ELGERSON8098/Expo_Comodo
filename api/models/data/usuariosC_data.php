<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/usuariosC_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class UsuariosData extends UsuariosHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
     *  Métodos para validar y asignar valores de los atributos.
     */

      // Método para validar y asignar el identificador del usuario.
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
    // Método para validar y asignar el nombre del usuario.
    public function setNombre($value, $min = 2, $max = 50)
    {
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
    // Método para validar y asignar el correo del usuario.
    public function setCorreo($value, $min = 8, $max = 100)
    {
        if (!Validator::validateEmail($value)) {
            $this->data_error = 'El correo no es válido';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->correo = $value;
            return true;
        } else {
            $this->data_error = 'El correo debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    // Método para validar y asignar el alias del usuario.
    public function setAlias($value, $min = 6, $max = 25)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El alias debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->alias = $value;
            return true;
        } else {
            $this->data_error = 'El alias debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    // Método para validar y asignar la dirección del usuario.
    public function setDic($value, $min = 6, $max = 5000)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'La direccion debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->direccion = $value;
            return true;
        } else {
            $this->data_error = 'La direccion debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    // Método para validar y asignar el DUI del usuario.
    public function setDUI($value)
    {
        if (!Validator::validateDUI($value)) {
            $this->data_error = 'El DUI debe tener el formato #########';
            return false;
        } 
        
        $this->dui = $value;
        return true;
    }
    // Método para validar y asignar el estado del usuario.
    public function setEstado($value)
    {
        if (Validator::validateBoolean($value)) {
            $this->estadocliente = $value;
            return true;
        } else {
            $this->data_error = 'Estado incorrecto';
            return false;
        }
    }
    
    // Método para validar y asignar el teléfono del usuario.
    public function setTelefono($value)
    {
        // Eliminar todos los caracteres no numéricos del número de teléfono
        $value = preg_replace('/\D/', '', $value);
        
        // Validar que el número de teléfono tenga al menos 7 dígitos
        if (strlen($value) >= 7) {
            $this->telefono = $value;
            return true;
        } else {
            $this->data_error = 'El teléfono debe tener al menos 7 dígitos';
            return false;
        }
    }
    


    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}

