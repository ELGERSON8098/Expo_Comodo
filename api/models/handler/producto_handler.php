<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class productoHandler
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
    protected $id_detalle_producto = null;
    protected $material = null;
    protected $id_talla = null;
    protected $precio = null;
    
    
     /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_administrador, user_administrador, clave_administrador
                FROM tbAdmins
                WHERE  user_administrador = ?';
        $params = array($username);
        $data = Database::getRow($sql, $params);
        if (password_verify($password, $data['clave_administrador'])) {
            $_SESSION['idAdministrador'] = $data['id_administrador'];
            $_SESSION['aliasAdministrador'] = $data['user_administrador'];
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
        $sql = 'UPDATE administrador
                SET clave_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['idadministrador']);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, correo_administrador, alias_administrador
                FROM administrador
                WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE administrador
                SET nombre_administrador = ?, apellido_administrador = ?, correo_administrador = ?, alias_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->alias, $_SESSION['idAdministrador']);
        return Database::executeRow($sql, $params);
    }

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_administrador, nombre_administrador, apellido_administrador, correo_administrador, alias_administrador
                FROM administrador
                WHERE apellido_administrador LIKE ? OR nombre_administrador LIKE ?
                ORDER BY apellido_administrador';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        // Verificar si la tabla está vacía
        $sql = 'SELECT COUNT(*) AS count FROM tbAdmins';
        $result = Database::getRow($sql);
    
        // Si la tabla está vacía, asignar el nivel de usuario "Administrador" por defecto
        if ($result['count'] == 0) {
            // Obtener el ID del nivel de usuario correspondiente a "Administrador"
            $sql = 'SELECT id_nivel_usuario FROM tbniveles_usuario WHERE nombre_nivel = "Administrador"';
            $nivelAdministrador = Database::getRow($sql);
    
            // Verificar si se obtuvo el ID del nivel de usuario
            if ($nivelAdministrador && isset($nivelAdministrador['id_nivel_usuario'])) {
                // Insertar el administrador con el nivel correspondiente
                $sql = 'INSERT INTO tbAdmins(nombre_administrador, user_administrador, correo_administrador, clave_administrador, id_nivel_usuario)
                        VALUES(?, ?, ?, ?, ?)';
                $params = array($this->nombre, $this->alias, $this->correo, $this->clave, $nivelAdministrador['id_nivel_usuario']);
                return Database::executeRow($sql, $params);
            } else {
                // Manejar el caso en el que no se encontró el ID del nivel de usuario
                return false; // O mostrar un mensaje de error, lanzar una excepción, etc.
            }
        } else {
            // Si la tabla no está vacía, insertar el administrador sin modificar el nivel de usuario
            $sql = 'INSERT INTO tbAdmins(nombre_administrador, user_administrador, correo_administrador, clave_administrador, id_nivel_usuario)
                    VALUES(?, ?, ?, ?, ?)';
            $params = array($this->nombre, $this->alias, $this->correo, $this->clave, $this->nivel);
            return Database::executeRow($sql, $params);
        }
    }
    
    
    
    
    public function readAll()
    {
        $sql = 'SELECT id_producto, nombre_producto, codigo_interno, referencia_proveedor, imagen
                FROM tb_productos';
                    
        return Database::getRows($sql);
    }
    

    public function readOne()
    {
        $sql = "SELECT * FROM tbproductos 
        INNER JOIN tbdetalles_producto USING (id_producto)
        INNER JOIN tbmarca USING (id_marca)
        INNER JOIN tbcolor USING (id_color)
        INNER JOIN tbdescuentos USING (id_descuento)
        INNER JOIN tbcategorias USING (id_categoria)
        INNER JOIN tbsubcategorias USING (id_subcategoria)
        WHERE id_producto = ?;";
    
        $params = array($id_producto);

        return Database::getRow($sql, $params);    
    }

    public function readFilename()
    {
        $sql = 'SELECT imagen
                FROM tbproductos
                WHERE id_producto = ?';
        $params = array($this->id);
        $sql = "SELECT p.id_producto, p.nombre_producto, p.codigo_interno, p.Referencia_provedor,
                dp.id_detalle_producto, dp.material, dp.descripcion, dp.precio, dp.imagen_detale_producto, dp.existencias,
                dp.id_talla, dp.id_color, dp.id_marca, dp.id_descuento, dp.id_categoria, dp.id_subcategoria
                FROM tbproductos p
                INNER JOIN tbdetalles_producto dp ON p.id_producto = dp.id_producto";
    
        return Database::getRows($sql);
    }
    

    public function updateRow()
    {
        $sql = 'UPDATE administrador
                SET nombre_administrador = ?, apellido_administrador = ?, correo_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->apellido, $this->correo, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM administrador
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
