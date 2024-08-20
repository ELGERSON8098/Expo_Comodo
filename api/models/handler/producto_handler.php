<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *	Clase para manejar el comportamiento de los datos de la tabla PRODUCTO.
 */
class ProductoHandler
{
    /*
     *   Declaración de atributos para el manejo de datos.
     */
    protected $id_producto = null;
    protected $nombre_producto = null;
    protected $codigo_interno = null;
    protected $referencia_proveedor = null;
    protected $precio = null;
    protected $id_marca = null;
    protected $id_genero = null;
    protected $id_categoria = null;
    protected $id_material = null;
    protected $imagen = null;
    protected $id_descuento = null;
    protected $imagen_producto = null;
    protected $existencias = null;
    protected $id_talla = null;
    protected $id_color = null;
    protected $descripcion = null;

    protected $id_detalle_producto = null;

    // Constante para establecer la ruta de las imágenes.
    const RUTA_IMAGEN = '../../images/productos/';

    public function createRow()
    {
        $sql = 'INSERT INTO tb_productos(nombre_producto, codigo_interno, referencia_proveedor, precio, id_marca, id_genero, id_categoria, id_material, id_descuento, imagen)
        VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $params = array($this->nombre_producto, $this->codigo_interno, $this->referencia_proveedor, $this->precio, $this->id_marca, $this->id_genero, $this->id_categoria, $this->id_material, $this->id_descuento, $this->imagen_producto);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT
        p.id_producto,
        p.nombre_producto,
        p.codigo_interno,
        p.referencia_proveedor,
        p.precio,
        p.imagen,
        m.id_marca,
        m.marca AS nombre_marca,
        g.id_genero,
        g.nombre_genero AS nombre_genero,
        c.id_categoria,
        c.nombre_categoria AS nombre_categoria,
        ma.id_material,
        ma.nombre AS nombre_material,
        d.id_descuento,
        COALESCE(d.nombre_descuento, "Sin descuento") AS nombre_descuento,
        COALESCE(d.valor, 0) AS porcentaje_descuento
    FROM
        tb_productos AS p
    LEFT JOIN
        tb_marcas AS m ON p.id_marca = m.id_marca
    LEFT JOIN
        tb_generos_zapatos AS g ON p.id_genero = g.id_genero
    LEFT JOIN
        tb_categorias AS c ON p.id_categoria = c.id_categoria
    INNER JOIN
        tb_materiales AS ma ON p.id_material = ma.id_material
    LEFT JOIN
        tb_descuentos AS d ON p.id_descuento = d.id_descuento
    ORDER BY
        p.nombre_producto;';
        return Database::getRows($sql);
    }

    public function readProductosCategoria()
    {
        $sql = 'SELECT 
    p.id_producto, 
    p.nombre_producto, 
    p.codigo_interno, 
    p.referencia_proveedor, 
    p.precio, 
    p.imagen, 
    p.id_marca,
    p.id_genero,
    p.id_categoria,
    p.id_material,
    p.id_descuento,
    dp.id_detalle_producto,
    dp.id_talla,
    dp.existencias,
    dp.id_color,
    dp.descripcion AS descripcion_detalle,
    m.marca,
    g.nombre_genero,
    c.nombre_categoria,
    mat.nombre AS nombre_material,
    d.nombre_descuento,
    d.descripcion AS descripcion_descuento,
    d.valor AS valor_descuento,
    t.nombre_talla,
    col.color
FROM 
    tb_productos p
INNER JOIN 
    tb_categorias c ON p.id_categoria = c.id_categoria
INNER JOIN 
    tb_marcas m ON p.id_marca = m.id_marca
INNER JOIN 
    tb_generos_zapatos g ON p.id_genero = g.id_genero
INNER JOIN 
    tb_materiales mat ON p.id_material = mat.id_material
LEFT JOIN 
    tb_descuentos d ON p.id_descuento = d.id_descuento
INNER JOIN 
    tb_detalles_productos dp ON p.id_producto = dp.id_producto
INNER JOIN 
    tb_tallas t ON dp.id_talla = t.id_talla
INNER JOIN 
    tb_colores col ON dp.id_color = col.id_color
WHERE 
    p.id_categoria = ? 
ORDER BY 
    p.nombre_producto';

        $params = array($this->id_categoria);
        return Database::getRows($sql, $params);
    }


