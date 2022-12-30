// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_ESTADO_PRODUCTO = SERVER + 'private/estado_producto.php?action=';

//Configuración de la tabla-------------------.
const options = {
    "info": false,
    "searching": false,
    "dom":
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 d-flex justify-content-center'p><'col-sm-12 d-flex justify-content-center'l>>",
    "language": {
        "lengthMenu": "Mostrando _MENU_ registros",
        "paginate": {
            "next": '<i class="bi bi-arrow-right-short"></i>',
            "previous": '<i class="bi bi-arrow-left-short"></i>'
        }
    },
    "lengthMenu": [[10, 15, 20, -1], [10, 15, 20, "Todos"]]
};
//Guardamos id de la tabla para usarlo más adelante
const table = '#estado_producto';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    //Método para manejar niveles de usuario
    userLevel();
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_ESTADO_PRODUCTO, 'readAll', table, options);
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_ESTADO_PRODUCTO, 'readAll', table, options);
}

function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        (row.estado_estado_producto) ? num = 0 : num = 1;
        row.estado_estado_producto ? (icon = `<buttom onclick="inactiveStateEP('${row.uuid_estado_producto}','${num}')" class=" estado " type="button" id="uestado_c" name="uestado_c" >Activo</buttom> `,
        content += `
        <tr>
            <td class="col-table ">${row.estado_producto}</td>
            <td class="estado-stock">${icon}</td>
            <td class="botones-table">
                <a onclick="openUpdateEP('${row.uuid_estado_producto}')" class="editar"
                    data-bs-toggle="modal" type="button"data-bs-target="#modal-agregarEP">Editar
                </a>
                </div>
            </td>
        </tr>
        `) : (icon = `<buttom onclick="activeStateEP('${row.uuid_estado_producto}','${num}')" class="  estado3 " type="button" id="uestado_c" name="uestado_c" >Inactivo</buttom>`,
        content += `
        <tr>
            <td class="col-table ">${row.estado_producto}</td>
            <td class="estado-stock">${icon}</td>
            <td class="botones-table">
                <button onclick="openUpdateEP('${row.uuid_estado_producto}')" class="editar-disabled border-0 m-0"
                    data-bs-toggle="modal" type="button"data-bs-target="#modal-agregarEP" disabled>Editar
                </button>
                </div>
            </td>
        </tr>
        `);
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows234').innerHTML = content;
}

function inactiveStateEP(id, num) {

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
            document.getElementById('id_delete').value = (id)
            document.getElementById('num').value = (num)
            // Se evita recargar la página web después de enviar el formulario.
            event.preventDefault();
            //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
            //formulario dentro de nuestro modal eliminar
            change(API_ESTADO_PRODUCTO, 'delete-form');
            reInitTable();
        }
    });
}

function activeStateEP(id, num) {
    document.getElementById('id_delete').value = (id)
    document.getElementById('num').value = (num)
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
    //formulario dentro de nuestro modal eliminar
    change(API_ESTADO_PRODUCTO, 'delete-form');
    reInitTable();
}

function openCreateEP() {
    document.getElementById("agregar-ep").reset();
    document.getElementById("modal-estadoproducto-title").innerText = "Añadir estado de producto";
}

function openUpdateEP(id) {
    document.getElementById("agregar-ep").reset();
    document.getElementById("modal-estadoproducto-title").innerText = "Editar estado de producto";
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_ESTADO_PRODUCTO + 'readOne', {
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
                    document.getElementById('nombre_estado_producto').value = response.dataset.estado_producto;
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
document.getElementById('agregar-ep').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id').value) ? action = 'update' : action = 'create';
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_ESTADO_PRODUCTO, action, 'agregar-ep', 'modal-agregarEP');
    reInitTable();
});

function openDeleteEP(id) {
    document.getElementById('id_delete').value = (id);
    tableDelete = 1;
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
//No se borra se deshabilita--------------------------.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
    confirmDelete(API_ESTADO_PRODUCTO, 'delete-form');
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