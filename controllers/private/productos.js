// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_PRODUCTOS = SERVER + 'private/productos.php?action=';
const ENDPOINT_CATEGORIA = SERVER + 'private/categoria.php?action=readAll';
const ENDPOINT_CATEGORIA_FILTER = SERVER + 'private/categoria.php?action=readAllReport';
const ENDPOINT_SUBCATEGORIA = SERVER + 'private/subcategoria.php?action=readAllParam';
const ENDPOINT_MARCA = SERVER + 'private/marca.php?action=readAllSelect';
const ENDPOINT_PROVEEDOR = SERVER + 'private/proveedor.php?action=readAllSelect';
const ENDPOINT_COLOR = SERVER + 'private/color.php?action=readAllSelect';
const ENDPOINT_ESTADO = SERVER + 'private/estado_producto.php?action=readAllSelect';


let from;
let to;
//Configuración de la tabla-------------------.
const options = {
    "info": false,
    "columnDefs": [
        {
            "targets": [0, 9],
            "orderable": false
        },
        {
            "targets": [5],
            "visible": false,
            "searchable": false
        },
        {
            "targets": [6],
            "visible": false
        },
        {
            "targets": [7],
            "visible": false
        }
    ],
    "searching": false,
    "dom":
        "<'row'<'col-sm-12'tr>>" +
        "<'row ms-2'<'col-sm-2 text-center'l><'col-sm-10'p>>",
    "language": {
        "lengthMenu": "Mostrando _MENU_ registros",
        "paginate": {
            "next": '<i class="bi bi-arrow-right-short"></i>',
            "previous": '<i class="bi bi-arrow-left-short"></i>'
        }
    },
    "lengthMenu": [[10, 15, 20, -1], [10, 15, 20, "Todos"]]
};
//Columnas de la tabla que se esconden y se muestran con el check
const columns = [5, 6, 7];
//Guardamos id de la tabla para usarlo más adelante
const table = '#table';
// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    userLevel();
    //Metodo en componentes para llenar la tabla e inicializar datatable
    readRows(API_PRODUCTOS, 'readAll', table, options);
    loadStadictics();
    /*Función para mostrar y ocultar campos de la tabla*/
    document.getElementById('checkTabla').addEventListener('change', function () {
        $(table).DataTable().columns(columns).visible($(this).is(':checked'))
    });
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    loadStadictics();
    readRows(API_PRODUCTOS, 'readAll', table, options);
}

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    let estadoColor;
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        if (row.estado_producto == "En stock") {
            estadoColor = 'estado';
        }
        else if (row.estado_producto == "Cantidad escasa") {
            estadoColor = 'estado2';
        }
        else {
            estadoColor = 'estado3';
        }

        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        // Se coloca el nombre de la columna de la tabla---------------.
        content += `
            <tr>
                <td class="align-items-center">
                    <img src="${SERVER}images/productos/${row.imagen_producto}" alt="">
                </td>
                <td data-title="PRODUCTO" class="text-center">${row.nombre_producto}</td>
                <td data-title="CATEGORIA" class="categoria">${row.nombre_subcategoria_p}</td>
                <td data-title="PRECIO" class="precio">$${row.precio_producto}</td>
                <td data-title="INVENTARIO" class="inventario">${row.stock}</td>
                <td data-title="MARCA" class="marca">${row.nombre_marca}</td>
                <td data-title="PROVEEDOR" class="proveedor">${row.nombre_proveedor}</td>
                <td data-title="DESCRIPCION" class="descripcion">${row.descripcion_producto}</td>
                <td data-title="ESTADO" class="estado-stock"><span id="estado-color" class="${estadoColor}">${row.estado_producto}</span></td>
                <td data-title="Acciones" class="botones-table">
                    <div class="dropdown">
                        <button class=" btn-acciones dropdown-toggle" type="button"
                            id="dropdownMenuButton1" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            Acciones
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end animate slideIn"
                            aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" onclick="openUpdate('${row.uuid_producto}')" type="button"  data-bs-toggle="modal"
                                    data-bs-target="#save-modal">Editar</a>
                            </li>
                            <li><a class="dropdown-item" onclick="openDelete('${row.uuid_producto}')" type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-eliminar">Eliminar</a>
                            </li>
                            <li><a class="dropdown-item" onclick="openStats('${row.uuid_producto}')" type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-stats">Estadísticas</a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>            
		`;

    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody_rows').innerHTML = content;
}