    public function readOne()
    {
        $sql = 'SELECT
            p.id_producto,
            p.nombre_producto,
            p.codigo_interno,
            p.referencia_proveedor,
            p.precio,
            p.imagen,
            m.id_marca,
            m.marca AS nombre_marca,
            g.id_genero,
            g.nombre_genero AS nombre_genero,
            c.id_categoria,
            c.nombre_categoria AS nombre_categoria,
            ma.id_material,
            ma.nombre AS nombre_material,
            COALESCE(d.id_descuento, NULL) AS id_descuento,
            COALESCE(d.nombre_descuento, "Sin descuento") AS nombre_descuento,
            COALESCE(d.valor, 0) AS porcentaje_descuento,
            dp.id_detalle_producto,
            dp.existencias,
            dp.descripcion AS descripcion_detalle,
            t.id_talla,
            t.nombre_talla,
            col.id_color,
            col.color
        FROM
            tb_productos AS p
        LEFT JOIN
            tb_marcas AS m ON p.id_marca = m.id_marca
        LEFT JOIN
            tb_generos_zapatos AS g ON p.id_genero = g.id_genero
        LEFT JOIN
            tb_categorias AS c ON p.id_categoria = c.id_categoria
        INNER JOIN
            tb_materiales AS ma ON p.id_material = ma.id_material
        LEFT JOIN
            tb_descuentos AS d ON p.id_descuento = d.id_descuento
        INNER JOIN
            tb_detalles_productos AS dp ON p.id_producto = dp.id_producto
        INNER JOIN
            tb_tallas AS t ON dp.id_talla = t.id_talla
        INNER JOIN
            tb_colores AS col ON dp.id_color = col.id_color
        WHERE
            p.id_producto = ?';
        $params = array($this->id_producto);
        return Database::getRow($sql, $params);
    }

    /*
     * Método para buscar registros de los productos por nombre y por codigo interno.
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT
        p.id_producto,
        p.nombre_producto,
        p.codigo_interno,
        p.referencia_proveedor,
        p.precio,
        p.imagen,
        m.marca AS nombre_marca,
        g.nombre_genero AS nombre_genero,
        c.nombre_categoria AS nombre_categoria,
        ma.nombre AS nombre_material,
        COALESCE(d.id_descuento, NULL) AS id_descuento
    FROM
        tb_productos AS p
    INNER JOIN
        tb_marcas AS m ON p.id_marca = m.id_marca
    INNER JOIN
        tb_generos_zapatos AS g ON p.id_genero = g.id_genero
    INNER JOIN
        tb_categorias AS c ON p.id_categoria = c.id_categoria
    INNER JOIN
        tb_materiales AS ma ON p.id_material = ma.id_material
    LEFT JOIN
        tb_descuentos AS d ON p.id_descuento = d.id_descuento
    WHERE
        p.nombre_producto LIKE ? OR
        p.codigo_interno LIKE ?
    ORDER BY
        p.nombre_producto;';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function productosMarca()
    {
        $sql = 'SELECT p.nombre_producto, p.codigo_interno, p.referencia_proveedor, dp.existencias
            FROM tb_productos p
            INNER JOIN tb_marcas m ON p.id_marca = m.id_marca
            INNER JOIN tb_detalles_productos dp ON p.id_producto = dp.id_producto
            WHERE p.id_marca = ?
            ORDER BY p.nombre_producto';
        $params = array($this->id_marca);
        return Database::getRows($sql, $params);
    }



    /*
     * Método para leer el nombre de archivo de la imagen de un libro.
     */
    public function readFilename()
    {
        $sql = 'SELECT imagen FROM tb_productos WHERE id_producto = ?';
        $params = array($this->id_producto);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_productos
            SET nombre_producto = ?, codigo_interno = ?, referencia_proveedor = ?, precio = ?, id_marca = ?, id_genero = ?, id_categoria = ?, id_material = ?, id_descuento = ?, imagen = ?
            WHERE id_producto = ?';
        $params = array($this->nombre_producto, $this->codigo_interno, $this->referencia_proveedor, $this->precio, $this->id_marca, $this->id_genero, $this->id_categoria, $this->id_material, $this->id_descuento, $this->imagen_producto, $this->id_producto);
        return Database::executeRow($sql, $params);
    }


