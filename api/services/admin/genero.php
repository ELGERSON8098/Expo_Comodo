<?php
// Se incluye la clase del modelo.
require_once('../../models/data/genero_data.php');
require_once '../../helpers/security.php';
// Configurar las cabeceras de seguridad.
Security::setClickjackingProtection();
Security::setAdditionalSecurityHeaders();
// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $genero = new GeneroData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $genero->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$genero->setNombre($_POST['nombre_genero']) or
                    !$genero->setImagen($_FILES['imagen_genero'])
                ) {
                    $result['error'] = $genero->getDataError();
                } elseif ($genero->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Género creado correctamente';
                    // Se asigna el estado del archivo después de insertar.
                    $result['fileStatus'] = Validator::saveFile($_FILES['imagen_genero'], $genero::RUTA_IMAGEN);
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el género';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $genero->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen géneros registrados';
                }
                break;
            case 'readOne':
                if (!$genero->setId($_POST['idGenero'])) {
                    $result['error'] = $genero->getDataError();
                } elseif ($result['dataset'] = $genero->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Género inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);

                // Verificar y establecer los datos del género
                if (
                    !$genero->setId($_POST['idGenero']) ||
                    !$genero->setNombre($_POST['nombre_genero']) ||
                    !$genero->setImagen($_FILES['nombreIMG'], $_POST['imagenActual']) // Usa la imagen actual si no se proporciona una nueva
                ) {
                    $result['error'] = $genero->getDataError();
                } elseif ($genero->updateRow()) {
                    // Obtener el nombre actualizado del género
                    $nombreGenero = $_POST['nombre_genero'];

                    $result['status'] = 1;
                    $result['message'] = "Género '$nombreGenero' modificado correctamente";

                    // Cambiar el archivo de imagen solo si se ha subido una nueva
                    if ($_FILES['nombreIMG']['size'] > 0) {
                        $result['fileStatus'] = Validator::changeFile($_FILES['nombreIMG'], $genero::RUTA_IMAGEN, $genero->getFilename());
                    }
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el género';
                }
                break;

            case 'deleteRow':
                if (
                    !$genero->setId($_POST['idGenero']) or
                    !$genero->setFilename()
                ) {
                    $result['error'] = $genero->getDataError();
                } else {
                    // Obtener el nombre del género antes de eliminarlo
                    $generoNombre = $genero->getNombreGenero();

                    if ($genero->deleteRow()) {
                        $result['status'] = 1;
                        // Mostrar el nombre del género eliminado en el mensaje
                        $result['message'] = 'Género "' . $generoNombre . '" eliminado correctamente';
                        // Eliminar el archivo asociado
                        $result['fileStatus'] = Validator::deleteFile($genero::RUTA_IMAGEN, $genero->getFilename());
                    } else {
                        $result['error'] = 'Ocurrió un problema al eliminar el Género';
                    }
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
