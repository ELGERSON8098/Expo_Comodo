// Constantes para establecer los elementos del formulario de editar perfil.
const PROFILE_FORM = document.getElementById('profileForm'),
    NOMBRE_ADMINISTRADOR = document.getElementById('nombreAdministrador'),
    CORREO_ADMINISTRADOR = document.getElementById('correoAdministrador'),
    ALIAS_ADMINISTRADOR = document.getElementById('aliasAdministrador'),
    EDIT_BUTTON = document.getElementById('editButton'),
    SAVE_BUTTON = document.getElementById('saveButton'),
    CANCEL_BUTTON = document.getElementById('cancelButton');

let originalData = {}; // Variable para almacenar los valores originales

// Constante para establecer la modal de cambiar contraseña.
const PASSWORD_MODAL = new bootstrap.Modal('#passwordModal');
// Constante para establecer el formulario de cambiar contraseña.
const PASSWORD_FORM = document.getElementById('passwordForm');
document.querySelector('title').textContent = 'Perfil';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Editar perfil';
    // Petición para obtener los datos del usuario que ha iniciado sesión.
    const DATA = await fetchData(USER_API, 'readProfile');
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se inicializan los campos del formulario con los datos del usuario que ha iniciado sesión.
        const ROW = DATA.dataset;
        NOMBRE_ADMINISTRADOR.value = ROW.nombre_administrador;
        CORREO_ADMINISTRADOR.value = ROW.correo_administrador;
        ALIAS_ADMINISTRADOR.value = ROW.usuario_administrador;

        // Guardar los valores originales.
        originalData = {
            nombre: ROW.nombre_administrador,
            correo: ROW.correo_administrador,
            usuario: ROW.usuario_administrador
        };
    } else {
        sweetAlert(2, DATA.error, null);
    }
});

// Método para habilitar edición
EDIT_BUTTON.addEventListener('click', () => {
    // Habilitar los campos
    NOMBRE_ADMINISTRADOR.disabled = false;
    CORREO_ADMINISTRADOR.disabled = false;
    ALIAS_ADMINISTRADOR.disabled = false;

    // Mostrar botón Guardar y Cancelar, y deshabilitar Editar
    SAVE_BUTTON.style.display = 'inline-block';
    CANCEL_BUTTON.style.display = 'inline-block';
    EDIT_BUTTON.disabled = true;
});

// Método del evento para cancelar edición
CANCEL_BUTTON.addEventListener('click', () => {
    // Revertir los cambios y deshabilitar los campos
    NOMBRE_ADMINISTRADOR.value = originalData.nombre;
    CORREO_ADMINISTRADOR.value = originalData.correo;
    ALIAS_ADMINISTRADOR.value = originalData.usuario;

    NOMBRE_ADMINISTRADOR.disabled = true;
    CORREO_ADMINISTRADOR.disabled = true;
    ALIAS_ADMINISTRADOR.disabled = true;

    // Ocultar botón Guardar y Cancelar, y habilitar Editar
    SAVE_BUTTON.style.display = 'none';
    CANCEL_BUTTON.style.display = 'none';
    EDIT_BUTTON.disabled = false;
});

// Método del evento para cuando se envía el formulario de editar perfil.
PROFILE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();

    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(PROFILE_FORM);
    // Petición para actualizar los datos personales del usuario.
    const DATA = await fetchData(USER_API, 'editProfile', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        sweetAlert(1, DATA.message, true);
        
        // Deshabilitar los campos y botones tras guardar
        NOMBRE_ADMINISTRADOR.disabled = true;
        CORREO_ADMINISTRADOR.disabled = true;
        ALIAS_ADMINISTRADOR.disabled = true;

        // Ocultar botón Guardar y Cancelar
        SAVE_BUTTON.style.display = 'none';
        CANCEL_BUTTON.style.display = 'none';
        EDIT_BUTTON.disabled = false;

        // Actualizar valores originales
        originalData = {
            nombre: NOMBRE_ADMINISTRADOR.value,
            correo: CORREO_ADMINISTRADOR.value,
            usuario: ALIAS_ADMINISTRADOR.value
        };
    } else {
        sweetAlert(2, DATA.error, false);
    }
});
