<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/materiales_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $material = new materialData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $material->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (!$material->setNombre($_POST['nombreMaterial'])) {
                    $result['error'] = $material->getDataError();
                } elseif ($material->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Material agregado correctamente';
                } else {
                    $result['error'] = $material->getDataError() ?: 'Ocurrió un problema al agregar el material';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $material->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen materiales registrados.';
                }
                break;
            case 'readOne':
                if (!$material->setId($_POST['idMaterial'])) {
                    $result['error'] = $material->getDataError();
                } elseif ($result['dataset'] = $material->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Material inexistente.';
                }
                break;
                case 'updateRow':
                    $_POST = Validator::validateForm($_POST);
                
                    // Verificar y establecer los datos del material
                    if (
                        !$material->setId($_POST['idMaterial']) ||
                        !$material->setNombre($_POST['nombreMaterial'])
                    ) {
                        $result['error'] = $material->getDataError();
                    } elseif ($material->updateRow()) {
                        // Obtener el nombre actualizado del material
                        $nombreMaterial = $_POST['nombreMaterial']; // Nombre actualizado
                
                        $result['status'] = 1;
                        $result['message'] = "Material '$nombreMaterial' modificado correctamente.";
                    } else {
                        $result['error'] = 'Ocurrió un problema al modificar el material.';
                    }
                    break;                
                case 'deleteRow':
                    if (!$material->setId($_POST['idMaterial'])) {
                        $result['error'] = $material->getDataError();
                    } else {
                        // Obtener el nombre del material antes de eliminarlo
                        $materialNombre = $material->getNombreMaterial();
                
                        if ($material->deleteRow()) {
                            $result['status'] = 1;
                            // Mostrar el nombre del material eliminado en el mensaje
                            $result['message'] = 'Material "' . $materialNombre . '" eliminado correctamente.';
                        } else {
                            $result['error'] = 'Ocurrió un problema al eliminar el material.';
                        }
                    }
                    break;
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
