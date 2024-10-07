// Constante para completar la ruta de la API.
const PRODUCTO_API = 'services/admin/producto.php';
const RESERVA_API = 'services/admin/reserva.php';
const USER_API = 'services/admin/administrador.php';

window.addEventListener('popstate', function(event) {
    handleBackButton();
});

async function handleBackButton() {
    try {
        const response = await fetchData(USER_API, 'logOut');
        if (response.status) {
            // Redirigir al index y recargar la página
            window.location.replace('index.html');
        }
    } catch (error) {
        console.error('Error al cerrar sesión:', error);
    }
}

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {

     // Deshabilitar el caché de la página
     window.onpageshow = function(event) {
        if (event.persisted) {
            window.location.reload();
        }
    };
    
    // Verificar si hay una sesión activa
    const DATA = fetchData(USER_API, 'checkSession');
    if (!DATA.session) {
        // Si no hay sesión activa, redirigir al index
        window.location.replace('index.html');
    }

    // Constante para obtener el número de horas.
    const HOUR = new Date().getHours();
    // Se define una variable para guardar un saludo.
    let greeting = '';
    // Dependiendo del número de horas transcurridas en el día, se asigna un saludo para el usuario.
    if (HOUR < 12) {
        greeting = 'Buenos días';
    } else if (HOUR < 19) {
        greeting = 'Buenas tardes';
    } else if (HOUR <= 23) {
        greeting = 'Buenas noches';
    }
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = `${greeting}, bienvenido`;
    // Llamada a las funciones que generan los gráficos en la página web.
    graficoBarrasCategorias();
    graficoPastelCategorias();
    graficoLineasCategorias();
    graficoRadarCategorias();
    graficoPolarCategorias();
    graficoTortaGeneros();
    graficoTortaReservas();
    graficaVentasPrediccion();
    graficoBarrasCategoriasVentas();
    top5ProductosMasVendidos();
    graficoInventarioMarcasyTallas();
    graficoPrediccionAgotamiento();
});

/*
*   Función asíncrona para mostrar un gráfico de barras con la cantidad de productos por categoría.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const graficoBarrasCategorias = async () => {
    const DATA = await fetchData(PRODUCTO_API, 'cantidadProductosCategoria');
    if (DATA.status) {
        let categorias = [];
        let cantidades = [];
        DATA.dataset.forEach(row => {
            categorias.push(row.nombre_categoria);
            cantidades.push(row.cantidad);
        });
        barGraph('chart1', categorias, cantidades, 'Cantidad de productos', 'Cantidad de productos por categoría');
    } else {
        document.getElementById('chart1').remove();
        console.log(DATA.error);
    }
}

/*
*   Función asíncrona para mostrar un gráfico de pastel con el porcentaje de productos por categoría.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const graficoPastelCategorias = async () => {
    const DATA = await fetchData(PRODUCTO_API, 'porcentajeProductosCategoria');
    if (DATA.status) {
        let categorias = [];
        let porcentajes = [];
        DATA.dataset.forEach(row => {
            categorias.push(row.nombre_categoria);
            porcentajes.push(row.porcentaje);
        });
        pieGraph('chart2', categorias, porcentajes, 'Categorías con mayor porcentaje de productos');
    } else {
        document.getElementById('chart2').remove();
        console.log(DATA.error);
    }
}

/*
*   Función asíncrona para mostrar un gráfico de líneas con la cantidad de productos por categoría.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const graficoLineasCategorias = async () => {
    const DATA = await fetchData(PRODUCTO_API, 'descuentosMasUtilizados');
    if (DATA.status) {
        let descuentos = [];
        let cantidades = [];
        DATA.dataset.forEach(row => {
            descuentos.push(row.nombre_descuento);
            cantidades.push(row.cantidad);
        });
        lineGraph('chart3', descuentos, cantidades, 'Cantidad de productos', 'Descuentos más utilizados');
    } else {
        document.getElementById('chart3').remove();
        console.log(DATA.error);
    }
}

/*
*   Función asíncrona para mostrar un gráfico de radar con la cantidad de productos por categoría.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const graficoRadarCategorias = async () => {
    const DATA = await fetchData(PRODUCTO_API, 'marcaMasComprada');
    if (DATA.status) {
        let marcas = [];
        let cantidades = [];
        DATA.dataset.forEach(row => {
            marcas.push(row.marca);
            cantidades.push(row.cantidad);
        });
        radarGraph('chart4', marcas, cantidades, 'Cantidad de productos vendidos', 'Marcas más comprada');
    } else {
        document.getElementById('chart4').remove();
        console.log(DATA.error);
    }
}

/*
*   Función asíncrona para mostrar un gráfico polar con la cantidad de productos por categoría.
*   Parámetros: ninguno.
*   Retorno: ninguno.
*/
const graficoPolarCategorias = async () => {
    const DATA = await fetchData(PRODUCTO_API, 'productosMasVendidosPorCategoria');
    if (DATA.status) {
        let categorias = [];
        let cantidades = [];
        DATA.dataset.forEach(row => {
            categorias.push(row.nombre_categoria);
            cantidades.push(row.cantidad);
        });
        polarGraph('chart5', categorias, cantidades, 'Cantidad de productos vendidos por categoría', 'Productos más vendidos por categoría');
    } else {
        document.getElementById('chart5').remove();
        console.log(DATA.error);
    }
}

