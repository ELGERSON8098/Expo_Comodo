// Constante para completar la ruta de la API.
const USER_API = 'services/admin/administrador.php';
// Constante para establecer el elemento del contenido principal.
const MAIN = document.querySelector('main');
MAIN.style.paddingTop = '45px';
MAIN.style.paddingBottom = '35px';
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
                            <h2><a href="../admin/perfil.html">${DATA.username}</a></h2>
                            <p>Activo</p>
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

            // Verificar si el sidebar ya existe
            let existingSidebar = document.querySelector('.sidebar');
            if (existingSidebar) {
                // Si existe, actualizamos su contenido
                existingSidebar.innerHTML = sidebarHTML;
            } else {
                // Si no existe, lo insertamos antes del contenido principal
                MAIN.insertAdjacentHTML('beforebegin', sidebarHTML);
            }

            // Ajustar el estilo del contenido principal
            function adjustMainContent() {
                if (window.innerWidth > 768) {
                    MAIN.style.marginLeft = '260px'; // Ancho del sidebar + un poco de espacio
                } else {
                    MAIN.style.marginLeft = '0';
                }
            }

            // Llamar a la función inicialmente y agregar un event listener para redimensionar
            adjustMainContent();
            window.addEventListener('resize', adjustMainContent);

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

            // Crear y agregar el botón de toggle del sidebar solo si no existe
            if (!document.querySelector('.sidebar-toggle')) {
                const sidebarToggle = document.createElement('button');
                sidebarToggle.classList.add('sidebar-toggle');
                sidebarToggle.innerHTML = '☰';
                document.body.appendChild(sidebarToggle);

                const sidebarElement = document.querySelector('.sidebar');
                sidebarToggle.addEventListener('click', function () {
                    sidebarElement.classList.toggle('active');
                });
            }

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