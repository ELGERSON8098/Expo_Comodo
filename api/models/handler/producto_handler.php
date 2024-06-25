<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
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
    WHERE
        p.id_producto = ?';
        $params = array($this->id_producto);
        return Database::getRow($sql, $params);
    }
    /*
     * Método para buscar registros de los productos.
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
        $sql = 'DELETE FROM tb_productos
            WHERE id_producto = ?';
        $params = array($this->id_producto);
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
}
