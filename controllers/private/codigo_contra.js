// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + 'private/usuarios.php?action=';


// Eventos que se ejecutan cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
// Petición para consultar si existen usuarios registrados.

});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de iniciar sesión.
document.getElementById('verify_email').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    fetch(API_USUARIOS + 'verifyCodigo', {
        method: 'post',
        body: new FormData(document.getElementById('verify_email'))
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si existe una sesión, de lo contrario se revisa si la respuesta es satisfactoria.
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status == 1) {
                    sweetAlert(1, 'Codigo de verificacion correcto', 'contra.html');
                } else if(response.status == 2){
                    document.getElementById('verify_email').reset();
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

let n = 20; //300 segundos = 5 minutos
document.getElementById('reenviar_codigo').disabled = true;
var inactivity = window.setInterval(function(){
    //reflejamos el tiempo de manera visual
    document.getElementById('timer_verification_code').innerText = n;
    n--;
    //Si el tiempo llega a 0, se permite reenviar el codigo 1 vez
    if(n ==-1){
        clearInterval(inactivity);
        document.getElementById('reenviar_codigo').disabled = false;
        document.getElementById('reenviar_codigo').classList.remove('btn-disabled');
        document.getElementById('reenviar_codigo').classList.add('btn-cancel');
        document.getElementById('timer_verification_code').style.display = 'none';
    }
},1000) //1000 milisegundos = cada 1 segundos

//Método para reenviar el codigo de verificación
document.getElementById("reenviar_codigo").addEventListener("click", function () {
    //Mostramos el loader
    document.getElementById('spinner').classList.remove('spinner-hide');
    fetch(API_USUARIOS + 'resendVerificationCode2', {
        method: 'get',
    }).then(function (request) {
        //Al obtener respuesta ocultamos el loader
        document.getElementById('spinner').classList.add('spinner-hide');
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    sweetAlert(1, response.message, null);
                    document.getElementById('reenviar_codigo').disabled = true;
                } else {
                    sweetAlert(2, response.exception, 'index.html');
            }});
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
});