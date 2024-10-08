<?php
// Se incluye la clase del modelo.
require_once('../../models/data/color_data.php');
require_once '../../helpers/security.php';
// Configurar las cabeceras de seguridad.
Security::setClickjackingProtection();
Security::setAdditionalSecurityHeaders();
// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $color = new colorData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
              // Buscar filas que coincidan con la búsqueda proporcionada
            case 'searchRows':
                        // Validar el texto de búsqueda
                if (!Validator::validateSearch($_POST['search'])) {
                    // Si es inválido, almacenar el mensaje de error
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $color->searchRows()) {
                     // Si hay coincidencias, guardar los resultados en 'dataset' y mostrar la cantidad encontrada
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
                // Crear un nuevo registro
                case 'createRow':
                    $_POST = Validator::validateForm($_POST);
                     // Validar y limpiar los datos recibidos en $_POST
                    if (!$color->setNombre($_POST['nombreColor'])) {
                           // Si hay un error en los datos, almacenar el mensaje de error
                        $result['error'] = $color->getDataError();
                    } elseif ($color->createRow()) {
                         // Si se crea el registro correctamente, mostrar un mensaje de éxito
                        $result['status'] = 1;
                        $result['message'] = 'Color creado correctamente';
                    } else {
                         // Si ocurre un error al crear, devolver el mensaje de error correspondiente
                        $result['error'] = $color->getDataError() ?: 'Ocurrió un problema al crear el Color';
                    }
                    break;
                 // Leer todos los registros
            case 'readAll':
                // Obtener todos los registros de la base de datos
                if ($result['dataset'] = $color->readAll()) {
                     // Si se encuentran registros, mostrar la cantidad encontrada
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                     // Si no hay registros, devolver un mensaje de error
                    $result['error'] = 'No existen colores registrados';
                }
                break;
                // Leer un registro específico
            case 'readOne':
                 // Verificar si se ha enviado un ID válido
                if (!$color->setId($_POST['idColor'])) {
                    // Si el ID es inválido, devolver un mensaje de error
                    $result['error'] = $color->getDataError();
                } elseif ($result['dataset'] = $color->readOne()) {
                    // Si se encuentra el registro, devolver los datos
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Producto inexistente';
                }
                break;
                // Actualizar un registro existente
                case 'updateRow':
                    $_POST = Validator::validateForm($_POST);
                
                    // Verificar y establecer los datos del color
                    if (
                        !$color->setId($_POST['idColor']) or
                        !$color->setNombre($_POST['nombreColor'])
                    ) {
                        $result['error'] = $color->getDataError();
                    } elseif ($color->updateRow()) {
                        // Obtener el nombre actualizado del color
                        $nombreColor = $_POST['nombreColor']; // Nombre actualizado
                
                        $result['status'] = 1;
                        $result['message'] = "Color '$nombreColor' modificado correctamente";
                    } else {
                        $result['error'] = 'Ocurrió un problema al modificar el color';
                    }
                    break;
                
                case 'deleteRow':
                    // Establecer el ID del color a eliminar
                    if (!$color->setId($_POST['idColor'])) {
                        $result['error'] = $color->getDataError();
                    } else {
                        // Obtener el nombre del color antes de eliminarlo
                        $colorNombre = $color->getNombreColor();
                
                        if ($color->deleteRow()) {
                            $result['status'] = 1;
                            // Mostrar el nombre del color eliminado en el mensaje
                            $result['message'] = 'Color "' . $colorNombre . '" eliminado correctamente';
                        } else {
                            $result['error'] = 'Ocurrió un problema al eliminar el color';
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
