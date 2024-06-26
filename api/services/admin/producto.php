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
            // Caso para leer un detalle específico
            case 'readOneDetail':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);

                // Verificar si se puede establecer el ID del detalle del producto
                if (!$producto->setIdDetalle($_POST['idDetalleProducto'])) {
                    // Si hay un error al establecer el ID del detalle, se asigna el mensaje de error
                    $result['error'] = $producto->getDataError();
                }
                // Intentar leer el detalle específico
                elseif ($result['dataset'] = $producto->readOneDetail()) {
                    // Si se encuentra el detalle, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Detalle encontrado';
                } else {
                    // Si no se encuentra el detalle, se asigna el mensaje de error
                    $result['error'] = 'Detalle inexistente';
                }
                break;
            // Caso para buscar filas
            case 'searchRows':
                // Validar el término de búsqueda
                if (!Validator::validateSearch($_POST['search'])) {
                    // Si la validación falla, se asigna el mensaje de error
                    $result['error'] = Validator::getSearchError();
                }
                // Intentar buscar filas que coincidan con el término de búsqueda
                elseif ($result['dataset'] = $producto->searchRows()) {
                    // Si se encuentran coincidencias, se asigna el estado y el mensaje de éxito con el número de coincidencias
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                } else {
                    // Si no se encuentran coincidencias, se asigna el mensaje de error
                    $result['error'] = 'No hay coincidencias';
                }
                break;
            // Caso para crear una nueva fila
            case 'createRow':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);
                // Verificar si se pueden establecer todos los datos del producto
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
                    // Si hay un error al establecer alguno de los datos, se asigna el mensaje de error
                    $result['error'] = $producto->getDataError();
                }
                // Intentar crear la nueva fila en la base de datos
                elseif ($producto->createRow()) {
                    // Si la creación es exitosa, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Producto creado correctamente';
                    // Guardar el archivo de imagen
                    $result['fileStatus'] = Validator::saveFile($_FILES['imagen'], $producto::RUTA_IMAGEN);
                } else {
                    // Si hay un problema al crear el producto, se asigna el mensaje de error
                    $result['error'] = 'Ocurrió un problema al crear el producto';
                }
                break;
            // Caso para leer todos los registros
            case 'readAll':
                // Intentar leer todos los registros de productos
                if ($result['dataset'] = $producto->readAll()) {
                    // Si la lectura es exitosa, se asigna el estado y el mensaje con la cantidad de registros encontrados
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    // Si no se encuentran registros, se asigna el mensaje de error
                    $result['error'] = 'No existen productos registrados';
                }
                break;
            // Caso para leer un registro específico
            case 'readOne':
                // Verificar si se puede establecer el ID del producto
                if (!$producto->setId($_POST['idProducto'])) {
                    // Si hay un error al establecer el ID del producto, se asigna el mensaje de error
                    $result['error'] = $producto->getDataError();
                }
                // Intentar leer el producto específico
                elseif ($result['dataset'] = $producto->readOne()) {
                    // Si la lectura es exitosa, se asigna el estado
                    $result['status'] = 1;
                } else {
                    // Si no se encuentra el producto, se asigna el mensaje de error
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
                    !$producto->setId($_POST['idProductoDetalle']) or
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
            case 'deleteRow': // Acción para eliminar una fila por ID.
                // Verificar y establecer el ID del género a eliminar.
                if (
                    !$producto->setId($_POST['idProducto'])
                ) {
                    $result['error'] = $producto->getDataError(); // Mensaje de error si el ID es inválido.
                } elseif ($producto->deleteRow()) { // Intentar eliminar la fila.
                    $result['status'] = 1; // Indicar que la operación fue exitosa.
                    $result['message'] = 'Producto eliminado correctamente'; // Mensaje de éxito.
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el producto'; // Mensaje de error si ocurre un problema.
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

            case 'deleteDetail': // Acción para eliminar una fila por ID.
                // Verificar y establecer el ID del género a eliminar.
                if (!$producto->setIdDetalle($_POST['idProductoDetalle'])) {
                    $result['error'] = $producto->getDataError(); // Mensaje de error si el ID es inválido.
                } elseif ($producto->deleteDetail()) { // Intentar eliminar la fila.
                    $result['status'] = 1; // Indicar que la operación fue exitosa.
                    $result['message'] = 'Detalle de producto eliminado correctamente'; // Mensaje de éxito.
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el detalle'; // Mensaje de error si ocurre un problema.
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