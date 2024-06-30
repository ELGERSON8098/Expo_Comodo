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
    protected $estado_reserva = null;
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
    dr.precio_unitario,
    p.codigo_interno,
    p.referencia_proveedor,
    m.marca AS nombre_marca,
    g.nombre_genero AS nombre_genero,
    u.nombre AS nombre_usuario,
    u.correo AS correo_usuario,
    u.direccion_cliente AS direccion_usuario,
    u.telefono AS telefono_usuario,
    u.dui_cliente AS dui_usuario,
    p.nombre_producto AS nombre_producto,
    d.nombre_descuento AS nombre_descuento,
    ROUND(d.valor, 2) AS valor_descuento,  -- Redondea el valor del descuento a 2 decimales
    CASE 
        WHEN d.valor IS NOT NULL THEN ROUND(dr.precio_unitario * (1 - d.valor / 100), 2)
        ELSE dr.precio_unitario
    END AS precio_con_descuento
FROM 
    tb_detalles_reservas dr
INNER JOIN 
    tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
INNER JOIN 
    tb_colores c ON dp.id_color = c.id_color
INNER JOIN 
    tb_tallas t ON dp.id_talla = t.id_talla
INNER JOIN 
    tb_productos p ON dp.id_producto = p.id_producto
INNER JOIN 
    tb_marcas m ON p.id_marca = m.id_marca
INNER JOIN 
    tb_generos_zapatos g ON p.id_genero = g.id_genero
INNER JOIN 
    tb_reservas r ON dr.id_reserva = r.id_reserva
INNER JOIN 
    tb_usuarios u ON r.id_usuario = u.id_usuario
INNER JOIN 
    tb_descuentos d ON p.id_descuento = d.id_descuento
WHERE 
    dr.id_detalle_reserva = ?;';

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

    public function UpdateORW()
{
    // La consulta SQL para actualizar el estado de la reserva
    $sql = 'UPDATE tb_reservas SET estado_reserva = ? WHERE id_reserva = ?';
    
    // Los parámetros que se pasarán a la consulta
    $params = array($this->estado_reserva, $this->id);
    
    // Ejecutar la consulta y devolver el resultado
    return Database::executeRow($sql, $params);
}

public function readEstado()
{
    $sql = 'SELECT estado_reserva FROM tb_reservas WHERE id_reserva = ?';
    $params = array($this->id);
    return Database::getRows($sql, $params);
}


}
