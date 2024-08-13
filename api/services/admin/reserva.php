<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/reserva_data.php');
//fillSelect(RESERVA_API, 'getEstados', 'estadoPedido', ROW.estado);
// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $reserva = new reservaData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null, 'reserva' => 0, 'detalle' => 0);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            // Caso para leer un detalle específico
            case 'readOneDetail':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);
                // Verificar si se puede establecer el ID del detalle del reserva
                if (!$reserva->setIdDetalle($_POST['idDetalleReserva'])) {
                    // Si hay un error al establecer el ID del detalle, se asigna el mensaje de error
                    $result['error'] = $reserva->getDataError();
                }
                // Intentar leer el detalle específico
                elseif ($result['dataset'] = $reserva->readOneDetail()) {
                    // Si se encuentra el detalle, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Detalle encontrado';
                } else {
                    // Si no se encuentra el detalle, se asigna el mensaje de error
                    $result['error'] = 'Detalle inexistente';
                }
                break;
            case 'readDetalles2':
                if (!$reserva->setIdDetalle($_POST['idDetalleReserva'])) {
                    $result['error'] = $reserva->getDataError();
                } elseif ($result['dataset'] = $reserva->readDetalles2()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Reserva inexistente';
                }
                break;

            case 'readOneDetailForForm':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);

                // Verificar si se puede establecer el ID del detalle del reserva
                if (!$reserva->setIdDetalle($_POST['idDetalleReserva'])) {
                    // Si hay un error al establecer el ID del detalle, se asigna el mensaje de error
                    $result['error'] = $reserva->getDataError();
                }
                // Intentar leer los detalles específicos para llenar el formulario
                elseif ($result['dataset'] = $reserva->readOneDetailForForm()) {
                    // Si se encuentran los detalles, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Detalles encontrados para formulario';
                } else {
                    // Si no se encuentran detalles, se asigna el mensaje de error
                    $result['error'] = 'Detalles no encontrados para formulario';
                }
                break;

            // Caso para buscar filas
            case 'searchRows':
                // Validar el término de búsqueda
                if (!Validator::validateSearch($_POST['search'])) {
                    // Si la validación falla, se asigna el mensaje de error
                    $result['error'] = Validator::getSearchError();
                }
                // Intentar buscar filas que coincidan con el término de búsqueda
                elseif ($result['dataset'] = $reserva->searchRows()) {
                    // Si se encuentran coincidencias, se asigna el estado y el mensaje de éxito con el número de coincidencias
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    // Si no se encuentran coincidencias, se asigna el mensaje de error
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);
                // Verificar si se pueden establecer todos los datos del reserva
                if (
                    !$reserva->setIdReserva($_POST['idReserva']) or
                    !$reserva->setEstado($_POST['estadoPedido'])
                ) {
                    // Si hay un error al establecer alguno de los datos, se asigna el mensaje de error
                    $result['error'] = $reserva->getDataError();
                }
                // Intentar crear la nueva fila en la base de datos
                elseif ($reserva->createRow()) {
                    // Si la creación es exitosa, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Reserva creada correctamente';
                } else {
                    // Si hay un problema al crear el reserva, se asigna el mensaje de error
                    $result['error'] = 'Ocurrió un problema al crear la reserva';
                }
                break;
            // Caso para leer todos los registros
            case 'readAll':
                // Intentar leer todos los registros de reservas
                if ($result['dataset'] = $reserva->readAll()) {
                    // Si la lectura es exitosa, se asigna el estado y el mensaje con la cantidad de registros encontrados
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    // Si no se encuentran registros, se asigna el mensaje de error
                    $result['error'] = 'No existen reservas registrados';
                }
                break;
            // Caso para leer un registro específico
            case 'readOne':
                // Verificar si se puede establecer el ID del reserva
                if (!$reserva->setIdReserva($_POST['idReserva'])) {
                    // Si hay un error al establecer el ID del reserva, se asigna el mensaje de error
                    $result['error'] = $reserva->getDataError();
                } elseif ($result['dataset'] = $reserva->readOne()) {
                    // Si la lectura es exitosa, se asigna el estado
                    $result['status'] = 1;
                } else {
                    // Si no se encuentra el reserva, se asigna el mensaje de error
                    $result['error'] = 'Reserva inexistente';
                }
                break;
            // Caso para actualizar una fila existente
            case 'updateRow':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);

                // Verificar si se pueden establecer todos los datos del reserva para actualizar
                if (
                    !$reserva->setIdReserva($_POST['idReserva']) or
                    !$reserva->setEstado($_POST['estadoPedido'])
                ) {
                    // Si hay un error al establecer alguno de los datos, se asigna el mensaje de error
                    $result['error'] = $reserva->getDataError();
                }
                // Intentar actualizar la fila en la base de datos
                elseif ($reserva->updateRow()) {
                    // Si la actualización es exitosa, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Reserva modificada correctamente';
                } else {
                    // Si hay un problema al modificar el reserva, se asigna el mensaje de error
                    $result['error'] = 'Ocurrió un problema al modificar la reserva';
                }
                break;
            case 'createDetail':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);
                // Verificar si se pueden establecer todos los datos del reserva
                if (
                    !$reserva->setIdReserva($_POST['idReservaDetalle']) or
                    !$reserva->setEstado($_POST['estadoPedido'])
                ) {
                    // Si hay un error al establecer alguno de los datos, se asigna el mensaje de error
                    $result['error'] = $reserva->getDataError();
                }
                // Intentar crear la nueva fila en la base de datos
                elseif ($reserva->createRow()) {
                    // Si la creación es exitosa, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Reserva creada correctamente';
                } else {
                    // Si hay un problema al crear el reserva, se asigna el mensaje de error
                    $result['error'] = 'Ocurrió un problema al crear la reserva';
                }
                break;
            case 'updateDetail':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);

                // Verificar si se pueden establecer todos los datos del detalle del reserva para actualizarlo
                if (
                    !$reserva->setIdDetalle($_POST['idDetalle']) or
                    !$reserva->setEstado($_POST['estadoPedido'])
                ) {
                    // Si hay un error al establecer alguno de los datos, se asigna el mensaje de error
                    $result['error'] = $reserva->getDataError();
                }
                // Intentar actualizar el detalle del reserva en la base de datos
                elseif ($reserva->updateDetail()) {
                    // Si la actualización es exitosa, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Detalle de reserva actualizado correctamente';
                } else {
                    // Si hay un problema al actualizar el detalle del reserva, se asigna el mensaje de error
                    $result['error'] = 'Ocurrió un problema al actualizar el detalle';
                }
                break;
            case 'deleteDetail': // Acción para eliminar una fila por ID.
                // Verificar y establecer el ID del género a eliminar.
                if (!$reserva->setIdDetalle($_POST['idReservaDetalle'])) {
                    $result['error'] = $reserva->getDataError(); // Mensaje de error si el ID es inválido.
                } elseif ($reserva->deleteDetail()) { // Intentar eliminar la fila.
                    $result['status'] = 1; // Indicar que la operación fue exitosa.
                    $result['message'] = 'Detalle de reserva eliminado correctamente'; // Mensaje de éxito.
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el detalle'; // Mensaje de error si ocurre un problema.
                }
                break;
            // Caso para leer los detalles de un reserva específico
            case 'readDetails':
                // Verificar si se puede establecer el ID del reserva
                if (!$reserva->setIdReserva($_POST['idReserva'])) {
                    // Si hay un error al establecer el ID del reserva, se asigna el mensaje de error
                    $result['error'] = $reserva->getDataError();
                }
                // Intentar leer los detalles del reserva
                elseif ($result['dataset'] = $reserva->readDetails()) {
                    // Si se encuentran detalles, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Detalles encontrados';
                } else {
                    // Si no se encuentran detalles para el reserva, se asigna el mensaje de error
                    $result['error'] = 'No hay detalles para esta reserva';
                }
                break;
            case 'getEstados':
                if ($result['dataset'] = $reserva->getEstados()) {
                    $result['status'] = 1; // Indicar que la operación fue exitosa.
                } else {
                    $result['error'] = 'No exiten estados disponibles'; // Mensaje si no se encuentran autores.
                }
                break;
            case 'cantidadReservasEstado':
                if ($result['dataset'] = $reserva->cantidadReservasEstado()) {
                    $result['status'] = 1;
                    $result['message'] = 'Datos obtenidos correctamente';
                } else {
                    $result['error'] = 'No se pudieron obtener los datos';
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
        print (json_encode($result));
    } else {
        print (json_encode('Acceso denegado'));
    }
} else {
    print (json_encode('Recurso no disponible'));
}