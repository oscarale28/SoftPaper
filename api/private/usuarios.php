<?php
require_once('../helpers/database.php');
require_once('../helpers/validaciones.php');
require_once('../helpers/mail.php');
require_once('../models/usuarios.php');

$DEFAULT_CARGO = '58a8b7aa-0e40-44e4-9409-0eab6bd23255';
$DEFAULT_FOTO = '7875dbce-e16c-400f-94f0-acb86a329fb5';
// Se comprueba si existe una acción a realizar por medio de isset, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    //Comprobamos si esta definida la sesión 'tiempo'.
    // if(isset($_SESSION['tiempo']) ) {

    //     //Tiempo en segundos para dar vida a la sesión.
    //     $inactivo = 300;//5min en este caso.

    //     //Calculamos tiempo de vida inactivo.
    //     $vida_session = time() - $_SESSION['tiempo'];

    //         //Compraración para redirigir página, si la vida de sesión sea mayor a el tiempo insertado en inactivo.
    //         if($vida_session > $inactivo)
    //         {
    //             //Removemos sesión.
    //             session_unset();
    //             //Destruimos sesión.
    //             session_destroy();              
    //             //Redirigimos pagina.
    //             header("Location: tupagina");
    //             exit();
    //         } else {  // si no ha caducado la sesion, actualizamos
    //             $_SESSION['tiempo'] = time();
    //         }


    // } else {
    //     //Activamos sesion tiempo.
    //     $_SESSION['tiempo'] = time();
    // }

    // Se instancia la clase correspondiente.
    $usuario = new Usuarios;
    $correo = new Mail;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null, 'avatar' => null, 'verification' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['uuid_empleado'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Acción de leer la información en base al Correo ------------------.
            case 'getUser':
                if ($usuario->getInfoUserByMail($_SESSION['uuid_empleado'])) {
                    $result['status'] = 1;
                    //$result['username'] = $usuario->getCorreo();
                    $result['username'] = $_SESSION['correo_empleado'];
                    $result['avatar'] = $usuario->getFoto();
                } else {
                    $result['exception'] = 'Usuario indefinido';
                }
                break;
            case 'getUserLevel':
                if ($usuario->getLevelUser($_SESSION['uuid_empleado'])) {
                    $result['status'] = 1;
                    $result['cargo'] = $usuario->getCargo();
                } else {
                    $result['exception'] = 'Usuario indefinido';
                }
                break;
                // Acción de cerrar sesión------------------.        
            case 'logOut':
                if (session_destroy()) {
                    $result['status'] = 1;
                    $result['message'] = 'Sesión eliminada correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al cerrar la sesión';
                }
                break;

                // Acción de leer toda la información de usuarios registrados------------------.    
            case 'readAll':
                if ($result['dataset'] = $usuario->readAll()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;

                // Acción de buscar información de los usuarios disponibles------------------.     
            case 'search':
                if ($result['dataset'] = $usuario->searchRows($_POST['buscar-empleado-input'])) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;

                // Acción de crear un nuevo usuario ------------------.       
            case 'create':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->setNombres($_POST['nombres'])) {
                    $result['exception'] = 'Nombres inválidos';
                } elseif (!$usuario->setApellidos($_POST['apellidos'])) {
                    $result['exception'] = 'Apellidos inválidos';
                } elseif (!$usuario->setCargo($_POST['cargo'])) {
                    $result['exception'] = 'Cargo inválido';
                } elseif (!$usuario->setCorreo($_POST['correo'])) {
                    $result['exception'] = 'Correo inválido';
                } elseif (!$usuario->setEstado('ae11fe84-64d7-43cd-a519-fe42ebfe2ce4')) {
                    $result['exception'] = 'Estado inválido';
                } elseif (!$usuario->setFoto($_POST['foto'])) {
                    $result['exception'] = 'Avatar inválido';
                } elseif (!$usuario->setAlias($_POST['alias'])) {
                    $result['exception'] = 'Alias inválido';
                } elseif ($_POST['clave'] != $_POST['clave2']) {
                    $result['exception'] = 'Claves diferentes';
                } elseif (!$usuario->setClave($_POST['clave2'], $_POST['nombres'], $_POST['apellidos'], $_POST['alias'])) {
                    $result['exception'] = $usuario->getPasswordError();
                } elseif ($usuario->createRow()) {
                    if ($usuario->registerCambioFecha()) {
                        $result['status'] = 1;
                        $result['message'] = 'Empleado creado correctamente';
                    } else {
                        $result['exception'] = Database::getException();
                    }
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

                // Acción leer toda la información de un elemento------------------.       
            case 'readOne':
                if (!$usuario->setId($_POST['id'])) {
                    $result['exception'] = 'Empleado incorrecto';
                } elseif ($result['dataset'] = $usuario->readOne()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Empleado inexistente';
                }
                break;

                // Accion leer toda la información del usuario logueado--------------.
            case 'readOnePerfil':
                if (!$usuario->setId($_SESSION['uuid_empleado'])) {
                    $result['exception'] = 'Usuario incorrecto';
                } elseif ($result['dataset'] = $usuario->readOneShow()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Usuario inexistente';
                }
                break;

                // Accion de leer si el id existe del empleado------------------.     
                // case 'readOneShow':
                //         if (!$usuario->setId($_POST['id'])) {
                //             $result['exception'] = 'Empleado incorrecto';
                //         } elseif ($result['dataset'] = $usuario->readOneShow()) {
                //             $result['status'] = 1;
                //         } elseif (Database::getException()) {
                //             $result['exception'] = Database::getException();
                //         } else {
                //             $result['exception'] = 'Empleado inexistente';
                //         }
                //         break;

                // Acción de actualizar toda la información de un elemento ------------------.         
            case 'update':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->setId($_POST['id'])) {
                    $result['exception'] = 'Empleado incorrecto';
                } elseif (!$usuario->setNombres($_POST['nombres'])) {
                    $result['exception'] = 'Nombres inválidos';
                } elseif (!$usuario->setApellidos($_POST['apellidos'])) {
                    $result['exception'] = 'Apellidos inválidos';
                } elseif (!$usuario->setCargo($_POST['cargo'])) {
                    $result['exception'] = 'Cargo inválido';
                } elseif (!$usuario->setCorreo($_POST['correo'])) {
                    $result['exception'] = 'Correo inválido';
                } elseif (!$usuario->setEstado($_POST['estado-empleado'])) {
                    $result['exception'] = 'Estado inválido';
                } elseif (!$usuario->setFoto($_POST['foto'])) {
                    $result['exception'] = 'Avatar inválido';
                } elseif ($_POST['id'] == $_SESSION['uuid_empleado']) {
                    if ($_POST['estado-empleado'] == 'ae11fe84-64d7-43cd-a519-fe42ebfe2ce4') {
                        if ($usuario->updateRow()) {
                            $result['status'] = 1;
                            $result['message'] = 'Empleado modificado correctamente';
                        } else {
                            $result['exception'] = Database::getException();
                        }
                    } else {
                        $result['exception'] = 'No se puede dar de baja o bloquearse a sí mismo';
                    }
                } elseif ($usuario->updateRow()) {
                        $result['status'] = 1;
                        $result['message'] = 'Empleado modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
                // Acción de actualizar toda la información en el perfil de usuario-----------------------------.      
            case 'updateP':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->setId($_POST['id'])) {
                    $result['exception'] = 'Empleado incorrecto';
                } elseif (!$usuario->readOne()) {
                    $result['exception'] = 'Empleado inexistente';
                }elseif (!$usuario->setNombres($_POST['nombre_emp'])) {
                    $result['exception'] = 'Nombres inválidos';
                } elseif (!$usuario->setApellidos($_POST['apellido_emp'])) {
                    $result['exception'] = 'Apellidos inválidos';
                } elseif (!$usuario->setAlias($_POST['alias_emp'])) {
                    $result['exception'] = 'Alias inválido';
                } elseif (!$usuario->setDobleAutenticacion($_POST['factor_autenticacion_value'])) {
                    $result['exception'] = 'Doble autenticación inválida';
                }elseif (!$usuario->setFoto($_POST['foto'])) {
                    $result['exception'] = 'Avatar inválido';
                }elseif ($usuario->updatePerfil()) { 
                    $result['status'] = 1;
                    $result['message'] = 'Perfil modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
                // Acción para cargar gráfica
            case 'ventasEmpleado':
                if (!$usuario->setId($_POST['idr'])) {
                    $result['exception'] = 'Empleado incorrecto';
                } elseif ($result['dataset'] = $usuario->ventasEmpleado($_POST['start-date'], $_POST['end-date'])) {
                    $result['status'] = 1;
                } else {
                    $result['exception'] = 'No hay datos disponibles';
                }
                break;

                // Reporte de ventas por empleado en un rango de fecha
            case 'reportDate':
                if (!$usuario->setId($_POST['idr'])) {
                    $result['exception'] = 'Empleado incorrecto';
                } elseif ($result['dataset'] = $usuario->readRowsReportDate($_POST['start-date'], $_POST['end-date'])) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;

                // Accion de deshabilitar un elemento selecciondao------------------------.       
            case 'delete':
                if ($_POST['uuid_empleado'] == $_SESSION['uuid_empleado']) {
                    $result['exception'] = 'No se puede dar de baja a sí mismo';
                } elseif (!$usuario->setId($_POST['uuid_empleado'])) {
                    $result['exception'] = 'Empleado incorrecto';
                } elseif (!$usuario->readOne()) {
                    $result['exception'] = 'Empleado inexistente';
                } elseif ($usuario->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Empleado inhabilitado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

            case 'change':
                if (!$usuario->setId($_POST['id_delete'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$usuario->setNum($_POST['num'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$usuario->readOne()) {
                    $result['exception'] = 'Tipo de venta inexistenteS';
                } elseif ($usuario->changeRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cambio de estado correcto';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            //Caso para cuando se cambia la contraseña desde editar perfil.
            case 'updateC':
                if (!$usuario->setId($_SESSION['uuid_empleado'])) {
                    $result['exception'] = 'Usuario incorrecto';
                    // Revisa si las contraseñas actuales son iguales.
                }if (!$usuario->getInfoRestaurarContraseña()) {
                    $result['exception'] = 'No se pudieron obtener los datos de su usuario';
                } elseif ($_POST['contra-new'] != $_POST['confirm-new']) {
                    $result['exception'] = 'Claves diferentes';
                 //Encriptamos la clave que pasamos por medio de post y la comparamos con la almacenada en la base, para validar que no sean iguales   
                } elseif (password_verify($_POST['confirm-new'],$usuario->getClave())) {
                    $result['exception'] = 'No puedes usar tu contraseña anterior';
                } elseif (!$usuario->setClave($_POST['confirm-new'], $usuario->getNombres(), $usuario->getApellidos(), $usuario->getAlias())) {
                    $result['exception'] = $usuario->getPasswordError();
                } elseif ($usuario->restaurarContrasena()) {
                    $_SESSION['uuid_empleado_recover'] = $usuario->getId();
                    if ($correo->sendVerificationMessageC($_SESSION['correo_empleado'],'Cambio de contraseña')) {
                        if ($usuario->renovarContrasenaRecu1()) {
                            if ($usuario->renovarContrasenaRecu2()) {
                                $result['status'] = 1;
                                $result['message'] = 'Contraseña guardada correctamente';
                            } else {
                                $result['exception'] = Database::getException();
                            }
                        } else {
                            $result['exception'] = Database::getException();
                        }
                    } else{
                        $result['exception'] = 'Código incorrecto';
                    }
                }
            break;
            case 'chPass':
                if (!$usuario->setId($_SESSION['uuid_empleado'])) {
                    $result['exception'] = 'Usuario incorrecto';
                    // Revisa si las contraseñas actuales son iguales.
                } elseif ($usuario->checkPassword($_POST['pass-act'])) {
                    // Revisa que la contraseña nueva y la de confirmar sean iguales.
                    if ($_POST['pass-new'] != $_POST['confpass-new']) {
                        $result['exception'] = 'Claves diferentes';
                    // Revisa que la contraseña nueva cumpla con todos los requisitos.
                    } elseif (!$usuario->setClave($_POST['pass-new'], $_SESSION['nombres_usuario'], $_SESSION['apellidos_usuario'], $_SESSION['alias_usuario'])) {
                        $result['exception'] = $usuario->getPasswordError();
                    // Revisa que la contraseña nueva no sea igual a la contraseña actual.
                    } elseif ($_POST['pass-act'] == $_POST['pass-new']) {
                        $result['exception'] = 'La contraseña debe ser diferente a la actual';
                        // Agenda la nueva fecha cuando será necesario cambiar la contraseña. (En 90 días)
                    } elseif ($usuario->renovarContrasenaPerfil1()) {
                        if ($usuario->renovarContrasenaPerfil2()) {
                            $result['status'] = 1;
                            $result['message'] = 'Contraseña cambiada correctamente';
                        } else {
                            $result['exception'] = Database::getException();
                        }
                    } else {
                        $result['exception'] = Database::getException();
                    }
                } else {
                    $result['exception'] = 'La contraseñas actuales no coinciden';
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        // Se compara la acción a realizar cuando el administrador no ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readUsers':
                //Verificamos si existen usuarios registrados en la base de datos, para que en caso de no, registrar el primer usuario
                if ($usuario->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existe al menos un usuario registrado';
                } else {
                    $result['exception'] = 'No existen usuarios registrados';
                }
                break;
            case 'register-first-user':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $usuario->validateForm($_POST);
                if($usuario->readAll()) {
                    $result['exception'] = 'No puede registrar un usuario, ya hay existentes.';
                } elseif (!$usuario->setNombres($_POST['nombres'])) {
                    $result['exception'] = 'Nombres inválidos';
                } elseif (!$usuario->setApellidos($_POST['apellidos'])) {
                    $result['exception'] = 'Apellidos inválidos';
                } elseif (!$usuario->setCargo($DEFAULT_CARGO)) {
                    $result['exception'] = 'Cargo inválido';
                } elseif (!$usuario->setCorreo($_POST['correo'])) {
                    $result['exception'] = 'Correo inválido';
                } elseif (!$usuario->setEstado('ae11fe84-64d7-43cd-a519-fe42ebfe2ce4')) {
                    $result['exception'] = 'Estado inválido';
                } elseif (!$usuario->setFoto($DEFAULT_FOTO)) {
                    $result['exception'] = 'Avatar inválido';
                } elseif (!$usuario->setAlias($_POST['alias'])) {
                    $result['exception'] = 'Alias inválido';
                } elseif ($_POST['clave'] != $_POST['confirmar']) {
                    $result['exception'] = 'Claves diferentes';
                } elseif (!$usuario->setClave($_POST['clave'], $_POST['nombres'], $_POST['apellidos'], $_POST['alias'])) {
                    $result['exception'] = $usuario->getPasswordError();
                } elseif ($usuario->createRow()) {
                    if ($usuario->renovarContrasenaFirstUser()) {
                        $result['status'] = 1;
                        $result['message'] = 'Usuario registrado correctamente';
                    } else {
                        $result['exception'] = Database::getException();
                    }
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'logIn':
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->checkUser($_POST['correo'])) {
                    $result['exception'] = 'Credenciales incorrectas';
                    // Valida si el usuario se encuentra bloqueado o inhabilitado---------------------------------------------.
                } elseif (!$usuario->checkBlockedUser()) {
                    // En caso de haber pasado las 24 horas de bloqueo al intentar el login, se desbloquea al usuario---------------------------------------------.
                    if($usuario->unblockUser()){
                        $result['status'] = 4;
                        $result['message'] = 'Su cuenta ha sido desbloqueada.';
                    } else{
                        $result['exception'] = 'Su cuenta ha sido inhabilitada o bloqueada. Comuníquese con su administrador.';
                    }
                } elseif ($usuario->checkPassword($_POST['clave'])) {
                    $hoy = date('Y-m-d');
                    $futuro = implode($usuario->checkDatePassword());
                    // Verifica si la fecha de la zona es igual a la fecha guardada hace 90 días.
                    if ($futuro > $hoy) {
                        if($usuario->checkVerificationState()){
                            //Si las credenciales son correctas y la doble autenticacion está activada, creamos, enviamos el token al correo y guardamos el mismo en la base
                            $token = $correo->Obtener_token(4);
                            if (!$correo->sendVerificationMessage($usuario->getCorreo(), 'Autenticación de doble factor SoftPaper', $token)) {
                                $result['exception'] = 'Ocurrió un error al enviar su código de verificación.';
                            } elseif (!$usuario->insertToken($token)) {
                                $result['exception'] = 'Ocurrió un error al guardar el token.';
                            } else {
                                $_SESSION['uuid_empleado_verification'] = $usuario->getId();
                                $_SESSION['email_empleado_verification'] = $usuario->getCorreo();
                                $result['status'] = 1;
                                $result['message'] = 'Autenticación correcta, se envio un código de verificación a su correo';
                            }
                        } else {
                            $_SESSION['uuid_empleado'] = $usuario->getId();
                            $_SESSION['correo_empleado'] = $usuario->getCorreo();
                            $_SESSION['nombres_usuario'] = $usuario->getNombres();
                            $_SESSION['apellidos_usuario'] = $usuario->getApellidos();
                            //
                            $_SESSION['alias_usuario'] = $usuario->getAlias();
                            $result['status'] = 5   ;
                            $result['message'] = 'Autenticación correcta';
                        }
                        
                    } else {
                        $tokenVerify = $correo->Obtener_token(6);
                    if (!$correo->sendVerificationMessageR($usuario->getCorreo(), 'Cambio de contraseña',$tokenVerify)) {
                        $result['exception'] = 'Ocurrió un error al enviar su código de verificación.';
                    } elseif (!$usuario->insertCodigo($tokenVerify)) {
                        $result['exception'] = 'Ocurrió un error al guardar el token.';
                    } else {
                        $_SESSION['uuid_empleado_renew'] = $usuario->getId();
                        $_SESSION['nombres_empleado_renew'] = $usuario->getNombres();
                        $_SESSION['apellidos_empleado_renew'] = $usuario->getApellidos();
                        $_SESSION['alias_empleado_renew'] = $usuario->getAlias();
                        $_SESSION['correo_empleado'] = $usuario->getCorreo();
                        $result['status'] = 2;
                        $result['message'] = 'Clave vencida, debe cambiar su contraseña';
                    } 
                    
                } 
            } else {

                    $result['status'] = 3;
                    $result['exception'] = 'Credenciales incorrectas';
                
                }
                break;
            case 'resendVerificationCode':
                //Reenviamos y reinsertamos token
                $token = $correo->Obtener_token(4);
                if (!$correo->sendVerificationMessage($_SESSION['correo_empleado'], 'Autenticación de doble factor SoftPaper', $token)) {
                    $result['exception'] = 'Ocurrió un error al enviar su código de verificación.';
                } elseif (!$usuario->reInsertToken($token)) {
                    $result['exception'] = 'Ocurrió un error al guardar el token, vuelve a intentar acceder.';
                } else {
                    $result['status'] = 1;
                    $result['message'] = 'Se reenvió exitosamente el código de verificación';
                }
                break;
            // Reenviar codigo para recuperar contraseña
            case 'resendVerificationCode2':
                //Reenviamos y reinsertamos token
                $tokenVerify = $correo->Obtener_token(6);
                if (!$correo->sendVerificationMessageR($_SESSION['correo_empleado'],'Cambio de contraseña',$tokenVerify)) {
                    $result['exception'] = 'Ocurrió un error al enviar su código de verificación.';
                } elseif (!$usuario->reInsertToken2($tokenVerify)) {
                    $result['exception'] = 'Ocurrió un error al guardar el token, vuelve a intentar acceder.';
                } else {
                    $result['status'] = 1;
                    $result['message'] = 'Se reenvió exitosamente el código de verificación';
                }
                break;
                //Verificación del código de autenticación
            case 'checkVerification':
                if (!$usuario->checkVerificationCode($_POST['token'])) {
                    $result['status'] = 2;
                    $result['message'] = 'Código de verificación incorrecto';
                //Verificamos el tiempo del token, dandole una caducidad de 2 minutos
                } elseif (!$usuario->checkTimeVerificationCode()) {
                    $result['exception'] = 'Su código de verificación ha caducado, vuelva a iniciar sesión';
                //Verificamos si ya se ha iniciado sesión desde la ip del dispositivo    
                }  elseif ($usuario->checkExistingSession($_POST['ip'], $_POST['dispositivo'], $_POST['ciudad'])) {
                    if (!$correo->sendSessionMessage($usuario->getCorreo(), 'Alerta de seguridad', $_POST['ciudad'], $_POST['dispositivo'], $_POST['ip'])) {
                        $result['exception'] = 'Ocurrió un error al enviar su correo de seguridad';
                    } else {
                        $result['status'] = 1;
                        $result['message'] = 'Código de verificación correcto';
                        $_SESSION['uuid_empleado'] = $usuario->getId();
                        $_SESSION['correo_empleado'] = $usuario->getCorreo();
                        $_SESSION['imagen_avatar'] = $usuario->getFoto();
                        $_SESSION['nombres_usuario'] = $usuario->getNombres();
                        $_SESSION['apellidos_usuario'] = $usuario->getApellidos();
                        $_SESSION['alias_usuario'] = $usuario->getAlias();
                    }
                } else {
                    $result['status'] = 1;
                    $result['message'] = 'Código de verificación correcto';
                    $_SESSION['uuid_empleado'] = $usuario->getId();
                    $_SESSION['correo_empleado'] = $usuario->getCorreo();
                    $_SESSION['imagen_avatar'] = $usuario->getFoto();
                    $_SESSION['nombres_usuario'] = $usuario->getNombres();
                    $_SESSION['apellidos_usuario'] = $usuario->getApellidos();
                    $_SESSION['alias_usuario'] = $usuario->getAlias();
                }
                break;
            //Se verifica que el correo sea el mismo que se encuentra en la base
            case 'verifyEmail':
                $_POST = $usuario->validateForm($_POST);
                // Valida si el usuario está bloqueado, de ser así, impide el proceso de recuperación
                if(!$usuario->checkBlockedUserRecu($_POST['correo'])){
                    $result['exception'] = 'Su cuenta ha sido inhabilitada o bloqueada. Comuníquese con su administrador.';
                } elseif($usuario->readEmail($_POST['correo'])){
                    $usuario->setCorreo($_POST['correo']);//Poner en if y retornar exception
                    $tokenVerify = $correo->Obtener_token(6);
                    if ($correo->sendVerificationMessageR($_POST['correo'],'Cambio de contraseña',$tokenVerify)) {
                        if ($usuario->insertCodigo($tokenVerify)) {
                            $result['status'] = 1;
                            $result['message'] = 'correo enviado exitosamente';
                            $_SESSION['correo_empleado'] = $_POST['correo'];
                            $_SESSION['uuid_correo_empleado'] = $usuario->getId();
                        }
                        else {
                            $result['exception'] = 'Ocurrió un error al guardar el token.';
                        }
                    }
                    else{
                        $result['status'] = 3;
                        $result['exception'] = 'Verifica que el correo sea correo';
                    }
                }
                else{
                    $result['status'] = 3;
                    $result['exception'] = 'Verifica que el correo sea correcto';
                }
                break;
               //Se verifica que el codigo sea el mismo que en la base
            case 'verifyCodigo':
                $_POST = $usuario->validateForm($_POST);
                if($usuario->verifyCode($_POST['codigo'])) {
                    if ($usuario->checkTimeVerificationCodec()) {
                        $result['status'] = 1;
                        $result['message'] = 'Código de verificación correcto';
                    }else{
                        $result['exception'] = 'Su código de verificación ha caducado, vuelva a iniciar sesión';
                    }
                }else{
                    $result['status'] = 2;
                    $result['message'] = 'Código incorrecto';
                }
            break;
            case 'verifyCodigo2':
                $_POST = $usuario->validateForm($_POST);
                if($usuario->verifyCode2($_POST['codigo'])) {
                    if ($usuario->checkTimeVerificationCodec()) {
                        $result['status'] = 1;
                        $result['message'] = 'Código de verificación correcto';
                    }else{
                        $result['exception'] = 'Su código de verificación ha caducado, vuelva a iniciar sesión';
                    }
                }else{
                    $result['status'] = 2;
                    $result['message'] = 'Código incorrecto';
                }
            break;
            //Se actualiza la contra
            case 'updateC':
                $_POST = $usuario->validateForm($_POST);
                if (!$usuario->getInfoRestaurarContraseña()) {
                    $result['exception'] = 'No se pudieron obtener los datos de su usuario';
                } elseif ($_POST['contra-new'] != $_POST['confirm-new']) {
                    $result['exception'] = 'Claves diferentes';
                 //Encriptamos la clave que pasamos por medio de post y la comparamos con la almacenada en la base, para validar que no sean iguales   
                } elseif (password_verify($_POST['confirm-new'],$usuario->getClave())) {
                    $result['exception'] = 'No puedes usar tu contraseña anterior';
                } elseif (!$usuario->setClave($_POST['confirm-new'], $usuario->getNombres(), $usuario->getApellidos(), $usuario->getAlias())) {
                    $result['exception'] = $usuario->getPasswordError();
                } elseif ($usuario->restaurarContrasena()) {
                    $_SESSION['uuid_empleado_recover'] = $usuario->getId();
                    if ($correo->sendVerificationMessageC($_SESSION['correo_empleado'],'Cambio de contraseña')) {
                        if ($usuario->renovarContrasenaRecu1()) {
                            if ($usuario->renovarContrasenaRecu2()) {
                                $result['status'] = 1;
                                $result['message'] = 'Contraseña guardada correctamente';
                            } else {
                                $result['exception'] = Database::getException();
                            }
                        } else {
                            $result['exception'] = Database::getException();
                        }
                    } else{
                        $result['exception'] = 'Código incorrecto';
                    }
                }
            break;
            case 'checkPass':
                // Revisa si la contraseña coincide con la actual
                if ($usuario->checkPassword2($_POST['contra-act'])) {
                    // Revisa que las contraseñas nuevas sean iguales
                    if ($_POST['contra-new'] != $_POST['confirm-new']) {
                        $result['exception'] = 'Claves diferentes';
                        // Revisa que la contraseña cumpla con todos los requisitos
                    } elseif (!$usuario->setClave($_POST['contra-new'], $_SESSION['nombres_empleado_renew'], $_SESSION['apellidos_empleado_renew'], $_SESSION['alias_empleado_renew'])) {
                        $result['exception'] = $usuario->getPasswordError();
                        // Revisa que la contraseña nueva no sea igual a la actual
                    } elseif ($_POST['contra-act'] == $_POST['contra-new']) {
                        $result['exception'] = 'La contraseña debe ser diferente a la actual';
                        // Guarda contraseña y la nueva fecha en la que debera cambiar su contraseña
                    } elseif ($usuario->renovarContrasena1()) {
                        if ($usuario->renovarContrasena2()) {
                            $result['status'] = 1;
                            $result['message'] = 'Contraseña cambiada correctamente';
                        } else {
                            $result['exception'] = Database::getException();
                        }
                    } else {
                        $result['exception'] = Database::getException();
                    }
                } else {
                    $result['exception'] = 'La contraseñas actuales no coinciden';
                }
                break;
                // Bloqueo de cuenta tras 3 ingresos erróneos de contraseña---------------------------------------------.
            case 'blockAccount':
                if (!$usuario->checkUser($_POST['correo'])) {
                    $result['exception'] = 'Correo incorrecto';
                } elseif ($usuario->blockAccount()) {
                    $result['status'] = 1;
                    $usuario->registerUserBlock();
                    $result['exception'] = 'Su cuenta ha sido bloqueada tras 3 intentos fallidos de autenticación.';
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible fuera de la sesión';
        }
    }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
