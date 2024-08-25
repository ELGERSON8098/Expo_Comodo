<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class direccion_handler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $departamento = null;
    protected $municipio = null;
    protected $distrito = null;
    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_administrador, nombre_administrador, correo_administrador, user_administrador
                FROM tb_admins
                WHERE nombre_administrador LIKE ?
                ORDER BY nombre_administrador';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }
    //Crea una nueva dirección en la base de datos.
    public function createRow()
    {
        $sql = 'INSERT INTO tb_admins(nombre_administrador, correo_administrador, usuario_administrador, clave_administrador)
                VALUES(?, ?, ?, ?)';
        $params = array($this->nombre, $this->correo, $this->alias, $this->clave);
        return Database::executeRow($sql, $params);
    }
    // Lee todas las direcciones de la base de datos.
    public function readAll()
    {
        $sql = 'SELECT departamento, municipio, distrito from tb_distritos
        INNER JOIN tb_municipios USING (id_municipio)
        INNER JOIN tb_departamentos USING (id_departamento)';
        return Database::getRows($sql);
    }
    //Lee los detalles de una dirección específica por ID.
    public function readOne()
    {
        $sql = 'SELECT departamento, municipio, distrito from tb_distritos
        INNER JOIN tb_municipios USING (id_municipio)
        INNER JOIN tb_departamentos USING (id_departamento)';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }
    
    //Actualiza la información de una dirección específica.
    public function updateRow() 
    {
        $sql = 'UPDATE tb_distritos AS d
                INNER JOIN tb_municipios AS m ON d.id_municipio = m.id_municipio
                SET d.distrito = ?
                WHERE d.id_distrito = ?';
        $params = array($this->distrito, $this->id);
        return Database::executeRow($sql, $params);
    }
    
    //Elimina una dirección específica por ID.
    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_admins
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