// Función para preparar el modal al momento de insertar un registro.
function openCreate() {
    document.getElementById('modal-producto-title').innerText = 'Añadir producto';
    //Limpiamos los campos del modal
    document.getElementById('save-form').reset();
    //Limpiamos el select dependiente
    subcategoria.length = null;
    //Añadimos la clase que esconde el select estado ya que todos los usuarios ingresados, tendrán el valor de activo y este se manda automaticamente
    document.getElementById('estado').classList.add('input-hide')
    document.getElementById('estado-label').classList.add('input-hide')
    document.getElementById('img-thumbnail').classList.add('input-hide');
    //Activamos el stock
    document.getElementById('stock').disabled = false;
    document.getElementById('update-stock').classList.add('input-hide')
    // Se establece el campo de archivo como requerido.
    document.getElementById('archivo').required = true;
    /* Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js, 
    * mandar de parametros la ruta de la api de la tabla que utiliza el select, y el id del select*/
    fillSelect(ENDPOINT_CATEGORIA, 'categoria', null);
    fillSelect(ENDPOINT_MARCA, 'marca', null);
    fillSelect(ENDPOINT_PROVEEDOR, 'proveedor', null);
    fillSelect(ENDPOINT_COLOR, 'color', null);
}

document.getElementById("categoria").addEventListener("change", function () {
    var selectValue = document.getElementById('categoria').value;
    fillSelectDependent(ENDPOINT_SUBCATEGORIA, 'subcategoria', null, selectValue);
});

// Función para preparar el formulario al momento de modificar un registro.
function openUpdate(id) {
    document.getElementById('modal-producto-title').innerText = 'Editar producto';
    //Limpiamos los campos del modal
    document.getElementById('save-form').reset();
    document.getElementById('id').value = id;
    //Mostramos label y select de estado
    document.getElementById('estado').classList.remove('input-hide')
    document.getElementById('estado-label').classList.remove('input-hide')
    // Se establece el campo de archivo como opcional.
    document.getElementById('archivo').required = false;
    //Desactivamos el stock al actualizar
    document.getElementById('stock').disabled = true;
    document.getElementById('update-stock').classList.remove('input-hide')


    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_PRODUCTOS + 'readOne', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById('nombre').value = response.dataset.nombre_producto;
                    document.getElementById('descripcion').value = response.dataset.descripcion_producto;
                    document.getElementById('precio').value = response.dataset.precio_producto;
                    document.getElementById('stock').value = response.dataset.stock;
                    fillSelect(ENDPOINT_CATEGORIA, 'categoria', response.dataset.uuid_categoria_p);
                    fillSelectDependent(ENDPOINT_SUBCATEGORIA, 'subcategoria', response.dataset.uuid_subcategoria_p, response.dataset.uuid_categoria_p);
                    fillSelect(ENDPOINT_MARCA, 'marca', response.dataset.uuid_marca);
                    fillSelect(ENDPOINT_PROVEEDOR, 'proveedor', response.dataset.uuid_proveedor);
                    fillSelect(ENDPOINT_COLOR, 'color', response.dataset.uuid_color_producto);
                    fillSelect(ENDPOINT_ESTADO, 'estado', response.dataset.uuid_estado_producto);
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

//Funcion de para seleccionar provedor en dashboard
function selectProv() {
    //Limpiamos los campos del modal
    document.getElementById('save-form').reset();
    /* Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js, 
    * mandar de parametros la ruta de la api de la tabla que utiliza el select, y el id del select*/
    fillSelect(ENDPOINT_PROVEEDOR, 'proveedor', null);
}


// Método manejador de eventos que se ejecuta cuando se envía el formulario de guardar.
document.getElementById('save-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id').value) ? action = 'update' : action = 'create';
    //Se activa el campo de stock para poder mandarlo al servidor, si está desactivado no se puede mandar
    document.getElementById('stock').disabled = false;
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_PRODUCTOS, action, 'save-form', 'save-modal');
    //Se desactiva el campo de stock porque ya se utilizo
    document.getElementById('stock').disabled = true;
    reInitTable();
});

