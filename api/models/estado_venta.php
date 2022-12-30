<?php
/*
*	Clase para manejar la tabla catalogo de colores de la base de datos de la tienda.
*   Es una clase hija de Validator.
*/
class EstadoVenta extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $estado = null;
    private $estado_estado = null;

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


    public function setEstado($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->estado = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstadoEstado($value)
    {
        if ($this->validateBoolean($value)) {
            $this->estado_estado = $value;
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

    public function getEstado()
    {
        return $this->estado;
    }

    public function getEstadoEstado()
    {
        return $this->estado_estado;
    }

    // Método para leer toda la información de los estados existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_estado_venta, estado_venta, estado_estado_venta FROM estado_venta ORDER BY estado_venta';
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Método para un dato en especifico de los estados existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT uuid_estado_venta, estado_venta, estado_estado_venta FROM estado_venta where uuid_estado_venta = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO estado_venta(estado_venta, estado_estado_venta)
            VALUES (?, ?);';
        $params = array($this->estado, $this->estado_estado);
        return Database::executeRow($sql, $params);
    }

    /* UPDATE */
    public function updateRow()
    {
        $sql = 'UPDATE estado_venta
        SET estado_venta=?
        WHERE uuid_estado_venta = ?;';
            $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    /* DELETE */
    /* Función para borrar un color de la base (Solo se inahbilita)-------------------------*/
    public function deleteRow()
    {
        $this->estado = 0;
        //No eliminaremos registros, solo los inhabilitaremos-------------------------
        $sql = 'UPDATE estado_venta
                SET estado_estado_venta = ?
                WHERE uuid_estado_venta = ?';
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
        $sql = 'UPDATE estado_venta SET estado_estado_venta = ? WHERE uuid_estado_venta = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }
}