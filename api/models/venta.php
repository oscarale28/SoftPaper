<?php
/*
*	Clase para manejar la tabla ventas de la base de datos.
*   Es clase hija de Validator.
*/
class Ventas extends Validator
{
    // Declaración de atributos (propiedades) según nuestra tabla en la base de datos.
    private $id = null;
    private $empleado = null;
    private $cliente = null;
    private $estado = null;
    private $tipo_venta = null;
    private $tipo_factura = null;
    private $fecha = null;
    private $correlativo = null;
    private $total = null;
    private $color = null;
    private $id_detalle = null;
    private $producto = null;
    private $cantidad = null;
    private $precio_unitario = null;
    private $categoria_producto = null;

    /*Variables de tipos de facturas y ventas */
    private $credito_fiscal = "bfd00eca-a737-46f0-9ac8-14f1017cbde1";
    private $venta_normal = "84a2a8bf-54b1-4ab4-9bf1-4ec7057c361e";
    private $estado_pagado = "43fbc4c4-35a4-4890-865e-21bf562606ec";
    private $estado_proceso = "8b432835-70fc-4bed-83cc-18011f28acbe";
    private $consumidor_final = "c69b153d-a384-4c0a-b1f2-8c2b0403e9d6";

    /*
    *   Métodos para validar y asignar valores de los atributos.
    */
    public function setId($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->id = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setColor($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->color = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setPrecio($value)//validaciones
    {
        if ($this->validateMoney($value)) {
            $this->precio_unitario = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setIdDetalle($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->id_detalle = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setProducto($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->producto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEmpleado($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->empleado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCliente($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->cliente = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstado($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTipoVenta($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->tipo_venta = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTipoFactura($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->tipo_factura = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setFecha($value)
    {
        if ($this->validateDate($value)) {
            $this->fecha = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCorrelativo($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->correlativo = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTotal($value)
    {
        if ($this->validateMoney($value)) {
            $this->total = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCantidad($value)
    {
        if ($this->validateNaturalNumber($value)) {
            $this->cantidad = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCategoria($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->categoria_producto = $value;
            return true;
        } else {
            return false;
        }
    }

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getIdVenta()
    {
        return $this->id;
    }


    /* 
    *   Método para comprobar que existen pedidos registrados en nuestra base de datos-----------------.
    */

    public function readAll()
    {
        $sql = 'SELECT uuid_venta, nombres_empleado, apellidos_empleado, nombre_cliente, estado_venta, tipo_venta, tipo_factura, fecha_venta, correlativo_venta, (getmonto(uuid_venta)) as monto
        from venta inner join empleado using(uuid_empleado)
        inner join cliente using(uuid_cliente)
        inner join estado_venta using(uuid_estado_venta)
        inner join tipo_venta using(uuid_tipo_venta)
        inner join tipo_factura using (uuid_tipo_factura)';
       $params = null;;
        return Database::getRows($sql, $params);
    }

    // Método para obtener los productos que se encuentran en la venta seleccionada------------------.
    public function readOrderDetail()
    {
        $sql = 'SELECT correlativo_venta, uuid_detalle_venta, imagen_producto, nombre_producto, color_producto, precio_unitario, cantidad_producto, uuid_color_stock
                from venta inner join detalle_venta using(uuid_venta)
                inner join color_stock using (uuid_color_stock)
                inner join producto using (uuid_producto)
                inner join color_producto using (uuid_color_producto)
                where uuid_venta = ?
                order by color_producto';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }

    public function readCountDetails()
    {
        $sql = 'SELECT count(*) as cantidad_productos
                from venta inner join detalle_venta using(uuid_venta)
                inner join color_stock using (uuid_color_stock)
                inner join producto using (uuid_producto)
                inner join color_producto using (uuid_color_producto)
                where uuid_venta = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT "idPedido", c."nombresCliente", c."apellidosCliente", c."direccionCliente", c."telefonoCliente", "fechaPedido", p."estadoPedido", "montoTotal", tp."tipoPago"
                from pedido as p inner join "cliente" as c using ("idCliente")
                inner join "tipoPago" as tp on p."tipoPago" = tp."idTipoPago"
                where "idPedido" = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function readMonto()
    {
        $sql = 'SELECT SUM(precio_unitario*cantidad_producto) as monto from detalle_venta WHERE uuid_venta = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */

    /* SEARCH */

    public function searchRows($value)
    {
        $sql = 'SELECT uuid_venta, nombres_empleado, apellidos_empleado, nombre_cliente, estado_venta, tipo_venta, tipo_factura, fecha_venta, correlativo_venta, (getmonto(uuid_venta)) as monto
                from venta inner join empleado using(uuid_empleado)
                inner join cliente using(uuid_cliente)
                inner join estado_venta using(uuid_estado_venta)
                inner join tipo_venta using(uuid_tipo_venta)
                inner join tipo_factura using (uuid_tipo_factura)
                WHERE correlativo_venta::varchar(255) ILIKE ?';
        $params = array("%$value%");
        return Database::getRows($sql, $params);
    }

    /* Método para filtrar tabla
    *   Parámetros: categoria = categoria por la cual filtrar, estado = estado por el cual filtrar-----------------.
    */
    public function readRowsFilter($tipo, $estado)
    {
        $sql = 'SELECT uuid_venta, nombres_empleado, apellidos_empleado, nombre_cliente, estado_venta, tipo_venta, tipo_factura, fecha_venta, correlativo_venta, (getmonto(uuid_venta)) as monto
        from venta inner join empleado using(uuid_empleado)
        inner join cliente using(uuid_cliente)
        inner join estado_venta using(uuid_estado_venta)
        inner join tipo_venta using(uuid_tipo_venta)
        inner join tipo_factura using (uuid_tipo_factura)
        WHERE uuid_tipo_venta = ? AND uuid_estado_venta = ?';
        $params = array($tipo, $estado);
        return Database::getRows($sql, $params);
    }

    public function readRowsFilterDate($start, $end)
    {
        $sql = 'SELECT uuid_venta, nombres_empleado, apellidos_empleado, nombre_cliente, estado_venta, tipo_venta, tipo_factura, fecha_venta, correlativo_venta, (getmonto(uuid_venta)) as monto
        from venta inner join empleado using(uuid_empleado)
        inner join cliente using(uuid_cliente)
        inner join estado_venta using(uuid_estado_venta)
        inner join tipo_venta using(uuid_tipo_venta)
        inner join tipo_factura using (uuid_tipo_factura)
        WHERE fecha_venta BETWEEN ? AND ?';
        $params = array($start, $end);
        return Database::getRows($sql, $params);
    }

    /* UPDATE */

    public function updateRow()
    {
        $sql = 'UPDATE "pedido"
                SET "estadoPedido" = ?
                WHERE "idPedido" = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    /*
    *   MÉTODOS PARA EL SITIO PÚBLICO
    */

    /*
    *   Método para obtener los pedidos del cliente activo--------------------.
    */

    public function readPedidosCliente()
    {
        $sql = 'SELECT "idPedido", "fechaPedido", ep."estadoPedido", "montoTotal"
                from pedido as p inner join "estadoPedido" as ep on p."estadoPedido" = ep."idEstadoPedido"
                inner join "cliente" as c on p."idCliente" = c."idCliente"
                where p."idCliente" = ?
                and ep."idEstadoPedido" != 2
                order by "idPedido"';
        $params = array($_SESSION['idCliente']);
        return Database::getRows($sql, $params);
    }

    /* Método para verificar si existe un pedido en proceso para seguir comprando, de lo contrario se crea uno.*/
    public function startOrder()
    {           
        $sql = "SELECT uuid_venta
        FROM venta
        WHERE uuid_estado_venta = (select uuid_estado_venta from estado_venta where estado_venta = 'En proceso') 
        and uuid_tipo_venta IS NULL
        AND uuid_empleado = ?";

        $params = array($_SESSION['uuid_empleado']);
        if ($data = Database::getRow($sql, $params)) {
            $_SESSION['uuid_venta_normal'] = $data['uuid_venta'];
            return 2;
        } else {
            $sql = "INSERT INTO venta(uuid_empleado, uuid_estado_venta)
            VALUES(?, (select uuid_estado_venta from estado_venta where estado_venta = 'En proceso')) RETURNING uuid_venta;";
            $params = array($_SESSION['uuid_empleado']);
            // Se obtiene el ultimo valor insertado en la llave primaria de la tabla pedidos.
            if ($_SESSION['uuid_venta_normal'] = Database::getRowId($sql, $params)) {
                return 1;
            } else {
                return 3;
            }
        }
    }

    
    /* Método para verificar si existe un pedido en proceso para seguir comprando, de lo contrario se crea uno.*/
    public function startOrderDay()
    {
        $sql = "SELECT uuid_venta
        FROM venta
        WHERE uuid_estado_venta = (select uuid_estado_venta from estado_venta where estado_venta = 'En proceso') and uuid_tipo_venta = (select uuid_tipo_venta from tipo_venta where tipo_venta = 'Venta del día') AND uuid_empleado = ?";
        $params = array($_SESSION['uuid_empleado']);
        if ($data = Database::getRow($sql, $params)) {
            $_SESSION['uuid_venta_dia'] = $data['uuid_venta'];
            return true;
        } else {
            $sql = "INSERT INTO venta(uuid_empleado, uuid_estado_venta, uuid_tipo_venta, uuid_tipo_factura)
            VALUES(?, (select uuid_estado_venta from estado_venta where estado_venta = 'En proceso'), (select uuid_tipo_venta from tipo_venta where tipo_venta = 'Venta del día'), (select uuid_tipo_factura from tipo_factura where tipo_factura = 'Consumidor final')) RETURNING uuid_venta;";
            $params = array($_SESSION['uuid_empleado']);
            // Se obtiene el ultimo valor insertado en la llave primaria de la tabla pedidos.
            if ($_SESSION['uuid_venta_dia'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }


    public function readVentaId()
    {
        $sql = 'SELECT uuid_venta FROM venta WHERE uuid_venta = ?;';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Método para agregar un producto al carrito de compras.
    public function createDetail()
    {
        // Se realiza el insert
        $sql = 'INSERT INTO detalle_venta(uuid_color_stock, precio_unitario, cantidad_producto, uuid_venta)
        VALUES((SELECT uuid_color_stock FROM color_stock WHERE uuid_producto = ? AND uuid_color_producto = ?), ?, ?, ?) RETURNING uuid_color_stock';
        $params = array($this->producto, $this->color, $this->precio_unitario, $this->cantidad, $this->id);
        //Capturamos el id del insert, para después obtener los datos
        if ($this->id = Database::getRowId($sql, $params)) {
            $sql = 'SELECT uuid_color_stock, cantidad_producto FROM detalle_venta WHERE uuid_color_stock = ?';
            $params = array($this->id);
            return Database::getRow($sql, $params);
        } else {
            return false;
        }
    }



    public function checkProducto($idProducto)
    {
        $sql = 'SELECT COUNT(*) 
        FROM detalle_venta INNER JOIN color_stock USING (uuid_color_stock) INNER JOIN venta USING (uuid_venta)
        WHERE color_stock.uuid_producto = ? AND color_stock.uuid_color_producto = ? AND uuid_venta = ?;';
        $params = array($idProducto, $this->color, $this->id);
        return Database::registerExist($sql, $params);
    }

    public function readProductStock()
    {
        $sql = 'SELECT  stock
        FROM "colorStock" 
		WHERE "idColorStock" = ?';
        $params = array($this->color);
        return Database::getRow($sql, $params);
    }

    public function readPrecioProducto()
    {
        $sql = 'SELECT precio_producto, nombre_producto
        FROM producto
		WHERE uuid_producto = ?';
        $params = array($this->producto);
        return Database::getRow($sql, $params);
    }

    // Método para finalizar un pedido por parte del cliente.
    public function finishOrder($tipoFactura, $tipoVenta)
    {   
        //Credito
        if($tipoFactura == $this->credito_fiscal){
            if($tipoVenta == $this->venta_normal){
                $sql = "UPDATE venta
                SET uuid_cliente = (SELECT uuid_cliente FROM cliente WHERE dui_cliente = ?), uuid_tipo_venta = ?, uuid_tipo_factura = ?, fecha_venta = CURRENT_DATE, correlativo_venta = ?, uuid_estado_venta = ". "'".(strval($this->estado_pagado))."'" ." WHERE uuid_venta = ?";
            } else {
                $sql = "UPDATE venta
                SET uuid_cliente = (SELECT uuid_cliente FROM cliente WHERE dui_cliente = ?), uuid_tipo_venta = ?, uuid_tipo_factura = ?, fecha_venta = CURRENT_DATE, correlativo_venta = ?, uuid_estado_venta = ". "'".(strval($this->estado_proceso))."'" ." WHERE uuid_venta = ?";
            }
            
        //Consumidor
        } elseif($tipoFactura == $this->consumidor_final) {
            if($tipoVenta == $this->venta_normal){
                $sql = "UPDATE venta
                SET uuid_cliente = (SELECT uuid_cliente FROM cliente WHERE dui_cliente = ?), uuid_tipo_venta = ?, uuid_tipo_factura = ?, fecha_venta = CURRENT_DATE, correlativo_venta = ?, uuid_estado_venta = ". "'".(strval($this->estado_pagado))."'" ." WHERE uuid_venta = ?";
            } else {
                $sql = "UPDATE venta
                SET uuid_cliente = (SELECT uuid_cliente FROM cliente WHERE dui_cliente = ?), uuid_tipo_venta = ?, uuid_tipo_factura = ?, fecha_venta = CURRENT_DATE, correlativo_venta = ?, uuid_estado_venta = ". "'".(strval($this->estado_proceso))."'" ." WHERE uuid_venta = ?";
            }
            
        //Ticket
        } else {
            if($tipoVenta == $this->venta_normal){
                $sql = "UPDATE venta
                SET uuid_cliente = (SELECT uuid_cliente FROM cliente WHERE dui_cliente = ?), uuid_tipo_venta = ?, uuid_tipo_factura = ?, fecha_venta = CURRENT_DATE, correlativo_venta = ?, uuid_estado_venta = ". "'".(strval($this->estado_pagado))."'" ." WHERE uuid_venta = ?";
            } else {
                $sql = "UPDATE venta
                SET uuid_cliente = (SELECT uuid_cliente FROM cliente WHERE dui_cliente = ?), uuid_tipo_venta = ?, uuid_tipo_factura = ?, fecha_venta = CURRENT_DATE, correlativo_venta = ?, uuid_estado_venta = ". "'".(strval($this->estado_proceso))."'" ." WHERE uuid_venta = ?";
            }
        }
        $params = array($this->cliente, $this->tipo_venta, $this->tipo_factura, $this->correlativo, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function finishOrderDay()
    {   
        $sql = "UPDATE venta
        SET uuid_cliente = (SELECT uuid_cliente FROM cliente WHERE nombre_cliente = 'Factura'),
        fecha_venta = CURRENT_DATE, 
        correlativo_venta = ?, 
        uuid_estado_venta = ". "'".(strval($this->estado_pagado))."'" ." WHERE uuid_venta = ?";
        $params = array($this->correlativo, $this->id);
        return Database::executeRow($sql, $params);
    }

    // Método para actualizar la cantidad de un producto agregado al carrito de compras.
    public function updateDetail()
    {   
        $sql = 'UPDATE detalle_venta
            SET cantidad_producto = ?
            WHERE uuid_detalle_venta = ? AND uuid_venta = ? RETURNING uuid_detalle_venta;';
        $params = array($this->cantidad, $this->id_detalle, $this->id);
        if ($this->id = Database::getRowId($sql, $params)) {
            $sql = 'SELECT uuid_color_stock, cantidad_producto FROM detalle_venta WHERE uuid_detalle_venta = ?';
            $params = array($this->id);
            return Database::getRow($sql, $params);
        } else {
            return false;
        }
    }

    // Método obtener los detalles.
    public function getDetails()
    {   
        $sql = 'SELECT uuid_detalle_venta, uuid_color_stock, cantidad_producto FROM detalle_venta WHERE uuid_venta = ?;';
        $params = array($this->id);
        return Database::getRows($sql, $params);
        
    }

    // Método obtener los detalles.
    public function updateStockProducto()
    {   
        $sql = ' SELECT updateStock(?, ?);';
        $params = array($this->producto, $this->cantidad);
        return Database::executeRow($sql, $params);
        
    }

    // Método para eliminar un producto que se encuentra en el carrito de compras.
    public function deleteDetail()
    {
        $sql = 'DELETE FROM detalle_venta
        WHERE uuid_detalle_venta = ? AND uuid_venta = ?';
        $params = array($this->id_detalle, $this->id);
        return Database::executeRow($sql, $params);
    }

    // Método para cancelar la venta.
    public function cancelOrder()
    {
        $sql = "DELETE FROM venta
                WHERE uuid_venta = ?";
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function deleteDetails()
    {
        $sql = "DELETE FROM detalle_venta
                WHERE uuid_venta = ?";
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }


    /*
    *   Métodos para generar gráficas.
    */
    public function reportVentasDelDia()
    {
        $sql = '';
        $params = null;
        return Database::getRows($sql, $params);
    }
    public function reportVentasDelDiaEstadistica()
    {
        $sql = '';
        
        $params = null;
        return Database::getRow($sql, $params);
    }

    public function ventasDiaPorTipo()
    {
        $sql = 'SELECT COUNT(uuid_venta) as cantidad, tipo_factura 
        FROM venta 
        INNER JOIN tipo_factura USING(uuid_tipo_factura) 
        WHERE fecha_venta = CURRENT_DATE 
        GROUP BY tipo_factura';
        $params = null;
        return Database::getRows($sql, $params);
    }

    //REPORTES CONSUMIDOR--------------------------
    
    /*Para cuadro de productos*/
    public function consumidorFinal(){
        $sql = "SELECT cantidad_producto, nombre_producto, precio_unitario, (cantidad_producto*precio_unitario) as monto_producto 
        from detalle_venta
        inner join color_stock using(uuid_color_stock)
        inner join producto using (uuid_producto)
        where uuid_venta = ?";
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }
    
    /*Para monto total de la venta*/
    public function montoConsumidorFinal(){
        $sql = "SELECT getmonto(uuid_venta) as monto_total from venta where uuid_venta = ?";
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /*Para información de la venta*/
    public function infoConsumidorFinal(){
        $sql = 'SELECT current_date as fecha';
        $params = null;
        return Database::getRow($sql, $params);
    }

    //INFORMACIÓN DE LA VENTA---------------------------------------

    public function detalleClie(){
        $sql = "SELECT uuid_venta, nombre_cliente, direccion_cliente, nombre_municipio, nombre_departamento, nit_cliente, telefono_cliente, giro_cliente, current_date as fecha
        FROM cliente 
        INNER JOIN municipio USING(uuid_municipio)
        INNER JOIN departamento USING(uuid_departamento)
        INNER JOIN giro_cliente USING (uuid_giro_cliente)
        INNER JOIN venta USING(uuid_cliente)
        WHERE uuid_venta = ?";
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    //CREDITO FICAL CUADRO DE PRODUCTOS---------------------------------------
    public function creditoFiscal(){
        $sql = "SELECT cantidad_producto, nombre_producto, precio_unitario, (cantidad_producto*precio_unitario) as monto_producto 
        from detalle_venta
        inner join color_stock using(uuid_color_stock)
        inner join producto using (uuid_producto)
        where uuid_venta = ?";
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }

    /*
    MÉTODOS PARA GRÁFICA Y REPORTE VENTAS POR CATEGORÍA
    */

    // Gráfica unidades vendidas por categoría a la semana
    public function ventasCategoriaG(){
        $sql = "SELECT SUM(cantidad_producto), nombre_categoria_p
                FROM detalle_venta
                INNER JOIN venta using(uuid_venta)
                INNER JOIN color_stock using(uuid_color_stock)
                INNER JOIN producto using (uuid_producto)
                INNER JOIN subcategoria_producto using(uuid_subcategoria_p)
                INNER JOIN categoria_producto using(uuid_categoria_p)
                WHERE fecha_venta between (select current_date - cast('7 days' as interval))  and current_date
                GROUP BY nombre_categoria_p";
        $params = null;
        return Database::getRows($sql, $params);
    }

    //PARA MONTO TOTAL DE LA VENTA---------------------------------------
    public function montoCredito(){
        $sql = "SELECT getmonto(uuid_venta) as monto_total from venta where uuid_venta = ?";
        $params = $params = array($this->id);;
        return Database::getRow($sql, $params);
    }

    // Reporte parametrizado detalle de ventas por categoría
    public function ventasCategoriaR(){
        $sql = "SELECT nombre_producto, precio_unitario, SUM(cantidad_producto) as cantidad_producto, (precio_unitario * (SELECT SUM(cantidad_producto))) as monto_total, fecha_venta
                from detalle_venta
                INNER JOIN color_stock using(uuid_color_stock)
                INNER JOIN producto using (uuid_producto)
                INNER JOIN subcategoria_producto using(uuid_subcategoria_p)
                INNER JOIN categoria_producto using(uuid_categoria_p)
                INNER JOIN venta using (uuid_venta)
                WHERE uuid_categoria_p = ?
                AND fecha_venta between (select current_date - cast('7 days' as interval))  and current_date
                GROUP BY nombre_producto, precio_unitario, cantidad_producto, fecha_venta";        
        $params = array($this->categoria_producto);
        return Database::getRows($sql, $params);
    }

    // Calcular monto total de ventas por categoría para reporte parametrizado
    public function montoReporteVentasCategoria(){
        $sql = "SELECT SUM(cantidad_producto*precio_unitario) as monto_semanal 
        from detalle_venta INNER JOIN venta using (uuid_venta)
        INNER JOIN color_stock using(uuid_color_stock)
        INNER JOIN producto using (uuid_producto)
        INNER JOIN subcategoria_producto using(uuid_subcategoria_p)
        INNER JOIN categoria_producto using(uuid_categoria_p)
        WHERE fecha_venta between (select current_date - cast('7 days' as interval))  and current_date
        AND uuid_categoria_p = ?";
        $params = array($this->categoria_producto);
        return Database::getRow($sql, $params);
    }


    // Ventas en los 7 días anteriores hasta la fecha seleccionada
    public function ventasPorSemanaReport($start)
    {
        $sql = "SELECT nombre_cliente, tipo_venta, tipo_factura, fecha_venta, estado_venta, correlativo_venta, getmonto(uuid_venta) as monto
        FROM venta INNER JOIN cliente USING(uuid_cliente)
        INNER JOIN tipo_venta USING(uuid_tipo_venta)
        INNER JOIN tipo_factura USING(uuid_tipo_factura)
        INNER JOIN estado_venta USING (uuid_estado_venta)
        INNER JOIN detalle_venta using(uuid_venta)
        WHERE fecha_venta BETWEEN (cast(? as date) - cast('7 days' as interval)) AND ? AND estado_venta = 'Pagado'
        GROUP BY fecha_venta, nombre_cliente, tipo_venta, tipo_factura, estado_venta, correlativo_venta, monto
        ORDER BY correlativo_venta";
        $params = array($start, $start);
        return Database::getRows($sql, $params);
    }




}
