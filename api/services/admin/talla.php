<?php
// Se incluye la clase del modelo.
require_once('../../models/data/talla_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $talla = new tallaData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $talla->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;

                case 'createRow':
                    $_POST = Validator::validateForm($_POST);
                    if (!$talla->setNombre($_POST['nombreTalla'])) {
                        $result['error'] = $talla->getDataError();
                    } elseif ($talla->createRow()) {
                        $result['status'] = 1;
                        $result['message'] = 'Talla creada correctamente';
                    } else {
                        $result['error'] = $talla->getDataError() ?: 'Ocurrió un problema al crear la talla';
                    }
                    break;

            case 'readAll':
                if ($result['dataset'] = $talla->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen tallas registradas';
                }
                break;
            case 'readOne':
                if (!$talla->setId($_POST['idTalla'])) {
                    $result['error'] = $talla->getDataError();
                } elseif ($result['dataset'] = $talla->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Talla inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$talla->setid($_POST['idTalla']) or
                    !$talla->setNombre($_POST['nombreTalla']) 
                ) {
                    $result['error'] = $talla->getDataError();
                } elseif ($talla->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Talla modificada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la talla';
                }
                break;
                case 'deleteRow':
                    if (!$talla->setId($_POST['idTalla'])) {
                        $result['error'] = $talla->getDataError();
                    } else {
                        // Obtener el nombre de la talla antes de eliminarla
                        $tallaNombre = $talla->getNombreTalla();
                
                        if ($talla->deleteRow()) {
                            $result['status'] = 1;
                            // Mostrar el nombre de la talla eliminada en el mensaje
                            $result['message'] = 'Talla "' . $tallaNombre . '" eliminada correctamente';
                        } else {
                            $result['error'] = 'Ocurrió un problema al eliminar la talla';
                        }
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
