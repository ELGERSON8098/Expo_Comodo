// Constante para el formulario de verificación de código
const VERIFY_CODE_FORM = document.getElementById('verifyCodeForm');
const CODE_INPUTS = document.querySelectorAll('.code-input');
const HIDDEN_CODE_INPUT = document.getElementById('codigo');

// Ocultar el campo de correo y asignar el valor desde sessionStorage
const verifyEmailInput = document.getElementById('correo');
const storedEmail = sessionStorage.getItem('resetEmail');

if (storedEmail) {
    verifyEmailInput.value = storedEmail;
    verifyEmailInput.setAttribute('type', 'hidden');  // Oculta el campo de correo
}

// Manejar la entrada de código
CODE_INPUTS.forEach((input, index) => {
    input.addEventListener('input', (e) => {
        if (e.target.value.length === 1) {
            if (index < CODE_INPUTS.length - 1) {
                CODE_INPUTS[index + 1].focus();
            }
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && input.value === '' && index > 0) {
            CODE_INPUTS[index - 1].focus();
        }
    });
});

// Evento para verificar el código
VERIFY_CODE_FORM.addEventListener('submit', async (event) => {
    event.preventDefault();

    // Combinar los valores de los inputs individuales
    const code = Array.from(CODE_INPUTS).map(input => input.value).join('');
    HIDDEN_CODE_INPUT.value = code;

    const FORM = new FormData(VERIFY_CODE_FORM);
    const storedEmail = sessionStorage.getItem('resetEmail');

    if (storedEmail) {
        FORM.set('correo', storedEmail);  // Usa set en lugar de append para evitar duplicados
    }

    const DATA = await fetchData(USER_API, 'verifyResetCode', FORM);

    if (DATA.status) {
        sweetAlert(1, DATA.message, false);
        sessionStorage.setItem('resetCode', FORM.get('codigo'));
        location.href = 'reset_password.html';
    } else {
        sweetAlert(2, DATA.error, false);
    }
});