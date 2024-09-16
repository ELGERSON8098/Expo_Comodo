<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/direccion_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class direccionData extends direccion_handler
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
      // Método para asignar el nombre de la dirección.
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
     // Método para asignar el apellido.
    public function setApellido($value, $min = 2, $max = 50)
    {
        // Valida si el apellido es alfabético.
        if (!Validator::validateAlphabetic($value)) {
            $this->data_error = 'El apellido debe ser un valor alfabético';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->apellido = $value;
            return true;
        } else {
            $this->data_error = 'El apellido debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    // Método para asignar el correo.
    public function setCorreo($value, $min = 8, $max = 100)
    {
        // Valida si el correo tiene un formato válido.
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
    // Método para asignar el alias.
    public function setAlias($value, $min = 6, $max = 25)
    {
         // Valida si el alias es alfanumérico.
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

    // Método para asignar la clave (contraseña).
    public function setClave($value)
    {
         // Valida si la clave cumple con las reglas de seguridad.
        if (Validator::validatePassword($value)) {
            $this->clave = password_hash($value, PASSWORD_DEFAULT);
            return true;
        } else {
            $this->data_error = Validator::getPasswordError();
            return false;
        }
    }
    // Método para asignar el DUI.
    public function setDUI($value)
    {
        // Valida si el formato del DUI es correcto.
        if (!Validator::validateDUI($value)) {
            $this->data_error = 'El DUI debe tener el formato #########';
            return false;
        } elseif($this->checkDuplicate($value)) {
            $this->data_error = 'El DUI ingresado ya existe';
            return false;
        } else {
            $this->dui = $value;
            return true;
        }
    }
      // Método para asignar el teléfono.
    public function setTelefono($value)
    {
        // Valida si el formato del teléfono es correcto.
        if (Validator::validatePhone($value)) {
            $this->telefono = $value;
            return true;
        } else {
            $this->data_error = 'El teléfono debe tener el formato (2, 6, 7)#######';
            return false;
        }
    }
    // Método para asignar la cantidad.
    public function setCantidad($value)
    {
        // Valida si la cantidad es un número natural (positivo y entero).
        if (Validator::validateNaturalNumber($value)) {
            $this->cantidad = $value;
            return true;
        } else {
            $this->data_error = 'La cantidad del producto debe ser mayor o igual a 1';
            return false;
        }
    }
    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}