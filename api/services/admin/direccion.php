<?php
// Se incluye la clase del modelo.
require_once('../../models/data/direccion_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $direccion = new direccionData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $direccion->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$direccion->setNombre($_POST['Direc']) or
                    !$direccion->setCorreo($_POST['Departamento']) or
                    !$direccion->setCorreo($_POST['Direccion']) 
                    
                ) {
                    $result['error'] = $direccion->getDataError();
                } elseif ($_POST['claveAdministrador'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($direccion->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el administrador';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $direccion->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen Direcciones registradas';
                }
                break;
            case 'readOne':
                if (!$direccion->setId($_POST['Direc'])) {
                    $result['error'] = 'Direccion incorrecto';
                } elseif ($result['dataset'] = $direccion->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Direccion inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$direccion->setNombre($_POST['Direc']) or
                    !$direccion->setCorreo($_POST['Departamento']) or
                    !$direccion->setCorreo($_POST['Direccion']) 
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Direccion modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la direccion';
                }
                break;
            case 'deleteRow':
                if ($_POST['idAdministrador'] == $_SESSION['idAdministrador']) {
                    $result['error'] = 'No se puede eliminar a sí mismo';
                } elseif (!$direccion->setId($_POST['idAdministrador'])) {
                    $result['error'] = $direccion->getDataError();
                } elseif ($direccion->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Direccion eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el la direccion';
                }
                break;
        }
    }
    // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
    $result['exception'] = Database::getException();
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('Content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
