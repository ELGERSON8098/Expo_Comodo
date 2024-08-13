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
            // Caso para actualizar una fila existente
            case 'updateRow':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);

                // Verificar si se pueden establecer todos los datos del producto para actualizar
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
                    // Si hay un error al establecer alguno de los datos, se asigna el mensaje de error
                    $result['error'] = $producto->getDataError();
                }
                // Intentar actualizar la fila en la base de datos
                elseif ($producto->updateRow()) {
                    // Si la actualización es exitosa, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Producto modificado correctamente';
                    // Cambiar el archivo de imagen si se ha actualizado
                    $result['fileStatus'] = Validator::changeFile($_FILES['imagen'], $producto::RUTA_IMAGEN, $producto->getFilename());
                } else {
                    // Si hay un problema al modificar el producto, se asigna el mensaje de error
                    $result['error'] = 'Ocurrió un problema al modificar el producto';
                }
                break;

            // Caso para crear un nuevo detalle de producto
            case 'createDetail':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);

                // Verificar si se pueden establecer todos los datos del detalle del producto para crearlo
                if (
                    !$producto->setId($_POST['idProductoDetalle']) or
                    !$producto->setTalla($_POST['nombreTalla']) or
                    !$producto->setExistencias($_POST['existencias']) or
                    !$producto->setColor($_POST['nombreColor']) or
                    !$producto->setDescripcion($_POST['descripcion'])
                ) {
                    // Si hay un error al establecer alguno de los datos, se asigna el mensaje de error
                    $result['error'] = $producto->getDataError();
                }
                // Intentar crear el detalle del producto en la base de datos
                elseif ($producto->createDetail()) {
                    // Si la creación es exitosa, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Detalle creado correctamente';
                } else {
                    // Si hay un problema al crear el detalle del producto, se asigna el mensaje de error
                    $result['error'] = 'Ocurrió un problema al crear el detalle';
                }
                break;
            // Caso para actualizar un detalle de producto existente
            case 'updateDetail':
                // Validar y obtener los datos del formulario
                $_POST = Validator::validateForm($_POST);

                // Verificar si se pueden establecer todos los datos del detalle del producto para actualizarlo
                if (
                    !$producto->setIdDetalle($_POST['idDetalle']) or
                    !$producto->setTalla($_POST['nombreTalla']) or
                    !$producto->setExistencias($_POST['existencias']) or
                    !$producto->setColor($_POST['nombreColor']) or
                    !$producto->setDescripcion($_POST['descripcion'])
                ) {
                    // Si hay un error al establecer alguno de los datos, se asigna el mensaje de error
                    $result['error'] = $producto->getDataError();
                }
                // Intentar actualizar el detalle del producto en la base de datos
                elseif ($producto->updateDetail()) {
                    // Si la actualización es exitosa, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Detalle actualizado correctamente';
                } else {
                    // Si hay un problema al actualizar el detalle del producto, se asigna el mensaje de error
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
            case 'cantidadProductosCategoria':
                if ($result['dataset'] = $producto->cantidadProductosCategoria()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay datos disponibles';
                }
                break;
            case 'porcentajeProductosCategoria':
                if ($result['dataset'] = $producto->porcentajeProductosCategoria()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay datos disponibles';
                }
                break;
            case 'descuentosMasUtilizados':
                if ($result['dataset'] = $producto->descuentosMasUtilizados()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay datos disponibles';
                }
                break;
            case 'marcaMasComprada':
                if ($result['dataset'] = $producto->marcaMasComprada()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay datos disponibles';
                }
                break;
            case 'productosMasVendidosPorCategoria':
                if ($result['dataset'] = $producto->productosMasVendidosPorCategoria()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'No hay datos disponibles';
                }
                break;
            // Caso para leer los detalles de un producto específico
            case 'readDetails':
                // Verificar si se puede establecer el ID del producto
                if (!$producto->setId($_POST['idProducto'])) {
                    // Si hay un error al establecer el ID del producto, se asigna el mensaje de error
                    $result['error'] = $producto->getDataError();
                }
                // Intentar leer los detalles del producto
                elseif ($result['dataset'] = $producto->readDetails()) {
                    // Si se encuentran detalles, se asigna el estado y el mensaje de éxito
                    $result['status'] = 1;
                    $result['message'] = 'Detalles encontrados';
                } else {
                    // Si no se encuentran detalles para el producto, se asigna el mensaje de error
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
            case 'cantidadProductosGenero':
                if ($result['dataset'] = $producto->cantidadProductosGenero()) {
                    $result['status'] = 1;
                    $result['message'] = 'Datos obtenidos correctamente';
                } else {
                    $result['error'] = 'No se pudieron obtener los datos';
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
