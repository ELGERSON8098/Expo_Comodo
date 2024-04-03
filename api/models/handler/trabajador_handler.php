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
    protected $id = null;
    protected $nombre = null;
    protected $correo = null;
    protected $usuario = null;
    protected $clave = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_administrador, user_administrador,  clave_administrador
                FROM tbAdmins
                WHERE  user_administrador = ?';
        $params = array($username);
        $data = Database::getRow($sql, $params);
        if (password_verify($password, $data['clave_administrador'])) {
            $_SESSION['idAdmin'] = $data['id_administrador'];
            $_SESSION['NUsuario'] = $data['user_administrador'];

            
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT clave_administrador
                FROM tbAdmins
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
        $sql = 'UPDATE tbAdmins
                SET clave_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['id_administrador']);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, correo_administrador, user_administrador
                FROM tbAdmins
                WHERE id_administrador = ?';
        $params = array($_SESSION['id_administrador']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE tbAdmins
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
                FROM tbAdmins
                WHERE nombre_administrador LIKE ?
                ORDER BY nombre_administrador';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tbAdmins(nombre_administrador, correo_administrador, user_administrador, clave_administrador)
                VALUES(?, ?, ?, ?)';
        $params = array($this->nombre, $this->correo, $this->alias, $this->clave);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
{
    $sql = 'SELECT a.id_administrador, a.nombre_administrador, a.correo_administrador, a.user_administrador, n.nombre_nivel
            FROM tbAdmins a
            INNER JOIN tbniveles_usuario n ON a.id_nivel_usuario = n.id_nivel_usuario
            WHERE a.id_administrador >= 2
            ORDER BY a.nombre_administrador';
    return Database::getRows($sql);
}

public function readOne()
{
    $sql = 'SELECT a.id_administrador, a.nombre_administrador, a.correo_administrador, a.user_administrador, n.nombre_nivel
            FROM tbAdmins a
            INNER JOIN tbniveles_usuario n ON a.id_nivel_usuario = n.id_nivel_usuario
            WHERE a.id_administrador >= 2 AND a.id_administrador = ?';
    $params = array($this->id);
    return Database::getRow($sql, $params);
}

    

    public function updateRow() 
    {
        $sql = 'UPDATE tbAdmins
                SET nombre_administrador = ?,  correo_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM tbAdmins
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
