// Constante para establecer la ruta y parámetros de comunicación con la API.
const API = SERVER + 'private/usuarios.php?action=verifyEmail';

// Eventos que se ejecutan cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {

});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de iniciar sesión.
document.getElementById('verify_form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Mostramos el spinner mientras obtenemos respuesta
    document.getElementById('spinner').classList.remove('spinner-hide');
    // Petición para revisar si el administrador se encuentra registrado.
    fetch(API, {
        method: 'post',
        body: new FormData(document.getElementById('verify_form'))
    }).then(function (request) {
        //Ocultamos el loader al obtener respuesta
        document.getElementById('spinner').classList.add('spinner-hide');
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status == 1) {
                    sweetAlert(1, response.message, 'codigo.html');
                }
                else if(response.status == 3){
                    document.getElementById('verify_form').reset();
                    sweetAlert(3, response.exception, null);
                }
                else{
                    sweetAlert(2, response.exception, 'index.html');
                }

            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
});