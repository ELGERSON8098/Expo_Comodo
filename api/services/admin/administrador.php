<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/administrador_data.php');
require_once ('../../services/admin/mail_config.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $administrador = new AdministradorData;
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
                } elseif ($result['dataset'] = $administrador->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setNombre($_POST['nombreAdministrador']) or
                    !$administrador->setCorreo($_POST['correoAdministrador']) or
                    !$administrador->setAlias($_POST['aliasAdministrador']) or
                    !$administrador->setClave($_POST['claveAdministrador'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($_POST['claveAdministrador'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($administrador->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el administrador';
                }
                break;
            case 'createTrabajadores':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setNombre($_POST['NAdmin']) or
                    !$administrador->setAlias($_POST['NUsuario']) or
                    !$administrador->setCorreo($_POST['CorreoAd']) or
                    !$administrador->setClave($_POST['ContraAd']) or
                    !$administrador->setNivel($_POST['NivAd'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($_POST['ContraAd'] != $_POST['confirmarClaveA']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($administrador->createTrabajadores()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador creado correctamente';
                    // Enviar correo de confirmación
                    $email = $_POST['CorreoAd'];
                    $subject = "Cuenta de trabajador en Comodo$";
                    $body = "
                    <p>Bienvenido/a a Comodo$</p>
                    <p>¡Hola {$_POST['NAdmin']}!</p>
                    <p>Gracias por unirte al equipo de administradores de Comodo$. 
                      Se ha confiado en ti para formar parte de este proyecto emocionante, y estamos seguros de que tu experiencia y habilidades serán un gran activo para nuestro sitio web.</p>
                        <p>Su cuenta de trabajador ha sido creada exitosamente.</p>
                        <p>Tus credenciales de acceso:</p>
                        <ul>
                            <li>Nombre de usuario: {$_POST['NUsuario']}</li>
                            <li>Correo electrónico: {$_POST['CorreoAd']}</li>
                            <li>Contraseña temporal: {$_POST['ContraAd']}</li>
                        </ul>
                        <p>Por favor, inicia sesión con esta información y asegúrate de cambiar tu contraseña temporal por una más segura y personal.</p>
                        <p>Saludos cordiales,<br>
                        El equipo de Comodo$</p>
                    ";

                    $emailResult = sendEmail($email, $subject, $body);
                    if ($emailResult !== true) {
                        $result['message'] .= ' No se pudo enviar el correo de confirmación.';
                    }
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el trabajador';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $administrador->readAllS()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen administradores registrados';
                }
                break;
            case 'readAllNivelesUsuarios':
                if ($result['dataset'] = $administrador->readAllNivelesUsuarios()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen niveles de usuario registrados';
                }
                break;
            case 'readOne':
                if (!$administrador->setId($_POST['idAdmin'])) {
                    $result['error'] = 'Administrador incorrecto';
                } elseif ($result['dataset'] = $administrador->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Administrador inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setId($_POST['idAdmin']) or
                    !$administrador->setNombre($_POST['NAdmin']) or
                    !$administrador->setAlias($_POST['NUsuario']) or
                    !$administrador->setCorreo($_POST['CorreoAd']) or
                    !$administrador->setNivel($_POST['NivAd'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Trabajador modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el trabajador';
                }
                break;
            case 'deleteRow':
                if (
                    !$administrador->setid($_POST['idAdmin'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el administrador';
                }
                break;
            case 'getUser':
                if (isset($_SESSION['aliasAdministrador'])) {
                    // Inicia la conexión a la base de datos.
                    $db = new Database();

                    // Obtener el alias del administrador de la sesión.
                    $alias = $_SESSION['aliasAdministrador'];

                    // Consulta para obtener el nivel de usuario basado en el alias.
                    $sql = 'SELECT id_nivel_usuario FROM tb_admins WHERE usuario_administrador = ?';
                    $params = array($alias);
                    $data = $db->getRow($sql, $params);

                    if ($data) {
                        $result['status'] = 1;
                        $result['username'] = $alias;
                        $result['user_level'] = $data['id_nivel_usuario'];
                    } else {
                        $result['error'] = 'No se pudo obtener el nivel de usuario';
                    }
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
                    !$administrador->setAlias($_POST['aliasAdministrador'])
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
    } else {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readUsers':
                if ($administrador->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Debe autenticarse para ingresar';
                } else {
                    $result['error'] = 'Debe crear un administrador para comenzar';
                }
                break;
            case 'signUp':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$administrador->setNombre($_POST['nombreAdministrador']) or
                    !$administrador->setCorreo($_POST['correoAdministrador']) or
                    !$administrador->setAlias($_POST['aliasAdministrador']) or
                    !$administrador->setClave($_POST['claveAdministrador'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($_POST['claveAdministrador'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($administrador->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Administrador registrado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar el administrador';
                }
                break;
            case 'logIn':
                $_POST = Validator::validateForm($_POST);
                if ($administrador->checkUser($_POST['alias'], $_POST['clave'])) {
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
    print (json_encode($result));
} else {
    print (json_encode('Recurso no disponible'));
}
