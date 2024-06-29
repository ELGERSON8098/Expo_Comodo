// Constantes para completar las rutas de la API.
const PEDIDO_API = 'services/admin/reserva.php',
    DETALLEPEDIDO_API = 'services/admin/detallepedidos.php';

// Constantes para establecer los elementos del formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm'),
    SEARCHSUB_FORM = document.getElementById('searchsubForm');

// Constantes para establecer el contenido de la tabla.
const SUBTABLE_HEAD = document.getElementById('subheaderT'),
    SUBTABLE = document.getElementById('subtable'),
    SUBTABLE_BODY = document.getElementById('subtableBody'),
    TABLE_BODY = document.getElementById('tableBody'),
    ROWS_FOUND = document.getElementById('rowsFound'),
    SUBROWS_FOUND = document.getElementById('subrowsFound');

// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal'),
    MODAL_TITLE = document.getElementById('modalTitle'),
    ESTADO_DEL_PEDIDO = document.getElementById('estado'),
    SUBMODAL_TITLE = document.getElementById('submodalTitle');

// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    INPUTSEARCH = document.getElementById('inputsearch'),
    ESTADO_PEDIDO = document.getElementById('estadoPedido');

const SAVE_MODALSS = new bootstrap.Modal('#saveModalSS'),
    MODAL_TITLESS = document.getElementById('modalTitleSS');

const SAVE_FORMSS = document.getElementById('saveFormSS'),
    ID_ESTADOSA = document.getElementById('idEstadosa');

// Constantes para establecer los elementos del componente Modal.
const SAVE_TREMODAL = new bootstrap.Modal('#savetreModal'),
    TREMODAL_TITLE = document.getElementById('tremodalTitle');

// Constantes para establecer los elementos del formulario de guardar.
const SAVE_TREFORM = document.getElementById('savetreForm'),
    USUARIO_RESERVA = document.getElementById('user'),
    DUI_RESERVA = document.getElementById('dui'),
    TELEFONO_RESERVA = document.getElementById('telefono'),
    PRODUCTO_RESERVA = document.getElementById('producto'),
    INTERNO = document.getElementById('interno'),
    PROVEEDOR_RESERVA = document.getElementById('proveedor'),
    MARCA_RESERVA = document.getElementById('Marca'),
    GENERO_RESERVA = document.getElementById('Genero'),
    DESCUENTO_RESERVA = document.getElementById('precio_descuento'),
    DIRECCION_RESERVA = document.getElementById('DirecC'),
    COLOR = document.getElementById('color'),
    TALLA = document.getElementById('talla'),
    PRECIO_UNI = document.getElementById('precio_unitario'),
    PRECIO_DESCUENTOS = document.getElementById('descuento'),
    ID_DETALLE = document.getElementById('idDetalleReserva'),
    CANTIDAD = document.getElementById('cantidad');

let ESTADO_BUSQUEDA = "Pendiente",
    TIMEOUT_ID;

document.querySelector('title').textContent = 'Reservas';

// Variable global para mantener la referencia del mapa y marcador
let map;
let marker;

// Evento para inicializar el mapa cuando se muestra el modal
document.getElementById('savetreModal').addEventListener('shown.bs.modal', function () {
    if (map) {
        map.invalidateSize(); // Redimensionar el mapa si ya está inicializado
    } else {
        initializeMap();
    }
});

// Evento para limpiar el marcador cuando se oculta el modal
document.getElementById('savetreModal').addEventListener('hidden.bs.modal', function () {
    if (marker) {
        map.removeLayer(marker); // Remover el marcador del mapa
        marker = null; // Resetear el marcador
    }
});

// Función para inicializar el mapa
function initializeMap() {
    map = L.map('map').setView([13.6929, -89.2182], 13); // Coordenadas de San Salvador

    // Añadir la capa del mapa
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
}

