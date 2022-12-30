const API_VENTAS = SERVER + "private/venta.php?action=";
const API_PRODUCTOS = SERVER + "private/productos.php?action=";
const ENDPOINT_COLOR = SERVER + "private/color.php?action=readProductoColor";
const API_CLIENTES = SERVER + "private/clientes.php?action=";
const ENDPOINT_TIPOVENTA = SERVER + "private/tipo_venta.php?action=readAllTVDetalle";
const ENDPOINT_TIPOFACTURA = SERVER + "private/tipo_factura.php?action=readAllTFDetalle";

const options = {
    info: false,
    columnDefs: [],
    searching: false,
    dom:
        "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-2 text-center'l><'col-sm-10'p>>",
    language: {
        lengthMenu: "Mostrando _MENU_ registros",
        paginate: {
            next: '<i class="bi bi-arrow-right-short"></i>',
            previous: '<i class="bi bi-arrow-left-short"></i>',
        },
    },
    lengthMenu: [
        [10, 15, 20, -1],
        [10, 15, 20, "Todos"],
    ],
};

const optionsCli = {
    info: false,
    columnDefs: [],
    searching: false,
    select: true,
    dom:
        "<'row'<'col-sm-12'tr>>" + "<'row'<'col-sm-2 text-center'l><'col-sm-10'p>>",
    language: {
        lengthMenu: "Mostrando _MENU_ registros",
        paginate: {
            next: '<i class="bi bi-arrow-right-short"></i>',
            previous: '<i class="bi bi-arrow-left-short"></i>',
        },
    },
    lengthMenu: [
        [10, 15, 20, -1],
        [10, 15, 20, "Todos"],
    ],
};
let maxStock;
let idVenta;
let cliente;
const table = "#table-productos";
const table2 = "#table-clientes";
let tableClientes;
let productos_agregados;

let cantidadProductos = [];   
// Método manejador de eventos que se ejecuta cuando el documento ha cargado.
document.addEventListener("DOMContentLoaded", function () {
    fillSelect(ENDPOINT_TIPOVENTA, "tipo-venta", null);
    fillSelect(ENDPOINT_TIPOFACTURA, "tipo-factura", null);
    readRows(API_PRODUCTOS, "readProductosVentas", table, options);
    readRows2(API_CLIENTES, "readClientesVenta", table2, optionsCli);

    // Se busca en la URL las variables (parámetros) disponibles.
    let params = new URLSearchParams(location.search);
    if (Array.from(params).length > 0) {
        // Se obtienen los datos localizados por medio de las variables.
        idVenta = params.get("uuid_venta");
        // Se llama a la función que muestra el detalle del producto seleccionado previamente.
        readOrderDetail(idVenta);
    }
});

// Método para volver a cargar la tabla de productos después de cualquier cambio en los datos de esta
const reInitTable = () => {
    $(table).DataTable().destroy();
    readRows(API_PRODUCTOS, "readProductosVentas", table, options);
};

// Método para volver a cargar la tabla de clientes después de cualquier cambio en los datos de esta
const reInitTable2 = () => {
    $(table2).DataTable().destroy();
    readRows2(API_CLIENTES, "readClientesVenta", table2, optionsCli);
};

