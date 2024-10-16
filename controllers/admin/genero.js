
// Constante para completar la ruta de la API.
const GENERO_API = 'services/admin/genero.php';
// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');
// Constantes para establecer los elementos de la tabla.
const TABLE_BODY = document.getElementById('tableBody'),
    ROWS_FOUND = document.getElementById('rowsFound');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal'),
    MODAL_TITLE = document.getElementById('modalTitle');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_GENERO = document.getElementById('idGenero'),
    NOMBRE_GENERO = document.getElementById('nombre_genero'),
    IMAGEN_GENERO = document.getElementById('imagen_genero');
document.querySelector('title').textContent = 'Géneros';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Gestionar géneros';
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
    (ID_GENERO.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(GENERO_API, action, FORM);
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
    // Se inicializa el contenido del contenedor de tarjetas.
    ROWS_FOUND.textContent = '';
    const cardContainer = document.getElementById('cardContainer');
    cardContainer.innerHTML = '';

    // Se verifica la acción a realizar.
    const action = form ? 'searchRows' : 'readAll';

    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(GENERO_API, action, form);

    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se crea un contenedor para las tarjetas
        const row = document.createElement('div');
        row.className = 'row g-4';

        // Se recorre el conjunto de registros (dataset) para crear una tarjeta por cada registro.
        DATA.dataset.forEach(genero => {
            // Se crea la tarjeta para cada registro
            const card = document.createElement('div');
            card.className = 'col-md-4 col-lg-4';
            card.innerHTML = `
                <div class="card h-100">
                    <img src="${SERVER_URL}images/categorias/${genero.imagen_genero}" class="card-img-top" alt="${genero.nombre_genero}" height="200">
                    <div class="card-body">
                        <h5 class="card-title">${genero.nombre_genero}</h5>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-info btn-sm" onclick="openUpdate(${genero.id_genero})">
                            <i class="bi bi-pencil-fill"></i> Editar
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="openDelete(${genero.id_genero})">
                            <i class="bi bi-trash-fill"></i> Eliminar
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="openReport(${genero.id_genero})">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Reporte
                        </button>
                    </div>
                </div>
            `;
            row.appendChild(card);
        });

        cardContainer.appendChild(row);

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
    MODAL_TITLE.textContent = 'Crear género';
    // Se prepara el formulario.
    SAVE_FORM.reset();
}

/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    const FORM = new FormData();
    FORM.append('idGenero', id);
    const DATA = await fetchData(GENERO_API, 'readOne', FORM);
    if (DATA.status) {
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar género';
        SAVE_FORM.reset();
        const ROW = DATA.dataset;
        ID_GENERO.value = ROW.id_genero;
        NOMBRE_GENERO.value = ROW.nombre_genero;
        document.getElementById('imagenActual').value = ROW.imagen_genero;
    } else {
        sweetAlert(2, DATA.error, false);
        console.log(error);
    }
}


/*
*   Función asíncrona para eliminar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openDelete = async (id) => {
    // Llamada a la función para mostrar un mensaje de confirmación, capturando la respuesta en una constante.
    const RESPONSE = await confirmAction('¿Desea eliminar el género de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idGenero', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(GENERO_API, 'deleteRow', FORM);
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
    const PATH = new URL(`${SERVER_URL}reports/admin/genero.php`);
    // Se agrega un parámetro a la ruta con el valor del registro seleccionado.
    PATH.searchParams.append('idGenero', id);
    // Se abre el reporte en una nueva pestaña.
    window.open(PATH.href);
}