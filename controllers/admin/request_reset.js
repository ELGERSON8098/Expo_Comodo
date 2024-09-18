// Constante para el formulario de solicitud de restablecimiento
const RESET_REQUEST_FORM = document.getElementById('resetRequestForm');

// Cuando se solicita el código de recuperación
RESET_REQUEST_FORM.addEventListener('submit', async (event) => {
    event.preventDefault();
    const FORM = new FormData(RESET_REQUEST_FORM);
    const DATA = await fetchData(USER_API, 'requestPasswordReset', FORM);
    
    if (DATA.status) {
        // Guardamos el correo en sessionStorage
        sessionStorage.setItem('resetEmail', FORM.get('correo'));
        sweetAlert(1, DATA.message, true, 'verify_code.html');
    } else {
        sweetAlert(2, DATA.error, false);
    }
});
