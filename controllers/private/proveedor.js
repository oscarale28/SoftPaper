// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_PROVEEDOR = SERVER + 'private/proveedor.php?action=';

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
    "lengthMenu": [[11, 15, 20, -1], [11, 15, 20, "Todos"]]
};
//Guardamos id de la tabla para usarlo más adelante
const table = '#proveedor';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_PROVEEDOR, 'readAll', table, options);
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_PROVEEDOR, 'readAll', table, options);
}

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        let num = 0;
        (row.estado_proveedor) ? num = 0 : num = 1;
        row.estado_proveedor ? (icon = `<button onclick="inactiveStateProv('${row.uuid_proveedor}','${num}')" class=" estado border-0 m-0" type="button" id="uestado_c" name="uestado_c" >Activo</button> `,
        content += `
        <tr>
            <td data-title="Proveedor" class="col-table ">${row.nombre_proveedor}</td>
            <td data-title="telefono"
                class="proveedores text-center">
                ${row.telefono_proveedor}</td>
            <td data-title="estado" class="estado-stock">${icon}</td>
            <td class="botones-table">
              <a onclick="openUpdateProv('${row.uuid_proveedor}')" class="editar" 
              data-bs-toggle="modal" type="button"
              data-bs-target="#modal-agregarP">Editar</a>
            </td>
        </tr>
        `) : (icon =`<button onclick="activeStateProv('${row.uuid_proveedor}','${num}')" class="  estado3 border-0 m-0" type="button" id="uestado_c" name="uestado_c" >Inactivo</button>`,
        content += `
        <tr>
            <td data-title="Proveedor" class="col-table ">${row.nombre_proveedor}</td>
            <td data-title="telefono"
                class="proveedores text-center">
                ${row.telefono_proveedor}</td>
            <td data-title="estado" class="estado-stock">${icon}</td>
            <td class="botones-table">
              <button onclick="openUpdateProv('${row.uuid_proveedor}')" class="editar-disabled border-0 m-0" 
              data-bs-toggle="modal" type="button"
              data-bs-target="#modal-agregarP" disabled>Editar</button>
            </td>
        </tr>
        `);
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows234').innerHTML = content;
}

document.getElementById('buscar-proveedor').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('buscar-proveedor-input').value == "") {
        sweetAlert(3, 'Cambo de búsqueda vacío', null)
    }
    else {
        //Destruimos la instancia de la tabla para volver a crearla con los nuevos datos
        $(table).DataTable().destroy();
        // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
        searchRows(API_PROVEEDOR, 'search', 'readAll', 'buscar-proveedor', 'buscar-proveedor-input', table, options);
    }
});

function inactiveStateProv(id, num) {

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
            change(API_PROVEEDOR, 'delete-form');
            reInitTable();
        }
    });
}

function activeStateProv(id,num) {
    document.getElementById('id_delete').value = (id)
    document.getElementById('num').value = (num)
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
    //formulario dentro de nuestro modal eliminar
    change(API_PROVEEDOR, 'delete-form');
    reInitTable();
}

function openCreateProv() {
    // Se limpian los campos, se deshabilita el campo de estado y se cambia el título del modal-----------------.
    //Limpiamos los campos del modal
    document.getElementById('agregar-prov').reset();
    document.getElementById('modal-proveedor-title').innerText = 'Añadir proveedor';
}

function openUpdateProv(id) {
    document.getElementById('modal-proveedor-title').innerText = 'Editar proveedor';
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_PROVEEDOR + 'readOne', {
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
                    document.getElementById('nombre_prov').value = response.dataset.nombre_proveedor;
                    document.getElementById('tele_prov').value = response.dataset.telefono_proveedor;
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
document.getElementById('agregar-prov').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id').value) ? action = 'update' : action = 'create';
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_PROVEEDOR, action, 'agregar-prov', 'modal-agregarP');
    reInitTable();
});

// Función para cargar el id a eliminar
function openDeleteProv(id) {
    document.getElementById('id_delete').value = (id);
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
    confirmDelete(API_PROVEEDOR, 'delete-form');
    reInitTable();
});

//Función para refrescar la tabla manualmente al darle click al botón refresh
document.getElementById('limpiar-proveedor').addEventListener('click', function () {
    reInitTable();
    document.getElementById('buscar-proveedor-input').value = "";
});