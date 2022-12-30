<?php
if (isset($_GET['id_categoria'])){
    // Archivos necesarios para crear el reporte---------->
    require('../helpers/dashboard_report.php');
    require('../models/categoria.php');
    require('../models/venta.php');

    // Se instancia el módelo proveedor para procesar los datos.
    $categoria = new Categoria;

    // Se verifica si el parámetro es un valor correcto, de lo contrario se direcciona a la página web de origen.
    if ($categoria->setId($_GET['id_categoria'])) {

        // Se verifica si la categoría del parametro existe, de lo contrario se direcciona a la página web de origen.
        if ($rowCategoria = $categoria->readOne()) {

            // Se instancia la clase para crear el reporte.
            $pdf = new Report;

            // Se inicia el reporte con el encabezado del documento.
            $pdf->startReport('Ventas por categoría');

            // Se instancia el módelo proveedor para procesar los datos.
            $venta = new Ventas;
            if ($venta->setCategoria($rowCategoria['uuid_categoria_p'])) {
                //Color del texto y color de los margenes--------->
                $pdf->SetTextColor(31, 30, 44);//Define el color del texto-->
                $pdf->SetDrawColor(0, 126, 176);

                //Agregamos fuentes externas
                $pdf->addFont('Mohave-Bold','','Mohave-Bold.php');//Importa una fuente y la pone a disposición-->
                $pdf->addFont('Mohave-Light','','Mohave-Light.php');

                $pdf->setFont('Mohave-Bold','',12);//Establece la fuente utilizada para la cel del proveedor----->

                 // Se establece un color de relleno para mostrar el nombre del proveedor
                $pdf->setFillColor(0, 126, 176);
                $pdf->SetTextColor(255, 255, 255);
                // Se dibuja la celda del proveedor------------>
                $pdf->cell(0, 10, utf8_decode('Categoría: '.$rowCategoria['nombre_categoria_p']), 1, 1, 'C', 1);

                if ($dataVentas = $venta->ventasCategoriaR()) {
                    // Se establece un color de relleno para los encabezados.
                    $pdf->setFillColor(218, 242, 255);//Define el color utilizado para todas las operaciones de relleno (rectángulos rellenos y fondos de celda)-->
                    $pdf->SetTextColor(0, 0, 0);
                    // Se establece la fuente para los encabezados.
                    
                    $pdf->setFont('Mohave-Bold','',12);//Establece la fuente utilizada para imprimir cadenas de caracteres-->
                    // Se imprimen las celdas con los encabezados.
                    $pdf->SetDrawColor(218, 242, 255);
                    $pdf->cell(35, 10, utf8_decode('Fecha'), 0, 0, 'C', 1);//Imprime una celda (área rectangular) con bordes opcionales, color de fondo y cadena de caracteres-->
                    $pdf->cell(55, 10, utf8_decode('Producto'), 0, 0, 'C', 1);//Imprime una celda (área rectangular) con bordes opcionales, color de fondo y cadena de caracteres-->
                    $pdf->cell(32, 10, utf8_decode('Precio Unitario'), 0, 0, 'C', 1);
                    $pdf->cell(32, 10, utf8_decode('Unidades vendidas'), 0, 0, 'C', 1);
                    $pdf->cell(32, 10, utf8_decode('Monto'), 0, 0, 'C', 1);
                    $pdf->ln(10);
                
                    // Se establece la fuente para los datos de los proveedor.
                    $pdf->setFont('Mohave-Light','',12);
                    // Se recorren los registros ($datapro) fila por fila ($rowproveedor).
                    foreach ($dataVentas as $rowventas) {
                        // Se imprimen las celdas con los datos de los proveedor.
                        $pdf->cell(35, 10, utf8_decode($rowventas['fecha_venta']), 'B', 0,'C');
                        $pdf->cell(55, 10, utf8_decode($rowventas['nombre_producto']), 'B', 0,'C');
                        $pdf->cell(32, 10, '$'.number_format(($rowventas['precio_unitario']), 2, '.', ""), 'B', 0, 'C');
                        $pdf->cell(32, 10, utf8_decode($rowventas['cantidad_producto']), 'B', 0, 'C'); //
                        $pdf->cell(32, 10, '$'.number_format(($rowventas['monto_total']), 2, '.', ""), 'B', 0,'C');
                        $pdf->ln(10);
                    }
                    $pdf->ln(5);//Realiza un salto de línea-->
                    $pdf->setFillColor(0, 126, 176);
                    $pdf->SetTextColor(255, 255, 255);
                    if ($totalVentas = $venta->montoReporteVentasCategoria()){
                        $pdf->setFont('Mohave-Bold','',12);
                        $pdf->cell(154, 10, utf8_decode('Monto total de ventas en la semana:'), 0, 0, 'R', 1);
                        $pdf->cell(32, 10, ('$'.$totalVentas['monto_semanal']), 0, 0, 'C', 1);
                    }
                    else{
                        $pdf->cell(0, 10, utf8_decode('No hay total de ventas para mostrar'), 1, 1);
                    }
                } else {
                    $pdf->SetTextColor(0, 0, 0);
                    $pdf->cell(0, 10, utf8_decode('No hay ventas para mostrar'), 'B', 1, 'C', 0);
                }
                // Se envía el documento al navegador y se llama al método footer()
                $pdf->output('I', 'ventas_categoria.pdf');

            } else {
                $pdf->cell(0, 10, utf8_decode('Categoría incorrecta o inexistente'), 1, 1);
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



