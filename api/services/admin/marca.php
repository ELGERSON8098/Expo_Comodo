<?php
// Se incluye la clase del modelo.
require_once('../../models/data/marca_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $marca = new marcaData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $marca->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (!$marca->setNombre($_POST['nombreMarca'])) {
                    $result['error'] = $marca->getDataError();
                } elseif ($marca->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Marca agregada correctamente';
                } else {
                    $result['error'] = $marca->getDataError() ?: 'Ocurrió un problema al agregar la marca';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $marca->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen marcas registradas';
                }
                break;
            case 'readOne':
                if (!$marca->setId($_POST['idMarca'])) {
                    $result['error'] = $marca->getDataError();
                } elseif ($result['dataset'] = $marca->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Marca inexistente';
                }
                break;
                case 'updateRow':
                    $_POST = Validator::validateForm($_POST);
                
                    // Verificar y establecer los datos de la marca
                    if (
                        !$marca->setid($_POST['idMarca']) ||
                        !$marca->setNombre($_POST['nombreMarca'])
                    ) {
                        $result['error'] = $marca->getDataError();
                    } elseif ($marca->updateRow()) {
                        // Obtener el nombre actualizado de la marca
                        $nombreMarca = $_POST['nombreMarca']; // Nombre actualizado
                
                        $result['status'] = 1;
                        $result['message'] = "Marca '$nombreMarca' modificada correctamente.";
                    } else {
                        $result['error'] = 'Ocurrió un problema al modificar la marca.';
                    }
                    break;                
                case 'deleteRow':
                    if (!$marca->setId($_POST['idMarca'])) {
                        $result['error'] = $marca->getDataError();
                    } else {
                        // Obtener el nombre de la marca antes de eliminarla
                        $marcaNombre = $marca->getNombreMarca();
                
                        if ($marca->deleteRow()) {
                            $result['status'] = 1;
                            // Mostrar el nombre de la marca eliminada en el mensaje
                            $result['message'] = 'Marca "' . $marcaNombre . '" eliminada correctamente.';
                        } else {
                            $result['error'] = 'Ocurrió un problema al eliminar la marca.';
                        }
                    }
                    break;

            case 'ventasPorMarcasFecha':
                $_POST = Validator::validateForm($_POST);
                $fecha_inicio = $_POST['fechaInicio'];
                $fecha_fin = $_POST['fechaFin'];
                $marcas = json_decode($_POST['marcas'], true);

                // Validar las fechas
                if (!$marca->setFechaInicio($fecha_inicio)) {
                    $result['error'] = $marca->getDataError();
                    break;
                }
                if (!$marca->setFechaFin($fecha_fin)) {
                    $result['error'] = $marca->getDataError();
                    break;
                }

                // Validar que se hayan seleccionado marcas
                if (empty($marcas)) {
                    $result['error'] = 'Debe seleccionar al menos una marca';
                    break;
                }

                if ($result['dataset'] = $marca->ventasPorMarcasFecha($fecha_inicio, $fecha_fin, $marcas)) {
                    $result['status'] = 1;
                    $result['message'] = 'Datos obtenidos correctamente';
                } else {
                    $result['error'] = 'No se pudieron obtener los datos';
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
