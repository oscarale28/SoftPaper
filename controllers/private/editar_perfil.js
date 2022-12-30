// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIO = SERVER + 'private/usuario.php?action=';

const ENDPOINT_CARGO = SERVER + 'private/usuario.php?action=readAll';
const ENDPOINT_AVATAR = SERVER + 'private/usuario.php?action=readAvatar';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Se declara e inicializa un objeto para obtener la fecha y hora actual.
    cargarDatos();
});

// Función para preparar el formulario al momento de modificar un registro.
function openUpdate() {
    //Limpiamos los campos del modal
    document.getElementById('save-form').reset();
    // Se define un objeto con los datos del registro seleccionado.

    // Petición para obtener los datos del registro solicitado.
    fetch(API_USUARIO + 'readOne', {
        method: 'post',
        body: new FormData(document.getElementById('save-form'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById('nombres').value = response.dataset.nombresEmpleado;
                    document.getElementById('apellidos').value = response.dataset.apellidosEmpleado;
                    document.getElementById('correo').value = response.dataset.correoEmpleado;
                    document.getElementById('alias').value = response.dataset.aliasEmpleado;
                    document.getElementById('estado').value = response.dataset.estadoEmpleado;
                    document.getElementById('cargo').value = response.dataset.cargoEmpleado;
                    
                    fillSelect(ENDPOINT_AVATAR, 'foto', response.dataset.avatar);
                    document.getElementById('imagen-avatar').src = `../../resources/img/avatares/avatar${response.dataset.avatar}.jpg`
                    document.getElementById('imagen-avatar').style.display = 'inline-block'
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

function cargarDatos(id) {
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id', id);
    // Petición para obtener los datos del registro solicitado.
    fetch(API_USUARIOS + 'readOnePerfil', {
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
                    // document.getElementById('id').value = response.dataset.id_usuario;
                    document.getElementById('alias_perfil').value = response.dataset.alias_empleado;
                    document.getElementById('estado_perfil').value = response.dataset.estado_empleado;
                    document.getElementById('correo_perfil').value = response.dataset.correo_empleado;
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
document.getElementById('save-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se define una variable para establecer la acción a realizar en la API.
    let action = '';
    // Se comprueba si el campo oculto del formulario esta seteado para actualizar, de lo contrario será para crear.
    action = 'update'
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow3(API_USUARIO, action, 'save-form', 'save-modal');
    cargarUsuario();
});

//Función para cambiar y mostrar el avatar dinámicamente en modals-------------------.
function changeAvatar() {
    let combo = document.getElementById('foto')
    let selected = combo.options[combo.selectedIndex].text;
    document.getElementById('imagen-avatar').style.display = 'inline-block'
    document.getElementById('imagen-avatar').src = `../../resources/img/avatares/${selected}.jpg`;
}
// Función para obtener el detalle del pedido (carrito de compras).
function cargarUsuario() {
    // Petición para solicitar los datos del pedido en proceso.
    fetch(API_USUARIO + 'readOneShow', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById('nombres-empleado').innerText = response.dataset.nombresEmpleado;
                    document.getElementById('apellidos-empleado').innerText = response.dataset.apellidosEmpleado;
                    document.getElementById('correo-empleado').innerText = response.dataset.correoEmpleado;
                    document.getElementById('cargo-empleado').innerText = response.dataset.cargoEmpleado;
                    document.getElementById('estado-empleado').innerText = response.dataset.estadoEmpleado;
                    document.getElementById('alias-empleado').innerText = response.dataset.aliasEmpleado;
                    document.getElementById('avatar-empleado').src = `../../resources/img/avatares/${response.dataset.avatar}.jpg`
                    document.getElementById('avatar-empleado').style.display = 'inline-block'
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}