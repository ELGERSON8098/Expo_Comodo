// Constantes para completar las rutas de la API.
const RESERVA_API = 'services/admin/reserva.php';
// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');
// Constantes para establecer el contenido de la tabla.
const TABLE_BODY = document.getElementById('tableBody'),
    ROWS_FOUND = document.getElementById('rowsFound');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal'),
MODAL_TITLE = document.getElementById('modalTitle');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODALS = new bootstrap.Modal('#saveModalS'),
MODAL_TITLES = document.getElementById('modalTitleS');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORMS = document.getElementById('saveForms'),
ID_ESTADO = document.getElementById('idReservas');
COMBOC_RESERVA = document.getElementById('EstadoP');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_USUARIO = document.getElementById('idReserva'),
    NOMBRE_USUARIO = document.getElementById('NomClien'),
    TELEFONO_RESERVA = document.getElementById('TEL'),
    FECHA_RESERVA = document.getElementById('FechReserva'),
    PROD_RESERVA = document.getElementById('Produc'),
    MATERIAL_RESERVA = document.getElementById('Materi'),
    COLOR_RESERVA = document.getElementById('COL'),
    TALLA_RESERVA = document.getElementById('Tallas'),
    MARCA_RESERVA = document.getElementById('MARCA'),
    CANTIDAD_RESERVA = document.getElementById('Cant'),
    PRECIO_RESERVA = document.getElementById('Precio'),
    DESCUENTO_RESERVA = document.getElementById('Descu'),
    DESCUENTO_RESERVAS = document.getElementById('Descuentos');

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Gestionar Reservas';
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
    (form) ? action = 'searchRows' : action = 'readAll';
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(RESERVA_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros fila por fila.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre}</td>
                    <td>${row.dui_cliente}</td>
                    <td>${row.fecha_reserva}</td>
                    <td>${row.departamento}</td>
                    <td>${row.municipio}</td>
                    <td>${row.distrito}</td>
                    <td>${row.estado_reserva}</td>
                
                <td>
                    <div class="btn-group" role="group" aria-label="Acciones">
                        <button type="button" class="btn  btn-success rounded me-2 mb-2 mb-sm-2" onclick="openUpdateS(${row.id_reserva})">
                            <i class="bi bi-bag-check"></i>
                        </button>
                        <button type="button" class="btn btn-warning rounded me-2 mb-2 mb-sm-2" onclick="openUpdate(${row.id_reserva})">
                            <i class="bi bi-eye-fill"></i>
                        </button>
                        <button type="button" class="btn btn-danger rounded me-2 mb-2 mb-sm-2" onclick="openDelete(${row.id_reserva})">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </div>
                </td>
                

                </tr>`;
        });
        // Se muestra un mensaje de acuerdo con el resultado.
        ROWS_FOUND.textContent = DATA.message;
    } else {
        sweetAlert(4, DATA.error, true);
    }
}


/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    console.log("id_reserva" + id);
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idReserva', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(RESERVA_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Datos de la reserva';
        // Se inicializan los campos con los datos.
        SAVE_FORM.reset();
        ID_USUARIO.disabled = false;
        const ROW = DATA.dataset;
        NOMBRE_USUARIO.value = ROW.nombre_usuario;
        TELEFONO_RESERVA.value = ROW.telefono;
        FECHA_RESERVA.value = ROW.fecha_reserva;
        PROD_RESERVA.value = ROW.nombre_producto;
        MATERIAL_RESERVA.value = ROW.material;
        COLOR_RESERVA.value = ROW.color;
        TALLA_RESERVA.value = ROW.nombre_talla;
        MARCA_RESERVA.value = ROW.marca;
        CANTIDAD_RESERVA.value = ROW.cantidad;
        PRECIO_RESERVA.value = ROW.precio_unitario;
        DESCUENTO_RESERVA.value = ROW.valor_descuento;
        DESCUENTO_RESERVAS.value = ROW.precio_con_descuento;

    } else {
        sweetAlert(2, DATA.error, false);
    }
}

const openUpdateS = async (id) => {
    console.log("id_reserva" + id);
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idReservas', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(RESERVA_API, 'readOneS', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODALS.show();
        MODAL_TITLES.textContent = 'Estado del pedido';
        // Se inicializan los campos con los datos.
        SAVE_FORM.reset();
        ID_ESTADO.disabled = false;
        const ROW = DATA.dataset;
        fillSelect(RESERVA_API, 'readAlls', 'EstadoP', ROW.estado_reserva);
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
    const RESPONSE = await confirmAction('¿Desea eliminar la reserva de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idReserva', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(RESERVA_API, 'deleteRow', FORM);
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