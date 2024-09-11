// Constante para el formulario de restablecimiento de contraseña
const RESET_PASSWORD_FORM = document.getElementById('resetPasswordForm');

// Cargar el correo y el código desde sessionStorage
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('resetEmail').value = sessionStorage.getItem('resetEmail');
    document.getElementById('resetCode').value = sessionStorage.getItem('resetCode');
});

// Evento para restablecer la contraseña
RESET_PASSWORD_FORM.addEventListener('submit', async (event) => {
    event.preventDefault();

    // Verificar si las contraseñas coinciden antes de enviar
    const nuevaClave = document.getElementById('nuevaClave').value;
    const confirmarClave = document.getElementById('confirmarClave').value;

    if (nuevaClave !== confirmarClave) {
        // Mostrar alerta si las contraseñas no coinciden
        sweetAlert(2, 'Las contraseñas no coinciden', false);
    } else {
        // Crear el FormData con los datos del formulario
        const FORM = new FormData(RESET_PASSWORD_FORM);

        // Llamada a la API para restablecer la contraseña
        const DATA = await fetchData(USER_API, 'resetPassword', FORM);
        
        if (DATA.status) {
            // Limpiar los datos de sessionStorage si todo fue bien
            sessionStorage.removeItem('resetEmail');
            sessionStorage.removeItem('resetCode');
            sweetAlert(1, DATA.message, true, 'index.html');
        } else {
            // Mostrar el error si hubo un problema
            sweetAlert(2, DATA.error, false);
        }
    }
});