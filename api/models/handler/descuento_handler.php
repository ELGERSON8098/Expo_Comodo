<?php
// Se incluye la clase para trabajar con la base de datos.
require_once ('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class descuentoHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $nombre = null;
    protected $descripcion = null;
    protected $valor = null;

    /*
     *  Métodos para gestionar la cuenta del administrador.
     */

     //Busca descuentos en la base de datos por nombre.
    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_descuento, nombre_descuento, descripcion, valor
                FROM tb_descuentos
                WHERE nombre_descuento LIKE ? 
                ORDER BY nombre_descuento';
        $params = array($value);
        return Database::getRows($sql, $params);
    }

    //Crea un nuevo descuento en la base de datos
    public function createRow()
    {
        $sql = 'INSERT INTO tb_descuentos(nombre_descuento, descripcion, valor)
                 VALUES(?, ?, ?)';
        $params = array($this->nombre, $this->descripcion, $this->valor);
        return Database::executeRow($sql, $params);
    }

    //Llamar los datos de la base de datos 
    public function readAll()
    {
        $sql = 'SELECT id_descuento, descripcion, nombre_descuento, valor
            FROM tb_descuentos
            ORDER BY nombre_descuento ASC;
    ';
        return Database::getRows($sql);
    }

    //Lee los detalles de un descuento específico por ID.
    public function readOne()
    {
        $sql = 'SELECT id_descuento, nombre_descuento, descripcion, valor
                FROM tb_descuentos
                WHERE id_descuento = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }
    //Actualiza la información de un descuento específico
    public function updateRow()
    {
        $sql = 'UPDATE tb_descuentos
                SET nombre_descuento = ?, descripcion = ?, valor = ?
                WHERE id_descuento = ?';
        $params = array($this->nombre, $this->descripcion, $this->valor, $this->id);
        return Database::executeRow($sql, $params);
    }

    //Elimina un descuento específico por ID.
    public function deleteRow()
    {
        $sql = 'DELETE FROM tb_descuentos
                WHERE id_descuento = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
    //Obtiene descuentos aplicables a productos dentro de un rango de precios.
    public function descuentosPorRangoPrecio($precioMin, $precioMax) {
        $sql = 'SELECT 
                    p.nombre_producto, 
                    p.precio, 
                    d.valor 
                FROM 
                    tb_productos p
                INNER JOIN 
                    tb_descuentos d ON p.id_descuento = d.id_descuento
                WHERE 
                    p.precio BETWEEN ? AND ?';
        
        $params = array($precioMin, $precioMax);
        return Database::getRows($sql, $params);
    }    
    
}
