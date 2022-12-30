// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + 'private/usuarios.php?action=';


// Eventos que se ejecutan cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
// Petición para consultar si existen usuarios registrados.

});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de iniciar sesión.
document.getElementById('verify_email2').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    fetch(API_USUARIOS + 'verifyCodigo2', {
        method: 'post',
        body: new FormData(document.getElementById('verify_email2'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si existe una sesión, de lo contrario se revisa si la respuesta es satisfactoria.
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status == 1) {
                    sweetAlert(1, 'Codigo de verificacion correcto', 'contra.html');
                } else if(response.status == 2){
                    document.getElementById('verify_email2').reset();
                    sweetAlert(2, response.message, null);
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