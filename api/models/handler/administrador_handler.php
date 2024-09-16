<?php
require_once('../../helpers/database.php');

class AdministradorHandler
{
    protected $id = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $alias = null;
    protected $clave = null;
    protected $reset_code = null;
    protected $id_nivel_usuario = 1;

    public function checkUser($username, $password)
    {
        // Primero, obtenemos los detalles del usuario.
        $sql = 'SELECT id_administrador, usuario_administrador, clave_administrador, intentos_fallidos, bloqueo_hasta
                FROM tb_admins
                WHERE usuario_administrador = ?';
        $params = array($username);
        $data = Database::getRow($sql, $params);

        if (!$data) {
            return false; // Usuario no encontrado
        }

        // Verificamos si el usuario está bloqueado
        if ($data['bloqueo_hasta'] && new DateTime() < new DateTime($data['bloqueo_hasta'])) {
            return 'bloqueado'; // Usuario está bloqueado
        }

        if (password_verify($password, $data['clave_administrador'])) {
            // Inicio de sesión exitoso
            $this->resetIntentos($data['id_administrador']);
            // Generar y almacenar código 2FA
            $codigo2FA = $this->generar2FACode($data['id_administrador']);
            return ['status' => true, 'id_administrador' => $data['id_administrador'], 'codigo2FA' => $codigo2FA];
        } else {
            // Contraseña incorrecta
            $this->incrementarIntentos($data['id_administrador'], $data['intentos_fallidos']);
            return false;
        }
    }

    public function isTwoFactorEnabled($idAdministrador)
    {
        $sql = 'SELECT two_factor_enabled FROM tb_admins WHERE id_administrador = ?';
        $params = array($idAdministrador);
        $result = Database::getRow($sql, $params);

        return $result && $result['two_factor_enabled'] == 1;
    }

    public function getBlockDataByAlias($alias)
    {
        $sql = 'SELECT id_administrador, intentos_fallidos, bloqueo_hasta
            FROM tb_admins
            WHERE usuario_administrador = ?';
        $params = array($alias);
        return Database::getRow($sql, $params);
    }

    private function incrementarIntentos($id_administrador, $intentos_fallidos)
    {
        $intentos_fallidos++;
        $bloqueo_hasta = null;

        if ($intentos_fallidos >= 3) {
            $bloqueo_hasta = (new DateTime())->add(new DateInterval('PT24H'))->format('Y-m-d H:i:s');
        }

        $sql = 'UPDATE tb_admins
                SET intentos_fallidos = ?, bloqueo_hasta = ?
                WHERE id_administrador = ?';
        $params = array($intentos_fallidos, $bloqueo_hasta, $id_administrador);
        Database::executeRow($sql, $params);
    }

    private function resetIntentos($id_administrador)
    {
        $sql = 'UPDATE tb_admins
                SET intentos_fallidos = 0, bloqueo_hasta = NULL
                WHERE id_administrador = ?';
        $params = array($id_administrador);
        Database::executeRow($sql, $params);
    }

    public function checkPassword($password)
    {
        $sql = 'SELECT clave_administrador
                FROM tb_admins
                WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        $data = Database::getRow($sql, $params);
        return password_verify($password, $data['clave_administrador']);
    }

    public function changePassword()
    {
        $sql = 'UPDATE tb_admins
                SET clave_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, correo_administrador, usuario_administrador
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

    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT a.id_administrador, a.nombre_administrador, a.correo_administrador, a.usuario_administrador, n.nombre_nivel
            FROM tb_admins a
            INNER JOIN tb_niveles_usuarios n ON a.id_nivel_usuario = n.id_nivel_usuario
            WHERE a.nombre_administrador LIKE ?
            ORDER BY a.nombre_administrador';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_admins(nombre_administrador, usuario_administrador, correo_administrador, clave_administrador, id_nivel_usuario)
                VALUES (?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->alias, $this->correo, $this->clave, 1);
        return Database::executeRow($sql, $params);
    }

    public function checkIfAnyUserExists()
    {
        $sql = 'SELECT COUNT(*) FROM tb_admins';
        return Database::getRow($sql); // Retorna el número de administradores registrados
    }

