<?php
// Se incluye la clase del modelo.
require_once('../../models/data/administrador_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $tbAdmins = new AdministradorData;
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
                } elseif ($result['dataset'] = $tbAdmins->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$tbAdmins->setNombre($_POST['nombreAdministrador']) or
                    !$tbAdmins->setAlias($_POST['usuarioAdministrador']) or
                    !$tbAdmins->setCorreo($_POST['correoAdministrador']) or
                    !$tbAdmins->setTel($_POST['telefonoAdministrador']) or
                    !$tbAdmins->setDui($_POST['duiAdministrador']) or
                    !$tbAdmins->setClave($_POST['claveAdministrador'])
                ) {
                    $result['error'] = $tbAdmins->getDataError();
                } elseif ($_POST['claveAdministrador'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($tbAdmins->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el administrador';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $tbAdmins->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen administradores registrados';
                }
                break;
            case 'readOne':
                if (!$tbAdmins->setId($_POST['idAdministrador'])) {
                    $result['error'] = 'Administrador incorrecto';
                } elseif ($result['dataset'] = $tbAdmins->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Administrador inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$tbAdmins->setNombre($_POST['nombreAdministrador']) or
                    !$tbAdmins->setUser($_POST['usuarioAdministrador']) or
                    !$tbAdmins->setCorreo($_POST['correoAdministrador']) or
                    !$tbAdmins->setTel($_POST['telefonoAdministrador']) or
                    !$tbAdmins->setDui($_POST['duiAdministrador']) or
                    !$tbAdmins->setClave($_POST['claveAdministrador'])
                ) {
                    $result['error'] = $tbAdmins->getDataError();
                } elseif ($tbAdmins->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el administrador';
                }
                break;
            case 'deleteRow':
                if ($_POST['idAdministrador'] == $_SESSION['idAdministrador']) {
                    $result['error'] = 'No se puede eliminar a sí mismo';
                } elseif (!$tbAdmins->setId($_POST['idAdministrador'])) {
                    $result['error'] = $tbAdmins->getDataError();
                } elseif ($tbAdmins->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el administrador';
                }
                break;
            case 'getUser':
                if (isset($_SESSION['usuarioAdministrador'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['usuarioAdministrador'];
                } else {
                    $result['error'] = 'Usuario de administrador indefinido';
                }
                break;
            case 'logOut':
                if (session_destroy()) {
                    $result['status'] = 1;
                    $result['message'] = 'Sesión eliminada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cerrar la sesión';
                }
                break;
            case 'readProfile':
                if ($result['dataset'] = $tbAdmins->readProfile()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Ocurrió un problema al leer el perfil';
                }
                break;
            case 'editProfile':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$tbAdmins->setNombre($_POST['nombreAdministrador']) or
                    !$tbAdmins->setUser($_POST['usuarioAdministrador']) or
                    !$tbAdmins->setCorreo($_POST['correoAdministrador']) or
                    !$tbAdmins->setTel($_POST['telefonoAdministrador']) or
                    !$tbAdmins->setDui($_POST['duiAdministrador']) or
                    !$tbAdmins->setClave($_POST['claveAdministrador'])
                ) {
                    $result['error'] = $tbAdmins->getDataError();
                } elseif ($tbAdmins->editProfile()) {
                    $result['status'] = 1;
                    $result['message'] = 'Perfil modificado correctamente';
                    $_SESSION['usuarioAdministrador'] = $_POST['usuarioAdministrador'];
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el perfil';
                }
                break;
            case 'changePassword':
                $_POST = Validator::validateForm($_POST);
                if (!$tbAdmins->checkPassword($_POST['claveActual'])) {
                    $result['error'] = 'Contraseña actual incorrecta';
                } elseif ($_POST['claveNueva'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Confirmación de contraseña diferente';
                } elseif (!$tbAdmins->setClave($_POST['claveNueva'])) {
                    $result['error'] = $tbAdmins->getDataError();
                } elseif ($tbAdmins->changePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña cambiada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readUsers':
                if ($tbAdmins->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['error'] = 'Debe crear un administrador para comenzar';
                }
                break;
            case 'signUp':
                $_POST = Validator::validateForm($_POST);
                echo $_POST['usuarioAdministrador'];
                if (
                    !$tbAdmins->setNombre($_POST['nombreAdministrador']) or
                    !$tbAdmins->setAlias($_POST['usuarioAdministrador']) or
                    !$tbAdmins->setCorreo($_POST['correoAdministrador']) or
                    !$tbAdmins->setTel($_POST['telefonoAdministrador']) or
                    !$tbAdmins->setDui($_POST['duiAdministrador']) or
                    !$tbAdmins->setClave($_POST['claveAdministrador'])
                ) {
                    $result['error'] = $tbAdmins->getDataError();
                } elseif ($_POST['claveAdministrador'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($tbAdmins->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador registrado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar el administrador';
                }
                break;
            case 'logIn':
                $_POST = Validator::validateForm($_POST);
                if ($tbAdmins->checkUser($_POST['usuario'], $_POST['clave'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación correcta';
                } else {
                    $result['error'] = 'Credenciales incorrectas';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible fuera de la sesión';
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