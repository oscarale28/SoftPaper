<?php
if (isset($_GET['id_empleado'])){

    if (isset($_GET['fechas'])){
        
        if (isset($_GET['fechaf'])){
            // Archivos necesarios para crear el reporte---------->
            require('../helpers/dashboard_report.php');
            require('../models/usuarios.php');
            require('../models/venta.php');

            // Se instancia el módelo usuario para procesar los datos.
            $usuario = new Usuarios;

            // Se verifica si el parámetro es un valor correcto, de lo contrario se direcciona a la página web de origen.
            if ($usuario->setId($_GET['id_empleado'])) {

                // Se verifica si la categoría del parametro existe, de lo contrario se direcciona a la página web de origen.
                if ($rowUsuario = $usuario->readOne()) {

                    // Se instancia la clase para crear el reporte.
                    $pdf = new Report;

                    // Se inicia el reporte con el encabezado del documento.
                    $pdf->startReport('Ventas por empleado');

                    // Se instancia el módelo proveedor para procesar los datos.
                    $venta = new Ventas;
                    if ($venta->setEmpleado($rowUsuario['uuid_empleado'])) {
                        $fechaS = $_GET['fechas'];
                        $fechaF = $_GET['fechaf'];

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
                            $pdf->cell(0, 10, utf8_decode('Empleado: '.$rowUsuario['nombres_empleado']), 0, 1, 'C', 1);
                            $pdf->SetDrawColor(218, 242, 255);
                            $pdf->SetTextColor(31, 30, 44);//Define el color del texto-->
                            if ($datapro = $usuario->readRowsReportDate($fechaS, $fechaF)) {
                                // Se establece un color de relleno para los encabezados.
                                $pdf->setFillColor(218, 242, 255);//Define el color utilizado para todas las operaciones de relleno (rectángulos rellenos y fondos de celda)-->
                                // Se establece la fuente para los encabezados.
                                
                                $pdf->setFont('Mohave-Bold','',12);//Establece la fuente utilizada para imprimir cadenas de caracteres-->
                                // Se imprimen las celdas con los encabezados.
                                $pdf->cell(12, 10, utf8_decode('Corr.'), 1, 0, 'C', 1);
                                $pdf->cell(42, 10, utf8_decode('Cliente'), 1, 0, 'C', 1);//Imprime una celda (área rectangular) con bordes opcionales, color de fondo y cadena de caracteres-->
                                $pdf->cell(36, 10, utf8_decode('Tipo de venta'), 1, 0, 'C', 1);
                                $pdf->cell(36, 10, utf8_decode('Tipo de factura'), 1, 0, 'C', 1);
                                $pdf->cell(30, 10, utf8_decode('Fecha'), 1, 0, 'C', 1);
                                $pdf->cell(30, 10, utf8_decode('Estado'), 1, 1, 'C', 1);
                            
                                // Se establece la fuente para los datos de los proveedor.
                                $pdf->setFont('Mohave-Light','',12);
                                // Se recorren los registros ($datapro) fila por fila ($rowproveedor).
                                foreach ($datapro as $rowUsuario) {
                                    // Se imprimen las celdas con los datos de los proveedor.
                                    $pdf->cell(12, 10, utf8_decode($rowUsuario['correlativo_venta']), 'B', 0,'C');
                                    $pdf->cell(42, 10, utf8_decode($rowUsuario['nombre_cliente']), 'B', 0,'C');
                                    $pdf->cell(36, 10, utf8_decode($rowUsuario['tipo_venta']), 'B', 0, 'C');
                                    $pdf->cell(36, 10, utf8_decode($rowUsuario['tipo_factura']), 'B', 0, 'C'); //
                                    $pdf->cell(30, 10, utf8_decode($rowUsuario['fecha_venta']), 'B', 0,'C');
                                    $pdf->cell(30, 10, $rowUsuario['estado_venta'], 'B', 1, 'C');
                                }
                            } else {
                                $pdf->cell(0, 10, utf8_decode('No hay ventas para mostrar'), 1, 1);
                            }
                        // Se envía el documento al navegador y se llama al método footer()
                        $pdf->output('I', 'ventas_empleado.pdf');

                    } else {
                        header('location: ../../views/private/empleados.html');
                    }
                } else {
                    header('location: ../../views/private/empleados.html');
                }
            } else {
                hheader('location: ../../views/private/empleados.html');
            }
        } else {
            header('location: ../../views/private/empleados.html');
        }
    } else {
        header('location: ../../views/private/empleados.html');
    }
} else {
    header('location: ../../views/private/empleados.html');
}



