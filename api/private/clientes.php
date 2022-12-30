<?php
require_once('../helpers/database.php');
require_once('../helpers/validaciones.php');
require_once('../models/clientes.php');

// Se comprueba si existe una acción a realizar por medio de isset, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $clientes = new Cliente;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
    switch ($_GET['action']) {

            // Accion de leer toda la información para llenar la tabla de clientes------------------.
        case 'readAll':
            if ($result['dataset'] = $clientes->readAll()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay clientes registrados';
            }
            break;

            // Accion de leer el listado de clientes para generar una venta------------------.
        case 'readClientesVenta':
            if ($result['dataset'] = $clientes->readClientesVenta()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay clientes registrados';
            }
            break;
        case 'searchClientesVenta':
            if ($result['dataset'] = $clientes->searchClientesVenta($_POST['buscar-cliente'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;
            // Accion de buscar información de los clientes registrados------------------.
        case 'search':
            if ($result['dataset'] = $clientes->searchRows($_POST['search'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;

            // Accion de filtrar la tabla de clientes según el parámetro seleccionado------------------.
        case 'filter':
            if ($result['dataset'] = $clientes->searchRows($_POST['filtroC'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;

            // Accion de registrar un nuevo cliente------------------.
        case 'create':
            //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
            $_POST = $clientes->validateForm($_POST);
            if (!$clientes->setNombre($_POST['nombre_c'])) {
                $result['exception'] = 'Nombre inválido';
            } elseif (!$clientes->setDireccion($_POST['direccion_c'])) {
                $result['exception'] = 'Dirección inválida';
            } elseif (!$clientes->setNrc($_POST['nrc_c'])) {
                $result['exception'] = 'NRC inválido';
            } elseif (!$clientes->setDUI($_POST['dui_c'])) {
                $result['exception'] = 'DUI inválido';
            } elseif (!$clientes->setNIT($_POST['nit_c'])) {
                $result['exception'] = 'NIT inválido';
            } elseif (!$clientes->setTelefono($_POST['telefono_c'])) {
                $result['exception'] = 'Teléfono inválido';
            } elseif (!$clientes->setMunicipio($_POST['municipio_c'])) {
                $result['exception'] = 'Municipio inválido';
            } elseif (!$clientes->setGiro($_POST['giro_c'])) {
                $result['exception'] = 'Giro inválido';
            } elseif (!$clientes->setEstado(1)) {
                $result['exception'] = 'Estado inválido';
            } elseif ($clientes->createRow()) {
                $result['status'] = 1;
                $result['message'] = 'Cliente ingresado correctamente';
            } else {
                $result['exception'] = Database::getException();
            }
            break;

            // Accion de leer la información completa de un cliente seleccionado------------------.
        case 'readOne':
            if (!$clientes->setId($_POST['id'])) {
                $result['exception'] = 'ID incorrecto';
            } elseif ($result['dataset'] = $clientes->readOne()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Producto inexistente';
            }
            break;

            // Accion de modificar los datos de un cliente seleccionado------------------.
        case 'update':
            //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
            $_POST = $clientes->validateForm($_POST);
            if (!$clientes->setId($_POST['id'])) {
                $result['exception'] = 'Id incorrecto';
            } elseif (!$data = $clientes->readOne()) {
                $result['exception'] = 'Producto inexistente';
            } elseif (!$clientes->setNombre($_POST['unombre_c'])) {
                $result['exception'] = 'Nombre inválido';
            } elseif (!$clientes->setDUI($_POST['udui_c'])) {
                $result['exception'] = 'DUI inválido';
            } elseif (!$clientes->setNIT($_POST['unit_c'])) {
                $result['exception'] = 'NIT inválido';
            } elseif (!$clientes->setDireccion($_POST['udireccion_c'])) {
                $result['exception'] = 'Dirección inválida';
            } elseif (!$clientes->setTelefono($_POST['utelefono_c'])) {
                $result['exception'] = 'Teléfono inválido';
            } elseif (!$clientes->setNrc($_POST['unrc_c'])) {
                $result['exception'] = 'NRC inválido';
            } elseif (!$clientes->setGiro($_POST['ugiro_c'])) {
                $result['exception'] = 'Giro inválido';
            } elseif (!$clientes->setMunicipio($_POST['umunicipio_c'])) {
                $result['exception'] = 'Municipio inválido';
            } elseif ($clientes->updateRow()) {
                $result['status'] = 1;
                $result['message'] = 'Cliente actualizado correctamente';
            } else {
                $result['exception'] = Database::getException();
            }
            break;

            // Accion de inhabilitar un cliente seleccionado------------------.
        case 'delete':
            if (!$clientes->setId($_POST['id_delete'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$clientes->readOne()) {
                $result['exception'] = 'Producto inexistente';
            } elseif ($clientes->deleteRow()) {
                $result['status'] = 1;
                $result['message'] = 'Cliente inhabilitado correctamente';
            } else {
                $result['exception'] = Database::getException();
            }
            break;

        case 'change':
            if (!$clientes->setId($_POST['id_delete'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$clientes->setNum($_POST['num'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$clientes->readOne()) {
                $result['exception'] = 'Producto inexistente';
            } elseif ($clientes->changeRow()) {
                $result['status'] = 1;
                $result['message'] = 'Cambio de estado correcto';
            } else {
                $result['exception'] = Database::getException();
            }
            break;

        default:
            $result['exception'] = 'Acción no disponible dentro de la sesión';
    }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