    /*
     * Método SCRUD para detalles de productos
     */

    public function createDetail()
    {
        $sql = 'INSERT INTO tb_detalles_productos(id_producto, id_talla, existencias, id_color, descripcion)
            VALUES(?, ?, ?, ?, ?)';
        $params = array($this->id_producto, $this->id_talla, $this->existencias, $this->id_color, $this->descripcion);
        return Database::executeRow($sql, $params);
    }
    /*
     * Método para eliminar un registro específico  por id.
     */

    public function deleteRow()
    {
        $sqlDeleteDetalles = 'DELETE FROM tb_detalles_productos WHERE id_producto = ?';
        $paramsDeleteDetalles = array($this->id_producto);
        Database::executeRow($sqlDeleteDetalles, $paramsDeleteDetalles);

        $sqlDeleteProducto = 'DELETE FROM tb_productos WHERE id_producto = ?';
        $paramsDeleteProducto = array($this->id_producto);
        return Database::executeRow($sqlDeleteProducto, $paramsDeleteProducto);
    }
    /*
     * Método para eliminar un detalle de productos específico  por id.
     */

    public function deleteDetail()
    {
        $sql = 'DELETE FROM tb_detalles_productos
            WHERE id_detalle_producto = ?';
        $params = array($this->id_detalle_producto);
        return Database::executeRow($sql, $params);
    }


    // Dentro de producto_data.php

    public function readDetails()
    {
        $sql = 'SELECT 
            dp.id_detalle_producto,
            t.nombre_talla AS nombre_talla,
            c.color AS nombre_color,
            dp.existencias,
            dp.descripcion
            FROM tb_detalles_productos dp
            JOIN tb_tallas t ON dp.id_talla = t.id_talla
            JOIN tb_colores c ON dp.id_color = c.id_color
            WHERE dp.id_producto = ?';
        $params = array($this->id_producto);
        return Database::getRows($sql, $params);
    }

    public function updateDetail()
    {
        $sql = 'UPDATE tb_detalles_productos dp
            SET
                dp.id_talla = ?,
                dp.id_color = ?,
                dp.existencias = ?, 
                dp.descripcion = ?
            WHERE 
                dp.id_detalle_producto = ?';

        $params = array(
            $this->id_talla,
            $this->id_color,
            $this->existencias,
            $this->descripcion,
            $this->id_detalle_producto
        );

        return Database::executeRow($sql, $params);
    }


    public function readOneDetail()
    {
        $sql = 'SELECT 
        dp.id_detalle_producto,
        dp.id_producto,
        dp.id_talla,
        dp.id_color,
        t.nombre_talla AS nombre_talla,
        c.color AS nombre_color,
        dp.existencias,
        dp.descripcion
        FROM 
        tb_detalles_productos dp
        JOIN tb_tallas t ON dp.id_talla = t.id_talla
        JOIN tb_colores c ON dp.id_color = c.id_color
        WHERE 
        dp.id_detalle_producto = ?';
        $params = array($this->id_detalle_producto);
        return Database::getRow($sql, $params);
    }

    // Método para obtener productos con descuento
    public function getProductosConDescuento()
    {
        $sql = 'SELECT 
    p.id_producto, 
    p.nombre_producto, 
    p.precio, 
    p.imagen,
    dp.id_detalle_producto,
    d.nombre_descuento, 
    d.valor 
FROM 
    tb_productos p
JOIN 
    tb_descuentos d ON p.id_descuento = d.id_descuento
JOIN 
    tb_detalles_productos dp ON p.id_producto = dp.id_producto
WHERE 
    p.id_descuento IS NOT NULL';
        return Database::getRows($sql);
    }

    public function cantidadProductosCategoria()
    {
        $sql = 'SELECT nombre_categoria, COUNT(id_producto) cantidad
                FROM tb_productos
                INNER JOIN tb_categorias USING(id_categoria)
                GROUP BY nombre_categoria ORDER BY cantidad DESC LIMIT 5';
        return Database::getRows($sql);
    }

