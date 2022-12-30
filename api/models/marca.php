<?php
/*
*	Clase para manejar la tabla categorias de la base de datos.
*   Es clase hija de Validator.
*/
class Marca extends Validator
{
    // Declaración de atributos (propiedades).
    private $id = null;
    private $marca = null;
    private $imagen = null;
    private $estado = null;
    private $ruta = '../images/marcas/';

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

    public function setMarca($value)
    {
        if ($this->validateString($value, 1, 50)) {
            $this->marca = $value;
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

    public function getMarca()
    {
        return $this->marca;
    }

    public function getImagen()
    {
        return $this->imagen;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getRuta()
    {
        return $this->ruta;
    }

    // Método para leer toda la información de las marcas existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_marca, nombre_marca, imagen_marca, estado_marca FROM marca';
        $params = null;
        return Database::getRows($sql, $params);
    }

    public function readAllSelect()
    {
        $sql = 'SELECT uuid_marca, nombre_marca, imagen_marca, estado_marca FROM marca WHERE estado_marca = true';
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Método para un dato en especifico de las marcas existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT "nombre_marca", "imagen_marca", "estado_marca" FROM marca  WHERE "uuid_marca" = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    /* SEARCH */
    public function searchRows($value)
    {
        $sql = 'SELECT "uuid_marca", "nombre_marca", "imagen_marca", "estado_marca" FROM marca
        WHERE "nombre_marca" ILIKE ?
		ORDER BY "uuid_marca"';
        $params = array("%$value%");
        return Database::getRows($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO marca(
            "nombre_marca", "imagen_marca", "estado_marca")
            VALUES (?, ?, ?);';
        $params = array($this->marca, $this->imagen, $this->estado);
        return Database::executeRow($sql, $params);
    }

    /* UPDATE */
    public function updateRow($current_image)
    {   
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagen) ? $this->deleteFile($this->getRuta(), $current_image) : $this->imagen = $current_image;

        $sql = 'UPDATE marca
            SET "nombre_marca"=?, "imagen_marca"=?
            WHERE "uuid_marca"= ?;';
            $params = array($this->marca, $this->imagen, $this->id);
        return Database::executeRow($sql, $params);
    }

    /* DELETE */
    /* Función para borrar un color de la base (Solo se inahbilita)-------------------------*/
    public function deleteRow()
    {
        //No eliminaremos registros, solo los inhabilitaremos----------------------
        $this->estado = 0;
        $sql = 'UPDATE marca SET estado_marca = ? WHERE "uuid_marca" = ?';
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
        $sql = 'UPDATE marca SET "estado_marca" = ? WHERE "uuid_marca" = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }
}
