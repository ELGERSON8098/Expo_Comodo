<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class AdministradorHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */

    protected $id_producto = null;
    protected $nombre_producto = null;
    protected $descripcion = null;
    protected $codigo_interno = null;
    protected $referencia_proveedor = null;
    protected $imagen = null;
    protected $id_subcategoria = null;
    protected $id_administrador = null;

    
    public function readAll()
    {
        $sql = 'SELECT id_producto, nombre_producto, descripcion, codigo_interno, Referencia_provedor, imagen, id_subcategoria, id_administrador
        FROM tbproductos';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_producto, nombre_producto, descripcion, codigo_interno, Referencia_provedor, imagen, id_subcategoria, id_administrador
        FROM tbproductos
        WHERE id_producto = ?';
        $params = array($this->id_producto);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tbproductos
                SET nombre_producto = ?, descripcion = ?, codigo_interno = ?, Referencia_provedor = ?, imagen = ?, id_subcategoria = ?, id_administrador = ?
                WHERE id_producto = ?';
        $params = array($this->nombre_producto, $this->descripcion, $this->codigo_interno, $this->referencia_proveedor, $this->imagen, $this->id_subcategoria, $this->id_administrador, $this->id_producto);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tbproductos
        WHERE id_producto = ?';
        $params = array($this->id_producto);
        return Database::executeRow($sql, $params);
    }
}