<?php
// Se incluye la clase del modelo.
require_once('../../models/data/genero_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $genero = new generoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $categoria->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$genero->setNombre($_POST['nombreCategoria']) or
                    !$genero->setDescripcion($_POST['descripcionCategoria']) or
                    !$genero->setImagen($_FILES['imagenCategoria'])
                ) {
                    $result['error'] = $genero->getDataError();
                } elseif ($genero->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Genero de zapato creado correctamente';
                    // Se asigna el estado del archivo después de insertar.
                    $result['fileStatus'] = Validator::saveFile($_FILES['imagenCategoria'], $genero::RUTA_IMAGEN);
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el genero de zapato';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $genero->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen generos de zapatos registradas';
                }
                break;
            case 'readOne':
                if (!$genero->setId($_POST['idCategoria'])) {
                    $result['error'] = $genero->getDataError();
                } elseif ($result['dataset'] = $genero->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Genero de zapato inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$genero->setId($_POST['idCategoria']) or
                    !$genero->setFilename() or
                    !$genero->setNombre($_POST['nombreCategoria']) or
                    !$genero->setDescripcion($_POST['descripcionCategoria']) or
                    !$genero->setImagen($_FILES['imagenCategoria'], $categoria->getFilename())
                ) {
                    $result['error'] = $genero->getDataError();
                } elseif ($genero->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Genero del zapato modificado correctamente';
                    // Se asigna el estado del archivo después de actualizar.
                    $result['fileStatus'] = Validator::changeFile($_FILES['imagenCategoria'], $genero::RUTA_IMAGEN, $categoria->getFilename());
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la categoría';
                }
                break;
            case 'deleteRow':
                if (
                    !$genero->setId($_POST['idCategoria']) or
                    !$genero->setFilename()
                ) {
                    $result['error'] = $genero->getDataError();
                } elseif ($genero->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Categoría eliminada correctamente';
                    // Se asigna el estado del archivo después de eliminar.
                    $result['fileStatus'] = Validator::deleteFile($genero::RUTA_IMAGEN, $genero->getFilename());
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar la categoría';
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
