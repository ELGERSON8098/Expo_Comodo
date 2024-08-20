// Constantes para completar las rutas de la API.
const MARCA_API = 'services/admin/marca.php';
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
    ID_Marca = document.getElementById('idMarca'),
    NOMBRE_Marca = document.getElementById('nombreMarca');

// Se establece el título de la página web.
document.querySelector('title').textContent = 'Marcas';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Gestionar marcas';
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
    (ID_Marca.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(MARCA_API, action, FORM);
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
    const DATA = await fetchData(MARCA_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros fila por fila.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.marca}</td>
                    <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td>
                    <td>
                        <button type="button" class="btn btn-info  me-2 mb-2 mb-sm-2" onclick="openUpdate(${row.id_marca})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button type="button" class="btn btn-danger  me-2 mb-2 mb-sm-2" onclick="openDelete(${row.id_marca})">
                            <i class="bi bi-trash-fill"></i>
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
    MODAL_TITLE.textContent = 'Agregar una nueva marca';
    // Se prepara el formulario.
    SAVE_FORM.reset();
}

/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idMarca', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(MARCA_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar marca';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_Marca.value = ROW.id_marca;
        NOMBRE_Marca.value = ROW.marca;
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
    const RESPONSE = await confirmAction('¿Desea eliminar la marca de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idMarca', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(MARCA_API, 'deleteRow', FORM);
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

// Función para cargar las marcas disponibles como checkboxes
async function cargarMarcas() {
    const DATA = await fetchData(MARCA_API, 'readAll');
    if (DATA.status) {
        const marcasCheckboxes = document.getElementById('marcasCheckboxes');
        marcasCheckboxes.innerHTML = ''; // Limpiar contenido existente
        DATA.dataset.forEach(marca => {
            marcasCheckboxes.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="${marca.marca}" id="marca${marca.id_marca}" name="marca">
                    <label class="form-check-label" for="marca${marca.id_marca}">
                        ${marca.marca}
                    </label>
                </div>
            `;
        });
    } else {
        sweetAlert(2, DATA.error, false);
    }
}

// Función para generar el gráfico
async function graficoVentasPorMarcas() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;
    const selectedMarcas = Array.from(document.querySelectorAll('input[name="marca"]:checked')).map(el => el.value);

    if (!fechaInicio || !fechaFin || selectedMarcas.length === 0) {
        sweetAlert(3, 'Por favor, seleccione fechas y al menos una marca', null);
        return;
    }

    const form = new FormData();
    form.append('fechaInicio', fechaInicio);
    form.append('fechaFin', fechaFin);
    form.append('marcas', JSON.stringify(selectedMarcas));

    const DATA = await fetchData(MARCA_API, 'ventasPorMarcasFecha', form);
    if (DATA.status) {
        const marcasData = selectedMarcas.map(marca => {
            return {
                label: marca,
                data: DATA.dataset.filter(row => row.nombre_marca === marca).map(row => ({
                    x: row.fecha_reserva,
                    y: parseFloat(row.total_ventas)
                })),
                backgroundColor: getRandomColor(),
                borderColor: getRandomColor(),
                borderWidth: 1
            };
        });

        const ctx = document.getElementById('chartVentasMarcas').getContext('2d');
        if (window.ventasChart) {
            window.ventasChart.destroy();
        }
        window.ventasChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: DATA.dataset.map(row => row.fecha_reserva),
                datasets: marcasData
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        stacked: true
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Ventas ($)'
                        },
                        stacked: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Ventas por marca'
                    },
                    legend: {
                        display: true,
                        position: 'top'
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

// Evento para cargar las marcas cuando se abre el modal
document.getElementById('chartModal').addEventListener('show.bs.modal', cargarMarcas);

// Asegúrate de que esto esté dentro de tu evento DOMContentLoaded existente
document.addEventListener('DOMContentLoaded', () => {
    // ... tu código existente ...

    // Agregar evento al botón que abre el modal de la gráfica
    document.querySelector('[data-bs-target="#chartModal"]').addEventListener('click', () => {
        // Restablecer fechas y limpiar gráfica existente si la hay
        document.getElementById('fechaInicio').value = '';
        document.getElementById('fechaFin').value = '';
        if (window.ventasChart) {
            window.ventasChart.destroy();
        }
    });
});