// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_CLIENTES = SERVER + 'private/clientes.php?action=';
const API_CLIENTES2 = SERVER + 'private/clientes.php?action=';
const ENDPOINT_GIROC = SERVER + 'private/giro.php?action=readAll';
const ENDPOINT_DEPAC = SERVER + 'private/departamento.php?action=readAll';
const ENDPOINT_MUNIC = SERVER + 'private/municipio.php?action=readAllParam';

let depC;

//Configuración de la tabla y paginación-------------------.
const options = {
    "info": false,
    "columnDefs": [
        {
            "targets": [2],
            "visible": false,
            "searchable": false
        },
        {
            "targets": [3],
            "visible": false,
            "searchable": false
        },
        {
            "targets": [5],
            "visible": false
        },
        {
            "targets": [6],
            "visible": false
        }
    ],
    "searching": false,
    "dom":
        "<'row'<'col-sm-12'tr>>" +
        "<'row ms-2'<'col-sm-2 text-center'l><'col-sm-10'p>>",
    "language": {
        "lengthMenu": "Mostrando _MENU_ registros",
        "paginate": {
            "next": '<i class="bi bi-arrow-right-short"></i>',
            "previous": '<i class="bi bi-arrow-left-short"></i>'
        }
    },
    "lengthMenu": [[10, 15, 20, -1], [10, 15, 20, "Todos"]]
};

//Columnas de la tabla que se esconden y se muestran con el check
const columns = [2, 3, 5, 6];
let table = '#table';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    //Método para manejar niveles de usuario
    userLevel();
    //Guardamos id de la tabla para usarlo más adelante
    //Metodo en componentes para llenar la tabla e inicializar datatable
    readRows(API_CLIENTES, 'readAll', table, options);
    /*Función para mostrar y ocultar campos de la tabla*/
    document.getElementById('checkTabla').addEventListener('change', function () {
        $(table).DataTable().columns(columns).visible($(this).is(':checked'))
    });
});

//Método para volver a cargar la tabla después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_CLIENTES, 'readAll', table, options);
}

// Función para llenar la tabla con los datos de los registros. Se manda a llamar en la función readRows().
function fillTable(dataset) {
    let content = '';
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        let num = 0;
        (row.estado_cliente) ? num = 0 : num = 1;
        row.estado_cliente ? (icon = `<buttom onclick="prueba('${row.uuid_cliente}','${num}')" class=" estado " type="button" id="uestado_c" name="uestado_c" >Activo</buttom>`,
        content += `
        <tr>
            <td class="col-table text-center">${row.nombre_cliente}</td>
            <td class="text-center">${row.direccion_cliente}</td>
            <td class="text-center">${row.nombre_municipio}</td>
            <td class="text-center">${row.nrc_cliente}</td>
            <td class="text-center">${row.dui_cliente}</td>
            <td class="text-center">${row.telefono_cliente}</td>
            <td class="text-center">${row.giro_cliente}</td>
            <td class="estado-stock">${icon}</td>
            
            <td class="botones-table">
                <a onclick="openUpdate('${row.uuid_cliente}')" class="editar" 
                    data-bs-toggle="modal" type="button"
                    data-bs-target="#modal-actualizar">Editar
                </a>
        </td>
        </tr>
        `) 
        : (icon =`<buttom onclick="prueba('${row.uuid_cliente}','${num}')" class="  estado3 " type="button" id="uestado_c" name="uestado_c" >Inactivo</buttom>`,
        content += `
        <tr>
            <td class="col-table text-center">${row.nombre_cliente}</td>
            <td class="text-center">${row.direccion_cliente}</td>
            <td class="text-center">${row.nombre_municipio}</td>
            <td class="text-center">${row.nrc_cliente}</td>
            <td class="text-center">${row.dui_cliente}</td>
            <td class="text-center">${row.telefono_cliente}</td>
            <td class="text-center">${row.giro_cliente}</td>
            <td class="estado-stock">${icon}</td>
            
            <td class="botones-table">
                <button class="editar-disabled border-0 m-0" disabled> Editar </button>
            </td>
        </tr>
        `);
        
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.

    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById('tbody-rows').innerHTML = content;
}

document.getElementById('buscar-cliente').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('search').value == "") {
        sweetAlert(3, 'Campo de búsqueda vacío', null)
    }
    else {
        $(table).DataTable().destroy();
        // Se llama a la función que realiza la búsqueda. Se encuentra en el archivo components.js
        searchRows(API_CLIENTES, 'search', 'readAll', 'buscar-cliente', 'search', table, options);
    }
});

