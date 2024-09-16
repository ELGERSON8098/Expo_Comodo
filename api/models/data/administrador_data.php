<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/administrador_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla USUARIO.
 */
class AdministradorData extends AdministradorHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
     *  Métodos para validar y asignar valores de los atributos.
     */

    // Método para asignar el ID del administrador.
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
    public function setResetCodeForVerification($codigo)
    {
        if (preg_match('/^[0-9]{6}$/', $codigo)) {
            $this->reset_code = $codigo;
            return true;
        } else {
            return false;
        }
    }

    // Método para asignar el nivel del administrador.
    public function setNivel($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_nivel_usuario = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del nivel de usuario es incorrecto';
            return false;
        }
    }

    // Método para asignar el correo del administrador.
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

    // Método para asignar el alias del administrador.
    public function setAlias($value, $min = 6, $max = 25)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El usuario debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->alias = $value;
            return true;
        } else {
            $this->data_error = 'El usuario debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    // Método para asignar y verificar la unicidad del correo.
    public function setCorreos($value, $min = 8, $max = 100)
    {
        // Verificar si el nombre ya existe en la base de datos, excluyendo el registro actual
        if ($this->id) {
            $checkSql = 'SELECT COUNT(*) as count FROM tb_admins WHERE correo_administrador = ? AND id_administrador != ?';
            $checkParams = array($value, $this->id);
        } else {
            $checkSql = 'SELECT COUNT(*) as count FROM tb_admins WHERE correo_administrador = ?';
            $checkParams = array($value);
        }

        $checkResult = Database::getRow($checkSql, $checkParams);
        // Si el correo ya existe, establece un error.
        if ($checkResult['count'] > 0) {
            $this->data_error = 'El correo ya existe';
            return false;
        }
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

    public function setAlia($value, $min = 6, $max = 25)
    {
        // Verificar si el nombre ya existe en la base de datos, excluyendo el registro actual
        if ($this->id) {
            $checkSql = 'SELECT COUNT(*) as count FROM tb_admins WHERE usuario_administrador = ? AND id_administrador != ?';
            $checkParams = array($value, $this->id);
        } else {
            $checkSql = 'SELECT COUNT(*) as count FROM tb_admins WHERE usuario_administrador = ?';
            $checkParams = array($value);
        }
        // Si el alias ya existe, establece un error.
        $checkResult = Database::getRow($checkSql, $checkParams);
        if ($checkResult['count'] > 0) {
            $this->data_error = 'El usuario ya existe';
            return false;
        }
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



    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}
