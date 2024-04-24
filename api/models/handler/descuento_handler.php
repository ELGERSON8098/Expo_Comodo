<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class descuentoHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;
    protected $valor = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
 
     public function searchRows()
     {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_descuento, nombre_descuento, descripcion, valor
                FROM tb_descuentos
                WHERE nombre_descuento LIKE ? 
                ORDER BY nombre_descuento';
        $params = array($value);
        return Database::getRows($sql, $params);
     }
     
     public function createRow()
     {
         $sql = 'INSERT INTO tb_descuentos(nombre_descuento, descripcion, valor)
                 VALUES(?, ?, ?)';
         $params = array($this->nombre, $this->descripcion, $this->valor);
         return Database::executeRow($sql, $params);
     }
     
    
    
    
    
//Llamar los datos de la base de datos 
    public function readAll()
    {
        $sql = 'SELECT id_descuento, nombre_descuento, descripcion, valor
                FROM tb_descuentos';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_descuento, nombre_descuento, descripcion, valor
                FROM tb_descuentos
                WHERE id_descuento = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_descuentos
                SET nombre_descuento = ?, descripcion = ?, valor = ?
                WHERE id_descuento = ?';
        $params = array($this->nombre, $this->descripcion, $this->valor, $this->id);
        return Database::executeRow($sql, $params);
    }
    

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_descuentos
                WHERE id_descuento = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
