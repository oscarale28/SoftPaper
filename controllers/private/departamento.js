// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_DEPARTAMENTO = SERVER + "private/departamento.php?action=";

//Configuración de la tabla-------------------.
const options = {
    info: false,
    searching: false,
    dom:
        "<'row'<'col-sm-12'tr>>" +
        "<'row'<'col-sm-12 d-flex justify-content-center'p><'col-sm-12 d-flex justify-content-center'l>>",
    language: {
        lengthMenu: "Mostrando _MENU_ registros",
        paginate: {
            next: '<i class="bi bi-arrow-right-short"></i>',
            previous: '<i class="bi bi-arrow-left-short"></i>',
        },
    },
    lengthMenu: [
        [12, 15, 20, -1],
        [12, 15, 20, "Todos"],
    ],
};

//Guardamos id de la tabla para usarlo más adelante
const table = "#departamento";

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener("DOMContentLoaded", function () {
    //Método para manejar niveles de usuario
    userLevel();

    // Se llama a la función que obtiene los registros para llenar la tabla. Se encuentra en el archivo components.js
    readRows(API_DEPARTAMENTO, "readAll", table, options);
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_DEPARTAMENTO, "readAll", table, options);
};

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = "";
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        //Se coloca el nombre de la columna---------------.
        content += `
        <tr>
            <td data-title="Departamento" class="col-table ">${row.nombre_departamento}</td>
            <td data-title="Acciones" class="botones-table">
                <div class="dropdown">
                    <button class=" btn-acciones dropdown-toggle"
                        type="button" id="dropdownMenuButton1"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Acciones
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end animate slideIn"
                        aria-labelledby="dropdownMenuButton1">
                        <li><a onclick="openUpdateDepa('${row.uuid_departamento}')" class="dropdown-item"
                                data-bs-toggle="modal" type="button"
                                data-bs-target="#modal-agregarD">Editar</a>
                        </li>
                        <li><a onclick="openDeleteDepa('${row.uuid_departamento}')" class="dropdown-item"
                                data-bs-toggle="modal" type="button"
                                data-bs-target="#modal-eliminar">Eliminar</a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        `;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById("tbody-rows234").innerHTML = content;
}

function openCreateDepa() {
    // Se limpian los campos, se deshabilita el campo de estado -----------.
    document.getElementById("agregar-depa").reset();
    document.getElementById("modal-departamento-title").innerText = "Añadir departamento";
}

function openUpdateDepa(id) {
    document.getElementById("agregar-depa").reset();
    document.getElementById("modal-departamento-title").innerText =
        "Editar departamento";
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append("id", id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_DEPARTAMENTO + "readOne", {
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
                    document.getElementById("nombre_depa").value =
                        response.dataset.nombre_departamento;
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
    .getElementById("agregar-depa")
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
        saveRow(API_DEPARTAMENTO, action, "agregar-depa", "modal-agregarD");
        reInitTable();
    });

// Función para cargar el id a eliminar
function openDeleteDepa(id) {
    document.getElementById("id_delete_d").value = id;
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
document
    .getElementById("delete-form")
    .addEventListener("submit", function (event) {
        // Se evita recargar la página web después de enviar el formulario.
        event.preventDefault();
        if (!(document.getElementById("id_delete_d").value == "")) {
            //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
            confirmDelete(API_DEPARTAMENTO, "delete-form");
            reInitTable();
        }
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