//Función para refrescar la tabla manualmente al darle click al botón refresh
document.getElementById('limpiar').addEventListener('click', function () {
    reInitTable();
    document.getElementById('search').value = "";
});

/*Función para mostrar y ocultar cmbs según departamento seleccionado---------*/
function depaOnChange(sel) {
    if (sel.value == "") {
        divC = document.getElementById("municipio");
        divC.style.display = "none";

    } else {
        depC = document.getElementById("departamento_c");
        divC = document.getElementById("municipio");
        divC.style.display = "block";
    }
}

function prueba(id,num) {
    document.getElementById('id_delete').value = (id)
    document.getElementById('num').value = (num)
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del 
    //formulario dentro de nuestro modal eliminar
    change(API_CLIENTES, 'delete-form');
    reInitTable();
}

function openCreate() { 
    document.getElementById("nombre_c").value = "";
    document.getElementById("dui_c").value = "";
    document.getElementById("nit_c").value = "";
    document.getElementById("direccion_c").value = "";
    document.getElementById("telefono_c").value = "";
    document.getElementById("nrc_c").value = "";
    // Se llama a la función para cargar los select---------------------------.
    fillSelect(ENDPOINT_GIROC, 'giro_c', null);
    fillSelect(ENDPOINT_DEPAC, 'departamento_c', null);
    divC = document.getElementById("municipio");
    divC.style.display = "none";
}

document.getElementById("departamento_c").addEventListener("change", function () {
    var selectValue = document.getElementById('departamento_c').value;
    fillSelectDependentM(ENDPOINT_MUNIC, 'municipio_c', null, selectValue);
});

document.getElementById("udepartamento_c").addEventListener("change", function () {
    var selectValue = document.getElementById('udepartamento_c').value;
    fillSelectDependentM(ENDPOINT_MUNIC, 'umunicipio_c', null, selectValue);
});

// Función para crear cliente-------------------.
document.getElementById('agregar-cliente').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    saveRow(API_CLIENTES, 'create', 'agregar-cliente', 'modal-agregar');
    reInitTable();
});

function openUpdate(id) {
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_CLIENTES + 'readOne', {
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
                    divC = document.getElementById("municipio2");
                    divC.style.display = "block";
                    document.getElementById('id').value = (id);
                    document.getElementById('unombre_c').value = response.dataset.nombre_cliente;
                    document.getElementById('udui_c').value = response.dataset.dui_cliente;
                    document.getElementById('unit_c').value = response.dataset.nit_cliente;
                    document.getElementById('udireccion_c').value = response.dataset.direccion_cliente;
                    document.getElementById('utelefono_c').value = response.dataset.telefono_cliente;
                    document.getElementById('unrc_c').value = response.dataset.nrc_cliente;
                    fillSelect(ENDPOINT_GIROC, 'ugiro_c', response.dataset.uuid_giro_cliente);
                    fillSelect(ENDPOINT_DEPAC, 'udepartamento_c', response.dataset.uuid_departamento);
                    fillSelectDependentM(ENDPOINT_MUNIC, 'umunicipio_c', response.dataset.uuid_municipio, response.dataset.uuid_departamento);
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de actualizar.
document.getElementById('update-cliente').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_CLIENTES, 'update', 'update-cliente', 'modal-actualizar');
    reInitTable();
});

function openDeleteCliente(id) {
    document.getElementById('id_delete').value = (id);
}

// Método manejador de eventos que se ejecuta cuando se envía el modal de eliminar.
document.getElementById('delete-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Llamamos al método que se encuentra en la api y le pasamos la ruta de la API y el id del formulario dentro de nuestro modal eliminar
    confirmDelete(API_CLIENTES, 'delete-form');
    reInitTable();
});

//Método para manejar niveles de usuario
function userLevel() {
	//Se obtienen todos los elementos que tengan la clase de noadmin, y se transforman en array
    let hideElements = document.querySelectorAll('.noadmin'); // returns NodeList
    let hideArray = [...hideElements]; // converts NodeList to Array
    
    //Evaluamos el nivel de usuario
    if(JSON.parse(localStorage.getItem('levelUser')) != 'Administrador'){
        //Oculatamos cada elemento
        hideArray.forEach(e => {
            //console.log(e);
            e.style.display='none';
        });
        //console.log('No sos admin')
    }
}