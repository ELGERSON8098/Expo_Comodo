<?php
// Se incluye la clase del modelo.
require_once('../../models/data/descuento_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $descuento = new descuentoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $descuento->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$descuento->setNombre($_POST['nombreDescuento']) or
                    !$descuento->setDesc($_POST['nombreDesc']) or
                    !$descuento->setvalor($_POST['ValorM'])
                ) {
                    $result['error'] = $descuento->getDataError();
                } elseif ($descuento->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Descuento creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el Descuento';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $descuento->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen Descuentos registrados';
                }
                break;
            case 'readOne':
                if (!$descuento->setId($_POST['idDescuento'])) {
                    $result['error'] = $descuento->getDataError();
                } elseif ($result['dataset'] = $descuento->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Descuento inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$descuento->setid($_POST['idDescuento']) or
                    !$descuento->setNombre($_POST['nombreDescuento']) or
                    !$descuento->setDesc($_POST['nombreDesc']) or
                    !$descuento->setvalor($_POST['ValorM'])
                ) {
                    $result['error'] = $descuento->getDataError();
                } elseif ($descuento->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Descuento modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el descuento';
                }
                break;
            case 'deleteRow':
                if (
                    !$descuento->setid($_POST['idDescuento'])
                ) {
                    $result['error'] = $descuento->getDataError();
                } elseif ($descuento->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Descuento eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el Descuento';
                }
                break;
            
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
