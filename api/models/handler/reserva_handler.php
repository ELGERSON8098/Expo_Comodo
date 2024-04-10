<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class reservaHandler
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
    protected $nivel = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */
    public function checkUser($username, $password)
    {
        $sql = 'SELECT id_administrador, usuario_administrador, clave_administrador
                FROM tb_admins
                WHERE  usuario_administrador = ?';
        $params = array($username);
        $data = Database::getRow($sql, $params);
        if (password_verify($password, $data['clave_administrador'])) {
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
        $sql = 'UPDATE administrador
                SET clave_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->clave, $_SESSION['idadministrador']);
        return Database::executeRow($sql, $params);
    }

    public function readProfile()
    {
        $sql = 'SELECT id_administrador, nombre_administrador, usuario_administrador, correo_administrador, id_nivel_usuario
                FROM tb_admins
                WHERE id_administrador = ?';
        $params = array($_SESSION['idAdministrador']);
        return Database::getRow($sql, $params);
    }

    public function editProfile()
    {
        $sql = 'UPDATE tb_admins
                SET nombre_administrador = ?, usuario_administrador= ?, correo_administrador = ?
                WHERE id_administrador = ?';
        $params = array($this->nombre, $this->alias, $this->correo,  $_SESSION['idAdministrador']);
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
        $sql = 'SELECT COUNT(*) AS count FROM tb_admins';
        $result = Database::getRow($sql);

        // Si la tabla está vacía, asignar el nivel de usuario "Administrador" por defecto
        if ($result['count'] == 0) {
            // Obtener el ID del nivel de usuario correspondiente a "Administrador"
            $sql = 'SELECT id_nivel_usuario FROM tb_niveles_usuarios WHERE nombre_nivel = "administrador"';
            $nivelAdministrador = Database::getRow($sql);

            // Verificar si se obtuvo el ID del nivel de usuario
            if ($nivelAdministrador && isset($nivelAdministrador['id_nivel_usuario'])) {
                // Insertar el administrador con el nivel correspondiente
                $sql = 'INSERT INTO tb_admins(nombre_administrador, usuario_administrador, correo_administrador, clave_administrador, id_nivel_usuario)
                        VALUES(?, ?, ?, ?, ?)';
                $params = array($this->nombre, $this->alias, $this->correo, $this->clave, $nivelAdministrador['id_nivel_usuario']);
                return Database::executeRow($sql, $params);
            } else {
                // Manejar el caso en el que no se encontró el ID del nivel de usuario
                return false; // O mostrar un mensaje de error, lanzar una excepción, etc.
            }
        } else {
            // Si la tabla no está vacía, insertar el administrador sin modificar el nivel de usuario
            $sql = 'INSERT INTO tb_admins(nombre_administrador, usuario_administrador, correo_administrador, clave_administrador, id_nivel_usuario)
                    VALUES(?, ?, ?, ?, ?)';
            $params = array($this->nombre, $this->alias, $this->correo, $this->clave, $this->nivel);
            return Database::executeRow($sql, $params);
        }
    }





    public function readAll()
    {
        $sql = 'SELECT 
        u.id_usuario,
        r.id_reserva,
        u.nombre,
        r.fecha_reserva,
        d.distrito,
        m.municipio,
        dept.departamento
    FROM 
        tb_reservas r
    INNER JOIN 
        tb_usuarios u ON r.id_usuario = u.id_usuario
    INNER JOIN 
        tb_distritos d ON r.id_distrito = d.id_distrito
    INNER JOIN 
        tb_municipios m ON d.id_municipio = m.id_municipio
    INNER JOIN 
        tb_departamentos dept ON m.id_departamento = dept.id_departamento';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT r.id_usuario,dr.id_detalle_reserva,r.id_reserva,u.nombre AS nombre_usuario,u.telefono,
        r.fecha_reserva,d.distrito,p.id_producto,p.nombre_producto,dp.material,dp.id_talla,
        t.nombre_talla,dp.precio AS precio_original,dr.cantidad,dr.precio_unitario,
        c.color,m.marca,g.nombre_genero,cat.nombre_categoria,k.nombre_descuento,
        k.valor AS valor_descuento,
        ROUND(
            CASE 
                WHEN k.valor IS NOT NULL THEN dp.precio - (dp.precio * (k.valor / 100))
                ELSE dp.precio
            END,
            2
        ) AS precio_con_descuento
    FROM 
        tb_detalles_reservas dr
    INNER JOIN 
        tb_reservas r ON dr.id_reserva = r.id_reserva
    INNER JOIN 
        tb_usuarios u ON r.id_usuario = u.id_usuario
    INNER JOIN 
        tb_distritos d ON r.id_distrito = d.id_distrito
    INNER JOIN 
        tb_productos p ON dr.id_producto = p.id_producto
    INNER JOIN 
        tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
    INNER JOIN 
        tb_tallas t ON dp.id_talla = t.id_talla
    INNER JOIN 
        tb_colores c ON dp.id_color = c.id_color
    INNER JOIN 
        tb_marcas m ON dp.id_marca = m.id_marca
    INNER JOIN 
        tb_generos_zapatos g ON dp.id_genero = g.id_genero
    INNER JOIN 
        tb_categorias cat ON dp.id_categoria = cat.id_categoria
    LEFT JOIN 
        tb_descuentos k ON dp.id_descuento = k.id_descuento
    WHERE dr.id_reserva=?;';

        $params = array($this->id);
        return Database::getRow($sql,$params);
    }


    public function deleteRow()
    {
        $sql = 'DELETE FROM administrador
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
