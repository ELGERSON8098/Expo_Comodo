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
    protected $cantidadSolicitada = null;
    protected $cantidad = null;
    protected $precio = null;
    protected $estado = null;
    protected $idProducto = null;
    protected $idDetalleProducto = null;
    protected $idUsuario = null;
    protected $condicion = null;

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


    public function createDetail()
    {
        // Validar existencias
        if (!$this->validateStock($this->producto, $this->cantidad)) {
            return json_encode(['status' => false, 'message' => 'La cantidad solicitada excede las existencias disponibles.']);
        }

        // Si la validación es exitosa, proceder a insertar
        $sql = 'INSERT INTO tb_detalles_reservas (id_detalle_producto, precio_unitario, cantidad, id_reserva)
            VALUES (?, (SELECT precio FROM tb_productos INNER JOIN tb_detalles_productos USING(id_producto) WHERE id_detalle_producto = ?), ?, ?)';

        $params = array($this->producto, $this->producto, $this->cantidad, $_SESSION['idReserva']);

        // Ejecutar la inserción
        if (Database::executeRow($sql, $params)) {
            return json_encode(['status' => true, 'message' => 'Producto agregado al carrito con éxito.']);
        } else {
            return json_encode(['status' => false, 'message' => 'No se pudo agregar el producto al carrito.']);
        }
    }

    public function validateStock($idDetalleProducto, $cantidadSolicitada)
    {
        // Consulta para obtener las existencias del producto
        $sql = 'SELECT dp.existencias 
            FROM tb_detalles_productos dp 
            WHERE dp.id_detalle_producto = ?';

        $params = array($idDetalleProducto);
        $result = Database::getRow($sql, $params);

        // Verifica si se las reservas totales no han pasado las existencias
        if ($result && $result['existencias'] >= $cantidadSolicitada) {
        $sql2 = 'SELECT SUM(cantidad) AS reservas 
        FROM tb_detalles_reservas WHERE id_detalle_producto = ?';
        $params2 = array($idDetalleProducto);
        $result2 = Database::getRow($sql2, $params2);
            $suma = $cantidadSolicitada + $result2['reservas'];
            if($result && $result['existencias'] >= $suma){
                return true; // La compra es válida
            }else{
                $this->condicion = 'reservas';
                return false;
            }
        } else {
                $this->condicion = 'existencias';
                return false;
        }
    }


    // Método para obtener los productos que se encuentran en el carrito de compras.
    public function readDetail()
    {
        $sql = 'SELECT 
                dr.id_detalle_reserva, 
                p.nombre_producto,
                p.id_producto,
                p.imagen, 
                IFNULL(o.valor, 0) AS valor_oferta, -- Utiliza IFNULL para manejar el caso de no oferta
                dr.precio_unitario, 
                dr.cantidad, 
                r.estado_reserva,
                dp.existencias -- Agrega el campo de existencias a la consulta
                FROM 
                tb_detalles_reservas dr
                INNER JOIN 
                tb_reservas r ON dr.id_reserva = r.id_reserva
                INNER JOIN tb_detalles_productos dp 
                USING(id_detalle_producto)
                INNER JOIN 
                tb_productos p ON dp.id_producto = p.id_producto
                LEFT JOIN
                tb_descuentos o ON p.id_descuento = o.id_descuento -- Usa LEFT JOIN para incluir productos sin oferta
                WHERE 
                dr.id_reserva = ?
                AND dp.existencias >= dr.cantidad -- Agrega la validación de existencias
                ';
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

    public function getExistencias($idDetalle)
    {
        $sql = 'SELECT dp.existencias 
            FROM tb_detalles_productos dp 
            INNER JOIN tb_detalles_reservas dr ON dp.id_detalle_producto = dr.id_detalle_producto 
            WHERE dr.id_detalle_reserva = ?';

        $params = array($idDetalle);
        $result = Database::getRow($sql, $params);

        return $result ? $result['existencias'] : 0; // Retorna las existencias o 0 si no se encuentra
    }


    public function readHistorials()
    {
        // Consulta SQL actualizada para incluir el valor de la oferta
        $sql = 'SELECT 
        dr.id_detalle_reserva, 
        dp.id_detalle_producto, 
        p.id_producto,
        r.fecha_reserva,
        p.nombre_producto, 
        dr.precio_unitario, 
        dr.cantidad, 
        r.estado_reserva,
        u.nombre AS nombre_usuario,
        u.usuario,
        u.correo,
        u.direccion_cliente AS direccion,  
        p.imagen,
        o.valor AS valor_oferta,
        (dr.precio_unitario * dr.cantidad) AS subtotal  -- Añadida una coma antes de esta línea
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
            tb_descuentos o ON p.id_descuento = o.id_descuento
        WHERE 
            r.estado_reserva = "Aceptado" AND
            u.id_usuario = ?';
        $params = array($_SESSION['idUsuario']);
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
    // Método para leer un detalle de pedido específico.
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

    // Método para obtener la cantidad actual de un detalle específico.
    public function getCantidadActual($idDetalle)
    {
        $sql = 'SELECT cantidad 
            FROM tb_detalles_reservas
            WHERE id_detalle_reserva = ?';

        $params = array($idDetalle);
        $result = Database::getRow($sql, $params);

        return $result ? $result['cantidad'] : 0; // Retorna la cantidad actual o 0 si no se encuentra
    }

    // Método para actualizar la cantidad de un detalle de pedido.
    public function updateDetail()
    {
        // Primero, obtenemos las existencias actuales del producto
        $sql = 'SELECT dp.existencias, dr.cantidad
            FROM tb_detalles_productos dp 
            INNER JOIN tb_detalles_reservas dr ON dp.id_detalle_producto = dr.id_detalle_producto
            WHERE dr.id_detalle_reserva = ? AND dr.id_reserva = ?'
            ;

        $params = array($this->id_detalle, $_SESSION['idReserva']);
        $result = Database::getRow($sql, $params);

        // Verificamos si se obtuvo un resultado
        if ($result) {
            $existenciasDisponibles = $result['existencias'];
            $cantidadActual = $result['cantidad'];

            // Calculamos la nueva cantidad solicitada
            $nuevaCantidad = $this->cantidad;

            // Verificamos si la cantidad solicitada excede las existencias disponibles
            if ($nuevaCantidad > $existenciasDisponibles) {
                return json_encode(['status' => false, 'message' => 'La cantidad solicitada excede las existencias disponibles.']);
            }

            // Si la validación es exitosa, proceder a actualizar
            $sql = 'UPDATE tb_detalles_reservas
                SET cantidad = ?
                WHERE id_detalle_reserva = ? AND id_reserva = ?';

            $params = array($nuevaCantidad, $this->id_detalle, $_SESSION['idReserva']);

            if (Database::executeRow($sql, $params)) {
                return json_encode(['status' => true, 'message' => 'Cantidad actualizada con éxito.']);
            } else {
                return json_encode(['status' => false, 'message' => 'Error: No se pudo actualizar la cantidad.']);
            }
        } else {
            return json_encode(['status' => false, 'message' => 'Error: No se encontraron existencias para el producto.']);
        }
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
