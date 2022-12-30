<?php
/*


*	Clase para manejar la tabla tipo factura de la base de datos de la tienda.
*   Es una clase hija de Validator.
*/
class CargoEmpleado extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $cargo = null;

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




    public function setCargo($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->cargo = $value;
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

    /*
    *   Métodos para obtener valores de los atributos.
    */
    public function getId()
    {
        return $this->id;
    }

    public function getCargo()

    {
        return $this->cargo;
    }


    public function getEstado()
    {
        return $this->estado;
    }

    // Método para leer toda la información de tipo factura existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_cargo_empleado, cargo_empleado from cargo_empleado;';
        $params = null;
        return Database::getRows($sql, $params);
    }

}