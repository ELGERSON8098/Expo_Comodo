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
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
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
                    $result['error'] = $producto->getDataError(); // Obtener mensaje de error si la validación falla.
                } elseif ($producto->createRow()) { // Intentar crear un nuevo libro.
                    $result['status'] = 1; // Indicar que la operación fue exitosa.
                    $result['message'] = 'Producto creado con éxito';
                    $result['fileStatus'] = Validator::saveFile($_FILES['imagen'], $producto::RUTA_IMAGEN);
                } else {
                    $result['error'] = 'Ocurrió un problema al crear el libro'; // Mensaje de error si ocurre un problema.
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
            default: // Caso por defecto para manejar acciones desconocidas.
                $result['error'] = 'Acción no disponible dentro de la sesión'; // Mensaje si la acción no es válida.
        }

        // Capturar cualquier excepción de la base de datos.
        $result['exception'] = Database::getException();

        // Configurar el tipo de contenido para la respuesta y la codificación de caracteres.
        header('Content-type: application/json; charset=utf-8');

        // Convertir el resultado a formato JSON y enviarlo como respuesta.
        print (json_encode($result));
    } else {
        // Si no hay una sesión válida, se devuelve un mensaje de acceso denegado.
        print (json_encode('Acceso denegado'));
    }
} else {
    // Si no se recibe una acción, se devuelve un mensaje de recurso no disponible.
    print (json_encode('Recurso no disponible'));
}