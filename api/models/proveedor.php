<?php
/*
*	Clase para manejar la tabla proveedores de la base de datos.
*   Es clase hija de Validator.
*/
class Proveedor extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $proveedor = null;
    private $telefono = null;
    private $estado = null;
    private $num = null;
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

    public function setProveedor($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->proveedor = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setTelefono($value)
    {   
        if ($this->validatePhone($value)) {
            $this->telefono = $value;
            return true;
        } else {
            return false;
        }
        
    }

    public function setEstado($value)
    {
        if ($this->validateBoolean($value)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setNum($value)
    {
        if ($this->validateBoolean($value)) {
            $this->num = $value;
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

    public function getProveedor()
    {
        return $this->proveedor;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getNum()
    {
        return $this->num;
    }

    // Método para leer toda la información de los proveedores existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_proveedor, nombre_proveedor, telefono_proveedor, estado_proveedor FROM proveedor;';
        $params = null;
        return Database::getRows($sql, $params);
    }

    public function readAllSelect()
    {
        $sql = 'SELECT uuid_proveedor, nombre_proveedor, telefono_proveedor, estado_proveedor FROM proveedor WHERE estado_proveedor = true;';
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Método para leer toda la información de los proveedores existentes-------------------------.
    public function readAllReport()
    {
        $sql = "SELECT uuid_proveedor, nombre_proveedor, telefono_proveedor, estado_proveedor 
        FROM proveedor inner join detalle_producto using(uuid_proveedor) 
		INNER JOIN producto USING(uuid_producto)
		INNER JOIN color_stock USING(uuid_producto)
		WHERE stock > 0 AND estado_proveedor = 'true'
		AND uuid_proveedor != (SELECT uuid_proveedor FROM proveedor WHERE nombre_proveedor = 'Librería Económica')
		group by uuid_proveedor";
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Método para un dato en especifico de los proveedores existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT uuid_proveedor, nombre_proveedor, telefono_proveedor, estado_proveedor FROM proveedor WHERE "uuid_proveedor" = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    /* SEARCH */
    public function searchRows($value)
    {
        $sql = 'SELECT "uuid_proveedor", "nombre_proveedor", "telefono_proveedor", "estado_proveedor"
        FROM proveedor
        WHERE "nombre_proveedor" ILIKE ?
		ORDER BY "uuid_proveedor"';
        $params = array("%$value%");
        return Database::getRows($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO proveedor(
            "nombre_proveedor", "telefono_proveedor", "estado_proveedor")
            VALUES (?, ?, ?);';
        $params = array($this->proveedor, $this->telefono, $this->estado);
        return Database::executeRow($sql, $params);
    }

    /* UPDATE */
    public function updateRow()
    {   
        $sql = 'UPDATE proveedor
        SET "nombre_proveedor" = ?, "telefono_proveedor" = ?
        WHERE "uuid_proveedor" = ?;';
            $params = array($this->proveedor, $this->telefono, $this->id);
        return Database::executeRow($sql, $params);
    }

    /* DELETE */
    /* Función para inhabilitar un usuario ya que no los borraremos de la base---------------------------*/
    public function deleteRow()
    {
        $this->estado = 0;
        //No eliminaremos registros, solo los inhabilitaremos----------------------------
        $sql = 'UPDATE proveedor SET "estado_proveedor" = ? WHERE "uuid_proveedor" = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    public function changeRow()
    {
        if ($this->num == 1) {
            $this->estado = 1;
        }
        else{
            $this->estado = 0;
        }
        $sql = 'UPDATE proveedor SET "estado_proveedor" = ? WHERE "uuid_proveedor" = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }
}