<?php
/*
*	Esta sera la clase para manejar la tabla usuarios de la base de datos.
*   Es una clase hija de Validator.
*/
class Usuarios extends Validator
{
    // Declaración de atributos (propiedades) según nuestra tabla en la base de datos.
    private $id = null;
    private $nombres = null;
    private $apellidos = null;
    private $cargo = null;
    private $foto = null;
    private $estado = null;
    private $correo = null;
    private $alias = null;
    private $clave = null;
    private $num = null;
    private $dobleAutenticacion = null;

    private $estado_activo = 'ae11fe84-64d7-43cd-a519-fe42ebfe2ce4';
    private $estado_bloqueado = '5d18c453-29da-49bc-9216-69f408ff7362';

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

    public function setNombres($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->nombres = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setApellidos($value)
    {
        if ($this->validateAlphabetic($value, 1, 50)) {
            $this->apellidos = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setCargo($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->cargo = $value;
            return true;
        } else {
            return false;
        }
    }


    public function setCorreo($value)
    {
        if ($this->validateEmail($value)) {
            $this->correo = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setAlias($value)
    {
        if ($this->validateAlphanumeric($value, 1, 38)) {
            $this->alias = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setClave($value, $nombres, $apellidos, $alias)
    {
        if ($this->validatePassword($value, $nombres, $apellidos, $alias)) {
            $this->clave = password_hash($value, PASSWORD_DEFAULT);
            return true;
        } else {
            return false;
        }
    }


    public function setFoto($value)
    {
        if ($this->validateString($value, 1, 38)) {
            $this->foto = $value;
            return true;
        } else {
            return false;
        }
    }

    public function setEstado($value)
    {
        if ($this->validateString($value, 1, 38)) {
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

    public function setDobleAutenticacion($value)
    {
        if ($this->validateBoolean($value)) {
            $this->dobleAutenticacion = $value;
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

    public function getNombres()
    {
        return $this->nombres;
    }

    public function getApellidos()
    {
        return $this->apellidos;
    }

    public function getCargo()
    {
        return $this->cargo;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function getNum()
    {
        return $this->num;
    }

    public function getDobleAutenticacion()
    {
        return $this->dobleAutenticacion;
    }
    /*
    *   Métodos para gestionar la cuenta de la tabla usuario.
    */

    // Se verifica si hay concidencias de información mediante el correo del empleado ingresado-------------------------.

    public function checkUser($correo)
    {
        $sql = 'SELECT uuid_empleado, uuid_estado_empleado, correo_empleado, nombres_empleado, apellidos_empleado, alias_empleado
        FROM empleado inner join avatar_empleado using (uuid_avatar) 
        WHERE correo_empleado = ? or alias_empleado = ?';
        $params = array($correo, $correo);
        if ($data = Database::getRow($sql, $params)) {
            $this->id = $data['uuid_empleado'];
            $this->estado = $data['uuid_estado_empleado'];
            $this->correo = $data['correo_empleado'];
            $this->nombres = $data['nombres_empleado'];
            $this->apellidos = $data['apellidos_empleado'];
            $this->alias = $data['alias_empleado'];
            
            return true;
        } else {
            return false;
        }
    }

    // Método para verificar la contraseña-------------------------.
    public function checkPassword($password)
    {
        $sql = "SELECT contrasena_empleado FROM empleado WHERE uuid_empleado = ?";
        $params = array($this->id);
        $data = Database::getRow($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($password, $data['contrasena_empleado'])) {
            return true;
        } else {
            return false;
        }
    }

    public function checkPassword2($pass)
    {
        $sql = "SELECT contrasena_empleado FROM empleado WHERE uuid_empleado = ?";
        $params = array($_SESSION['uuid_empleado_renew']);
        $data = Database::getRow($sql, $params);
        // Se verifica si la contraseña coincide con el hash almacenado en la base de datos.
        if (password_verify($pass, $data['contrasena_empleado'])) {
            return true;
        } else {
            return false;
        }
    }

    //Método para verificar que el token ingresado sea el mismo al guardado en la base
    public function checkVerificationCode($token)
    {
        $sql = 'SELECT token FROM autenticacion WHERE uuid_empleado = ?';
        $params = array($_SESSION['uuid_empleado_verification']);
        if ($data = Database::getRow($sql, $params)) {
            if ($token == $data['token']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }


    //Método para verificar si el token ya venció
    public function checkTimeVerificationCode()
    {   //seteamos la zona horaria
        ini_set('date.timezone', 'America/El_Salvador');
        $sql = "SELECT to_char(fecha_token,'YYYY:MM:DD HH:MI:SS') as fecha_token FROM autenticacion WHERE uuid_empleado = ?";
        $params = array($_SESSION['uuid_empleado_verification']);
        $data = Database::getRow($sql, $params);

        $currentDate = new DateTime();
        $createdAt = new DateTime($data['fecha_token']);
        //Le restamos 12 horas para que el formato sea el mismo
        $createdAt->modify('-12 hour');
        //Sacamos la diferencia entre los dos timestamps
        $difference = $currentDate->diff($createdAt);
        
        //Si la diferencia es menor a 2 minutos seguimos
        if ($difference->i < 2) { //s de segundo, h de horas, i de minutos

            $sql = 'SELECT uuid_empleado, imagen_avatar, uuid_estado_empleado, alias_empleado, correo_empleado, nombres_empleado, apellidos_empleado
            FROM empleado 
            INNER JOIN avatar_empleado USING (uuid_avatar)
            INNER JOIN estado_empleado USING (uuid_estado_empleado)
            WHERE uuid_empleado = ?';
            $params = array($_SESSION['uuid_empleado_verification']);
            
            //Si los datos existen, retornamos true, en caso contrario false
            if ($data = Database::getRow($sql, $params)) {
                $this->id = $data['uuid_empleado'];
                $this->estado = $data['uuid_estado_empleado'];
                $this->correo = $data['correo_empleado'];
                $this->foto = $data['imagen_avatar'];
                $this->nombres = $data['nombres_empleado'];
                $this->apellidos = $data['apellidos_empleado'];
                $this->alias = $data['alias_empleado'];
                //unset($_SESSION['uuid_empleado_verification']);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    // Método para cambiar la contraseña-------------------------.
    public function changePassword()
    {
        $sql = 'UPDATE empleado SET claveEmpleado = ? WHERE idEmpleado = ?';
        $params = array($this->clave, $this->id);
        return Database::executeRow($sql, $params);
    }

    // Método para actualizar estado de empleado al fallar 3 o más veces la contraseña-------------------------.
    public function blockAccount()
    {
        $sql = 'UPDATE empleado SET uuid_estado_empleado = ? WHERE uuid_empleado = ?';
        $params = array($this->estado_bloqueado, $this->id);
        return Database::executeRow($sql, $params);
    }

    // Método para comprobar si el empleado está bloqueado (login)-------------------------.
    public function checkBlockedUser()
    {
        $sql = 'SELECT uuid_empleado from empleado where uuid_empleado = ? and uuid_estado_empleado = ?';
        $params = array($this->id, $this->estado_activo);

        if (Database::getRow($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    // Método para comprobar si el empleado está bloqueado (recuperación)-------------------------.
    public function checkBlockedUserRecu($correoRecu)
    {
        $sql = 'SELECT uuid_empleado from empleado where correo_empleado = ? and uuid_estado_empleado = ?';
        $params = array($correoRecu, $this->estado_activo);

        if (Database::getRow($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    // Método para registrar bloqueo de usuario tras intentos fallidos de autenticación-------------------------.
    public function registerUserBlock()
    {
        $sql = 'INSERT INTO bitacora_bloqueos(uuid_empleado, fecha_bloqueo)
        VALUES (?, CURRENT_TIMESTAMP);';
        $params = array($this->id);
        if (Database::getRow($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    // Método para desbloquear un usuario después de 24 horas -------------------------.
    public function unblockUser()
    {
        ini_set('date.timezone', 'America/El_Salvador');
        $sql = "SELECT to_char(fecha_bloqueo, 'YYYY:MM:DD HH:MI:SS') as fecha_bloqueo 
        from bitacora_bloqueos where uuid_empleado = ?
        order by fecha_bloqueo desc limit 1";
        $params = array($this->id);
        $data = Database::getRow($sql, $params);

        $currentDate = new DateTime();
        $createdAt = new DateTime($data['fecha_bloqueo']);
        $createdAt->modify('-12 hour');
        $difference = $currentDate->diff($createdAt);

        if ($difference->h < 24) { //s de segundo, h de horas, i de minutos
            $sql = 'UPDATE empleado set uuid_estado_empleado = ?
            WHERE uuid_empleado = ?';
            $params = array($this->estado_activo, $this->id);
            if ($data = Database::getRow($sql, $params)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /* 
    *   Método para comprobar que existen usuarios registrados en nuestra base de datos
    */

    // Método para leer toda la información de los usuarios registrados-------------------------.
    public function readAll()
    {
        $sql = 'SELECT uuid_empleado, nombres_empleado, apellidos_empleado, ce.cargo_empleado, estado_empleado, alias_empleado, correo_empleado, imagen_avatar, estado_empleado
        FROM empleado as e inner join cargo_empleado as ce USING(uuid_cargo_empleado)
		INNER JOIN avatar_empleado using (uuid_avatar)
        INNER JOIN estado_empleado using(uuid_estado_empleado)
		order by nombres_empleado, estado_empleado;';
        $params = null;
        return Database::getRows($sql, $params);
    }

    // Método para un dato en especifico de los usuarios registrados-------------------------.
    public function readOne()
    {
        $sql = 'SELECT "uuid_empleado", "nombres_empleado", "apellidos_empleado", "correo_empleado", "alias_empleado", "uuid_avatar", "imagen_avatar", "uuid_estado_empleado", "uuid_cargo_empleado"
        FROM empleado inner join avatar_empleado using(uuid_avatar)
        where "uuid_empleado" = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    /* Método para obtener un empleado y mostrarlo en modal de visualizar*/
    public function readOneShow()
    {
        $sql = 'SELECT "uuid_empleado", "nombres_empleado", "apellidos_empleado", "correo_empleado",  "alias_empleado", a."uuid_avatar", a."imagen_avatar", ce."uuid_cargo_empleado", ce."cargo_empleado", "estado_empleado", factor_autenticacion
        FROM empleado as e inner join "cargo_empleado" as ce on e."uuid_cargo_empleado" = ce."uuid_cargo_empleado"
		inner join "avatar_empleado" as a on e."uuid_avatar" = a."uuid_avatar"
        INNER JOIN "estado_empleado" using(uuid_estado_empleado)
        where "uuid_empleado" = ? ';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Método para reporte, filtrar según el empleado y en un rango de fecha
    public function readRowsReportDate($start, $end)
    {
        $sql = 'SELECT e."nombres_empleado", c."nombre_cliente", tv."tipo_venta", tf."tipo_factura", fecha_venta, ev."estado_venta", correlativo_venta
        FROM venta as v
        INNER JOIN empleado as e
        ON v.uuid_empleado = e.uuid_empleado
        INNER JOIN cliente as c
        ON v.uuid_cliente = c.uuid_cliente
        INNER JOIN tipo_venta as tv
        ON v.uuid_tipo_venta = tv.uuid_tipo_venta
        INNER JOIN tipo_factura as tf
        ON v.uuid_tipo_factura = tf.uuid_tipo_factura
        INNER JOIN estado_venta as ev
        ON v.uuid_estado_venta = ev.uuid_estado_venta
        WHERE v.uuid_empleado = ? AND fecha_venta BETWEEN ? AND ?   
        ORDER BY correlativo_venta ASC';
        $params = array($this->id, $start, $end);
        return Database::getRows($sql, $params);
    }

    // Método para gráficos
    public function ventasEmpleado($start, $end)
    {
        $sql = "SELECT fecha_venta, COUNT(uuid_venta) Ventas
        FROM empleado as e
        INNER JOIN venta as v
        ON e.uuid_empleado = v.uuid_empleado
        WHERE v.uuid_empleado = ? AND fecha_venta BETWEEN ? AND ?
        GROUP BY fecha_venta ORDER BY Ventas DESC";
        $params = array($this->id, $start, $end);
        return Database::getRows($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, delete).
    */
    /* SEARCH */
    public function searchRows($value)
    {
        $sql = 'SELECT "uuid_empleado", "nombres_empleado", "apellidos_empleado", "cargo_empleado", "estado_empleado", alias_empleado, correo_empleado, imagen_avatar
                        FROM empleado inner join cargo_empleado using(uuid_cargo_empleado)
                        INNER JOIN avatar_empleado using(uuid_avatar)
                        INNER JOIN estado_empleado using(uuid_estado_empleado)
                        WHERE "nombres_empleado" ILIKE ? OR "apellidos_empleado" ILIKE ?
                        order by "uuid_empleado", estado_empleado';
        $params = array("%$value%", "%$value%");
        return Database::getRows($sql, $params);
    }

    /* CREATE */
    public function createRow()
    {
        $sql = 'INSERT INTO empleado(nombres_empleado, apellidos_empleado, correo_empleado, alias_empleado, contrasena_empleado, uuid_avatar, uuid_cargo_empleado, uuid_estado_empleado)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?);';
        $params = array($this->nombres, $this->apellidos, $this->correo, $this->alias, $this->clave, $this->foto, $this->cargo, $this->estado);
        return Database::executeRow($sql, $params);
    }


    /* UPDATE */
    public function updateRow()
    {
        $sql = 'UPDATE empleado
                SET "nombres_empleado" = ?, "apellidos_empleado" = ?, "correo_empleado" = ?, "uuid_avatar" = ?, "uuid_cargo_empleado" = ?, "uuid_estado_empleado" = ?
                WHERE "uuid_empleado" = ?';
        $params = array($this->nombres, $this->apellidos, $this->correo, $this->foto, $this->cargo, $this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }

    // Update perfil del empleado
    public function updatePerfil()
    {
        $sql = 'UPDATE empleado
                SET "nombres_empleado" = ?, "apellidos_empleado" = ?, "alias_empleado" = ?, "uuid_avatar" = ?, "factor_autenticacion" = ?
                WHERE "uuid_empleado" = ?';
        $params = array($this->nombres, $this->apellidos, $this->alias, $this->foto, $this->dobleAutenticacion, $this->id);
        return Database::executeRow($sql, $params);
    }

    /* DELETE */
    /* Función para inhabilitar un usuario ya que no los borraremos de la base------------------------- */
    public function deleteRow()
    {
        //No eliminaremos registros, solo los inhabilitaremos-------------------------.
        $sql = "UPDATE empleado SET uuid_estado_empleado = (SELECT uuid_estado_empleado FROM estado_empleado WHERE estado_empleado = 'Inhabilitado') WHERE uuid_empleado = ?"; //Delete from empleado where "uuid_empleado" = ? -------------------------.
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function changeRow()
    {
        if ($this->num == 1) {
            $this->estado = 1;
        } else {
            $this->estado = 0;
        }
        $sql = 'UPDATE empleado SET "estado_empleado" = ? WHERE "uuid_empleado" = ?';
        $params = array($this->estado, $this->id);
        return Database::executeRow($sql, $params);
    }


    //Métodos para seguridad web
    //Método para insertar código de verificación de dos pasos
    public function insertToken($token)
    {
        $sql = "SELECT token FROM autenticacion WHERE uuid_empleado = ?";
        $params = array($this->id);
        if ($data = Database::getRow($sql, $params)) {
            $sql = "UPDATE autenticacion
            SET token=?, fecha_token=CURRENT_TIMESTAMP
            WHERE uuid_empleado = ? RETURNING token;";
            $params = array($token, $this->id);
            if ($_SESSION['verification_token'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        } else {
            $sql = "INSERT INTO autenticacion(token, fecha_token, uuid_empleado)
            VALUES (?, CURRENT_TIMESTAMP, ?) RETURNING token;";
            $params = array($token, $this->id);
            // Se obtiene el ultimo valor insertado en la llave primaria de la tabla pedidos.
            if ($_SESSION['verification_token'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function reInsertToken($token)
    {
        //Reenvio de correo de verificación
        $sql = "SELECT token FROM autenticacion WHERE uuid_empleado = ?";
        $params = array($_SESSION['uuid_empleado_verification']);
        if ($data = Database::getRow($sql, $params)) {
            $sql = "UPDATE autenticacion
            SET token=?, fecha_token=CURRENT_TIMESTAMP
            WHERE uuid_empleado = ? RETURNING token;";
            $params = array($token, $_SESSION['uuid_empleado_verification']);
            if ($_SESSION['verification_token'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        } else {
            $sql = "INSERT INTO autenticacion(token, fecha_token, uuid_empleado)
            VALUES (?, CURRENT_TIMESTAMP, ?) RETURNING token;";
            $params = array($token, $_SESSION['uuid_empleado_verification']);
            // Se guarda el valor del token.
            if ($_SESSION['verification_token'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }

         // Método para registrar inicios de sesiones-------------------------.
    public function createSessionInfo($ip, $dispositivo, $ciudad)
    {
        $sql = "INSERT INTO sesion(
            dispositivo, fecha_sesion, ip_sesion, ciudad, uuid_empleado)
            VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?);";
        $params = array($dispositivo, $ip, $ciudad, $_SESSION['uuid_empleado_verification']);
        return Database::executeRow($sql, $params);
            
    }

    //Método para verificar si ya existe una sesión de ese dispositivo con esa ip
    public function checkExistingSession($ip, $dispositivo, $ciudad)
    {
        $sql = 'SELECT COUNT(*) 
        FROM sesion 
        WHERE ip_sesion = ? AND dispositivo = ? AND uuid_empleado = ?';
        //echo $_SESSION['uuid_empleado_verification'];
        $params = array($ip, $dispositivo, $_SESSION['uuid_empleado_verification']);
        if(Database::registerExist($sql, $params)){
            return false;
        } else {
            $sql = "INSERT INTO sesion(
                dispositivo, fecha_sesion, ip_sesion, ciudad, uuid_empleado)
                VALUES (?, CURRENT_TIMESTAMP, ?, ?, ?);";
            $params = array($dispositivo, $ip, $ciudad, $_SESSION['uuid_empleado_verification']);
            return Database::executeRow($sql, $params);
        }
    }
    public function reInsertToken2($codigo)
    {
        $sql = "SELECT codigo FROM restaurar_contrasena WHERE correo_empleado = ?";
        $params = array($_SESSION['uuid_correo_empleado']);
        if ($data = Database::getRow($sql, $params)) {
            $sql = "UPDATE restaurar_contrasena
            SET codigo=?, fecha_codigo=CURRENT_TIMESTAMP
            WHERE correo_empleado = ? RETURNING codigo;";
            $params = array($codigo, $_SESSION['uuid_correo_empleado']);
            if ($_SESSION['verification_token'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        } else {
            $sql = "INSERT INTO restaurar_contrasena(codigo, fecha_codigo, correo_empleado)
            VALUES (?, CURRENT_TIMESTAMP, ?) RETURNING codigo;";
            $params = array($codigo, $_SESSION['uuid_correo_empleado']);
            // Se obtiene el ultimo valor insertado en la llave primaria de la tabla pedidos.
            if ($_SESSION['verification_token'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }



    public function registerCambioFecha()
    {
        $sql = "INSERT INTO public.cambio_contrasena(fecha_actual, fecha_dos, contrasena_actual, uuid_empleado)
                VALUES (CURRENT_DATE, CURRENT_DATE + 90, ?, (SELECT uuid_empleado FROM empleado WHERE contrasena_empleado = ?));";
        $params = array($this->clave, $this->clave);
        return Database::executeRow($sql, $params);
    }

    public function checkDatePassword()
    {
        $sql = "SELECT fecha_dos FROM cambio_contrasena WHERE uuid_empleado = ?;";
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    // Función para controlar que cuando se cree el primer usuario agende la fecha de cambio de contraseña
    public function renovarContrasenaFirstUser()
    {
        $sql = "INSERT INTO public.cambio_contrasena(
                fecha_actual, fecha_dos, contrasena_actual, uuid_empleado)
                VALUES (CURRENT_DATE, CURRENT_DATE + 90, ?, (SELECT uuid_empleado FROM empleado WHERE contrasena_empleado = ?));";
        $params = array($this->clave, $this->clave);
        return Database::executeRow($sql, $params);
    }

    // (Inicio) Funciones para agendar la nueva fecha de cambio de contraseña en el caso que hayan pasado los 90 días
    public function renovarContrasena1()
    {
        $sql = "UPDATE empleado
                SET contrasena_empleado = ?
                WHERE uuid_empleado = ?;";
        $params = array($this->clave, $_SESSION['uuid_empleado_renew']);
        return Database::executeRow($sql, $params);
    }

    public function renovarContrasena2()
    {
        $sql = "UPDATE cambio_contrasena
                SET contrasena_actual = ?, fecha_actual = CURRENT_DATE, fecha_dos = CURRENT_DATE + 90
                WHERE uuid_empleado = ?;";
        $params = array($this->clave, $_SESSION['uuid_empleado_renew']);
        return Database::executeRow($sql, $params);
    }
    // (Fin) Funciones para agendar la nueva fecha de cambio de contraseña en el caso que hayan pasado los 90 días

    // (Inicio) Funciones para agendar la nueva fecha de cambio de contraseña en el caso que se haya recuperado al contraseña
    public function renovarContrasenaRecu1()
    {
        $sql = "UPDATE empleado
                SET contrasena_empleado = ?
                WHERE uuid_empleado = ?;";
        $params = array($this->clave, $_SESSION['uuid_empleado_recover']);
        return Database::executeRow($sql, $params);
    }

    public function renovarContrasenaRecu2()
    {
        $sql = "UPDATE cambio_contrasena
                SET contrasena_actual = ?, fecha_actual = CURRENT_DATE, fecha_dos = CURRENT_DATE + 90
                WHERE uuid_empleado = ?;";
        $params = array($this->clave, $_SESSION['uuid_empleado_recover']);
        return Database::executeRow($sql, $params);
    }
    // (Fin) Funciones para agendar la nueva fecha de cambio de contraseña en el caso que se haya recuperado al contraseña

    // (Inicio) Funciones para agendar la nueva fecha de cambio de contraseña en el caso que el empleado haya decidido cambiar
    // su contraseña desde editar perfil
    public function renovarContrasenaPerfil1()
    {
        $sql = "UPDATE empleado
                SET contrasena_empleado = ?
                WHERE uuid_empleado = ?;";
        $params = array($this->clave, $_SESSION['uuid_empleado']);
        return Database::executeRow($sql, $params);
    }

    public function renovarContrasenaPerfil2()
    {
        $sql = "UPDATE cambio_contrasena
                SET contrasena_actual = ?, fecha_actual = CURRENT_DATE, fecha_dos = CURRENT_DATE + 90
                WHERE uuid_empleado = ?;";
        $params = array($this->clave, $_SESSION['uuid_empleado']);
        return Database::executeRow($sql, $params);
    }
    // (Fin) Funciones para agendar la nueva fecha de cambio de contraseña en el caso que el empleado haya decidido cambiar
    // su contraseña desde editar perfil

        // Update perfil del empleado
        public function readEmail($correoRecu)
        {
            $sql = 'SELECT uuid_empleado, correo_empleado FROM empleado WHERE correo_empleado = ?';
            $params = array($correoRecu);

            if ($data = Database::getRow($sql, $params)) {
                $this->id = $data['uuid_empleado'];
                return true;
            } else {
                return false;
            }
        }
    //Metodo para verificar el codigo de recuperar contra
        public function verifyCode($code)
        {
            $sql = 'SELECT codigo FROM restaurar_contrasena WHERE codigo = ?';
            $params = array($code);
            return Database::getRow($sql, $params);
        }

        public function verifyCode2 ($code)
        {
            $sql = 'SELECT codigo FROM restaurar_contrasena WHERE codigo = ?';
            $params = array($code);
            return Database::getRow($sql, $params);
        }
        //Metodo para el codigo 
        public function insertCodigo($codigo)
    {
        $sql = "SELECT codigo FROM restaurar_contrasena WHERE correo_empleado = ?";
        $params = array($this->correo);
        if ($data = Database::getRow($sql, $params)) {
            $sql = "UPDATE restaurar_contrasena
            SET codigo=?, fecha_codigo=CURRENT_TIMESTAMP
            WHERE correo_empleado = ? RETURNING codigo;";
            $params = array($codigo, $this->correo);
            if ($_SESSION['verification_token'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        } else {
            $sql = "INSERT INTO restaurar_contrasena(codigo, fecha_codigo, correo_empleado)
            VALUES (?, CURRENT_TIMESTAMP, ?) RETURNING codigo;";
            $params = array($codigo, $this->correo);
            // Se obtiene el ultimo valor insertado en la llave primaria de la tabla pedidos.
            if ($_SESSION['verification_token'] = Database::getRowId($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }

    //Metodo para el tiempo de verificacion del codigo de la contraseña
    public function checkTimeVerificationCodec()
    {   //Zona horaria
        ini_set('date.timezone', 'America/El_Salvador');
        $sql = "SELECT to_char(fecha_codigo,'YYYY:MM:DD HH:MI:SS') as fecha_codigo FROM restaurar_contrasena WHERE correo_empleado = ?";
        $params = array($_SESSION['correo_empleado']);
        $data = Database::getRow($sql, $params);

        $currentDate = new DateTime();
        $createdAt = new DateTime($data['fecha_codigo']);
        //Le restamos 12 horas para que el formato sea el mismo
        $createdAt->modify('-12 hour');
        //Calculamos la diferencia
        $difference = $currentDate->diff($createdAt);

        if ($difference->i < 2) { //s de segundo, h de horas, i de minutos
            return true;
        } else {
            return false;
        }
    }

    //Método para restaurar la contraseña
    public function restaurarContrasena() {
        $sql = "UPDATE empleado
                SET contrasena_empleado = ?
                WHERE correo_empleado = ?;";
        $params = array($this->clave, $_SESSION['correo_empleado']);
        return Database::executeRow($sql, $params);
    }

    //Metodo para evitar que la contraseña contenha el nombre, apellido y alias
    public function getInfoRestaurarContraseña() {
        $sql = "SELECT uuid_empleado, nombres_empleado, apellidos_empleado, alias_empleado, contrasena_empleado from empleado WHERE correo_empleado = ?;";
        $params = array($_SESSION['correo_empleado']);
        if ($data = Database::getRow($sql, $params)) {
            $this->nombres = $data['nombres_empleado'];
            $this->apellidos = $data['apellidos_empleado'];
            $this->alias = $data['alias_empleado'];
            $this->id = $data['uuid_empleado'];
            $this->clave = $data['contrasena_empleado'];
            return true;
        } else {
            return false;
        }
    }


    // Método para comprobar si el empleado tiene activada la doble autenticación-------------------------.
    public function checkVerificationState()
    {
        $sql = "SELECT uuid_empleado from empleado where uuid_empleado = ? and factor_autenticacion = 'true';";
        $params = array($this->id);

        if (Database::getRow($sql, $params)) {
            return true;
        } else {
            return false;
        }
    }

    public function getInfoUserByMail($id){
        $sql = 'SELECT uuid_empleado, uuid_estado_empleado, correo_empleado, nombres_empleado, apellidos_empleado, alias_empleado, imagen_avatar
        FROM empleado inner join avatar_empleado using (uuid_avatar) 
        WHERE uuid_empleado = ?';
        $params = array($id);
        if ($data = Database::getRow($sql, $params)) {
            $this->id = $data['uuid_empleado'];
            $this->estado = $data['uuid_estado_empleado'];
            $this->correo = $data['correo_empleado'];
            $this->nombres = $data['nombres_empleado'];
            $this->apellidos = $data['apellidos_empleado'];
            $this->alias = $data['alias_empleado'];
            $this->foto = $data['imagen_avatar'];
            return true;
        } else {
            return false;
        }
    }

    public function getLevelUser($id){
        $sql = 'SELECT cargo_empleado FROM empleado INNER JOIN cargo_empleado USING(uuid_cargo_empleado)
        WHERE uuid_empleado = ?;';
        $params = array($id);
        if ($data = Database::getRow($sql, $params)) {
            $this->cargo = $data['cargo_empleado'];
            return true;
        } else {
            return false;
        }
    }
}
