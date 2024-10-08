// Constante para establecer el formulario de registro del primer usuario.
const SIGNUP_FORM = document.getElementById('signupForm');
// Constante para establecer el formulario de inicio de sesión.
const LOGIN_FORM = document.getElementById('loginForm');

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();

    // Deshabilitar el caché de la página
    window.onpageshow = function (event) {
        if (event.persisted) {
            window.location.reload();
        }
    };

    // Petición para consultar los usuarios registrados.
    const DATA = await fetchData(USER_API, 'readUsers');
    // Se comprueba si existe una sesión, de lo contrario se sigue con el flujo normal.
    if (DATA.session) {
        // Se direcciona a la página web de bienvenida.
        location.href = 'dashboard.html';
    } else if (DATA.status) {
        // Se establece el título del contenido principal.
        MAIN_TITLE.textContent = 'Iniciar sesión';
        // Se muestra el formulario para iniciar sesión.
        LOGIN_FORM.classList.remove('d-none');
        sweetAlert(4, DATA.message, true);
    } else {
        // Se establece el título del contenido principal.
        MAIN_TITLE.textContent = 'Registrar primer usuario';
        // Se muestra el formulario para registrar el primer usuario.
        SIGNUP_FORM.classList.remove('d-none');
        sweetAlert(4, DATA.error, true);
    }
});

// Método del evento para cuando se envía el formulario de registro del primer usuario.
SIGNUP_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SIGNUP_FORM);
    // Petición para registrar el primer usuario del sitio privado.
    const DATA = await fetchData(USER_API, 'signUp', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        sweetAlert(1, DATA.message, true, 'index.html');
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

const TWO_FACTOR_FORM = document.getElementById('twoFactorForm');
const SETUP_2FA_DIV = document.getElementById('setup2FA');
const SETUP_2FA_FORM = document.getElementById('verify2FASetup');
let currentAdminId = null;
// Método del evento para cuando se envía el formulario de inicio de sesión
LOGIN_FORM.addEventListener('submit', async (event) => {
    event.preventDefault();
    const FORM = new FormData(LOGIN_FORM);
    const omit2FA = document.getElementById('omit2FA').checked;
    FORM.append('omit2FA', omit2FA);
 
    const DATA = await fetchData(USER_API, 'logIn', FORM);
    console.log(DATA); // Agrega esta línea
    if (DATA.status) {
        if (DATA.omit_2fa) {
            console.log('Iniciando sesión sin 2FA'); // Agrega esta línea
            sweetAlert(1, DATA.message, true, 'dashboard.html');
        } else if (DATA.need_setup_2fa) {
            // Mostrar QR y secreto para configuración inicial
            LOGIN_FORM.classList.add('d-none');
            SETUP_2FA_DIV.classList.remove('d-none');
 
            // Generar y mostrar código QR usando qrcode.js
            const qrContent = `otpauth://totp/Comodos:${DATA.usuario}?secret=${DATA.totp_secret}&issuer=Comodos`;
            const qrCodeElement = document.getElementById('qrCode');
            new QRCode(qrCodeElement, {
                text: qrContent,
                width: 300,
                height: 300
            });
            document.getElementById('manualSecret').textContent = DATA.totp_secret;
 
            currentAdminId = DATA.id_administrador;
        } else if (DATA.need_2fa) {
            // Mostrar formulario para ingresar código TOTP
            LOGIN_FORM.classList.add('d-none');
            TWO_FACTOR_FORM.classList.remove('d-none');
            currentAdminId = DATA.id_administrador;
        }
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

TWO_FACTOR_FORM.addEventListener('submit', async (event) => {
    event.preventDefault();
    const FORM = new FormData(TWO_FACTOR_FORM);
    FORM.append('id_administrador', currentAdminId);

    const DATA = await fetchData(USER_API, 'verifyTOTP', FORM);
    if (DATA.status) {
        sweetAlert(1, DATA.message, true, 'dashboard.html');
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

SETUP_2FA_FORM.addEventListener('submit', async (event) => {
    event.preventDefault();
    const FORM = new FormData(SETUP_2FA_FORM);
    FORM.append('id_administrador', currentAdminId);

    const DATA = await fetchData(USER_API, 'setupTOTP', FORM);
    if (DATA.status) {
        sweetAlert(1, DATA.message, true, 'dashboard.html');
    } else {
        sweetAlert(2, DATA.error, false);
    }
});