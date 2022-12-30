<?php
require_once('../helpers/database.php');
require_once('../helpers/validaciones.php');
require_once('../models/municipio.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $municipio = new Municipio;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'exception' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['uuid_empleado'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            // Accion de leer toda la información------------------.
            case 'readAll':
                if (!$municipio->setDepartamento('depC')) {
                    $result['exception'] = 'Departamento inválido';
                } elseif ($result['dataset'] = $municipio->readAll2()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'readAllTable':
                if ($result['dataset'] = $municipio->readAllTable()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'readAllParam':
                if (!$municipio->setDepartamento($_POST['uuid_departamento'])) {
                    $result['exception'] = 'Departamento incorrecto';
                } elseif ($result['dataset'] = $municipio->readAllParam()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'create':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $municipio->validateForm($_POST);
                if (!$municipio->setMunicipio($_POST['nombre_municipio'])) {
                    $result['exception'] = 'Nombre invalido';
                } elseif (!$municipio->setDepartamento($_POST['depa_muni'])) {
                    $result['exception'] = 'Departamento inválido';
                } elseif ($municipio->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Municipio creado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            // Accion leer un elemento de toda la información------------------.        
            case 'readOne':
                if (!$municipio->setId($_POST['id'])) {
                    $result['exception'] = 'Municipio incorrecto';
                } elseif ($result['dataset'] = $municipio->readOne()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Municipio inexistente';
                }
                break;
            // Accion de actualizar un elemento de toda la información------------------.        
            case 'update':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $municipio->validateForm($_POST);
                if (!$municipio->setId($_POST['id1'])) {
                    $result['exception'] = 'Municipio incorrecto';
                } elseif (!$data = $municipio->readOne()) {
                    $result['exception'] = 'Municipio inexistente';
                } elseif (!$municipio->setMunicipio($_POST['nombre_municipio'])) {
                    $result['exception'] = 'Municipio inválido';
                } elseif (!$municipio->setDepartamento($_POST['depa_muni'])) {
                    $result['exception'] = 'Departamento inválido';
                } elseif ($municipio->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Municipio actualizado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            // Caso para eliminar el municipio
            case 'delete':
                if (!$municipio->setId($_POST['id_delete_m'])) {
                    $result['exception'] = 'Municipio incorrecto';
                } elseif (!$municipio->readOne()) {
                    $result['exception'] = 'Municipio inexistente';
                } elseif ($municipio->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Muncipio eliminado correctamente';
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
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}