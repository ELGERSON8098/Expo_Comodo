<?php
// Se incluye la clase del modelo.
require_once ('../../models/data/producto_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $producto = new productoData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null, 'producto' => 0, 'detalle' => 0);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readOneDetail':
                // Validar y obtener los datos.
                $_POST = Validator::validateForm($_POST);
                if (!$producto->setIdDetalle($_POST['idDetalleProducto'])) {
                    $result['error'] = $producto->getDataError();
                } elseif ($result['dataset'] = $producto->readOneDetail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Detalle encontrado';
                } else {
                    $result['error'] = 'Detalle inexistente';
                }
                break;
                case 'searchRows':
                    if (!Validator::validateSearch($_POST['search'])) {
                        $result['error'] = Validator::getSearchError();
                    } elseif ($result['dataset'] = $producto->searchRows()) {
                        $result['status'] = 1;
                        $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                    } else {
                        $result['error'] = 'No hay coincidencias';
                    }
                    break;
                case 'searchRows':
                    if (!Validator::validateSearch($_POST['search'])) {
                        $result['error'] = Validator::getSearchError();
                    } elseif ($result['dataset'] = $producto->searchRows()) {
                        $result['status'] = 1;
                        $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                    } else {
                        $result['error'] = 'No hay coincidencias';
                    }
                    break;
            case 'createRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$producto->setNombre($_POST['nombreProducto']) or
                    !$producto->setCodigo_Interno($_POST['codigoInterno']) or
                    !$producto->setReferenciaProveedor($_POST['referenciaPro']) or
                    !$producto->setPrecio($_POST['precioProducto']) or
                    !$producto->setMarca($_POST['nombreMarca']) or
                    !$producto->setGenero($_POST['nombre_genero']) or
                    !$producto->setCategoria($_POST['nombreCategoria']) or
                    !$producto->setMaterial($_POST['nombreMaterial']) or
                    !$producto->setDescuento($_POST['nombreDescuento']) or
                    !$producto->setImagen($_FILES['imagen'])
                ) {
                    $result['error'] = $producto->getDataError();
                } elseif ($producto->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto creado correctamente';
                    // Se asigna el estado del archivo después de insertar.
                    $result['fileStatus'] = Validator::saveFile($_FILES['imagen'], $producto::RUTA_IMAGEN);
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el producto';
                }
                break;
            case 'readAll':
                if ($result['dataset'] = $producto->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen productos registrados';
                }
                break;
            case 'readOne':
                if (!$producto->setId($_POST['idProducto'])) {
                    $result['error'] = $producto->getDataError();
                } elseif ($result['dataset'] = $producto->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Producto inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$producto->setId($_POST['idProducto']) or
                    !$producto->setFilename() or
                    !$producto->setNombre($_POST['nombreProducto']) or
                    !$producto->setCodigo_Interno($_POST['codigoInterno']) or
                    !$producto->setReferenciaProveedor($_POST['referenciaPro']) or
                    !$producto->setPrecio($_POST['precioProducto']) or
                    !$producto->setMarca($_POST['nombreMarca']) or
                    !$producto->setGenero($_POST['nombre_genero']) or
                    !$producto->setCategoria($_POST['nombreCategoria']) or
                    !$producto->setMaterial($_POST['nombreMaterial']) or
                    !$producto->setDescuento($_POST['nombreDescuento']) or
                    !$producto->setImagen($_FILES['imagen'], $producto->getFilename())
                ) {
                    $result['error'] = $producto->getDataError();
                } elseif ($producto->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto modificado correctamente';
                    // Se asigna el estado del archivo después de actualizar.
                    $result['fileStatus'] = Validator::changeFile($_FILES['imagen'], $producto::RUTA_IMAGEN, $producto->getFilename());
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar el producto';
                }
                break;

            case 'createDetail':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$producto->setTalla($_POST['nombreTalla']) or
                    !$producto->setExistencias($_POST['existencias']) or
                    !$producto->setColor($_POST['nombreColor']) or
                    !$producto->setDescripcion($_POST['descripcion'])
                ) {
                    $result['error'] = $producto->getDataError();
                } elseif ($producto->createDetail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Detalle creado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el detalle';
                }
                break;
            case 'updateDetail':
                // Validar y obtener los datos.
                $_POST = Validator::validateForm($_POST);
                if (
                    !$producto->setIdDetalle($_POST['idDetalle']) or
                    !$producto->setTalla($_POST['nombreTalla']) or
                    !$producto->setExistencias($_POST['existencias']) or
                    !$producto->setColor($_POST['nombreColor']) or
                    !$producto->setDescripcion($_POST['descripcion'])
                ) {
                    $result['error'] = $producto->getDataError();
                } elseif ($producto->updateDetail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Detalle actualizado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al actualizar el detalle';
                }
                break;
            case 'readDetails':
                if (!$producto->setId($_POST['idProducto'])) {
                    $result['error'] = $producto->getDataError();
                } elseif ($result['dataset'] = $producto->readDetails()) { // Supongamos que tienes una función readDetails() en productoData
                    $result['status'] = 1;
                    $result['message'] = 'Detalles encontrados';
                } else {
                    $result['error'] = 'No hay detalles para este producto';
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