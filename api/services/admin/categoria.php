<?php
// Se incluye la clase del modelo.
require_once('../../models/data/categoria_data.php');
require_once '../../helpers/security.php';
// Configurar las cabeceras de seguridad.
Security::setClickjackingProtection();
Security::setAdditionalSecurityHeaders();
// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $categoria = new CategoriaData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                 // Validar el término de búsqueda
                if (!Validator::validateSearch($_POST['search'])) {
                    // Si la validación falla, se asigna el error correspondiente
                    $result['error'] = Validator::getSearchError();
                    // Si la validación es exitosa, se ejecuta la búsqueda
                } elseif ($result['dataset'] = $categoria->searchRows()) {
                     // Si hay resultados, se asignan al dataset y se indica éxito
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                     // Si no hay coincidencias
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                 // Validar los datos enviados en el formulario
                $_POST = Validator::validateForm($_POST);
                // Verificar que el nombre de la categoría y la imagen hayan sido asignados correctamente
                if (
                    !$categoria->setNombre($_POST['nombreCategoria']) or
                    !$categoria->setImagen($_FILES['nombreIMG'])
                ) {
                 // Si falla alguna validación, se asigna el error correspondiente
                    $result['error'] = $categoria->getDataError();
                    // Si todo es válido, se intenta crear la categoría
                } elseif ($categoria->createRow()) {
                    // Si la creación es exitosa, se asigna el estado exitoso y el mensaje
                    $result['status'] = 1;
                    $result['message'] = 'Categoría creada correctamente';
                    // Se asigna el estado del archivo después de insertar.
                    $result['fileStatus'] = Validator::saveFile($_FILES['nombreIMG'], $categoria::RUTA_IMAGEN);
                } else {
                    $result['error'] = 'Ocurrió un problema al crear la categoría';
                }
                break;
            case 'readAll':
                // Obtener todas las categorías
                if ($result['dataset'] = $categoria->readAll()) {
                    // Si hay categorías registradas, se asignan al dataset y se indica éxito
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    // Si no hay categorías
                    $result['error'] = 'No existen categorías registradas';
                }
                break;
            case 'readOne':
                // Verificar si se ha establecido correctamente el ID de la categoría
                if (!$categoria->setId($_POST['idCategoria'])) {
                    $result['error'] = $categoria->getDataError();
                    // Si el ID es válido, se intenta leer la información de la categoría
                } elseif ($result['dataset'] = $categoria->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Categoría inexistente';
                }
                break;
                case 'updateRow':
                    $_POST = Validator::validateForm($_POST);
                
                    // Verificar y establecer los datos de la categoría
                    if (
                        !$categoria->setId($_POST['idCategoria']) or
                        !$categoria->setFilename() or
                        !$categoria->setNombre($_POST['nombreCategoria']) or
                        !$categoria->setImagen($_FILES['nombreIMG'], $categoria->getFilename())
                    ) {
                        $result['error'] = $categoria->getDataError();
                    } elseif ($categoria->updateRow()) {
                        // Obtener el nombre actualizado de la categoría
                        $nombreCategoria = $_POST['nombreCategoria']; // Nombre actualizado
                
                        $result['status'] = 1;
                        $result['message'] = "Categoría '$nombreCategoria' modificada correctamente";
                
                        // Se asigna el estado del archivo después de actualizar.
                        $result['fileStatus'] = Validator::changeFile($_FILES['nombreIMG'], $categoria::RUTA_IMAGEN, $categoria->getFilename());
                    } else {
                        $result['error'] = 'Ocurrió un problema al modificar la categoría';
                    }
                    break;                
                case 'deleteRow':
                    if (
                        !$categoria->setId($_POST['idCategoria']) or
                        !$categoria->setFilename()
                    ) {
                        $result['error'] = $categoria->getDataError();
                    } else {
                        // Obtener el nombre de la categoría antes de eliminarla
                        $categoriaNombre = $categoria->getNombreCategoria();
                
                        if ($categoria->deleteRow()) {
                            $result['status'] = 1;
                            // Mostrar el nombre de la categoría eliminada en el mensaje
                            $result['message'] = 'Categoría "' . $categoriaNombre . '" eliminada correctamente';
                            // Eliminar el archivo asociado
                            $result['fileStatus'] = Validator::deleteFile($categoria::RUTA_IMAGEN, $categoria->getFilename());
                        } else {
                            $result['error'] = 'Ocurrió un problema al eliminar la categoría';
                        }
                    }
                    break;
                case 'readTopProductos':
                    if (!$categoria->setId($_POST['idCategoria'])) {
                        $result['error'] = $categoria->getDataError();
                    } elseif ($result['dataset'] = $categoria->readTopProductos()) {
                        $result['status'] = 1;
                    } else {
                        $result['error'] = 'No existen productos vendidos por el momento';
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
