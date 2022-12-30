<?php
/*
*	Clase para manejar la tabla giro clientes de la base de datos de la tienda.
*   Es una clase hija de Validator.
*/
class Giro_Cliente extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $giro = null;

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

    public function setGiro($value)
    {
        if ($this->validateAlphabetic($value)) {
            $this->giro = $value;
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

    public function getGiro()
    {
        return $this->giro;
    }

    // Método para leer toda la información de los colores existentes-------------------------.
    public function readAll()
    {

        $sql = 'SELECT uuid_giro_cliente, giro_cliente FROM giro_cliente';
        $params = null;
        return Database::getRows($sql, $params);
    }
}