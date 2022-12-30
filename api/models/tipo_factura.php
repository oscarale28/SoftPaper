<?php
/*

*	Clase para manejar la tabla tipo factura de la base de datos de la tienda.
*   Es una clase hija de Validator.
*/
class TipoFactura extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $tipo = null;
    private $estado = null;


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



    public function setTipo($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->tipo = $value;
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

    public function getTipo()
    {
        return $this->tipo;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    // Método para leer toda la información de tipo factura existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_tipo_factura, tipo_factura, estado_tipo_factura FROM tipo_factura';
        $params = null;
        return Database::getRows($sql, $params);
    }

    public function readAllTFDetalle()
    {
        $sql = 'SELECT uuid_tipo_factura, tipo_factura, estado_tipo_factura FROM tipo_factura WHERE estado_tipo_factura = true';
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Método para un dato en especifico de tipo factura existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT uuid_tipo_factura, tipo_factura FROM tipo_factura where uuid_tipo_factura = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO tipo_factura(tipo_factura, estado_tipo_factura)
            VALUES (?, ?)';
        $params = array($this->tipo, $this->estado);
        return Database::executeRow($sql, $params);
    }

    /* UPDATE */
    public function updateRow()
    {
        $sql = 'UPDATE tipo_factura
        SET tipo_factura=?
        WHERE uuid_tipo_factura = ?;';
            $params = array($this->tipo, $this->id);
        return Database::executeRow($sql, $params);
    }

    /* DELETE */
    /* Función para borrar un color de la base (Solo se inahbilita)-------------------------*/
    public function deleteRow()
    {
        $this->estado = 0;
        //No eliminaremos registros, solo los inhabilitaremos-------------------------
        $sql = 'UPDATE tipo_factura
                SET estado_tipo_factura = ?
                WHERE uuid_tipo_factura = ?';
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
        $sql = 'UPDATE tipo_factura SET estado_tipo_factura = ? WHERE uuid_tipo_factura = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }
}