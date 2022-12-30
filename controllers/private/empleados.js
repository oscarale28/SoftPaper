// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + 'private/usuarios.php?action=';
const ENDPOINT_CARGO = SERVER + 'private/cargo_empleado.php?action=readAll';
const ENDPOINT_AVATAR = SERVER + 'private/avatar.php?action=readAll';
const ENDPOINT_ESTADO = SERVER + 'private/estado_empleado.php?action=readAll';

let from;
let to;
//Configuración de la tabla y paginación-------------------.
const options = {
    "info": false,
    "searching": false,
    "columnDefs": [
        {
            "targets": [2],
            "visible": false,
            "searchable": false
        }
    ],
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

//Columna de la tabla que se esconde y se muestra con el check
const columns = [2];
//Guardamos id de la tabla para usarlo más adelante
let table = '#table-empleados';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    //Método para manejar niveles de usuario
    userLevel(); 

    //Guardamos id de la tabla para usarlo más adelante
    //Metodo en componentes para llenar la tabla e inicializar datatable
    readRows(API_USUARIOS, 'readAll', table, options);
    /*Función para mostrar y ocultar campos de la tabla*/
    document.getElementById('checkTabla').addEventListener('change', function () {
        $(table).DataTable().columns(columns).visible($(this).is(':checked'))
    });
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_USUARIOS, 'readAll', table, options);
}

//Función para refrescar la tabla manualmente al darle click al botón refresh
document.getElementById('limpiar').addEventListener('click', function () {
    reInitTable();
    document.getElementById('buscar-empleado-input').value = "";
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    let estadoColor;
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        if (row.estado_empleado === "Activo") {
            estadoColor = 'estado';
        }
        else if (row.estado_empleado === "Inhabilitado") {
            estadoColor = 'estado4';
        }
        else if(row.estado_empleado === "Bloqueado") {
            estadoColor = 'estado3';
        }
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
            <tr>
            <td class="col-table">
                <div class="nombre-producto"><img
                        src="../../api/images/avatares/${row.imagen_avatar}" alt="">${row.nombres_empleado}
                </div>
            </td>
            <td class="text-center">${row.apellidos_empleado}</td>
            <td class="text-center">${row.alias_empleado}</td>
            <td class="text-center">${row.correo_empleado}</td>
            <td class="text-center">${row.cargo_empleado}</td>
            <td class="text-center"><span id="estado-color" class="${estadoColor}">${row.estado_empleado}</span></td>
            <td data-title="Acciones" class="botones-table">
                <div class="dropdown">
                    <button class=" btn-acciones dropdown-toggle" type="button"
                        id="dropdownMenuButton1" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Acciones
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end animate slideIn"
                        aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" onclick="openUpdate('${row.uuid_empleado}')" data-bs-toggle="modal"
                                type="button"
                                data-bs-target="#modal-agregarEmpl">Editar</a>
                        </li>
                        <li><a class="dropdown-item" onclick="openDelete('${row.uuid_empleado}')" type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-eliminar">Eliminar</a>
                        </li>
                        <li><a class="dropdown-item" onclick="openReport('${row.uuid_empleado}')" type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-reporteEmpl">Reporte</a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows').innerHTML = content;
}

//Método para realizar búsqueda en la tabla mediante el campo de buscador
document.getElementById('buscar-empleado-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('buscar-empleado-input').value == "") {
        sweetAlert(3, 'Campo de búsqueda vacío', null)
    }
    else {
        $(table).DataTable().destroy();
        // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
        searchRows(API_USUARIOS, 'search', 'readAll', 'buscar-empleado-form', 'buscar-empleado-input', table, options);
    }
});

// Función para preparar el modal al momento de insertar un registro.
function openCreate() {
    // Se escribe el título correspondiente de modal
    document.getElementById("modal-empleado-title").innerText = "Añadir empleado";
    //Limpiamos los campos del modal
    document.getElementById('save-form-empleado').reset();
    // Se habilitan los campos de contraseña y alias
    document.getElementById('clave').disabled = false;
    document.getElementById('clave2').disabled = false;
    document.getElementById('alias').disabled = false;
    // Se oculta el select de estado ya que se guardará como activo automáticamente
    document.getElementById('estado-empleado').style.display = 'none';
    document.getElementById('estado-label').style.display = 'none';
    //Ocultamos la imagen del avatar ya que por defecto no aparecerá, solo hasta que se seleccione un avatar
    document.getElementById('imagen-avatar').style.display = 'none'
    /* Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js, 
    * mandar de parametros la ruta de la api de la tabla que utiliza el select, y el id del select*/
    fillSelect(ENDPOINT_CARGO, 'cargo', null);
    fillSelect(ENDPOINT_AVATAR, 'foto', null);
}