const graficoTortaGeneros = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(PRODUCTO_API, 'cantidadProductosGenero');
    
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        let generos = [];
        let cantidades = [];
        
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            generos.push(row.nombre_genero);
            cantidades.push(row.cantidad);
        });
        
        // Llamada a la función para generar y mostrar un gráfico de torta.
        pieGraph('chart6', generos, cantidades, 'Top 5 géneros con mayor cantidad de productos');
    } else {
        document.getElementById('chart6').remove();
        console.log(DATA.error);
    }
}

const graficoTortaReservas = async () => {
    // Petición para obtener los datos del gráfico.
    const DATA = await fetchData(RESERVA_API, 'cantidadReservasEstado');
    
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
    if (DATA.status) {
        let estados = [];
        let cantidades = [];
        
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            estados.push(row.estado_reserva);
            cantidades.push(row.cantidad);
        });
        
        // Llamada a la función para generar y mostrar un gráfico de torta.
        polarGraph('chart7', estados, cantidades, 'Distribución de reservas por estado');
    } else {
        document.getElementById('chart7').remove();
        console.log(DATA.error);
    }
}

const graficaVentasPrediccion = async () => {
    // Obtener los datos de ventas de los ultimos seis meses desde services
    const DATA = await fetchData(PRODUCTO_API, 'ventasUltimosSeisMeses');
    //Se verifica si los datos fueron obtenidos con éxito
    if (DATA.status) {
        let meses = []; //Arreglo para almacenar los nombres de los meses
        let ventas = []; //Arreglo para almacenar las ventas correspondientes a cada mes

        //Orden definido para los meses
        const ordenMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
 
        // Ordenar los datos según el orden de los meses
        DATA.dataset.sort((a, b) => ordenMeses.indexOf(a.mes) - ordenMeses.indexOf(b.mes));
        //Se extrae del dataset y almacena los meses y las ventas
        DATA.dataset.forEach(row => {
            meses.push(row.mes); //Aqui se llama el campo según esta en la base o metódo
            ventas.push(parseFloat(row.ventas_totales));
        });
 
        // Convertir los datos de ventas a un tensor
        const xs = tf.tensor1d(ventas.map((_, i) => i)); //Tensor de indice de los meses
        const ys = tf.tensor1d(ventas); //Tensor de valores de venta
 
       //Se hace la  regresión lineal
        const model = tf.sequential();
        model.add(tf.layers.dense({units: 1, inputShape: [1]}));
 
        model.compile({loss: 'meanSquaredError', optimizer: 'sgd'});
 
        
await model.fit(xs, ys, {epochs: 500});
 
        // Hacer predicciones para los próximos 3 meses
        const numMeses = ventas.length; //Número total de meses en los datos de venta
        let predicciones = []; //Arreglo para almacenar las predicciones
        // Realizar predicciones para los proximos 3 meses, las predicciones comienzan en la ventas del último mes
        for (let i = numMeses; i < numMeses + 3; i++) {
            const prediccion = model.predict(tf.tensor2d([i], [1, 1])); //Predecir ventas para el mes i
            predicciones.push(prediccion.dataSync()[0]); //Se extrae el valor predicho y se añade al arreglo
        }
 
        // Añadir las predicciones a las ventas y los meses
        predicciones.forEach((venta, i) => {
            ventas.push(venta);//Añadir la predicción al arreglo de ventas
            //Se calcula el indice del proximo mes y añadirlo al arreglo de meses
            const ultimoMesIndex = ordenMeses.indexOf(meses[meses.length - 1]);
            const nuevoMesIndex = (ultimoMesIndex + 1 + i) % 12;
            meses.push(ordenMeses[nuevoMesIndex]);
        });
 
        // Crear la gráfica usando chart.js
        const ctx = document.getElementById('chartVentas').getContext('2d');
        new Chart(ctx, {
            type: 'line', //Tipo de gráfica
            data: {
                labels: meses, //   Etiqueta para el x osea meses
                datasets: [{
                    label: 'Ventas reales', //   Etiqueta para la linea de ventas reales
                    data: ventas.slice(0, -3), // Datos de ventas reales aquí se excluyen las predicciones
                    borderColor: 'blue', //Color de la linea de las ventas reales
                    fill: false //No se rellena debajo de la linea
                }, {
                    label: 'Ventas proyectadas', // Etiqueta para la linea de ventas proyectadas
                    data: ventas.slice(-3), // Datos de ventas proyectadas aquí se excluyen las reales
                    borderColor: 'red', //Color de la linea de las ventas proyectadas
                    borderDash: [5, 5], //Linea punteada para diferenciar las proyecciones
                    fill: false //No se rellena
                }]
            },
            options: {
                responsive: true, //Grafico responsivo
                title: {
                    display: true, //Mostrar el titulo de la grafica
                    text: 'Ventas reales y proyectadas' //Texto del titulo
                },
                legend: {
                    position: 'top',//Posicion de la leyenda en la parte superior
                },
                scales: {
                    x: {
                        display: true, //Mostrar el eje x
                        title: {
                            display: true, //Mostrar el titulo del eje x
                            text: 'Mes' //Texto del titulo del eje x
                        }
                    },
                    y: {
                        display: true, //Mostar eje y
                        title: {
                            display: true, //Mostrar el titulo del eje y
                            text: 'Ventas ($)' //Texto del titulo del eje y
                        },
                        beginAtZero: true //Empezar el eje y en 0
                    }
                }
            }
        });
    } else {
        console.log(DATA.error);
    }

}
// Grafico automatico
const graficoBarrasCategoriasVentas = async () => {
    try {
        // Reemplaza 'ventasDiariasPorCategoria' con la función PHP adecuada que no requiere parámetros
        const DATA = await fetchData(PRODUCTO_API, 'ventasDiariasPorCategoria'); // Ajusta el nombre según tu función PHP
        if (DATA.status) {
            let categorias = [];
            let ventas = [];
            DATA.dataset.forEach(row => {
                categorias.push(row.categoria); // Cambiado de 'fecha' a 'categoria'
                ventas.push(row.total_ventas);
            });

            // Utiliza la función barGraph para generar el gráfico de barras
            barGraph1('chart8', categorias, ventas, 'Ventas por categoría', 'Ventas totales por categoría'); // Ajusta el título según corresponda
        } else {
            // Si hay un error, eliminar el gráfico existente y mostrar el mensaje de error
            const chartElement = document.getElementById('chart8');
            if (chartElement) {
                chartElement.remove();
            }
            console.log(DATA.error);
        }
    } catch (error) {
        // Manejo de errores de la llamada fetch
        console.error('Error al obtener los datos del gráfico:', error);
    }
}
// se viene merge-Grafico automatico


