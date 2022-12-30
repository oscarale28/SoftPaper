// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_MUNICIPIO = SERVER + "private/municipio.php?action=";
const ENDPOINT_DEPA = SERVER + "private/departamento.php?action=readAll";

const options2 = {
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
const table2 = "#municipio-table";

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener("DOMContentLoaded", function () {
  readRows2(API_MUNICIPIO, "readAllTable", table2, options2);
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable2 = () => {
  $(table2).DataTable().destroy();
  readRows2(API_MUNICIPIO, "readAllTable", table2, options2);
};

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable2(dataset) {
  let content = "";
  // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
  dataset.map(function (row) {
    // Se crean y concatenan las filas de la tabla con los datos de cada registro.
    content += `
        <tr>
        <td data-title="Municipio" class="col-table ">${row.nombre_municipio}</td>
        <td data-title="Municipio" class="col-table ">${row.nombre_departamento}</td>
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
                    <li><a onclick="openUpdateMuni('${row.uuid_municipio}')" class="dropdown-item"
                            data-bs-toggle="modal" type="button"
                            data-bs-target="#modal-agregarM">Editar</a>
                    </li>
                    <li><a onclick="openDeleteMuni('${row.uuid_municipio}')" class="dropdown-item"
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
  document.getElementById("tbody-rows").innerHTML = content;
}

function openCreateMuni() {
  document.getElementById("agregar-muni").reset();
  document.getElementById("modal-municipio-title").innerText = "Añadir municipio";
  fillSelect(ENDPOINT_DEPA, "depa_muni", null);
}

function openUpdateMuni(id) {
  document.getElementById("modal-municipio-title").innerText = "Editar municipio";
  // Se define un objeto con los datos del registro seleccionado.
  const data = new FormData();
  data.append("id", id);
  // Petición para obtener los datos del registro solicitado.
  fetch(API_MUNICIPIO + "readOne", {
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
          document.getElementById("id1").value = id;
          document.getElementById("nombre_municipio").value =
            response.dataset.nombre_municipio;
          fillSelect(
            ENDPOINT_DEPA,
            "depa_muni",
            response.dataset.uuid_departamento
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
  .getElementById("agregar-muni")
  .addEventListener("submit", function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = "";
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    document.getElementById("id1").value
      ? (action = "update")
      : (action = "create");
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_MUNICIPIO, action, "agregar-muni", "modal-agregarM");
    reInitTable2();
  });

function openDeleteMuni(id) {
  document.getElementById("id_delete_m").value = id;
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
document
  .getElementById("delete-form")
  .addEventListener("submit", function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    if (!(document.getElementById("id_delete_m").value == "")){
      //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
      confirmDelete(API_MUNICIPIO, "delete-form");
      reInitTable2();
    }
  });
