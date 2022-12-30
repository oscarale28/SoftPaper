<?php
/*
*	Clase para manejar la tabla catalogo de colores de la base de datos de la tienda.
*   Es una clase hija de Validator.
*/
class EstadoEmpleado extends Validator
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

    // public function setEstadoEstado($value)
    // {
    //     if ($this->validateBoolean($value)) {
    //         $this->estado_estado = $value;
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

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

    // public function getEstadoEstado()
    // {
    //     return $this->estado_estado;
    // }

    // Método para leer toda la información de los estados existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_estado_empleado, estado_empleado FROM estado_empleado';
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Método para un dato en especifico de los estados existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT  uuid_estado_empleado, estado_empleado FROM estado_empleado where uuid_estado_empleado = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // /* CREATE */
    // public function createRow()
    // {
    //     $sql = 'INSERT INTO estado_producto(estado_producto, estado_estado_producto)
    //         VALUES (?, ?)';
    //     $params = array($this->estado, $this->estado_estado);
    //     return Database::executeRow($sql, $params);
    // }

    // /* UPDATE */
    // public function updateRow()
    // {
    //     $sql = 'UPDATE estado_producto
    //     SET estado_producto=?, estado_estado_producto=?
    //     WHERE uuid_estado_producto = ?;';
    //         $params = array($this->estado,$this->estado_estado,$this->id);
    //     return Database::executeRow($sql, $params);
    // }

    // /* DELETE */
    // /* Función para borrar un color de la base (Solo se inahbilita)-------------------------*/
    // public function deleteRow()
    // {
    //     $this->estado = 0;
    //     //No eliminaremos registros, solo los inhabilitaremos-------------------------
    //     $sql = 'UPDATE estado_producto
    //             SET estado_estado_producto = ?
    //             WHERE uuid_estado_producto = ?';
    //     $params = array($this->estado, $this->id);
    //     return Database::executeRow($sql, $params);
    // }
}