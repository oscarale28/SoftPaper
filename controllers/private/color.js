// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_COLOR = SERVER + 'private/color.php?action=';

//Configuración de la tabla
const options = {
    "info": false,
    "searching": false,
    "dom":
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-5'l><'col-sm-1'><'col-sm-6'p>>",
    "language": {
        "lengthMenu": "Mostrando _MENU_ registros",
        "paginate": {
            "next": '<i class="bi bi-arrow-right-short"></i>',
            "previous": '<i class="bi bi-arrow-left-short"></i>'
        }
    },
    "lengthMenu": [[10, 15, 20, -1], [10, 15, 20, "Todos"]]
};
let table = '#table-color';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    //Método para manejar niveles de usuario
    userLevel();

    //Guardamos id de la tabla para usarlo más adelante
    //Metodo en componentes para llenar la tabla e inicializar datatable
    readRows(API_COLOR, 'readAll', table, options);
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_COLOR, 'readAll', table, options);
}

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        let num = 0;
        (row.estado_color_producto) ? num = 0 : num = 1;
        row.estado_color_producto ? (icon = `<buttom onclick="inactiveStateColor('${row.uuid_color_producto}','${num}')" class=" estado " type="button" id="uestado_c" name="uestado_c" >Activo</buttom> `,
        content += `
        <tr>
            <td class="col-table">${row.color_producto}</td>
            <td class="estado-stock">${icon}</td>
            <td class="botones-table">
                <a onclick="openUpdate('${row.uuid_color_producto}')" class="editar"
                    data-bs-toggle="modal" type="button"data-bs-target="#modal-agregarCo">Editar
                </a>
                </div>
            </td>
        </tr>
        `)
         : (icon =`<buttom onclick="activeStateColor('${row.uuid_color_producto}','${num}')" class="  estado3 " type="button" id="uestado_c" name="uestado_c" >Inactivo</buttom>`,
         content += `
        <tr>
            <td class="col-table ">${row.color_producto}</td>
            <td class="estado-stock">${icon}</td>
            <td class="botones-table">
                <button onclick="openUpdate('${row.uuid_color_producto}')" class="editar-disabled border-0 m-0"
                    data-bs-toggle="modal" type="button"data-bs-target="#modal-agregarEP" disabled>Editar
                </button>
                </div>
            </td>
        </tr>
        `);
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows').innerHTML = content;
}

//Función para refrescar la tabla manualmente al darle click al botón refresh
document.getElementById('limpiar').addEventListener('click', function () {
    reInitTable();
    document.getElementById('buscar-color-input').value = "";
});

// Método para realizar búsquedas de registros en la tabla
document.getElementById('buscar-color-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('buscar-color-input').value == "") {
        sweetAlert(3, 'Campo de búsqueda vacío', null)
    }
    else {
        $(table).DataTable().destroy();
        // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
        searchRows(API_COLOR, 'search', 'readAll', 'buscar-color-form', 'buscar-color-input', table, options);
    }
});

function inactiveStateColor(id, num) {

    swal({
        title: 'Advertencia',
        text: '¿Está seguro de inhabilitar este registro? Podría afectar a otros dependientes de él.',
        icon: 'warning',
        buttons: ['No', 'Sí'],
        confirmButtonColor: '#00ACF0',
        closeOnClickOutside: false,
        closeOnEsc: false
    }).then(function (value) {
        // Se verifica si fue cliqueado el botón Sí para hacer la petición de cerrar sesión, de lo contrario se muestra un mensaje.
        if (value) {
            document.getElementById('id_delete').value = (id);
            document.getElementById('num').value = (num);
            // Se evita recargar la página web después de enviar el formulario.
            event.preventDefault();
            //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
            //formulario dentro de nuestro modal eliminar
            change(API_COLOR, 'delete-form');
            reInitTable();
        }
    });
}

function activeStateColor(id, num) {
    document.getElementById('id_delete').value = (id);
    document.getElementById('num').value = (num);
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
    //formulario dentro de nuestro modal eliminar
    change(API_COLOR, 'delete-form');
    reInitTable();
}

// Función para setear los campos del modal para registrar
function openCreate() {
    document.getElementById("agregar-color").reset();
    document.getElementById("modal-colores-title").innerHTML = "Añadir color";
}

// Función para setear los campos del modal para actualizar
function openUpdate(id) {
    document.getElementById("agregar-color").reset();
    document.getElementById("modal-colores-title").innerHTML = "Editar color";
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_COLOR + 'readOne', {
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
                    document.getElementById('id').value = (id);
                    document.getElementById('nombre_color').value = response.dataset.color_producto;

                    if (response.dataset.estado_cliente) {
                        document.getElementById('estado_color').value = 0;
                    } else {
                        document.getElementById('estado_color').value = 1;
                    }
                    // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de guardar.
document.getElementById('agregar-color').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id').value) ? action = 'update' : action = 'create';
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_COLOR, action, 'agregar-color', 'modal-agregarCo');
    reInitTable();
});

function openDelete(id) {
    document.getElementById('id_delete').value = (id);
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
    //No eliminamos solo deshabilita------------------.
    confirmDelete(API_COLOR, 'delete-form');
    reInitTable();
});


//Método para manejar niveles de usuario
function userLevel() {
	//Evaluamos el nivel de usuario
	if (JSON.parse(localStorage.getItem('levelUser')) != 'Administrador') {
		//Redirigimos cuando no se tenga permiso
		location.href = 'dashboard.html';
		//console.log('No sos admin')
	}
}