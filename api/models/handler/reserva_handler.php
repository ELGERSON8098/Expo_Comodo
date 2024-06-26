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
    protected $id_reserva = null;
    protected $nombre = null;
    protected $apellido = null;
    protected $correo = null;
    protected $alias = null;
    protected $clave = null;
    protected $nivel = null;
    const RUTA_IMAGEN = '../../images/productos/';

    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT 
                    r.id_reserva,
                    r.id_usuario,
                    u.usuario,
                    r.fecha_reserva,
                    r.estado_reserva
                FROM 
                    tb_reservas r
                INNER JOIN 
                    tb_usuarios u ON r.id_usuario = u.id_usuario
                where u.usuario like ?';
        $params = array($value);
        return Database::getRows($sql, $params);
    }


    public function readAll()
    {
        $sql = 'SELECT 
                    r.id_reserva,
                    r.id_usuario,
                    u.usuario,
                    r.fecha_reserva,
                    r.estado_reserva
                FROM 
                    tb_reservas r
                INNER JOIN 
                    tb_usuarios u ON r.id_usuario = u.id_usuario';
        return Database::getRows($sql);
    }

//Es una función que obtiene los detalles de una reserva específica, incluyendo información del usuario y del distrito asociado
    public function readOne()
    {
        $sql = 'SELECT 
                    r.id_usuario, 
                    r.id_reserva, 
                    r.estado_reserva, 
                    r.fecha_reserva,
                    d.distrito,
                    u.nombre AS nombre_usuario
                FROM 
                    tb_reservas r
                INNER JOIN 
                    tb_usuarios u ON r.id_usuario = u.id_usuario
                INNER JOIN 
                    tb_distritos d ON r.id_distrito = d.id_distrito
                WHERE r.id_reserva = ?';

        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

//Esta funcion es la que se utiliza para mostrar los datos de la segunda tabla
    public function readDetalles()
    {
        $sql = 'SELECT 
        p.nombre_producto, 
        p.imagen, 
        r.fecha_reserva,
        dr.id_detalle_reserva
    FROM 
        tb_detalles_reservas dr
    INNER JOIN 
        tb_reservas r ON dr.id_reserva = r.id_reserva
    INNER JOIN 
        tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
    INNER JOIN 
        tb_productos p ON dp.id_producto = p.id_producto
    WHERE 
        dr.id_reserva = ?';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }

    // Esta funcion es la que se utiliza cuando se abre el modal dentro de la segunda tabla para mostrar los detalles del producto
    public function readDetalles2()
    {
        $sql = 'SELECT 
    dr.cantidad,
    c.color,
    t.nombre_talla,
    dr.precio_unitario
FROM 
    tb_detalles_reservas dr
INNER JOIN 
    tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
INNER JOIN 
    tb_colores c ON dp.id_color = c.id_color
INNER JOIN 
    tb_tallas t ON dp.id_talla = t.id_talla
    WHERE 
        dr.id_detalle_reserva = ?'; // Ajusta esta condición según tus necesidades

        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function readOneS()
    {
        $sql = 'SELECT 
                dr.id_detalle_reserva,
                dr.id_reserva,
                dr.cantidad,
                r.estado_reserva,
                r.fecha_reserva,
                u.nombre AS nombre_usuario,
                d.distrito,
                m.municipio,
                dept.departamento
            FROM 
                tb_detalles_reservas dr
            INNER JOIN 
                tb_reservas r ON dr.id_reserva = r.id_reserva
            INNER JOIN 
                tb_usuarios u ON r.id_usuario = u.id_usuario
            INNER JOIN 
                tb_distritos d ON r.id_distrito = d.id_distrito
            INNER JOIN 
                tb_municipios m ON d.id_municipio = m.id_municipio
            INNER JOIN 
                tb_departamentos dept ON m.id_departamento = dept.id_departamento
            WHERE dr.id_detalle_reserva = ?';

        $params = array($this->id);
        return Database::getRow($sql, $params);
    }




    public function readAlls()
    {
        $sql = 'SELECT 
                dr.id_detalle_reserva,
                dr.id_reserva,
                dr.cantidad,
                dr.precio_unitario,
                r.estado_reserva,
                r.fecha_reserva,
                u.nombre AS nombre_usuario,
                d.distrito,
                m.municipio,
                dept.departamento
            FROM 
                tb_detalles_reservas dr
            INNER JOIN 
                tb_reservas r ON dr.id_reserva = r.id_reserva
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

    public function deleteRow()
    {
        $sql = 'DELETE FROM administrador
                WHERE id_administrador = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}
