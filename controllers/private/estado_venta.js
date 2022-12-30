// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_ESTADO_VENTA = SERVER + 'private/estado_venta.php?action=';

const options2 = {
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
const table2 = '#estado_venta';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows2(API_ESTADO_VENTA, 'readAll', table2, options2); 
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable2 = () => {
    $(table2).DataTable().destroy();
    readRows2(API_ESTADO_VENTA, 'readAll', table2, options2);
}

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable2(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        (row.estado_estado_venta) ? num = 0 : num = 1;
        row.estado_estado_venta ? (icon = `<buttom onclick="inactiveStateEV('${row.uuid_estado_venta}','${num}')" class=" estado " type="button" id="uestado_c" name="uestado_c" >Activo</buttom> `,
        content += `
        <tr>
            <td data-title="Proveedor" class="col-table ">${row.estado_venta}</td>
            <td data-title="estado" class="estado-stock">${icon}</td>
            <td data-title="Acciones" class="botones-table">
                <a onclick="openUpdateEV('${row.uuid_estado_venta}')" class="editar"
                    data-bs-toggle="modal" type="button"data-bs-target="#modal-agregarEV">Editar
                </a>
                </div>
            </td>
        </tr>
        `) : (icon = `<buttom onclick="activeStateEV('${row.uuid_estado_venta}','${num}')" class="  estado3 " type="button" id="uestado_c" name="uestado_c" >Inactivo</buttom>`,
        content += `
        <tr>
            <td data-title="Proveedor" class="col-table ">${row.estado_venta}</td>
            <td data-title="estado" class="estado-stock">${icon}</td>
            <td data-title="Acciones" class="botones-table">
                <button onclick="openUpdateEV('${row.uuid_estado_venta}')" class="editar-disabled border-0 m-0"
                    data-bs-toggle="modal" type="button"data-bs-target="#modal-agregarEV" disabled>Editar
                </button>
                </div>
            </td>
        </tr>
        `);
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows').innerHTML = content;
}

function inactiveStateEV(id, num) {

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
            document.getElementById('id_delete1').value = (id)
            document.getElementById('num').value = (num)
            // Se evita recargar la página web después de enviar el formulario.
            event.preventDefault();
            //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
            //formulario dentro de nuestro modal eliminar
            change(API_ESTADO_VENTA, 'delete-form');
            reInitTable2();
        }
    });
}

function activeStateEV(id, num) {
    document.getElementById('id_delete1').value = (id)
    document.getElementById('num').value = (num)
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
    //formulario dentro de nuestro modal eliminar
    change(API_ESTADO_VENTA, 'delete-form');
    reInitTable2();
}

function openCreateEV() {
    document.getElementById("agregar-ev").reset();
    document.getElementById("modal-estadoventa-title").innerText = "Añadir estado de venta";
}

function openUpdateEV(id) {
    document.getElementById("agregar-ev").reset();
    document.getElementById("modal-estadoventa-title").innerText = "Editar estado de venta";
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id-v', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_ESTADO_VENTA + 'readOne', {
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
                    document.getElementById('id-v').value = (id);
                    document.getElementById('nombre_estado_venta').value = response.dataset.estado_venta;
                    if (response.dataset.estado_estado_venta) {
                        document.getElementById('estado_estado_venta').value = 1;
                    } else {
                        document.getElementById('estado_estado_venta').value = 0;
                    }
                    // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos--------------.
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
document.getElementById('agregar-ev').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id-v').value) ? action = 'update' : action = 'create';
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow2(API_ESTADO_VENTA, action, 'agregar-ev', 'modal-agregarEV');
    reInitTable2();
});

function openDeleteEV(id) {
    document.getElementById('id_delete').value = (id);
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
//No se borra se deshabilita--------------------------.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
    confirmDelete(API_ESTADO_VENTA, 'delete-form');
    reInitTable2();
});