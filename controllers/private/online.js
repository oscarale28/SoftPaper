/*
*   Controlador de uso general en las páginas web del sitio privado cuando se ha iniciado sesión.
*   Sirve para manejar las plantillas del encabezado y pie del documento.
*/

// Constante para establecer la ruta y parámetros de comunicación con la API.
const API = SERVER + 'private/usuarios.php?action=';

// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
    // Petición para obtener en nombre del usuario que ha iniciado sesión.
    fetch(API + 'getUser', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se revisa si el usuario está autenticado, de lo contrario se envía a iniciar sesión.
                if (response.session) {
                    // Se comprueba si la respuesta es satisfactoria, de lo contrario se direcciona a la página web principal.
                    if (response.status) {
                        const avatarImg = document.getElementById("avatar-user")
                        avatarImg.src = `../../api/images/avatares/${response.avatar}`;
                    } else {
                        sweetAlert(3, response.exception, 'index.html');
                    }
                } else {
                    location.href = 'index.html';
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });

    getUserLevel();
});



let n = 3000; //300 segundos = 5 minutos
//Seteamos intervalo de tiempo a la ventana
var inactivity = window.setInterval(function(){
    //Cada vez que el mouse se mueva, se resetea el tiempo que se puede estar inactivo
    document.onmousemove = function(){
        n = 3000;
    };
    //Cada vez que se use el teclado se resetea el tiempo que se puede estar inactivo
    document.onkeydown = function(){
        n = 3000;
    };
    //Restamos por cada segundo pasado
    n--;
    //console.log(n);
    //Si el tiempo llega a 0, se cierra sesión automáticamente
    if(n ==0){
        fetch(API + 'logOut', {
            method: 'get'
        }).then(function (request) {
            // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
            if (request.ok) {
                // Se obtiene la respuesta en formato JSON.
                request.json().then(function (response) {
                    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                    if (response.status) {
                        //Variable para verificar en el login si se cerró sesión por inactividad
                        sessionStorage.setItem('inactividad',JSON.stringify(true));
                        location.href = 'index.html';
                    } else {
                        sweetAlert(2, response.exception, null);
                    }
                });
            } else {
                console.log(request.status + ' ' + request.statusText);
            }
        });
    }
},1000) //1000 milisegundos = cada 1 segundos

//Permisos de usuario, se obtiene el cargo y se setea en una variable de localStorage
function getUserLevel(){
    fetch(API + 'getUserLevel', {
        method: 'get'
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    //Variable para verificar en el login si se cerró sesión por inactividad
                    localStorage.setItem('levelUser',JSON.stringify(response.cargo));
                    console.log(JSON.parse(localStorage.getItem('levelUser')));
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
}
