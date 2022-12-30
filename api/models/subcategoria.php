<?php
/*
*	Clase para manejar la tabla subcategoria de la base de datos.
*   Es clase hija de Validator.
*/
class Subcategoria extends Validator
{
    // Declaración de atributos (propiedades) según nuestra tabla en la base de datos.
    private $id = null;
    private $nombre = null;
    private $categoria = null;
    private $imagen = null;
    private $estado = null;
    private $num = null;
    //Variable para un campo con imagen -------------------------.
    private $ruta = '../images/subcategoria/';

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
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->nombre = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCategoria($value)
    {
        $this->categoria = $value;
        return true;
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

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getCategoria()
    {
        return $this->categoria;
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

    public function getNum()
    {
        return $this->num;
    }

    /* 
    *   Método para comprobar que existen subcategorias registradas en nuestra base de datos
    */

    // Método para leer toda la información de las subcategorias existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_subcategoria_p, nombre_subcategoria_p, cp.nombre_categoria_p, "imagen_subcategoria", estado_subcategoria_p
        FROM "subcategoria_producto" as sc inner join "categoria_producto" as cp on sc."uuid_categoria_p" = cp."uuid_categoria_p"';
        
        $params = null;
        return Database::getRows($sql, $params);
    }

    //Método para leer información de subcategorías existentes dada una categoría
    public function readAllParam()
    {
        $sql = 'SELECT uuid_subcategoria_p, nombre_subcategoria_p FROM subcategoria_producto WHERE estado_subcategoria_p = true and uuid_categoria_p = ?;';
        
        $params = array($this->categoria);
        return Database::getRows($sql, $params);
    }
    
    // Método para un dato en especifico de las subcategorias existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT "uuid_subcategoria_p", "nombre_subcategoria_p", "imagen_subcategoria", "uuid_categoria_p", estado_subcategoria_p
        FROM "subcategoria_producto"
        WHERE "uuid_subcategoria_p" = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }
    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    /* SEARCH */
    public function searchRows($value)
    {
        $sql = 'SELECT uuid_subcategoria_p, nombre_subcategoria_p, cp.nombre_categoria_p, "imagen_subcategoria", estado_subcategoria_p
                FROM "subcategoria_producto" as sc inner join "categoria_producto" as cp on sc."uuid_categoria_p" = cp."uuid_categoria_p"
                WHERE "nombre_subcategoria_p" ILIKE ?
                ORDER BY "uuid_subcategoria_p"';
        $params = array("%$value%");
        return Database::getRows($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO "subcategoria_producto"("uuid_categoria_p", "nombre_subcategoria_p", "imagen_subcategoria", "estado_subcategoria_p")
        VALUES (?, ?, ?, ?);';
        $params = array($this->categoria, $this->nombre, $this->imagen, $this->estado);
        return Database::executeRow($sql, $params);
    }


    /* UPDATE */
    public function updateRow($current_image)
    {   
        // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
        ($this->imagen) ? $this->deleteFile($this->getRuta(), $current_image) : $this->imagen = $current_image;

        $sql = 'UPDATE "subcategoria_producto"
                SET "uuid_categoria_p"=?, "nombre_subcategoria_p"=?, "imagen_subcategoria"=?
                WHERE "uuid_subcategoria_p" = ?;';
            $params = array($this->categoria, $this->nombre, $this->imagen, $this->id);
        return Database::executeRow($sql, $params);
    }

    /* DELETE */
    /* Función para inhabilitar una subcategoria ya que no los borraremos de la base -------------------------.*/
    public function deleteRow()
    {
        $this->estado = 0;
        //No eliminaremos registros, solo los inhabilitaremos----------------------------
        $sql = 'UPDATE "subcategoria_producto" SET estado_subcategoria_p = ? WHERE "uuid_subcategoria_p" = ?';
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
        $sql = 'UPDATE "subcategoria_producto" SET "estado_subcategoria_p" = ? WHERE "uuid_subcategoria_p" = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    /*  */

}