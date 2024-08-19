// Constantes para completar las rutas de la API.
const USUARIO_API = 'services/admin/usuariosC.php';

// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');

// Constantes para establecer el contenido de la tabla.
const TABLE_BODY = document.getElementById('tableBody');
const ROWS_FOUND = document.getElementById('rowsFound');

// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal');
const MODAL_TITLE = document.getElementById('modalTitle');

// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm');
const ID_USUARIO = document.getElementById('idusuarioC');
const NOMBRE_USUARIO = document.getElementById('nombreUsuarioC');
const ALIAS_USUARIO = document.getElementById('aliasUsuarioC');
const CORREO_USUARIO = document.getElementById('correoUsuarioC');
const TEL_USUARIO = document.getElementById('Telefono');
const DIRECCION_USUARIO = document.getElementById('DirecC');
const DUI_USUARIO = document.getElementById('duiUsuarioC');

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    document.getElementById('mainTitle').textContent = 'Gestionar clientes';
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
    const action = (ID_USUARIO.value) ? 'updateRow' : 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(USUARIO_API, action, FORM);
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
    try {
        // Se inicializa el contenido de la tabla.
        ROWS_FOUND.textContent = '';
        TABLE_BODY.innerHTML = '';
        // Se verifica la acción a realizar.
        const action = (form) ? 'searchRows' : 'readAll';
        // Petición para obtener los registros disponibles.
        const DATA = await fetchData(USUARIO_API, action, form);
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se recorre el conjunto de registros fila por fila.
            DATA.dataset.forEach(row => {
                // Se crean y concatenan las filas de la tabla con los datos de cada registro.
                TABLE_BODY.innerHTML += `
                    <tr>
                        <td>${row.nombre}</td>
                        <td>${row.usuario}</td>
                        <td>${row.correo}</td>
                        <td>${row.telefono}</td>
                        <td>${row.dui_cliente}</td>
                        <td>
                            <button type="button" class="btn btn-info  me-2 mb-2 mb-sm-2" onclick="openView(${row.id_usuario})">
                                <i class="bi bi-eye-fill"></i>
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
    } catch (error) {
        sweetAlert(4, 'Error al llenar la tabla: ' + error.message, false);
    }
}

// Variable global para mantener la referencia del mapa y marcador
let map;
let marker;

// Evento para inicializar el mapa cuando se muestra el modal
document.getElementById('saveModal').addEventListener('shown.bs.modal', function () {
    if (map) {
        map.invalidateSize(); // Redimensionar el mapa si ya está inicializado
    } else {
        initializeMap();
    }
});

// Evento para limpiar el marcador cuando se oculta el modal
document.getElementById('saveModal').addEventListener('hidden.bs.modal', function () {
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

// Función para abrir el modal y mostrar datos del usuario para editar
const openView = async (id) => {
    try {
        const FORM = new FormData();
        FORM.append('idusuarioC', id);

        const DATA = await fetchData(USUARIO_API, 'readOne', FORM);

        if (DATA.status) {
            // Deshabilitar campos que no deben ser editables
            SAVE_MODAL.show(); // Mostrar el modal

            // Establecer título del modal
            MODAL_TITLE.textContent = 'Información del Cliente';

            // Resetear el formulario
            SAVE_FORM.reset();
            NOMBRE_USUARIO.disabled = true;
            ALIAS_USUARIO.disabled = true;
            CORREO_USUARIO.disabled = true;
            TEL_USUARIO.disabled = true;
            DUI_USUARIO.disabled = true;
            DIRECCION_USUARIO.disabled = true;

            // Llenar campos con datos del usuario
            const ROW = DATA.dataset;
            ID_USUARIO.value = ROW.id_usuario;
            NOMBRE_USUARIO.value = ROW.nombre;
            ALIAS_USUARIO.value = ROW.usuario;
            CORREO_USUARIO.value = ROW.correo;
            TEL_USUARIO.value = ROW.telefono;
            DIRECCION_USUARIO.value = ROW.direccion_cliente;
            DUI_USUARIO.value = ROW.dui_cliente;

            // Actualizar el mapa con la dirección del usuario al abrir el modal
            updateMap(DIRECCION_USUARIO.value);
        } else {
            sweetAlert(2, DATA.error, false);
        }
    } catch (error) {
        sweetAlert(2, 'Error al abrir la vista: ' + error.message, false);
    }
}
const openReportClientes = () => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/admin/productos_categoria.php`);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}
