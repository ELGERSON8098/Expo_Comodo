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
        fillSelect(RESERVA_API, 'getEstados', 'estadoPedido', ROW.estado_reserva);
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
                    <td><img src="${SERVER_URL}images/productos/${row.imagen}" height="50"></td>
                    <td>${row.fecha_reserva}</td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="opensubUpdate(${row.id_detalle_reserva})">
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
    CANTIDAD = document.getElementById('cantidad');

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
const opensubUpdate = async (idDetalle) => {
    try {
        // Se define un objeto con los datos del registro seleccionado.
        SAVE_MODAL.hide();
        const FORM = new FormData();
        FORM.append('idDetalleReserva', idDetalle);
        // Petición para obtener los datos del registro solicitado.
        const DATA = await fetchData(RESERVA_API, 'readDetalles2', FORM);
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se muestra la caja de diálogo con su título.
            SAVE_TREMODAL.show();
            TREMODAL_TITLE.textContent = 'Detalle';
            // Se prepara el formulario.
            SAVE_TREFORM.reset();

            COLOR.disabled = true;
            CANTIDAD.disabled = true;
            TALLA.disabled = true;
            PRECIO_UNI.disabled = true;
            USUARIO_RESERVA.disabled = true;
            DUI_RESERVA.disabled = true;
            TELEFONO_RESERVA.disabled = true;
            PRODUCTO_RESERVA.disabled = true;
            INTERNO.disabled = true;
            PROVEEDOR_RESERVA.disabled = true;
            MARCA_RESERVA.disabled = true;
            GENERO_RESERVA.disabled = true;
            PRECIO_DESCUENTOS.disabled = true;
            DESCUENTO_RESERVA.disabled = true;
            DIRECCION_RESERVA.disabled = true;
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

async function graficoVentasPorCategoria() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    if (!fechaInicio || !fechaFin) {
        sweetAlert(3, 'Por favor, seleccione ambas fechas', null);
        return;
    }

    const form = new FormData();
    form.append('fechaInicio', fechaInicio);
    form.append('fechaFin', fechaFin);

    const DATA = await fetchData(RESERVA_API, 'ventasPorCategoriaEnRango', form);

    if (DATA.status) {
        const categorias = DATA.dataset.map(row => row.nombre_categoria);
        const ventas = DATA.dataset.map(row => parseFloat(row.total_ventas));

        const ctx = document.getElementById('chartVentas').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categorias,
                datasets: [{
                    label: 'Total de ventas (US$)',
                    data: ventas,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    } else {
        sweetAlert(2, DATA.error, null);
    }
}

const openReport = (id) => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.

    const PATH = new URL(`${SERVER_URL}reports/admin/reservas_reporte.php`);
    // Se agrega un parámetro a la ruta con el valor del registro seleccionado.
    PATH.searchParams.append('idReserva', id);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}