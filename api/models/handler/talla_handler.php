<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class tallaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    //Método para buscar filas que coincidan con el valor de búsqueda.
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_talla, nombre_talla
                FROM tb_tallas
                WHERE nombre_talla LIKE ?
                ORDER BY nombre_talla';
        $params = array($value);
        return Database::getRows($sql, $params);
    }
    //Método para crear una nueva talla.
    public function createRow()
    {
        $sql = 'INSERT INTO tb_tallas(nombre_talla)
                VALUES(?)';
        $params = array($this->nombre);
        return Database::executeRow($sql, $params);
    }

      /*
     *  Método para leer todas las tallas.
     */
    //Llamar los datos de la base de datos 
    public function readAll()
    {
        $sql = 'SELECT id_talla, nombre_talla
            FROM tb_tallas
            ORDER BY id_talla ASC';
        return Database::getRows($sql);
    }

    // Método para leer una talla específica por su ID.
    public function readOne()
    {
        $sql = 'SELECT id_talla, nombre_talla
                FROM tb_tallas
                WHERE id_talla = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    //Método para actualizar una talla existente.
    public function updateRow()
    {
        $sql = 'UPDATE tb_tallas
                SET nombre_talla = ?
                WHERE id_talla = ?';
        $params = array($this->nombre, $this->id);
        return Database::executeRow($sql, $params);
    }

    //Método para eliminar una talla
    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_tallas
                WHERE id_talla = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function getNombreTalla() {
        $sql = 'SELECT nombre_talla FROM tb_tallas WHERE id_talla = ?';
        $params = array($this->id);

        // Ejecutar la consulta
        if ($data = Database::getRow($sql, $params)) {
            $this->nombre = $data['nombre_talla'];
            return $this->nombre;
        } else {
            return null; // Si no se encuentra la talla
        }
    }

}
