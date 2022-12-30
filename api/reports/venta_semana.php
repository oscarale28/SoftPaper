<?php
if (isset($_GET['fechaV'])){

            // Archivos necesarios para crear el reporte---------->
            require('../helpers/dashboard_report.php');
            require('../models/usuarios.php');
            require('../models/venta.php');

            // Se instancia el módelo usuario para procesar los datos.
            $usuario = new Usuarios;

                // Se verifica si la categoría del parametro existe, de lo contrario se direcciona a la página web de origen.
                if ($rowUsuario = $usuario->readAll()) {

                    // Se instancia la clase para crear el reporte.
                    $pdf = new Report;

                    // Se inicia el reporte con el encabezado del documento.
                    $pdf->startReport('Ingresos en una semana');

                    // Se instancia el módelo proveedor para procesar los datos.
                    $venta = new Ventas;
                        //Color del texto y color de los margenes--------->
                        $pdf->SetTextColor(31, 30, 44);//Define el color del texto-->
                        $pdf->SetDrawColor(0, 126, 176);

                        //Agregamos fuentes externas
                        $pdf->addFont('Mohave-Bold','','Mohave-Bold.php');//Importa una fuente y la pone a disposición-->
                        $pdf->addFont('Mohave-Light','','Mohave-Light.php');

                        $pdf->setFont('Mohave-Bold','',12);//Establece la fuente utilizada para la cel del proveedor----->

                        // Se establece un color de relleno para mostrar el nombre del proveedor
                        $pdf->setFillColor(0, 126, 176);
                        // Se dibuja la celda del proveedor------------>
                            // $pdf->cell(0, 10, utf8_decode('Ingreso Total: '.$rowUsuario['nombre_cliente']), 0, 1, 'C', 1);
                            
                            $fechaS = $_GET['fechaV'];
                            if ($datapro = $venta->ventasPorSemanaReport($fechaS)) {
                                // Se establece un color de relleno para los encabezados.
                                $pdf->setFillColor(218, 242, 255);//Define el color utilizado para todas las operaciones de relleno (rectángulos rellenos y fondos de celda)-->
                                // Se establece la fuente para los encabezados.
                                
                                $pdf->setFont('Mohave-Bold','',12);//Establece la fuente utilizada para imprimir cadenas de caracteres-->
                                // Se imprimen las celdas con los encabezados.
                                $pdf->cell(12, 10, utf8_decode('Corr.'), 0, 0, 'C', 1);
                                $pdf->cell(44, 10, utf8_decode('Cliente'), 0, 0, 'C', 1);//Imprime una celda (área rectangular) con bordes opcionales, color de fondo y cadena de caracteres-->
                                $pdf->cell(38, 10, utf8_decode('Tipo de venta'), 0, 0, 'C', 1);
                                $pdf->cell(38, 10, utf8_decode('Tipo de factura'), 0, 0, 'C', 1);
                                $pdf->cell(30, 10, utf8_decode('Fecha'), 0, 0, 'C', 1);
                                $pdf->cell(24, 10, utf8_decode('Monto $'), 0, 1, 'C', 1);
                            
                                // Se establece la fuente para los datos de los proveedor.
                                $pdf->setFont('Mohave-Light','',12);
                                $pdf->SetDrawColor(218, 242, 255);
                                // Se recorren los registros ($datapro) fila por fila ($rowproveedor).
                                $suma = 0.00;
                                foreach ($datapro as $rowUsuario) {
                                    // Se imprimen las celdas con los datos de los proveedor.
                                    $pdf->cell(12, 10, utf8_decode($rowUsuario['correlativo_venta']), 'B', 0,'C');
                                    $pdf->cell(44, 10, utf8_decode($rowUsuario['nombre_cliente']), 'B', 0,'C');
                                    $pdf->cell(38, 10, utf8_decode($rowUsuario['tipo_venta']), 'B', 0, 'C');
                                    $pdf->cell(38, 10, utf8_decode($rowUsuario['tipo_factura']), 'B', 0, 'C'); //
                                    $pdf->cell(30, 10, utf8_decode($rowUsuario['fecha_venta']), 'B', 0,'C');
                                    $pdf->cell(24, 10, $rowUsuario['monto'], 'B', 1, 'C');
                                    $suma = $suma + $rowUsuario['monto'];
                                }
                                $pdf->setFillColor(0, 126, 176);
                                $pdf->SetTextColor(255, 255, 255);
                                $pdf->cell(0, 10, utf8_decode('Ingreso Total  $'.$suma), 1, 1, 'C', 1);
                            } else {
                                $pdf->cell(0, 10, utf8_decode('No hay ventas para mostrar'), 1, 1);
                            }
                        // Se envía el documento al navegador y se llama al método footer()
                        $pdf->output('I', 'ventas_semana.pdf');
                } else {
                    header('location: ../../views/private/dashboard.html');
                }
} else {
    header('location: ../../views/private/dashboard.html');
}



