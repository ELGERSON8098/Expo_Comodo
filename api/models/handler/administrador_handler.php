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
    protected $apellido = null;
    protected $correo = null;
    protected $alias = null;
    protected $clave = null;
    protected $id_nivel_usuario = 1;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_administrador, usuario_administrador, clave_administrador
                FROM tb_admins
                WHERE usuario_administrador = ?';
        $params = array($username);
        if (!($data = Database::getRow($sql, $params))) {
            return false;
        } elseif (password_verify($password, $data['clave_administrador'])) {
            $_SESSION['idAdministrador'] = $data['id_administrador'];
            $_SESSION['aliasAdministrador'] = $data['usuario_administrador'];
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
        $params = array($_SESSION['idAdministrador']);
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
        $params = array($this->clave, $_SESSION['idadministrador']);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, correo_administrador, alias_administrador
                FROM tb_admins
                WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE tb_admins
                SET nombre_administrador = ?, correo_administrador = ?, usuario_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->correo, $this->alias, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_administrador, nombre_administrador, correo_administrador, usuario_administrador
                FROM tb_admins
                WHERE nombre_administrador LIKE ?
                ORDER BY nombre_administrador';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        // Insertar el administrador con el nivel de usuario correspondiente
        $sql = 'INSERT INTO tb_admins(nombre_administrador, usuario_administrador, correo_administrador, clave_administrador, id_nivel_usuario)
                VALUES (?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->alias, $this->correo, $this->clave, 1); // ID de nivel de usuario = 1
        return Database::executeRow($sql, $params);
    }

    public function createTrabajadores()
    {

        // Insertar el nuevo usuario como administrador
        $sql = 'INSERT INTO tb_admins(nombre_administrador, correo_administrador, usuario_administrador, clave_administrador, id_nivel_usuario)
            VALUES(?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->correo, $this->alias, $this->clave, $this->id_nivel_usuario);

        return Database::executeRow($sql, $params);
    }


    public function readAll()
    {
        $sql = 'SELECT a.id_administrador, a.nombre_administrador, a.correo_administrador, a.usuario_administrador, n.nombre_nivel
FROM tb_admins a
JOIN tb_niveles_usuarios n ON a.id_nivel_usuario = n.id_nivel_usuario
WHERE a.id_nivel_usuario IN (2, 3)
ORDER BY a.nombre_administrador';
        return Database::getRows($sql);
    }

    public function readAllNivelesUsuarios()
    {
        $sql = 'SELECT 
                    id_nivel_usuario,
                    nombre_nivel
                FROM 
                    tb_niveles_usuarios
                WHERE 
                    id_nivel_usuario IN (2, 3)';
    
        return Database::getRows($sql);
    }
    



    public function readOne()
    {
        $sql = 'SELECT 
    a.id_administrador, 
    a.nombre_administrador, 
    a.correo_administrador, 
    a.usuario_administrador,
    a.id_nivel_usuario,
    n.nombre_nivel
FROM 
    tb_admins a
INNER JOIN 
    tb_niveles_usuarios n ON a.id_nivel_usuario = n.id_nivel_usuario
WHERE 
    a.id_administrador = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_admins
                  SET nombre_administrador = ?, correo_administrador = ?, usuario_administrador = ?, id_nivel_usuario = ?
                  WHERE id_administrador = ?';
        $params = array($this->nombre, $this->correo, $this->alias, $this->id_nivel_usuario, $this->id);
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
