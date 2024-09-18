<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
class materialHandler
{
    protected $id = null;
    protected $nombre = null;

    // Método para buscar filas basadas en un valor
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_material, nombre
                FROM tb_materiales
                WHERE nombre LIKE ?
                ORDER BY nombre';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    // Método para crear una nueva fila
    public function createRow()
    {
        $sql = 'INSERT INTO tb_materiales(nombre)
                VALUES(?)';
        $params = array($this->nombre);
        return Database::executeRow($sql, $params);
    }

    // Método para leer todas las filas
    public function readAll()
    {
        $sql = 'SELECT id_material, nombre
            FROM tb_materiales
            ORDER BY nombre ASC;
    ';
        return Database::getRows($sql);
    }


    // Método para leer una fila específica por id
    public function readOne()
    {
        $sql = 'SELECT id_material, nombre
                FROM tb_materiales
                WHERE id_material = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Método para actualizar una fila
    public function updateRow()
    {
        $sql = 'UPDATE tb_materiales
                SET nombre = ?
                WHERE id_material = ?';
        $params = array($this->nombre, $this->id);
        return Database::executeRow($sql, $params);
    }

    // Método para eliminar una fila
    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_materiales
                WHERE id_material = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