    public function porcentajeProductosCategoria()
    {
        $sql = 'SELECT c.nombre_categoria, 
                       ROUND((COUNT(p.id_producto) * 100.0 / (SELECT COUNT(id_producto) FROM tb_productos)), 2) AS porcentaje
                FROM tb_productos p
                INNER JOIN tb_categorias c ON p.id_categoria = c.id_categoria
                GROUP BY c.nombre_categoria
                ORDER BY porcentaje DESC
                LIMIT 5';
        return Database::getRows($sql);
    }


    public function descuentosMasUtilizados()
    {
        $sql = 'SELECT d.nombre_descuento, COUNT(p.id_producto) AS cantidad
                FROM tb_descuentos d
                INNER JOIN tb_productos p ON d.id_descuento = p.id_descuento
                GROUP BY d.nombre_descuento
                ORDER BY cantidad DESC
                LIMIT 5';
        return Database::getRows($sql);
    }


    public function marcaMasComprada()
    {
        $sql = 'SELECT m.marca, COUNT(p.id_producto) AS cantidad
    FROM tb_productos p
    INNER JOIN tb_marcas m ON p.id_marca = m.id_marca
    INNER JOIN tb_detalles_reservas dr ON p.id_producto = dr.id_detalle_producto
    INNER JOIN tb_reservas r ON dr.id_reserva = r.id_reserva 
    WHERE r.estado_reserva = "Aceptado"
    GROUP BY m.marca
    ORDER BY cantidad DESC
    LIMIT 5;';
        return Database::getRows($sql);
    }

    public function productosMasVendidosPorCategoria()
    {
        $sql = 'SELECT c.nombre_categoria, COUNT(p.id_producto) AS cantidad
    FROM tb_productos p
    INNER JOIN tb_detalles_reservas dr ON p.id_producto = dr.id_detalle_producto  
    INNER JOIN tb_reservas r ON dr.id_reserva = r.id_reserva
    INNER JOIN tb_categorias c ON p.id_categoria = c.id_categoria
    WHERE r.estado_reserva = "Aceptado"
    GROUP BY c.nombre_categoria
    ORDER BY cantidad DESC
    LIMIT 5;';
        return Database::getRows($sql);
    }



    //Metodo para la grafica de distribucion de productos por genero (Automatica)
    public function cantidadProductosGenero()
    {
        $sql = 'SELECT g.nombre_genero, COUNT(p.id_producto) AS cantidad
                FROM tb_productos p
                JOIN tb_generos_zapatos g ON p.id_genero = g.id_genero
                GROUP BY g.nombre_genero
                LIMIT 5';
        return Database::getRows($sql);
    }
    //Metodo para grafica predictiva
    public function ventasUltimosSeisMeses()
    {
        $sql = "SELECT 
    CASE MONTH(r.fecha_reserva)
        WHEN 1 THEN 'Enero'
        WHEN 2 THEN 'Febrero'
        WHEN 3 THEN 'Marzo'
        WHEN 4 THEN 'Abril'
        WHEN 5 THEN 'Mayo'
        WHEN 6 THEN 'Junio'
        WHEN 7 THEN 'Julio'
        WHEN 8 THEN 'Agosto'
        WHEN 9 THEN 'Septiembre'
        WHEN 10 THEN 'Octubre'
        WHEN 11 THEN 'Noviembre'
        WHEN 12 THEN 'Diciembre'
    END AS mes,
    SUM(dr.cantidad * dr.precio_unitario) AS ventas_totales
FROM tb_reservas r
INNER JOIN tb_detalles_reservas dr ON r.id_reserva = dr.id_reserva
WHERE r.estado_reserva = 'Aceptado'
  AND r.fecha_reserva >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
  AND r.fecha_reserva <= CURDATE()
GROUP BY mes
ORDER BY MONTH(r.fecha_reserva) ASC;";
        return Database::getRows($sql);
    }

    /*
     *   Métodos para generar reportes .
     */
    public function productosCategoria()
    {
        $sql = 'SELECT p.nombre_producto, p.codigo_interno, dp.existencias, p.precio
                FROM tb_productos p
                INNER JOIN tb_categorias c ON p.id_categoria = c.id_categoria
                INNER JOIN tb_detalles_productos dp ON p.id_producto = dp.id_detalle_producto
                WHERE c.id_categoria = ?
                ORDER BY p.nombre_producto';
        $params = array($this->id_categoria);
        return Database::getRows($sql, $params);
    }
}

