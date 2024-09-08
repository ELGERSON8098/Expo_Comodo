
document.getElementById('claveAdministrador').addEventListener('input', function () {
    var password = this.value;
    var strengthBar = document.getElementById('passwordStrength');
    var feedback = document.getElementById('passwordFeedback');
    var strength = 0;

    // Condiciones para aumentar la fortaleza de la contraseña
    if (password.length >= 8) strength += 25; // Longitud mínima
    if (/[A-Z]/.test(password)) strength += 25; // Al menos una mayúscula
    if (/[0-9]/.test(password)) strength += 25; // Al menos un número
    if (/[\W_]/.test(password)) strength += 25; // Al menos un carácter especial

    // Actualiza la barra de progreso y el mensaje
    strengthBar.style.width = strength + '%';
    strengthBar.setAttribute('aria-valuenow', strength);

    if (strength <= 25) {
        strengthBar.className = 'progress-bar bg-danger'; // Débil
        feedback.textContent = 'Contraseña débil';
    } else if (strength <= 50) {
        strengthBar.className = 'progress-bar bg-warning'; // Media
        feedback.textContent = 'Contraseña moderada';
    } else if (strength <= 75) {
        strengthBar.className = 'progress-bar bg-info'; // Fuerte
        feedback.textContent = 'Contraseña fuerte';
    } else {
        strengthBar.className = 'progress-bar bg-success'; // Muy fuerte
        feedback.textContent = 'Contraseña muy fuerte';
    }
});
