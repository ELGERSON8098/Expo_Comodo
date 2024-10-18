<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');

/*
 *  Clase para manejar el comportamiento de los datos de la tabla RESERVA.
 */
class ReservaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id_reserva = null;
    protected $id_usuario = null;
    protected $fecha_reserva = null;
    protected $estado = null;
    protected $id_detalle_producto = null;
    protected $cantidad = null;
    protected $precio_unitario = null;
    protected $id_detalle_reserva = null;

    protected $fecha_inicio = null;
    protected $fecha_fin = null;

    // Constante para establecer la ruta de las imágenes (si aplica).
    const RUTA_IMAGEN = '../../images/productos/';

    /*
     * Método para crear una nueva reserva.
     */
    public function createRow()
    {
        $sql = 'INSERT INTO tb_reservas(id_usuario, fecha_reserva, estado_reserva)
                VALUES(?, ?, ?)';
        $params = array($this->id_usuario, $this->fecha_reserva, $this->estado);
        return Database::executeRow($sql, $params);
    }

    /*
     * Método para leer todas las reservas.
     */
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
    tb_usuarios u ON r.id_usuario = u.id_usuario
    ORDER BY
    r.fecha_reserva DESC;';
        return Database::getRows($sql);
    }
    public function readGroupedByClient()
    {
        $sql = 'SELECT u.id_usuario, u.usuario, 
                   COUNT(r.id_reserva) as total_reservas, 
                   GROUP_CONCAT(r.fecha_reserva ORDER BY r.fecha_reserva DESC) as fechas_reservas,
                   GROUP_CONCAT(r.estado_reserva ORDER BY r.fecha_reserva DESC) as estados_reservas
            FROM tb_usuarios u
            LEFT JOIN tb_reservas r ON u.id_usuario = r.id_usuario
            GROUP BY u.id_usuario, u.usuario
            ORDER BY total_reservas DESC';
        return Database::getRows($sql);
    }

    public function readClientReservas($idUsuario)
    {
        $sql = 'SELECT id_reserva, fecha_reserva, estado_reserva
            FROM tb_reservas
            WHERE id_usuario = ?
            ORDER BY fecha_reserva DESC';
        $params = array($idUsuario);
        return Database::getRows($sql, $params);
    }

    public function readFiltered($estado)
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
                tb_usuarios u ON r.id_usuario = u.id_usuario
            WHERE
                r.estado_reserva = ?
            ORDER BY r.fecha_reserva DESC;';
        $params = array($estado);
        return Database::getRows($sql, $params);
    }

    /*
     * Método para leer una reserva específica.
     */
    public function readOne()
    {
        $sql = 'SELECT
                    r.id_usuario,
                    r.id_reserva,
                    r.estado_reserva,
                    r.fecha_reserva,
                    u.nombre AS nombre_usuario
                FROM
                    tb_reservas r
                INNER JOIN
                    tb_usuarios u ON r.id_usuario = u.id_usuario
                WHERE r.id_reserva = ?';
        $params = array($this->id_reserva);
        return Database::getRow($sql, $params);
    }

    /*
     * Método para buscar reservas por usuario.
     */
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
                WHERE u.usuario LIKE ?';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    /*
     * Método para actualizar el estado de una reserva.
     */
    public function updateRow()
    {
        $sql = 'UPDATE tb_reservas
                SET estado_reserva = ?
                WHERE id_reserva = ?';
        $params = array($this->estado, $this->id_reserva);
        return Database::executeRow($sql, $params);
    }

    /*
     * Método para eliminar una reserva específica.
     */

    /*
     * Métodos CRUD para los detalles de la reserva.
     */
    public function createDetail()
    {
        $sql = 'INSERT INTO tb_detalles_reservas(id_reserva, id_detalle_producto, cantidad, precio_unitario)
                VALUES(?, ?, ?, ?)';
        $params = array($this->id_reserva, $this->id_detalle_producto, $this->cantidad, $this->precio_unitario);
        return Database::executeRow($sql, $params);
    }

    public function readDetails()
    {
        $sql = 'SELECT
                    dr.id_detalle_reserva,
                    p.nombre_producto,
                    p.imagen,
                    r.fecha_reserva,
                    dr.cantidad,
                    dr.precio_unitario
                FROM
                    tb_detalles_reservas dr
                 INNER JOIN 
                tb_reservas r ON dr.id_reserva = r.id_reserva
                INNER JOIN tb_detalles_productos dp 
                USING(id_detalle_producto)
                INNER JOIN 
                tb_productos p ON dp.id_producto = p.id_producto
                WHERE dr.id_reserva = ?';
        $params = array($this->id_reserva);
        return Database::getRows($sql, $params);
    }

    public function updateDetail()
    {
        $sql = 'UPDATE tb_detalles_reservas
                SET id_detalle_producto = ?, cantidad = ?, precio_unitario = ?
                WHERE id_detalle_reserva = ?';
        $params = array($this->id_detalle_producto, $this->cantidad, $this->precio_unitario, $this->id_detalle_reserva);
        return Database::executeRow($sql, $params);
    }

    public function deleteDetail()
    {
        $sql = 'DELETE FROM tb_detalles_reservas
                WHERE id_detalle_reserva = ?';
        $params = array($this->id_detalle_reserva);
        return Database::executeRow($sql, $params);
    }

    public function readOneDetail()
    {
        $sql = 'SELECT
                    dr.id_detalle_reserva,
                    dr.id_reserva,
                    dr.id_detalle_producto,
                    dr.cantidad,
                    dr.precio_unitario,
                    p.nombre_producto,
                    p.imagen
                FROM
                    tb_detalles_reservas dr
                INNER JOIN
                    tb_productos p ON dr.id_detalle_producto = p.id_producto
                WHERE dr.id_detalle_reserva = ?';
        $params = array($this->id_detalle_reserva);
        return Database::getRow($sql, $params);
    }

    /*
     * Método para leer detalles específicos de una reserva para el formulario.
     */
    public function readOneDetailForForm()
    {
        $sql = 'SELECT
    u.usuario,
    u.dui_cliente ,
    u.telefono ,
    p.nombre_producto ,
    p.codigo_interno ,
    p.referencia_proveedor,
    m.marca ,
    g.nombre_genero ,
    c.color,
    dr.cantidad,
    t.nombre_talla,
    dr.precio_unitario,
    d.valor,
    (p.precio - (p.precio * (d.valor / 100))) AS precio_descuento,
    u.direccion_cliente
    FROM
    tb_detalles_reservas dr
    INNER JOIN
    tb_reservas r ON dr.id_reserva = r.id_reserva
    INNER JOIN
    tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
    INNER JOIN
    tb_productos p ON dp.id_producto = p.id_producto
    INNER JOIN
    tb_usuarios u ON r.id_usuario = u.id_usuario
    LEFT JOIN
    tb_marcas m ON p.id_marca = m.id_marca
    LEFT JOIN
    tb_generos_zapatos g ON p.id_genero = g.id_genero
    LEFT JOIN
    tb_colores c ON dp.id_color = c.id_color
    LEFT JOIN
    tb_tallas t ON dp.id_talla = t.id_talla
    LEFT JOIN
    tb_descuentos d ON p.id_descuento = d.id_descuento
    WHERE
    dr.id_detalle_reserva = ?';

        $params = array($this->id_detalle_reserva);
        return Database::getRow($sql, $params);
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
    LEFT JOIN 
    tb_descuentos d ON p.id_descuento = d.id_descuento
    WHERE 
    dr.id_detalle_reserva = ?';

        $params = array($this->id_detalle_reserva);
        return Database::getRow($sql, $params);
    }
    //Metodo para grafica automatica de reservas
    public function cantidadReservasEstado()
    {
        $sql = 'SELECT estado_reserva, COUNT(id_reserva) AS cantidad
                FROM tb_reservas
                GROUP BY estado_reserva';
        return Database::getRows($sql);
    }

    public function ventasPorCategoriaFecha($fecha_inicio, $fecha_fin)
    {
        $sql = 'SELECT 
    c.nombre_categoria, 
    SUM(dr.cantidad * dr.precio_unitario) AS total_ventas
    FROM 
    tb_detalles_reservas dr
    INNER JOIN 
    tb_reservas r ON dr.id_reserva = r.id_reserva
    INNER JOIN 
    tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
    INNER JOIN 
    tb_productos p ON dp.id_producto = p.id_producto
    INNER JOIN 
    tb_categorias c ON p.id_categoria = c.id_categoria
    WHERE 
    r.fecha_reserva BETWEEN ? AND ?
    GROUP BY 
    c.nombre_categoria
    LIMIT 5;';
        $params = array($fecha_inicio, $fecha_fin);
        return Database::getRows($sql, $params);
    }

    public function ventasPorMarcasFecha($fecha_inicio, $fecha_fin)
    {
        $sql = 'SELECT 
    m.marca AS nombre_marca, 
    r.fecha_reserva, 
    SUM(dr.cantidad * dr.precio_unitario) AS total_ventas
    FROM 
    tb_detalles_reservas dr
    INNER JOIN 
    tb_reservas r ON dr.id_reserva = r.id_reserva
    INNER JOIN 
    tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
    INNER JOIN 
    tb_productos p ON dp.id_producto = p.id_producto
    INNER JOIN 
    tb_marcas m ON p.id_marca = m.id_marca
    WHERE 
    r.fecha_reserva BETWEEN ? AND ?
    GROUP BY 
    m.marca, r.fecha_reserva
    ORDER BY 
    r.fecha_reserva ASC;';
        $params = array($fecha_inicio, $fecha_fin);
        return Database::getRows($sql, $params);
    }

    //Funcion de la consulta para generar reporte

    public function reportePedido()
    {
        $sql = 'SELECT
    u.id_usuario,
    p.nombre_producto AS Producto,
    u.nombre AS NombreUsuario,
    r.fecha_reserva AS FechaReserva,
    dr.cantidad AS CantidadLibros,
    dr.precio_unitario AS PrecioUnitario,
    (dr.precio_unitario * dr.cantidad) AS Subtotal
FROM
    tb_reservas r
JOIN
    tb_usuarios u ON r.id_usuario = u.id_usuario  
JOIN
    tb_detalles_reservas dr ON r.id_reserva = dr.id_reserva
JOIN
    tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
JOIN
    tb_productos p ON dp.id_producto = p.id_producto
WHERE
    r.estado_reserva = "Aceptado"
ORDER BY
    r.fecha_reserva DESC;  -- Ordenar por fecha de reserva;';

        return Database::getRows($sql);
    }

}