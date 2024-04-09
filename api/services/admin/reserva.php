<?php
// Se incluye la clase del modelo de reservas.
require_once('../../models/data/reserva_data.php');
 
// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
 
    // Se instancia la clase correspondiente.
    $reservaHandler = new ReservaHandler;
 
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
 
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (!$reservaHandler->setIdUsuario($_POST['id_usuario']) || !$reservaHandler->setFechaReserva($_POST['fecha_reserva']) || !$reservaHandler->setIdDireccion($_POST['id_direccion']) || !$reservaHandler->setDescripcionDireccion($_POST['descripcion_direccion'])) {
                    $result['error'] = $reservaHandler->getDataError();
                } elseif ($reservaHandler->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Reserva creada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear la reserva';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $reservaHandler->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' reservas';
                } else {
                    $result['error'] = 'No existen reservas registradas';
                }
                break;
            case 'readOne':
                if (!$reservaHandler->setIdReserva($_POST['id_reserva'])) {
                    $result['error'] = $reservaHandler->getDataError();
                } elseif ($result['dataset'] = $reservaHandler->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Reserva inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (!$reservaHandler->setIdReserva($_POST['id_reserva']) || !$reservaHandler->setIdUsuario($_POST['id_usuario']) || !$reservaHandler->setFechaReserva($_POST['fecha_reserva']) || !$reservaHandler->setIdDireccion($_POST['id_direccion']) || !$reservaHandler->setDescripcionDireccion($_POST['descripcion_direccion'])) {
                    $result['error'] = $reservaHandler->getDataError();
                } elseif ($reservaHandler->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Reserva modificada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la reserva';
                }
                break;
            case 'deleteRow':
                if (!$reservaHandler->setIdReserva($_POST['id_reserva'])) {
                    $result['error'] = $reservaHandler->getDataError();
                } elseif ($reservaHandler->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Reserva eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar la reserva';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
 
        // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
        $result['exception'] = Database::getException();
 
        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('Content-type: application/json; charset=utf-8');
 
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}
?>
