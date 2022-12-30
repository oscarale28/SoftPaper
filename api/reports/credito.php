<?php
require('../helpers/consumidor_report.php');
require('../models/venta.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Credito fiscal');


// Se instancia el módelo Categorías para obtener los datos.
$venta = new Ventas;
// Se verifica si el parámetro es un valor correcto, de lo contrario se direcciona a la página web de origen.
if ($venta->setId($_GET['uuid_venta'])) {
    if ($infoClie = $venta->detalleClie()) {
        //Añadimos la fuente poppins externa para el título del reporte
        $pdf->SetTextColor(50, 54, 57);
        $pdf->addFont('Mohave-Bold','','Mohave-Bold.php');
        $pdf->addFont('Mohave-Light','','Mohave-Light.php');

        $pdf->ln(-2);
        //celda 1 nombre cliente y fecha
        $pdf->setFont('Mohave-Light','',6);
        $pdf->cell(45, 4, utf8_decode($infoClie['nombre_cliente']), 0, 0, 'R');
        $pdf->cell(25, 4, "", 0, 0, 'R');
        $pdf->cell(30, 4, utf8_decode($infoClie['fecha']), 0, 0, 'R');
        $pdf->ln(3);

        //celda 2 Direccion y Registro
        $pdf->setFont('Mohave-Light','',6);
        $pdf->cell(50, 6, utf8_decode($infoClie['direccion_cliente']), 0, 0, 'R');
        $pdf->cell(15, 6, "", 0, 0, 'R');
        $pdf->cell(25, 6, "", 0, 0, 'R');
        $pdf->ln(3);

        //celda 3 municipio y giro
        $pdf->setFont('Mohave-Light','',6);
        $pdf->cell(45, 6, utf8_decode($infoClie['nombre_municipio']), 0, 0, 'R');
        $pdf->cell(15, 6, "", 0, 0, 'R');
        $pdf->cell(30, 6, utf8_decode($infoClie['giro_cliente']), 0, 0, 'R');
        $pdf->ln(3);

        //celda 4 Departamento y nit 
        $pdf->setFont('Mohave-Light','',6);
        $pdf->cell(50, 6, utf8_decode($infoClie['nombre_departamento']), 0, 0, 'R');
        $pdf->cell(10, 6, "", 0, 0, 'R');
        $pdf->cell(35, 6, utf8_decode($infoClie['nit_cliente']), 0, 0, 'R');
        $pdf->ln(3);

        //celda 5 Condicion y Venta a cuenta de 
        $pdf->setFont('Mohave-Light','',6);
        $pdf->cell(45, 6, "", 0, 0, 'R');
        $pdf->cell(10, 6, "", 0, 0, 'R');
        $pdf->cell(35, 6, "", 0, 0, 'R');
        $pdf->ln(3);

        //celda 6 Condicion y Venta a cuenta de 
        $pdf->setFont('Mohave-Light','',6);
        $pdf->cell(45, 6, "", 0, 0, 'R');
        $pdf->cell(10, 6, "", 0, 0, 'R');
        $pdf->cell(35, 6, "", 0, 0, 'R');
        $pdf->ln(3);

        $pdf->cell(10, 5, "", 0, 0, 'R');
        $pdf->ln(8);
        if ($datafactura = $venta->creditoFiscal()) {
            // Se establece la fuente para los encabezados.
            $pdf->setFont('Mohave-Light','',6);
            //Se establece el color de las líneas de la tabla
            foreach ($datafactura as $rowFactura) {
                // Se imprimen las celdas con los datos de los productos.
                //El parametro B es para poner bordes solo en bottom
                $pdf->cell(18, 5, $rowFactura['cantidad_producto'], 0, 0, 'R');
                $pdf->cell(40, 5, utf8_decode($rowFactura['nombre_producto']), 0, 0, 'C');
                $pdf->cell(17, 5, '$'.$rowFactura['precio_unitario'], 0, 0,'R');
                $pdf->cell(9, 5, "", 0, 0, 'R');
                $pdf->cell(15, 5, '$'.$rowFactura['monto_producto'], 0, 1, 'R');
            }
        }
        $pdf->ln(5);
        //Si se pudo obtener el monto se procede a mostrarlo, se guardan todos los datos en el array $monto
        if ($monto = $venta->montoCredito()){

            $pdf->ln(43);
            //Total de la venta sin iva
            $pdf->setFont('Mohave-Light','',6);
            $pdf->cell(99, 9, "$".number_format($monto['monto_total'] ), 0, 1, 'R');

            //Se desgloza el iva
            $pdf->setFont('Mohave-Light','',6);
            $pdf->cell(99, 3, ("$".number_format(($monto['monto_total'] * 0.13), 2, '.', "")), 0, 1, 'R');
            $pdf->ln(8);

            //Total a pagar con iva
            $pdf->setFont('Mohave-Bold','',6);
            $pdf->cell(99, 7, ("$".number_format(($monto['monto_total'] * 1.13), 2, '.', "")), 0, 1, 'R');
        }
        else{
            $pdf->cell(0, 5, utf8_decode('No hay detalle para mostrar'), 1, 1);
        }
    }
} else {
    header('location: ../../views/private/detalle_venta.html');
}

// Se envía el documento al navegador y se llama al método footer()
$pdf->output('I', 'credito_fiscal.pdf');