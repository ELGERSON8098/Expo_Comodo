<?php
// Se incluye la clase del modelo.
require_once('../../models/data/pedido_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedido = new PedidoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'error' => null, 'exception' => null, 'dataset' => null);
    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    if (isset($_SESSION['idUsuario'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
                // Acción para agregar un producto al carrito de compras.
                case 'createDetail':
                    $_POST = Validator::validateForm($_POST);
                    
                    // Iniciar el pedido
                    if (!$pedido->startOrder()) {
                        $result['error'] = 'Ocurrió un problema al iniciar el pedido';
                    } elseif (
                        !$pedido->setProducto($_POST['idProducto']) or
                        !$pedido->setCantidad($_POST['cantidadProducto'])
                    ) {
                        $result['error'] = $pedido->getDataError();
                    } else {
                        // Validar existencias antes de agregar el producto
                        if (!$pedido->validateStock($_POST['idProducto'], $_POST['cantidadProducto'])) {
                            $result['error'] = 'La cantidad solicitada excede las existencias disponibles.';
                        } elseif ($pedido->createDetail()) {
                            $result['status'] = 1;
                            $result['message'] = 'Producto agregado correctamente a la reserva ' . $_SESSION['idReserva'];
                        } else {
                            $result['error'] = 'Ocurrió un problema al agregar el producto';
                        }
                    }
                    break;
                // Acción para obtener los productos agregados en el carrito de compras.
            case 'readDetail':
                if (!$pedido->getOrder()) {
                    $result['error'] = 'No ha agregado productos al carrito';
                } elseif ($result['dataset'] = $pedido->readDetail()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No existen productos en el carrito';
                }
                break;
            case 'readHistorials':
                if ($result['dataset'] = $pedido->readHistorials()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No existen productos para mostrar';
                }
                break;
            case 'getExistencias':
                // Verificar si el ID del producto está presente en la solicitud.
                if (isset($_POST['idProducto'])) {
                    $idProducto = $_POST['idProducto'];
                    // Obtener las existencias del producto.
                    $existencias = $pedido->getExistencias($idProducto);
                    if ($existencias !== false) {
                        $result['status'] = 1;
                        $result['data'] = ['existencias' => $existencias];
                    } else {
                        $result['error'] = 'No se pudo obtener las existencias del producto. ID: ' . $idProducto;
                    }
                } else {
                    $result['error'] = 'ID del producto no especificado.';
                }
                break;
                // Acción para actualizar la cantidad de un producto en el carrito de compras.
                case 'updateDetail':
                    $_POST = Validator::validateForm($_POST);
                    
                    if (
                        !$pedido->setIdDetalle($_POST['idDetalle']) or
                        !$pedido->setCantidad($_POST['cantidadProducto'])
                    ) {
                        $result['error'] = $pedido->getDataError();
                    } else {
                        // Obtener existencias y cantidad actual del producto
                        $existencias = $pedido->getExistencias($_POST['idDetalle']); // Método que obtendrá las existencias del producto
                        $cantidadActual = $pedido->getCantidadActual($_POST['idDetalle']); // Método que obtendrá la cantidad actual del producto
                        
                        // Calcular la nueva cantidad
                        $nuevaCantidad = $_POST['cantidadProducto'];
                        
                        // Validar si la nueva cantidad excede las existencias disponibles
                        if ($nuevaCantidad > $existencias) {
                            $result['error'] = 'La cantidad solicitada excede las existencias disponibles.';
                        } elseif ($nuevaCantidad === $existencias) {
                            $result['error'] = 'La cantidad solicitada ya está en el límite de existencias.';
                        } elseif ($pedido->updateDetail()) {
                            $result['status'] = 1;
                            $result['message'] = 'Cantidad modificada correctamente';
                        } else {
                            $result['error'] = 'Ocurrió un problema al modificar la cantidad';
                        }
                    }
                    break;
                // Acción para remover un producto del carrito de compras.
            case 'deleteDetail':
                if (!$pedido->setIdDetalle($_POST['idDetalle'])) {
                    $result['error'] = $pedido->getDataError();
                } elseif ($pedido->deleteDetail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto removido correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al remover el producto';
                }
                break;
                // En tu switch case, asegúrate de pasar un valor adecuado a readCompra

                // Acción para finalizar el carrito de compras.
            case 'finishOrder':
                if ($pedido->finishOrder()) {
                    $result['status'] = 1;
                    $result['message'] = 'Pedido finalizado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al finalizar el pedido, no puede comprar mas productos de los que ya estan en stock';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando un cliente no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'createDetail':
                $result['error'] = 'Debe iniciar sesión para agregar el producto al carrito';
                break;
            default:
                $result['error'] = 'Acción no disponible fuera de la sesión';
        }
    }
    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
