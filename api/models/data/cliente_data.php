<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/cliente_handler.php');
/*
*	Clase para manejar el encapsulamiento de los datos de la tabla CLIENTE.
*/
class ClienteData extends ClienteHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
    *   Métodos para validar y establecer los datos.
    */
    public function setId($value)
    {
         // Valida si el valor es un número natural (positivo y entero)
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del cliente es incorrecto';
            return false;
        }
    }
    // Método para asignar el alias del cliente.
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
    // Método para asignar el nombre del cliente.
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

    // Método para asignar la dirección del cliente.
    public function setDirec($value, $min = 2, $max = 1000)
    {
        if (!Validator::validateString($value)) {
            $this->data_error = 'Direccion';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->direccion = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    // Método para asignar el correo del cliente y verificar su unicidad.
    public function setCorreo($value, $min = 8, $max = 100)
    {
        if (!Validator::validateEmail($value)) {
            $this->data_error = 'El correo no es válido';
            return false;
        } elseif (!Validator::validateLength($value, $min, $max)) {
            $this->data_error = 'El correo debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        } elseif ($this->checkDuplicate($value)) {
            $this->data_error = 'El correo ingresado ya existe';
            return false;
        } else {
            $this->correo = $value;
            return true;
        }
    }
    // Método alternativo para asignar el correo del cliente sin verificar unicidad.
    public function setCorreos($value, $min = 8, $max = 100)
    {
        if (!Validator::validateEmail($value)) {
            $this->data_error = 'El correo no es válido';
            return false;
        } elseif (!Validator::validateLength($value, $min, $max)) {
            $this->data_error = 'El correo debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        } else {
            $this->correo = $value;
            return true;
        }
    }
     // Método para asignar el teléfono del cliente y verificar su unicidad.
    public function setTelefono($value)
    {
        // Verificar si el valor del teléfono es una cadena.
        if (!is_string($value)) {
            $this->data_error = 'El teléfono debe tener el formato (2, 6, 7)####-####';
            return false;
        }
    
        // Consulta para verificar si el teléfono ya existe en la base de datos.
        $checkSql = 'SELECT COUNT(*) as count FROM tb_usuarios WHERE telefono = ?';
        $checkParams = array($value); // El valor se tratará como una cadena automáticamente
        $checkResult = Database::getRow($checkSql, $checkParams);
    
        if ($checkResult && $checkResult['count'] > 0) {
            $this->data_error = 'El teléfono ingresado ya existe';
            return false;
        }
    
        // Validar el formato del teléfono.
        if (Validator::validatePhone($value)) {
            $this->telefono = $value;
            return true;
        } else {
            $this->data_error = 'El teléfono debe tener el formato (2, 6, 7)###-####';
            return false;
        }
    }
    // Método alternativo para asignar el teléfono del cliente sin verificar unicidad.
    public function setTelefonos($value)
    {
        // Verificar si el valor del teléfono es una cadena.
        if (!is_string($value)) {
            $this->data_error = 'El teléfono debe tener el formato (2, 6, 7)###-####';
            return false;
        }
        // Validar el formato del teléfono.
        if (Validator::validatePhone($value)) {
            $this->telefono = $value;
            return true;
        } else {
            $this->data_error = 'El teléfono debe tener el formato (2, 6, 7)###-####';
            return false;
        }
    }
    
    // Método para asignar el DUI del cliente y verificar su unicidad.
    public function setDui($value)
    {
        $checkSql = 'SELECT COUNT(*) as count FROM tb_usuarios WHERE dui_cliente = ?';
        $checkParams = array($value);
        $checkResult = Database::getRow($checkSql, $checkParams);

        if ($checkResult['count'] > 0) {
            $this->data_error = 'El DUI ingresado ya existe';
            return false;
        }

        if (Validator::validateDUI($value)) {
            $this->dui = $value;
            return true;
        } else {
            $this->data_error = 'El DUI debe tener el formato ########-#';
            return false;
        }
    }
    // Método para asignar la clave del cliente.
    public function setClave($value)
    {
        if (Validator::validatePassword($value)) {
            $this->clave = password_hash($value, PASSWORD_DEFAULT);
            return true;
        } else {
            $this->data_error = Validator::getPasswordError();
            return false;
        }
    }
    // Método para asignar el estado del cliente.
    public function setEstado($value)
    {
        if (Validator::validateBoolean($value)) {
            $this->estado = $value;
            return true;
        } else {
            $this->data_error = 'Estado incorrecto';
            return false;
        }
    }

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
