// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + 'private/usuarios.php?action=';

// Eventos que se ejecutan cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
// Petición para consultar si existen usuarios registrados.
fetch(API_USUARIOS + 'readUsers', {
    method: 'get'
}).then(function (request) {
    // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
    if (request.ok) {
        request.json().then(function (response) {
            // Se comprueba si existe una sesión, de lo contrario se revisa si la respuesta es satisfactoria.
            if (response.session) {
                location.href = 'dashboard.html';
            } else if (response.status) {
                sweetAlert(4, 'Ingresa el código de verificación', null);
            } else {
                //Mostramos el mensaje y específicamos la página que se abrirá como siguiente paso-------------------.
                sweetAlert(3, response.exception, 'crear.html');
            }
        });
    } else {
        console.log(request.status + ' ' + request.statusText);
    }
});
});

//Función para obtener el sistema del dispositivo en usos
function getOS() {
    var userAgent = window.navigator.userAgent,
        platform = window.navigator.platform,
        macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
        windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
        iosPlatforms = ['iPhone', 'iPad', 'iPod'],
        os = null;

    if (macosPlatforms.indexOf(platform) !== -1) {
        os = 'Mac OS';
    } else if (iosPlatforms.indexOf(platform) !== -1) {
        os = 'iOS';
    } else if (windowsPlatforms.indexOf(platform) !== -1) {
        os = 'Windows';
    } else if (/Android/.test(userAgent)) {
        os = 'Android';
    } else if (!os && /Linux/.test(platform)) {
        os = 'Linux';
    }

    return os;
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de iniciar sesión.
document.getElementById('autentication-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();

    //Obtenemos geolocalización de api externa
    fetch("https://ipinfo.io/json?token=44f591d9c81ec7").then(
        (response) => response.json()
        ).then(
        (jsonResponse) =>{
        //ip del dispostivo
        let ip = jsonResponse.ip;
        //Ciudad donde se encuentra el dispositivo
        let ciudad = jsonResponse.city;
        //formulario a enviar
        const data = new FormData(document.getElementById('autentication-form'));
        //Obtenemos el sistema operativo del dispositivo
        let os = getOS();
        //Mandamos todos los datos necesarios en data hacia la api
        data.append('dispositivo', os);
        data.append('ip', ip);
        data.append('ciudad', ciudad);
        console.log(os);
        console.log(ip);
        console.log(ciudad);
        // Petición para autenticación de doble factor.
        fetch(API_USUARIOS + 'checkVerification', {
            method: 'post',
            body: data,
        }).then(function (request) {
            // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
            if (request.ok) {
                request.json().then(function (response) {
                    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                    if (response.status == 1) {
                        sweetAlert(1, response.message, 'dashboard.html');
                    }
                    //Si status = 2, significa que se equivoco al escribir la contraseña
                    else if(response.status == 2){
                        document.getElementById('autentication-form').reset();
                        sweetAlert(2, response.message, null);
                    }
                    //Si retorna exception significa que el código ya vención y retorna al login
                    else{
                        sweetAlert(2, response.exception, 'index.html');
                    }

                });
            } else {
                console.log(request.status + ' ' + request.statusText);
            }
        });
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
    fetch(API_USUARIOS + 'resendVerificationCode', {
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
