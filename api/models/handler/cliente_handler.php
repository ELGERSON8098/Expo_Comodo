<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *	Clase para manejar el comportamiento de los datos de la tabla CLIENTE.
 */
class ClienteHandler
{
    /*
     *   Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $alias = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $telefono = null;
    protected $dui = null;
    protected $nacimiento = null;
    protected $direccion = null;
    protected $clave = null;
    protected $estado = null;

    /*
     *   Métodos para gestionar la cuenta del cliente.
     */
    public function checkUser($correo, $password)
    {
        $sql = 'SELECT id_usuario, usuario, clave, estado_cliente, usuario
                FROM tb_usuarios
                WHERE usuario = ?';
        $params = array($correo);
        $data = Database::getRow($sql, $params);

        if ($data && password_verify($password, $data['clave'])) {
            $this->id = $data['id_usuario'];
            $this->alias = $data['usuario'];
            $this->estado = $data['estado_cliente'];
            return true;
        } else {
            return false;
        }
    }


    public function checkStatus()
    {
        if ($this->estado) {
            $_SESSION['idUsuario'] = $this->id;
            $_SESSION['UsuarioCliente'] = $this->alias;
            return true;
        } else {
            return false;
        }
    }

    public function changePassword()
    {
        $sql = 'UPDATE tb_usuarios
                SET clave = ?
                WHERE id_usuario = ?';
        $params = array($this->clave, $_SESSION['idUsuario']);
        return Database::executeRow($sql, $params);
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT clave
                FROM tb_usuarios
                WHERE id_usuario = ?';
        $params = array($_SESSION['idUsuario']);
        $data = Database::getRow($sql, $params);
        if (password_verify($password, $data['clave'])) {
            return true;
        } else {
            return false;
        }
    }

    public function changeStatus()
    {
        $sql = 'UPDATE cliente
                SET estado_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    /*
     *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_cliente, nombre_cliente, apellido_cliente, correo_cliente, dui_cliente, telefono_cliente, nacimiento_cliente, direccion_cliente
                FROM cliente
                WHERE apellido_cliente LIKE ? OR nombre_cliente LIKE ? OR correo_cliente LIKE ?
                ORDER BY apellido_cliente';
        $params = array($value, $value, $value);
        return Database::getRows($sql, $params);
    }

    public function createUsuario()
    {
        $sql = 'INSERT INTO tb_usuarios (nombre, usuario, correo, clave, direccion_cliente, telefono, dui_cliente)
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->alias, $this->correo, $this->clave, $this->direccion, $this->telefono, $this->dui);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_usuario, nombre, usuario, correo
                FROM tb_usuarios
                ORDER BY usuario';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_usuario, nombre, usuario, correo, clave
                FROM tb_usuarios
                WHERE id_usuario = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function readProfile()
    {
        $sql = 'SELECT id_usuario, nombre, usuario, correo, direccion_cliente, clave, estado_cliente, telefono
        FROM tb_usuarios
        WHERE id_usuario = ? ';
        $params = array($_SESSION['idUsuario']);
        return Database::getRow($sql, $params);
    }

    public function editProfileS()
    {
        $sql = 'UPDATE tb_usuarios
                SET nombre = ?, usuario = ?, correo = ?, telefono = ?, direccion_cliente = ?
                WHERE id_usuario = ?';
        $params = array($this->nombre, $this->alias, $this->correo, $this->telefono, $this->direccion, $_SESSION['idUsuario']);
        return Database::executeRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE cliente
                SET nombre_cliente = ?, apellido_cliente = ?, dui_cliente = ?, estado_cliente = ?, telefono_cliente = ?, nacimiento_cliente = ?, direccion_cliente = ?
                WHERE id_cliente = ?';
        $params = array($this->nombre, $this->apellido, $this->dui, $this->estado, $this->telefono, $this->nacimiento, $this->direccion, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM cliente
                WHERE id_cliente = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function checkDuplicate($value)
    {
        // Consulta para verificar si el correo electrónico ya existe en la base de datos.
        $sql = 'SELECT id_usuario
            FROM tb_usuarios
            WHERE correo = ?';
        $params = array($value);
        return Database::getRow($sql, $params);
    }


    public function generarPinRecuperacion()
    {
        $pin = sprintf("%06d", mt_rand(1, 999999)); // Genera un PIN de 6 dígitos
        $expiry = date('Y-m-d H:i:s', strtotime('+30 minutes')); // 30 minutos desde ahora

        $sql = "UPDATE tb_usuarios SET recovery_pin = ?, pin_expiry = ? WHERE correo = ?";
        $params = array($pin, $expiry, $this->correo);

        if (Database::executeRow($sql, $params)) {
            return $pin; // Retorna el PIN para enviarlo al usuario
        } else {
            // Manejo de errores
            error_log("Error al generar el PIN de recuperación para el correo: " . $this->correo);
        }
        return false;
    }

    public function verificarPinRecuperacion($pin)
    {
        $sql = "SELECT id_usuario FROM tb_usuarios 
            WHERE correo = ? AND recovery_pin = ? AND pin_expiry > NOW()";
        $params = array($this->correo, $pin);

        $result = Database::getRow($sql, $params);

        if ($result) {
            return $result['id_usuario'];
        } else {
            // Manejo de errores
            error_log("Error al verificar el PIN de recuperación para el correo: " . $this->correo);
        }
        return false;
    }

    public function resetearPin()
    {
        $sql = "UPDATE tb_usuarios SET recovery_pin = NULL, pin_expiry = NULL WHERE id_usuario = ?";
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function cambiarClaveConPin($id_usuario, $nuevaClave)
    {
        $sql = 'UPDATE tb_usuarios SET clave = ? WHERE id_usuario = ?';
        $params = array(password_hash($nuevaClave, PASSWORD_DEFAULT), $id_usuario);
        return Database::executeRow($sql, $params);
    }

}