// Función para actualizar la ubicación en el mapa con la dirección proporcionada
function updateMap(address) {
    if (!map) {
        // Inicializar el mapa si no existe
        initializeMap();
    }

    // Utilizar Nominatim para geocodificar la dirección
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${address}`)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                const { lat, lon } = data[0];
                // Si el marcador ya existe, actualizar su ubicación
                if (marker) {
                    marker.setLatLng([lat, lon]);
                } else {
                    marker = L.marker([lat, lon]).addTo(map);
                }
                map.setView([lat, lon], 15); // Centrar el mapa en la ubicación encontrada
            } else {
                sweetAlert(2, 'No se pudo encontrar la ubicación', false);
            }
        })
        .catch(error => {
            sweetAlert(2, 'Error al buscar la ubicación', false);
        });
}

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Reservas';
    // Llamada a la función para llenar la tabla con los registros existentes.
    fillTable();
});

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
    //(ID_PEDIDO.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(PEDIDO_API, 'UpdateORW', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        SAVE_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
        ID_PEDIDO.value = null;
        // Se carga nuevamente la tabla para visualizar los cambios.
        fillTable();
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

//Función asíncrona para llenar la tabla con los registros disponibles.
const fillTable = async (form = null) => {
    // Se inicializa el contenido de la tabla.
    ROWS_FOUND.textContent = '';
    TABLE_BODY.innerHTML = '';
    // Se verifica la acción a realizar.
    (form) ? action = 'searchRows' : action = 'readAll';
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(PEDIDO_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se establece un icono para el estado del PEDIDO.
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.usuario}</td>
                    <td>${row.fecha_reserva}</td>                 
                    <td>${row.estado_reserva}</i></td>
                    <td>
                        <button type="button" class="btn btn-success" onclick="openUpdate(${row.id_reserva})">
                            <i class="bi bi-info-circle"></i>
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


//Función para preparar el formulario al momento de insertar un registro.
const openCreate = () => {
    // Se muestra la caja de diálogo con su título.
    SAVE_MODAL.show();
    IMAGEN_PRE.innerHTML = '';
    MODAL_TITLE.textContent = 'Crear PEDIDO';
    SUBTABLE.hidden = true;
    // Se prepara el formulario.
    SAVE_FORM.reset();
    //EXISTENCIAS_PEDIDO.disabled = false;
    fillSelect(MARCA_API, 'readAll', 'marcaModelo');
}


//Función asíncrona para preparar el formulario al momento de actualizar un registro.
const openUpdate = async (id) => {
    try {
        console.log('Abriendo la actualización para el ID:', id);

        // Mostrar modal y configurar título
        SAVE_MODAL.show();
        SUBTABLE.hidden = false;
        MODAL_TITLE.textContent = 'Información del pedido';
        
        // Reiniciar formulario
        SAVE_FORM.reset();
        
        // Llenar los detalles del pedido de manera asincrónica
        await fillSubTable(id);
        
        // Petición para obtener el estado actual del pedido
        const FORM = new FormData();
        FORM.append('idReserva', id); // Asegúrate de ajustar el nombre del campo según tu PHP
        const DATA = await fetchData(PEDIDO_API, 'readEstado', FORM);
        
        // Verificar si se obtuvo el estado correctamente
        if (DATA.status && DATA.dataset.estado_reserva) {
            const estadoReserva = DATA.dataset.estado_reserva;
            console.log('Estado actual del pedido:', estadoReserva);

            // Iterar sobre las opciones del selector de estado
            for (let i = 0; i < ESTADO_DEL_PEDIDO.options.length; i++) {
                if (ESTADO_DEL_PEDIDO.options[i].value === estadoReserva) {
                    ESTADO_DEL_PEDIDO.selectedIndex = i;
                    console.log('Estado seleccionado:', ESTADO_DEL_PEDIDO.options[i].value);
                    break;
                }
            }
        } else {
            console.error('No se pudo obtener el estado del pedido desde DATA:', DATA);
        }
    } catch (error) {
        console.error('Error al abrir la actualización:', error);
    }
}

//Función asíncrona para llenar la tabla con los registros disponibles.
const fillSubTable = async (id) => {
    SUBROWS_FOUND.textContent = '';
    SUBTABLE_BODY.innerHTML = '';
    const FORM = new FormData();
    FORM.append('idReservas', id);
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(PEDIDO_API, 'readDetalles', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            SUBTABLE_BODY.innerHTML += `
                <tr>
                <td>${row.nombre_producto}</td>
                <td><img src="${SERVER_URL}images/productos/${row.imagen}" height="50"></td>
                <td>${row.fecha_reserva}</td>
                <td><button type="button" class="btn btn-success" onclick="opensubUpdate(${row.id_detalle_reserva})">
                            <i class="bi bi-info-circle"></i>
                        </button></td>
                </tr>
            `;
        });
        // Se muestra un mensaje de acuerdo con el resultado.
        SUBROWS_FOUND.textContent = DATA.message;
    } else {
        sweetAlert(4, DATA.error, true);
    }
}

//ABRIR EL MODAL DESDE EL HTML
const subClose = () => {
    SAVE_MODAL.show();
}

const opensubCreate = () => {
    SAVE_MODAL.hide();
    SAVE_TREMODAL.show();
    SELECTALLA.hidden = false;
    //SAVE_MODAL.hidden = false;
    TREMODAL_TITLE.textContent = 'Agregar talla';
    // Se prepara el formulario.
    SAVE_TREFORM.reset();
    //EXISTENCIAS_PEDIDO.disabled = false;
    fillSelect(TALLA_API, 'readAll', 'tallaModeloTalla');
}

//Función asíncrona para preparar el formulario al momento de actualizar un registro.
const opensubUpdate = async (id) => {
    try {
        // Se define un objeto con los datos del registro seleccionado.
        SAVE_MODAL.hide();
        const FORM = new FormData();
        FORM.append('idDetalleReserva', id);
        // Petición para obtener los datos del registro solicitado.
        const DATA = await fetchData(PEDIDO_API, 'readDetalles2', FORM);
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se muestra la caja de diálogo con su título.
            SAVE_TREMODAL.show();
            TREMODAL_TITLE.textContent = 'Detalle';
            // Se prepara el formulario.
            SAVE_TREFORM.reset();
            // Se inicializan los campos con los datos.
            const ROW = DATA.dataset;

            ID_DETALLE.value = ROW.id_detalle_reserva;
            COLOR.value = ROW.color;
            CANTIDAD.value = ROW.cantidad;
            TALLA.value = ROW.nombre_talla;
            PRECIO_UNI.value = ROW.precio_unitario;
            USUARIO_RESERVA.value = ROW.nombre_usuario;
            DUI_RESERVA.value = ROW.dui_usuario;
            TELEFONO_RESERVA.value = ROW.telefono_usuario;
            PRODUCTO_RESERVA.value = ROW.nombre_producto;
            INTERNO.value = ROW.codigo_interno;
            PROVEEDOR_RESERVA.value = ROW.referencia_proveedor;
            MARCA_RESERVA.value = ROW.nombre_marca;
            GENERO_RESERVA.value = ROW.nombre_genero;
            PRECIO_DESCUENTOS.value = ROW.valor_descuento;
            DESCUENTO_RESERVA.value = ROW.precio_con_descuento;
            DIRECCION_RESERVA.value = ROW.direccion_usuario;

            // Actualizar el mapa con la dirección del usuario
            updateMap(ROW.direccion_usuario);

        } else {
            sweetAlert(2, DATA.error, false);
        }
    } catch (error) {
        console.error('Error al abrir la actualización:', error);
    }
}

//Función asíncrona para eliminar un registro.
const opensubDelete = async (id) => {
    // Llamada a la función para mostrar un mensaje de confirmación, capturando la respuesta en una constante.
    const RESPONSE = await confirmAction('¿Desea inactivar el PEDIDO de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idModelo', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(PEDIDO_API, 'deleteRow', FORM);
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se muestra un mensaje de éxito.
            await sweetAlert(1, DATA.message, true);
            // Se carga nuevamente la tabla para visualizar los cambios.
            fillTable(ESTADO_BUSQUEDA);
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
}

/*
*   Función para abrir un reporte automático de PEDIDOs por categoría.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const openReport = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/admin/PEDIDOs.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}
