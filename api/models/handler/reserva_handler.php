<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
 
/*
* Clase para manejar el comportamiento de los datos de la tabla tb_reservas.
*/
class ReservaHandler {
    /*
     * Declaración de atributos para el manejo de datos.
     */
    protected $id_reserva = null;
    protected $id_usuario = null;
    protected $fecha_reserva = null;
    protected $id_direccion = null;
    protected $descripcion_direccion = null;
 
    /*
     * Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function createRow() {
        $sql = 'INSERT INTO tb_reservas(id_usuario, fecha_reserva, id_direccion, descripcion_direccion) VALUES(?, ?, ?, ?)';
        $params = array($this->id_usuario, $this->fecha_reserva, $this->id_direccion, $this->descripcion_direccion);
        return Database::executeRow($sql, $params);
    }
 
    public function readAll() {
        $sql = 'SELECT r.id_reserva, r.fecha_reserva, r.descripcion_direccion, u.nombre AS nombre_usuario, d.direccion
                FROM tb_reservas r
                INNER JOIN tb_usuarios u ON r.id_usuario = u.id_usuario
                INNER JOIN tb_direcciones d ON r.id_direccion = d.id_direccion
                ORDER BY r.fecha_reserva DESC';
        return Database::getRows($sql);
    }
 
    public function readOne() {
        $sql = 'SELECT r.id_reserva, r.fecha_reserva, r.descripcion_direccion, u.nombre AS nombre_usuario, d.direccion
                FROM tb_reservas r
                INNER JOIN tb_usuarios u ON r.id_usuario = u.id_usuario
                INNER JOIN tb_direcciones d ON r.id_direccion = d.id_direccion
                WHERE r.id_reserva = ?';
        $params = array($this->id_reserva);
        return Database::getRow($sql, $params);
    }
 
    public function updateRow() {
        $sql = 'UPDATE tb_reservas SET id_usuario = ?, fecha_reserva = ?, id_direccion = ?, descripcion_direccion = ? WHERE id_reserva = ?';
        $params = array($this->id_usuario, $this->fecha_reserva, $this->id_direccion, $this->descripcion_direccion, $this->id_reserva);
        return Database::executeRow($sql, $params);
    }
 
    public function deleteRow() {
        $sql = 'DELETE FROM tb_reservas WHERE id_reserva = ?';
        $params = array($this->id_reserva);
        return Database::executeRow($sql, $params);
    }
}
?
>
