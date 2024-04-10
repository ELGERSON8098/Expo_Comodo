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
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_administrador, usuario_administrador,  clave_administrador
                FROM tb_admins
                WHERE  usuario_administrador = ?';
        $params = array($username);
        $data = Database::getRow($sql, $params);
        if (password_verify($password, $data['clave_administrador'])) {
            $_SESSION['idAdmin'] = $data['id_administrador'];
            $_SESSION['NUsuario'] = $data['usuario_administrador'];

            
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT clave_administrador
                FROM tb_admins
                WHERE id_administrador = ?';
        $params = array($_SESSION['id_administrador']);
        $data = Database::getRow($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($password, $data['clave_administrador'])) {
            return true;
        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE tb_admins
                SET clave_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['id_administrador']);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, correo_administrador, user_administrador
                FROM tb_admins
                WHERE id_administrador = ?';
        $params = array($_SESSION['id_administrador']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE tb_admins
                SET nombre_administrador = ?, correo_administrador = ?, user_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->correo, $this->usuario, $_SESSION['id_administrador']);
        return Database::executeRow($sql, $params);
    }

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

    public function createRow()
    {
        $sql = 'INSERT INTO tb_admins(nombre_administrador, correo_administrador, usuario_administrador, clave_administrador)
                VALUES(?, ?, ?, ?)';
        $params = array($this->nombre, $this->correo, $this->alias, $this->clave);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT departamento, municipio, distrito from tb_distritos
        INNER JOIN tb_municipios USING (id_municipio)
        INNER JOIN tb_departamentos USING (id_departamento)';
        return Database::getRows($sql);
    }
    
    public function readOne()
    {
        $sql = 'SELECT departamento, municipio, distrito from tb_distritos
        INNER JOIN tb_municipios USING (id_municipio)
        INNER JOIN tb_departamentos USING (id_departamento)';
        $params = array($id_distrito);
        return Database::getRow($sql, $params);
    }
    
    

    public function updateRow() 
    {
        $sql = 'UPDATE tb_admins
                SET nombre_administrador = ?,  correo_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_admins
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