const top5ProductosMasVendidos = async () => {
    try {
        // Reemplaza 'productosMasVendidosTop5' con la función PHP adecuada que no requiere parámetros
        const DATA = await fetchData(PRODUCTO_API, 'productosMasVendidosTop5'); // Ajusta el nombre según tu función PHP
        if (DATA.status) {
            let productos = [];
            let ventas = [];
            DATA.dataset.forEach(row => {
                productos.push(row.nombre_producto); 
                ventas.push(row.total_vendido);
            });

            // Utiliza la función barGraph para generar el gráfico de barras
            barGraph2('chart9', productos, ventas, 'Ventas por producto', 'Top 5 productos más vendidos'); // Ajusta el título según corresponda
        } else {
            // Si hay un error, eliminar el gráfico existente y mostrar el mensaje de error
            const chartElement = document.getElementById('chart9');
            if (chartElement) {
                chartElement.remove();
            }
            console.log(DATA.error);
        }
    } catch (error) {
        // Manejo de errores de la llamada fetch
        console.error('Error al obtener los datos del gráfico:', error);
    }
}

const graficoInventarioMarcasyTallas = async () => {
    const DATA = await fetchData(PRODUCTO_API, 'InventarioMarcasyTallas');
    if (DATA.status) {
        let productos = [];
        let cantidades = [];
        DATA.dataset.forEach(row => {
            productos.push(`${row.nombre_producto} (${row.nombre_talla})`); // Captura el nombre del producto junto con la talla
            cantidades.push(row.total_existencias); // Captura la cantidad de productos
        });
        barGraph5('chart10', productos, cantidades, 'Cantidad de productos en inventario', 'Top 5 productos con mayor cantidad de existencias por marca y talla');
    } else {
        document.getElementById('chart10').remove();
        console.log(DATA.error);
    }
};


const graficoPrediccionAgotamiento = async () => {
    // Realiza la solicitud a la API para obtener los datos de la predicción de agotamiento de stock
    const DATA = await fetchData(PRODUCTO_API, 'PrediccionAgotamientoStock');
    
    if (DATA.status) {
        let productos = [];
        let diasParaAgotamiento = [];
        
        // Procesa los datos obtenidos del dataset
        DATA.dataset.forEach(row => {
            productos.push(row.nombre_producto); // Captura los nombres de los productos
            diasParaAgotamiento.push(row.dias_para_agotamiento); // Captura los días para el agotamiento del stock
        });
        
        // Genera el gráfico de barras con los datos procesados
        barGraph('chart12', productos, diasParaAgotamiento, 'Días para agotamiento', '');
    } else {
        document.getElementById('chart12').remove(); // Remueve el gráfico si no hay datos disponibles
        console.log(DATA.error); // Muestra el error en la consola
    }
};







