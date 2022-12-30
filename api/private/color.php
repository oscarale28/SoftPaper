
<?php
require_once('../helpers/database.php');
require_once('../helpers/validaciones.php');
require_once('../models/color.php');

// Se comprueba si existe una acción a realizar por medio de isset, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $color = new ColorProducto;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
    if (isset($_SESSION['uuid_empleado'])) {
        $result['session'] = 1;
        switch ($_GET['action']) {

                // Accion de leer toda la información de los colores registrados------------------.
            case 'readAll':
                if ($result['dataset'] = $color->readAll()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'readAllSelect':
                if ($result['dataset'] = $color->readAllSelect()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
                // Accion de buscar información de las color disponibles------------------.     
            case 'search':
                if ($result['dataset'] = $color->searchRows($_POST['buscar-color-input'])) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;

                // Accion de registrar un nuevo color ------------------.       
            case 'create':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $color->validateForm($_POST);
                if (!$color->setColor($_POST['nombre_color'])) {
                    $result['exception'] = 'Nombre inválido';
                } elseif (!$color->setEstado(1)) {
                    $result['exception'] = 'Estado inválido';
                } elseif ($color->createRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Color creado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

                // Accion leer la información completa de un color seleccionado------------------.       
            case 'readOne':
                if (!$color->setId($_POST['id'])) {
                    $result['exception'] = 'Color incorrecta';
                } elseif ($result['dataset'] = $color->readOne()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'Color inexistente';
                }
                break;

                // Accion de modificar la información de un color seleccionado------------------.     
            case 'update':
                //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
                $_POST = $color->validateForm($_POST);
                if (!$color->setId($_POST['id'])) {
                    $result['exception'] = 'Color incorrecta';
                } elseif (!$data = $color->readOne()) {
                    $result['exception'] = 'Color inexistente';
                } elseif (!$color->setColor($_POST['nombre_color'])) {
                    $result['exception'] = 'Nombre inválido';
                } elseif ($color->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Color actualizado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

                // Accion de deshabilitar un color seleccionado------------------.        
            case 'delete':
                if (!$color->setId($_POST['id_delete'])) {
                    $result['exception'] = 'Color incorrecto';
                } elseif (!$color->readOne()) {
                    $result['exception'] = 'SubcColorategoría inexistente';
                } elseif ($color->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Color inhabilitado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

            case 'change':
                if (!$color->setId($_POST['id_delete'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$color->setNum($_POST['num'])) {
                    $result['exception'] = 'Tipo de venta incorrecto';
                } elseif (!$color->readOne()) {
                    $result['exception'] = 'Tipo de venta inexistenteS';
                } elseif ($color->changeRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cambio de estado correcto';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;

                // Accion de leer los colores disponibles de un producto para generar una venta------------------.        
            case 'readProductoColor':
                if (!$color->setId($_POST['idProducto'])) {
                    $result['exception'] = 'Producto incorrecto';
                } elseif ($result['dataset'] = $color->readColorProducto()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
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
}