// Función para preparar el formulario al momento de modificar un registro.
function openUpdate(id) {
    // Se escribe el título correspondiente de modal
    document.getElementById("modal-empleado-title").innerText = "Editar empleado";
    document.getElementById('col-estado-empleado').classList.remove("input-hide");
    //Limpiamos los campos del modal
    document.getElementById('save-form-empleado').reset();
    // Se vuelve a mostrar el select de estado para poder modificar el dato.
    document.getElementById('estado-empleado').style.display = 'block';
    document.getElementById('estado-label').style.display = 'block';
    // Se esconden los campos de contraseña porque ya no son necesarios.
    document.getElementById('clave-label').style.display = 'none';
    document.getElementById('clave2-label').style.display = 'none';
    document.getElementById('clave').style.display = 'none';
    document.getElementById('clave2').style.display = 'none';
    // Se inhabilitan los campos de contraseña y alias para no poder ser editados
    document.getElementById('clave').disabled = true;
    document.getElementById('clave2').disabled = true;
    document.getElementById('alias').disabled = true;
    // Se guarda el id del registro seleccionado en un campo oculto
    document.getElementById('id').value = id;

    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_USUARIOS + 'readOne', {
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
                    fillSelect(ENDPOINT_AVATAR, 'foto', response.dataset.uuid_avatar);
                    document.getElementById('nombres').value = response.dataset.nombres_empleado;
                    document.getElementById('apellidos').value = response.dataset.apellidos_empleado;
                    document.getElementById('alias').value = response.dataset.alias_empleado;
                    document.getElementById('correo').value = response.dataset.correo_empleado;
                    fillSelect(ENDPOINT_CARGO, 'cargo', response.dataset.uuid_cargo_empleado);
                    fillSelect(ENDPOINT_ESTADO, 'estado-empleado', response.dataset.uuid_estado_empleado);
                    document.getElementById('imagen-avatar').src = `../../resources/img/avatares/${response.dataset.imagen_avatar}`
                    document.getElementById('imagen-avatar').style.display = 'inline-block';
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function openReport(idr) {
    if(Chart.getChart("chart1")){
        // Destroys a specific chart instance
        Chart.getChart("chart1").destroy();
    }
    document.getElementById('idr').value = idr;
    document.getElementById('calendar-range').value = '';
    document.getElementById('title-chart-1').classList.add('input-hide');
    document.getElementById('chart1').classList.add('input-hide');
}

function numeroAdosCaracteres(fecha) {
    if (fecha > 9) {
        return "" + fecha;
    } else {
        return "0" + fecha;
    }
}

flatpickr('#calendar-range', {
    "mode": "range",
    dateFormat: "d-m-Y",
    onChange: function (selectedDates, dateStr, instance) {
        if (selectedDates.length == 2) {
            from = selectedDates[0].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[0].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[0].getDate());

            to = selectedDates[1].getFullYear() + "-" + numeroAdosCaracteres(selectedDates[1].getMonth() + 1) + "-" + numeroAdosCaracteres(selectedDates[1].getDate());
            graficoVentasEmpleado(from, to);
            document.getElementById('chart1').classList.remove('input-hide');
            document.getElementById('title-chart-1').classList.remove('input-hide');
        }
    }
});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de guardar.
document.getElementById('save-form-empleado').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id').value) ? action = 'update' : action = 'create';
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_USUARIOS, action, 'save-form-empleado', 'modal-agregarEmpl');
    reInitTable();
});

function reportDate(start, end) {
    const data = new FormData();
    idr = document.getElementById('idr').value;
    data.append('idr', idr)
    data.append('start-date', start);
    data.append('end-date', end);
    fetch(API_USUARIOS + 'reportDate', {
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
                        let fechaS = '&fechas=' + start;
                        let fechaF = '&fechaf=' + end;
                        param = '?id_empleado=' + document.getElementById('idr').value;
                        // Se establece la ruta del reporte en el servidor.
                        let url = SERVER + 'reports/ventas_empleado_param.php';
                        // Se abre el reporte en una nueva pestaña del navegador web.
                        window.open(url + param + fechaS + fechaF);
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

function graficoVentasEmpleado(start, end) {
    if(Chart.getChart("chart1")){
        // Destroys a specific chart instance
        Chart.getChart("chart1").destroy();
    }
    
    const data = new FormData(document.getElementById("stats-Empl"));
    idr = document.getElementById('idr').value;
    data.append('idr', idr);
    data.append('start-date', start);
    data.append('end-date', end);
    // Petición para obtener los datos del gráfico.
    fetch(API_USUARIOS + "ventasEmpleado", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se remueve la etiqueta canvas.
                if (response.status) {
                    // Se declaran los arreglos para guardar los datos a graficar.
                    let fecha = [];
                    let ventas = [];
                    //Iteramos por cada elemento del array que contiene la semana pasada
                    response.dataset.map(function (row) {
                        // Se agregan los datos a los arreglos.
                        console.log(row.dia)
                        fecha.push(row.fecha_venta);
                        ventas.push(row.ventas);
                    });
                    // Se llama a la función que genera y muestra un gráfico de barras. Se encuentra en el archivo components.js
                    lineGraph("chart1", fecha, ventas, "Ventas por empleado");
                    document.getElementById("title-chart-1").innerText='Venta por empleado';
                } else {
                    document.getElementById("title-chart-1").innerText=response.exception;
                    document.getElementById('report-empl').classList.add('input-hide');
                    console.log(response.exception);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}


// Función para mandar el id de la row seleccionada al modal eliminar-------------------.
function openDelete(id) {
    document.getElementById('uuid_empleado').value = id;
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar-------------------.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario-------------------.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar-------------------.
    confirmDelete(API_USUARIOS, 'delete-form');
    reInitTable();
});

document.getElementById('report-empl').addEventListener('click', function () {
    //Se llama a la función reportDate
    reportDate(from, to);
});

//Función para cambiar y mostrar el avatar dinámicamente en modals-------------------.
function changeAvatar() {
    let combo = document.getElementById('foto')
    let selected = combo.options[combo.selectedIndex].text;
    document.getElementById('imagen-avatar').style.display = 'inline-block'
    document.getElementById('imagen-avatar').src = `../../api/images/avatares/${selected}`;
}


//Método para manejar niveles de usuario
function userLevel() {
	//Evaluamos el nivel de usuario
	if (JSON.parse(localStorage.getItem('levelUser')) != 'Administrador') {
		//Redirigimos cuando no se tenga permiso
		location.href = 'dashboard.html';
		//console.log('No sos admin')
	}
}