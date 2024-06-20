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
    d.nombre_descuento AS nombre_descuento,
    d.valor AS porcentaje_descuento
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
    INNER JOIN
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
        d.id_descuento,
        d.nombre_descuento AS nombre_descuento,
        d.valor AS porcentaje_descuento
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
    INNER JOIN
        tb_descuentos AS d ON p.id_descuento = d.id_descuento
    WHERE
        p.id_producto = ?';
        $params = array($this->id_producto);
        return Database::getRow($sql, $params);
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

}
