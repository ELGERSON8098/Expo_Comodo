<?php
// Se incluye la clase del modelo.
require_once('../../models/data/reserva_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $reserva = new reservaData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } elseif ($result['dataset'] = $reserva->searchRows()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$categoria->setNombre($_POST['nombreCategoria']) or
                    !$categoria->setDescripcion($_POST['descripcionCategoria']) or
                    !$categoria->setImagen($_FILES['imagenCategoria'])
                ) {
                    $result['error'] = $categoria->getDataError();
                } elseif ($categoria->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Categoría creada correctamente';
                    // Se asigna el estado del archivo después de insertar.
                    $result['fileStatus'] = Validator::saveFile($_FILES['imagenCategoria'], $categoria::RUTA_IMAGEN);
                } else {
                    $result['error'] = 'Ocurrió un problema al crear la categoría';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $reserva->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen reservas registradas';
                }
                break;
                case 'readAlls':
                    if ($result['dataset'] = $reserva->readAlls()) {
                        $result['status'] = 1;
                        $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                    } else {
                        $result['error'] = 'No existen categorías registradas';
                    }
                    break;
            case 'readOne':
                if (!$reserva->setId($_POST['idReserva'])) {
                    $result['error'] = $reserva->getDataError();
                } elseif ($result['dataset'] = $reserva->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Reserva inexistente';
                }
                break;
                case 'readOneS':
                    if (!$reserva->setId($_POST['idReservass'])) {
                        $result['error'] = $reserva->getDataError();
                    } elseif ($result['dataset'] = $reserva->readOneS()) {
                        $result['status'] = 1;
                    } else {
                        $result['error'] = 'Reserva inexistentes';
                    }
                    break;
                    case 'readDetalles':
                        if (!$reserva->setId($_POST['idReservas'])) {
                            $result['error'] = $reserva->getDataError();
                        } elseif ($result['dataset'] = $reserva->readDetalles()) {
                            $result['status'] = 1;
                        } else {
                            $result['error'] = 'Reserva inexistentes';
                        }
                        break;
                        case 'readEstado':
                            if (!$reserva->setEstado($_POST['estado'])) {
                                $result['error'] = $reserva->getDataError();
                            } elseif ($result['dataset'] = $reserva->readEstado()) {
                                $result['status'] = 1;
                            } else {
                                $result['error'] = 'Inaxistentes';
                            }
                            break;
                        case 'readDetalles2':
                            if (!$reserva->setId($_POST['idDetalleReserva'])) {
                                $result['error'] = $reserva->getDataError();
                            } elseif ($result['dataset'] = $reserva->readDetalles2()) {
                                $result['status'] = 1;
                            } else {
                                $result['error'] = 'Reserva inexistentes';
                            }
                            break;
            case 'UpdateORW':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$reserva->setEstado($_POST['estado'])
                ) {
                    $result['error'] = $reserva->getDataError();
                } elseif ($reserva->UpdateORW()) {
                    $result['status'] = 1;
                    $result['message'] = 'Estado de la reserva modificada correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la reserva';
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


// Se imprime el resultado en formato JSON y se retorna al controlador.