/*
*   CONTROLADOR DE USO GENERAL EN TODAS LAS PÁGINAS WEB.
*/
// Constante para establecer la ruta base del servidor.
const SERVER_URL = 'https://comodosv.site/Expo_Comodo/api/';

/*
*   Función para mostrar un mensaje de confirmación. Requiere la librería sweetalert para funcionar.
*   Parámetros: message (mensaje de confirmación).
*   Retorno: resultado de la promesa.
*/
const confirmAction = (message) => {
    return swal({
        title: 'Advertencia',
        text: message,
        icon: 'warning',
        closeOnClickOutside: false,
        closeOnEsc: false,
        buttons: {
            cancel: {
                text: 'No',
                value: false,
                visible: true
            },
            confirm: {
                text: 'Sí',
                value: true,
                visible: true
            }
        }
    });
}

/*
*   Función asíncrona para manejar los mensajes de notificación al usuario. Requiere la librería sweetalert para funcionar.
*   Parámetros: type (tipo de mensaje), text (texto a mostrar), timer (uso de temporizador) y url (valor opcional con la ubicación de destino).
*   Retorno: ninguno.
*/
const sweetAlert = async (type, text, timer, url = null) => {
    // Se compara el tipo de mensaje a mostrar.
    switch (type) {
        case 1:
            title = 'Éxito';
            icon = 'success';
            break;
        case 2:
            title = 'Error';
            icon = 'error';
            break;
        case 3:
            title = 'Advertencia';
            icon = 'warning';
            break;
        case 4:
            title = 'Aviso';
            icon = 'info';
    }
    // Se define un objeto con las opciones principales para el mensaje.
    let options = {
        title: title,
        text: text,
        icon: icon,
        closeOnClickOutside: false,
        closeOnEsc: false,
        button: {
            text: 'Aceptar'
        }
    };
    // Se verifica el uso del temporizador.
    (timer) ? options.timer = 3000 : options.timer = null;
    // Se muestra el mensaje.
    await swal(options);
    // Se direcciona a una página web si se indica.
    (url) ? location.href = url : undefined;
}

/*
*   Función asíncrona para cargar las opciones en un select de formulario.
*   Parámetros: filename (nombre del archivo), action (acción a realizar), select (identificador del select en el formulario) y selected (dato opcional con el valor seleccionado).
*   Retorno: ninguno.
*/
/*const fillSelect = async (filename, action, select, selected = null) => {
    // Petición para obtener los datos.
    const DATA = await fetchData(filename, action);
    let content = '';
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje.
    if (DATA.status) {
        content += '<option value="" selected>Seleccione una opción</option>';
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se obtiene el dato del primer campo.
            value = Object.values(row)[0];
            // Se obtiene el dato del segundo campo.
            text = Object.values(row)[1];
            // Se verifica cada valor para enlistar las opciones.
            if (value != selected) {
                content += `<option value="${value}">${text}</option>`;
            } else {
                content += `<option value="${value}" selected>${text}</option>`;
            }
        });
    } else {
        content += '<option>No hay opciones disponibles</option>';
    }
    // Se agregan las opciones a la etiqueta select mediante el id.
    document.getElementById(select).innerHTML = content;
}*/

const fillSelect = async (filename, action, select, filter = undefined) => {
    // Se verifica si el filtro contiene un objeto para enviar a la API.
    console.log(typeof (filter))
    const FORM = (typeof (filter) == 'object') ? filter : null;
    // Petición para obtener los datos.
    const DATA = await fetchData(filename, action, FORM);
    let content = '';
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje.
    if (DATA.status) {
        content += '<option value="" selected>Seleccione una opción</option>';
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se obtiene el dato del primer campo de la sentencia SQL.
            value = Object.values(row)[0];
            // Se obtiene el dato del segundo campo de la sentencia SQL.
            text = Object.values(row)[1];
            // Se verifica el valor del filtro para enlistar las opciones.
            const SELECTED = (typeof (filter) == 'number' || typeof (filter) == 'string') ? filter : null;
            if (value != SELECTED) {
                content += `<option value="${value}">${text}</option>`;
            } else {
                content += `<option value="${value}" selected>${text}</option>`;
            }
        });
    } else {
        content += '<option>No hay opciones disponibles</option>';
    }
    // Se agregan las opciones a la etiqueta select mediante el id.
    document.getElementById(select).innerHTML = content;
}

