let n = 300; // Tiempo de inactividad en segundos
const l = document.getElementById("number"); // Elemento para mostrar el contador

const startSessionTimer = () => {
    const id = window.setInterval(function() {
        // Mostrar el tiempo restante en el contador
        l.innerText = Math.ceil(n / 60) + " min"; // Mostrar el tiempo en minutos
        n--;

        // Si el contador llega a 0, cerrar sesión
        if (n < 0) {
            logOut2(); // Llamar a la función de cierre de sesión
            clearInterval(id); // Detener el contador
        }
    }, 1000); // Actualizar cada segundo

    // Reiniciar el contador al mover el ratón
    document.onmousemove = function() {
        n = 300; // Reiniciar el contador a 10 segundos
    };
};

// Iniciar el temporizador de sesión al cargar el documento
document.addEventListener('DOMContentLoaded', () => {
    startSessionTimer(); // Iniciar el temporizador de sesión
});

const logOut2 = async () => {
    // Mostrar una alerta personalizada
    const alertMessage = document.createElement('div');
    alertMessage.innerText = 'Tu sesión ha expirado por motivos de inactividad';
    alertMessage.style.position = 'fixed';
    alertMessage.style.top = '50%';
    alertMessage.style.left = '50%';
    alertMessage.style.transform = 'translate(-50%, -50%)';
    alertMessage.style.backgroundColor = 'rgba(0, 0, 0, 0.8)';
    alertMessage.style.color = 'white';
    alertMessage.style.padding = '80px';
    alertMessage.style.borderRadius = '5px';
    alertMessage.style.zIndex = '1000';
    document.body.appendChild(alertMessage);

    // Esperar 3 segundos antes de cerrar sesión
    setTimeout(async () => {
        // Petición para eliminar la sesión.
        const DATA = await fetchData(USER_API, 'logOut');
        // Se comprueba si la respuesta es satisfactoria.
        if (DATA.status) {
            location.href = 'index.html'; // Redirigir a la página de inicio
        } else {
            alert(DATA.exception);
        }
        // Remover la alerta del DOM
        document.body.removeChild(alertMessage);
    }, 3000); // Cambia 3000 por el tiempo que desees que dure la alerta (en milisegundos)
}