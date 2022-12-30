// Constante para establecer la ruta y parámetros de comunicación con la API.
const API_USUARIOS = SERVER + 'private/usuarios.php?action=';

// Eventos que se ejecutan cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', function () {
// if para verificar conexión a internat
    if(navigator.onLine) {
        let validarInactividad = JSON.parse(sessionStorage.getItem('inactividad'));
        console.log(validarInactividad);
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
                        if (validarInactividad){
                            sessionStorage.setItem('inactividad',JSON.stringify(false));
                            sweetAlert(4, 'Se cerró su sesión por inactividad', null);
                        }
                        else{
                            sessionStorage.setItem('inactividad',JSON.stringify(false));
                            sweetAlert(4, 'Debe autenticarse para ingresar', null);
                        }
                    } else {
                        //Mostramos el mensaje y específicamos la página que se abrirá como siguiente paso-------------------.
                        sweetAlert(3, response.exception, 'crear.html');
                    }
                });
            } else {
                console.log(request.status + ' ' + request.statusText);
            }
        });
    } else {
        //sweetAlert(2, 'No está conectado a internet', null);
        alert('No hay internet');
        window.location.reload();
    }

});

// Variable contador de intentos fallidos de contraseña ------------------------------------------------.
let loginAttempts = 3;
// Método manejador de eventos que se ejecuta cuando se envía el formulario de iniciar sesión.
document.getElementById('login-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Petición para revisar si el administrador se encuentra registrado.
    //Mostramos el spinner mientras obtenemos respuesta
    document.getElementById('spinner').classList.remove('spinner-hide');
    fetch(API_USUARIOS + 'logIn', {
        method: 'post',
        body: new FormData(document.getElementById('login-form'))
    }).then(function (request) {
        //Ocultamos el loader al obtener respuesta
        document.getElementById('spinner').classList.add('spinner-hide');
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    switch (response.status) {
                        //Error de tipeo
                        case 5:
                            sweetAlert(1, response.message, 'dashboard.html');
                            break;
                        //Credenciales correctas y se prosigue a doble factor de autenticación
                        case 1:
                            sweetAlert(1, response.message, 'autenticacion.html');
                            break;
                        //Se necesita renovar la contraseña
                        case 2:
                            sweetAlert(3, response.message, 'codigo2.html');
                            break;
                        //Si se equivoca de contraseña se restan intentos
                        case 3: 
                        // Decrementa el contador por cada fallo de contraseña --------------------------------------.
                            loginAttempts--;
                            console.log(loginAttempts);
                            sweetAlert(2, response.exception, null);
                            // Al llegar el contador a 0, se llama a la API para bloquear la cuenta---------------------------------------------.
                            if (loginAttempts == 0) {
                                fetch(API_USUARIOS + 'blockAccount', {
                                    method: 'post',
                                    body: new FormData(document.getElementById('login-form'))
                                }).then(function (request) {
                                    // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                                    if (request.ok) {
                                        request.json().then(function (response) {
                                            // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                                            if (response.status) {
                                                sweetAlert(4, response.exception, null);
                                            }
                                        });
                                    } else {
                                        console.log(request.status + ' ' + request.statusText);
                                    }
                                });
                            }
                            break;
                        case 4:
                            sweetAlert(3, response.message, null);
                            break;
                    }
                } else {
                    sweetAlert(2, response.exception, null);
            }});
        } else {
            console.log(request.status + ' ' + request.statusText);
        }
    });
});
