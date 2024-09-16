<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla CATEGORIA.
 */
class GeneroHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id_genero = null;
    protected $nombre = null;
    protected $imagen = null;

    // Constante para establecer la ruta de las imágenes.
    const RUTA_IMAGEN = '../../images/generos/';

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */

     //Busca géneros en la base de datos por nombre.
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_genero, nombre_genero, imagen_genero
            FROM tb_generos_zapatos
            WHERE nombre_genero LIKE ?
            ORDER BY nombre_genero';
        $params = array($value);
        return Database::getRows($sql, $params);
    }
    // Crea un nuevo género en la base de datos.
    public function createRow()
    {
        $sql = 'INSERT INTO tb_generos_zapatos(nombre_genero, imagen_genero)
            VALUES(?, ?)';
        $params = array($this->nombre, $this->imagen);
        return Database::executeRow($sql, $params);
    }
    //Lee todos los géneros de la base de datos.
    public function readAll()
    {
        $sql = 'SELECT id_genero, nombre_genero, imagen_genero
            FROM tb_generos_zapatos
            ORDER BY nombre_genero';
        return Database::getRows($sql);
    }
    //Lee los detalles de un género específico por ID.
    public function readOne()
    {
        $sql = 'SELECT id_genero, nombre_genero, imagen_genero
            FROM tb_generos_zapatos
            WHERE id_genero = ?';
        $params = array($this->id_genero);
        return Database::getRow($sql, $params);
    }
    //Lee el nombre del archivo de imagen de un género específico.
    public function readFilename()
    {
        $sql = 'SELECT imagen_genero
            FROM tb_generos_zapatos
            WHERE id_genero = ?';
        $params = array($this->id_genero);
        return Database::getRow($sql, $params);
    }
    //Actualiza la información de un género específico.
    public function updateRow()
    {
        $sql = 'UPDATE tb_generos_zapatos
            SET nombre_genero = ?
            WHERE id_genero = ?';
        $params = array($this->nombre, $this->id_genero);
        return Database::executeRow($sql, $params);
    }
    // Elimina un género específico por ID.
    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_generos_zapatos
            WHERE id_genero = ?';
        $params = array($this->id_genero);
        return Database::executeRow($sql, $params);
    }
    
}
