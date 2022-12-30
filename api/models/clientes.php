<?php
/*
*	Clase para manejar la tabla usuarios de la base de datos.
*   Es clase hija de Validator.
*/
class Cliente extends Validator
{
    // Declaración de atributos (propiedades) según nuestra tabla en la base de datos.
    private $id = null;
    private $nombre = null;
    private $direccion = null;
    private $municipio = null;
    private $nrc = null;
    private $nit = null;
    private $dui = null;
    private $telefono = null;
    private $giro = null;
    private $estado = null;
    private $num = null;

    /*
    *   Métodos para validar y asignar valores de los atributos de clientes.
    */
    public function setId($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->id = $value;
            return true;
        } else {
            return false;
        }
    }

    
    public function setNombre($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->nombre = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDireccion($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->direccion = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setMunicipio($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->municipio = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setNrc($value)
    {
        if ($this->validateNRC($value)) {
            $this->nrc = $value;
            return true;
        } else {
            return false;
        }
    }
    public function setNIT($value)
    {
        if ($this->validateNIT($value)) {
            $this->nit = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setDUI($value)
    {
        if ($this->validateDUI($value)) {
            $this->dui = $value;
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

    public function setGiro($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->giro = $value;
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

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDireccion()
    {
        return $this->direccion;
    }

    public function getMunicipio()
    {
        return $this->municipio;
    }

    public function getNRC()
    {
        return $this->nrc;
    }

    public function getNIT()
    {
        return $this->nit;
    }

    public function getDUI()
    {
        return $this->dui;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function getGiro()
    {
        return $this->giro;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getNum()
    {
        return $this->num;
    }

    /* 
    *   Método para comprobar que existen subcategorias registradas en nuestra base de datos
    */

    // Método para leer toda la información de los clientes existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT "uuid_cliente", "nombre_cliente", "direccion_cliente", m."nombre_municipio", "nrc_cliente", "nit_cliente", "dui_cliente", "telefono_cliente", g."giro_cliente", "estado_cliente"
        FROM cliente as cc inner join "municipio" as m on cc."uuid_municipio" = m."uuid_municipio"
		inner join "giro_cliente" as g on cc."uuid_giro_cliente" = g."uuid_giro_cliente" 
        WHERE "nombre_cliente" != ?
        ORDER BY "uuid_cliente"';
        $params = array('Factura');
        return Database::getRows($sql, $params);
    }

    public function readClientesVenta()
    {
        $sql = 'SELECT "uuid_cliente", "nombre_cliente", "direccion_cliente", m."nombre_municipio", "nrc_cliente", "nit_cliente", "dui_cliente", "telefono_cliente", g."giro_cliente", "estado_cliente"
        FROM cliente as cc inner join "municipio" as m on cc."uuid_municipio" = m."uuid_municipio"
		inner join "giro_cliente" as g on cc."uuid_giro_cliente" = g."uuid_giro_cliente" 
		WHERE estado_cliente = true
        AND nombre_cliente != ?
        ORDER BY "uuid_cliente";
        ';
        $params = array('Factura');
        return Database::getRows($sql, $params);
    }

    public function searchClientesVenta($value)
    {
        $sql = 'SELECT "uuid_cliente", "nombre_cliente", "direccion_cliente", m."nombre_municipio", "nrc_cliente", "nit_cliente", "dui_cliente", "telefono_cliente", g."giro_cliente", "estado_cliente"
        FROM cliente as cc inner join "municipio" as m on cc."uuid_municipio" = m."uuid_municipio"
		inner join "giro_cliente" as g on cc."uuid_giro_cliente" = g."uuid_giro_cliente" 
		WHERE estado_cliente = true AND nombre_cliente ILIKE ?
        AND nombre_cliente != ?
        ORDER BY "uuid_cliente";
        ';
        $params = array("%$value%", 'Factura');
        return Database::getRows($sql, $params);
    }

    // Método para un dato en especifico de los clientes existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT cc."uuid_cliente", "nombre_cliente", "direccion_cliente", cc."uuid_municipio", "nombre_municipio", "nrc_cliente", "nit_cliente", "dui_cliente", "telefono_cliente", cc."uuid_giro_cliente", "giro_cliente", "estado_cliente", (SELECT uuid_departamento FROM municipio WHERE uuid_municipio = (SELECT uuid_municipio FROM cliente WHERE uuid_cliente = ?))
        FROM cliente as cc inner join "municipio" as m on cc."uuid_municipio" = m."uuid_municipio"
		inner join "giro_cliente" as g on cc."uuid_giro_cliente" = g."uuid_giro_cliente"
        WHERE cc."uuid_cliente" = ?';
        $params = array($this->id, $this->id);
        return Database::getRow($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    /* SEARCH */
    public function searchRows($value)
    {
        $sql = 'SELECT "uuid_cliente", "nombre_cliente", "direccion_cliente", m."nombre_municipio", "nrc_cliente", "nit_cliente", "dui_cliente", "telefono_cliente", g."giro_cliente", "estado_cliente"
        FROM cliente as cc inner join "municipio" as m on cc."uuid_municipio" = m."uuid_municipio"
		inner join "giro_cliente" as g on cc."uuid_giro_cliente" = g."uuid_giro_cliente"
        WHERE "nombre_cliente" ILIKE ? OR "dui_cliente" ILIKE ? OR g."giro_cliente" ILIKE ?
        ORDER BY "uuid_cliente"';
        $params = array("%$value%", "%$value%", "%$value%");
        return Database::getRows($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO cliente(
            "nombre_cliente", "direccion_cliente", "uuid_municipio", "nrc_cliente", "nit_cliente", "dui_cliente", "telefono_cliente", "uuid_giro_cliente", "estado_cliente")
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING "uuid_cliente";';
        $params = array($this->nombre, $this->direccion, $this->municipio, $this->nrc, $this->nit, $this->dui, $this->telefono, $this->giro, $this->estado);
        return Database::executeRow($sql, $params);
    }

    /* UPDATE */
    public function updateRow()
    {
        $sql = 'UPDATE cliente
            SET "nombre_cliente" = ?, "direccion_cliente" = ?, "uuid_municipio"=?, "nrc_cliente"=?, "nit_cliente"=?, "dui_cliente"=?, "telefono_cliente"=?, "uuid_giro_cliente"=?
            WHERE "uuid_cliente"=?;';
            $params = array($this->nombre, $this->direccion, $this->municipio, $this->nrc, $this->nit, $this->dui, $this->telefono, $this->giro, $this->id);
        return Database::executeRow($sql, $params);
    }

    /* DELETE */
    /* Función para inhabilitar un usuario ya que no los borraremos de la base*/
    public function deleteRow()
    {
        //No eliminaremos registros, solo los inhabilitaremos UwU-------------------------.
        $this->estado = 0;
        $sql = 'UPDATE cliente SET "estado_cliente" = ? WHERE "uuid_cliente" = ?';
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
        $sql = 'UPDATE cliente SET "estado_cliente" = ? WHERE "uuid_cliente" = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }
}
