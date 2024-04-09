<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/reserva_handler.php');
/*
 *  Clase para manejar el encapsulamiento de los datos de la tabla CATEGORIA.
 */
class ReservaData extends ReservaHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;
    private $filename = null;

     /*
     * Atributos de la tabla tb_reservas.
     */

    private $id_reserva;
    private $id_usuario;
    private $fecha_reserva;
    private $id_direccion;
    private $descripcion_direccion;
 
    /*
     * Métodos para establecer los datos de la reserva.
     */

    public function setIdUsuario($value) {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_usuario = $value;
            return true;
        } else {
            return false;
        }
    }
 
    public function setFechaReserva($value) {
        $this->fecha_reserva = $value;
        return true;
    }
 
    public function setIdDireccion($value) {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_direccion = $value;
            return true;
        } else {
            return false;
        }
    }
 
    public function setDescripcionDireccion($value) {
        $this->descripcion_direccion = $value;
        return true;
    }
 
    /*
     * Métodos para obtener los atributos de la reserva.
     */
    public function getIdUsuario() {
        return $this->id_usuario;
    }
 
    public function getFechaReserva() {
        return $this->fecha_reserva;
    }
 
    public function getIdDireccion() {
        return $this->id_direccion;
    }
 
    public function getDescripcionDireccion() {
        return $this->descripcion_direccion;
    }
}
?>

