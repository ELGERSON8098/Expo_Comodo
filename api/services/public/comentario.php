<?php
// Se incluye la clase del modelo.
require_once('../../models/data/valoraciones_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $pedido = new ValoracionesData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'error' => null, 'exception' => null, 'dataset' => null);
    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    //echo 'que '+$_SESSION['idUsuario'];
    if (isset($_SESSION['idUsuario'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
            // Acción para agregar un producto al carrito de compras.
            // Acción para obtener los productos agregados en el carrito de compras.
            case 'readAllByProducto':
                if (
                    !$pedido->setIdProducto($_POST['idProducto1']) 
                ) {
                    $result['error'] = $pedido->getDataError();
                } elseif ($result['dataset'] = $pedido->readAllByProducto()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No existen categorías para mostrar';
                }
                break;
            // Acción para actualizar la cantidad de un producto en el carrito de compras.
            case 'createComentario':
                $_POST = Validator::validateForm($_POST);

                if (
                    !$pedido->setComentarioValoracion($_POST['floatingTextarea2']) or
                    !$pedido->setCalificaionValoracion($_POST['starValue'])  or
                    !$pedido->setIdProducto($_POST['idProducto1'])
                ) {
                    $result['error'] = $pedido->getDataError();
                } elseif ($pedido->createComentario()) {
                    $result['status'] = 1;
                    $result['message'] = 'Comentario creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la cantidad';
                }
                break;
            // Acción para remover un producto del carrito de compras.
            
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
}