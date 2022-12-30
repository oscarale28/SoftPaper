const API_VENTAS = SERVER + 'private/venta.php?action=';
const API_VENTAS_DAY = SERVER + 'private/venta_dia.php?action=';
const ENDPOINT_TIPOVENTA = SERVER + 'private/tipo_venta.php?action=readAll';
const ENDPOINT_ESTADO = SERVER + 'private/estado_venta.php?action=readAll';

const options = {
    "info": false,
    "columnDefs": [
        {
            "targets": [2],
            "visible": false,
            "searchable": false
        },
        {
            "targets": [6],
            "visible": false
        }
    ],
    "searching": false,
    "dom":
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-2 text-center'l><'col-sm-10'p>>",
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
const columns = [2, 6];
let table;

flatpickr('#calendar-range', {
    "mode": "range",
    dateFormat: "Y-m-d",
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 2) {
            var from = selectedDates[0].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[0].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[0].getDate());
            var to = selectedDates[1].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[1].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[1].getDate());
            console.log(from);
            console.log(to);
            
            filterDate(from, to);
            $(table).DataTable().destroy();
            setTimeout(() => {
                /*Inicializando y configurando tabla*/
                table = '#table-ventas';

                /*Función para mostrar y ocultar campos de la tabla*/
                document.getElementById('checkTabla').addEventListener('change', function () {
                    $(table).DataTable().columns(columns).visible($(this).is(':checked'))
                });

            }, 300);
            

            // interact with selected dates here
        }




    }
});

document.addEventListener('DOMContentLoaded', function () {
    userLevel();
    //Guardamos id de la tabla para usarlo más adelante
    table = '#table-ventas';
    //Metodo en componentes para llenar la tabla e inicializar datatable
    readRows(API_VENTAS, 'readAll', table, options);
    /*Función para mostrar y ocultar campos de la tabla*/
    document.getElementById('checkTabla').addEventListener('change', function () {
        $(table).DataTable().columns(columns).visible($(this).is(':checked'))
    }, 300);
});

function numeroAdosCaracteres(fecha) {
    if (fecha > 9) {
        return "" + fecha;
    } else {
        return "0" + fecha;
    }
}

const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_VENTAS, 'readAll', table, options);
}

document.getElementById('limpiar').addEventListener('click', function () {
    reInitTable();
    document.getElementById('search').value = "";
});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de agregar un producto al carrito.
function crearVenta() {
    // Petición para agregar un producto al pedido.
    fetch(API_VENTAS + 'startOrder', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se constata si el cliente ha iniciado sesión.
                if (response.status) {
                    switch (response.status) {
                        case 1:
                            console.log(response.message);
                            console.log(response.dataset.uuid_venta);
                            location.href = `detalle_venta.html?uuid_venta=${response.dataset}`;
                            sessionStorage.setItem('ventaRestaurada',JSON.stringify(false));
                            break;
                        //Se necesita renovar la contraseña
                        case 2:
                            console.log(response.message);
                            console.log(response.dataset.uuid_venta);
                            location.href = `detalle_venta.html?uuid_venta=${response.dataset}`;
                            sessionStorage.setItem('ventaRestaurada',JSON.stringify(true));
                            break;
                    }
                } else {
                    console.log('Todo mal master: ' + response.exception)
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de agregar un producto al carrito.
function crearVentaDay() {
    // Petición para agregar un producto al pedido.
    fetch(API_VENTAS_DAY + 'startOrder', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se constata si el cliente ha iniciado sesión.
                if (response.status) {
                    // sweetAlert(1, response.message, 'carrito.html');
                    console.log(response.message);
                    console.log(response.dataset);
                    location.href = `detalle_venta_dia.html?uuid_venta=${response.dataset}`
                } else {
                    console.log('Todo mal master: ' + response.message)
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    })
}


function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {

        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
            <tr>
                <td class="correlativo">${row.correlativo_venta}</td>
                <td class="tipo">${row.tipo_venta}</td>
                <td class="tipo">${row.tipo_factura}</td>
                <td class="cliente">${row.nombre_cliente}</td>
                <td class="fecha">${row.fecha_venta}</td>
                <td class="monto">$${row.monto}</td>
                <td class="empleado">${row.nombres_empleado} ${row.apellidos_empleado}</td>
                <td class="estado-stock"><span class="estado">${row.estado_venta}</span></td>
            
        </tr>  
		`;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody_rows').innerHTML = content;
}

document.getElementById('search-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('search').value == "") {
        sweetAlert(3, 'Campo de búsqueda vacío', null)
    }
    else {
        //Destruimos la instancia de la tabla para volver a crearla con los nuevos datos
        $(table).DataTable().destroy();
        // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
        searchRows(API_VENTAS, 'search', 'readAll', 'search-form', 'search', table, options);
    }
});

document.getElementById('filter-btn').addEventListener('click', function () {
    fillSelect(ENDPOINT_TIPOVENTA, 'filter-tipo-venta', null);
    fillSelect(ENDPOINT_ESTADO, 'filter-estado', null);
});

document.getElementById('filter-form').addEventListener('submit', function (event) {
    event.preventDefault();
    let valCategoria = document.getElementById('filter-tipo-venta').value;
    let valEstado = document.getElementById('filter-estado').value;
    if (valCategoria == "Seleccione una opción") {
        sweetAlert(3, 'No se han seleccionado opciones para filtrar', null)
    }
    else if (valEstado == "Seleccione una opción") {
        sweetAlert(3, 'No se han seleccionado opciones para filtrar', null)
    }
    else {
        $(table).DataTable().destroy();
        readRowsFilter(API_VENTAS, 'filter-form', table, options);
        setTimeout(() => {
            /*Inicializando y configurando tabla*/
            table = '#table-ventas';

            /*Función para mostrar y ocultar campos de la tabla*/
            document.getElementById('checkTabla').addEventListener('change', function () {
                $(table).DataTable().columns(columns).visible($(this).is(':checked'))
            });

        }, 300);
    }
});

function filterDate(start, end) {
    const data = new FormData();
    data.append('start-date', start);
    data.append('end-date', end);
    fetch(API_VENTAS + 'filterDate', {
        method: 'post',
        body: data
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se envían los datos a la función del controlador para que llene la tabla en la vista y se muestra un mensaje de éxito.
                    fillTable(response.dataset);
                    //sweetAlert(1, response.message, null);
                } else {
                    /* En caso de no encontrar coincidencias, limpiara el campo y se recargará la tabla */
                    sweetAlert(2, response.exception, null);
                    readRows(API_VENTAS);
                    document.getElementById('calendar-range').value = "";
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}



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
