// Constantes para completar las rutas de la API.
const MATERIAL_API = 'services/admin/materiales.php'; // Ruta para la API de materiales
const MARCA_API = 'services/admin/marca.php'; // Ruta para la API de marcas
const GENERO_API = 'services/admin/genero.php'; // Ruta para la API de géneros
const DESCUENTO_API = 'services/admin/descuento.php'; // Ruta para la API de descuentos
const CATEGORIA_API = 'services/admin/categoria.php'; // Ruta para la API de categorías
const PRODUCTO_API = 'services/admin/producto.php'; // Ruta para la API de productos

// Constante para establecer el formulario de búsqueda.
const SEARCH_FORM = document.getElementById('searchForm'); // Formulario de búsqueda

// Constantes para establecer el contenido de la tabla.
const TABLE_BODY = document.getElementById('tableBody'), // Cuerpo de la tabla
    ROWS_FOUND = document.getElementById('rowsFound'); // Contador de filas encontradas

// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal'), // Modal de guardar
    MODAL_TITLE = document.getElementById('modalTitle'); // Título del modal

// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'), // Formulario de guardar
    ID_PRODUCTO = document.getElementById('idProducto'), // Campo de ID de producto
    ID_PRODUCTO_DETALLE = document.getElementById('idProductoDetalle'), // Campo de ID de detalle de producto
    ID_DETALLE = document.getElementById('idDetalle'), // Campo de ID de detalle
    EXISTENCIAS = document.getElementById('existencias'), // Campo de existencias
    DESCRIPCION = document.getElementById('descripcion'), // Campo de descripción
    NOMBRE_PRODUCTO = document.getElementById('nombreProducto'), // Campo de nombre de producto
    CODIGO_INTERNO = document.getElementById('codigoInterno'), // Campo de código interno
    REFERENCIA_PRO = document.getElementById('referenciaPro'), // Campo de referencia del producto
    PRECIO = document.getElementById('precioProducto'); // Campo de precio
IMAGEN_PRODUCTO = document.getElementById('imagen'); // Campo de imagen del producto
document.querySelector('title').textContent = 'Productos';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Gestionar productos';
    // Llamada a la función para llenar la tabla con los registros existentes.
    fillTable();
});

