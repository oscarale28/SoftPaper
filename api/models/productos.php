<?php
/*
*	Clase para manejar la tabla productos de la base de datos.
*   Es clase hija de Validator.
*/
class Productos extends Validator
{
    // Declaración de atributos (propiedades) según nuestra tabla en la base de datos.
    private $id = null;
    private $nombre = null;
    private $descripcion = null;
    private $precio = null;
    private $subcategoria = null;
    private $proveedor = null;
    private $marca = null;
    private $estado = null;
    private $color = null;
    private $stock = null;
    private $descuento = null;
    private $imagen = null;
    private $ruta = '../images/productos/';

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


    public function setNombre($value)
    {
        if ($this->validateString($value, 1, 150)) {
            $this->nombre = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setPrecio($value) //validaciones
    {
        if ($this->validateMoney($value)) {
            $this->precio = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setSubcategoria($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->subcategoria = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setProveedor($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->proveedor = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setMarca($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->marca = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDescripcion($value)
    {
        if ($this->validateString($value, 1, 400)) {
            $this->descripcion = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setImagen($file)
    {
        if ($this->validateImageFile($file, 1000, 1000)) {
            $this->imagen = $this->getFileName();
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

    public function setColor($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->color = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setStock($value)
    {
        if ($this->validateStock($value)) {
            $this->stock = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDescuento($value)
    {
        if ($this->validateStock($value)) {
            $this->descuento = $value;
            return true;
        } else {
            return false;
        }
    }

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function getSubcategoria()
    {
        return $this->subcategoria;
    }

    public function getProveedor()
    {
        return $this->proveedor;
    }

    public function getMarca()
    {
        return $this->marca;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function getDescuento()
    {
        return $this->descuento;
    }

    public function getRuta()
    {
        return $this->ruta;
    }

    /* 
    *   Método para comprobar que existen subcategorias registradas en nuestra base de datos
    */

    // Método para leer toda la información de los productos existentes-------------------------.
    public function readAll()
    {
        $sql = "SELECT Distinct on (nombre_producto) nombre_producto, uuid_producto, imagen_producto, nombre_subcategoria_p, precio_producto, uuid_color_producto, uuid_color_stock, (select sumStockRead(uuid_producto::varchar)) as stock, nombre_marca, nombre_proveedor, descripcion_producto, estado_producto
        FROM producto INNER JOIN estado_producto USING(uuid_estado_producto)
		INNER JOIN subcategoria_producto USING(uuid_subcategoria_p)
		INNER JOIN color_stock USING(uuid_producto)
        INNER JOIN color_producto USING(uuid_color_producto)
		INNER JOIN marca USING(uuid_marca)
		INNER JOIN detalle_producto USING(uuid_producto)
		INNER JOIN proveedor USING(uuid_proveedor)
		WHERE  precio_producto != 0.00
		AND estado_subcategoria_p = true
		AND estado_marca = true
		AND estado_proveedor = true
        AND estado_color_producto = true
		ORDER BY  nombre_producto, stock DESC";

        $params = null;
        return Database::getRows($sql, $params);
    }

    // Método para un dato en especifico de los productos existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT Distinct on (nombre_producto) nombre_producto, descripcion_producto, imagen_producto, precio_producto, uuid_subcategoria_p, uuid_marca, uuid_estado_producto,  uuid_proveedor, uuid_color_producto, stock, (SELECT uuid_categoria_p FROM subcategoria_producto WHERE uuid_subcategoria_p = (SELECT uuid_subcategoria_p FROM producto WHERE uuid_producto = ?))
        FROM producto INNER JOIN color_stock USING(uuid_producto) INNER JOIN detalle_producto USING(uuid_producto)
        WHERE uuid_producto = ?
		ORDER BY  nombre_producto, stock DESC;';
        $params = array($this->id, $this->id);
        return Database::getRow($sql, $params);
    }



    /*
    *   Métodos para obtener estadísticas de productos------------------------.
    */

    public function readStadistics()
    {
        $sql = "SELECT (SELECT COUNT(DISTINCT uuid_producto) FROM producto
        INNER JOIN subcategoria_producto using(uuid_subcategoria_p)
        INNER JOIN marca using(uuid_marca)
        INNER JOIN detalle_producto using(uuid_producto)
        INNER JOIN proveedor using(uuid_proveedor)
        WHERE estado_marca = true
        AND estado_subcategoria_p = true
        AND estado_proveedor = true
        AND nombre_subcategoria_p != 'Servicios de papelería') as total,
         (SELECT COUNT(*) FROM producto WHERE uuid_estado_producto = (SELECT uuid_estado_producto FROM estado_producto WHERE estado_producto = 'Sin existencias')) as agotados,
         (SELECT SUM(sumStockRead(uuid_producto::varchar)) FROM producto where precio_producto != 0.00) as existencias, 
         (SELECT COUNT(*) FROM categoria_producto WHERE estado_categoria_p = true) as categorias  
         FROM producto, color_stock, categoria_producto
         GROUP BY total;";
        $params = null;
        return Database::getRow($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    /* SEARCH */
    public function searchRows($value)
    {
        $sql = 'SELECT Distinct on (nombre_producto) nombre_producto, uuid_producto, imagen_producto, nombre_subcategoria_p, precio_producto, uuid_color_producto, uuid_color_stock, stock, nombre_marca, nombre_proveedor, descripcion_producto, estado_producto
        FROM producto INNER JOIN estado_producto USING(uuid_estado_producto)
		INNER JOIN subcategoria_producto USING(uuid_subcategoria_p)
		INNER JOIN color_stock USING(uuid_producto)
		INNER JOIN marca USING(uuid_marca)
		INNER JOIN detalle_producto USING(uuid_producto)
		INNER JOIN proveedor USING(uuid_proveedor)
        WHERE "nombre_producto" ILIKE ?
        ORDER BY  nombre_producto, stock DESC';
        $params = array("%$value%");
        return Database::getRows($sql, $params);
    }

    /* Método para filtrar tabla
    *   Parámetros: categoria = categoria por la cual filtrar, estado = estado por el cual filtrar---------------------------.
    */
    public function readRowsFilter($categoria, $estado)
    {
        $sql = 'SELECT Distinct on (nombre_producto) nombre_producto, uuid_producto, imagen_producto, nombre_subcategoria_p, precio_producto, uuid_color_producto, uuid_color_stock, stock, nombre_marca, nombre_proveedor, descripcion_producto, estado_producto
        FROM producto INNER JOIN estado_producto USING(uuid_estado_producto)
		INNER JOIN subcategoria_producto USING(uuid_subcategoria_p)
		INNER JOIN categoria_producto USING(uuid_categoria_p)
		INNER JOIN color_stock USING(uuid_producto)
		INNER JOIN marca USING(uuid_marca)
		INNER JOIN detalle_producto USING(uuid_producto)
		INNER JOIN proveedor USING(uuid_proveedor)
        WHERE "uuid_categoria_p" = ? AND uuid_estado_producto = ?
        ORDER BY  nombre_producto, stock DESC';
        $params = array($categoria, $estado);
        return Database::getRows($sql, $params);
    }


    /* CREATE */
    public function createRow()
    {
        $sql = "INSERT INTO public.producto(
            uuid_subcategoria_p, uuid_marca, nombre_producto, descripcion_producto, precio_producto, imagen_producto, uuid_estado_producto)
            VALUES (?, ?, ?, ?, ?, ?, (select uuid_estado_producto from estado_producto where estado_producto = 'En stock')) RETURNING uuid_producto";
        $params = array($this->subcategoria, $this->marca, $this->nombre, $this->descripcion, $this->precio, $this->imagen);
        /* Guardamos el último id porque es necesario para hacer el insert de manera completa */
        if ($this->id = Database::getRowId($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    /*FUNCIONES PARA INSERTAR EN TABLAS FORANEAS INFORMACIÓN DEL PRODUCTO -------------------------------------.*/
    public function insertStock()
    {
        $sql = 'INSERT INTO color_stock(uuid_producto, uuid_color_producto, stock) VALUES(?, ?, ?)';
        $params = array($this->id, $this->color, $this->stock);
        return Database::executeRow($sql, $params);
    }

    public function insertProvider()
    {
        $sql = 'INSERT INTO detalle_producto(uuid_producto, uuid_proveedor) VALUES(?, ?)';
        $params = array($this->id, $this->proveedor);
        return Database::executeRow($sql, $params);
    }


    /* UPDATE */
    public function updateRow($current_image)
    {
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagen) ? $this->deleteFile($this->getRuta(), $current_image) : $this->imagen = $current_image;

        $sql = 'UPDATE producto
                SET uuid_subcategoria_p=?, uuid_marca=?, nombre_producto=?, descripcion_producto=?, precio_producto=?, imagen_producto=?, uuid_estado_producto=?
                WHERE uuid_producto=?;';
        $params = array($this->subcategoria, $this->marca, $this->nombre, $this->descripcion, $this->precio, $this->imagen, $this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function updateStock()
    {
        $sql = 'SELECT COUNT(*) 
                FROM color_stock WHERE uuid_color_producto = ? AND uuid_producto = ?';
        $params = array($this->color, $this->id);

        if (Database::registerExist($sql, $params)) {
            $sql = 'UPDATE color_stock SET stock = ? WHERE uuid_color_producto = ? AND uuid_producto = ?;';
            $params = array($this->stock, $this->color, $this->id);
            return Database::executeRow($sql, $params);
        } else {
            $sql = 'INSERT INTO color_stock(uuid_producto, uuid_color_producto, stock) VALUES(?, ?, ?)';
            $params = array($this->id, $this->color, $this->stock);
            return Database::executeRow($sql, $params);
        }
    }
    public function updateProvider()
    {
        $sql = 'SELECT COUNT(*) 
                FROM detalle_producto WHERE uuid_proveedor = ? AND uuid_producto = ?;';
        $params = array($this->proveedor, $this->id);

        if (Database::registerExist($sql, $params)) {
            return true;
        } else {
            $sql = 'INSERT INTO detalle_producto(uuid_producto, uuid_proveedor) VALUES(?, ?)';
            $params = array($this->id, $this->proveedor);
            return Database::executeRow($sql, $params);
        }
    }
    /* */
    public function readProductStock()
    {
        $sql = 'SELECT 	stock
        FROM color_stock 
		WHERE uuid_producto = ? and uuid_color_producto = ?';
        $params = array($this->id, $this->color);
        return Database::getRow($sql, $params);
    }
    public function readProductStockUpdate()
    {
        $sql = 'SELECT 	stock
        FROM color_stock 
		WHERE uuid_color_stock = ?';
        $params = array($this->color);
        return Database::getRow($sql, $params);
    }

    /* DELETE */
    /* Funciones para inhabilitar un producto ya que no los borraremos de la base*/
    /* Función que actualiza el estado del producto a sin existencias--------------------------------------*/
    public function deleteRow()
    {
        //No eliminaremos registros, solo los inhabilitaremos, borraremos el stock, y borraremos sus registros en tablas foraneas, dejando uno para que se pueda mostrar en la tabla
        $sql = "UPDATE producto SET uuid_estado_producto = (SELECT uuid_estado_producto FROM estado_producto WHERE estado_producto = 'Sin existencias') WHERE uuid_producto = ?;";
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    /* Función que borra todos los registros del producto en la tabla color_stock dejando uno para que se muestre en el readRows-------------------------------------.*/
    public function deleteColorStock()
    {
        $sql = 'DELETE FROM color_stock
        WHERE uuid_color_stock IN 
        (SELECT uuid_color_stock
        FROM (SELECT uuid_color_stock,
                ROW_NUMBER() OVER (partition BY uuid_producto ORDER BY uuid_color_stock) AS RowNumber
                FROM color_stock) AS T
        WHERE T.RowNumber > 1) AND uuid_producto = ?;';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
    /* Función que borra todos los registros del producto en la tabla detalle_producto dejando uno para que se muestre en el readRows------------------------.*/
    public function deleteProvider()
    {
        $sql = 'DELETE FROM detalle_producto
        WHERE uuid_detalle_producto IN 
        (SELECT uuid_detalle_producto
        FROM (SELECT uuid_detalle_producto,
                ROW_NUMBER() OVER (partition BY uuid_producto ORDER BY uuid_detalle_producto) AS RowNumber
            FROM detalle_producto) AS T
        WHERE T.RowNumber > 1) AND uuid_producto = ?;';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
    /* Función que actualiza el stock del producto después de vaciar las tablas necesarias------------------------.*/
    public function colorAfterDelete()
    {
        $sql = 'UPDATE color_stock SET stock = 0 WHERE uuid_producto = ?;';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    // Función para leer todos los productos con stock para mostrar en las tablas de detalle venta y detalle venta del día
    public function readProductosVentas()
    {
        $sql = "SELECT DISTINCT on (nombre_producto) nombre_producto, uuid_producto, color_producto, (select sumStockRead(uuid_producto::varchar)) as stock, imagen_producto
                from color_stock inner join producto using(uuid_producto)
                inner join color_producto using (uuid_color_producto)
                inner join detalle_producto using (uuid_producto)
                WHERE stock > 0
                ORDER BY  nombre_producto, stock DESC;";
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Función para leer todos los productos tras una búsqueda en la tabla de detalle venta y detalle del venta del día
    public function readProductosVentasSearch($value)
    {
        $sql = "SELECT DISTINCT on (nombre_producto) nombre_producto, uuid_producto, color_producto, (select sumStockRead(uuid_producto::varchar)) as stock, imagen_producto
                from color_stock inner join producto using(uuid_producto)
                inner join color_producto using (uuid_color_producto)
                inner join detalle_producto using (uuid_producto)
                WHERE nombre_producto ILIKE ?
                AND stock > 0
                ORDER BY  nombre_producto, stock DESC";
        $params = array("%$value%");
        return Database::getRows($sql, $params);
    }


    /*
    *   Métodos para generar gráficas.
    */

    //Grafica de iventario por proveedor ------------------------------.

    public function inventarioGProveedor()
    {
        $sql = "SELECT sum(stock) as count, nombre_proveedor
        FROM producto
		INNER JOIN color_stock USING(uuid_producto)
        INNER JOIN detalle_producto USING(uuid_producto)
        INNER JOIN proveedor USING(uuid_proveedor) 
		WHERE uuid_proveedor != (SELECT uuid_proveedor FROM proveedor WHERE nombre_proveedor = 'Librería Económica')
		AND stock > 0
		GROUP BY nombre_proveedor";
        $params = null;
        return Database::getRows($sql, $params);
    }

    public function ventasPorSemana()
    {
        $sql = 'SELECT extract(day from fecha_venta) as "Fecha", sum(getmonto(uuid_venta)) as total from venta 
        where fecha_venta between (select current_date - cast(\'6 days\' as interval))  and current_date and uuid_estado_venta = (select uuid_estado_venta from estado_venta where estado_venta = \'Pagado\')
        group by fecha_venta order by fecha_venta';
        $params = null;
        return Database::getRows($sql, $params);
    }


    public function estadisticaVentasPorSemana()
    {
        $sql = 'SELECT sum(getMonto(uuid_venta)) as total from venta 
        where fecha_venta between (select current_date - cast(\'6 days\' as interval))  and (select current_date) and uuid_estado_venta = (select uuid_estado_venta from estado_venta where estado_venta = \'Pagado\')';
        $params = null;
        return Database::getRow($sql, $params);
    }


    // Método para generar gráfico de flujo de precios ------------------------------.

    public function flujoPrecio($start, $end)
    { 
        $sql = 'SELECT ROUND(AVG(precio_producto),2) as precio_producto, EXTRACT(DAY FROM fecha_bitacora) as "dia", EXTRACT(MONTH FROM fecha_bitacora) as "mes"
        FROM bitacora_precios 
        WHERE uuid_producto = ? AND fecha_bitacora BETWEEN ? AND ? GROUP BY fecha_bitacora Order by fecha_bitacora asc';
        $params = array($this->id, $start, $end);
        return Database::getRows($sql, $params);
    }

    // Método para generar gráfico de flujo de stock ------------------------------.

    public function flujoStock($start, $end)
    {
        $sql = 'SELECT SUM(stock_agregado) as stock_agregado, EXTRACT(DAY FROM fecha_bitacora) as "dia", EXTRACT(MONTH FROM fecha_bitacora) as "mes"
        FROM bitacora_stock
        WHERE uuid_producto = ?
        AND fecha_bitacora BETWEEN ? AND ?
        GROUP BY fecha_bitacora
        ORDER BY fecha_bitacora asc';
        $params = array($this->id, $start, $end);
        return Database::getRows($sql, $params);
    }

    // Método para generar gráfico de flujo de Ventas ------------------------------.

    public function flujoVentas($start, $end)
    {
        $sql = 'SELECT sum(cantidad_producto) as cantidad, extract(day from fecha_venta) as fecha_venta, extract(MONTH from fecha_venta) as mes
        FROM detalle_venta INNER JOIN venta USING(uuid_venta)
        INNER JOIN color_stock USING(uuid_color_stock) 
        INNER JOIN producto USING(uuid_producto)
        WHERE uuid_producto = ? AND fecha_venta BETWEEN ? AND ? AND uuid_estado_venta = (SELECT uuid_estado_venta FROM estado_venta WHERE estado_venta = \'Pagado\')
        GROUP BY fecha_venta ORDER BY mes;';
        $params = array($this->id, $start, $end);
        return Database::getRows($sql, $params);
    }

        
    

    /*
    *   Métodos para generar reportes.
    */
    public function inventarioProveedor()
    {
        $sql = 'SELECT nombre_subcategoria_p, nombre_marca, nombre_producto, precio_producto, estado_producto
        FROM producto
        INNER JOIN detalle_producto USING(uuid_producto)
        INNER JOIN proveedor USING(uuid_proveedor)
        INNER JOIN subcategoria_producto USING(uuid_subcategoria_p)
        INNER JOIN marca USING(uuid_marca)
        INNER JOIN estado_producto USING(uuid_estado_producto)
        WHERE proveedor.uuid_proveedor = ?';
        $params = array($this->proveedor);
        return Database::getRows($sql, $params);
    }

    // Método para generar reporte de flujo de precios ------------------------------.

    public function flujoPrecioReport($start, $end)
    {
        $sql = 'SELECT ROUND(AVG(bitacora_precios.precio_producto),2) as precio_producto, fecha_bitacora 
            FROM bitacora_precios 
            INNER JOIN producto USING(uuid_producto)
            WHERE uuid_producto = ?
            AND fecha_bitacora BETWEEN ? AND ?
            GROUP BY fecha_bitacora
            Order by fecha_bitacora asc';
        $params = array($this->id, $start, $end);
        return Database::getRows($sql, $params);
    }

    // Método para obtener información adicional de reporte de flujo de precios ------------------------------.

    public function flujoPrecioReportInfo()
    {
        $sql = 'SELECT nombre_producto
            FROM bitacora_precios 
            INNER JOIN producto USING(uuid_producto)
            WHERE uuid_producto = ? limit 1';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Método para generar reporte de flujo de stock ------------------------------.

    public function flujoStockReport($start, $end)
    {
        $sql = 'SELECT color_producto, SUM(stock_agregado) as stock_agregado, fecha_bitacora 
        FROM bitacora_stock 
        INNER JOIN color_producto USING(uuid_color_producto)
        WHERE uuid_producto = ?
        AND fecha_bitacora BETWEEN ? AND ?
        GROUP BY color_producto, fecha_bitacora
        Order by fecha_bitacora asc';
        $params = array($this->id, $start, $end);
        return Database::getRows($sql, $params);
    }

    // Método para obtener información adicional de reporte de flujo de stock ------------------------------.

    public function flujoStockReportInfo()
    {
        $sql = 'SELECT nombre_producto
        FROM bitacora_precios 
        INNER JOIN producto USING(uuid_producto)
        WHERE uuid_producto = ? limit 1';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Método para generar reporte de flujo de Ventas de productos ------------------------------.
    public function flujoVentasReport($start, $end)
    {
        $sql = 'SELECT correlativo_venta, nombre_cliente, tipo_venta, tipo_factura, fecha_venta, getmonto(uuid_venta) as monto
        FROM venta INNER JOIN cliente USING(uuid_cliente)
        INNER JOIN tipo_venta USING(uuid_tipo_venta)
        INNER JOIN tipo_factura USING(uuid_tipo_factura)
        INNER JOIN detalle_venta USING(uuid_venta)
        INNER JOIN color_stock USING(uuid_color_stock)
        WHERE uuid_producto = ? AND fecha_venta BETWEEN ? AND ? AND uuid_estado_venta = (SELECT uuid_estado_venta FROM estado_venta WHERE estado_venta = \'Pagado\')';
        $params = array($this->id, $start, $end);
        return Database::getRows($sql, $params);
    }
 // Método para obtener información adicional reporte de flujo de Ventas de productos ------------------------------.
    public function flujoVentasReportInfo()
    {
        $sql = 'SELECT nombre_producto
        FROM producto 
        WHERE uuid_producto = ? limit 1';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

}

