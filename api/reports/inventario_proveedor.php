<?php
if (isset($_GET['id_proveedor'])){
    // Archivos necesarios para crear el reporte---------->
    require('../helpers/dashboard_report.php');
    require('../models/proveedor.php');
    require('../models/productos.php');

    // Se instancia el módelo proveedor para procesar los datos.
    $proveedor = new Proveedor;

    // Se verifica si el parámetro es un valor correcto, de lo contrario se direcciona a la página web de origen.
    if ($proveedor->setId($_GET['id_proveedor'])) {

        // Se verifica si la categoría del parametro existe, de lo contrario se direcciona a la página web de origen.
        if ($rowProveedor = $proveedor->readOne()) {

            // Se instancia la clase para crear el reporte.
            $pdf = new Report;

            // Se inicia el reporte con el encabezado del documento.
            $pdf->startReport('Productos disponibles');

            // Se instancia el módelo proveedor para procesar los datos.
            $producto = new Productos;
            if ($producto->setProveedor($rowProveedor['uuid_proveedor'])) {
                //Color del texto y color de los margenes--------->
                $pdf->SetTextColor(255);//Define el color del texto-->
                $pdf->SetDrawColor(0, 126, 176);

                //Agregamos fuentes externas
                $pdf->addFont('Mohave-Bold','','Mohave-Bold.php');//Importa una fuente y la pone a disposición-->
                $pdf->addFont('Mohave-Light','','Mohave-Light.php');

                $pdf->setFont('Mohave-Bold','',12);//Establece la fuente utilizada para la cel del proveedor----->

                 // Se establece un color de relleno para mostrar el nombre del proveedor
                $pdf->setFillColor(0, 126, 176);
                // Se dibuja la celda del proveedor------------>
                $pdf->cell(0, 10, utf8_decode('Proveedor: '.$rowProveedor['nombre_proveedor']), 1, 1, 'C', 1);

                $pdf->SetTextColor(31, 30, 44);//Define el color del texto-->
                if ($datapro = $producto->inventarioProveedor()) {
                    // Se establece un color de relleno para los encabezados.
                    $pdf->setFillColor(218, 242, 255);//Define el color utilizado para todas las operaciones de relleno (rectángulos rellenos y fondos de celda)-->
                    // Se establece la fuente para los encabezados.
                    $pdf->SetDrawColor(218, 242, 255);
                    $pdf->setFont('Mohave-Bold','',12);//Establece la fuente utilizada para imprimir cadenas de caracteres-->
                    // Se imprimen las celdas con los encabezados.
                    $pdf->cell(46, 10, utf8_decode('Producto'), 'B', 0, 'C', 1);//Imprime una celda (área rectangular) con bordes opcionales, color de fondo y cadena de caracteres-->
                    $pdf->cell(38, 10, utf8_decode('Subcategoria'), 'B', 0, 'C', 1);
                    $pdf->cell(38, 10, utf8_decode('Marca'), 'B', 0, 'C', 1);
                    $pdf->cell(32, 10, utf8_decode('Precio'), 'B', 0, 'C', 1);
                    $pdf->cell(32, 10, utf8_decode('Estado'), 'B', 1, 'C', 1);
                
                    // Se establece la fuente para los datos de los proveedor.
                    $pdf->setFont('Mohave-Light','',12);
                    // Se recorren los registros ($datapro) fila por fila ($rowproveedor).
                    foreach ($datapro as $rowpedido) {
                        // Se imprimen las celdas con los datos de los proveedor.
                        $pdf->cell(46, 10, utf8_decode($rowpedido['nombre_producto']), 'B', 0,'C');
                        $pdf->cell(38, 10, utf8_decode($rowpedido['nombre_subcategoria_p']), 'B', 0, 'C');
                        $pdf->cell(38, 10, utf8_decode($rowpedido['nombre_marca']), 'B', 0, 'C'); //
                        $pdf->cell(32, 10, utf8_decode($rowpedido['precio_producto']), 'B', 0,'C');
                        $pdf->cell(32, 10, $rowpedido['estado_producto'], 'B', 1, 'C');
                    }
                } else {
                    $pdf->cell(0, 10, utf8_decode('No hay ventas para mostrar'), 1, 1);
                }
                // Se envía el documento al navegador y se llama al método footer()
                $pdf->output('I', 'inventario_proveedor.pdf');

            } else {
                header('location: ../../views/private/dashboard.html');
            }
        } else {
            header('location: ../../views/private/dashboard.html');
        }
    } else {
        header('location: ../../views/private/dashboard.html');
    }
} else {
    header('location: ../../views/private/dashboard.html');
}



