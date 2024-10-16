<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla CATEGORIA.
 */
class CategoriaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;

    protected $imagen = null;

    // Constante para establecer la ruta de las imágenes.
    const RUTA_IMAGEN = '../../images/categorias/';

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    //Busca categorías en la base de datos por nombre.
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_categoria, nombre_categoria, imagen
                FROM tb_categorias
                WHERE nombre_categoria LIKE ?
                ORDER BY nombre_categoria';
        $params = array($value);
        return Database::getRows($sql, $params);
    }
    //Crea una nueva categoría en la base de datos.
    public function createRow()
{
    // Sanitizar los valores antes de realizar la inserción
    $this->nombre = htmlspecialchars(trim($this->nombre), ENT_QUOTES, 'UTF-8');
    $this->imagen = htmlspecialchars(trim($this->imagen), ENT_QUOTES, 'UTF-8');

    // Sentencia SQL para insertar en la base de datos
    $sql = 'INSERT INTO tb_categorias(nombre_categoria, imagen)
            VALUES(?, ?)';

    // Los parámetros sanitizados que se usarán en la sentencia
    $params = array($this->nombre, $this->imagen);

    // Ejecutar la sentencia preparada con los parámetros
    return Database::executeRow($sql, $params);
}

    //Lee todas las categorías de la base de datos.
    public function readAll()
    {
        $sql = 'SELECT id_categoria, nombre_categoria, imagen
                FROM tb_categorias
                ORDER BY nombre_categoria';
        return Database::getRows($sql);
    }
    //Lee los detalles de una categoría específica por ID.
    public function readOne()
    {
        $sql = 'SELECT id_categoria, nombre_categoria, imagen
                FROM tb_categorias
                WHERE id_categoria = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }
    //Lee el nombre del archivo de imagen de una categoría específica por ID.
    public function readFilename()
    {
        $sql = 'SELECT imagen
                FROM tb_categorias
                WHERE id_categoria = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }
    
    //Actualiza la información de una categoría específica
    public function updateRow()
    {
        $sql = 'UPDATE tb_categorias
                SET nombre_categoria = ?, imagen = ?
                WHERE id_categoria = ?';
        $params = array( $this->nombre, $this->imagen, $this->id);
        return Database::executeRow($sql, $params);
    }

    //Elimina una categoría específica por ID
    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_categorias
                WHERE id_categoria = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function getNombreCategoria() {
        $sql = 'SELECT nombre_categoria FROM tb_categorias WHERE id_categoria = ?';
        $params = array($this->id);

        // Ejecutar la consulta
        if ($data = Database::getRow($sql, $params)) {
            $this->nombre = $data['nombre_categoria'];
            return $this->nombre;
        } else {
            return null; // Si no se encuentra la categoría
        }
    }

    // Lee todas las categorías que tienen productos asociados.
    public function readAllCategorias()
    {
        $sql = 'SELECT c.id_categoria, c.nombre_categoria, c.imagen
            FROM tb_categorias c
            JOIN tb_productos p ON c.id_categoria = p.id_categoria
            JOIN tb_detalles_productos dp ON p.id_producto = dp.id_producto
            GROUP BY c.id_categoria, c.nombre_categoria, c.imagen
            HAVING COUNT(dp.id_detalle_producto) > 0
            ORDER BY c.nombre_categoria';
        return Database::getRows($sql);
    }
    // Lee los productos más vendidos de una categoría específica.
    public function readTopProductos()
    {
        $sql = 'SELECT p.nombre_producto, SUM(dr.cantidad) AS total_vendido
            FROM tb_detalles_reservas dr
            INNER JOIN tb_detalles_productos dp ON dr.id_detalle_producto = dp.id_detalle_producto
            INNER JOIN tb_productos p ON dp.id_producto = p.id_producto
            WHERE p.id_categoria = ?
            GROUP BY p.nombre_producto
            ORDER BY total_vendido DESC
            LIMIT 5';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }

}
