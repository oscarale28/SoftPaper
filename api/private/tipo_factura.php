<?php
require_once('../helpers/database.php');
require_once('../helpers/validaciones.php');
require_once('../models/tipo_factura.php');

// Se comprueba si existe una acción a realizar por medio de isset, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $tipoFactura = new TipoFactura;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['uuid_empleado'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
                // Accion de leer toda la información------------------.
            case 'readAll':
                if ($result['dataset'] = $tipoFactura->readAll()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'readAllTFDetalle':
                if ($result['dataset'] = $tipoFactura->readAllTFDetalle()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'readOne':
                if (!$tipoFactura->setId($_POST['id-tf'])) {
                    $result['exception'] = 'Tipo de factura incorrecto';
                } elseif ($result['dataset'] = $tipoFactura->readOne()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Tipo de factura inexistente';
                }
                break;
            case 'create':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $tipoFactura->validateForm($_POST);
                if (!$tipoFactura->setTipo($_POST['nombre_tipo_factura'])) {
                    $result['exception'] = 'Tipo de factura inválido';
                } elseif (!$tipoFactura->setEstado(1)) {
                    $result['exception'] = 'Estado inválido';
                } elseif ($tipoFactura->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Tipo de factura creado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'update':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $tipoFactura->validateForm($_POST);
                if (!$tipoFactura->setId($_POST['id-tf'])) {
                    $result['exception'] = 'Tipo de factura incorrecto';
                } elseif (!$tipoFactura->setTipo($_POST['nombre_tipo_factura'])) {
                    $result['exception'] = 'Nombre de tipo de factura inválido';
                } elseif ($tipoFactura->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Tipo de factura actualizado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'delete':
                if (!$tipoFactura->setId($_POST['id_delete'])) {
                    $result['exception'] = 'Tipo de factura incorrecto';
                } elseif (!$tipoFactura->readOne()) {
                    $result['exception'] = 'Tipo de factura inexistente';
                } elseif ($tipoFactura->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Tipo de factura inhabilitado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'change':
                if (!$tipoFactura->setId($_POST['id_delete1'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$tipoFactura->setNum($_POST['num'])) {
                    $result['exception'] = 'Tipo de factura incorrecto';
                } elseif (!$tipoFactura->readOne()) {
                    $result['exception'] = 'Tipo de factura inexistente';
                } elseif ($tipoFactura->changeRow()) {
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
