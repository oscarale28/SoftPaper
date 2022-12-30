 // Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + 'private/usuarios.php?action=';
const ENDPOINT_AVATAR = SERVER + 'private/avatar.php?action=readAll';
const ENDPOINT_CARGO = SERVER + 'private/cargo_empleado.php?action=readAll';
let editar = false;

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    //Método para manejar niveles de usuario
    userLevel();
    // Se define una variable para establecer las opciones del componente Modal.
    cargarDatos();
});

function cargarDatos() {
    // Se define un objeto con los datos del registro seleccionado.
    const data = new FormData();
    data.append('id2', id);
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
                    document.getElementById("id").value = response.dataset.uuid_empleado
                    document.getElementById("nombre").innerText = response.dataset.nombres_empleado + " " + response.dataset.apellidos_empleado;
                    document.getElementById("cargo_empleado").innerText = response.dataset.cargo_empleado;
                    const avatarImg = document.getElementById("avatar-info");
                    avatarImg.src = `../../api/images/avatares/${response.dataset.imagen_avatar}`;
                    document.getElementById("correo_empleado").innerText = response.dataset.correo_empleado;
                    document.getElementById("nombre_emp").value = response.dataset.nombres_empleado;
                    document.getElementById("apellido_emp").value = response.dataset.apellidos_empleado;
                    document.getElementById("alias_emp").value = response.dataset.alias_empleado;
                    //Verificamos el si está activado el doble factor de autenticación
                    response.dataset.factor_autenticacion == true ? document.getElementById('factor_autenticacion').setAttribute('checked', "") : document.getElementById('factor_autenticacion').removeAttribute('checked');
                    document.getElementById('factor_autenticacion_value').value = $(document.getElementById('factor_autenticacion')).is(':checked');
                    //console.log(response.dataset.factor_autenticacion);
                    //document.getElementById('factor_autenticacion').value = response.dataset.factor_autenticacion;
                    fillSelect(ENDPOINT_AVATAR, 'foto', response.dataset.uuid_avatar);
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}

// Función ocupada cuando el usuario de click en editar
function openUpdate(id) {
    // Se habilitan campos, botones y aparece el boton de guardar y cancelar.
    document.getElementById('nombre_emp').disabled = false;
    document.getElementById('apellido_emp').disabled = false;
    document.getElementById('alias_emp').disabled = false;
    document.getElementById('foto').disabled = false;
    document.getElementById('btn-guardar').classList.toggle('input-hide');
    document.getElementById('btn-editar').classList.toggle('input-hide');
    document.getElementById('btn-cancelar').classList.toggle('input-hide');
    document.getElementById("btn-chpass").classList.remove('btn-disabled');
    document.getElementById("btn-chpass").classList.add('btn-main');
    document.getElementById("btn-chpass").disabled = false;
    document.getElementById('btn-chpass').classList.toggle('input-hide');

    document.getElementById("factor_autenticacion").disabled = false;
}


// Regresa todos los campos y oculta los botones a como estaban antes.
function cancelEdit(id) {
    document.getElementById('nombre_emp').disabled = true;
    document.getElementById('apellido_emp').disabled = true;
    document.getElementById('alias_emp').disabled = true;
    document.getElementById('btn-guardar').classList.toggle('input-hide');
    document.getElementById('btn-editar').classList.toggle('input-hide');
    document.getElementById('btn-cancelar').classList.toggle('input-hide')
    document.getElementById('foto').disabled = true;
    document.getElementById("btn-chpass").classList.remove('btn-main');
    document.getElementById("btn-chpass").classList.add('btn-disabled');
    document.getElementById("btn-chpass").disabled = true;
    document.getElementById('btn-chpass').classList.toggle('input-hide');
    document.getElementById("factor_autenticacion").disabled = true;
}

function openChangePass() {
    document.getElementById('restaurar_contra').reset();
}

document.getElementById('restaurar_contra').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    saveRow(API_USUARIOS, 'updateC', 'restaurar_contra', 'modal-agregarCP');
});

//Función para cambiar y mostrar el avatar dinámicamente en modals-------------------.
function changeAvatar() {
    let combo = document.getElementById('foto')
    let selected = combo.options[combo.selectedIndex].text;
    document.getElementById('avatar-info').src = `../../api/images/avatares/${selected}`;
}


document.getElementById('profile-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
        // Se llama a la función para guardar el registro. Se encuentra en el archivo components.js
    
        saveRow(API_USUARIOS, 'updateP', 'profile-form', null);
        cargarDatos();
        document.getElementById('nombre_emp').disabled = true;
        document.getElementById('apellido_emp').disabled = true;
        document.getElementById('alias_emp').disabled = true;
        document.getElementById('btn-guardar').classList.toggle('input-hide');
        document.getElementById('btn-editar').classList.toggle('input-hide');
        document.getElementById('btn-cancelar').classList.toggle('input-hide');
        document.getElementById('foto').disabled = true;
        document.getElementById("btn-chpass").classList.remove('btn-main');
        document.getElementById('btn-chpass').classList.toggle('input-hide');
        document.getElementById("btn-chpass").classList.add('btn-disabled');
        document.getElementById("btn-chpass").disabled = true;
        document.getElementById("factor_autenticacion").disabled = true;
        cargarDatos();
    
});



$('#factor_autenticacion').on('change', function() {
    document.getElementById('factor_autenticacion_value').value = $(this).is(':checked');
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