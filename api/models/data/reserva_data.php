<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/reserva_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla CATEGORIA.
 */
class reservaData extends ReservaHandler
{
    private $data_error = null;
    private $estados = array(
        array('Pendiente', 'Pendiente'),
        array('Aceptado', 'Aceptado')
    );

    /*
     *  Métodos para validar y asignar valores de los atributos.
     */
    public function setIdReserva($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_reserva = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la reserva es incorrecto';
            return false;
        }
    }
    public function setIdUsuario($value)
    {
        // Valida que el identificador de usuario sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            $this->id_usuario = $value; // Asigna el valor del identificador de usuario.
            return true;
        } else {
            $this->data_error = 'El identificador de usuario es incorrecto'; // Almacena mensaje de error.
            return false;
        }
    }


    public function setEstado($value)
    {
        // Valida que el estado sea uno de los permitidos.
        if (in_array($value, array_column($this->estados, 0))) {
            $this->estado = $value;
            return true;
        } else {
            $this->data_error = 'Estado incorrecto'; // Almacena mensaje de error.
            return false;
        }
    }

    public function setFecha($value)
    {
        // Valida el formato de la fecha.
        if (Validator::validateDateTime($value, 'Y-m-d H:i:s')) {
            $this->fecha_reserva = $value; // Asigna el valor de la fecha.
            return true;
        } else {
            $this->data_error = 'El formato de fecha debe ser YYYY-MM-DD HH:MM:SS'; // Almacena mensaje de error.
            return false;
        }
    }

    public function setIdDetalle($value)
    {
        // Valida que el identificador del detalle sea un número natural.
        if (Validator::validateNaturalNumber($value)) {
            $this->id_detalle_reserva = $value; // Asigna el valor del identificador del detalle.
            return true;
        } else {
            $this->data_error = 'El identificador del detalle es incorrecto'; // Almacena mensaje de error.
            return false;
        }
    }

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
    public function getEstados()
    {
        return $this->estados; // Devuelve los estados permitidos.
    }
}
