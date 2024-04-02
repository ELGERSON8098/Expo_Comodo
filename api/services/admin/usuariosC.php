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
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$usuariosC->setNombre($_POST['nombreUsuarioC']) or
                    !$usuariosC->setDescripcion($_POST['aliasUsuarioC']) or
                    !$usuariosC->setPrecio($_POST['correoUsuarioC']) or
                    !$usuariosC->setExistencias($_POST['claveUsuarioC']) or
                    !$usuariosC->setCategoria($_POST['TelUsuarioC']) or
                    !$usuariosC->setCategoria($_POST['duiUsuarioC'])
                ) {
                    $result['error'] = $usuariosC->getDataError();
                } elseif ($usuariosC->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el producto';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $usuariosC->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen productos registrados';
                }
                break;
            case 'readOne':
                if (!$usuariosC->setId($_POST['idusuarioC'])) {
                    $result['error'] = $usuariosC->getDataError();
                } elseif ($result['dataset'] = $usuariosC->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Producto inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$usuariosC->setNombre($_POST['nombreUsuarioC']) or
                    !$usuariosC->setDescripcion($_POST['aliasUsuarioC']) or
                    !$usuariosC->setPrecio($_POST['correoUsuarioC']) or
                    !$usuariosC->setExistencias($_POST['claveUsuarioC']) or
                    !$usuariosC->setCategoria($_POST['TelUsuarioC']) or
                    !$usuariosC->setCategoria($_POST['duiUsuarioC'])
                ) {
                    $result['error'] = $usuariosC->getDataError();
                } elseif ($usuariosC->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el producto';
                }
                break;
            case 'deleteRow':
                if (
                    !$usuariosC->setId($_POST['idusuarioC']) or
                    !$usuariosC->setFilename()
                ) {
                    $result['error'] = $usuariosC->getDataError();
                } elseif ($usuariosC->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el producto';
                }
                break;
            case 'cantidadProductosCategoria':
                if ($result['dataset'] = $usuariosC->cantidadProductosCategoria()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay datos disponibles';
                }
                break;
            case 'porcentajeProductosCategoria':
                if ($result['dataset'] = $usuariosC->porcentajeProductosCategoria()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay datos disponibles';
                }
                break;
                case 'getUser':
                    if (isset($_SESSION['aliasAdministrador'])) {
                        $result['status'] = 1;
                        $result['username'] = $_SESSION['aliasAdministrador'];
                    } else {
                        $result['error'] = 'Alias de administrador indefinido';
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
                    if ($result['dataset'] = $administrador->readProfile()) {
                        $result['status'] = 1;
                    } else {
                        $result['error'] = 'Ocurrió un problema al leer el perfil';
                    }
                    break;
                case 'editProfile':
                    $_POST = Validator::validateForm($_POST);
                    if (
                        !$administrador->setNombre($_POST['nombreAdministrador']) or
                        !$administrador->setCorreo($_POST['correoAdministrador']) or
                        !$administrador->setAlias($_POST['aliasAdministrador']) or
                        !$administrador->setClave($_POST['claveAdministrador'])
                    ) {
                        $result['error'] = $administrador->getDataError();
                    } elseif ($administrador->editProfile()) {
                        $result['status'] = 1;
                        $result['message'] = 'Perfil modificado correctamente';
                        $_SESSION['aliasAdministrador'] = $_POST['aliasAdministrador'];
                    } else {
                        $result['error'] = 'Ocurrió un problema al modificar el perfil';
                    }
                    break;
                case 'changePassword':
                    $_POST = Validator::validateForm($_POST);
                    if (!$administrador->checkPassword($_POST['claveActual'])) {
                        $result['error'] = 'Contraseña actual incorrecta';
                    } elseif ($_POST['claveNueva'] != $_POST['confirmarClave']) {
                        $result['error'] = 'Confirmación de contraseña diferente';
                    } elseif (!$administrador->setClave($_POST['claveNueva'])) {
                        $result['error'] = $administrador->getDataError();
                    } elseif ($administrador->changePassword()) {
                        $result['status'] = 1;
                        $result['message'] = 'Contraseña cambiada correctamente';
                    } else {
                        $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
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
