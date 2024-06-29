<?php
// Se incluye la clase del modelo.
require_once('../../models/data/usuariosC_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $usuariosC = new UsuariosData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $usuariosC->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $usuariosC->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen clientes registrados.';
                }
                break;
            case 'readOne':
                if (!$usuariosC->setId($_POST['idusuarioC'])) {
                    $result['error'] = $usuariosC->getDataError();
                } elseif ($result['dataset'] = $usuariosC->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Cliente inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$usuariosC->setNombre($_POST['nombreUsuarioC']) or
                    !$usuariosC->setAlias($_POST['aliasUsuarioC']) or
                    !$usuariosC->setCorreo($_POST['correoUsuarioC']) or
                    !$usuariosC->setDic($_POST['DirecC']) or
                    !$usuariosC->setTelefono($_POST['TelUsuarioC']) or
                    !$usuariosC->setDUI($_POST['duiUsuarioC'])
                ) {
                    $result['error'] = $usuariosC->getDataError();
                } elseif ($usuariosC->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cliente modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar al cliente.';
                }
                break;
            case 'deleteRow':
                if (
                    !$usuariosC->setId($_POST['idusuarioC'])
                ) {
                    $result['error'] = $usuariosC->getDataError();
                } elseif ($usuariosC->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cliente eliminado correctamente.';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el cliente.';
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