    public function adminExists()
    {
        $sql = 'SELECT COUNT(*) FROM tb_admins WHERE id_nivel_usuario = 1';
        $params = array(); // No se necesitan parámetros adicionales
        $result = Database::getRow($sql, $params); // Ejecuta la consulta
        return $result[0] > 0; // Retorna true si hay al menos un administrador
    }
    public function createTrabajadores()
    {
        $sql = 'INSERT INTO tb_admins(nombre_administrador, correo_administrador, usuario_administrador, clave_administrador, id_nivel_usuario)
            VALUES(?, ?, ?, ?, ?)';
        $params = array($this->nombre, $this->correo, $this->alias, $this->clave, $this->id_nivel_usuario);
        return Database::executeRow($sql, $params);
    }

    public function readAllS()
    {
        $sql = 'SELECT a.id_administrador, a.nombre_administrador, a.correo_administrador, a.usuario_administrador, n.nombre_nivel
        FROM tb_admins a
        JOIN tb_niveles_usuarios n ON a.id_nivel_usuario = n.id_nivel_usuario
        WHERE a.id_nivel_usuario IN (2, 3)
        ORDER BY a.nombre_administrador';
        return Database::getRows($sql);
    }

    public function readAll()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, correo_administrador, usuario_administrador
                FROM tb_admins
                ORDER BY nombre_administrador';
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

    public function obtenerAdministradores()
    {
        $sql = 'SELECT 
                a.usuario_administrador AS usuario,
                a.correo_administrador AS correo,
                n.nombre_nivel AS nivel_usuario
            FROM 
                tb_admins a
            JOIN 
                tb_niveles_usuarios n ON a.id_nivel_usuario = n.id_nivel_usuario
            ORDER BY 
                a.usuario_administrador ASC;';
        return Database::getRows($sql);
    }

    public function checkEmail()
    {
        $sql = 'SELECT id_administrador FROM tb_admins WHERE correo_administrador = ?';
        $params = array($this->correo);
        return Database::getRow($sql, $params) ? true : false;
    }

    public function setResetCode($codigo)
    {
        $sql = 'UPDATE tb_admins SET reset_code = ?, reset_code_expiry = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE correo_administrador = ?';
        $params = array($codigo, $this->correo);
        return Database::executeRow($sql, $params);
    }

    public function verifyResetCode()
    {
        $sql = 'SELECT id_administrador FROM tb_admins WHERE correo_administrador = ? AND reset_code = ? AND reset_code_expiry > NOW()';
        $params = array($this->correo, $this->reset_code);
        return Database::getRow($sql, $params) ? true : false;
    }

    public function resetPassword()
    {
        // SQL para actualizar la contraseña, eliminar el código de restablecimiento y su caducidad.
        $sql = 'UPDATE tb_admins 
                SET clave_administrador = ?, reset_code = NULL, reset_code_expiry = NULL 
                WHERE correo_administrador = ? AND reset_code = ? AND reset_code_expiry > NOW()';

        // Parámetros que se pasan a la consulta.
        $params = array($this->clave, $this->correo, $this->reset_code);

        // Ejecutamos la consulta y retornamos el resultado.
        return Database::executeRow($sql, $params);
    }
    private function generar2FACode($id_administrador)
    {
        $codigo = sprintf("%06d", mt_rand(1, 999999));

        $sql = "UPDATE tb_admins SET codigo_2fa = ?, expiracion_2fa = DATE_ADD(NOW(), INTERVAL 5 MINUTE) WHERE id_administrador = ?";
        $params = array($codigo, $id_administrador);
        Database::executeRow($sql, $params);

        return $codigo;
    }

    public function verify2FACode($id_administrador, $codigo)
    {
        $sql = 'SELECT codigo_2fa, expiracion_2fa FROM tb_admins WHERE id_administrador = ?';
        $params = array($id_administrador);
        $data = Database::getRow($sql, $params);

        if ($data && $data['codigo_2fa'] == $codigo && new DateTime() < new DateTime($data['expiracion_2fa'])) {
            $sql = 'UPDATE tb_admins SET codigo_2fa = NULL, expiracion_2fa = NULL WHERE id_administrador = ?';
            Database::executeRow($sql, array($id_administrador));
            return true;
        }
        return false;
    }


    public function getEmailById($id_administrador)
    {
        $sql = 'SELECT correo_administrador FROM tb_admins WHERE id_administrador = ?';
        $params = array($id_administrador);
        $data = Database::getRow($sql, $params);
        return $data ? $data['correo_administrador'] : null;
    }

    public function getAliasById($id_administrador)
    {
        $sql = 'SELECT usuario_administrador FROM tb_admins WHERE id_administrador = ?';
        $params = array($id_administrador);
        $data = Database::getRow($sql, $params);
        return $data ? $data['usuario_administrador'] : null;
    }
}
