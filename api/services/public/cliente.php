<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/cliente_data.php');
require_once ('../../services/admin/mail_config.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $cliente = new ClienteData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'recaptcha' => 0, 'message' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como cliente para realizar las acciones correspondientes.
    if (isset($_SESSION['idUsuario'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un cliente ha iniciado sesión.
        switch ($_GET['action']) {
            case 'getUser':
                if (isset($_SESSION['UsuarioCliente'])) {
                    $result['status'] = 1;
                    $result['username'] = $_SESSION['UsuarioCliente'];
                } else {
                    $result['error'] = 'Usuario indefinido';
                }
                break;
            case 'readProfile':
                if ($result['dataset'] = $cliente->readProfile()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Ocurrió un problema al leer el perfil';
                }
                break;
                case 'editProfile':
                    $_POST = Validator::validateForm($_POST);
                    if (
                        !$cliente->setNombre($_POST['nombre']) or
                        !$cliente->setCorreos($_POST['correo']) or
                        !$cliente->setAlias($_POST['username']) or
                        !$cliente->setTelefono($_POST['telefono']) or
                        !$cliente->setDirec($_POST['direccion'])
                    ) {
                        $result['error'] = $cliente->getDataError();
                    } elseif ($cliente->editProfileS()) {
                        $result['status'] = 1;
                        $result['message'] = 'Perfil modificado correctamente';
                        $_SESSION['aliaCliente'] = $_POST['username'];
                    } else {
                        $result['error'] = 'Ocurrió un problema al modificar el perfil';
                    }
                    break;

            case 'changePassword':
                $_POST = Validator::validateForm($_POST);
                if (!$cliente->checkPassword($_POST['claveActual'])) {
                    $result['error'] = 'Contraseña actual incorrecta';
                } elseif ($_POST['claveNueva'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Confirmación de contraseña diferente';
                } elseif (!$cliente->setClave($_POST['claveNueva'])) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->changePassword()) {
                    $result['status'] = 1;
                    $result['message'] = 'La constraseña ha sido cambiada exitosamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al cambiar la contraseña';
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
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el cliente no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'signUp':
                $_POST = Validator::validateForm($_POST);
                // Se establece la clave secreta para el reCAPTCHA de acuerdo con la cuenta de Google.
                $secretKey = '6LdBzLQUAAAAAL6oP4xpgMao-SmEkmRCpoLBLri-';
                // Se establece la dirección IP del servidor.
                $ip = $_SERVER['REMOTE_ADDR'];
                // Se establecen los datos del raCAPTCHA.
                $data = array('secret' => $secretKey, 'response' => $_POST['gRecaptchaResponse'], 'remoteip' => $ip);
                // Se establecen las opciones del reCAPTCHA.
                $options = array(
                    'http' => array('header' => 'Content-type: application/x-www-form-urlencoded\r\n', 'method' => 'POST', 'content' => http_build_query($data)),
                    'ssl' => array('verify_peer' => false, 'verify_peer_name' => false)
                );

                $url = 'https://www.google.com/recaptcha/api/siteverify';
                $context = stream_context_create($options);
                $response = file_get_contents($url, false, $context);
                $captcha = json_decode($response, true);

                if (!$captcha['success']) {
                    $result['recaptcha'] = 1;
                    $result['error'] = 'No eres humano';
                } elseif (!isset($_POST['condicion'])) {
                    $result['error'] = 'Debe marcar la aceptación de términos y condiciones';
                } elseif (
                    !$cliente->setNombre($_POST['nombreCliente']) or
                    !$cliente->setAlias($_POST['UsuarioCliente']) or
                    !$cliente->setCorreo($_POST['correoCliente']) or
                    !$cliente->setClave($_POST['claveCliente']) or
                    !$cliente->setDirec($_POST['direc'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($_POST['claveCliente'] != $_POST['confirmarClave']) {
                    $result['error'] = 'Contraseñas diferentes';
                } elseif ($cliente->createUsuario()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cuenta registrada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al registrar la cuenta';
                }
                break;

            case 'signUpMovil':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$cliente->setNombre($_POST['nombreCliente']) or
                    !$cliente->setAlias($_POST['usuarioCliente']) or
                    !$cliente->setCorreo($_POST['correoCliente']) or
                    !$cliente->setClave($_POST['claveCliente']) or
                    !$cliente->setTelefono($_POST['telefonoCliente']) or
                    !$cliente->setDui($_POST['duiCliente']) or
                    !$cliente->setDirec($_POST['direccionCliente'])
                ) {
                    $result['error'] = $cliente->getDataError();
                } elseif ($cliente->createUsuario()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cuenta registrada correctamente';
                } else {
                    $result['error'] = 'Ocurrio un problema al registrar la cuenta';
                }
                break;



            case 'logIn':
                $_POST = Validator::validateForm($_POST);
                if (!$cliente->checkUser($_POST['UsuarioCliente'], $_POST['clave'])) {
                    $result['error'] = 'Datos incorrectos';
                } elseif ($cliente->checkStatus()) {
                    $result['status'] = 1;
                    $result['message'] = 'Autenticación correcta';
                } else {
                    $result['error'] = 'La cuenta ha sido desactivada';
                }
                break;


            //Metodos para recuperación de contraseña en movil
           case 'solicitarPinRecuperacion':
                $_POST = Validator::validateForm($_POST);
                if (!isset($_POST['correo'])) {
                    $result['error'] = 'Falta el correo electrónico';
                } elseif (!$cliente->setCorreos($_POST['correo'])) {
                    $result['error'] = 'Correo electrónico inválido';
                } else {
                    // Verificar si el correo existe en la base de datos
                    $checkCorreoSql = 'SELECT COUNT(*) as count, nombre FROM tb_usuarios WHERE correo = ?';
                    $checkCorreoParams = array($_POST['correo']);
                    $checkCorreoResult = Database::getRow($checkCorreoSql, $checkCorreoParams);

                    if ($checkCorreoResult['count'] == 0) {
                        $result['error'] = 'No existe una cuenta asociada a este correo electrónico';
                    } elseif ($pin = $cliente->generarPinRecuperacion($_POST['correo'])) {
                        $result['status'] = 1;
                        $result['message'] = 'PIN generado con éxito';

                        // Enviar correo con el PIN
                        $email = $_POST['correo'];
                        $nombre = $checkCorreoResult['nombre'];
                        $subject = "Recuperacion de clave - Comodo$";
                        $body = "
                                <p>Estimado/a {$nombre},</p>
                                <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en Comodo$.</p>
                                <p>Tu PIN de recuperación es: <strong>{$pin}</strong></p>
                                <p>Este PIN es válido por los próximos 30 minutos. Si no solicitaste este cambio, por favor ignora este mensaje.</p>
                                <p>Para completar el proceso de recuperación de contraseña, ingresa este PIN en la aplicación.</p>
                                <p>Si tienes alguna pregunta o necesitas ayuda adicional, no dudes en contactarnos.</p>
                                <p>Saludos cordiales,<br>
                                El equipo de Comodo$</p>
                            ";

                        $emailResult = sendEmail($email, $subject, $body);
                        if ($emailResult !== true) {
                            $result['message'] .= ' Sin embargo, no se pudo enviar el correo con el PIN.';
                        }
                    } else {
                        $result['error'] = 'No se pudo generar el PIN';
                    }
                }
                break;
            case 'verificarPin':
                if (!isset($_POST['correo']) || !isset($_POST['pin'])) {
                    $result['error'] = 'Faltan datos necesarios';
                } elseif (!$cliente->setCorreo($_POST['correo'])) {
                    $result['error'] = 'Correo electrónico inválido';
                } else {
                    $id_usuario = $cliente->verificarPinRecuperacion($_POST['pin']);
                    if ($id_usuario) {
                        $result['status'] = 1;
                        $result['id_usuario'] = $id_usuario;
                        $result['message'] = 'PIN verificado correctamente';
                    } else {
                        $result['error'] = 'PIN inválido o expirado';
                    }
                }
                break;
            case 'cambiarClaveConPin':
                $_POST = Validator::validateForm($_POST);
                if (!isset($_POST['id_usuario']) || !isset($_POST['nuevaClave'])) {
                    $result['error'] = 'Faltan datos necesarios';
                } else {
                    $id_usuario = $_POST['id_usuario'];
                    $nuevaClave = $_POST['nuevaClave'];
                    if ($cliente->cambiarClaveConPin($id_usuario, $nuevaClave)) {
                        $result['status'] = 1;
                        $result['message'] = 'Contraseña cambiada exitosamente';
                        $cliente->resetearPin(); // Resetea el PIN para que no se pueda usar nuevamente
                    } else {
                        $result['error'] = 'No se pudo cambiar la contraseña';
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
    print (json_encode($result));
} else {
    print (json_encode('Recurso no disponible'));
}
