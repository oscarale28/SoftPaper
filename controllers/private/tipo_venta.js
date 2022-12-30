// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_TIPO_VENTA = SERVER + 'private/tipo_venta.php?action=';

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
const table = '#tipo_venta';

document.addEventListener('DOMContentLoaded', function () {
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_TIPO_VENTA, 'readAll', table, options);
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_TIPO_VENTA, 'readAll', table, options);
}

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        let num = 0;
        (row.estado_tipo_venta) ? num = 0 : num = 1;
        row.estado_tipo_venta ? (icon = `<buttom onclick="inactivateStateTV('${row.uuid_tipo_venta}','${num}')" class=" estado " type="button" id="uestado_c" name="uestado_c" >Activo</buttom> `,
        content += `
        <tr>
            <td data-title="Proveedor" class="col-table ">${row.tipo_venta}</td>
            <td data-title="estado" class="estado-stock">${icon}</td>
            <td class="botones-table">
                <a onclick="openUpdateTV('${row.uuid_tipo_venta}')" class="editar"
                    data-bs-toggle="modal" type="button"
                    data-bs-target="#modal-agregarTV">Editar
                </a>
            </td>
        </tr>
        `) : (icon = `<buttom onclick="activateStateTV('${row.uuid_tipo_venta}','${num}')" class="  estado3 " type="button" id="uestado_c" name="uestado_c" >Inactivo</buttom>`,
        content += `
        <tr>
            <td data-title="Proveedor" class="col-table ">${row.tipo_venta}</td>
            <td data-title="estado" class="estado-stock">${icon}</td>
            <td data-title="Acciones" class="botones-table">
                <button onclick="openUpdateTV('${row.uuid_tipo_venta}')" class="editar-disabled border-0 m-0"
                    data-bs-toggle="modal"
                    data-bs-target="#modal-agregarTV" disabled>Editar
                </button>
            </td>
        </tr>
        `);
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows234').innerHTML = content;
}

function inactivateStateTV(id, num) {

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
            change(API_TIPO_VENTA, 'delete-form');
            reInitTable();
        }
    });
}

function activateStateTV(id, num) {
    document.getElementById('id_delete').value = (id)
    document.getElementById('num').value = (num)
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
    //formulario dentro de nuestro modal eliminar
    change(API_TIPO_VENTA, 'delete-form');
    reInitTable();
}


function openCreateTV() {
    document.getElementById("agregar-tv").reset();
    document.getElementById('modal-tipoventa-title').innerText = 'Añadir tipo de venta';
}

function openUpdateTV(id) {
    document.getElementById('modal-tipoventa-title').innerText = 'Editar tipo de venta';
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id-tv', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_TIPO_VENTA + 'readOne', {
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
                    document.getElementById('id-tv').value = (id);
                    document.getElementById('nombre_tipo_venta').value = response.dataset.tipo_venta;
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
document.getElementById('agregar-tv').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id-tv').value) ? action = 'update' : action = 'create';
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_TIPO_VENTA, action, 'agregar-tv', 'modal-agregarTV');
    reInitTable();
});

function openDeleteTV(id) {
    document.getElementById('id_delete').value = (id);
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
//No se borra se deshabilita-----------------------------.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
    confirmDelete(API_TIPO_VENTA, 'delete-form');
    reInitTable();
});