// Método para leer el detalle de la venta y restaurar en caso se quiera
function readOrderDetail(uuid_venta) {
    let validarVenta = JSON.parse(sessionStorage.getItem('ventaRestaurada'));
    console.log(validarVenta);
    if(validarVenta){
        //Si existe una venta en proceso, se puede restaurar o crear una nueva
            swal({
                title: 'Advertencia',
                text: '¿Hay una venta en proceso, desea continuarla?',
                icon: 'warning',
                buttons: ['No', 'Sí'],
                closeOnClickOutside: false,
                closeOnEsc: false
            }).then(function (value) {
                // Se verifica si fue cliqueado el botón Sí para hacer la petición de leer la venta en proceso, de lo contrario la borrar y crea una nueva desde 0.
                if (value) {
                    //Continuar, restaurar venta en proceso
                    readOrderDetailNotInit(uuid_venta);
                } else {
                    //No continuar, cancelar venta en proceso y empezar una nueva
                    // Se define un objeto con los datos del producto seleccionado.
                    const data = new FormData();
                    data.append("idVenta", uuid_venta);
                    // Petición para finalizar el pedido en proceso.
                    fetch(API_VENTAS + "cancelOrder", {
                        method: "post",
                        body: data,
                    }).then(function (request) {
                        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                        if (request.ok) {
                            request.json().then(function (response) {
                                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                                if (response.status) {
                                    // Petición para agregar un producto al pedido.
                                    fetch(API_VENTAS + 'startOrder', {
                                        method: 'get'
                                    }).then(function (request) {
                                        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                                        if (request.ok) {
                                            // Se obtiene la respuesta en formato JSON.
                                            request.json().then(function (response) {
                                                // Se comprueba si la respuesta es satisfactoria, de lo contrario se constata si el cliente ha iniciado sesión.
                                                if (response.status) {
                                                    switch (response.status) {
                                                        case 1:
                                                            console.log(response.message);
                                                            console.log(response.dataset.uuid_venta);
                                                            location.href = `detalle_venta.html?uuid_venta=${response.dataset}`;
                                                            sessionStorage.setItem('ventaRestaurada',JSON.stringify(false));
                                                            break;
                                                    }
                                                } else {
                                                    console.log('Error: ' + response.exception)
                                                }
                                            });
                                        } else {
                                            console.log(request.status + ' ' + request.statusText);
                                        }
                                    });
                                } else {
                                    sweetAlert(2, response.exception, null);
                                }
                            });
                        } else {
                            console.log(request.status + " " + request.statusText);
                        }
                    });
                    //sweetAlert(4, 'Puede continuar con la sesión', null);
                }
            });
    } else {
        //No hay venta
        readOrderDetailNotInit(uuid_venta);
        readCountDetails(uuid_venta);
    }
}