/*
*   Función para generar un gráfico de barras verticales. Requiere la librería chart.js para funcionar.
*   Parámetros: canvas (identificador de la etiqueta canvas), xAxis (datos para el eje X), yAxis (datos para el eje Y), legend (etiqueta para los datos) y title (título del gráfico).
*   Retorno: ninguno.
*/
const barGraph = (canvas, xAxis, yAxis, legend, title) => {
    // Se declara un arreglo para guardar códigos de colores en formato hexadecimal.
    let colors = [];
    // Se generan códigos hexadecimales de 6 cifras de acuerdo con el número de datos a mostrar y se agregan al arreglo.
    xAxis.forEach(() => {
        colors.push('#' + (Math.random().toString(16)).substring(2, 8));
    });

    // Se crea una instancia para generar el gráfico con los datos recibidos.
    new Chart(document.getElementById(canvas), {
        type: 'bar',
        data: {
            labels: xAxis,
            datasets: [{
                label: legend,
                data: yAxis,
                backgroundColor: colors,
                borderColor: '#333', // Color del borde
                borderWidth: 1 // Ancho del borde
            }]
        },
        options: {
            responsive: true, // Hacer que el gráfico sea responsivo
            maintainAspectRatio: false, // Mantener la relación de aspecto
            plugins: {
                title: {
                    display: true,
                    text: title,
                    font: {
                        weight: 'bold',
                        size: 18 // Tamaño del texto del título
                    }
                },
                legend: {
                    display: true,
                    position: 'top' // Posición de la leyenda
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`; // Muestra el valor exacto
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true // Comenzar desde cero en el eje X
                },
                y: {
                    beginAtZero: true // Comenzar desde cero en el eje Y
                }
            }
        }
    });
}



// Gráfico de Pastel
const pieGraph = (canvas, legends, values, title) => {
    let colors = [];
    values.forEach(() => {
        colors.push('rgba(' + [Math.floor(Math.random() * 255), Math.floor(Math.random() * 255), Math.floor(Math.random() * 255), 0.7].join(',') + ')');
    });

    new Chart(document.getElementById(canvas), {
        type: 'pie',
        data: {
            labels: legends,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        bottom: 20
                    },
                    color: '#333',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'top',
                    labels: {
                        color: '#333',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
}

const lineGraph = (canvas, xAxis, yAxis, legend, title) => {
    new Chart(document.getElementById(canvas), {
        type: 'line',
        data: {
            labels: xAxis,
            datasets: [{
                label: legend,
                data: yAxis,
                fill: false,
                borderColor: '#007bff', // Color de la línea
                tension: 0.3, // Suavizado de la línea
                borderWidth: 2,
                pointRadius: 6,
                pointBackgroundColor: '#007bff',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#007bff',
                pointHoverRadius: 8 // Aumentar el tamaño del punto al pasar el cursor
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Permitir que el gráfico cambie de forma
            scales: {
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        bottom: 20
                    },
                    color: '#333',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`; // Muestra el valor exacto
                        }
                    }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
}

// Gráfico de Radar
const radarGraph = (canvas, labels, data, label, title) => {
    new Chart(document.getElementById(canvas), {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [{
                label: label,
                data: data,
                fill: true,
                backgroundColor: 'rgba(54, 162, 235, 0.3)',
                borderColor: 'rgba(54, 162, 235, 1)',
                pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#666'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        bottom: 20
                    },
                    color: '#333',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
}
const polarGraph = (canvas, labels, data, title) => {
    // Define un conjunto de colores personalizados
    const colors = [
        'rgba(255, 99, 132, 0.7)', // Color 1
        'rgba(54, 162, 235, 0.7)', // Color 2
        'rgba(255, 206, 86, 0.7)', // Color 3
        'rgba(75, 192, 192, 0.7)', // Color 4
        'rgba(153, 102, 255, 0.7)', // Color 5
        'rgba(255, 159, 64, 0.7)'  // Color 6
    ];

    // Si hay más datos que colores, repite los colores
    const backgroundColors = data.map((_, index) => colors[index % colors.length]);

    new Chart(document.getElementById(canvas), {
        type: 'polarArea',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: backgroundColors,
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        bottom: 20
                    },
                    color: '#333',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'top',
                    labels: {
                        color: '#333',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
    
}
// Función barGraph proporcionada
const barGraph1 = (canvas, xAxis, yAxis, legend, title) => {
    let colors = [];
    xAxis.forEach(() => {
        colors.push('rgba(' + [Math.floor(Math.random() * 255), Math.floor(Math.random() * 255), Math.floor(Math.random() * 255), 0.7].join(',') + ')');
    });

    new Chart(document.getElementById(canvas), {
        type: 'bar',
        data: {
            labels: xAxis,
            datasets: [{
                label: legend,
                data: yAxis,
                backgroundColor: colors,
                borderColor: '#ddd',
                borderWidth: 1,
                barThickness: 24,
                maxBarThickness: 40,
                minBarLength: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        bottom: 20
                    },
                    color: '#333',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
}

const barGraph2 = (canvas, xAxis, yAxis, legend, title) => {
    // Generar colores en formato RGBA
    let colors = xAxis.map(() => {
        return `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`;
    });

    new Chart(document.getElementById(canvas), {
        type: 'bar',
        data: {
            labels: xAxis,
            datasets: [{
                label: legend,
                data: yAxis,
                backgroundColor: colors,
                borderColor: '#ddd',
                borderWidth: 1,
                barThickness: 'flex', // Permite que las barras se ajusten automáticamente
                maxBarThickness: 40,
                minBarLength: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Permitir que el gráfico cambie de forma
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        bottom: 20
                    },
                    color: '#333',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`; // Muestra el valor exacto
                        }
                    }
                },
                legend: {
                    display: false // Desactivar leyenda si no es necesaria
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false // Ocultar líneas de la cuadrícula en el eje X
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
}
const barGraph3 = (canvas, xAxis, yAxis, legend, title) => {
    let colors = [];
    xAxis.forEach(() => {
        colors.push('rgba(' + [Math.floor(Math.random() * 255), Math.floor(Math.random() * 255), Math.floor(Math.random() * 255), 0.7].join(',') + ')');
    });

    new Chart(document.getElementById(canvas), {
        type: 'bar',
        data: {
            labels: xAxis,
            datasets: [{
                label: legend,
                data: yAxis,
                backgroundColor: colors,
                borderColor: '#ddd',
                borderWidth: 1,
                barThickness: 24,
                maxBarThickness: 40,
                minBarLength: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        bottom: 20
                    },
                    color: '#333',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
}

const barGraph4 = (canvas, xAxis, yAxis, legend, title) => {
    let colors = [];
    xAxis.forEach(() => {
        colors.push('rgba(' + [Math.floor(Math.random() * 255), Math.floor(Math.random() * 255), Math.floor(Math.random() * 255), 0.7].join(',') + ')');
    });

    new Chart(document.getElementById(canvas), {
        type: 'bar',
        data: {
            labels: xAxis,
            datasets: [{
                label: legend,
                data: yAxis,
                backgroundColor: colors,
                borderColor: '#ddd',
                borderWidth: 1,
                barThickness: 24,
                maxBarThickness: 40,
                minBarLength: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: {
                        bottom: 20
                    },
                    color: '#333',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)',
                        borderDash: [5, 5]
                    },
                    ticks: {
                        color: '#666',
                        font: {
                            size: 14
                        }
                    }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
}
const barGraph5 = (canvas, xAxis, yAxis, legend, title) => {
    let colors = xAxis.map(() => {
        return `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.7)`;
    });

    new Chart(document.getElementById(canvas), {
        type: 'bar',
        data: {
            labels: xAxis,
            datasets: [{
                label: legend,
                data: yAxis,
                backgroundColor: colors,
                borderColor: '#ddd',
                borderWidth: 1,
                barThickness: 'flex', // Adjusts automatically based on available space
                maxBarThickness: 40,
                minBarLength: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Allow flexible height
            plugins: {
                title: {
                    display: true,
                    text: title,
                    padding: { bottom: 20 },
                    color: '#333',
                    font: { size: 18, weight: 'bold' }
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `${tooltipItem.dataset.label}: ${tooltipItem.raw}`; // Show value on hover
                        }
                    }
                },
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#666', font: { size: 14 } }
                },
                y: {
                    grid: { color: 'rgba(0, 0, 0, 0.1)', borderDash: [5, 5] },
                    ticks: { color: '#666', font: { size: 14 } }
                }
            },
            animation: {
                duration: 1200,
                easing: 'easeInOutExpo'
            }
        }
    });
}
/*
*   Función asíncrona para cerrar la sesión del usuario.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const logOut = async () => {
    // Se muestra un mensaje de confirmación y se captura la respuesta en una constante.
    const RESPONSE = await confirmAction('¿Está seguro de cerrar la sesión?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Petición para eliminar la sesión.
        const DATA = await fetchData(USER_API, 'logOut');
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            sweetAlert(1, DATA.message, true, 'index.html');
        } else {
            sweetAlert(2, DATA.exception, false);
        }
    }
}

/*
*   Función asíncrona para intercambiar datos con el servidor.
*   Parámetros: filename (nombre del archivo), action (accion a realizar) y form (objeto opcional con los datos que serán enviados al servidor).
*   Retorno: constante tipo objeto con los datos en formato JSON.
*/
const fetchData = async (filename, action, form = null) => {
    // Se define una constante tipo objeto para establecer las opciones de la petición.
    const OPTIONS = {};
    // Se determina el tipo de petición a realizar.
    if (form) {
        OPTIONS.method = 'post';
        OPTIONS.body = form;
    } else {
        OPTIONS.method = 'get';
    }
    try {
        // Se declara una constante tipo objeto con la ruta específica del servidor.
        const PATH = new URL(SERVER_URL + filename);
        // Se agrega un parámetro a la ruta con el valor de la acción solicitada.
        PATH.searchParams.append('action', action);
        // Se define una constante tipo objeto con la respuesta de la petición.
        const RESPONSE = await fetch(PATH.href, OPTIONS);
        // Se retorna el resultado en formato JSON.
        return await RESPONSE.json();
    } catch (error) {
        // Se muestra un mensaje en la consola del navegador web cuando ocurre un problema.
        console.log(error);
    }
}

