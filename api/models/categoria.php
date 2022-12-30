<?php
/*
*	Clase para manejar la tabla subcategoria de la base de datos.
*   Es clase hija de Validator.
*/
class Categoria extends Validator
{
    // Declaración de atributos (propiedades) según nuestra tabla en la base de datos.
    private $id = null;
    private $nombre = null;
    private $descripcion = null;
    private $estado = null;

    /*
    *   Métodos para validar y asignar valores de los atributos de categoria.
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

    public function setDescripcion($value)
    {
        if($this->validateString($value, 1, 250)) {
            $this->descripcion = $value;
            return true;
        } else {
            return false;
        }
        
    }

    public function setEstado($value)
    {
        if ($this->validateNaturalNumber($value)) {
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

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function getEstado()
    {
        return $this->estado;
    }


    /* 
    *   Método para comprobar que existen categorias registradas en nuestra base de datos
    */

    // Método para leer toda la información de las categorias existentes-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_categoria_p, nombre_categoria_p FROM categoria_producto WHERE estado_categoria_p = true;';
        
        $params = null;
        return Database::getRows($sql, $params);
    }

    public function readAllReport()
    {
        $sql = 'SELECT uuid_categoria_p, nombre_categoria_p 
        FROM categoria_producto inner join subcategoria_producto using(uuid_categoria_p)
        inner join producto using(uuid_subcategoria_p) 
        inner join color_stock using(uuid_producto)
        WHERE estado_categoria_p = true
        GROUP BY uuid_categoria_p';
        
        $params = null;
        return Database::getRows($sql, $params);
    }
    
    // Método para un dato en especifico de las categorias existentes-------------------------.
    public function readOne()
    {
        $sql = 'SELECT *
        FROM categoria_producto
        wHERE uuid_categoria_p = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }
    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    /* SEARCH */
    public function searchRows($value)
    {
        $sql = 'SELECT "idSubCategoriaP", "nombreSubCategoriaP", sc."descripcionSubCategoriaP", "imagenSubcategoria", cp."nombreCategoriaP", e.estado
                FROM "subcategoriaProducto" as sc inner join "categoriaProducto" as cp on sc."idCategoriaP" = cp."idCategoria"
                inner join estado e on sc.estado = e."idEstado"
                WHERE "nombreSubCategoriaP" ILIKE ? OR cp."nombreCategoriaP" ILIKE ?
                ORDER BY "idSubCategoriaP"';
        $params = array("%$value%", "%$value%");
        return Database::getRows($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO "subcategoriaProducto"("idCategoriaP", "nombreSubCategoriaP", "descripcionSubCategoriaP", "imagenSubcategoria", "estado")
        VALUES (?, ?, ?, ?, ?);';
        $params = array($this->categoria, $this->nombre, $this->descripcion, $this->imagen, $this->estado);
        return Database::executeRow($sql, $params);
    }


    /* UPDATE */
    // public function updateRow($current_image)
    // {   
    //     // Se verifica si existe una nueva imagen para borrar la actual, de lo contrario se mantiene la actual.
    //     ($this->imagen) ? $this->deleteFile($this->getRuta(), $current_image) : $this->imagen = $current_image;

    //     $sql = 'UPDATE "subcategoriaProducto"
    //             SET "idCategoriaP"=?, "nombreSubCategoriaP"=?, "descripcionSubCategoriaP"=?, "imagenSubcategoria"=?, estado=?
    //             WHERE "idSubCategoriaP" = ?;';
    //         $params = array($this->categoria, $this->nombre, $this->descripcion, $this->imagen, $this->estado, $this->id);
    //     return Database::executeRow($sql, $params);
    // }

    /* DELETE */
    /* Función para inhabilitar una categoria ya que no los borraremos de la base -------------------------.*/
    public function deleteRow()
    {
        //No eliminaremos registros, solo los inhabilitaremos UwU-------------------------.
        $sql = 'UPDATE "subcategoriaProducto" SET estado = 3 WHERE "idSubCategoriaP" = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    /*  */
    public function readcategoriasCategoria()
    {
        $sql = 'SELECT "idSubCategoriaP", "imagenSubcategoria", "nombreSubCategoriaP", "descripcionSubCategoriaP"
        FROM "subcategoriaProducto"
        WHERE "idCategoriaP" =  ?
        ORDER BY "idSubCategoriaP"';
        $params = array($this->id);
        return Database::getRows($sql, $params);
    }
}