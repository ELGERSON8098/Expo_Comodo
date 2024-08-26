// Constantes para completar las rutas de la API.
const DESCUENTO_API = 'services/admin/descuento.php';
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
    ID_DESCUENTO = document.getElementById('idDescuento'),
    NOMBRE_DESCUENTO = document.getElementById('nombreDescuento');
NOMBRE_DESCRIPCION = document.getElementById('nombreDesc');
NOMBRE_VALOR = document.getElementById('ValorM');
document.querySelector('title').textContent = 'Descuentos';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Gestionar descuentos';
    // Llamada a la función para llenar la tabla con los registros existentes.
    fillTable();
});

SEARCH_FORM.addEventListener('submit', (event) => {
    event.preventDefault(); // Evitar recargar la página
    const FORM = new FormData(SEARCH_FORM); // Obtener datos del formulario
    fillTable(FORM); // Llamar a la función fillTable con los datos del formulario
});


// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se verifica la acción a realizar.
    (ID_DESCUENTO.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(DESCUENTO_API, action, FORM);
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
    (form) ? action = 'searchRows' : action = 'readAll';
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(DESCUENTO_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros fila por fila.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre_descuento}</td>
                    <td>${row.descripcion}</td>
                    <td>${row.valor}</td>
                    <td>
                <button type="button" class="btn btn-info me-2 mb-2 mb-sm-2" onclick="openUpdate(${row.id_descuento})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                <button type="button" class="btn btn-danger  me-2 mb-2 mb-sm-2" onclick="openDelete(${row.id_descuento})">
                    <i class="bi bi-trash-fill"></i>
                </button>    
                <button type="button" class="btn btn-warning me-2 mb-2 mb-sm-2" onclick="openReport(${row.id_descuento})">
                            <i class="bi bi-file-earmark-pdf-fill"></i>
                        </button>            
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
*   Función para preparar el formulario al momento de insertar un registro.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const openCreate = () => {
    // Se muestra la caja de diálogo con su título.
    SAVE_MODAL.show();
    MODAL_TITLE.textContent = 'Agregar nuevo descuento';
    // Se prepara el formulario.
    SAVE_FORM.reset();
    ID_DESCUENTO.disabled = false;
    NOMBRE_DESCUENTO.disabled = false;
    NOMBRE_DESCRIPCION.disabled = false;
    NOMBRE_VALOR.disabled = false;
}

/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idDescuento', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(DESCUENTO_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar descuento';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_DESCUENTO.value = ROW.id_descuento;
        NOMBRE_DESCUENTO.value = ROW.nombre_descuento;
        NOMBRE_DESCRIPCION.value = ROW.descripcion;
        NOMBRE_VALOR.value = ROW.valor;
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
    const RESPONSE = await confirmAction('¿Desea eliminar el descuento de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idDescuento', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(DESCUENTO_API, 'deleteRow', FORM);
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

const openReport = (id) => {
    // Se declara una constante tipo objeto con la ruta específica del reporte en el servidor.
    const PATH = new URL(`${SERVER_URL}reports/admin/descuento.php`);
    // Se agrega un parámetro a la ruta con el valor del registro seleccionado.
    PATH.searchParams.append('idDescuento', id);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}

// Función para generar el gráfico de descuentos por rango de precios
async function generarGraficoDescuentos() {
    const precioMin = document.getElementById('precioMin').value;
    const precioMax = document.getElementById('precioMax').value;

    if (!precioMin || !precioMax) {
        sweetAlert(3, 'Por favor, ingrese ambos valores de precio', null);
        return;
    }

    // Validar que los precios sean números positivos
    if (isNaN(precioMin) || isNaN(precioMax) || precioMin < 0 || precioMax < 0) {
        sweetAlert(3, 'Los precios deben ser números positivos', null);
        return;
    }

    // Validar que el precio máximo sea mayor que el precio mínimo
    if (parseFloat(precioMax) <= parseFloat(precioMin)) {
        sweetAlert(3, 'El precio máximo debe ser mayor que el precio mínimo', null);
        return;
    }

    const form = new FormData();
    form.append('precioMin', precioMin);
    form.append('precioMax', precioMax);

    
    const DATA = await fetchData(DESCUENTO_API, 'descuentosPorRangoPrecio', form);
    if (DATA.status) {
        const descuentosData = DATA.dataset.map(row => ({
            x: parseFloat(row.precio),
            y: parseFloat(row.valor)
        }));

        const ctx = document.getElementById('chartDescuentos').getContext('2d');
        if (window.descuentosChart) {
            window.descuentosChart.destroy();
        }
        window.descuentosChart = new Chart(ctx, {
            type: 'scatter',
            data: {
                datasets: [{
                    label: 'Descuentos por Rango de Precios',
                    data: descuentosData,
                    backgroundColor: getRandomColor(),
                    borderColor: getRandomColor(),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Precio ($)'
                        },
                        beginAtZero: true
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Descuento (%)'
                        },
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Descuentos aplicados por rango de precios'
                    },
                    legend: {
                        display: false
                    }
                }
            }
        });
    } else {
        sweetAlert(2, DATA.error, null);
    }
}

// Función auxiliar para generar colores aleatorios
function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
