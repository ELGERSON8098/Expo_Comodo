// Constante para completar la ruta de la API.
const PRODUCTO_API = 'services/admin/producto.php';
const RESERVA_API = 'services/admin/reserva.php';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', () => {
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
    const DATA = await fetchData(PRODUCTO_API, 'ventasUltimosSeisMeses');
    
    if (DATA.status) {
        let meses = [];
        let ventasReales = [];
        let ventasProyectadas = [];
        let todosLosMeses = [];

        const ordenMeses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

        DATA.dataset.sort((a, b) => ordenMeses.indexOf(a.mes) - ordenMeses.indexOf(b.mes));
        
        DATA.dataset.forEach(row => {
            meses.push(row.mes);
            ventasReales.push(parseFloat(row.ventas_totales));
        });

        // Calcular la diferencia promedio entre meses para proyectar
        let diferencias = ventasReales.slice(1).map((v, i) => v - ventasReales[i]);
        let promedioDiferencia = diferencias.reduce((acc, v) => acc + v, 0) / diferencias.length;

        // Copiar meses y ventas reales
        todosLosMeses = [...meses];
        ventasProyectadas = [...ventasReales];

        // Realizar predicciones para los próximos 3 meses
        for (let i = 0; i < 3; i++) {
            const ultimaVenta = ventasProyectadas[ventasProyectadas.length - 1];
            ventasProyectadas.push(ultimaVenta + promedioDiferencia);
            
            const ultimoMesIndex = ordenMeses.indexOf(todosLosMeses[todosLosMeses.length - 1]);
            const nuevoMesIndex = (ultimoMesIndex + 1) % 12;
            todosLosMeses.push(ordenMeses[nuevoMesIndex]);
        }

        // Crear la gráfica usando chart.js
        const ctx = document.getElementById('chartVentas').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: todosLosMeses,
                datasets: [{
                    label: 'Ventas reales',
                    data: ventasProyectadas.map((v, i) => i < ventasReales.length ? v : null),
                    borderColor: 'blue',
                    fill: false,
                    spanGaps: false
                }, {
                    label: 'Ventas proyectadas',
                    data: ventasProyectadas.map((v, i) => i >= ventasReales.length - 1 ? v : null),
                    borderColor: 'red',
                    borderDash: [5, 5],
                    fill: false,
                    spanGaps: false
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Ventas reales y proyectadas'
                },
                legend: {
                    position: 'top',
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Mes'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Ventas ($)'
                        },
                        beginAtZero: true
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







