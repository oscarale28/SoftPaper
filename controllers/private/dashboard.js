// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_VENTAS = SERVER + "private/venta.php?action=";
const API_PRODUCTOS = SERVER + 'private/productos.php?action=';
const ENDPOINT_PROVEEDOR = SERVER + 'private/proveedor.php?action=readAllReport';
const ENDPOINT_CATEGORIA = SERVER + 'private/categoria.php?action=readAllReport';


let multi = {};
// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener("DOMContentLoaded", function () {
    // Se llaman a la funciones que generan los gráficos en la página web.
    graficoVentasCategoria();
    graficoPastelVentasDia();
    graficoInventarioProveedor();
    graficoLineasVentasSemanales();
    userLevel();
    userLevel();
});

// Función para mostrar el porcentaje de productos por categoría en un gráfico de pastel.
function graficoPastelVentasDia() {
    // Petición para obtener los datos del gráfico.
    fetch(API_VENTAS + 'ventasDiaPorTipo', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a gráficar.
                    let tipo_factura = [];
                    let cantidad = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        tipo_factura.push(row.tipo_factura);
                        cantidad.push(row.cantidad);
                    });
                    // Se llama a la función que genera y muestra un gráfico de pastel. Se encuentra en el archivo components.js
                    doughnutGraph('chart2', tipo_factura, cantidad, 'Ventas por tipo de factura');
                } else {
                    document.getElementById('chart2').remove();
                    document.getElementById('chart2-text').innerText = response.exception;
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function graficoInventarioProveedor() {
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + 'inventarioGProveedor', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a graficar.
                    let proveedores = [];
                    let cantidades = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        proveedores.push(row.nombre_proveedor);
                        cantidades.push(row.count);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    barGraph('inventario-proveedor', proveedores, cantidades, 'Cantidad de Productos');
                } else {
                    document.getElementById('inventario-proveedor').remove();
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function selectProv() {
    /* Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js, 
    * mandar de parametros la ruta de la api de la tabla que utiliza el select, y el id del select*/
    fillSelect(ENDPOINT_PROVEEDOR, 'proveedor', null);
}


document.getElementById('btn-reporte1').addEventListener('click', function () {
    let param;
    param = '?id_proveedor=' + document.getElementById('proveedor').value;
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/inventario_proveedor.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    console.log(url+param);  
    window.open(url + param);
});


// Función para mostrar el porcentaje de productos por categoría en un gráfico de pastel.
function graficoVentasCategoria() {
    // Petición para obtener los datos del gráfico.
    fetch(API_VENTAS + 'ventasCategoriaG', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a gráficar.
                    let categorias = [];
                    let cantidad = [];
                    // Se recorre el conjunto de registros devuelto por la API (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        categorias.push(row.nombre_categoria_p);
                        cantidad.push(row.sum);
                    });
                    // Se llama a la función que genera y muestra un gráfico de pastel. Se encuentra en el archivo components.js
                    pieGraph('ventas-categoria', categorias, cantidad, 'Ventas por categoría');
                } else {
                    document.getElementById('ventas-categoria').remove();
                    document.getElementById('ventas-categoria-title').innerText = response.exception;
                    document.getElementById('btn-reportC').style.display = 'none';
                    
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function getWeek() {
    let today = new Date();
    let fechas = [];
    for (let i = 0; i < 7; i++) {
        fechas.push(today.getDate() - i);
    }
    return fechas.reverse();
}

function selectCategoria() {
    /* Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js, 
    * mandar de parametros la ruta de la api de la tabla que utiliza el select, y el id del select*/
    fillSelect(ENDPOINT_CATEGORIA, 'categoria', null);
}


document.getElementById('btn-reporte-categorias').addEventListener('click', function () {
    let param;
    param = '?id_categoria=' + document.getElementById('categoria').value;
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/ventas_categoria.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    console.log(url+param);  
    window.open(url + param);
});


/*Ingresos totales por dia en la semana*/
function graficoLineasVentasSemanales() {
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + "ventasPorSemana", {
        method: "get",
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a graficar.
                    let dias = getWeek();
                    let cantidades = [];
                    //Iteramos por cada elemento del array que contiene la semana pasada
                    dias.forEach((dia) => {
                        //buscamos elemento que coincida con el día para validar existencia de este en el dataset, en caso de no existir retorna undefined
                        const registro = response.dataset.find((row) => row.Fecha == dia);
                        let total = 0;
                        if (registro != undefined) {
                            total = registro.total;
                        }
                        //const total = registro ? registro.total : 0;
                        cantidades.push(total);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    lineGraph("chart4", dias, cantidades, "Ventas semanales (USD)");
                    console.log(getWeek());
                } else {
                    document.getElementById("chart4").remove();
                    document.getElementById('btn-reportV').style.display = 'none';
                    
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });


    //Petición para obtener la estadistica de ventas totales por semana
    fetch(API_PRODUCTOS + "ventasPorSemanaEstadistica", {
        method: "get",
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    //Le asignamos el valor a la etiqueta del monto
                    if (isNaN(parseFloat(response.dataset.total))) {
                        document.getElementById("e-semana").innerText =
                            "No hay ventas esta semana";
                    } else {
                        document.getElementById("e-semana").innerText = `$${parseFloat(
                            response.dataset.total
                        ).toFixed(2)} en ingresos semanales`;
                    }
                } else {
                    // Se presenta un mensaje de error cuando no existen datos para mostrar.
                    sweetAlert(4, response.exception, null);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}

function numeroAdosCaracteres(fecha) {
    if (fecha > 9) {
        return "" + fecha;
    } else {
        return "0" + fecha;
    }
}

flatpickr('#calendar-range', {
    dateFormat: "Y-m-d",
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 1) {
            from = selectedDates[0].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[0].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[0].getDate());
        }
    }
});

function reportDateV(start) {
    const data = new FormData();
    data.append('start-date', start);
    console.log(start);
    fetch(API_VENTAS + 'reportDateV', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                        let param;
                        param = '?fechaV=' + start;
                        // Se establece la ruta del reporte en el servidor.
                        let url = SERVER + 'reports/venta_semana.php';
                        // Se abre el reporte en una nueva pestaña del navegador web.
                        window.open(url + param);
                        document.getElementById('calendar-range').value = "";
                } else {
                    /* En caso de no encontrar coincidencias, limpiara el campo y se recargará la tabla */
                    sweetAlert(2, response.exception, null);
                    document.getElementById('calendar-range').value = "";
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

document.getElementById('btn-reporteV').addEventListener('click', function () {
    //Se llama a la función reportDate
    reportDateV(from);
});

//Método para manejar niveles de usuario
function userLevel(){
    //Se obtienen todos los elementos que tengan la clase de noadmin, y se transforman en array
    let hideElements = document.querySelectorAll('.noadmin'); // returns NodeList
    let hideArray = [...hideElements]; // converts NodeList to Array
    
    //Evaluamos el nivel de usuario
    if(JSON.parse(localStorage.getItem('levelUser')) != 'Administrador'){
        //Oculatamos cada elemento
        hideArray.forEach(e => {
            //console.log(e);
            e.style.display='none';
        });
        //console.log('No sos admin')
    }
}