//Función para general thumbnail de imagen en el input file------------------.
function previewFile() {
    var preview = document.getElementById('img-thumbnail');
    var file = document.querySelector('input[type=file]').files[0];
    var reader = new FileReader();

    if (preview.classList.contains('input-hide')) {
        preview.classList.remove('input-hide');
    }

    reader.onloadend = function () {
        preview.src = reader.result;
    }

    if (file) {
        reader.readAsDataURL(file);
    } else {
        preview.src = "";
    }
}


//Función para cambiar el stock al cambiar de color en el select-----------------------.
document.getElementById("color").addEventListener("change", function () {
    if (!document.getElementById("id").value.length == 0) {
        let selectValue = document.getElementById('color').value;
        console.log(selectValue);
        setStock();
    }
});

//Funcion para asignar el atributo max del input max dinámicamente----------------------------------.
function setStock() {
    let input = document.getElementById("stock");
    // Petición para obtener los datos del producto solicitado.
    fetch(API_PRODUCTOS + 'readStock', {
        method: 'post',
        body: new FormData(document.getElementById('save-form'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    //Le asignamos el valor del stock del color y producto seleccionado
                    input.value = parseInt(response.dataset.stock);
                    if (response.dataset.stock === 0) {
                        sweetAlert(4, 'No hay stock del producto en el color seleccionado', null);
                    }
                } else {
                    // Se presenta un mensaje de error cuando no existen datos para mostrar.
                    sweetAlert(4, response.exception, null);
                    input.value = 0;
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

//Funcion para suma el stock al producto--------------------.
let sumarStock = () => {
    let nuevo = parseInt(document.getElementById("stock-nuevo").value);
    let stock = parseInt(document.getElementById("stock").value);

    if (!isNaN(nuevo)) {
        let stockActualizado = stock + nuevo;
        document.getElementById("stock-nuevo").value = "";
        document.getElementById("stock").value = stockActualizado;
    }


}

//Funcion para restar stock al producto---------------------.
let restarStock = () => {
    let nuevo = parseInt(document.getElementById("stock-nuevo").value);
    let stock = parseInt(document.getElementById("stock").value);

    if (!isNaN(nuevo)) {
        if ((stock - nuevo) < 0) {
            alert("No se puede tener stock en negativo")
            document.getElementById("stock-nuevo").value = "";
        }
        else {
            let stockActualizado = stock - nuevo;
            document.getElementById("stock-nuevo").value = "";
            document.getElementById("stock").value = stockActualizado;
        }
    }
}

// Función para mandar el id de la row seleccionada al modal eliminar.
function openDelete(id) {
    document.getElementById('id-delete').value = id;
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
    confirmDelete(API_PRODUCTOS, 'delete-form');
    reInitTable();
});


// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar-----------------------.
document.getElementById('search-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('search').value == "") {
        sweetAlert(3, 'Campo de búsqueda vacío', null);
        reInitTable();
    }
    else {
        //Destruimos la instancia de la tabla para volver a crearla con los nuevos datos
        $(table).DataTable().destroy();
        // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
        searchRows(API_PRODUCTOS, 'search', 'readAll', 'search-form', 'search', table, options);
    }
});



//Función para refrescar la tabla manualmente al darle click al botón refresh
document.getElementById('limpiar').addEventListener('click', function () {
    reInitTable();
    document.getElementById('search').value = "";
});

document.getElementById('filter-btn').addEventListener('click', function () {
    fillSelect(ENDPOINT_CATEGORIA_FILTER, 'filter-categoria', null);
    fillSelect(ENDPOINT_ESTADO, 'filter-estado', null);
});

document.getElementById('filter-form').addEventListener('submit', function (event) {
    event.preventDefault();
    let valCategoria = document.getElementById('filter-categoria').value;
    let valEstado = document.getElementById('filter-estado').value;
    if (valCategoria == "Seleccione una opción" || valEstado == "Seleccione una opción") {
        sweetAlert(3, 'No se han seleccionado opciones para filtrar', null)
    }
    else {
        //Destruimos la tabla para crear la nueva con los datos que buscamos
        $(table).DataTable().destroy();
        readRowsFilter(API_PRODUCTOS, 'filter-form', table, options);
    }
});

function loadStadictics() {

    // Petición para obtener los datos del registro solicitado.
    fetch(API_PRODUCTOS + 'readStadistics', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.  
                    document.getElementById('stadistic-total').innerText = response.dataset.total;
                    document.getElementById('stadistic-agotados').innerText = response.dataset.agotados;
                    document.getElementById('stadistic-existencias').innerText = response.dataset.existencias;
                    document.getElementById('stadistic-categorias').innerText = response.dataset.categorias;
                } else {
                    sweetAlert(3, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

/*Reportes y gráficas */
//Método para el input de rango de fecha
flatpickr('#calendar-range', {
    "mode": "range",
    dateFormat: "d-m-Y",
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 2) {
            from = selectedDates[0].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[0].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[0].getDate());

            to = selectedDates[1].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[1].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[1].getDate());
            
            console.log(from);
            console.log(to);
            graficoLineasVentasSemanales(from, to);
            graficoFlujoStock(from, to);
            graficoVentaProducto(from, to);
            document.getElementById('chart1-report').classList.remove('input-hide');
            document.getElementById('title-chart1').classList.remove('input-hide');
            document.getElementById('chart-stock-report').classList.remove('input-hide');
            document.getElementById('title-chart-stock').classList.remove('input-hide');
            document.getElementById('chart-ventas-report').classList.remove('input-hide');
            document.getElementById('title-chart-ventas').classList.remove('input-hide');
            //Acción a realizar al seleccionar rango de fechas (mostrar los gráficos con sus botones para generar sus respectivos reportes)

        }



    }
});

function numeroAdosCaracteres(fecha) {
    if (fecha > 9) {
        return "" + fecha;
    } else {
        return "0" + fecha;
    }
}


// Función para mandar el id de la row seleccionada al modal eliminar.
function openStats(id) {
    document.getElementById('stats-form').reset();
    document.getElementById('id-stats').value = id;
    if(Chart.getChart("chart1")){
        Chart.getChart("chart1").destroy();
    }
    if (Chart.getChart("chart-stock")) {
        Chart.getChart("chart-stock").destroy();
    }
    if(Chart.getChart("chart-ventas")){
        Chart.getChart("chart-ventas").destroy();
    }
    document.getElementById('chart1-report').classList.add('input-hide');
    document.getElementById('chart-stock-report').classList.add('input-hide');
    document.getElementById('chart-ventas-report').classList.add('input-hide');
    document.getElementById('title-chart1').classList.add('input-hide');
    document.getElementById('title-chart-stock').classList.add('input-hide');
    document.getElementById('title-chart-ventas').classList.add('input-hide');
    
}


function getWeek() {
    let today = new Date();
    let fechas = [];
    for (let i = 0; i < 7; i++) {
        fechas.push(today.getDate() - i);
    }
    return fechas.reverse();
}

/*Graficos*/
/*Flujo de precios*/
function graficoLineasVentasSemanales(start, end) {
    if(Chart.getChart("chart1")){
        // Destroys a specific chart instance
        Chart.getChart("chart1").destroy();
    }
    
    const data = new FormData(document.getElementById("stats-form"));
    data.append('start-date', start);
    data.append('end-date', end);
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + "flujoPrecio", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a graficar.
                    let dias = [];
                    let cantidades = [];
                    //Iteramos por cada elemento del array que contiene la semana pasada
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        dias.push(row.dia + '/' +row.mes);
                        cantidades.push(row.precio_producto);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    lineGraph("chart1", dias, cantidades, "Flujo del precio (USD)");
                    document.getElementById("title-chart1").innerText='Flujo del precio (USD)';
                } else {
                    document.getElementById("title-chart1").innerText=response.exception;
                    document.getElementById('chart1-report').classList.add('input-hide');
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}

/*Flujo de ingresos de stock*/
function graficoFlujoStock(start, end) {
    if(Chart.getChart("chart-stock")){
        // Destroys a specific chart instance
        Chart.getChart("chart-stock").destroy();
    }
    
    const data = new FormData(document.getElementById("stats-form"));
    data.append('start-date', start);
    data.append('end-date', end);
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + "flujoStock", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a graficar.
                    let dias = [];
                    let cantidades = [];
                    //Iteramos por cada elemento del array que contiene la semana pasada
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        //console.log(row.dia)
                        dias.push(row.dia + '/' +row.mes);
                        cantidades.push(row.stock_agregado);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    lineGraph("chart-stock", dias, cantidades, "Flujo de ingresos de stock");
                    document.getElementById("title-chart-stock").innerText='Flujo de ingresos de stock';
                } else {
                    document.getElementById("title-chart-stock").innerText=response.exception;
                    document.getElementById('chart-stock-report').classList.add('input-hide');
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}

/*Flujo Ventas de un producto*/
function graficoVentaProducto(start, end) {
    if(Chart.getChart("chart-ventas")){
        // Destroys a specific chart instance
        Chart.getChart("chart-ventas").destroy();
    }
    
    const data = new FormData(document.getElementById("stats-form"));
    data.append('start-date', start);
    data.append('end-date', end);
    // Petición para obtener los datos del gráfico.
    fetch(API_PRODUCTOS + "flujoVentas", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a graficar.
                    let fechas = [];
                    let cantidades = [];
                    //Iteramos por cada elemento del array que contiene la semana pasada
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        fechas.push(row.fecha_venta + '/' + row.mes);
                        cantidades.push(row.cantidad);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    lineGraph("chart-ventas", fechas, cantidades, "Flujo de Ventas de un Producto");
                    document.getElementById("title-chart-ventas").innerText='Flujo de Ventas de un Producto';
                } else {
                    document.getElementById("title-chart-ventas").innerText=response.exception;
                    document.getElementById('chart-ventas-report').classList.add('input-hide');
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}


document.getElementById('chart1-report').addEventListener('click', function () {
    let params;
    params = '?uuid_producto=' + document.getElementById('id-stats').value + '&start=' + from + '&end=' + to;
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/flujo_precio.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    console.log(url + params);  
    window.open(url + params);
});

document.getElementById('chart-stock-report').addEventListener('click', function () {
    let params;
    params = '?uuid_producto=' + document.getElementById('id-stats').value + '&start=' + from + '&end=' + to;
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/flujo_stock.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    console.log(url + params);  
    window.open(url + params);
});

document.getElementById('chart-ventas-report').addEventListener('click', function () {
    let params;
    params = '?uuid_producto=' + document.getElementById('id-stats').value + '&start=' + from + '&end=' + to;
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/flujo_ventas.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    console.log(url + params);  
    window.open(url + params);
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