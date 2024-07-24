<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de las tablas PEDIDO y DETALLE_PEDIDO.
*/
class PedidoHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id_pedido = null;
    protected $id_detalle = null;
    protected $id_reserva = null;
    protected $cliente = null;
    protected $producto = null;
    protected $cantidad = null;
    protected $precio = null;
    protected $estado = null;
    protected $idProducto = null;
    protected $idDetalleProducto = null;
    protected $idUsuario = null;

    /*
    *   ESTADOS DEL PEDIDO
    *   Pendiente (valor por defecto en la base de datos). Pedido en proceso y se puede modificar el detalle.
    *   Finalizado. Pedido terminado por el cliente y ya no es posible modificar el detalle.
    *   Entregado. Pedido enviado al cliente.
    *   Anulado. Pedido cancelado por el cliente después de ser finalizado.
    */

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */
    // Método para verificar si existe un pedido en proceso con el fin de iniciar o continuar una compra.
    public function getOrder()
    {
        $this->estado = 'Pendiente';
        $sql = 'SELECT id_reserva
            FROM tb_reservas
            WHERE estado_reserva = ? AND id_usuario = ?';
        $params = array($this->estado, $_SESSION['idUsuario']);
        if ($data = Database::getRow($sql, $params)) {
            $_SESSION['idReserva'] = $data['id_reserva']; // Asegúrate de usar 'idReserva' en lugar de 'idPedido' para consistencia.
            return true;
        } else {
            return false;
        }
    }

    // Método para iniciar un pedido en proceso.
    public function startOrder()
    {
        // Verifica si ya existe una orden para el usuario actual.
        if ($this->getOrder()) {
            return true;
        } else {
            // Crea una nueva reserva para el usuario actual.
            $sql = 'INSERT INTO tb_reservas(id_usuario, fecha_reserva, estado_reserva)
                    VALUES(?, now(), ?)';
            $params = array($_SESSION['idUsuario'], 'Pendiente');

            // Ejecuta la consulta y obtiene el ID de la nueva reserva.
            if ($_SESSION['idReserva'] = Database::getLastRow($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }

    // Método para agregar un producto al carrito de compras.
    public function createDetail()
{
    $sql = 'INSERT INTO tb_detalles_reservas (id_producto, precio_unitario, cantidad, id_reserva, id_detalle_producto)
            VALUES (?, (SELECT precio FROM tb_productos WHERE id_producto = ?), ?, ?, ?)';
    $params = array($this->producto, $this->producto, $this->cantidad, $_SESSION['idReserva'], $this->idDetalleProducto);
    return Database::executeRow($sql, $params);
}


    // Método para obtener los productos que se encuentran en el carrito de compras.
    public function readDetail()
    {
        $sql = 'SELECT 
                dr.id_detalle_reserva, 
                p.nombre_producto,
                p.imagen, 
                IFNULL(o.valor, 0) AS valor_oferta, -- Utiliza IFNULL para manejar el caso de no oferta
                dr.precio_unitario, 
                dr.cantidad, 
                r.estado_reserva
            FROM 
                tb_detalles_reservas dr
            INNER JOIN 
                tb_reservas r ON dr.id_reserva = r.id_reserva
            INNER JOIN 
                tb_productos p ON dr.id_producto = p.id_producto
            LEFT JOIN
                tb_ofertas o ON p.id_oferta = o.id_oferta -- Usa LEFT JOIN para incluir productos sin oferta
            WHERE 
                dr.id_reserva = ?';
        $params = array($_SESSION['idReserva']);
        return Database::getRows($sql, $params);
    }

    // Método para finalizar un pedido por parte del cliente.
    public function finishOrder()
    {
        $this->estado = 'Aceptado';
        $sql = 'UPDATE tb_reservas
                SET estado_reserva = ?
                WHERE id_reserva = ?';
        $params = array($this->estado, $_SESSION['idReserva']);
        return Database::executeRow($sql, $params);
    }

    public function readCompra($value)
    {
        $value = $value === '' ? '%%' : '%' . $value . '%';

        // Consulta SQL actualizada para incluir el valor de la oferta
        $sql = 'SELECT 
    dr.id_detalle_reserva, 
    p.id_producto, 
    r.fecha_registro,
    p.nombre_producto, 
    dr.precio_unitario, 
    dr.cantidad, 
    r.estado_reserva,
    u.nombre AS nombre_usuario,
    u.usuario,
    u.correo,
    u.direccion,
    p.imagen,
    o.valor AS valor_oferta
FROM 
    tb_detalles_reservas dr
INNER JOIN 
    tb_reservas r ON dr.id_reserva = r.id_reserva
INNER JOIN 
    tb_productos p ON dr.id_producto = p.id_producto
INNER JOIN
    tb_usuarios u ON r.id_usuario = u.id_usuario
LEFT JOIN
    tb_ofertas o ON p.id_oferta = o.id_oferta
WHERE 
    r.estado_reserva = "Aceptado" AND
    r.id_reserva = ? AND
    p.nombre_producto LIKE ?';
$params = array($_SESSION['idReserva'], $value);
        return Database::getRows($sql, $params);
    }




    public function getExistencias()
    {
        $sql = 'SELECT existencias FROM tb_productos WHERE id_producto = ?';
        $params = array($this->idProducto);
        if ($data = Database::getRow($sql, $params)) {
            return $data['existencias'];
        }
    }
    public function readHistorials($value)
    {
        $value = $value === '' ? '%%' : '%' . $value . '%';

        // Consulta SQL actualizada para incluir el valor de la oferta
        $sql = 'SELECT 
    dr.id_detalle_reserva, 
    p.id_producto, 
    r.fecha_registro,
    p.nombre_producto, 
    dr.precio_unitario, 
    dr.cantidad, 
    r.estado_reserva,
    u.nombre AS nombre_usuario,
    u.usuario,
    u.correo,
    u.direccion,
    p.imagen,
    o.valor AS valor_oferta
FROM 
    tb_detalles_reservas dr
INNER JOIN 
    tb_reservas r ON dr.id_reserva = r.id_reserva
INNER JOIN 
    tb_productos p ON dr.id_producto = p.id_producto
INNER JOIN
    tb_usuarios u ON r.id_usuario = u.id_usuario
LEFT JOIN
    tb_ofertas o ON p.id_oferta = o.id_oferta
WHERE r.estado_reserva = "Aceptado" AND
    u.id_usuario = ? AND nombre_producto LIKE ?';

        $params = array($_SESSION['idUsuario'], $value);
        return Database::getRows($sql, $params);
    }


    public function readFactura()
    {
        // Consulta para obtener todos los productos reservados por el usuario
        $sql = '        SELECT 
    dr.id_detalle_reserva, 
    p.id_producto, 
    r.fecha_registro,
    p.nombre_producto, 
    dr.precio_unitario, 
    dr.cantidad, 
    r.estado_reserva,
    u.nombre AS nombre_usuario,
    p.imagen,
    IFNULL(o.valor, 0) AS valor_oferta,
    IFNULL(o.descripcion, "Sin oferta") AS descripcion_oferta
FROM 
    tb_detalles_reservas dr
INNER JOIN 
    tb_reservas r ON dr.id_reserva = r.id_reserva
INNER JOIN 
    tb_productos p ON dr.id_producto = p.id_producto
INNER JOIN
    tb_usuarios u ON r.id_usuario = u.id_usuario
LEFT JOIN
    tb_ofertas o ON p.id_oferta = o.id_oferta
WHERE 
    r.estado_reserva = "Aceptado" AND
    u.id_usuario = ?';
        $params = array($_SESSION['idUsuario']);
        return Database::getRows($sql, $params);
    }





    public function readOne()
    {
        $sql = 'SELECT
    r.id_reserva,
    r.id_usuario,
    r.estado_reserva,
    r.fecha_registro,
    u.usuario,
    d.id_detalle_reserva,
    d.cantidad,
    d.id_producto,
    d.precio_unitario,
    p.nombre_producto
FROM
    tb_detalles_reservas d
INNER JOIN
    tb_reservas r ON d.id_reserva = r.id_reserva
INNER JOIN
    tb_usuarios u ON r.id_usuario = u.id_usuario
INNER JOIN
    tb_productos p ON d.id_producto = p.id_producto
WHERE
    d.id_detalle_reserva = ?';
        $params = array($this->id_detalle);
        return Database::getRow($sql, $params);
    }

    // Método para actualizar la cantidad de un producto agregado al carrito de compras.
    public function updateDetail()
    {
        $sql = 'UPDATE tb_detalles_reservas
                SET cantidad = ?
                WHERE id_detalle_reserva = ? AND id_reserva = ?';
        $params = array($this->cantidad, $this->id_detalle, $_SESSION['idReserva']);
        return Database::executeRow($sql, $params);
    }


    // Método para eliminar un producto que se encuentra en el carrito de compras.
    public function deleteDetail()
    {
        $sql = 'DELETE FROM tb_detalles_reservas
                WHERE id_detalle_reserva = ? AND id_reserva = ?';
        $params = array($this->id_detalle, $_SESSION['idReserva']);
        return Database::executeRow($sql, $params);
    }
}
