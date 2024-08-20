<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class marcaHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_marca, marca
                FROM tb_marcas
                WHERE marca LIKE ?
                ORDER BY marca';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO tb_marcas(marca)
                VALUES(?)';
        $params = array($this->nombre);
        return Database::executeRow($sql, $params);
    }

    //Llamar los datos de la base de datos 
    public function readAll()
    {
        $sql = 'SELECT id_marca, marca
            FROM tb_marcas
            ORDER BY marca ASC';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_marca, marca
                FROM tb_marcas
                WHERE id_marca = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE tb_marcas
                SET marca = ?
                WHERE id_marca = ?';
        $params = array($this->nombre, $this->id);
        return Database::executeRow($sql, $params);
    }


    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_marcas
                WHERE id_marca = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
    public function ventasPorMarcasFecha($fechaInicio, $fechaFin) {
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
    r.fecha_reserva ASC
    LIMIT 5;';
        $params = array($fechaInicio, $fechaFin);
        return Database::getRows($sql, $params);
    }
     
}
