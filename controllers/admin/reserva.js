// Constantes para completar las rutas de la API.
const RESERVA_API = 'services/admin/reserva.php'; // Ruta para la API de materiales


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
    ID_RESERVA = document.getElementById('idReserva'), // Campo de ID de producto
    ID_RESERVA_DETALLE = document.getElementById('idReservaDetalle'), // Campo de ID de detalle de producto
    ID_DETALLE = document.getElementById('idDetalle'), // Campo de ID de detalle
    FECHA = document.getElementById('fecha'); // Campo de imagen del producto
document.querySelector('title').innerText = 'Reservas';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.innerText = 'Gestionar reservas';
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
    (ID_RESERVA.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(RESERVA_API, action, FORM);
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
    ROWS_FOUND.innerText = '';
    TABLE_BODY.innerHTML = '';
    // Se verifica la acción a realizar.
    // searchRows es un metodo para buscar productos.
    (form) ? action = 'searchRows' : action = 'readAll';
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(RESERVA_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.usuario}</td>
                    <td>${row.fecha_reserva}</td>
                    <td>${row.estado_reserva}</td>
                    <td> </td>
                    <td> </td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="openUpdate(${row.id_reserva})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                         <button type="button" class="btn btn-danger" onclick="openCreateDetail(${row.id_reserva})">
                         <i class="bi bi-eye-fill"></i>
                         </button>
                    </td>
                </tr>
            `;
        });
        // Se muestra un mensaje de acuerdo con el resultado.
        ROWS_FOUND.innerText = DATA.message;
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
    MODAL_TITLE.innerText = 'Crear reserva';

}
/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idReserva', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(RESERVA_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.innerText = 'Actualizar estado';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_RESERVA.value = ROW.id_reserva;
        fillSelect(RESERVA_API, 'getEstados', 'estadoPedido', ROW.estado);
    } else {
        sweetAlert(2, DATA.error, false);
    }

}


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
    const DATA = await fetchData(RESERVA_API, action, FORM);

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

const openCreateDetail = async (idProducto) => {
    // Mostrar el formulario de detalles para agregar nuevos
    SAVE_DETAIL_FORM.reset(); // Restablece el formulario a su estado inicial
    SAVE_DETAIL_FORM.classList.remove('d-none'); // Muestra el formulario si estaba oculto // Cambia el título del modal
    ID_RESERVA_DETALLE.value = idProducto; // Asigna el id del producto al campo correspondiente
    SAVE_DETAIL_MODAL.show(); // Muestra el modal

    // Obtener y mostrar los detalles existentes del producto
    fillDetailsTable(idProducto); // Llenar la tabla con los detalles del producto
}

// Función asíncrona para llenar la tabla con los detalles disponibles.
const fillDetailsTable = async (idProducto) => {
    // Se inicializa el contenido de la tabla.
    DETAILS_TABLE_BODY.innerHTML = '';

    // Se crea un FormData y se añade el id del producto.
    const FORM = new FormData();
    FORM.append('idReserva', idProducto);

    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(RESERVA_API, 'readDetails', FORM);

    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            DETAILS_TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre_producto}</td>
                    <td>${row.imagen}</td>
                    <td>${row.fecha_reserva}</td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="openViewDetail(${row.id_detalle_reserva})">
                            <i class="bi bi-person-exclamation"></i>
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

const openViewDetail = async (idDetalleReservaVista) => {
    // Preparar FormData con el ID del detalle de reserva seleccionado
    const formData = new FormData();
    formData.append('idDetalleReserva', idDetalleReservaVista);

    try {
        // Realizar petición para obtener detalles del detalle de reserva
        const data = await fetchData(RESERVA_API, 'readOneDetailForForm', formData);

        // Verificar si la respuesta fue exitosa
        if (data.status) {
            // Obtener el primer detalle de la reserva (suponiendo que solo hay uno por ID de reserva)
            const ROW = data.dataset;

            // Mostrar el modal y actualizar su contenido
            AbrirModalVista(); // Función para abrir el modal

            // Actualizar los elementos del modal con los detalles obtenidos
            document.getElementById('detailUser').textContent = ROW.user;
            document.getElementById('detailDUI').textContent = ROW.dui;
            document.getElementById('detailTelefono').textContent = ROW.telefono;
            document.getElementById('detailProducto').textContent = ROW.producto;
            document.getElementById('detailInterno').textContent = ROW.interno;
            document.getElementById('detailProveedor').textContent = ROW.proveedor;
            document.getElementById('detailMarca').textContent = ROW.marca;
            document.getElementById('detailGenero').textContent = ROW.genero;
            document.getElementById('detailColor').textContent = ROW.color;
            document.getElementById('detailCantidad').textContent = ROW.cantidad;
            document.getElementById('detailTalla').textContent = ROW.talla;
            document.getElementById('detailPrecioUnitario').textContent = ROW.precio_unitario;
            document.getElementById('detailDescuento').textContent = ROW.descuento;
            document.getElementById('detailPrecioDescuento').textContent = ROW.precio_descuento;
            document.getElementById('detailDireccionCliente').textContent = ROW.DirecC;
        } else {
            // Mostrar mensaje de error si no se pudieron obtener los detalles
            sweetAlert(4, data.error, true);
        }
    } catch (error) {
        // Mostrar mensaje de error si ocurre un error en la petición
        console.error('Error al obtener detalles de la reserva:', error);
        sweetAlert(4, 'Error al obtener detalles de la reserva. Inténtelo de nuevo más tarde.', true);
    }
};
