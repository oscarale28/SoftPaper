<?php
require_once('../helpers/database.php');
require_once('../helpers/validaciones.php');
require_once('../models/tipo_venta.php');

// Se comprueba si existe una acción a realizar por medio de isset, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $tipoVenta = new TipoVenta;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['uuid_empleado'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Accion de leer toda la información------------------.
            case 'readAll':
                if ($result['dataset'] = $tipoVenta->readAll()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'readAllTVDetalle':
                if ($result['dataset'] = $tipoVenta->readAllTVDetalle()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'readOne':
                if (!$tipoVenta->setId($_POST['id-tv'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif ($result['dataset'] = $tipoVenta->readOne()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Tipo de venta inexistente';
                }
                break;
            case 'create':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $tipoVenta->validateForm($_POST);
                if (!$tipoVenta->setTipo($_POST['nombre_tipo_venta'])) {
                    $result['exception'] = 'Tipo de venta inválido';
                } elseif (!$tipoVenta->setEstado(1)) {
                    $result['exception'] = 'Estado inválido';
                } elseif ($tipoVenta->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Tipo de venta creado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'update':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $tipoVenta->validateForm($_POST);
                if (!$tipoVenta->setId($_POST['id-tv'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$tipoVenta->setTipo($_POST['nombre_tipo_venta'])) {
                    $result['exception'] = 'Nombre de tipo de venta inválido';
                } elseif ($tipoVenta->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Tipo de venta actualizado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'delete':
                if (!$tipoVenta->setId($_POST['id_delete'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$tipoVenta->readOne()) {
                    $result['exception'] = 'Tipo de venta inexistente';
                } elseif ($tipoVenta->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Tipo de venta inhabilitado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

            case 'change':
                if (!$tipoVenta->setId($_POST['id_delete'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$tipoVenta->setNum($_POST['num'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$tipoVenta->readOne()) {
                    $result['exception'] = 'Tipo de venta inexistenteS';
                } elseif ($tipoVenta->changeRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cambio de estado correcto';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            default:
                $result['exception'] = 'Acción no disponible dentro de la sesión';
        }
    } else {
        print(json_encode('Acceso denegado'));
    }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