function readOrderDetailNotInit(uuid_venta){
    const data = new FormData();
    data.append("uuid_venta", uuid_venta);
    // Petición para solicitar los datos del pedido en proceso.
    fetch(API_VENTAS + "readOrderDetail", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se declara e inicializa una variable para concatenar las filas de la tabla en la vista.
                    let content = "";
                    // Se declara e inicializa una variable para calcular el importe por cada producto.
                    let subtotal = 0;
                    // Se declara e inicializa una variable para ir sumando cada subtotal y obtener el monto final a pagar.
                    let total = 0;

                    let iva = 0;
                    let correlativo;

                    let totalVenta = 0;
                    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
                    response.dataset.map(function (row) {
                        correlativo = row.correlativo_venta;
                        subtotal = row.precio_unitario * row.cantidad_producto;
                        total += subtotal;
                        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
                        content += `
                        <div class="col my-2 producto-card">
                            <div class="row mx-0 p-2 border border-dashed rounded producto-card-content align-items-center">
                                <div class="imagen-producto col-3">
                                    <img src="${SERVER}images/productos/${row.imagen_producto}" alt="">
                                </div>
                                <div class="info-producto col-6">
                                    <h4>${row.nombre_producto}</h4>
                                    <h5>Color: ${row.color_producto}</h5>
                                    <h5>${row.cantidad_producto} x $${row.precio_unitario} = <span>$${subtotal.toFixed(2)}</span></h5>
                                </div>
                                <div class="col-3">
                                    <button class="btn btn-cancel" onclick="openUpdateDialog('${row.uuid_detalle_venta}', ${row.cantidad_producto}, '${row.uuid_color_stock}')" data-bs-toggle="modal" data-bs-target="#item-modal-update">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                </div>
                                <i class="bi bi-x" onclick="openDeleteDialog('${row.uuid_detalle_venta}')"></i>
                            </div>
                        </div>
                        `;
                    });
                    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
                    totalVenta = total * 1.13;
                    iva = total  * 0.13;
                    document.getElementById("productos-detalle").innerHTML = content;
                    document.getElementById("correlativo").value = correlativo;
                    // Se muestra el total a pagar con dos decimales.
                    document.getElementById("subtotal-detalle").textContent = `$${parseFloat(total.toFixed(2))}`;
                    document.getElementById("iva").textContent = `$${parseFloat(iva.toFixed(2))}`;
                    document.getElementById("total-venta").textContent = `$${parseFloat(totalVenta.toFixed(2))}`;
                    document.getElementById("monto").value = parseFloat(totalVenta.toFixed(2));
                } else {
                    document.getElementById(
                        "productos-detalle"
                    ).innerHTML = `<h1 class="text-center fs-5 w-100 mt-1">${response.exception}</h1>`;
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
    readCountDetails(uuid_venta);
}

function readCountDetails(uuid_venta){
    const data = new FormData();
    data.append("uuid_venta", uuid_venta);
    // Petición para solicitar los datos del pedido en proceso.
    fetch(API_VENTAS + "readCountDetails", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se declara e inicializa una variable para concatenar las filas de la tabla en la vista.
                    productos_agregados = response.dataset.cantidad_productos;
                    document.getElementById('added-products').innerText = response.dataset.cantidad_productos;
                } else {
                    document.getElementById('added-products').innerText = "";
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}

// Método para cargar los datos de la tabla de productos
function fillTable(dataset) {
    let content = "";
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
        <tr>
            <td>
                <img src="${SERVER}images/productos/${row.imagen_producto}" alt="">
            </td>
            <td class="text-center"> ${row.nombre_producto}</td>
            <td class="text-center">${row.stock}</td>
            <td>
                <a onclick="openProduct('${row.uuid_producto}')" data-bs-toggle="modal" data-bs-target="#item-modal">
                    <i class="bi bi-plus-circle ms-3"></i>
                </a>
            </td>
        </tr> 
		`;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById("tbody_rows").innerHTML = content;
}

// Método para cargar los datos de la tabla de clientes
function fillTable2(dataset) {
    let content = "";
    // Se recorre el conjunto de registros (dataset) fila por fila a través del objeto row.
    dataset.map(function (row) {
        row.estado_cliente
            ? (icon = '<span class="estado">Activo</span>')
            : (icon = '<span class="estado3">Inactivo</span>');
        // Se crean y concatenan las filas de la tabla con los datos de cada registro.
        content += `
        <tr>
            <td class="col-table text-center">${row.nombre_cliente}</td>
            <td class="text-center">${row.dui_cliente}</td>
            <td class="estado-stock">${icon}</td>
        </tr> 
		`;
    });
    // Se agregan las filas al cuerpo de la tabla mediante su id para mostrar los registros.
    document.getElementById("tbody_clientes").innerHTML = content;
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar producto.
document.getElementById('search-form').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('buscar-producto').value == "") {
        sweetAlert(3, 'Campo de búsqueda vacío', null)
    }
    else {
        //Destruimos la instancia de la tabla para volver a crearla con los nuevos datos;
        $(table).DataTable().destroy();
        searchRows(API_PRODUCTOS, 'searchProductosVentas', 'readProductosVentas', 'search-form', 'buscar-producto', table, options);
    }
});

// Método manejador de eventos que se ejecuta cuando se envía el formulario de buscar clientes.
document.getElementById('search-form-cliente').addEventListener('submit', function (event) {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    //Validamos el campo vacío
    if (document.getElementById('buscar-cliente').value == "") {
        sweetAlert(3, 'Campo de búsqueda vacío', null)
    }
    else {
        //Destruimos la instancia de la tabla para volver a crearla con los nuevos datos;
        $(table2).DataTable().destroy();
        searchRows2(API_CLIENTES, 'searchClientesVenta', 'readClientesVenta', 'search-form-cliente', 'buscar-cliente', table2, optionsCli);
        setTimeout(() => {
            /*Inicializando y configurando tabla*/
            tableClientes = $(table2).DataTable();
            tableClientes.on("select", function () {
                rowData = tableClientes.rows({ selected: true }).data()[0][1];
                cliente = rowData;
                console.log(rowData);
            });
        }, 200);
    }
});

//Función para refrescar la tabla de productos manualmente al darle click al botón refresh
document.getElementById('limpiar').addEventListener('click', function () {
    reInitTable();
    document.getElementById('buscar-producto').value = "";
});

//Función para refrescar la tabla de clientes manualmente al darle click al botón refresh
document.getElementById('limpiar-clientes').addEventListener('click', function () {
    reInitTable2();
    document.getElementById('buscar-cliente').value = "";
    setTimeout(() => {
        /*Inicializando y configurando tabla*/
        tableClientes = $(table2).DataTable();
        tableClientes.on("select", function () {
            rowData = tableClientes.rows({ selected: true }).data()[0][1];
            cliente = rowData;
            console.log(rowData);
        });
    }, 200);
});

function openProduct(id) {
    document.getElementById("id").value = id;
    document.getElementById("input-stock").value = "";
    document.getElementById("input-stock").disabled = true;

    fillSelectProducto(ENDPOINT_COLOR, "color", null, id);

    const data = new FormData();
    data.append("id_producto", id);
    fetch(API_VENTAS + "readPrecioProducto", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    // Se inicializan los campos del formulario con los datos del registro seleccionado.
                    document.getElementById("precio").value = response.dataset.precio_producto;
                    document.getElementById("modal-producto-title").innerText = response.dataset.nombre_producto;
                } else {
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}
//Función para cambiar el stock al cambiar de color en el select
document.getElementById("item-form").addEventListener("submit", function (event) {
        // Se evita recargar la página web después de enviar el formulario.
        event.preventDefault();
        const data = new FormData(document.getElementById("item-form"));
        data.append("idVenta", idVenta);
        // Petición para agregar un producto a la venta
        fetch(API_VENTAS + "createDetail", {
            method: "post",
            body: data,
        }).then(function (request) {
            // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
            if (request.ok) {
                // Se obtiene la respuesta en formato JSON.
                request.json().then(function (response) {
                    // Se comprueba si la respuesta es satisfactoria.
                    
                    if (response.status) {
                        switch (response.status) {
                            case 1:
                                sweetAlert(1, response.message, null);
                                bootstrap.Modal.getInstance(document.getElementById('item-modal')).hide();
                                console.log(response.dataset.uuid_color_stock);
                                cantidadProductos.push({"uuid_color_stock":response.dataset.uuid_color_stock, "cantidad":response.dataset.cantidad_producto});
                                console.log(cantidadProductos);
                                readOrderDetailNotInit(idVenta);
                                break;
                            //Se necesita renovar la contraseña
                            case 2:
                                sweetAlert(1, response.message, null);
                                bootstrap.Modal.getInstance(document.getElementById('item-modal')).hide();
                                readOrderDetailNotInit(idVenta);
                                break;
                        }

                        
                    } else {
                        sweetAlert(2, response.exception, null);
                    }
                });
            } else {
                console.log(request.status + " " + request.statusText);
            }
        });
    });

// Función para abrir una caja de dialogo (modal) con el formulario de cambiar cantidad de producto.
function openUpdateDialog(id, quantity, color) {
    // Se inicializan los campos del formulario con los datos del registro seleccionado.
    document.getElementById("idDetalle").value = id;
    document.getElementById("idColorStock").value = color;
    document.getElementById("input-stock-update").value = quantity;

    let input = document.getElementById("input-stock-update");
    // Se define un objeto con los datos del producto seleccionado.
    const data = new FormData();
    data.append("idColorStock", color);
    // Petición para obtener los datos del producto solicitado.
    fetch(API_PRODUCTOS + "readStockUpdate", {
        method: "post",
        body: data,
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    console.log(response.dataset.stock);
                    maxStock = parseInt(response.dataset.stock);
                    //Le ponemos el atributo max a nuestro input de stock para que no se pueda agregar al carrito más del stock que se tiene
                    input.max = parseInt(response.dataset.stock);
                } else {
                    // Se presenta un mensaje de error cuando no existen datos para mostrar.
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}

// Método manejador de eventos que se ejecuta cuando se envía el formulario de cambiar cantidad de producto.
document.getElementById("item-form-update").addEventListener("submit", function (event) {
        // Se evita recargar la página web después de enviar el formulario.
        event.preventDefault();
        const data = new FormData(document.getElementById("item-form-update"));
        data.append("idVenta", idVenta);
        // Petición para actualizar la cantidad de producto.
        fetch(API_VENTAS + "updateDetail", {
            method: "post",
            body: data,
        }).then(function (request) {
            // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
            if (request.ok) {
                // Se obtiene la respuesta en formato JSON.
                request.json().then(function (response) {
                    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                    if (response.status) {
                        //console.log(cantidadProductos);
                        // Se actualiza la tabla en la vista para mostrar el cambio de la cantidad de producto.
                        cantidadProductos.forEach(()=>{
                            const row = cantidadProductos.find(elm => elm.uuid_color_stock == response.dataset.uuid_color_stock);
                            row.cantidad = response.dataset.cantidad_producto;
                        });
                        //console.log(cantidadProductos);
                        readOrderDetailNotInit(idVenta);
                        sweetAlert(1, response.message, null);
                    } else {
                        sweetAlert(2, response.exception, null);
                    }
                });
            } else {
                console.log(request.status + " " + request.statusText);
            }
        });
    });

/*Función para mostrar y ocultar cmbs según tipo de venta seleccionado*/
function pagoOnChange(sel) {
    let tipoF = document.getElementById("tipo-factura").options[2];
    if (sel.selectedIndex == 2) {
        document.getElementById("tipo-factura").remove(3);
    } else {
        fillSelect(ENDPOINT_TIPOFACTURA, "tipo-factura", null);
    }
}

//Funcion para asignar el atributo max del input max dinámicamente
function setStock() {
    let input = document.getElementById("input-stock");
    // Petición para obtener los datos del producto solicitado.
    fetch(API_PRODUCTOS + "readStock", {
        method: "post",
        body: new FormData(document.getElementById("item-form")),
    }).then(function (request) {
        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
        if (request.ok) {
            // Se obtiene la respuesta en formato JSON.
            request.json().then(function (response) {
                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                if (response.status) {
                    maxStock = parseInt(response.dataset.stock);
                    //Le ponemos el atributo max a nuestro input de stock para que no se pueda agregar al carrito más del stock que se tiene
                    input.max = parseInt(response.dataset.stock);
                } else {
                    // Se presenta un mensaje de error cuando no existen datos para mostrar.
                    sweetAlert(2, response.exception, null);
                }
            });
        } else {
            console.log(request.status + " " + request.statusText);
        }
    });
}

//Función para cambiar el stock al cambiar de color en el select
document.getElementById("color").addEventListener("change", function () {
    if (!document.getElementById("id").value.length == 0) {
        let input = (document.getElementById("input-stock").value = 1);

        setStock();
        document.getElementById("input-stock").disabled = false;
    }
});

//Función que suma 1 al stock y valida que no supere el max
let sumarStock = () => {
    let input = document.getElementById("input-stock");
    let max = input.max;
    let valor = parseInt(input.value);
    if (valor + 1 > max || valor + 1 > maxStock) {
        input.value = input.value;
    } else {
        input.value = parseInt(input.value) + 1;
    }
};

//Función que resta 1 al stock y valida que no descienda del min
let restarStock = () => {
    let input = document.getElementById("input-stock");
    let min = input.min;
    let valor = parseInt(input.value);
    if (valor - 1 <= min || valor - 1 < 0) {
        input.value = input.value;
    } else {
        input.value = parseInt(input.value) - 1;
    }
};

//Funciones de validaciones

let validacionInputStockUpdate = () => {
    let input = document.getElementById("input-stock-update");
    let valor = parseInt(input.value);
    if (valor > input.max || valor <= input.min) {
        input.value = 1;
    }
};

//Función que suma 1 al stock y valida que no supere el max
let sumarStockUpdate = () => {
    let input = document.getElementById("input-stock-update");
    let max = input.max;
    let valor = parseInt(input.value);
    if (valor + 1 > max || valor + 1 > maxStock) {
        input.value = input.value;
    } else {
        input.value = parseInt(input.value) + 1;
    }
};

//Función que resta 1 al stock y valida que no descienda del min
let restarStockUpdate = () => {
    let input = document.getElementById("input-stock-update");
    let min = input.min;
    let valor = parseInt(input.value);
    if (valor - 1 <= min || valor - 1 < 0) {
        input.value = input.value;
    } else {
        input.value = parseInt(input.value) - 1;
    }
};

//Funciones de validaciones

let validacionInputStock = () => {
    let input = document.getElementById("input-stock");
    let valor = parseInt(input.value);
    if (valor > input.max || valor <= input.min) {
        input.value = 1;
    }
};

function valideKey(evt) {
    // code is the decimal ASCII representation of the pressed key.
    var code = evt.which ? evt.which : evt.keyCode;

    if (code == 8) {
        // backspace.
        return true;
    } else if (code >= 48 && code <= 57) {
        // is a number.
        return true;
    } else {
        // other keys.
        return false;
    }
}

//Función para cambiar el stock al cambiar de color en el select
document
    .getElementById("btn-add-client")
    .addEventListener("click", function () {
        document.getElementById('search-form-cliente').reset();
        let rowData = "";
        reInitTable2();
        //$(table2).DataTable().destroy();
        setTimeout(() => {
            /*Inicializando y configurando tabla*/
            tableClientes = $(table2).DataTable();
            tableClientes.on("select", function () {
                rowData = tableClientes.rows({ selected: true }).data()[0][1];
                cliente = rowData;
                console.log(rowData);
            });
        }, 200);
    });

function setCliente() {
    if (!cliente == "") {
        document.getElementById("dui-cliente").value = cliente;
        document.getElementById("btn-add-client").innerText = cliente;
        sweetAlert(1, "Cliente seleccionado", null);
    } else {
        sweetAlert(3, "No se ha seleccionado ningún cliente", null);
    }
}

// Función para mostrar un mensaje de confirmación al momento de finalizar el pedido.
function finishOrder() {
    console.log(document.getElementById("tipo-factura").value);
    console.log(document.getElementById("tipo-venta").value);
    //Validaciones
    if (document.getElementById("correlativo").value == "") {
        sweetAlert(3, "No se ha asignado un correlativo a la factura", null);
    } else if (!document.getElementById("tipo-factura").value || !document.getElementById("tipo-venta").value) {
        sweetAlert(3, "Debes de seleccionar un tipo de venta y de factura", null);
    } else {
        swal({
            title: "Aviso",
            text: "¿Está seguro de finalizar el pedido?",
            icon: "info",
            buttons: ["No", "Sí"],
            closeOnClickOutside: false,
            closeOnEsc: false,
        }).then(function (value) {
            // Se verifica si fue cliqueado el botón Sí para realizar la petición respectiva, de lo contrario se muestra un mensaje.
            if (value) {
                // Se define un objeto con los datos del producto seleccionado.
                const data = new FormData(document.getElementById("finalizar-venta"));
                data.append("idVenta", idVenta);
                data.append(
                    "correlativo",
                    document.getElementById("correlativo").value
                );
                console.log(document.getElementById("correlativo").value);
                // Petición para finalizar el pedido en proceso.
                fetch(API_VENTAS + "finishOrder", {
                    method: "post",
                    body: data,
                }).then(function (request) {
                    // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                    if (request.ok) {
                        request.json().then(function (response) {
                            // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                            if (response.status) {
                                //Update de stock
                                const data = new FormData();
                                data.append("idVenta", idVenta);
                                // Petición para obtener los detalles de la venta a finalizar
                                fetch(API_VENTAS + "getDetails", {
                                    method: "post", 
                                    body: data,
                                    }).then(function (request) {
                                    // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                                        if (request.ok) {
                                        // Se obtiene la respuesta en formato JSON.
                                            request.json().then(function (response) {
                                            // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                                            if (response.status) {
                                                //Se recorre cada detalle obtenido
                                                response.dataset.map(function (row) {
                                                    //Realizamos el update por cada detalle obtenido
                                                    const data = new FormData();
                                                    data.append("uuid_color_stock", row.uuid_color_stock);
                                                    data.append("cantidad_producto", row.cantidad_producto);
                                                    fetch(API_VENTAS + "updateDetailStock", {
                                                        method: "post",
                                                        body: data,
                                                        }).then(function (request) {
                                                        // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                                                            if (request.ok) {
                                                            // Se obtiene la respuesta en formato JSON.
                                                                request.json().then(function (response) {
                                                                // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                                                                if (response.status) {
                                                                    //Abriendo reporte y mensaje de que todo salió bien
                                                                    let url;
                                                                    let sel = document.getElementById("tipo-factura");
                                                                    let value = sel.options[sel.selectedIndex].text;
                                                                    let param = "?uuid_venta=" + idVenta;
                                                                    // Se establece la ruta del reporte en el servidor.
                                                                    if (value == "Crédito fiscal") {
                                                                        url = SERVER + "reports/credito.php";
                                                                    } else {
                                                                        url = SERVER + "reports/consumidor.php";
                                                                    }
                                                                    // Se abre el reporte en una nueva pestaña del navegador web.
                                                                    window.open(url + param);
                                                                    sweetAlert(1, 'Venta finalizada correctamente', "venta.html");
                                                                    console.log('Stock del producto actualizado')
                                                                } else {
                                                                    // Se presenta un mensaje de error cuando no existen datos para mostrar.
                                                                    sweetAlert(2, response.exception, null);
                                                                }
                                                            });
                                                        } else {
                                                            console.log(request.status + " " + request.statusText);
                                                        }
                                                    }); 
                                                });
                                            } else {
                                                // Se presenta un mensaje de error cuando no existen datos para mostrar.
                                                sweetAlert(2, response.exception, null);
                                            }
                                        });
                                    } else {
                                        console.log(request.status + " " + request.statusText);
                                    }
                                }); 
                            } else {
                                sweetAlert(2, response.exception, null);
                            }
                        });
                    } else {
                        console.log(request.status + " " + request.statusText);
                    }
                });
            } else {
            }
        });
    }
}

//Función para cambiar el stock al cambiar de color en el select
document.getElementById("cancelar-venta").addEventListener("click", function () {
        swal({
            title: "Aviso",
            text: "¿Está seguro de cancelar el pedido?",
            icon: "info",
            buttons: ["No", "Sí"],
            closeOnClickOutside: false,
            closeOnEsc: false,
        }).then(function (value) {
            // Se verifica si fue cliqueado el botón Sí para realizar la petición respectiva, de lo contrario se muestra un mensaje.
            if (value) {
                // Se define un objeto con los datos del producto seleccionado.
                const data = new FormData(document.getElementById("finalizar-venta"));
                data.append("idVenta", idVenta);
                // Petición para finalizar el pedido en proceso.
                fetch(API_VENTAS + "cancelOrder", {
                    method: "post",
                    body: data,
                }).then(function (request) {
                    // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                    if (request.ok) {
                        request.json().then(function (response) {
                            // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                            if (response.status) {
                                sweetAlert(1, response.message, "venta.html");
                            } else {
                                sweetAlert(2, response.exception, null);
                            }
                        });
                    } else {
                        console.log(request.status + " " + request.statusText);
                    }
                });
            } else {
            }
        });
    });


function openDeleteDialog(id) {
    swal({
        title: "Aviso",
        text: "¿Desea quitar el producto de la venta?",
        icon: "info",
        buttons: ["No", "Sí"],
        closeOnClickOutside: false,
        closeOnEsc: false,
    }).then(function (value) {
        // Se verifica si fue cliqueado el botón Sí para realizar la petición respectiva, de lo contrario se muestra un mensaje.
        if (value) {
            // Se define un objeto con los datos del producto seleccionado.
            const data = new FormData(document.getElementById("finalizar-venta"));
            data.append("id_detalle", id);
            data.append("id_venta", idVenta);
            // Petición para finalizar el pedido en proceso.
            fetch(API_VENTAS + "deleteDetail", {
                method: "post",
                body: data,
            }).then(function (request) {
                // Se verifica si la petición es correcta, de lo contrario se muestra un mensaje en la consola indicando el problema.
                if (request.ok) {
                    request.json().then(function (response) {
                        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
                        if (response.status) {
                            sweetAlert(1, response.message, null);
                            readOrderDetailNotInit(idVenta);
                        } else {
                            sweetAlert(2, response.exception, null);
                        }
                    });
                } else {
                    console.log(request.status + " " + request.statusText);
                }
            });
        } else {
        }
    });
}