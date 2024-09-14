// Constante para completar la ruta de la API.
const USER_API = 'services/admin/administrador.php';
// Constante para establecer el elemento del contenido principal.
const MAIN = document.querySelector('main');
MAIN.style.paddingTop = '75px';
MAIN.style.paddingBottom = '100px';
MAIN.classList.add('login-container');
// Constante para establecer el elemento del título principal.
const MAIN_TITLE = document.getElementById('mainTitle');
MAIN_TITLE.classList.add('text-center', 'py-3');

/*  Función asíncrona para cargar el encabezado y pie del documento.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const loadTemplate = async () => {
    // Petición para obtener el nombre del usuario que ha iniciado sesión.
    const DATA = await fetchData(USER_API, 'getUser');
    // Se verifica si el usuario está autenticado, de lo contrario se envía a iniciar sesión.
    if (DATA.session) {
        // Se comprueba si existe un alias definido para el usuario, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Generar el contenido del sidebar según el nivel de usuario
            let navOptions = '';
            if (DATA.user_level == 1) {
                navOptions = `
                    <li><a href="../admin/dashboard.html"><i class="fas fa-home"></i>Dashboard</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"><i class="fas fa-box"></i>Productos</a>
                        <ul class="dropdown-menu">
                            <li><a href="../admin/producto.html">Productos</a></li>
                            <li><a href="../admin/categoria.html">Categorías</a></li>
                            <li><a href="../admin/genero.html">Género</a></li>
                            <li><a href="../admin/colores.html">Colores</a></li>
                            <li><a href="../admin/marcas.html">Marcas</a></li>
                            <li><a href="../admin/tallas.html">Tallas</a></li>
                            <li><a href="../admin/material.html">Materiales</a></li>
                        </ul>
                    </li>
                    <li><a href="../admin/descuento.html"><i class="fas fa-percent"></i>Descuentos</a></li>
                    <li><a href="../admin/usuariosc.html"><i class="fas fa-users"></i>Clientes</a></li>
                    <li><a href="../admin/reserva.html"><i class="fas fa-calendar-alt"></i>Reservas</a></li>
                    <li><a href="../admin/administrador.html"><i class="fas fa-user-shield"></i>Administradores</a></li>
                `;
            } else if (DATA.user_level == 2) {
                navOptions = `
                    <li><a href="../admin/dashboard.html"><i class="fas fa-home"></i>Dashboard</a></li>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"><i class="fas fa-box"></i>Productos</a>
                        <ul class="dropdown-menu">
                            <li><a href="../admin/producto.html">Productos</a></li>
                            <li><a href="../admin/categoria.html">Categorías</a></li>
                            <li><a href="../admin/genero.html">Género</a></li>
                            <li><a href="../admin/colores.html">Colores</a></li>
                            <li><a href="../admin/marcas.html">Marcas</a></li>
                            <li><a href="../admin/tallas.html">Tallas</a></li>
                        </ul>
                    </li>
                    <li><a href="../admin/descuento.html"><i class="fas fa-percent"></i>Descuentos</a></li>
                `;
            } else if (DATA.user_level == 3) {
                navOptions = `
                    <li><a href="../admin/dashboard.html"><i class="fas fa-home"></i>Dashboard</a></li>
                    <li><a href="../admin/usuariosc.html"><i class="fas fa-users"></i>Clientes</a></li>
                    <li><a href="../admin/reserva.html"><i class="fas fa-calendar-alt"></i>Reservas</a></li>
                `;
            }

            const sidebarHTML = `
                    <div class="sidebar">
                        <div class="user-info">
                            <div class="user-avatar">?</div>
                            <div class="user-details">
                                <h2>${DATA.username}</h2>
                                <p>Active</p>
                            </div>
                        </div>
                        <nav>
                            <ul>
                                ${navOptions}
                            </ul>
                        </nav>
                        <div class="logout">
                            <a href="#" onclick="logOut()"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a>
                        </div>
                    </div>
                `;

            // Insertar el sidebar antes del contenido principal
            MAIN.insertAdjacentHTML('beforebegin', sidebarHTML);

            // Ajustar el estilo del contenido principal
            MAIN.style.marginLeft = '260px'; // Ancho del sidebar + un poco de espacio
            MAIN.style.transition = 'margin-left 0.3s';

            // Agregar event listeners para los dropdowns después de insertar el sidebar
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation(); // Evita que el evento se propague
                    const dropdownContainer = dropdown.closest('.dropdown');
                    dropdownContainer.classList.toggle('active');
                });
            });

            const sidebarToggle = document.createElement('button');
            sidebarToggle.classList.add('sidebar-toggle');
            sidebarToggle.innerHTML = '☰';
            document.body.appendChild(sidebarToggle);

            const sidebarElement = document.querySelector('.sidebar');
            sidebarToggle.addEventListener('click', function () {
                sidebarElement.classList.toggle('active');
            });

        } else {
            sweetAlert(3, DATA.error, false, 'index.html');
        }
    } else {
        // Se comprueba si la página web es la principal, de lo contrario se direcciona a iniciar sesión.
        if (location.pathname.endsWith('index.html')) {
            // Se agrega el pie de la página web después del contenido principal.
            MAIN.insertAdjacentHTML('afterend', ``);
        } else {
            location.href = 'index.html';
        }
    }
}

// Llamar a la función para cargar la plantilla.
loadTemplate();