<?php
require_once('../helpers/database.php');
require_once('../helpers/validaciones.php');
require_once('../models/venta.php');

// Se comprueba si existe una acción a realizar por medio de isset, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $ventas = new Ventas;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['uuid_empleado'])) {
        $result['session'] = 1;
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'readAll':
                if ($result['dataset'] = $ventas->readAll()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay datos registrados';
                }
                break;
            case 'getMonto':
                if (!$ventas->setId($_POST['uuid_venta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif ($result['dataset'] = $ventas->readMonto()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No existe el monto en esta venta';
                }
                break;
            case 'readOrderDetail':
                if (!$ventas->setId($_POST['uuid_venta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif ($result['dataset'] = $ventas->readOrderDetail()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No existen productos en esta venta';
                }
                break;
            case 'readCountDetails':
                if (!$ventas->setId($_POST['uuid_venta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif ($result['dataset'] = $ventas->readCountDetails()) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No existen productos en esta venta';
                }
                break;
            case 'deleteDetail':
                if (!$ventas->setIdDetalle($_POST['id_detalle'])) {
                    $result['exception'] = 'Detalle incorrecto';
                } elseif (!$ventas->setId($_POST['id_venta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif ($ventas->deleteDetail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto removido correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al remover el producto';
                }
                break;
            case 'startOrder':
                if ($ventas->startOrder()==1) {
                    $result['status'] = 1;
                    $result['message'] = 'Venta creada correctamente';
                    $result['dataset'] = $_SESSION['uuid_venta_normal'];
                } elseif($ventas->startOrder() == 2) {
                    $result['status'] = 2;
                    $result['message'] = 'Existe una venta sin finalizar, ¿desea restaurarla?';
                    $result['dataset'] = $_SESSION['uuid_venta_normal'];
                } else {
                    $result['exception'] = 'Ocurrió un problema al crear la venta';
                }
                break;
            case 'createDetail':
                $_POST = $ventas->validateForm($_POST);
                if (!$ventas->setColor($_POST['color'])) {
                    $result['message'] = 'Color inválido';
                } elseif (!$ventas->setId($_POST['idVenta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif ($ventas->checkProducto($_POST['id'])) {
                    $result['status'] = 2;
                    $result['message'] = 'El producto ya se encuentra en la venta';
                } elseif (!$ventas->setProducto($_POST['id'])) {
                    $result['exception'] = 'Producto incorrecto';
                } elseif (!$ventas->setPrecio($_POST['precio'])) {
                    $result['exception'] = 'Cantidad incorrecta';
                } elseif (!$ventas->setCantidad($_POST['input-stock'])) {
                    $result['exception'] = 'Cantidad incorrecta';
                } elseif ($result['dataset'] = $ventas->createDetail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto agregado correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al agregar el producto';
                }
                break;
            case 'updateDetail':
                $_POST = $ventas->validateForm($_POST);
                if (!$ventas->setIdDetalle($_POST['idDetalle'])) {
                    $result['exception'] = 'Detalle incorrecto';
                } elseif (!$ventas->setId($_POST['idVenta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif (!$ventas->setCantidad($_POST['input-stock-update'])) {
                    $result['exception'] = 'Cantidad incorrecta';
                } elseif ($result['dataset'] = $ventas->updateDetail()) {
                    $result['status'] = 1;
                    $result['message'] = 'Cantidad modificada correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al modificar la cantidad';
                }
                break;
            case 'finishOrder':
                $_POST = $ventas->validateForm($_POST);
                if (!$ventas->setId($_POST['idVenta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif (!$ventas->setCliente($_POST['dui-cliente'])) {
                    $result['exception'] = 'Cliente incorrecto';
                } elseif (!$ventas->setCorrelativo($_POST['correlativo'])) {
                    $result['exception'] = 'Correlativo incorrecto';
                } elseif (!$ventas->setTipoVenta($_POST['tipo-venta'])) {
                    $result['exception'] = 'Tipo de venta incorrecta';
                } elseif (!$ventas->setTipoFactura($_POST['tipo-factura'])) {
                    $result['exception'] = 'Tipo de factura incorrecta';
                } elseif ($ventas->finishOrder($_POST['tipo-factura'], $_POST['tipo-venta'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Venta finalizada correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'getDetails':
                $_POST = $ventas->validateForm($_POST);
                if (!$ventas->setId($_POST['idVenta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif ($result['dataset'] = $ventas->getDetails()) {
                    $result['status'] = 1;
                    $result['message'] = 'Detalles obtenidos correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'updateDetailStock':
                $_POST = $ventas->validateForm($_POST);
                if (!$ventas->setProducto($_POST['uuid_color_stock'])) {
                    $result['exception'] = 'Producto incorrecto';
                } elseif(!$ventas->setCantidad($_POST['cantidad_producto'])) {
                    $result['exception'] = 'Cantidad de producto incorrecta';
                } elseif ($ventas->updateStockProducto()) {
                    $result['status'] = 1;
                    $result['message'] = 'Stock actualizado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
                break;
            case 'cancelOrder':
                $_POST = $ventas->validateForm($_POST);
                if (!$ventas->setId($_POST['idVenta'])) {
                    $result['exception'] = 'Venta incorrecta';
                } elseif (!$ventas->deleteDetails()) {
                    $result['exception'] = 'Ocurrió un problema al eliminar la venta';
                } elseif ($ventas->cancelOrder()) {
                    $result['status'] = 1;
                    $result['message'] = 'Venta cancelada correctamente';
                } else {
                    $result['exception'] = 'Ocurrió un problema al finalizar la venta';
                }
                break;
            case 'search':
                if ($result['dataset'] = $ventas->searchRows($_POST['search'])) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;
            case 'filterTable':
                if ($result['dataset'] = $ventas->readRowsFilter($_POST['filter-tipo-venta'], $_POST['filter-estado'])) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;
            case 'filterDate':
                if ($result['dataset'] = $ventas->readRowsFilterDate($_POST['start-date'], $_POST['end-date'])) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
                }
                break;
            // case 'delete':
            //     if (!$ventas->setId($_POST['id-delete'])) {
            //         $result['exception'] = 'Producto incorrecto';
            //     } elseif (!$ventas->readOne()) {
            //         $result['exception'] = 'Venta inexistente';
            //     } elseif ($ventas->deleteRow()) {
            //         $result['status'] = 1;
            //         $result['message'] = 'Venta cancelada correctamente';
            //     } else {
            //         $result['exception'] = Database::getException();
            //     }
            //     break;
            case 'ventasDiaPorTipo':
                if ($result['dataset'] = $ventas->ventasDiaPorTipo()) {
                    $result['status'] = 1;
                } else {
                    $result['exception'] = 'No hay datos disponibles';
                }
                break;  
            case 'readPrecioProducto':
                $_POST = $ventas->validateForm($_POST);
                if (!$ventas->setProducto($_POST['id_producto'])) {
                    $result['exception'] = 'Producto incorrecto';
                } elseif ($result['dataset'] = $ventas->readPrecioProducto()) {
                    $result['status'] = 1;
                } else {
                    $result['exception'] = 'No hay datos disponibles';
                }
                break;
            case 'ventasCategoriaG':
                if ($result['dataset'] = $ventas->ventasCategoriaG()) {
                    $result['status'] = 1;
                } else {
                    $result['exception'] = 'No hay datos disponibles';
                }
                break;
            case 'reportDateV':
                if ($result['dataset'] = $ventas->ventasPorSemanaReport($_POST['start-date'])) {
                    $result['status'] = 1;
                } elseif (Database::getException()) {
                    $result['exception'] = Database::getException();
                } else {
                    $result['exception'] = 'No hay coincidencias';
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
