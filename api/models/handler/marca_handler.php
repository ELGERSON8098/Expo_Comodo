<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class marcaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_marca, marca
                FROM tb_marcas
                WHERE marca LIKE ?
                ORDER BY marca';
        $params = array($value);
        return Database::getRows($sql, $params);
    }
    
    public function createRow()
    {
        $sql = 'INSERT INTO tb_marcas(marca)
                VALUES(?)';
        $params = array($this->nombre);
        return Database::executeRow($sql, $params);
    }
    
//Llamar los datos de la base de datos 
    public function readAll()
    {
        $sql = 'SELECT id_marca, marca
                FROM tb_marcas';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_marca, marca
                FROM tb_marcas
                WHERE id_marca = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_marcas
                SET marca = ?
                WHERE id_marca = ?';
        $params = array($this->nombre, $this->id);
        return Database::executeRow($sql, $params);
    }
    

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_marcas
                WHERE id_marca = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
    
}
