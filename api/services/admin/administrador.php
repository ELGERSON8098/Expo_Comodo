<?php
// Se incluye la clase del modelo.
require_once('../../models/data/administrador_data.php');
require_once('../../services/admin/mail_config.php');
require_once '../../helpers/security.php';
// Configurar las cabeceras de seguridad.
Security::setClickjackingProtection();
Security::setAdditionalSecurityHeaders();
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

                // Validar datos del formulario
                if (
                    !$administrador->setNombre($_POST['NAdmin']) ||
                    !$administrador->setAlias($_POST['NUsuario']) ||
                    !$administrador->setCorreo($_POST['CorreoAd']) ||
                    !$administrador->setClave($_POST['ContraAd']) ||
                    !$administrador->setNivel($_POST['NivAd'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($_POST['ContraAd'] !== $_POST['confirmarClaveA']) {
                    $result['error'] = 'Las contraseñas no coinciden';
                } else {
                    // Verificar duplicidad de usuario y correo electrónico
                    $checkCorreoSql = 'SELECT COUNT(*) as count FROM tb_admins WHERE correo_administrador = ?';
                    $checkCorreoParams = array($_POST['CorreoAd']);

                    $checkAliasSql = 'SELECT COUNT(*) as count FROM tb_admins WHERE usuario_administrador = ?';
                    $checkAliasParams = array($_POST['NUsuario']);

                    $checkCorreoResult = Database::getRow($checkCorreoSql, $checkCorreoParams);
                    $checkAliasResult = Database::getRow($checkAliasSql, $checkAliasParams);

                    if ($checkCorreoResult['count'] > 0) {
                        $result['error'] = 'El correo electrónico ya está registrado';
                    } elseif ($checkAliasResult['count'] > 0) {
                        $result['error'] = 'El nombre de usuario ya está en uso';
                    } elseif ($administrador->createTrabajadores()) {
                        // Creación exitosa del trabajador (administrador)
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
                        // Error general al crear el trabajador (administrador)
                        $result['error'] = 'Ocurrió un problema al crear el trabajador';
                    }
                }
                break;
            case 'checkSession':
                if (isset($_SESSION['id_administrador'])) {
                    $result['status'] = 1;
                    $result['session'] = true;
                    $result['message'] = 'Sesión activa';
                } else {
                    $result['status'] = 1;
                    $result['session'] = false;
                    $result['message'] = 'No hay sesión activa';
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

                // Verificar y establecer los datos del administrador
                if (
                    !$administrador->setId($_POST['idAdmin']) or
                    !$administrador->setNombre($_POST['NAdmin']) or
                    !$administrador->setAlia($_POST['NUsuario']) or
                    !$administrador->setCorreos($_POST['CorreoAd']) or
                    !$administrador->setNivel($_POST['NivAd'])
                ) {
                    $result['error'] = $administrador->getDataError();
                } elseif ($administrador->updateRow()) {
                    // Obtener el nombre actualizado del administrador
                    $nombreAdministrador = $_POST['NAdmin']; // Nombre actualizado

                    $result['status'] = 1;
                    $result['message'] = "Trabajador $nombreAdministrador modificado correctamente";

                    // Enviar correo de confirmación de actualización
                    $email = $_POST['CorreoAd'];
                    $subject = "Actualización de cuenta de trabajador en Comodo$";
                    $body = "
                        <p>Actualización de cuenta en Comodo$</p>
                        <p>¡Hola $nombreAdministrador!</p>
                        <p>Queremos informarte que algunos datos de tu cuenta de trabajador en Comodo$ han sido actualizados exitosamente.</p>
                        <p>Detalles de tu cuenta actualizada:</p>
                        <ul>
                        <li>Nombre de usuario: {$_POST['NUsuario']}</li>
                        <li>Correo electrónico: {$_POST['CorreoAd']}</li>
                        </ul>
                        <p>Saludos cordiales,<br>
                        El equipo de Comodo$</p>
                        ";
                    $emailResult = sendEmail($email, $subject, $body);
                    if ($emailResult !== true) {
                        $result['message'] .= ' No se pudo enviar el correo de confirmación de actualización.';
                    }
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el trabajador';
                }
                break;
            case 'deleteRow':
                // Establecer el ID del administrador
                if (!$administrador->setId($_POST['idAdmin'])) {
                    $result['error'] = $administrador->getDataError();
                } else {
                    // Obtener el nombre del administrador antes de eliminarlo
                    $adminNombre = $administrador->getNombreAdministrador();

                    if ($administrador->deleteRow()) {
                        $result['status'] = 1;
                        // Mostrar el nombre del administrador eliminado en el mensaje
                        $result['message'] = 'Administrador "' . $adminNombre . '" eliminado correctamente';
                    } else {
                        $result['error'] = 'Ocurrió un problema al eliminar el administrador';
                    }
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

                // Verificar que la contraseña actual sea correcta
                if (!$administrador->checkPassword($_POST['claveActual'])) {
                    $result['error'] = 'Contraseña actual incorrecta';
                }
                // Verificar que la nueva contraseña no sea igual a la actual
                elseif (hash('sha256', $_POST['claveActual']) == hash('sha256', $_POST['claveNueva'])) {
                    $result['error'] = 'La nueva contraseña no puede ser igual a la contraseña actual';
                }
                // Verificar que la confirmación de contraseña coincida
                elseif ($_POST['claveNueva'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Confirmación de contraseña diferente';
                }
                // Verificar que se haya podido establecer la nueva contraseña
                elseif (!$administrador->setClave($_POST['claveNueva'])) {
                    $result['error'] = $administrador->getDataError();
                }
                // Intentar cambiar la contraseña
                elseif ($administrador->changePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'Contraseña cambiada correctamente';
                }
                // Error general
                else {
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

                // Verifica si ya existe un administrador
                if ($administrador->checkIfAnyUserExists()['COUNT(*)'] > 0) {
                    $result['error'] = 'Ya existe un administrador registrado. No se pueden agregar más.';
                } elseif (
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
                $alias = $_POST['alias'];
                $clave = $_POST['clave'];
                $omit2FA = isset($_POST['omit2FA']) && $_POST['omit2FA'] == '1';

                $loginResult = $administrador->checkUser($alias, $clave);

                if ($administrador->getCondicion() == "clave") {
                    $result['error'] = 'Ya pasaron 90 dias de la ultima vez que cambiaste tu clave';
                } else if ($loginResult === false) {
                    // Usuario o contraseña incorrectos
                    $blockData = $administrador->getBlockDataByAlias($alias); // Asegúrate de inicializar blockData correctamente
                    $newAttempts = $blockData['intentos_fallidos'];
                    if ($newAttempts >= 3) {
                        $bloqueoHasta = (new DateTime())->modify('+24 hours')->format('Y-m-d H:i:s');
                        $updateSql = 'UPDATE tb_admins SET intentos_fallidos = ?, bloqueo_hasta = ? WHERE usuario_administrador = ?';
                        Database::executeRow($updateSql, array($newAttempts, $bloqueoHasta, $alias));
                        $result['error'] = 'Cuenta bloqueada por 24 horas debido a múltiples intentos fallidos.';
                    } else {
                        $updateSql = 'UPDATE tb_admins SET intentos_fallidos = ? WHERE usuario_administrador = ?';
                        Database::executeRow($updateSql, array($newAttempts, $alias));
                        $result['error'] = 'Credenciales incorrectas. Intentos fallidos: ' . $newAttempts . '/3';
                    }
                } else if (is_array($loginResult) && $loginResult['status']) {
                    if ($omit2FA) {
                        $_SESSION['idAdministrador'] = $loginResult['id_administrador'];
                        $_SESSION['aliasAdministrador'] = $administrador->getAliasById($loginResult['id_administrador']);
                        $result['status'] = 1;
                        $result['message'] = 'Inicio de sesión exitoso. Bienvenido.';
                    } else {
                        $email = $administrador->getEmailById($loginResult['id_administrador']);
                        $subject = "Código de verificación 2FA - Comodo$";
                        $body = "Tu código de verificación es: {$loginResult['codigo2FA']}";
                        if (sendEmail($email, $subject, $body)) {
                            $result['status'] = 1;
                            $result['message'] = 'Primer paso de autenticación correcto. Se ha enviado un código a tu correo.';
                            $result['id_administrador'] = $loginResult['id_administrador'];
                            $result['twoFactorRequired'] = true;
                        } else {
                            $result['error'] = 'Error al enviar el código de verificación. Inténtalo de nuevo.';
                        }
                    }
                } else {
                    // Caso inesperado o usuario no existe
                    $result['error'] = 'El usuario no existe';
                }
                break;


            case 'verify2FA':
                $_POST = Validator::validateForm($_POST);
                $id_administrador = $_POST['id_administrador'];
                $codigo = $_POST['codigo2FA'];

                if ($administrador->verify2FACode($id_administrador, $codigo)) {
                    $_SESSION['idAdministrador'] = $id_administrador;
                    $_SESSION['aliasAdministrador'] = $administrador->getAliasById($id_administrador);
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación completa. Bienvenido.';
                } else {
                    $result['error'] = 'Código 2FA inválido o expirado.';
                }
                break;
            case 'requestPasswordReset':
                $_POST = Validator::validateForm($_POST);
                if (!$administrador->setCorreo($_POST['correo'])) {
                    $result['error'] = 'Correo inválido';
                } elseif ($administrador->checkEmail()) {
                    // Generar código de verificación
                    $codigo = sprintf("%06d", mt_rand(1, 999999));
                    if ($administrador->setResetCode($codigo)) {
                        $email = $_POST['correo'];
                        $subject = "Código de recuperación de contraseña - Comodo$";
                        $body = "
                <p>Has solicitado restablecer tu contraseña.</p>
                <p>Tu código de verificación es: <strong>{$codigo}</strong></p>
                <p>Introduce este código en la aplicación para crear una nueva contraseña.</p>
                <p>Si no has solicitado este cambio, puedes ignorar este correo.</p>
                <p>El equipo de Comodo$</p>
            ";
                        $emailResult = sendEmail($email, $subject, $body);
                        if ($emailResult === true) {
                            $result['status'] = 1;
                            $result['message'] = 'Se ha enviado un código de verificación a tu correo electrónico.';
                        } else {
                            $result['error'] = 'No se pudo enviar el correo de recuperación.';
                        }
                    } else {
                        $result['error'] = 'Ocurrió un problema al generar el código de recuperación.';
                    }
                } else {
                    $result['error'] = 'No existe una cuenta asociada a este correo.';
                }
                break;

            case 'verifyResetCode':
                $_POST = Validator::validateForm($_POST);

                if (!$administrador->setCorreo($_POST['correo'])) {
                    $result['error'] = 'Correo inválido';
                } elseif (!$administrador->setResetCodeForVerification($_POST['codigo'])) {
                    $result['error'] = 'Código inválido';
                } elseif ($administrador->verifyResetCode()) {
                    $result['status'] = 1;
                    $result['message'] = 'Código verificado correctamente';
                } else {
                    $result['error'] = 'Código de verificación incorrecto o expirado';
                }
                break;


            case 'resetPassword':
                $_POST = Validator::validateForm($_POST);

                // Verificar que el correo y el código de verificación sean correctos
                if (
                    !$administrador->setCorreo($_POST['correo']) ||  // Configurar el correo
                    !$administrador->setResetCodeForVerification($_POST['codigo'])  // Verificar el código de restablecimiento
                ) {
                    $result['error'] = $administrador->getDataError();
                }
                // Obtener la contraseña actual del usuario
                else {
                    // Obtener la contraseña encriptada actual y almacenarla en una variable
                    $claveActualEncriptada = $administrador->getClaveActual();

                    // Verificar si la nueva contraseña es igual a la actual
                    if (!$claveActualEncriptada) {
                        $result['error'] = 'No se pudo obtener la contraseña actual';
                    }
                    // Comparar la nueva contraseña en texto plano con la encriptada
                    elseif (password_verify($_POST['nuevaClave'], $claveActualEncriptada)) {
                        $result['error'] = 'La nueva contraseña no puede ser igual a la contraseña actual';
                    }
                    // Verificar que la nueva contraseña coincida con su confirmación
                    elseif ($_POST['nuevaClave'] != $_POST['confirmarClave']) {
                        $result['error'] = 'Las contraseñas no coinciden';
                    }
                    // Intentar configurar la nueva contraseña
                    elseif (!$administrador->setClave($_POST['nuevaClave'])) {
                        $result['error'] = $administrador->getDataError();
                    }
                    // Intentar restablecer la contraseña
                    elseif ($administrador->resetPassword()) {
                        $result['status'] = 1;
                        $result['message'] = 'Contraseña restablecida correctamente';
                    }
                    // Error general
                    else {
                        $result['error'] = 'Ocurrió un problema al restablecer la contraseña';
                    }
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
