<?php
require('../helpers/consumidor_report.php');
require('../models/venta.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Consumidor final');


// Se instancia el módelo Categorías para obtener los datos.
$venta = new Ventas;
// Se verifica si el parámetro es un valor correcto, de lo contrario se direcciona a la página web de origen.
if ($venta->setId($_GET['uuid_venta'])) {
    if ($infoClie = $venta->detalleClie()) {
        //Añadimos la fuente poppins externa para el título del reporte
        $pdf->SetTextColor(117, 54, 90);
        $pdf->addFont('Mohave-Bold','','Mohave-Bold.php');
        $pdf->addFont('Mohave-Light','','Mohave-Light.php');

        //celda 1 nombre cliente y fecha
        $pdf->setFont('Mohave-Light','',8);
        $pdf->cell(45, 5, "Ventas varias", 0, 0, 'R');
        $pdf->cell(20, 5, "", 0, 0, 'R');
        $pdf->cell(35, 5, utf8_decode($infoClie['fecha']), 0, 0, 'R');
        $pdf->ln(4);

        //celda 2 DUI y telefono
        $pdf->setFont('Mohave-Light','',8);
        $pdf->cell(50, 5,"", 0, 0, 'R');
        $pdf->cell(15, 5, "", 0, 0, 'R');
        $pdf->cell(35, 5, "", 0, 0, 'R');
        $pdf->ln(4);

        //celda 3 Direccion 
        $pdf->setFont('Mohave-Light','',8);
        $pdf->cell(50, 5,"", 0, 0, 'R');
        $pdf->cell(10, 5, "", 0, 0, 'R');
        $pdf->cell(35, 5, "", 0, 0, 'R');
        $pdf->ln(4);

        //celda 4 Venta de 
        $pdf->setFont('Mohave-Light','',8);
        $pdf->cell(45, 5, utf8_decode($_SESSION['alias_usuario']), 0, 0, 'R');
        $pdf->cell(10, 5, "", 0, 0, 'R');
        $pdf->cell(35, 5, "", 0, 0, 'R');
        $pdf->ln(4);

        $pdf->cell(10, 5, "", 0, 0, 'R');
        $pdf->ln(8);
        if ($datafactura = $venta->consumidorFinal()) {
            // Se establece la fuente para los encabezados.
            $pdf->setFont('Mohave-Light','',8);
            //Se establece el color de las líneas de la tabla
            foreach ($datafactura as $rowFactura) {
                // Se imprimen las celdas con los datos de los productos.
                //El parametro B es para poner bordes solo en bottom
                $pdf->cell(18, 5, $rowFactura['cantidad_producto'], 0, 0, 'R');
                $pdf->cell(40, 5, utf8_decode($rowFactura['nombre_producto']), 0, 0, 'C');
                $pdf->cell(17, 5, '$'.$rowFactura['precio_unitario'], 0, 0,'R');
                $pdf->cell(9, 5, "", 0, 0, 'R');
                $pdf->cell(16, 5, '$'.$rowFactura['monto_producto'], 0, 1, 'R');
            }
        }
        $pdf->ln(5);
        //Si se pudo obtener el monto se procede a mostrarlo, se guardan todos los datos en el array $monto
        if ($monto = $venta->montoConsumidorFinal()){

            $pdf->ln(55);
            //Dato del monto de la venta sin el iva-----------
            $pdf->setFont('Mohave-Light','',8);
            $pdf->cell(100, 5, "$".number_format($monto['monto_total'] ), 0, 1, 'R');
            $pdf->ln(6);

            //Resaltamos el dato del monto con iva aumentando el tamaño de la fuente 
            $pdf->setFont('Mohave-Bold','',8);
            $pdf->cell(100, 5, ("$".number_format(($monto['monto_total'] * 1.13), 2, '.', "")), 0, 0, 'R');
        }
        else{
            $pdf->cell(0, 5, utf8_decode('No hay detalle para mostrar'), 1, 1);
        }
    }
} else {
    header('location: ../../views/private/detalle_venta.html');
}
    

// Se envía el documento al navegador y se llama al método footer()
$pdf->output('I', 'vetas_dias.pdf');