// Método del evento para cuando se envía el formulario de buscar.
SEARCH_FORM.addEventListener('submit', (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SEARCH_FORM);
    // Llamada a la función para llenar la tabla con los resultados de la búsqueda.
    fillTable(FORM);
});

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se verifica la acción a realizar.
    (ID_PRODUCTO.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(PRODUCTO_API, action, FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        SAVE_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
        // Se carga nuevamente la tabla para visualizar los cambios.
        fillTable();
    } else {
        sweetAlert(2, DATA.error, false);
    }
});
/*
*   Función asíncrona para llenar la tabla con los registros disponibles.
*   Parámetros: form (objeto opcional con los datos de búsqueda).
*   Retorno: ninguno.
*/
const fillTable = async (form = null) => {
    // Se inicializa el contenido de la tabla.
    ROWS_FOUND.textContent = '';
    TABLE_BODY.innerHTML = '';
    // Se verifica la acción a realizar.
    // searchRows es un metodo para buscar productos.
    (form) ? action = 'searchRows' : action = 'readAll';
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(PRODUCTO_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td><img src="${SERVER_URL}images/productos/${row.imagen}" height="50"></td>
                    <td>${row.nombre_producto}</td>
                    <td>${row.codigo_interno}</td>
                    <td>${row.referencia_proveedor}</td>
                    <td>${row.precio}</td>
                    <td></td>
                    <td></td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="openUpdate(${row.id_producto})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="openDelete(${row.id_producto})">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                         <button type="button" class="btn btn-danger" onclick="openCreateDetail(${row.id_producto})">
                         <i class="bi bi-clipboard-plus-fill"></i>
                         </button>
                    </td>
                </tr>
            `;
        });
        // Se muestra un mensaje de acuerdo con el resultado.
        ROWS_FOUND.textContent = DATA.message;
    } else {
        sweetAlert(4, DATA.error, true);
    }
}
/*
*   Función para preparar el formulario al momento de insertar un registro.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const openCreate = () => {
    // Se muestra la caja de diálogo con su título.
    SAVE_MODAL.show();
    MODAL_TITLE.textContent = 'Crear producto';
    // Se prepara el formulario.
    SAVE_FORM.reset();
    fillSelect(CATEGORIA_API, 'readAll', 'nombreCategoria');
    fillSelect(DESCUENTO_API, 'readAll', 'nombreDescuento');
    fillSelect(MARCA_API, 'readAll', 'nombreMarca');
    fillSelect(GENERO_API, 'readAll', 'nombre_genero');
    fillSelect(MATERIAL_API, 'readAll', 'nombreMaterial');
}
/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idProducto', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(PRODUCTO_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar productos';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_PRODUCTO.value = ROW.id_producto;
        NOMBRE_PRODUCTO.value = ROW.nombre_producto;
        CODIGO_INTERNO.value = ROW.codigo_interno;
        REFERENCIA_PRO.value = ROW.referencia_proveedor;
        PRECIO.value = ROW.precio;
        fillSelect(CATEGORIA_API, 'readAll', 'nombreCategoria', parseInt(ROW.id_categoria));
        fillSelect(DESCUENTO_API, 'readAll', 'nombreDescuento', parseInt(ROW.id_descuento));
        fillSelect(MARCA_API, 'readAll', 'nombreMarca', parseInt(ROW.id_marca));
        fillSelect(GENERO_API, 'readAll', 'nombre_genero', parseInt(ROW.id_genero));
        fillSelect(MATERIAL_API, 'readAll', 'nombreMaterial', parseInt(ROW.id_material));
    } else {
        sweetAlert(2, DATA.error, false);
    }

}
/*
*   Función asíncrona para eliminar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openDelete = async (id) => {
    // Llamada a la función para mostrar un mensaje de confirmación, capturando la respuesta en una constante.
    const RESPONSE = await confirmAction('¿Desea eliminar el producto de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idProducto', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(PRODUCTO_API, 'deleteRow', FORM);
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se muestra un mensaje de éxito.
            await sweetAlert(1, DATA.message, true);
            // Se carga nuevamente la tabla para visualizar los cambios.
            fillTable();
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
}

// Rutas de las APIs para colores y tallas
const COLOR_API = 'services/admin/color.php';
const TALLA_API = 'services/admin/talla.php';

// Constantes para establecer el contenido de la tabla de detalles y elementos del DOM
const DETAILS_TABLE_BODY = document.getElementById('detailsTableBody'),
    ADD_DETAIL_BUTTON = document.getElementById('addDetailButton'),
    SAVE_DETAIL_FORM = document.getElementById('saveDetailForm'),
    SAVE_DETAIL_MODAL = new bootstrap.Modal('#saveDetailModal'),
    MODAL_DETAIL_TITLE = document.getElementById('modalDetailTitle');

// Método del evento para cuando se envía el formulario de guardar detalles
SAVE_DETAIL_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario
    event.preventDefault();

    // Se verifica la acción a realizar (actualizar o crear un detalle)
    const action = SAVE_DETAIL_FORM.idDetalle.value ? 'updateDetail' : 'createDetail';

    // Constante tipo objeto con los datos del formulario
    const FORM = new FormData(SAVE_DETAIL_FORM);

    // Petición para guardar los datos del formulario
    const DATA = await fetchData(PRODUCTO_API, action, FORM);

    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción
    if (DATA.status) {
        // Se cierra la caja de diálogo
        SAVE_DETAIL_MODAL.hide();
        // Se muestra un mensaje de éxito
        sweetAlert(1, DATA.message, true);
    } else {
        // Se muestra un mensaje de error
        sweetAlert(2, DATA.error, false);
    }
});

/*
*   Función asíncrona para llenar la tabla con los detalles disponibles.
*   Parámetros: idProducto (identificador del producto).
*   Retorno: ninguno.
*/
const openCreateDetail = async (idProducto) => {
    // Mostrar el formulario de detalles para agregar nuevos
    SAVE_DETAIL_FORM.reset(); // Restablece el formulario a su estado inicial
    SAVE_DETAIL_FORM.classList.remove('d-none'); // Muestra el formulario si estaba oculto
    MODAL_DETAIL_TITLE.textContent = 'Agregar detalle de producto'; // Cambia el título del modal
    ID_PRODUCTO_DETALLE.value = idProducto; // Asigna el id del producto al campo correspondiente
    SAVE_DETAIL_MODAL.show(); // Muestra el modal

    // Llenar los selects necesarios
    fillSelect(TALLA_API, 'readAll', 'nombreTalla'); // Llenar el select de tallas
    fillSelect(COLOR_API, 'readAll', 'nombreColor'); // Llenar el select de colores

    // Obtener y mostrar los detalles existentes del producto
    fillDetailsTable(idProducto); // Llenar la tabla con los detalles del producto
}

// Función asíncrona para llenar la tabla con los detalles disponibles.
const fillDetailsTable = async (idProducto) => {
    // Se inicializa el contenido de la tabla.
    DETAILS_TABLE_BODY.innerHTML = '';

    // Se crea un FormData y se añade el id del producto.
    const FORM = new FormData();
    FORM.append('idProducto', idProducto);

    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(PRODUCTO_API, 'readDetails', FORM);

    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            DETAILS_TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre_talla}</td>
                    <td>${row.existencias}</td>
                    <td>${row.nombre_color}</td>
                    <td>${row.descripcion}</td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="openUpdateDetail(${row.id_detalle_producto})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="openDeleteDetail(${row.id_detalle_producto})">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
    } else {
        // Muestra una alerta en caso de error.
        sweetAlert(4, DATA.error, true);
    }
};

/*
*   Función asíncrona para preparar el formulario al momento de actualizar un detalle.
*   Parámetros: idDetalle (identificador del detalle seleccionado).
*   Retorno: ninguno.
*/
const openUpdateDetail = async (idDetalleProducto) => {
    // Cambia el título del modal
    MODAL_DETAIL_TITLE.textContent = 'Actualizar detalle de producto';

    // Crea un FormData y añade el id del detalle del producto
    const formData = new FormData();
    formData.append('idDetalleProducto', idDetalleProducto); // Cambiado a idProducto

    // Llama a la API para obtener los datos del detalle del producto
    const DATA = await fetchData(PRODUCTO_API, 'readOneDetail', formData);

    // Si la solicitud fue exitosa
    if (DATA.status) {
        // Obtiene la fila de datos del dataset
        const ROW = DATA.dataset;

        // Llena los campos del formulario con los datos obtenidos
        ID_DETALLE.value = ROW.id_detalle_producto;
        EXISTENCIAS.value = ROW.existencias;
        DESCRIPCION.value = ROW.descripcion;

        // Llena los selects de talla y color con los datos obtenidos y selecciona el valor correspondiente
        fillSelect(TALLA_API, 'readAll', 'nombreTalla', parseInt(ROW.id_talla));
        fillSelect(COLOR_API, 'readAll', 'nombreColor', parseInt(ROW.id_color));

        // Muestra el modal de guardar detalle
        SAVE_DETAIL_MODAL.show();
    } else {
        // Muestra una alerta en caso de error
        sweetAlert(2, DATA.error, false);
    }
};

// Metodo para eliminar el detalle del producto en el modal
const openDeleteDetail = async (idDetalleProducto) => {
    const RESPONSE = await confirmAction('¿Desea eliminar el detalle del producto de forma permanente?');
    if (RESPONSE) {

        // Mostrar el formulario de detalles para agregar nuevos
        SAVE_DETAIL_FORM.reset();
        SAVE_DETAIL_FORM.classList.remove('d-none');

        // Obtener y mostrar los detalles existentes del producto
        fillDetailsTable(idProducto);
        const formData = new FormData();
        formData.append('idProductoDetalle', idDetalleProducto);
        const data = await fetchData(PRODUCTO_API, 'deleteDetail', formData);
        if (data.status) {
            await sweetAlert(1, data.message, true);
            fillTable();
        } else {
            sweetAlert(2, data.error, false);
        }
    }
}

const openReportCategoria = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/admin/productos_categoria.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

