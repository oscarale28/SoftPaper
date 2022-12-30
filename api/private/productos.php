<?php
require_once('../helpers/database.php');
require_once('../helpers/validaciones.php');
require_once('../models/productos.php');

// Se comprueba si existe una acción a realizar por medio de isset, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $productos = new Productos;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'session' => 0, 'message' => null, 'exception' => null, 'dataset' => null, 'username' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
    switch ($_GET['action']) {
        case 'readAll':
            if ($result['dataset'] = $productos->readAll()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay datos registrados';
            }
            break;
        case 'readStadistics':
            if ($result['dataset'] = $productos->readStadistics()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay datos registrados';
            }
            break;
        case 'search':
            if ($result['dataset'] = $productos->searchRows($_POST['search'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;
        case 'filterTable':
            if ($result['dataset'] = $productos->readRowsFilter($_POST['filter-categoria'], $_POST['filter-estado'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;
        case 'create':
            //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
            $_POST = $productos->validateForm($_POST);
            if (!$productos->setNombre($_POST['nombre'])) {
                $result['exception'] = 'Nombre inválido';
            } elseif (!$productos->setDescripcion($_POST['descripcion'])) {
                $result['exception'] = 'Descripción inválida';
            } elseif (!$productos->setSubcategoria($_POST['subcategoria'])) {
                $result['exception'] = 'Subcategoría inválida';
            } elseif (!$productos->setProveedor($_POST['proveedor'])) {
                $result['exception'] = 'Proveedor inválido';
            } elseif (!$productos->setMarca($_POST['marca'])) {
                $result['exception'] = 'marca inválida';
            } elseif (!$productos->setPrecio($_POST['precio'])) {
                $result['exception'] = 'Precio inválido';
            } elseif (!$productos->setColor($_POST['color'])) {
                $result['exception'] = 'Color inválido';
            } elseif (!$productos->setStock($_POST['stock'])) {
                $result['exception'] = 'Stock inválido';
            } elseif (!is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                $result['exception'] = 'Seleccione una imagen';
            } elseif (!$productos->setImagen($_FILES['archivo'])) {
                $result['exception'] = $productos->getFileError();
            } elseif ($productos->createRow()) {
                $result['status'] = 1;
                if ($productos->saveFile($_FILES['archivo'], $productos->getRuta(), $productos->getImagen())) {
                    if (!$productos->insertStock()) {
                        $result['exception'] = 'Ocurrió un error al insertar el stock';
                    } elseif (!$productos->insertProvider()) {
                        $result['exception'] = 'Ocurrió un error al insertar el proveedor';
                    } else {
                        $result['message'] = 'Producto creado correctamente';
                    }
                } else {
                    $result['message'] = 'Producto creado pero no se guardó la imagen';
                }
            } else {
                $result['exception'] = Database::getException();
            }
            break;
        case 'readOne':
            if (!$productos->setId($_POST['id'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $productos->readOne()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Producto inexistente';
            }
            break;
        case 'readStock':
            if (!$productos->setId($_POST['id'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$productos->setColor($_POST['color'])) {
                $result['exception'] = 'Color incorrecto';
            } elseif ($result['dataset'] = $productos->readProductStock()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay stock del producto en el color seleccionado';
            }
            break;
        case 'readStockUpdate':
            if (!$productos->setColor($_POST['idColorStock'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $productos->readProductStockUpdate()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Stock del producto inexistente';
            }
            break;
        case 'update':
            //Especificamos los inputs por medio de su atributo name, y los capturamos con el método post
            $_POST = $productos->validateForm($_POST);
            if (!$productos->setId($_POST['id'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$data = $productos->readOne()) {
                $result['exception'] = 'Producto inexistente';
            } elseif (!$productos->setNombre($_POST['nombre'])) {
                $result['exception'] = 'Nombre inválido';
            } elseif (!$productos->setDescripcion($_POST['descripcion'])) {
                $result['exception'] = 'Descripción inválida';
            } elseif (!$productos->setSubcategoria($_POST['subcategoria'])) {
                $result['exception'] = 'Subcategoría inválida';
            } elseif (!$productos->setProveedor($_POST['proveedor'])) {
                $result['exception'] = 'Proveedor inválido';
            } elseif (!$productos->setMarca($_POST['marca'])) {
                $result['exception'] = 'Marca inválida';
            } elseif (!$productos->setPrecio($_POST['precio'])) {
                $result['exception'] = 'Precio inválido';
            } elseif (!$productos->setColor($_POST['color'])) {
                $result['exception'] = 'Color inválido';
            } elseif (!$productos->setStock($_POST['stock'])) {
                $result['exception'] = 'Stock inválido';
            } elseif (!$productos->setEstado($_POST['estado'])) {
                $result['exception'] = 'Estado inválido';
            } elseif (!$productos->updateStock()) {
                $result['exception'] = 'Ocurrió un error al actualizar el stock';
            } elseif (!$productos->updateProvider()) {
                $result['exception'] = 'Ocurrió un error al actualizar el proveedor';
            } elseif (!is_uploaded_file($_FILES['archivo']['tmp_name'])) {
                if ($productos->updateRow($data['imagen_producto'])) {
                    $result['status'] = 1;
                    $result['message'] = 'Producto modificado correctamente';
                } else {
                    $result['exception'] = Database::getException();
                }
            } elseif (!$productos->setImagen($_FILES['archivo'])) {
                $result['exception'] = $subcategorias->getFileError();
            } elseif ($productos->updateRow($data['imagen_producto'])) {
                $result['status'] = 1;
                if ($productos->saveFile($_FILES['archivo'], $productos->getRuta(), $productos->getImagen())) {
                } else {
                    $result['message'] = 'Producto actualizado pero no se guardó la imagen';
                }
            } else {
                $result['exception'] = Database::getException();
            }
            break;
        case 'delete':
            if (!$productos->setId($_POST['id-delete'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif (!$productos->readOne()) {
                $result['exception'] = 'Producto inexistente';
            // } elseif (!$productos->deleteColorStock()) {
            //     $result['exception'] = 'Ocurrió un error al borrar el stock con los colores';
            } elseif (!$productos->deleteProvider()) {
                $result['exception'] = 'Ocurrió un error al borrar los proveedores del producto';
            } elseif (!$productos->colorAfterDelete()) {
                $result['exception'] = 'Ocurrió al vaciar el stock del producto';
            } elseif ($productos->deleteRow()) {
                $result['status'] = 1;
                $result['message'] = 'Producto vaciado correctamente';
            } else {
                $result['exception'] = Database::getException();
            }
            break;
        case 'readProductosVentas':
            if ($result['dataset'] = $productos->readProductosVentas()) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay datos registrados';
            }
            break;
        case 'searchProductosVentas':
            if ($result['dataset'] = $productos->readProductosVentasSearch($_POST['buscar-producto'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'No hay coincidencias';
            }
            break;
        case 'ventasPorSemana':
            if ($result['dataset'] = $productos->ventasPorSemana()) {
                $result['status'] = 1;
            } else {
                $result['exception'] = 'No hay datos disponibles';
            }
            break;
        case 'ventasPorSemanaEstadistica':
            if ($result['dataset'] = $productos->estadisticaVentasPorSemana()) {
                $result['status'] = 1;
            } else {
                $result['exception'] = 'No hay datos disponibles';
            }
            break;
        case 'inventarioGProveedor':
            if ($result['dataset'] = $productos->inventarioGProveedor()) {
                $result['status'] = 1;
            } else {
                $result['exception'] = 'No hay datos disponibles';
            }
            break;
        case 'flujoPrecio':
            if (!$productos->setId($_POST['id-stats'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $productos->flujoPrecio($_POST['start-date'], $_POST['end-date'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Flujo de Precio, no hay datos disponibles';
            }
            break;
        case 'flujoStock':
            if (!$productos->setId($_POST['id-stats'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $productos->flujoStock($_POST['start-date'], $_POST['end-date'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Flujo de Stock, no hay datos disponibles';
            }
            break;

        case 'flujoVentas':
            if (!$productos->setId($_POST['id-stats'])) {
                $result['exception'] = 'Producto incorrecto';
            } elseif ($result['dataset'] = $productos->flujoVentas($_POST['start-date'], $_POST['end-date'])) {
                $result['status'] = 1;
            } elseif (Database::getException()) {
                $result['exception'] = Database::getException();
            } else {
                $result['exception'] = 'Flujo de Ventas, no hay datos disponibles';
            }
            break;
        default:
            echo($_GET['action']);
            $result['exception'] = 'Acción no disponible dentro de la sesión';
    }
    // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
    header('content-type: application/json; charset=utf-8');
    // Se imprime el resultado en formato JSON y se retorna al controlador.
    print(json_encode($result));
} else {
    print(json_encode('Recurso no disponible'));
}
