// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_SUBCATEGORIA = SERVER + "private/subcategoria.php?action=";
const ENDPOINT_CATEGORIA = SERVER + "private/categoria.php?action=readAll";

//Configuración de la tabla--------------------.
const options = {
    info: false,
    "columnDefs": [
        {
            "targets": [0, 4],
            "orderable": false
        }
    ],
    searching: false,
    dom:
        "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-5'l><'col-sm-1'><'col-sm-6'p>>",
    language: {
        lengthMenu: "Mostrando _MENU_ registros",
        paginate: {
            next: '<i class="bi bi-arrow-right-short"></i>',
            previous: '<i class="bi bi-arrow-left-short"></i>',
        },
    },
    lengthMenu: [
        [10, 15, 20, -1],
        [10, 15, 20, "Todos"],
    ],
};
let table;
// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener("DOMContentLoaded", function () {
    //Método para manejar niveles de usuario
    userLevel();
    //Guardamos id de la tabla para usarlo más adelante
    table = "#subcategoria";
    //Metodo en componentes para llenar la tabla e inicializar datatable
    readRows(API_SUBCATEGORIA, "readAll", table, options);
});

const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_SUBCATEGORIA, "readAll", table, options);
};

document.getElementById("limpiar").addEventListener("click", function () {
    reInitTable();
    document.getElementById("buscar-subcategoria-input").value = "";
});

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = "";
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        let num = 0;
        row.estado_subcategoria_p ? (num = 0) : (num = 1);
        row.estado_subcategoria_p
            ? (icon = `<buttom onclick="inactiveState('${row.uuid_subcategoria_p}','${num}')" class=" estado " type="button" id="uestado_c" name="uestado_c" >Activo</buttom> `,
                content += `
      <tr>
            <td class="align-items-center">
                <img src="${SERVER}images/subcategoria/${row.imagen_subcategoria}">
            </td>
		    <td data-title="SUBCATEGORÍA" class="text-center">${row.nombre_subcategoria_p}</td>
            <td data-title="Categoría" class="categoria">${row.nombre_categoria_p}</td>
            <td data-title="Estado" class="estado-stock">${icon}</td>
          
            <td class="botones-table">
              <a onclick="openUpdate('${row.uuid_subcategoria_p}')" class="editar" 
              data-bs-toggle="modal" type="button"
              data-bs-target="#modal-agregarSub">Editar</a>
            </td>
      </tr>
      `)
            : (icon = `<buttom onclick="activeState('${row.uuid_subcategoria_p}','${num}')" class="  estado3 " type="button" id="uestado_c" name="uestado_c" >Inactivo</buttom>`,
                content += `
      <tr>
			<td>
			    <img src="${SERVER}images/subcategoria/${row.imagen_subcategoria}">
			</td>
		    <td data-title="PRODUCTO" class="text-center">${row.nombre_subcategoria_p}</td>
            <td data-title="Categoría" class="categoria">${row.nombre_categoria_p}</td>
            <td data-title="Estado" class="estado-stock">${icon}</td>
          
            <td class="botones-table">
              <button onclick="openUpdate('${row.uuid_subcategoria_p}')" class="editar-disabled border-0 m-0" 
              data-bs-toggle="modal" type="button"
              data-bs-target="#modal-agregarSub" disabled>Editar</button>
            </td>
      </tr>
      `);
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        // Se coloca el nombre de la columna de la tabla--------------------.
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById("tbody-rows").innerHTML = content;
}

document
    .getElementById("buscar-subcategoria")
    .addEventListener("submit", function (event) {
        // Se evita recargar la página web después de enviar el formulario.
        event.preventDefault();
        //Validamos el campo vacío
        if (document.getElementById("buscar-subcategoria-input").value == "") {
            sweetAlert(3, "Campo de búsqueda vacío", null);
        } else {
            //Destruimos la instancia de la tabla para volver a crearla con los nuevos datos
            $(table).DataTable().destroy();
            // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
            searchRows(
                API_SUBCATEGORIA,
                "search",
                "readAll",
                "buscar-subcategoria",
                "buscar-subcategoria-input",
                table,
                options
            );
        }
    });

function inactiveState(id, num) {

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
            document.getElementById("id_delete").value = id;
            document.getElementById("num").value = num;
            // Se evita recargar la página web después de enviar el formulario.
            event.preventDefault();
            change(API_SUBCATEGORIA, "delete-form");
            reInitTable();
        }
    });
}

function activeState(id, num) {
    document.getElementById("id_delete").value = id;
    document.getElementById("num").value = num;
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    change(API_SUBCATEGORIA, "delete-form");
    reInitTable();
}

function openCreate() {
    document.getElementById("agregar-sub").reset();
    fillSelect(ENDPOINT_CATEGORIA, "categoria", null);
    document.getElementById("modal-subcategoria-title").innerText = "Añadir subcategoría";
}

function openUpdate(id) {
    document.getElementById("agregar-sub").reset();
    document.getElementById("modal-subcategoria-title").innerText = "Editar subcategoría";
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append("id", id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_SUBCATEGORIA + "readOne", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById("id").value = id;
                    document.getElementById("nombre_sub").value =
                        response.dataset.nombre_subcategoria_p;
                    fillSelect(
                        ENDPOINT_CATEGORIA,
                        "categoria",
                        response.dataset.uuid_categoria_p
                    );
                    // Se actualizan los campos para que las etiquetas (labels) no queden sobre los datos.
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de guardar.
document
    .getElementById("agregar-sub")
    .addEventListener("submit", function (event) {
        // Se evita recargar la página web después de enviar el formulario.
        event.preventDefault();
        // Se define una variable para establecer la acción a realizar en la API.
        let action = "";
        // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
        document.getElementById("id").value
            ? (action = "update")
            : (action = "create");
        // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
        saveRow(API_SUBCATEGORIA, action, "agregar-sub", "modal-agregarSub");
        reInitTable();
    });

function openDelete(id) {
    document.getElementById("id_delete").value = id;
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
document
    .getElementById("delete-form")
    .addEventListener("submit", function (event) {
        // Se evita recargar la página web después de enviar el formulario.
        event.preventDefault();
        //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
        confirmDelete(API_SUBCATEGORIA, "delete-form");
        setTimeout(() => {
            reInitTable();
        }, 100);
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