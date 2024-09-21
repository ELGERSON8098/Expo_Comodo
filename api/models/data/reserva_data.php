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
        array('Aceptado', 'Aceptado'),
        array('Cancelado', 'Cancelado')
    );

    /*
     *  Métodos para validar y asignar valores de los atributos.
     */
    // Método para validar y asignar el identificador de la reserva.
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
    // Método para validar y asignar el identificador del usuario asociado a la reserva.
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

    // Método para validar y asignar el estado de la reserva
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
    // Método para validar y asignar el identificador del detalle de la reserva.
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
    // Establece la fecha de inicio después de validar el formato de la fecha
    public function setFechaInicio($value)
    {
        // Utiliza el validador para verificar si la fecha tiene un formato correcto
        if (Validator::validateDate($value)) {
             // Si la fecha es válida, se asigna a la propiedad $fecha_inicio.
            $this->fecha_inicio = $value;
            return true;
        } else {
             // Si la fecha es inválida, se asigna un mensaje de error.
            $this->data_error = 'El formato de la fecha de inicio es incorrecto.';
            return false;
        }
    }
    // Establece la fecha de fin después de validar el formato de la fecha y su relación con la fecha de inicio.
    public function setFechaFin($value)
    {
         // Utiliza el validador para verificar si la fecha tiene un formato correcto.
        if (Validator::validateDate($value)) {
             // Verifica si la fecha de inicio ya fue asignada.
            if ($this->fecha_inicio !== null) {
                 // Verifica si la fecha de fin es igual o posterior a la fecha de inicio.
                if ($value >= $this->fecha_inicio) {
                     // Si la fecha es válida, se asigna a la propiedad $fecha_fin.
                    $this->fecha_fin = $value;
                    return true;
                } else {
                    // Si la fecha de fin es anterior a la fecha de inicio, genera un error
                    $this->data_error = 'La fecha de fin no puede ser anterior a la fecha de inicio.';
                    return false;
                }
            } else {
                // Si la fecha de inicio no ha sido asignada, genera un error.
                $this->data_error = 'La fecha de inicio debe ser asignada antes de validar la fecha de fin.';
                return false;
            }
        } else {
            // Si la fecha de fin es inválida, se asigna un mensaje de error.
            $this->data_error = 'El formato de la fecha de fin es incorrecto.';
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