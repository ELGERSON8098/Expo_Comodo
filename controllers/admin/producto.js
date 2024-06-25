// Constantes para completar las rutas de la API.
const MATERIAL_API = 'services/admin/materiales.php';
const MARCA_API = 'services/admin/marca.php';
const GENERO_API = 'services/admin/genero.php';
const DESCUENTO_API = 'services/admin/descuento.php';
const CATEGORIA_API = 'services/admin/categoria.php';
const PRODUCTO_API = 'services/admin/producto.php';
// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');
// Constantes para establecer el contenido de la tabla.
const TABLE_BODY = document.getElementById('tableBody'),
    ROWS_FOUND = document.getElementById('rowsFound');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal'),
    MODAL_TITLE = document.getElementById('modalTitle');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_PRODUCTO = document.getElementById('idProducto'),
    ID_PRODUCTO_DETALLE = document.getElementById('idProductoDetalle'),
    ID_DETALLE = document.getElementById('idDetalle'),
    EXISTENCIAS = document.getElementById('existencias'),
    DESCRIPCION = document.getElementById('descripcion'),
    NOMBRE_PRODUCTO = document.getElementById('nombreProducto'),
    CODIGO_INTERNO = document.getElementById('codigoInterno'),
    REFERENCIA_PRO = document.getElementById('referenciaPro'),
    PRECIO = document.getElementById('precioProducto');
IMAGEN_PRODUCTO = document.getElementById('imagen');


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



const COLOR_API = 'services/admin/color.php';
const TALLA_API = 'services/admin/talla.php';
// Constantes para establecer el contenido de la tabla de detalles.
const DETAILS_TABLE_BODY = document.getElementById('detailsTableBody'),
    ADD_DETAIL_BUTTON = document.getElementById('addDetailButton'),
    SAVE_DETAIL_FORM = document.getElementById('saveDetailForm'),
    SAVE_DETAIL_MODAL = new bootstrap.Modal('#saveDetailModal'),
    MODAL_DETAIL_TITLE = document.getElementById('modalDetailTitle');

// Método del evento para cuando se envía el formulario de guardar detalles.
SAVE_DETAIL_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();

    // Se verifica la acción a realizar.
    const action = SAVE_DETAIL_FORM.idDetalle.value ? 'updateDetail' : 'createDetail';

    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_DETAIL_FORM);

    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(PRODUCTO_API, action, FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        SAVE_DETAIL_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
    } else {
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
    SAVE_DETAIL_FORM.reset();
    SAVE_DETAIL_FORM.classList.remove('d-none');
    MODAL_DETAIL_TITLE.textContent = 'Agregar detalle de producto';
    ID_PRODUCTO_DETALLE.value = idProducto;
    SAVE_DETAIL_MODAL.show();

    // Llenar los selects necesarios
    fillSelect(TALLA_API, 'readAll', 'nombreTalla');
    fillSelect(COLOR_API, 'readAll', 'nombreColor');

    // Obtener y mostrar los detalles existentes del producto
    fillDetailsTable(idProducto);
}

// Función asíncrona para llenar la tabla con los detalles disponibles.
const fillDetailsTable = async (idProducto) => {
    // Se inicializa el contenido de la tabla.
    DETAILS_TABLE_BODY.innerHTML = '';

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
        sweetAlert(4, DATA.error, true);
    }
}

/*
*   Función asíncrona para preparar el formulario al momento de actualizar un detalle.
*   Parámetros: idDetalle (identificador del detalle seleccionado).
*   Retorno: ninguno.
*/
const openUpdateDetail = async (idDetalleProducto) => {
    MODAL_DETAIL_TITLE.textContent = 'Actualizar detalle de producto';
    const formData = new FormData();
    formData.append('idDetalleProducto', idDetalleProducto); // Cambiado a idProducto
    const DATA = await fetchData(PRODUCTO_API, 'readOneDetail', formData);
    if (DATA.status) {
        const ROW = DATA.dataset;
        ID_DETALLE.value = ROW.id_detalle_producto;
        EXISTENCIAS.value = ROW.existencias;
        DESCRIPCION.value = ROW.descripcion;
        fillSelect(TALLA_API, 'readAll', 'nombreTalla', parseInt(ROW.id_talla));
        fillSelect(COLOR_API, 'readAll', 'nombreColor', parseInt(ROW.id_color));
        SAVE_DETAIL_MODAL.show();
    } else {
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


