<?php
if (isset($_GET['uuid_producto'])){
    // Archivos necesarios para crear el reporte---------->
    require('../helpers/dashboard_report.php');
    require('../models/productos.php');

    // Se instancia el módelo proveedor para procesar los datos.
    $producto = new Productos;

    // Se verifica si el parámetro es un valor correcto, de lo contrario se direcciona a la página web de origen.
    if ($producto->setId($_GET['uuid_producto'])) {
        // Se verifica si la categoría del parametro existe, de lo contrario se direcciona a la página web de origen.
        if ($rowproducto = $producto->readOne()) {

            // Se instancia la clase para crear el reporte.
            $pdf = new Report;

            // Se inicia el reporte con el encabezado del documento.
            $pdf->startReport('Ventas de un producto');
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
                if ($productoInfo = $producto->flujoVentasReportInfo()){
                    $pdf->cell(186, 10, (utf8_decode($productoInfo['nombre_producto'])), 0, 1, 'C', 1);
                }else{
                    $pdf->cell(186, 10, utf8_decode('Producto incorrecto'), 0, 1, 'C', 1);
                }

                $pdf->SetTextColor(31, 30, 44);//Define el color del texto-->
                if ($dataproducto = $producto->flujoVentasReport($_GET['start'], $_GET['end'])) {
                    // Se establece un color de relleno para los encabezados.
                    $pdf->setFillColor(218, 242, 255);//Define el color utilizado para todas las operaciones de relleno (rectángulos rellenos y fondos de celda)-->
                    // Se establece la fuente para los encabezados.
                    
                    $pdf->setFont('Mohave-Bold','',12);//Establece la fuente utilizada para imprimir cadenas de caracteres-->
                    // Se imprimen las celdas con los encabezados.
                    $pdf->SetDrawColor(218, 242, 255);
                    $pdf->cell(93, 10, utf8_decode('Fecha de venta'), 0, 0, 'C', 1);//Imprime una celda (área rectangular) con bordes opcionales, color de fondo y cadena de caracteres-->
                    $pdf->cell(93, 10, utf8_decode('Monto'), 0, 1, 'C', 1);
                
                    // Se establece la fuente para los datos de los proveedor.
                    $pdf->setFont('Mohave-Light','',12);
                    // Se recorren los registros ($datapro) fila por fila ($rowproveedor).
                    foreach ($dataproducto as $rowproducto) {
                        // Se imprimen las celdas con los datos de los proveedor.
                        $pdf->cell(93, 10, $rowproducto['fecha_venta'], 'B', 0,'C');
                        $pdf->cell(93, 10, $rowproducto['monto'], 'B', 1,'C');
                    }
                } else {
                    $pdf->cell(0, 10, utf8_decode('No flujo para mostrar'), 1, 1);
                }
                // Se envía el documento al navegador y se llama al método footer()
                $pdf->output('I', 'flujo_ventas.pdf');

        } else {
            header('location: ../../views/private/productos.html');
        }
    } else {
        header('location: ../../views/private/productos.html');
    }
} else {
    header('location: ../../views/private/productos.html');
}
