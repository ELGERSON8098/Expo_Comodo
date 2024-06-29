// Constantes para completar las rutas de la API.
const ADMINISTADOR_API = 'services/admin/administrador.php';
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
    ID_ADMINISTRADOR = document.getElementById('idAdmin'),
    NOMBRE_ADMINISTRADOR = document.getElementById('NAdmin'),
    USUARIO_ADMINISTRADOR= document.getElementById('NUsuario'),
    CORREO_ADMINISTRADOR = document.getElementById('CorreoAd'),
    CONTRASEÑA_ADMINISTRADOR = document.getElementById('ContraAd'),
    CONTRASEÑA_CONFIRMAR_ADMINISTRADOR = document.getElementById('confirmarClaveA'),
    NIVEL_ADMINISTRADOR = document.getElementById('NivAd');
    document.querySelector('title').textContent = 'Administrador';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Gestionar administradores';
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
    (ID_ADMINISTRADOR.value) ? action = 'updateRow' : action = 'createTrabajadores';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(ADMINISTADOR_API, action, FORM);
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
    const DATA = await fetchData(ADMINISTADOR_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros fila por fila.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre_administrador}</td>
                    <td>${row.usuario_administrador}</td>
                    <td>${row.correo_administrador}</td>
                    <td>${row.nombre_nivel}</td>
                    <td>
                <button type="button" class="btn btn-info  me-2 mb-2 mb-sm-2" onclick="openUpdate(${row.id_administrador})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                <button type="button" class="btn btn-danger  me-2 mb-2 mb-sm-2" onclick="openDelete(${row.id_administrador})">
                    <i class="bi bi-trash-fill"></i>
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
    MODAL_TITLE.textContent = 'Agregar nuevo trabajador';
    // Se prepara el formulario.
    SAVE_FORM.reset();
    fillSelect(ADMINISTADOR_API, 'readAllNivelesUsuarios', 'NivAd');
}

/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idAdmin', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(ADMINISTADOR_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar trabajador';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        CONTRASEÑA_ADMINISTRADOR.disabled = true;
        CONTRASEÑA_CONFIRMAR_ADMINISTRADOR.disabled = true;
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_ADMINISTRADOR.value = ROW.id_administrador;
        NOMBRE_ADMINISTRADOR.value = ROW.nombre_administrador;
        USUARIO_ADMINISTRADOR.value = ROW.usuario_administrador;
        CORREO_ADMINISTRADOR.value = ROW.correo_administrador;
        fillSelect(ADMINISTADOR_API, 'readAllNivelesUsuarios', 'NivAd',  parseInt(ROW.id_nivel_usuario));
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
    const RESPONSE = await confirmAction('¿Desea eliminar el trabajador de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idAdmin', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(ADMINISTADOR_API, 'deleteRow', FORM);
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
