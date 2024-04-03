<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla PRODUCTO.
*/
class ProductoHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;
    protected $codigo = null;
    protected $referencia = null;
    protected $imagen = null;
    // Constante para establecer la ruta de las imágenes.
    const RUTA_IMAGEN = '../../images/productos/';

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_producto, nombre_producto, descripcion, codigo_interno, Referencia_provedor, imagen
                FROM tbproductos';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tbproductos(id_producto, nombre_producto, descripcion, codigo_interno, Referencia_provedor, imagen)
                VALUES(?, ?, ?, ?, ?, ?)';
        $params = array($this->id, $this->nombre, $this->descripcion, $this->codigo, $this->referencia, $this->imagen);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_producto, nombre_producto, descripcion, codigo_interno, Referencia_provedor, imagen
                FROM tbproductos';
                
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_producto, nombre_producto, descripcion, codigo_interno, Referencia_provedor, imagen
                FROM tbproductos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function readFilename()
    {
        $sql = 'SELECT imagen
                FROM tbproductos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tbproductos
                SET imagen = ?, nombre_producto = ?, descripcion = ?, codigo_interno = ?, Referencia_provedor = ?, imagen = ?
                WHERE id_producto = ?';
        $params = array($this->imagen, $this->nombre, $this->descripcion, $this->codigo, $this->referencia, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tbproductos
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

 

}
