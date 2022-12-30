// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_MARCA = SERVER + 'private/marca.php?action=';

//Configuración de la tabla-------------------.
const options2 = {
    "info": false,
    "columnDefs": [
        {
            "targets": [0, 3],
            "orderable": false
        }
    ],
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
    "lengthMenu": [[12, 20, -1], [12, 20, "Todos"]]
};
//Guardamos id de la tabla para usarlo más adelante
const table2 = '#marca';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    //Método para niveles de usuario
    userLevel();
    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows2(API_MARCA, 'readAll', table2, options2);
});
//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable2 = () => {
    $(table2).DataTable().destroy();
    readRows2(API_MARCA, 'readAll', table2, options2);
}

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable2(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        let num = 0;
        (row.estado_marca) ? num = 0 : num = 1;
        row.estado_marca ? (icon = `<button onclick="inactivateStateMarca('${row.uuid_marca}','${num}')" class=" estado border-0 m-0" type="button" id="uestado_c" name="uestado_c" >Activo</button> `,
            content += `
        <tr>
            <td data-title="Marca" class="col-table">
                <img src="${SERVER}images/marcas/${row.imagen_marca}"
                    class="imgMP me-3" alt="">
            </td>
            <td data-title="MARCA" class="text-center">${row.nombre_marca}</td>
            <td data-title="estado" class="estado-stock">${icon}</td>
            <td class="botones-table">
              <a onclick="openUpdateMarca('${row.uuid_marca}')" class="editar" 
              data-bs-toggle="modal" type="button"
              data-bs-target="#modal-agregarM">Editar</a>
            </td>
        </tr>
        `) : (icon = `<button onclick="activateStateMarca('${row.uuid_marca}','${num}')" class="  estado3 border-0 m-0" type="button" id="uestado_c" name="uestado_c" >Inactivo</button>`,
            content += `
        <tr>
            <td data-title="Marca" class="col-table">
                <img src="${SERVER}images/marcas/${row.imagen_marca}"
                    class="imgMP me-3" alt="">
            </td>
            <td data-title="MARCA" class="text-center">${row.nombre_marca}</td>
            <td data-title="estado" class="estado-stock">${icon}</td>
            <td class="botones-table">
              <button onclick="openUpdateMarca('${row.uuid_marca}')" class="editar-disabled border-0 m-0" 
              data-bs-toggle="modal" type="button"
              data-bs-target="#modal-agregarM" disabled>Editar</button>
            </td>
        </tr>
        `);
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows').innerHTML = content;
}

function inactivateStateMarca(id, num) {

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
            change(API_MARCA, 'delete-form');
            reInitTable2();
        }
    });
}

function activateStateMarca(id, num) {
    document.getElementById('id_delete1').value = (id)
    document.getElementById('num').value = (num)
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
    //formulario dentro de nuestro modal eliminar
    change(API_MARCA, 'delete-form');
    reInitTable2();
}

document.getElementById('buscar-marca').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('buscar-marca-input').value == "") {
        sweetAlert(3, 'Cambo de búsqueda vacío', null)
    }
    else {
        //Destruimos la instancia de la tabla para volver a crearla con los nuevos datos
        $(table2).DataTable().destroy();
        // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
        searchRows2(API_MARCA, 'search', 'readAll', 'buscar-marca', 'buscar-marca-input', table2, options2);
    }
});

function openCreateMarca() {
    // Se establece que el campo archivo sea obligatorio (input de subir imagen).
    document.getElementById('modal-marca-title').innerText = 'Añadir marca';
    //Se limpian todos los campos del formulario
    document.getElementById('agregar-marca').reset();
}

function openUpdateMarca(id) {
    document.getElementById('modal-marca-title').innerText = 'Editar marca';
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_MARCA + 'readOne', {
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
                    document.getElementById('id1').value = (id);
                    document.getElementById('nombre_marca').value = response.dataset.nombre_marca;
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
document.getElementById('agregar-marca').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    (document.getElementById('id1').value) ? action = 'update' : action = 'create';
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow2(API_MARCA, action, 'agregar-marca', 'modal-agregarM');
    reInitTable2();
});

function openDeleteMarca(id) {
    document.getElementById('id_delete1').value = (id);
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
//No se borra se deshabilita--------------------------.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();//yameestrese
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
    confirmDelete(API_MARCA, 'delete-form');
    reInitTable2();
});

//Función para refrescar la tabla manualmente al darle click al botón refresh
document.getElementById('limpiar-marca').addEventListener('click', function () {
    reInitTable2();
    document.getElementById('buscar-marca-input').value = "";
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