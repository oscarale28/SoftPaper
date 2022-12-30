<?php
/*
*	Clase para manejar la tabla catalogo de colores de la base de datos de la tienda.
*   Es una clase hija de Validator.
*/
class Municipio extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $municipio = null;

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

    public function setMunicipio($value)
    {
        if ($this->validateAlphanumeric($value, 1, 50)) {
            $this->municipio = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDepartamento($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->departamento = $value;
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

    public function getMunicipio()
    {
        return $this->municipio;
    }

    public function getDepartamento()
    {
        return $this->departamento;
    }

    // Método para leer toda la información de los municipios existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_municipio, nombre_municipio, d.uuid_departamento
        FROM municipio as m inner join "departamento" as d on m.uuid_departamento = d.uuid_departamento 
        WHERE nombre_departamento = ?';
        $params = array($this->departamento);
        return Database::getRows($sql, $params);
    }

    public function readAllTable()
    {
        $sql = 'SELECT uuid_municipio, nombre_municipio, m.uuid_departamento, d.nombre_departamento 
        FROM municipio as m inner join departamento as d on m."uuid_departamento" = d."uuid_departamento"';
        $params = null;
        return Database::getRows($sql, $params);
    }

    public function readAllParam()
    {
        $sql = 'SELECT uuid_municipio, nombre_municipio FROM municipio WHERE uuid_departamento = ?;';
        
        $params = array($this->departamento);
        return Database::getRows($sql, $params);
    }

    public function readAll2()
    {
        $sql = 'SELECT uuid_municipio, nombre_municipio, m.uuid_departamento, d.nombre_departamento
        FROM municipio as m inner join "departamento" as d on m.uuid_departamento = d.uuid_departamento';
        $params = array($this->departamento);
        return Database::getRows($sql, $params);
    }

    // Función para leer uno específico
    public function readOne()
    {
        $sql = 'SELECT "uuid_municipio", "nombre_municipio", m."uuid_departamento", d."nombre_departamento"
        FROM municipio as m inner join "departamento" as d on m."uuid_departamento" = d."uuid_departamento"
        WHERE "uuid_municipio" = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO municipio(
            "nombre_municipio", "uuid_departamento")
            VALUES (?, ?);';
        $params = array($this->municipio, $this->departamento);
        return Database::executeRow($sql, $params);
    }

    /* UPDATE */
    public function updateRow()
    {   
        $sql = 'UPDATE municipio
        SET "nombre_municipio" = ?, "uuid_departamento" = ?
        WHERE "uuid_municipio" = ?;';
            $params = array($this->municipio, $this->departamento, $this->id);
        return Database::executeRow($sql, $params);
    }

    /* DELETE */
    /* Función para eliminar municipio de la base---------------------------*/
    public function deleteRow()
    {
        //Eliminamos el municipio----------------------------
        $sql = 'DELETE FROM municipio WHERE "uuid_municipio" = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }
}