const ENDPOINT_PROVEEDOR = SERVER + 'private/proveedor.php?action=readAll';

function selectProv() {
    /* Se llama a la función que llena el select del formulario. Se encuentra en el archivo components.js, 
    * mandar de parametros la ruta de la api de la tabla que utiliza el select, y el id del select*/
    fillSelect(ENDPOINT_PROVEEDOR, 'proveedor', null);
}


document.getElementById('btn-reporte1').addEventListener('click', function () {
    let param;
    param = '?id_proveedor=' + document.getElementById('proveedor').value;
    // Se establece la ruta del reporte en el servidor.
    let url = SERVER + 'reports/inventario_proveedor.php';
    // Se abre el reporte en una nueva pestaña del navegador web.
    console.log(url+param);  
    window.open(url + param);
});

