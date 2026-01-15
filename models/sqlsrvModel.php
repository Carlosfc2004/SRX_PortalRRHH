<?php

//Incluimos la libreria de PHPMailer para el envio de correos
@include_once('phpmailer/Exception.php');
@include_once('phpmailer/PHPMailer.php');
@include_once('phpmailer/SMTP.php');
@include_once('phpmailer/OAuth.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;



date_default_timezone_set('Europe/Madrid');
//Modelo sqlsrvModel.php
class sqlsrvModel
{
    protected $conn;

    //Función para conectar con la base de datos de la aplicación web
    public function ConectarAppReclutamiento()
    {
        try {
            $connectionInfo = array("Database" => ConfigAppReclutamiento::$bdsrx_nombre, "UID" => ConfigAppReclutamiento::$bdsrx_usuario, "PWD" => ConfigAppReclutamiento::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigAppReclutamiento::$bdsrx_hostname, $connectionInfo);
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                return $conn;
            }
        } catch (Exception $e) {
            echo ("Error!");
        }
    }



    //Función para conectar con la base de datos de la aplicación web (Mulesoft)
    public function conectarMuleSoft()
    {
        try {
            $connectionInfo = array("Database" => ConfigMuleSoft::$bdsrx_nombre, "UID" => ConfigMuleSoft::$bdsrx_usuario, "PWD" => ConfigMuleSoft::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigMuleSoft::$bdsrx_hostname, $connectionInfo);
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                return $conn;
            }
        } catch (Exception $e) {
            echo ("Error!");
        }
    }



    //Función para conectar con la base de datos de Mantenimiento (SistemaMantenimiento)
    public function conectarMante()
    {
        try {
            $connectionInfo = array("Database" => ConfigMante::$bdsrx_nombre, "UID" => ConfigMante::$bdsrx_usuario, "PWD" => ConfigMante::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigMante::$bdsrx_hostname, $connectionInfo);
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                return $conn;
            }
        } catch (Exception $e) {
            echo ("Error!");
        }
    }



    //Función para conectar con la base de datos de Portal del Empleado
    public function conectarEmpleado()
    {
        try {
            $connectionInfo = array("Database" => ConfigPortalEmpleado::$bdsrx_nombre, "UID" => ConfigPortalEmpleado::$bdsrx_usuario, "PWD" => ConfigPortalEmpleado::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigPortalEmpleado::$bdsrx_hostname, $connectionInfo);
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                return $conn;
            }
        } catch (Exception $e) {
            echo ("Error: " . $e->getMessage());
        }
    }



    // Función para conectar con la base de datos de DATASPHERE
    public function conectarDatasphere()
    {
        try {
            $connectionInfo = array("Database" => ConfigDatasphere::$bdsrx_nombre, "UID" => ConfigDatasphere::$bdsrx_usuario, "PWD" => ConfigDatasphere::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigDatasphere::$bdsrx_hostname, $connectionInfo);
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                return $conn;
            }
        } catch (Exception $e) {
            echo ("Error: " . $e->getMessage());
        }
    }



    // Función para conectar con la base de datos de SistemaProduccion
    public function conectarProduccion()
    {
        try {
            $connectionInfo = array("Database" => ConfigProduccion::$bdsrx_nombre, "UID" => ConfigProduccion::$bdsrx_usuario, "PWD" => ConfigProduccion::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigProduccion::$bdsrx_hostname, $connectionInfo);
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                return $conn;
            }
        } catch (Exception $e) {
            echo ("Error: " . $e->getMessage());
        }
    }



    //Función para conectar con la base de datos de 192.168.200.202
    public function conectarWebApp()
    {
        try {
            $connectionInfo = array("Database" => ConfigWebApp::$bdsrx_nombre, "UID" => ConfigWebApp::$bdsrx_usuario, "PWD" => ConfigWebApp::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigWebApp::$bdsrx_hostname, $connectionInfo);
            if ($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            } else {
                return $conn;
            }
        } catch (Exception $e) {
            echo ("Error: " . $e->getMessage());
        }
    }



    function FormatErrors($errors)
    {
        /* Display SQL error messages */
        echo "Error information: ";

        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
            echo "Code: " . $error['code'] . "<br />";
            echo "Message: " . $error['message'] . "<br />";
        }
    }



    // Funcion para el menu del portal
    public function menuPortal()
    {
        $conn = $this->conectarWebApp();
        $sql = "SELECT * FROM [" . ConfigWebApp::$bdsrx_nombre . "].[dbo].[webphp_menu_apps] WHERE app = 'portal_rrhh' AND estado = 1 ORDER BY id_padre, ord";
        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        }

        $menu_data = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            if ($row['id_padre'] == 0) {
                $menu_data[$row['id_hijo']] = $row;
            } else {
                $menu_data[$row['id_padre']]['children'][$row['id_hijo']] = $row;
            }
        }

        return $menu_data;
    }



    //Comprobamos los datos de acceso a los usuarios al ingresar al sistema

    public function loginUser($user)
    {
        $conn = $this->ConectarWebApp();
        $sql = "SELECT * 
                FROM webphp_Usuarios 
                WHERE usr_login='" . $user . "' 
                AND (elim = 0)";
        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        } else {
            if ((sqlsrv_num_rows($consulta) == 0) or (is_null($consulta))) {
                return false;
            } else {
                $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                return $row;
            }
        }
    }



    //Insertamos una acceso de un usuario
    public function AccesoUser($id_usuario, $login)
    {
        $conn = $this->ConectarAppReclutamiento();
        //Insertamos el acceso
        $sql = "insert into webphp_Accesos (id_usuario, login, fecha) values ('" . $id_usuario . "','" . $login . "','" . date("Y-m-d H:i:s") . "')";
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta == TRUE) {
            return true;
        } else {
            return false;
        }
    }



    // Registro de acciones en la web
    public function reg_acciones($accion, $referencia, $id_usuario, $estado)
    {
        $conn = $this->ConectarAppReclutamiento();

        $fecha_accion = date("Y-m-d H:i:s");

        //Insertamos el acceso
        $sql = "insert into webphp_auditor (accion, fecha_accion, referencia, id_usuario, estado) values ('" . $accion . "', '" . $fecha_accion . "', '" . $referencia . "', '" . $id_usuario . "', '" . $estado . "')";

        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta == TRUE) {
            return true;
        } else {
            return false;
        }
    }



    //Datos trabajadores sin respuesta en 15 días
    public function total_trabajadores_sinrespuesta()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "WITH UltimosRegistros AS (
                    SELECT 
                        ID,
                        PERNR,
                        FECHA_REGISTRO,
                        ID_REGISTRO_RELACION,
                        ESTADO,
                        NUM_ENVIO,
                        elim
                    FROM webphp_registros_llamamientos
                    WHERE NUM_ENVIO = 2 
                    AND elim IS NULL
                ),
                RegistrosConHijos AS (
                    SELECT DISTINCT ID_REGISTRO_RELACION
                    FROM webphp_registros_llamamientos
                    WHERE ID_REGISTRO_RELACION IS NOT NULL
                    AND elim IS NULL
                ),
                UltimoEstadoPorTrabajador AS (
                    SELECT PERNR, ESTADO
                    FROM (
                        SELECT 
                            PERNR,
                            ESTADO,
                            FECHA_REGISTRO,
                            ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY FECHA_REGISTRO DESC) AS rn
                        FROM webphp_registros_llamamientos
                        WHERE elim IS NULL
                    ) AS sub
                    WHERE rn = 1
                )

                SELECT COUNT(*) AS TotalRegistrosSinRespuesta
                FROM UltimosRegistros ur
                LEFT JOIN RegistrosConHijos rch 
                    ON ur.ID = rch.ID_REGISTRO_RELACION
                LEFT JOIN UltimoEstadoPorTrabajador ult 
                    ON ur.PERNR = ult.PERNR
                WHERE ur.FECHA_REGISTRO <= DATEADD(DAY, -5, GETDATE())
                AND ur.FECHA_REGISTRO >= DATEADD(MONTH, -1, GETDATE())
                AND ur.ESTADO IN (0, 3)
                AND ur.ID_REGISTRO_RELACION IS NULL
                AND rch.ID_REGISTRO_RELACION IS NULL
                AND (ult.ESTADO != 2);";



        $consulta = sqlsrv_query($conn, $sql);


        if ($consulta === FALSE) {
            die(print_r(sqlsrv_errors(), true));
        }

        $resultado = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);


        if ($resultado) {
            return $resultado['TotalRegistrosSinRespuesta'];
        } else {
            return 0;
        }
    }



    //Trabajadores aceptados en baja total
    public function total_aceptados_baja_total()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "WITH UltimoRegistro AS (
                    SELECT 
                        pa.PERNR,
                        pa.STAT2,
                        ROW_NUMBER() OVER (PARTITION BY pa.PERNR ORDER BY pa.ID DESC) AS rn
                    FROM 
                        [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0000] pa
                )
                SELECT COUNT(DISTINCT pa.PERNR) AS total_aceptados_baja
                FROM 
                    UltimoRegistro pa
                JOIN 
                    [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] wl 
                    ON pa.PERNR = wl.PERNR
                WHERE 
                    pa.rn = 1
                    AND pa.STAT2 = 0
                    AND wl.ESTADO = 1
                    AND wl.ID_REMESA = (
                        SELECT MAX(ID_REMESA)
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] 
                        WHERE PERNR = wl.PERNR
                        AND ANO_REMESA = ( 
                            SELECT MAX(ANO_REMESA)
                            FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos]
                            WHERE PERNR = wl.PERNR
                        )
                    )
                    AND wl.elim IS NULL";

        $consulta = sqlsrv_query($conn, $sql);


        if ($consulta === FALSE) {
            die(print_r(sqlsrv_errors(), true));
        }

        $resultado = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);


        if ($resultado) {
            return $resultado['total_aceptados_baja'];
        } else {
            return 0;
        }
    }



    //Trabajadores aceptados en baja Datos para la lista
    public function total_aceptados_baja()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG; 

                WITH UltimoRegistro AS (
                    SELECT 
                        pa.PERNR,
                        pa.STAT2,
                        ROW_NUMBER() OVER (PARTITION BY pa.PERNR ORDER BY pa.ID DESC) AS rn
                    FROM 
                        [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0000] pa
                )
                SELECT 
                    pa.PERNR,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.NOMBRE))) AS NOMBRE,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.APELLIDO1))) AS APELLIDO1,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.APELLIDO2))) AS APELLIDO2,
                    wl.ID_REMESA,
                    wl.ANO_REMESA,
	                rl.nombre_remesa
                FROM 
                    UltimoRegistro pa
                JOIN 
                    [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] wl 
                    ON pa.PERNR = wl.PERNR
                JOIN 
                     [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos] rl 
                    ON pa.PERNR = rl.PERNR
                JOIN 
                    [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0002] pa2
                    ON pa2.PERNR = pa.PERNR
                WHERE 
                    pa.rn = 1
                    AND pa.STAT2 = '0' 
                    AND wl.ESTADO = 1  
                    AND wl.ID_REMESA = (
                        SELECT MAX(ID_REMESA)
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] 
                        WHERE PERNR = wl.PERNR 
                        AND ANO_REMESA = ( 
                            SELECT MAX(ANO_REMESA)
                            FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos]
                            WHERE PERNR = wl.PERNR
                        )
                    )
                    AND wl.elim IS NULL
                GROUP BY pa.PERNR, pa2.NOMBREYAPELLIDOS, pa2.NOMBRE, pa2.APELLIDO1, pa2.APELLIDO2, wl.ID_REMESA, wl.ANO_REMESA, rl.nombre_remesa;

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Cumpleaños de trabajador
    public function cumple_trabajador()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;  

                    SELECT 
                        p.PERNR,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.FECHANACIMIENTO))) AS FECHANACIMIENTO 
                    FROM PA0002 p
                    INNER JOIN (
                        SELECT PERNR
                        FROM PA_ACTIVOS a1
                        WHERE STAT2 = 3
                        AND ZZWERKS = '1000'
                    ) act ON p.PERNR = act.PERNR
                    WHERE 
                        MONTH(CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.FECHANACIMIENTO)))) = MONTH(GETDATE())
                        AND DAY(CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.FECHANACIMIENTO)))) = DAY(GETDATE())
                    GROUP BY p.PERNR, p.NOMBREYAPELLIDOS, p.FECHANACIMIENTO
                    ORDER BY FECHANACIMIENTO DESC

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos opciones sociedades
    public function dni_caducados()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;

                    SELECT a.id, a.pernr, a.tipo_doc, a.fecha_validez, CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(b.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                        CASE 
                            WHEN fecha_validez < GETDATE() THEN 'Expirado'
                            ELSE 'Expira pronto'
                        END as estado
                    FROM MAESTRO_FV_DNI a
                    LEFT JOIN pa0002 b ON a.pernr = b.PERNR
                    WHERE fecha_validez < GETDATE()
                        OR (fecha_validez BETWEEN GETDATE() AND DATEADD(month, 1, GETDATE()))
                    ORDER BY fecha_validez ASC

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Trabajadores en una remesa sin llamamientos realizados
    public function trabajadores_sinllamamiento()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "
            OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
            DECRYPTION BY CERTIFICATE CertificadoPA_REG;

            SELECT 
                r.id_remesa,
                YEAR(r.fecha_remesa) as ano_remesa,
                r.nombre_remesa,
                r.PERNR,
                CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.NOMBRE))) AS NOMBRE,
                CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.APELLIDO1))) AS APELLIDO1,
                CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.APELLIDO2))) AS APELLIDO2
            FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos] r
            LEFT JOIN [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] l
                ON r.PERNR = l.PERNR
            LEFT JOIN pa0002 pa2 ON r.PERNR = pa2.PERNR
            WHERE r.sms_auto = 0
            AND l.PERNR IS NULL
            AND r.elim IS NULL; 

            CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;  ";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //total contrataciones mensuales
    public function total_contrataciones_mensuales()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT 
                    YEAR(BEGDA) AS Ano,
                    MONTH(BEGDA) AS Mes,
                    COUNT(DISTINCT PERNR) AS Total
                FROM 
                    [PA0000]
                WHERE 
                    STAT2 = 3
                    AND BEGDA >= DATEADD(YEAR, -1, GETDATE())
                GROUP BY 
                    YEAR(BEGDA), MONTH(BEGDA)
                ORDER BY 
                    Ano, Mes;";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //total trabajadores por sociedad
    public function sociedad_trabajador()
    {
        $conn = $this->conectarMuleSoft();

        // SQL query
        $sql = "SELECT ZZWERKS, DESC_CENTRO, 
                (SELECT COUNT(DISTINCT(PERNR)) FROM [PA0001] AS T2 
                WHERE T2.ZZWERKS = T1.ZZWERKS) AS CANTIDAD
                FROM [PA0001] AS T1
                WHERE DESC_CENTRO != '' and ZZWERKS IN (1000,1700,2000)
                GROUP BY ZZWERKS, DESC_CENTRO ORDER BY ZZWERKS";

        // Execute query
        $consulta = sqlsrv_query($conn, $sql);

        // Check for SQL execution errors
        if ($consulta === false) {
            // Display raw SQL errors for debugging
            die(print_r(sqlsrv_errors(), true));
        }

        $resultado = array();

        // Fetch results if no errors
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Trabajadores activos por sociedad()
    public function trabajadoresActivos()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT ZZWERKS, DESC_CENTRO, STAT2, COUNT(PERNR) as emp_activos 
                FROM PA_ACTIVOS 
                --WHERE STAT2 = 3 and ZZWERKS IN (1000,1700,2000)
                GROUP BY ZZWERKS, DESC_CENTRO, STAT2
                ORDER BY ZZWERKS";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            if ($row['STAT2'] == '3' && ($row['ZZWERKS'] == '1000' || $row['ZZWERKS'] == '1700' || $row['ZZWERKS'] == '2000')) {
                $resultado[] = $row;
            }
        }
        return $resultado;
    }



    //Datos opciones sociedades
    public function Sociedades_graf()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT DISTINCT(ZZWERKS), DESC_CENTRO
                FROM PA0001
                WHERE ZZWERKS = '1000'
                    OR ZZWERKS = '1700'
                OR ZZWERKS = '2000'
                ORDER BY ZZWERKS ASC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }




    public function buscarTrabajadoresSap($txt_pernr, $txt_nombre, $sociedad, $baja)
    {
        $conn = $this->conectarDatasphere();
        $sql = "EXEC [dbo].[RRHH_SP_personas_activas]
		        @txt_pernr = N'$txt_pernr'";
        if ($txt_nombre != '') {
            $sql .= ", @txt_nombre = N'$txt_nombre'";
        }
        if ($sociedad != '') {
            $sql .= ", @sociedad = N'$sociedad'";
        }
        if ($baja != '') {
            $sql .= ", @baja = N'$baja'";
        }
        $sql .= ";";

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos opciones sociedades
    public function Sociedades()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT DISTINCT(ZZWERKS), DESC_CENTRO
                FROM PA0001
                WHERE DESC_CENTRO!=''
                ORDER BY ZZWERKS ASC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Obtenemos todos los datos de un trabajador SAP
    public function info_trabajador($PERNR)
    {
        $conn = $this->conectarDatasphere();

        // Consulta SQL con parámetros
        $sql = "EXEC [dbo].[RRHH_SP_PA0002]
                @PERNR = N'$PERNR';";

        // Usar parámetros para prevenir inyección de SQL
        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === FALSE) {
            echo $sql; // Imprimir la consulta para depuración
            die($this->FormatErrors(sqlsrv_errors())); // Manejo de errores
        } else {
            // Verificar el número de filas
            if (sqlsrv_num_rows($consulta) === 0) {
                return []; // Devolver un array vacío si no hay resultados
            } else {
                $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                return $row; // Devolver el primer resultado
            }
        }
    }



    //Datos contacto trabajador
    public function datos_contacto_trabajador($PERNR)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT TOP 1
                    PERNR,
                    LOWER(CORREO) AS CORREO,
                    MOVIL,
                    TELEMPRESA,
                    TELEMERGENCIAS,
                    TIPO,
                    PRE_TELF,
                    PRE_TELF_EMP,
                    PRE_TELF_EMER,
                    PARENT_TELF,
                    PARENT_TELF_EMP,
                    PARENT_TELF_EMER
                FROM PA0105
                WHERE PERNR = $PERNR
                AND (
                        NULLIF(LTRIM(RTRIM(MOVIL)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(CORREO)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(TELEMPRESA)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(TELEMERGENCIAS)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(TIPO)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(PRE_TELF)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(PRE_TELF_EMP)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(PRE_TELF_EMER)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(PARENT_TELF)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(PARENT_TELF_EMP)), '') IS NOT NULL OR
                        NULLIF(LTRIM(RTRIM(PARENT_TELF_EMER)), '') IS NOT NULL
                )
                ORDER BY FECHA_IN DESC;";

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos prefijos
    public function datos_prefijos()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT [id]
                      ,[nombre]
                      ,[prefijo]
                FROM [webphp_prefijos]";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos parentesco para el numero telefono del trabajador
    public function datos_parentesco()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT [ID], [PARENTESCO] FROM [webphp_parentesco]";

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();

        if ($consulta == FALSE) // Verifica errores en la consulta
            die($this->FormatErrors(sqlsrv_errors()));

        // Recorre los resultados y los agrega al array
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }

        return $resultado; // Devuelve el array con los resultados
    }



    // Tipo de ausencias
    public function tipos_ausencias()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT [id], [valor] FROM [webphp_tipo_ausencias] WHERE estado = 1";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Trabajdores con solicitudes
    public function trabajadores_solicitudes()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;

                SELECT TOP (1000) 
                    wa.[pernr],
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS
                FROM [" . ConfigPortalEmpleado::$bdsrx_nombre . "].[dbo].[webphp_ausencias] wa
                LEFT JOIN pa0002 pa2 ON pa2.PERNR = wa.pernr
                GROUP BY wa.pernr, NOMBREYAPELLIDOS 

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";


        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        }
        $resultado = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $row['NOMBREYAPELLIDOS'] = ucwords(strtolower($row['NOMBREYAPELLIDOS']));
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Solicitudes de ausencias - Solo datos básicos para la lista (optimizado)
    public function solicitudes_lista($fecha_solic, $fecha_solic2, $pernr, $tipo_ausencia, $estado, $justificante)
    {
        $conn = $this->conectarMuleSoft();

        // Consulta optimizada: Solo campos necesarios para la tabla principal
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;
                SELECT TOP(200)
                    pe.id_solicitud,
                    pe.mail,
                    pe.mail_s,
                    pe.pernr,
                    pe.fecha_desde,
                    pe.fecha_hasta,
                    pe.fecha_solicitud,
                    pe.tipo,
                    pe.estado,
                    pe.motivo,
                    pe.justificante,  
                    pe.anio_origen, 
                    wu.NOMBREYAPELLIDOS AS nombre_trabajador,
                    wu_s.NOMBREYAPELLIDOS AS nombre_superior
                FROM [" . ConfigPortalEmpleado::$bdsrx_nombre . "].[dbo].[webphp_ausencias] pe
                LEFT JOIN [PA_ACTIVOS] wu 
                    ON pe.pernr = wu.pernr COLLATE Modern_Spanish_CI_AS
                LEFT JOIN [PA_ACTIVOS] wu_s
                    ON pe.pernr_s = wu_s.pernr COLLATE Modern_Spanish_CI_AS
                WHERE 1 = 1";
        if ($fecha_solic != '' && $fecha_solic2 != '') {
            $sql .= " AND pe.fecha_solicitud BETWEEN '$fecha_solic' AND '$fecha_solic2'";
        } elseif ($fecha_solic != '' && $fecha_solic2 == '') {
            $sql .= " AND pe.fecha_solicitud = '$fecha_solic'";
        }
        if ($pernr != '') {
            if (is_array($pernr)) {
                $sql .= " AND pe.pernr IN ('" . implode("','", $pernr) . "')";
            } else {
                $sql .= " AND pe.pernr IN ($pernr)";
            }
        }
        if ($tipo_ausencia != '') {
            $sql .= " AND pe.tipo = '$tipo_ausencia'";
        }
        if ($estado == '1') {
            $sql .= " AND (pe.firma_superior = '1' AND pe.estado = '$estado' OR pe.estado = '6' OR pe.estado = '7')";
        } elseif ($estado == '2') {
            $sql .= " AND (pe.firma_superior = '1' AND pe.estado = '$estado')";
        } elseif ($estado == '3') {
            $sql .= " AND (pe.firma_superior = '1' AND pe.estado = '$estado')";
        } elseif ($estado == '4') {
            $sql .= " AND (pe.firma_superior = '1' AND pe.estado = '$estado')";
        } elseif ($estado == '5') {
            $sql .= " AND pe.estado = '$estado'";
        } else {
            $sql .= " AND pe.estado IN ('1', '3', '4', '5', '6', '7', '8')";
        }
        if ($justificante == 'on') {
            $sql .= " AND pe.justificante != ''";
        }

        $sql .= " ORDER BY pe.id_solicitud ASC;
                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        }

        $resultado = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $row['nombre_trabajador'] = ucwords(strtolower($row['nombre_trabajador']));

            // Calcular días laborables según grupo horario del trabajador
            if (isset($row['fecha_desde']) && isset($row['fecha_hasta']) && isset($row['pernr'])) {
                $row['total_dias'] = $this->calcularDiasLaborables(
                    $row['pernr'],
                    $row['fecha_desde'],
                    $row['fecha_hasta']
                );
            } else {
                $row['total_dias'] = 0;
            }

            $resultado[] = $row;
        }
        return $resultado;
    }

    // Función para obtener días disponibles de vacaciones por año
    public function getDiasDisponiblesVacaciones($pernr, $anio_solicitud)
    {
        $conn = $this->conectarEmpleado();

        if (!$conn) {
            error_log("Error de conexión en getDiasDisponiblesVacaciones");
            return null;
        }

        // Usar parámetros preparados para evitar inyección SQL
        $sql = "EXEC [dbo].[sp_DiasDisponiblesVacaciones_años_proporcion2] @pernr = ?, @anio_solicitud = ?";
        $params = array($pernr, $anio_solicitud);

        $consulta = sqlsrv_query($conn, $sql, $params);

        if ($consulta === FALSE) {
            $errors = sqlsrv_errors();
            return null;
        }

        $resultado = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);

        return $resultado;
    }

    // Función para obtener días disponibles de ausencias por período y año
    public function getDiasDisponiblesAusencias($pernr, $id_ausencia, $periodo, $anio)
    {
        $conn = $this->conectarEmpleado();

        if (!$conn) {
            error_log("Error de conexión en getDiasDisponiblesAusencias");
            return null;
        }

        // Usar parámetros preparados para evitar inyección SQL
        $sql = "EXEC [dbo].[sp_contar_solicitudes_periodo_new] @pernr = ?, @id_ausencia = ?, @periodo = ?, @anio = ?";
        $params = array($pernr, $id_ausencia, $periodo, $anio);

        $consulta = sqlsrv_query($conn, $sql, $params);

        if ($consulta === FALSE) {
            $errors = sqlsrv_errors();
            error_log("Error al ejecutar sp_contar_solicitudes_periodo_new: " . print_r($errors, true));
            return null;
        }

        $resultado = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);

        return $resultado;
    }


    // Función para calcular días laborables considerando el grupo horario del trabajador
    public function calcularDiasLaborables($pernr, $fecha_desde, $fecha_hasta)
    {
        // Validación de parámetros
        if (!$pernr || !$fecha_desde || !$fecha_hasta) {
            return 0;
        }

        // Asegurar que las fechas son objetos DateTime
        if (!($fecha_desde instanceof DateTime)) {
            $fecha_desde = new DateTime($fecha_desde);
        }
        if (!($fecha_hasta instanceof DateTime)) {
            $fecha_hasta = new DateTime($fecha_hasta);
        }

        $conn = $this->ConectarAppReclutamiento();

        if (!$conn) {
            error_log("Error de conexión en calcularDiasLaborables");
            return $this->calcularDiasLaborablesSimple($fecha_desde, $fecha_hasta);
        }

        // Detectar si el rango abarca múltiples años
        $anio_inicio = (int) $fecha_desde->format('Y');
        $anio_fin = (int) $fecha_hasta->format('Y');
        $anios_involucrados = range($anio_inicio, $anio_fin);

        // Array para almacenar todos los grupos horarios por año
        $grupos_horarios = [];

        // 1. Para cada año involucrado, buscar el grupo horario correspondiente
        foreach ($anios_involucrados as $anio) {
            // Calcular el inicio y fin del año dentro del rango de fechas solicitado
            $inicio_anio = max($fecha_desde, new DateTime("$anio-01-01"));
            $fin_anio = min($fecha_hasta, new DateTime("$anio-12-31"));

            // Buscar el grupo horario asignado al trabajador para este año específico
            $sql_grupo = "SELECT gh.grupo_horario_id, gh.fecha_inicio, gh.fecha_fin
                          FROM webphp_trabajadores_grupos_horario gh
                          WHERE gh.pernr = ?
                            AND (
                                (gh.fecha_inicio <= ? AND gh.fecha_fin >= ?)
                                OR (gh.fecha_inicio <= ? AND gh.fecha_fin >= ?)
                                OR (gh.fecha_inicio >= ? AND gh.fecha_fin <= ?)
                            )
                          ORDER BY gh.fecha_asignacion DESC";

            $params_grupo = [
                $pernr,
                $inicio_anio->format('Y-m-d'),
                $inicio_anio->format('Y-m-d'),
                $fin_anio->format('Y-m-d'),
                $fin_anio->format('Y-m-d'),
                $inicio_anio->format('Y-m-d'),
                $fin_anio->format('Y-m-d')
            ];

            $consulta_grupo = sqlsrv_query($conn, $sql_grupo, $params_grupo);

            if ($consulta_grupo === false) {
                error_log("Error al obtener grupo horario para pernr $pernr en año $anio: " . print_r(sqlsrv_errors(), true));
                continue;
            }

            // Verificar si hay grupo asignado para este año
            $tiene_grupo_asignado = false;
            while ($grupo_asignado = sqlsrv_fetch_array($consulta_grupo, SQLSRV_FETCH_ASSOC)) {
                $grupos_horarios[] = $grupo_asignado['grupo_horario_id'];
                $tiene_grupo_asignado = true;
            }

            // Si no tiene grupo asignado para este año, buscar grupo predeterminado
            if (!$tiene_grupo_asignado) {
                $sql_predeterminado = "SELECT TOP 1 id FROM webphp_grupos_horarios WHERE grupo_predeterminado = 1 AND anio_configuracion = ?";
                $consulta_predeterminado = sqlsrv_query($conn, $sql_predeterminado, [$anio]);

                if ($consulta_predeterminado === false) {
                    error_log("Error al obtener grupo predeterminado para año $anio: " . print_r(sqlsrv_errors(), true));
                    continue;
                }

                $grupo_predeterminado = sqlsrv_fetch_array($consulta_predeterminado, SQLSRV_FETCH_ASSOC);
                if ($grupo_predeterminado) {
                    $grupos_horarios[] = $grupo_predeterminado['id'];
                }
            }
        }

        if (empty($grupos_horarios)) {
            error_log("ADVERTENCIA: No se encontró grupo horario para pernr $pernr. Usando cálculo simple (L-V).");
            sqlsrv_close($conn);
            return $this->calcularDiasLaborablesSimple($fecha_desde, $fecha_hasta);
        }

        // 2. Obtener todas las configuraciones de grupos horarios
        $todas_franjas = [];
        foreach ($grupos_horarios as $grupo_id) {
            $sql_config = "SELECT franjas_json FROM webphp_grupos_horarios WHERE id = ?";
            $consulta_config = sqlsrv_query($conn, $sql_config, [$grupo_id]);

            if ($consulta_config === false) {
                error_log("Error al obtener configuración de grupo $grupo_id: " . print_r(sqlsrv_errors(), true));
                continue;
            }

            $config = sqlsrv_fetch_array($consulta_config, SQLSRV_FETCH_ASSOC);

            if ($config && $config['franjas_json']) {
                $franjas = json_decode($config['franjas_json'], true);
                if ($franjas && is_array($franjas)) {
                    $todas_franjas = array_merge($todas_franjas, $franjas);
                }
            }
        }

        sqlsrv_close($conn);

        if (empty($todas_franjas)) {
            return $this->calcularDiasLaborablesSimple($fecha_desde, $fecha_hasta);
        }

        // 4. Crear un mapa de fechas festivas
        $festivos = [];
        foreach ($todas_franjas as $franja) {
            if (
                isset($franja['tipo_jornada']) &&
                (strpos($franja['tipo_jornada'], 'festivo') !== false)
            ) {
                $inicio = new DateTime($franja['inicio_fecha']);
                $fin = new DateTime($franja['fin_fecha']);

                while ($inicio <= $fin) {
                    $festivos[$inicio->format('Y-m-d')] = true;
                    $inicio->modify('+1 day');
                }
            }
        }

        // 5. Crear un mapa de días laborables por fecha
        $dias_laborables_por_fecha = [];
        foreach ($todas_franjas as $franja) {
            // Ignorar festivos en este recorrido
            if (
                isset($franja['tipo_jornada']) &&
                (strpos($franja['tipo_jornada'], 'festivo') !== false)
            ) {
                continue;
            }

            if (!isset($franja['inicio_fecha']) || !isset($franja['fin_fecha']) || !isset($franja['dias_semana'])) {
                continue;
            }

            $inicio = new DateTime($franja['inicio_fecha']);
            $fin = new DateTime($franja['fin_fecha']);
            $dias_semana = $franja['dias_semana'];

            while ($inicio <= $fin) {
                $fecha_str = $inicio->format('Y-m-d');
                $dia_semana = (int) $inicio->format('N'); // 1=Lunes, 7=Domingo

                // Si este día de la semana está en la configuración, es laborable
                if (in_array($dia_semana, $dias_semana)) {
                    $dias_laborables_por_fecha[$fecha_str] = true;
                }

                $inicio->modify('+1 day');
            }
        }

        // 6. Contar días laborables en el rango solicitado
        $total_dias = 0;
        $fecha_actual = clone $fecha_desde;

        while ($fecha_actual <= $fecha_hasta) {
            $fecha_str = $fecha_actual->format('Y-m-d');

            // Es laborable si está en el mapa de laborables Y NO es festivo
            if (isset($dias_laborables_por_fecha[$fecha_str]) && !isset($festivos[$fecha_str])) {
                $total_dias++;
            }

            $fecha_actual->modify('+1 day');
        }

        return $total_dias;
    }



    // Cálculo simple de días laborables (Lunes a Viernes)
    private function calcularDiasLaborablesSimple($fecha_desde, $fecha_hasta)
    {
        $total_dias = 0;
        $fecha_actual = clone $fecha_desde;

        while ($fecha_actual <= $fecha_hasta) {
            // Si el día no es sábado (6) ni domingo (7), contar como día laboral
            if ($fecha_actual->format('N') < 6) {
                $total_dias++;
            }
            $fecha_actual->modify('+1 day');
        }

        return $total_dias;
    }





    // Obtener detalles completos de una solicitud específica (para modal)
    public function solicitud_detalle($id_solicitud)
    {
        $conn = $this->conectarMuleSoft();

        // Obtener datos completos de la solicitud
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;
                SELECT 
                    wu.NOMBREYAPELLIDOS AS nombre,
                    wu_s.NOMBREYAPELLIDOS AS nombre_superior,
                    pe.*
                FROM [" . ConfigPortalEmpleado::$bdsrx_nombre . "].[dbo].[webphp_ausencias] pe
                LEFT JOIN [PA_ACTIVOS] wu 
                    ON pe.pernr = wu.pernr COLLATE Modern_Spanish_CI_AS
                LEFT JOIN [PA_ACTIVOS] wu_s 
                    ON pe.pernr_s = wu_s.pernr COLLATE Modern_Spanish_CI_AS
                WHERE pe.id_solicitud = '$id_solicitud'
                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";


        $params = array($id_solicitud);
        $consulta = sqlsrv_query($conn, $sql, $params);

        if ($consulta === FALSE) {
            return array('error' => $this->FormatErrors(sqlsrv_errors()));
        }

        $solicitud = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        $solicitud['nombre_superior'] = ucwords(strtolower($solicitud['nombre_superior']));

        if (!$solicitud) {
            return array('error' => 'Solicitud no encontrada');
        }

        // Obtener observaciones
        $sql_obs = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                    DECRYPTION BY CERTIFICATE CertificadoPA_REG;
                    SELECT id_solicitud_ausencia, wu.NOMBREYAPELLIDOS, pernr_solicitante, pernr_mod, comentario, fecha_modificacion, tipo_coment
                    FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_estado_solicitud_ausencias] sa
                    LEFT JOIN [PA_ACTIVOS] wu 
                        ON sa.pernr_mod = wu.pernr
                    WHERE id_solicitud_ausencia = ?
                    ORDER BY fecha_modificacion ASC
                    CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        $params_obs = array($id_solicitud);
        $consulta_obs = sqlsrv_query($conn, $sql_obs, $params_obs);

        $observaciones = array();
        while ($obs = sqlsrv_fetch_array($consulta_obs, SQLSRV_FETCH_ASSOC)) {
            $observaciones[] = array(
                'id_solicitud_ausencia' => $obs['id_solicitud_ausencia'],
                'nombre' => ucwords(strtolower($obs['NOMBREYAPELLIDOS'])),
                'pernr_solicitante' => $obs['pernr_solicitante'],
                'pernr_mod' => $obs['pernr_mod'],
                'comentario' => $obs['comentario'],
                'fecha_modificacion' => $obs['fecha_modificacion'],
                'tipo_coment' => $obs['tipo_coment'],
            );
        }

        $solicitud['observaciones'] = $observaciones;

        return $solicitud;
    }



    public function getotrasausencias($id_padre)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT TOP (1000) 
                    [id]
                   ,[id_padre]
                   ,[tipo_ausencia]
                   ,[diasMax]
                   ,[responsable]
                   ,[rrhh]
                   ,[Justificante]
                   ,[fijo]
                   ,[fijo_discontinuo]
                FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_tipo_otras_ausencias]";
        if (isset($id_padre) && $id_padre != '') {
            $sql .= " WHERE id_padre = '$id_padre'";
        }

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();

        if ($consulta == FALSE)
            die(print_r(sqlsrv_errors(), true));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }


    public function actualizarSolicitud($id_solicitud, $fecha_res_rrhh, $firma_rrhh, $fecha_sol, $estado, $id_rrhh, $mail_s, $nombre, $nombre_s, $mail_emp)
    {
        $conn = $this->conectarEmpleado();
        $sql = "UPDATE webphp_ausencias 
                SET fecha_res_rrhh = '$fecha_res_rrhh', 
                    firma_rrhh = '$firma_rrhh', 
                    estado = '$estado', 
                    comunicado_rrhh = '$id_rrhh' 
                WHERE id_solicitud = '$id_solicitud';";

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === FALSE) {
            return "Ha ocurrido un error al actualizar la solicitud de ausencia.";
        }

        if ($estado == '3') {
            $respuesta = 'aceptada por recursos humanos';
        } elseif ($estado == '4') {
            $respuesta = 'rechazada por recursos humanos';
        } elseif ($estado == '8') {
            $respuesta = 'rechazada la anulación, la solicitud siguirá en curso';
        } elseif ($estado == '5') {
            $respuesta = 'anulada por recursos humanos';
        } else {
            $respuesta = 'pendiente de recursos humanos';
        }



        $datos_sql = "SELECT * FROM webphp_ausencias WHERE id_solicitud = '$id_solicitud';";
        $datos_consulta = sqlsrv_query($conn, $datos_sql);
        $datos = sqlsrv_fetch_array($datos_consulta, SQLSRV_FETCH_ASSOC);


        // Construir el mensaje
        $mensaje = '
        <html>
            <head><title>Actualizacion de solicitud de ausencia</title></head>
            <body>
                <p>Estimado/a ' . $nombre . '</p>
                <p>La solicitud realizada el dia ' . date_format(new DateTime($fecha_sol), "d-m-Y") . ' ha sido ' . $respuesta . ' para los dias ' . $datos['fecha_desde']->format("d-m-Y") . ' a ' . $datos['fecha_hasta']->format("d-m-Y") . '.</p>';

        $mensaje .= '
                <ul>
                    <li>Fecha de respuesta: ' . date_format(new DateTime($fecha_res_rrhh), "d-m-Y") . '</li>
                </ul>
                <p>Por favor, accede al portal del empleado para revisarlas con el siguiente enlace.</p>
                <p><a href="https://webcorporativa.surexport.es">Acceder al portal del empleado</a></p>
                <p>Recuerda que puedes acceder a la aplicación desde cualquier dispositivo.</p>
                <p>Si tienes alguna duda, no dudes en ponerte en contacto con el departamento de Recursos Humanos.</p>
                <br>
                <img src="https://surexport.es/es/wp-content/themes/SurExport/images/logo-home.png" alt="Logotipo Surexport">
                <br>
            </body>
        </html>';

        // Enviar correo
        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        // $mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        // $mail->Username = 'itcomunication@surexport.es';
        // $mail->Password = 'Surxp2021+';
        // $mail->setFrom('itcomunication@surexport.es', 'Surexport');

        // $mail->Username = 'llamamientorrhh@surexport.es';
        $mail->Username = 'comunicadorrhh@surexport.com';
        $mail->Password = 'Ducu545130';
        // Correo de envio a correo de trabajador
        $mail->setFrom('comunicadorrhh@surexport.com', 'Surexport');

        $mail->addAddress($mail_emp, $nombre);
        $mail->addReplyTo($mail_s, $nombre_s);
        $mail->addCC($mail_s, $nombre_s);
        $mail->isHTML(true);
        $mail->Subject = 'Actualizacion de la solicitud';
        $mail->Body = $mensaje;
        $mail->AltBody = strip_tags($mensaje); // versión texto plano

        if ($mail->send()) {
            return "Ausencia actualizada y notificación enviada correctamente.";
        } else {
            return "La ausencia fue actualizada, pero ocurrió un error al enviar la notificación.";
        }
    }







    // Añadir observacion por RRHH
    public function agregarObservacion($id_solicitud, $pernr_obs, $fecha_crea, $pernr_usu, $observacion)
    {
        $conn2 = $this->ConectarAppReclutamiento();

        $sql_insert = "INSERT INTO webphp_estado_solicitud_ausencias (id_solicitud_ausencia, pernr_solicitante, pernr_mod, fecha_creacion, fecha_modificacion, comentario, tipo_coment) VALUES ('$id_solicitud', '$pernr_usu', '$pernr_obs', '$fecha_crea', '$fecha_crea', '$observacion', 'RRHH');";
        $consulta_insert = sqlsrv_query($conn2, $sql_insert);
        if ($consulta_insert === FALSE) {
            return false;
            // die(print_r(sqlsrv_errors(), true));
        } else {
            return true;
        }

    }



    //Datos medidas
    public function datos_medidas($PERNR)
    {
        $conn = $this->conectarDatasphere();
        $sql = "EXEC	[dbo].[RRHH_SP_PA0000]
		        @PERNR = N'$PERNR'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos direccion trabajador
    public function datos_direccion_trabajador($PERNR)
    {
        $conn = $this->conectarDatasphere();
        $sql = "EXEC    [dbo].[RRHH_SP_PA0006]
                @PERNR = N'$PERNR'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();

        // nos quedmaos con la primera fila
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos contratos empresa trabajador
    public function datos_contrato_trabajador($PERNR)
    {
        $conn = $this->conectarDatasphere();
        $sql = "EXEC	[dbo].[RRHH_SP_PA0480]
                @PERNR = N'$PERNR'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos contrato seguridad social trabajador
    public function datos_contrato2_trabajador($PERNR)
    {
        $conn = $this->conectarDatasphere();
        $sql = "EXEC	[dbo].[RRHH_SP_PA0061]
                @PERNR = N'$PERNR'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos carnet manipulador
    public function datos_ropo_trabajador($PERNR)
    {
        $conn = $this->conectarDatasphere();
        $sql = "EXEC	[dbo].[RRHH_SP_PA0032]
                @PERNR = N'$PERNR'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    public function datos_ausencia_trabajador($PERNR)
    {
        $conn = $this->conectarDatasphere();
        $sql = "EXEC	[dbo].[RRHH_SP_PA2001]
		        @PERNR = N'$PERNR'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos asignación trabajador
    public function datos_asignacion_trabajador($PERNR)
    {
        $conn = $this->conectarDatasphere();
        $sql = "EXEC	[dbo].[RRHH_SP_PA0001]
                @PERNR = N'$PERNR'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos NFC tarjeta trabajador
    public function datos_nfc_trabajador($PERNR)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT PERNR
                    ,ZZNFC AS ULTIMO_NFC
                    ,FECHA_NFC
                FROM MAESTRO_NFC
                WHERE PERNR = $PERNR";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos fecha validez documento identificacion nacional
    public function fecha_val_dni($pernr)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT 
                    id
                    ,pernr
                    ,tipo_doc
                    ,fecha_validez
                FROM MAESTRO_FV_DNI
                WHERE pernr = $pernr";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Actualizar o añadir fecha validez documento identificacion nacional al maestro
    public function update_fecha_val_dni($pernr, $tipo_doc, $fecha_validez)
    {
        $conn = $this->conectarMuleSoft();

        // Verificar si existe el registro
        $sql = "SELECT id FROM MAESTRO_FV_DNI WHERE pernr = ?";
        $params = array($pernr);
        $consulta = sqlsrv_query($conn, $sql, $params);

        if ($consulta === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        }

        if (sqlsrv_has_rows($consulta)) {
            // Actualizar registro existente
            $sql_update = "UPDATE MAESTRO_FV_DNI 
                          SET fecha_validez = ?
                          WHERE pernr = ?";
            $params = array($fecha_validez, $pernr);
            $result = sqlsrv_query($conn, $sql_update, $params);
        } else {
            // Insertar nuevo registro
            $sql_insert = "INSERT INTO MAESTRO_FV_DNI 
                          (pernr, tipo_doc, fecha_validez) 
                          VALUES (?, ?, ?)";
            $params = array($pernr, $tipo_doc, $fecha_validez);
            $result = sqlsrv_query($conn, $sql_insert, $params);
        }

        if ($result === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        }

        return $result;
    }



    // Funcion para utilizar el curl para todas las llamadas a la api de mulesoft
    public function curl_api_mulesoft($data, $metod, $path)
    {
        // URL de la API
        $url = ConfigApiMulesoft::$api_url . $path;

        // Credenciales
        $client_id = ConfigApiMulesoft::$api_client_id;
        $client_secret = ConfigApiMulesoft::$api_client_secret;

        $json_data = json_encode($data);

        // Inicializar cURL
        $ch = curl_init();

        // Configurar las opciones de cURL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $metod);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data),
            'client_id: ' . $client_id,
            'client_secret: ' . $client_secret,
            'Accept: application/json'
        ]);

        // Realizar la llamada PATCH
        $response = curl_exec($ch);

        // var_dump($response);

        // Preparar el array de respuesta
        $result = array(
            'success' => false,
            'message' => 'Error desconocido',
            'debug_info' => [] // Añadimos información de debug
        );

        // Verificar si hubo algún error de cURL
        if (curl_errno($ch)) {
            $result['message'] = 'Error de conexión: ' . curl_error($ch);
            $result['debug_info']['curl_error'] = curl_error($ch);
            curl_close($ch);
            return $result;
        }

        // Obtener el código de respuesta HTTP
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Decodificar la respuesta
        $api_response = json_decode($response, true);

        // echo $api_response['resul'] ." Resul <br>";
        // echo $api_response['status']." Status";
        // die;

        // Manejar diferentes códigos de respuesta HTTP
        switch ($http_code) {
            case 200:
            case 201:
            case 204:
                $result['success'] = $api_response['status'];
                $result['message'] = isset($api_response['message']) ? $api_response['message'] : 'Accion terminada correctamente';
                break;

            case 400:
                $result['message'] = 'Solicitud incorrecta: ' .
                    (isset($api_response['message']) ? $api_response['message'] : 'Los datos enviados son inválidos');
                break;

            case 401:
                $result['message'] = 'Error de autenticación: Las credenciales (client_id o client_secret) son inválidas o han expirado';
                break;

            case 403:
                $result['message'] = 'Acceso denegado: No tiene permisos para realizar esta acción';
                break;

            case 404:
                $result['message'] = 'No se encontró el recurso solicitado';
                break;

            case 500:
            case 502:
            case 503:
            case 504:
                $result['message'] = 'Error del servidor: Por favor, intente más tarde';
                break;

            default:
                $result['message'] = isset($api_response['message']) ?
                    $api_response['message'] :
                    'Error desconocido (Código: ' . $http_code . ')';
        }

        return $result;
    }



    // Actualizar NFC(ZZRFID) trabajador agromobile y eliminar lo que lo tengan asignado
    public function update_nfc_agro($pernr, $nfc)
    {
        $conn = $this->ConectarAppReclutamiento();
        $response = [
            'success' => false,
            'message' => '',
            'details' => []
        ];

        // SQL para comprobar que el trabajador esta en agromobile
        $sql = "SELECT [PERNR], [ZZRFID]
                FROM [192.168.200.202].[" . ConfigAgromobile::$bdsrx_nombre . "].[dbo].[ZHRT0001]
                WHERE PERNR = '" . $pernr . "'";

        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === false) {
            $response['message'] = 'Error en la consulta: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }

        $resultado = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }

        // Si la consulta tiene mas de un registro es que existe el trabajador
        if (count($resultado) > 0) {
            // Actualizar la tarjeta NFC
            $updateSql = "UPDATE [192.168.200.202].[" . ConfigAgromobile::$bdsrx_nombre . "].[dbo].[ZHRT0001]
                          SET ZZRFID = '" . strtolower($nfc) . "'
                          WHERE PERNR = '" . $pernr . "'";

            $updateConsulta = sqlsrv_query($conn, $updateSql);

            if ($updateConsulta === false) {
                $response['message'] = 'Error al actualizar NFC: ' . print_r(sqlsrv_errors(), true);
                return $response;
            }

            // Eliminar NFC duplicados
            $sql_elim = "UPDATE [192.168.200.202].[" . ConfigAgromobile::$bdsrx_nombre . "].[dbo].[ZHRT0001] 
                         SET ZZRFID = '' 
                         WHERE ZZRFID = '" . strtolower($nfc) . "' 
                         AND PERNR != '" . $pernr . "'";

            $elim_fila = sqlsrv_query($conn, $sql_elim);

            if ($elim_fila === false) {
                $response['message'] = 'Error al limpiar NFCs duplicados: ' . print_r(sqlsrv_errors(), true);
                return $response;
            }

            $response['success'] = true;
            $response['message'] = 'NFC actualizado correctamente';
            $response['details'] = [
                'pernr' => $pernr,
                'nfc' => $nfc,
                'nfc_duplicados_limpiados' => sqlsrv_rows_affected($elim_fila)
            ];
        } else {
            $response['message'] = 'Trabajador no encontrado en Agromobile';
        }

        return $response;
    }



    // Actualizar pernr a la ZZNFC en el maestro de nfc
    public function update_nfc_maestro($pernr, $nfc)
    {
        $conn = $this->conectarMuleSoft();
        $response = [
            'success' => false,
            'message' => '',
            'details' => []
        ];

        // Paso 1: Verificar si el NFC existe en la tabla
        $sql = "SELECT PERNR, ZZNFC
                FROM MAESTRO_NFC
                WHERE ZZNFC = '" . strtolower($nfc) . "'";

        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === false) {
            $response['message'] = 'Error al verificar NFC existente: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }

        $resultado = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        $fechaActual = date("Y-m-d H:i:s");

        // Paso 2: Desasignar el PERNR de otros registros con distintos ZZNFC
        $sql_desasignar = "UPDATE MAESTRO_NFC
                           SET PERNR = NULL, FECHA_NFC = '" . $fechaActual . "'
                           WHERE ZZNFC != '" . strtolower($nfc) . "' 
                           AND PERNR = '" . $pernr . "'";

        $desasignarConsulta = sqlsrv_query($conn, $sql_desasignar);
        if ($desasignarConsulta === false) {
            $response['message'] = 'Error al desasignar NFCs anteriores: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }

        $nfcsDesasignados = sqlsrv_rows_affected($desasignarConsulta);

        if ($resultado) {
            // Paso 3: Si el NFC existe, actualizarlo con el nuevo PERNR
            $updateSql = "UPDATE MAESTRO_NFC
                          SET PERNR = '" . $pernr . "', FECHA_NFC = '" . $fechaActual . "'
                          WHERE ZZNFC = '" . strtolower($nfc) . "'";

            $updateConsulta = sqlsrv_query($conn, $updateSql);
            if ($updateConsulta === false) {
                $response['message'] = 'Error al actualizar NFC existente: ' . print_r(sqlsrv_errors(), true);
                return $response;
            }

            $response['success'] = true;
            $response['message'] = 'NFC actualizado correctamente';
            $response['details'] = [
                'operacion' => 'update',
                'pernr' => $pernr,
                'nfc' => $nfc,
                'nfcs_desasignados' => $nfcsDesasignados
            ];
        } else {
            // Paso 4: Si el NFC no existe, insertarlo
            $insertSql = "INSERT INTO MAESTRO_NFC (PERNR, ZZNFC, FECHA_NFC)
                          VALUES ('" . $pernr . "', '" . strtolower($nfc) . "', '" . $fechaActual . "')";

            $insertConsulta = sqlsrv_query($conn, $insertSql);
            if ($insertConsulta === false) {
                $response['message'] = 'Error al insertar nuevo NFC: ' . print_r(sqlsrv_errors(), true);
                return $response;
            }

            $response['success'] = true;
            $response['message'] = 'Nuevo NFC registrado correctamente';
            $response['details'] = [
                'operacion' => 'insert',
                'pernr' => $pernr,
                'nfc' => $nfc,
                'nfcs_desasignados' => $nfcsDesasignados
            ];
        }

        return $response;
    }



    // Actualizar pernr a la tarjeta la tabla de usuarios de mantenimiento
    public function update_nfc_mantenimiento($pernr, $nfc)
    {
        $conn = $this->conectarMante();
        $response = [
            'success' => false,
            'message' => '',
            'details' => []
        ];

        // Paso 1: Verificar si el pernr existe en la tabla
        $sql = "SELECT id_objetive
                FROM webphp_Usuarios
                WHERE id_objetive LIKE '%" . ($pernr + 0) . "%'";


        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === false) {
            $response['message'] = 'Error al verificar PERNR existente: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }

        $resultado = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        $fechaActual = date("Y-m-d H:i:s");

        // Paso 2: Desasignar la tarjeta a todos los trabajadores que la tengan repetida
        $sql_desasignar = "UPDATE webphp_Usuarios
                           SET tarjeta = NULL
                           WHERE tarjeta = '" . strtolower($nfc) . "'";

        $desasignarConsulta = sqlsrv_query($conn, $sql_desasignar);
        if ($desasignarConsulta === false) {
            $response['message'] = 'Error al desasignar NFCs anteriores: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }

        $nfcsDesasignados = sqlsrv_rows_affected($desasignarConsulta);

        if ($resultado) {
            // Paso 3: Actualizar el trabajador con la nueva tarjeta
            $updateSql = "UPDATE webphp_Usuarios
                          SET tarjeta = '" . strtolower($nfc) . "' 
                          WHERE id_objetive LIKE '%" . ($pernr + 0) . "%'";

            $updateConsulta = sqlsrv_query($conn, $updateSql);
            if ($updateConsulta === false) {
                $response['message'] = 'Error al actualizar NFC existente: ' . print_r(sqlsrv_errors(), true);
                return $response;
            }

            $response['success'] = true;
            $response['message'] = 'NFC actualizado correctamente';
            $response['details'] = [
                'operacion' => 'update',
                'pernr' => $pernr,
                'nfc' => $nfc,
                'nfcs_desasignados' => $nfcsDesasignados
            ];
        } else {

        }

        return $response;
    }



    //Actualizar la asignación Organizativa PA0001 ----- David Pinilla
    public function update_nfc_pa0001($pernr, $nfc)
    {
        $conn = $this->conectarMuleSoft();
        $response = [
            'success' => false,
            'message' => '',
            'details' => []
        ];

        $updateSql = "UPDATE PA0001 set ZZNFC='" . strtolower($nfc) . "' where ID=(SELECT TOP(1) ID FROM PA0001 where PERNR='" . $pernr . "' order by BEGDA desc, FECHA_IN desc) and PERNR='" . $pernr . "'";

        $updateConsulta = sqlsrv_query($conn, $updateSql);
        if ($updateConsulta === false) {
            $response['message'] = 'Error al actualizar NFC en la PA0001: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }

        $response['success'] = true;
        $response['message'] = 'NFC actualizado correctamente en la PA0001';
        $response['details'] = [
            'operacion' => 'update PA0001',
            'pernr' => $pernr,
            'nfc' => $nfc
        ];

        return $response;
    }



    // Motivos del estado pendiente
    public function motivos_pendiente()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT id_motivo
                      ,desc_motivo
                FROM webphp_motivos_pendiente
                ORDER BY id_motivo DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Fincas agromobile
    public function fincas_agromobile($centro)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "select ZZCODFI, DESFI FROM [192.168.200.202].[SUREXPORT_AGROMOBILE].[dbo].[ZZMT0002] WHERE WERKS=$centro ORDER BY DESFI";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Función para generar el informe de presencia
    public function informePresencia($fincas, $fecha_ini, $fecha_fin, $centro, $division, $operario)
    {
        // Conectar a la base de datos
        $conn = $this->ConectarAppReclutamiento();

        // Definir la consulta para ejecutar el procedimiento almacenado
        $sql = "EXEC [192.168.200.202].[SUREXPORT_AGROMOBILE].[dbo].[PROC_INFORME_PRESENCIA_CENTRO_RRHH] ?, ?, ?, ?, ?, ?";

        // Convertir las entradas de $fincas y $operario a cadenas si son arrays
        $fincasString = is_array($fincas) ? implode(',', $fincas) : $fincas;
        $operarioString = is_array($operario) ? implode(',', $operario) : $operario;

        // Definir los parámetros para la consulta
        $params = array(
            array($fecha_ini, SQLSRV_PARAM_IN),
            array($fecha_fin, SQLSRV_PARAM_IN),
            array($centro, SQLSRV_PARAM_IN),
            array($division, SQLSRV_PARAM_IN),
            array($fincasString, SQLSRV_PARAM_IN),
            array($operarioString, SQLSRV_PARAM_IN)
        );

        // Preparar la consulta
        $consulta = sqlsrv_prepare($conn, $sql, $params);
        if ($consulta === false) {
            die($this->$this->formatErrors(sqlsrv_errors()));
        }

        // Ejecutar la consulta
        $exec = sqlsrv_execute($consulta);
        if ($exec === false) {
            die($this->$this->formatErrors(sqlsrv_errors()));
        }

        // Procesar los resultados
        $resultado = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }

        // Liberar recursos y cerrar la conexión
        sqlsrv_free_stmt($consulta);
        sqlsrv_close($conn);

        return $resultado;
    }



    // Informe presencia 
    public function informePresenciaOficina($fecha_ini, $fecha_fin, $tipo, $pernr, $manual, $sede, $ubicacion)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;
                    SELECT 
                        reg.id,
                        reg.fecha,
                        reg.pernr,
                        reg.fecha_reg,
                        reg.tipo_reg,
                        reg.manual,
                        dispo.id_dispositivo,
                        dispo.nombre AS nombre_dispositivo,
                        dispo.presencia,
                        dispo.activo,
                        ubi.sede AS sede_ubi,
                        ubi.nombre AS nombre_ubi,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS, 
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.NOMBRE))) AS NOMBRE,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.APELLIDO1))) AS APELLIDO1,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.APELLIDO2))) AS APELLIDO2
                    FROM
                        [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registro_horario] AS reg
                    LEFT JOIN
                        [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_dispositivos] AS dispo ON reg.id_dispo = dispo.id_dispositivo
                    LEFT JOIN
                        [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_ubicaciones_dispo] AS ubi ON dispo.ubicacion = ubi.id
                    LEFT JOIN
                        [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0002] AS emp ON reg.pernr = emp.PERNR WHERE 1=1 ";

        if ($tipo != '') {
            $sql .= "AND tipo_reg = '" . $tipo . "' ";
        }


        if ($fecha_ini != '' && $fecha_fin != '') {
            $fecha_fin_dt = new DateTime($fecha_fin);
            $fecha_fin_dt->modify('+1 day');
            $sql .= "AND CAST(COALESCE(reg.fecha_reg, reg.fecha) AS DATE) BETWEEN '" . $fecha_ini . " 03:00:00' AND '" . $fecha_fin_dt->format("Y-m-d") . " 03:00:00'";
        } else if ($fecha_ini != '') {
            $fecha_ini_dt = new DateTime($fecha_ini);
            $fecha_ini_dt->modify('+1 day');
            $sql .= "AND CAST(COALESCE(reg.fecha_reg, reg.fecha) AS DATE) BETWEEN '" . $fecha_ini . " 03:00:00' AND '" . $fecha_ini_dt->format("Y-m-d") . " 03:00:00'";
        }


        $sql .= " AND (
                    reg.tipo_reg <> 'salida' 
                    OR reg.id > (
                        SELECT MIN(id) 
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registro_horario] 
                        WHERE pernr = reg.pernr 
                        AND tipo_reg = 'entrada' 
                        AND CAST(COALESCE(fecha_reg, fecha) AS DATE) = '$fecha_ini'
                    )   
    
                )";


        if ($pernr != '') {
            if (is_string($pernr)) {
                $pernr = explode(',', $pernr); // Convierte la cadena separada por comas en un array
            }

            // Asegúrate de que $pernr_nom sea un array
            if (is_array($pernr)) {
                // Limpia los valores del array para quitar espacios innecesarios
                $pernr = array_map('trim', $pernr);

                // Filtra los valores vacíos
                $pernr = array_filter($pernr, function ($value) {
                    return !empty($value);
                });

                if (!empty($pernr)) {
                    // Convierte el array a una lista de valores SQL
                    $pernr_str = "'" . implode("','", array_map('addslashes', $pernr)) . "'";

                    // Construye la parte de la consulta
                    $sql .= ($fecha_ini ? " AND" : " WHERE") . " (
                                reg.pernr IN ($pernr_str)
                            ) ";
                }
            }
        }


        if ($manual == '1') {
            $sql .= "AND reg.manual = '1' ";
        }

        if ($sede != '') {
            $sql .= "AND ubi.sede = '" . $sede . "' ";
        }

        if ($ubicacion != '') {
            $sql .= "AND ubi.id = '" . $ubicacion . "' ";
        }

        $sql .= "ORDER BY reg.fecha ASC;
                         CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        // echo $sql;
        // die;

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        // echo $sql;
        // die;
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Trabjarores para el filtro de auditoria
    public function trabajadoresAuditoria()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;

                SELECT 
                    w.pernr,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS
                FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registro_horario] w
                INNER JOIN [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0002] p 
                    ON w.pernr = p.PERNR
                GROUP BY 
                    w.pernr, 
                    p.NOMBREYAPELLIDOS
                ORDER BY 
                    w.pernr ASC;

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    public function informePresenciaOficina2($filtros)
    {
        set_time_limit(300);

        $fecha_ini = $filtros['fecha_inicio_ofi'] ?? null;
        $fecha_fin = $filtros['fecha_fin_ofi'] ?? $fecha_ini;
        $calculo = $filtros['filtro_horas'] ?? '';
        $pernr_nom = $filtros['pernr_nom_trab'] ?? null;

        // Convertir array de pernr a cadena separada por comas
        if (is_array($pernr_nom)) {
            $pernr_nom = implode(',', array_filter(array_map('trim', $pernr_nom)));
        } elseif (is_string($pernr_nom)) {
            $pernr_nom = trim($pernr_nom);
        } else {
            $pernr_nom = null; // traer todos si está vacío
        }

        // Ejecutar procedimiento almacenado
        $conn = $this->ConectarAppReclutamiento();
        $sql = "EXEC [dbo].[sp_CalcularResumenJornada_pernr] ?, ?, ?, ?";

        $params = [
            [$fecha_ini, SQLSRV_PARAM_IN],
            [$fecha_fin, SQLSRV_PARAM_IN],
            [$calculo, SQLSRV_PARAM_IN],
            [$pernr_nom, SQLSRV_PARAM_IN]
        ];

        $consulta = sqlsrv_prepare($conn, $sql, $params);
        if (!$consulta || !sqlsrv_execute($consulta)) {
            die($this->formatErrors(sqlsrv_errors()));
        }

        // Recolectar resultados y pernr únicos
        $resultado = [];
        $pernrs = [];
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            // Verificar que pernr no esté vacío o NULL
            if (!empty($row['pernr'])) {
                $resultado[] = $row;
                $pernrs[] = $row['pernr'];
            }
        }
        sqlsrv_free_stmt($consulta);
        sqlsrv_close($conn);

        // --------------------------------------------------
        // Descifrar nombres desde Mulesoft
        // --------------------------------------------------
        if (!empty($pernrs)) {
            $conn2 = $this->ConectarMuleSoft();
            if (!sqlsrv_query($conn2, "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG DECRYPTION BY CERTIFICATE CertificadoPA_REG;")) {
                die($this->formatErrors(sqlsrv_errors()));
            }

            $inClause = "'" . implode("','", array_unique($pernrs)) . "'";
            $sql_nombres = "
                SELECT PERNR, CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS
                FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0002]
                WHERE PERNR IN ($inClause)
            ";

            $consultaNombres = sqlsrv_query($conn2, $sql_nombres);
            if ($consultaNombres === false)
                die($this->formatErrors(sqlsrv_errors()));

            $nombres = [];
            while ($row = sqlsrv_fetch_array($consultaNombres, SQLSRV_FETCH_ASSOC)) {
                $nombres[$row['PERNR']] = $row['NOMBREYAPELLIDOS'];
            }
            sqlsrv_free_stmt($consultaNombres);
            sqlsrv_query($conn2, "CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;");
            sqlsrv_close($conn2);

            // Agregar nombres a resultados
            foreach ($resultado as &$fila) {
                $fila['NOMBREYAPELLIDOS'] = $nombres[$fila['pernr']] ?? null;
            }
        }

        // --------------------------------------------------
        // Función auxiliar para convertir HH:MM a minutos
        // --------------------------------------------------
        function timeToMinutes($tiempo)
        {
            if (empty($tiempo))
                return 0;
            $partes = explode(':', $tiempo);
            return (count($partes) === 2) ? intval($partes[0]) * 60 + intval($partes[1]) : 0;
        }

        // --------------------------------------------------
        // Aplicar filtros de tiempo si existen
        // --------------------------------------------------
        $filtroCondiciones = [];
        for ($i = 1; $i <= 6; $i++) {
            $campo = $_POST["campo1_$i"] ?? '0';
            $operador = $_POST["campo2_$i"] ?? '0';
            $tiempoTexto = $_POST["campo3_$i"] ?? '';
            $conector = $_POST["conector_" . ($i - 1)] ?? '0';

            if ($campo !== '0' && $operador !== '0' && $tiempoTexto !== '') {
                list($h, $m) = explode(':', $tiempoTexto);
                $filtroCondiciones[] = [
                    'campo' => $campo,
                    'operador' => in_array($operador, ['<', '<=', '>', '>=', '==', '!=']) ? $operador : '==',
                    'minutos' => ((int) $h) * 60 + (int) $m,
                    'conector' => $conector
                ];
            }
        }

        if (!empty($filtroCondiciones)) {
            $resultado = array_filter($resultado, function ($fila) use ($filtroCondiciones) {
                $expresionFinal = '';
                foreach ($filtroCondiciones as $i => $cond) {
                    $valor = match ($cond['campo']) {
                        '1' => $fila['segundos_desayuno'] ?? 0,
                        '2' => $fila['segundos_almuerzo'] ?? 0,
                        '3' => $fila['segundos_otros'] ?? 0,
                        '4' => ($fila['segundos_desayuno'] ?? 0) + ($fila['segundos_almuerzo'] ?? 0) + ($fila['segundos_otros'] ?? 0),
                        '5' => ($fila['segundos_totales'] ?? 0) - (($fila['segundos_desayuno'] ?? 0) + ($fila['segundos_almuerzo'] ?? 0) + ($fila['segundos_otros'] ?? 0)),
                        '6' => $fila['segundos_totales'] ?? 0,
                        default => 0
                    };

                    $valorMin = $valor / 60;
                    $expresion = "($valorMin {$cond['operador']} {$cond['minutos']})";
                    if ($i > 0 && $cond['conector'] !== '0')
                        $expresionFinal .= " {$cond['conector']} ";
                    $expresionFinal .= $expresion;
                }

                return eval ("return $expresionFinal;");
            });
        }

        return $resultado;
    }
























    public function informePresenciaOficinaDatos($fecha_ini, $pernr)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Limpio y aseguro el pernr para la consulta
        $pernr = trim($pernr);

        $sql = "WITH registros AS (
                    SELECT 
                        reg.id,
                        reg.pernr,
                        reg.tipo_reg,
                        -- Si fecha_utc y utc_api están presentes, calcular fecha ajustada, sino usar fecha_reg
                        CASE 
                            WHEN (reg.fecha_utc IS NOT NULL AND reg.fecha_utc != '') AND (reg.utc_api IS NOT NULL AND reg.utc_api != '') 
                            THEN DATEADD(HOUR, CAST(reg.utc_api AS INT), CAST(reg.fecha_utc AS DATETIME))
                            ELSE CAST(reg.fecha_reg AS DATETIME)
                        END AS fecha_reg,
                        reg.id_dispo,
                        reg.fecha,
                        reg.manual,
                        ubi.sede,
                        ubi.nombre AS nombre_ubi,
                        reg.motivo,
                        reg.comentario,
                        reg.localizacion,
                        reg.dispositivo
                    FROM webphp_registro_horario reg
                    LEFT JOIN webphp_dispositivos dispo ON reg.id_dispo = dispo.id_dispositivo
                    LEFT JOIN webphp_ubicaciones_dispo ubi ON dispo.ubicacion = ubi.id
                    WHERE reg.pernr LIKE ?
                ),
                primeras_entradas AS (
                    SELECT
                        r.id,
                        r.pernr,
                        r.tipo_reg,
                        r.fecha_reg,
                        r.id_dispo,
                        r.fecha,
                        r.manual,
                        r.sede,
                        r.nombre_ubi,
                        r.motivo,
                        r.comentario,
                        r.localizacion,
                        r.dispositivo,
                        (
                            SELECT MAX(e.fecha_reg)
                            FROM registros e
                            WHERE e.pernr = r.pernr
                            AND e.tipo_reg = 'entrada'
                            AND e.fecha_reg <= r.fecha_reg
                        ) AS fecha_inicio_jornada
                    FROM registros r
                )
                SELECT
                    p.id,
                    p.pernr,
                    p.tipo_reg,
                    p.fecha_reg,
                    p.id_dispo,
                    p.fecha,
                    p.manual,
                    p.sede,
                    p.nombre_ubi,
                    p.motivo,
                    p.comentario,
                    p.localizacion,
                    p.dispositivo,
                    CAST(p.fecha_inicio_jornada AS DATE) AS fecha_jornada
                FROM primeras_entradas p
                WHERE (
                    -- Registros con entrada previa (lógica original)
                    CAST(p.fecha_inicio_jornada AS DATE) = '$fecha_ini'
                    OR 
                    -- Registros manuales (manual = 3) de la fecha seleccionada sin entrada previa
                    (p.manual = 3 AND CAST(p.fecha_reg AS DATE) = '$fecha_ini')
                )
                ORDER BY p.fecha_reg ASC;
            ";

        // Preparar los parámetros para la consulta
        $params = ["%$pernr%"];

        $consulta = sqlsrv_query($conn, $sql, $params);
        $resultado = [];

        if ($consulta === false) {
            die($this->FormatErrors(sqlsrv_errors()));
        }

        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }

        return $resultado;
    }



    // Validar registro de presencia oficina
    public function validar_registro($id, $fecha, $estado, $motivo, $editar = false)
    {
        $conn = $this->ConectarAppReclutamiento();

        if ($editar) {
            $reg_antiguo = $this->obtenerRegistroPorId($id);
        }

        $sql = "UPDATE webphp_registro_horario SET fecha_reg = '" . $fecha . "', manual = '" . $estado . "', motivo = '" . $motivo . "', fecha_utc = NULL, utc_api = NULL, zona_api = NULL WHERE id=" . $id;

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            return false;
        }
        if ($editar) {
            $this->addTrazaRegHorario($id, $reg_antiguo['fecha_reg'], $fecha, 'Modificar jornada presencia por RRHH');
        }
        return true;
    }

    public function obtenerRegistroPorId($id)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT * FROM webphp_registro_horario WHERE id = ?";
        $params = [$id];
        $consulta = sqlsrv_query($conn, $sql, $params);

        if ($consulta === false) {
            return null;
        }

        $registro = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $registro ?: null;
    }

    public function addTrazaRegHorario($id_registro, $fecha_ant, $fecha_nueva, $accion)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "INSERT INTO webphp_traza_registro_horario (id_registro, fecha_anterior, fecha_nueva, usuario_mod, fecha_mod, accion)
                VALUES (?, ?, ?, ?, GETDATE(), ?)";
        $params = [$id_registro, $fecha_ant, $fecha_nueva, $_SESSION["id_user_surexport_appreclu"], $accion];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            error_log("Error al insertar traza: " . print_r(sqlsrv_errors(), true));
            return false;
        }
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
        return true;
    }



    // Trabajadores Almacen
    public function trabajadoresAlmacen()
    {
        $conn = $this->conectarProduccion();
        $sql = "SELECT 
                    DISTINCT EXTERNALID, 
                    USERNAME 
                FROM [dbo].[Aperturas] 
                ORDER BY USERNAME ASC;";

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Informe presencia almacen
    public function informePresenciaAlmacen($fecha_ini, $fecha_fin, $pernr, $puerta)
    {
        $conn = $this->conectarProduccion();
        $sql = "SELECT [ID]
                      ,[USERID]
                      ,[EXTERNALID]
                      ,[USERNAME]
                      ,[DOORID]
                      ,[DOORNAME]
                      ,[EVENTTYPE]
                      ,[OPENINGDATE]
                FROM [dbo].[Aperturas] WHERE 1=1 ";


        if ($fecha_ini != '' && $fecha_fin != '') {
            $sql .= "AND OPENINGDATE BETWEEN '" . $fecha_ini . " 00:00:00' AND '" . $fecha_fin . " 23:59:59'";
        } else if ($fecha_ini != '') {
            $sql .= "AND OPENINGDATE BETWEEN '" . $fecha_ini . " 00:00:00' AND '" . $fecha_ini . " 23:59:59'";
        }

        // if ($txt_bus != '') {
        //     $sql .= "AND (USERNAME LIKE '%".$txt_bus."%' 
        //     OR EXTERNALID LIKE '%".$txt_bus."%') ";
        // }
        if ($pernr != '') {
            if (is_string($pernr)) {
                $pernr = explode('|', $pernr); // Convierte la cadena separada por comas en un array
            }

            // Asegúrate de que $pernr_nom sea un array
            if (is_array($pernr)) {

                if (!empty($pernr)) {
                    // Convierte el array a una lista de valores SQL
                    $pernr_str = "'" . implode("','", array_map('addslashes', $pernr)) . "'";

                    // Construye la parte de la consulta
                    $sql .= "AND USERNAME IN ($pernr_str)";
                }
            }
        }

        if ($puerta != '') {
            $sql .= "AND DOORID = '" . $puerta . "' ";
        }

        $sql .= "ORDER BY OPENINGDATE DESC;";

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();

        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Puertas para informe
    public function puertas_tesa()
    {
        $conn = $this->conectarProduccion();
        $sql = "SELECT [DOORID], [DOORNAME]
                FROM [dbo].[Aperturas]
                GROUP BY [DOORID], [DOORNAME]
                ORDER BY DOORID ASC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Info de un usuario en tesa
    public function tesa_info_usu($id)
    {
        // USUARIOS
        $externalid = $id;
        $curl = curl_init();
        $url = "http://192.168.200.210/surexport/users_integracion.php?operator=Cies&pass=Cies$2019&function=userGetInfoByExternalId&externalId=$externalid";

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Preparar el array de respuesta
        $result = array(
            'success' => false,
            'data' => null,
            'message' => '',
            'debug_info' => []
        );

        // Verificar si hubo error en la petición
        if ($response === false) {
            $result['message'] = 'Error de conexión: ' . curl_error($curl);
            $result['debug_info']['curl_error'] = curl_error($curl);
            curl_close($curl);
            return $result;
        }

        curl_close($curl);

        // Verificar el código de respuesta HTTP
        if ($httpCode !== 200) {
            $result['message'] = "Error en la llamada de la API. Código: $httpCode";
            return $result;
        }

        // Convertir XML a array
        $xmlObject = simplexml_load_string($response);
        if ($xmlObject === false) {
            $result['message'] = "Error al procesar XML";
            $result['debug_info']['xml_errors'] = libxml_get_errors();
            return $result;
        }

        // Convertir el objeto XML a array
        $userData = json_decode(json_encode($xmlObject), true);

        // Si todo está bien, devolver los datos
        $result['success'] = true;
        $result['data'] = $userData;
        $result['message'] = 'Datos recuperados correctamente';

        return $result;
    }



    //total trabajadores por sociedad
    public function sedes()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT [sede]
                FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_ubicaciones_dispo]
                GROUP BY sede";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        // echo $sql;
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Ubicaciones por sede
    public function obtener_ubicaciones_por_sede($sede)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT [id]
                      ,[sede]
                      ,[nombre]
                FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_ubicaciones_dispo]
                WHERE sede = '" . $sede . "'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Operarios por centro y division de trabajo
    public function operarios_centro($centro, $division)
    {
        $conn = $this->conectarMuleSoft();
        // La vista esta creada solo en la tabla Mulesoft
        $sql = "
        OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
        DECRYPTION BY CERTIFICATE CertificadoPA_REG;
        SELECT * FROM PA_SOCIEDAD_CENTRO";
        if ($centro != '') {
            $sql .= " WHERE ZZWERKS = $centro";
        }
        if ($division != '') {
            $sql .= " AND WERKS = $division";
        }

        $sql .= " CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Añadir nueva alerta al trabajador
    public function nueva_alerta($pernr, $tipo_alerta, $descripcion, $fecha_ini, $fecha_fin, $tipo_formacion, $obligatorio, $tipo_incidente, $prioridad, $notificado, $frecuencia)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Verificar si los valores están vacíos y establecerlos como NULL si es necesario

        $tipo_formacion = $tipo_formacion ? "'$tipo_formacion'" : 'NULL';
        $obligatorio = $obligatorio ? "'$obligatorio'" : 'NULL';
        $tipo_incidente = $tipo_incidente ? "'$tipo_incidente'" : 'NULL';

        $sql = "INSERT INTO webphp_alertas_trabajador 
                    (pernr, tipo_alerta, descripcion, fecha_ini, fecha_fin, tipo_formacion, obligatoria, tipo_incidente, prioridad, notificar, frecuencia_noti) 
                VALUES 
                    ('$pernr', '$tipo_alerta', '$descripcion', '$fecha_ini', '$fecha_fin', $tipo_formacion, $obligatorio, $tipo_incidente, '$prioridad', '$notificado', '$frecuencia')";


        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            return false;
        }
        return true;
    }



    // Alertas por trabajador
    public function alertas_trabajador($pernr)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT * FROM webphp_alertas_trabajador 
                WHERE pernr = '" . $pernr . "'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();

        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Listado de fincas y almacenes de la sociedad '1000'
    public function fincas_almacenes_sociedad()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT DISTINCT ZZLGORT, DESC_ALMACEN
                FROM PA0001 
                WHERE ZZWERKS = '1000' AND ZZLGORT <> '' 
                ORDER BY ZZLGORT ASC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Maestro de relaciones laborales PA0061
    public function maestro_relaciones_laborales()
    {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT [RELACION_LABORAL]
                      ,[DESC_RELACION_LABORAL]
                FROM [PA0061]
                GROUP BY RELACION_LABORAL, DESC_RELACION_LABORAL";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Trabajadores de baja para llamamientos con fecha de bajas desde fecha actual hasta 18 meses atrás
    public function trabajadores_baja($ubi_trab, $fecha_ini, $fecha_fin, $relacion_laboral = '', $codigos_formateados = '', $grupo_trabajador = '')
    {
        try {
            $conn = $this->conectarDatasphere();

            $sql = "EXEC [dbo].[RRHH_SP_personas_fijo_dis] ";

            // Flag para saber si ya agregamos algún parámetro
            $first_param = true;

            if (isset($codigos_formateados) && $codigos_formateados != '') {
                // $codigos_formateados viene como: '03006061', '04006006', '03000517', etc.
                // Necesitamos duplicar las comillas simples para SQL Server
                $codigos_escaped = str_replace("'", "", $codigos_formateados);
                $sql .= "@PERNR_LIST = '$codigos_escaped'";
                $first_param = false;
            }

            if (isset($fecha_ini) && $fecha_ini != '') {
                $fecha_ini_formatted = str_replace('-', '', $fecha_ini);
                $sql .= ($first_param ? "" : ", ") . "@BEGDA_POSICION_INICIO = '$fecha_ini_formatted'";
                $first_param = false;
            } else {
                $fecha_ini = DATE('Ymd', strtotime('-16 months'));
                $sql .= ($first_param ? "" : ", ") . "@BEGDA_POSICION_INICIO = '$fecha_ini'";
                $first_param = false;
            }

            if (isset($fecha_fin) && $fecha_fin != '') {
                $fecha_fin_formatted = str_replace('-', '', $fecha_fin);
                $sql .= ($first_param ? "" : ", ") . "@BEGDA_POSICION_FIN = '$fecha_fin_formatted'";
                $first_param = false;
            } else {
                $fecha_fin = DATE('Ymd');
                $sql .= ($first_param ? "" : ", ") . "@BEGDA_POSICION_FIN = '$fecha_fin'";
                $first_param = false;
            }

            if (isset($ubi_trab) && $ubi_trab != '') {
                $sql .= ($first_param ? "" : ", ") . "@ZZLGORT = '$ubi_trab'";
                $first_param = false;
            }

            if (isset($relacion_laboral) && $relacion_laboral != '') {
                $sql .= ($first_param ? "" : ", ") . "@EMPL_RELATION = '$relacion_laboral'";
            }

            $consulta = sqlsrv_query($conn, $sql);
            if ($consulta === FALSE) {
                throw new Exception(print_r(sqlsrv_errors(), true));
            }

            // Si el valor $grupo_trabajador es proporcionado, filtrar los resultados
            if (isset($grupo_trabajador) && $grupo_trabajador != '') {
                $filtered_results = array();
                while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
                    if (isset($row['grupo']) && $row['grupo'] == $grupo_trabajador) {
                        $filtered_results[] = $row;
                    }
                }
                sqlsrv_free_stmt($consulta);
                sqlsrv_close($conn);
                return $filtered_results;
            } else {
                $resultado = array();
                while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
                    $resultado[] = $row;
                }
            }

            sqlsrv_free_stmt($consulta);
            sqlsrv_close($conn);

            return $resultado;
        } catch (Exception $e) {
            die("Error en trabajadores_baja: " . $e->getMessage());
        }
    }



    //Trabajadores disponibles para remesa de llamamientos que no esten en una o que su estado en esa sea rechazado
    public function trabajadores_baja_rem($id_remesa = null, $ano_remesa = null, $ubi_trab = null, $fecha_ini = null, $fecha_fin = null, $codigos_formateados = null, $relacion_laboral = null)
    {
        if ($id_remesa == 0 && $ano_remesa == 0) {
            $id_remesa = null;
            $ano_remesa = null;
        }
        try {
            $conn = $this->conectarMuleSoft();

            // Subconsulta para obtener el último estado de llamamiento para cada trabajador
            $subquery_ultimo_estado = "
            SELECT wrl.PERNR, wrl.ESTADO, wrl.FECHA_REGISTRO
                FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] wrl
                INNER JOIN (
                    SELECT PERNR, MAX(FECHA_REGISTRO) AS UltimaFecha
                    FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos]
                    WHERE (elim IS NULL OR elim <> '1')
                    GROUP BY PERNR
                ) ult ON wrl.PERNR = ult.PERNR AND wrl.FECHA_REGISTRO = ult.UltimaFecha
            WHERE (elim IS NULL OR elim <> '1')
            ";

            // Cláusula WHERE actualizada
            $where = "
                        WHERE (a.PERNR NOT IN (
                                    SELECT PERNR
                                    FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos]
                                    WHERE (elim IS NULL OR elim <> '1')
                                )
                        OR a.PERNR IN (
                                SELECT PERNR
                                FROM ($subquery_ultimo_estado) AS UltimoEstado
                                WHERE UltimoEstado.ESTADO = 2 
                                OR UltimoEstado.ESTADO = 1
                            ))
                    AND (url.id_remesa IS NULL OR ur.id_remesa IS NULL OR (url.id_remesa = ur.id_remesa AND url.ano_remesa = ur.ano_remesa))";

            // Parámetros iniciales
            $params = array();

            // Añadir condición adicional si se proporcionan id_remesa y ano_remesa
            if ($id_remesa !== null && $ano_remesa !== null) {
                $where .= " AND (a.PERNR NOT IN (
                                    SELECT PERNR
                                    FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos]
                                    WHERE id_remesa = ?
                                    AND YEAR(fecha_remesa) = ?
                                    AND (elim <> '1' OR elim IS NULL)
                                )
                            OR a.PERNR IN (
                                        SELECT PERNR
                                        FROM ($subquery_ultimo_estado) AS UltimoEstado
                                        WHERE UltimoEstado.ESTADO = 2
                                        OR UltimoEstado.ESTADO = 1
                                        AND EXISTS (
                                            SELECT 1
                                            FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] wrl
                                            WHERE wrl.PERNR = UltimoEstado.PERNR
                                            AND wrl.id_remesa = ?
                                            AND wrl.ano_remesa = ?
                                            AND (wrl.elim <> '1' OR wrl.elim IS NULL)
                                        )
                                    ))";
                $params = array($id_remesa, $ano_remesa, $id_remesa, $ano_remesa);
            }


            $sql = "
                OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;
                with UltimoRegistroLlamamiento AS (
                        SELECT PERNR, id_remesa, ano_remesa, FECHA_REGISTRO, ESTADO,
                            ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY FECHA_REGISTRO DESC) AS rn
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos]
                        WHERE (elim IS NULL OR elim <> '1')
                    ),
                    UltimaRemesa AS (
                        SELECT PERNR, id_remesa, YEAR(fecha_remesa) AS ano_remesa,
                            ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY id_remesa DESC, fecha_remesa DESC) AS rn
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos]
                        WHERE (elim IS NULL OR elim <> '1')
                    ),
                    LatestRemesa AS (
                        SELECT PERNR, id_remesa, fecha_remesa, nombre_remesa, ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY id_remesa DESC) AS id_1
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos]
                        WHERE (elim IS NULL OR elim <> '1')
                    )
                    SELECT a.*,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.NOMBRE))) AS NOMBRE,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.APELLIDO1))) AS APELLIDO1,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.APELLIDO2))) AS APELLIDO2,
                        b.ZZLGORT, c.SEXO, d.MOVIL, d.PRE_TELF, LOWER(d.[CORREO]) as CORREO, b.DESC_ALMACEN,
                        CASE WHEN url.ESTADO IS NOT NULL THEN url.ESTADO ELSE 0 END as ESTADO_LLAMAMIENTO,
                        url.FECHA_REGISTRO,
                        url.id_remesa AS ultimo_id_remesa, url.ano_remesa AS ultimo_ano_remesa,
                        r.id_remesa, r.nombre_remesa, YEAR(r.fecha_remesa) as ano_remesa,
                        CASE
                            WHEN url.id_remesa = ur.id_remesa AND url.ano_remesa = ur.ano_remesa THEN 1
                            ELSE 0
                        END AS coincide_ultimo_registro, pa61.RELACION_LABORAL, pa61.DESC_RELACION_LABORAL
                    FROM (
                        SELECT pa0.PERNR, pa0.MASSN, pa0.BEGDA, pa0.ENDDA
                        FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0000] pa0
                        JOIN (
                            SELECT pernr, MAX(begda) AS begda
                            FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0000]
                            GROUP BY PERNR
                        ) AS pa00 ON pa0.PERNR = pa00.PERNR AND pa0.BEGDA = pa00.begda
                        WHERE pa0.MASSN = 'E2' AND pa0.STAT2 = 0 AND pa0.MASSG = '20'
                    ) a
                    LEFT JOIN
                        (SELECT pa61.PERNR,
                                pa61.RELACION_LABORAL,
                                pa61.DESC_RELACION_LABORAL
                        FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0061] pa61
                        JOIN
                            (SELECT pernr,
                                    MAX(begda) AS begda
                            FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0061]
                            GROUP BY PERNR) AS pa061 ON pa61.PERNR = pa061.PERNR
                        AND pa61.BEGDA = pa061.begda) AS pa61 ON a.PERNR = pa61.PERNR
                    LEFT JOIN (
                        SELECT pa1.PERNR, pa1.PLANS, pa1.STEXT_PLANS, pa1.ZZLGORT, pa1.ZZWERKS, pa1.DESC_ALMACEN
                        FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0001] pa1
                        JOIN (
                            SELECT pernr, MAX(begda) AS begda
                            FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0001]
                            GROUP BY PERNR
                        ) AS pa01 ON pa1.PERNR = pa01.PERNR AND pa1.BEGDA = pa01.begda
                    ) b ON a.PERNR = b.PERNR
                    LEFT JOIN [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0002] c ON a.PERNR = c.PERNR
                    LEFT JOIN (
                        SELECT PERNR, MOVIL, PRE_TELF, CORREO
                        FROM (
                            SELECT 
                                PERNR, 
                                MOVIL, 
                                PRE_TELF, 
                                CORREO, 
					            ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY FECHA_IN DESC) AS rn
                        FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0105] )
                        d WHERE rn = 1 ) d ON a.PERNR = d.PERNR
                    LEFT JOIN UltimoRegistroLlamamiento url
                        ON a.PERNR = url.PERNR AND url.rn = 1
                    LEFT JOIN LatestRemesa r
                        ON a.PERNR = r.PERNR AND r.id_1 = 1
                    LEFT JOIN UltimaRemesa ur ON a.PERNR = ur.PERNR AND ur.rn = 1
                    $where
                    AND (b.ZZWERKS LIKE '1000%')
                    ";
            if ($ubi_trab !== null && $ubi_trab !== '') {
                $sql .= " AND b.ZZLGORT = '$ubi_trab' ";
            }

            // Filtro por códigos formateados
            if ($codigos_formateados !== null && $codigos_formateados !== '') {
                $sql .= " AND a.PERNR IN ($codigos_formateados) 
                          OR CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.DNI))) IN ($codigos_formateados) ";
            }

            // Filtro por relación laboral
            if ($relacion_laboral !== null && $relacion_laboral !== '') {
                $sql .= " AND pa61.RELACION_LABORAL = '$relacion_laboral' ";
            }

            if ($fecha_ini != '' && $fecha_fin != '') {
                $sql .= " AND a.BEGDA BETWEEN '$fecha_ini' AND '$fecha_fin'";
            } elseif ($fecha_ini != '') {
                $sql .= " AND a.BEGDA = '$fecha_ini'";
            } else {
                $sql .= " AND a.BEGDA >= DATEADD(MONTH, -16, GETDATE())";
            }

            $sql .= "GROUP BY a.PERNR, a.MASSN, a.BEGDA, a.ENDDA,
                            c.NOMBREYAPELLIDOS, c.NOMBRE, c.APELLIDO1, c.APELLIDO2, b.ZZLGORT, c.SEXO, d.MOVIL, d.PRE_TELF, d.CORREO,
                            url.ESTADO, url.FECHA_REGISTRO, url.id_remesa, url.ano_remesa,
                            r.id_remesa, r.nombre_remesa, r.fecha_remesa,
                            ur.id_remesa, ur.ano_remesa, b.DESC_ALMACEN, pa61.RELACION_LABORAL, pa61.DESC_RELACION_LABORAL
                    ORDER BY a.BEGDA ASC;
                    CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

            // Ejecutar consulta
            $consulta = sqlsrv_query($conn, $sql, $params);

            if ($consulta === FALSE) {
                throw new Exception(print_r(sqlsrv_errors(), true));
            }

            $resultado = array();
            while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
                $resultado[] = $row;
            }

            sqlsrv_free_stmt($consulta);
            sqlsrv_close($conn);

            return $resultado;

        } catch (Exception $e) {
            die("Error en trabajadores_baja_rem: " . $e->getMessage());
        }
    }



    // Funciones para los distintos tipos de llamamientos

    //Llamamiento por correo electronico
    public function send_mail($pernr, $nombre, $tipo_llamamiento2, $fecha_registro, $info_contacto, $estado, $id_usuario, $id_remesa, $ano_remesa, $mail_usu_web, $mensaje_usu_web)
    {
        //Insertamos el registro y enviamos el correo
        $conn = $this->ConectarAppReclutamiento();

        // Verificar si se tienen todos los datos para enviar el correo
        $sql = "INSERT INTO webphp_registros_llamamientos (PERNR, TIPO_LLAMAMIENTO, INFO_CONTACTO, FECHA_REGISTRO, ESTADO, ID_USUARIO, ID_REMESA, ANO_REMESA, NUM_ENVIO) 
                VALUES (
                '" . $pernr . "',  
                '" . $tipo_llamamiento2 . "', 
                '" . $info_contacto . "', 
                '" . $fecha_registro . "',
                '" . $estado . "',
                '" . $id_usuario . "',
                " . (is_null($id_remesa) ? 'NULL' : "'" . $id_remesa . "'") . ", 
                " . (is_null($ano_remesa) ? 'NULL' : "'" . $ano_remesa . "'") . ",
                '1'
                );";

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            echo "Error al insertar en la base de datos: " . print_r(sqlsrv_errors(), true);
            return false;
        }

        if ($consulta == TRUE) {
            $mensaje = '
            <html>
                <body>
                    <h4>Llamamiento Surexport S.L.</h4>
                    <p>' . $nombre . ', Surexport le comunica su llamamiento para su puesto de trabajo.</p>
                    <p>' . $mensaje_usu_web . '</p>
                    <p>Accede al siguiente Link para mas info:</p>
                    <a href="https://aplicaciones.surexport.es:1110/portal_rrhh/cartas/llama1_base.htm">Link</a>
                    <br>
                    <img src="https://surexport.es/es/wp-content/themes/SurExport/images/logo-home.png" alt="Logotipo Surexport">
                </body>
            </html>';

            // Cargar modulo PHPMailer
            $mail = new PHPMailer();

            // Configuración del servidor
            $mail->CharSet = 'UTF-8';
            // $mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

            // Credenciales de la cuenta de correo
            $mail->Username = 'comunicadorrhh@surexport.com';
            $mail->Password = 'Ducu545130';

            // Correo de envio a correo de trabajador
            $mail->setFrom('comunicadorrhh@surexport.com', 'Surexport');
            $mail->addAddress($info_contacto, $nombre);
            $mail->addCC($mail_usu_web);

            // Contenido mensaje
            $mail->isHTML(true); // Formato del correo HTML
            $mail->Subject = 'Llamamiento Surexport S.L.';
            $mail->Body = $mensaje;

            // Intentar enviar el correo
            if (!$mail->send()) {
                error_log("Error al enviar el correo: " . $mail->ErrorInfo);
                return false;
            }
            return true;
        }
    }



    //Llamamiento por telefono
    public function reg_llamada($pernr, $tipo_llamamiento, $pre_contacto, $fecha_llamamiento, $info_contacto, $estado, $motivo, $descripcion, $justificante, $id_usuario, $id_remesa, $ano_remesa, $fecha_registro)
    {

        $fecha_llamamiento = date('Y-m-d H:i:s', strtotime($fecha_llamamiento));
        $fecha_registro = date('Y-m-d H:i:s'); // actual, si aplica

        // Conectar a la base de datos
        $conn = $this->ConectarAppReclutamiento();
        $sql = "INSERT INTO webphp_registros_llamamientos (PERNR, TIPO_LLAMAMIENTO, INFO_CONTACTO, FECHA_REGISTRO, ESTADO, MOTIVO, DESCRIPCION, JUSTIFICANTE, ID_USUARIO, ID_REMESA, ANO_REMESA, FECHA_LLAMAMIENTO) 
                VALUES (
                '" . $pernr . "',  
                '" . $tipo_llamamiento . "', 
                '" . $pre_contacto . $info_contacto . "', 
                '" . $fecha_registro . "',
                '" . $estado . "',
                '" . $motivo . "',
                '" . $descripcion . "',
                '" . $justificante . "',
                '" . $id_usuario . "',
                '" . $id_remesa . "', 
                '" . $ano_remesa . "',
                '" . $fecha_llamamiento . "'
                );";
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === false) {
            return false;
        } else {
            return true;
        }
    }



    //Datos de los registros de llamamiento por trabajador
    public function llamamientos_trabajador($pernr)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT wrl.[ID],
                    wrl.[FECHA_REGISTRO],
                    wrl.[PERNR],
                    wrl.[TIPO_LLAMAMIENTO],
                    wrl.[FECHA_LLAMAMIENTO],
                    wrl.[INFO_CONTACTO],
                    wrl.[ESTADO],
                    wrl.[MOTIVO],
                    wmp.desc_motivo,
                    wrl.DESCRIPCION,
                    wrl.JUSTIFICANTE,
                    wrl.[ID_USUARIO],
                    wrl.[ID_REGISTRO_RELACION],
                    (SELECT CONCAT(nombre, ' ', apellidos)
                    FROM [192.168.200.202].[SUREXPORT_WEBAPP].[dbo].[webphp_Usuarios] wu
                    WHERE wu.id = wrl.ID_USUARIO) AS NOMBRE_USUARIO,
                    (SELECT COUNT(*)
                    FROM [webphp_registros_llamamientos] AS child
                    WHERE child.ID_REGISTRO_RELACION = wrl.ID) AS NUM_RELACIONES,
                    wrl.NUM_ENVIO,
                    wrl.MSG_ENVIO
                FROM [webphp_registros_llamamientos] wrl
                LEFT JOIN webphp_motivos_pendiente  wmp
                    ON wmp.id_motivo = wrl.MOTIVO
                WHERE PERNR = '$pernr'
                AND (elim IS NULL
                    OR elim <> '1')
                ORDER BY FECHA_REGISTRO DESC";


        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            echo $sql;
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Registros de llamamiento para la pagina Registros
    public function registros_llamamiento($txt_pernr, $txt_nombre, $estado, $tipo_llama, $desde, $hasta, $filtros)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;

                SELECT 
                    a.[ID],
                    a.[FECHA_REGISTRO],
                    a.[PERNR],
                    a.[TIPO_LLAMAMIENTO],
                    a.[FECHA_LLAMAMIENTO],
                    a.[INFO_CONTACTO],
                    a.[ESTADO],
                    a.[MOTIVO],
                    a.[ID_USUARIO],
                    a.[ID_REGISTRO_RELACION],
                    a.NUM_ENVIO,
                    a.MSG_ENVIO,
                    (SELECT CONCAT(nombre,' ',apellidos) 
                        FROM [192.168.200.202].[" . ConfigWebApp::$bdsrx_nombre . "].[dbo].[webphp_Usuarios]
                        WHERE id = a.[ID_USUARIO]) AS NOMBRE_USUARIO,
                    (SELECT COUNT(*) 
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].webphp_registros_llamamientos AS child 
                        WHERE child.ID_REGISTRO_RELACION = a.[ID]) AS NUM_RELACIONES,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBRE))) AS NOMBRE,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.APELLIDO1))) AS APELLIDO1,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.APELLIDO2))) AS APELLIDO2
                FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] a
                LEFT JOIN [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0002] p 
                    ON a.PERNR = p.PERNR
                WHERE 1=1
                AND (a.elim IS NULL OR a.elim <> '1')";

        // Aplicar los filtros solo si hay valores no vacíos
        if ($txt_pernr != '') {
            $sql .= " AND p.PERNR LIKE '%" . $txt_pernr . "%'";
        }

        if ($filtros == 'sin_respuesta') {
            $sql .= " AND a.NUM_ENVIO = '2'
                    AND a.FECHA_REGISTRO < DATEADD(DAY, -5, GETDATE())
                    AND a.FECHA_REGISTRO >= DATEADD(MONTH, -1, GETDATE())
                    AND a.ESTADO IN ('0', '3')
                    AND NOT EXISTS (
                        SELECT 1 
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] AS child
                        WHERE child.ID_REGISTRO_RELACION = a.ID
                            AND (child.elim IS NULL OR child.elim <> '1')
                    )
                    AND NOT EXISTS (
                        SELECT 1
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] AS r2
                        WHERE r2.PERNR = a.PERNR
                            AND (r2.elim IS NULL OR r2.elim <> '1')
                            AND r2.ESTADO = 2
                            AND r2.FECHA_REGISTRO >= a.FECHA_REGISTRO
                    )
                    AND NOT EXISTS (
                        SELECT 1
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] AS r3
                        WHERE r3.PERNR = a.PERNR
                            AND r3.NUM_ENVIO = 2
                            AND r3.ESTADO IN (0, 3)
                            AND (r3.elim IS NULL OR r3.elim <> '1')
                            AND r3.FECHA_REGISTRO > a.FECHA_REGISTRO
                    )";
        }

        if ($estado != '') {
            $sql .= " AND ESTADO = '" . $estado . "'";
        }
        if ($tipo_llama != '') {
            $sql .= " AND TIPO_LLAMAMIENTO = '" . $tipo_llama . "'";
        }
        if ($desde != '' && $hasta != '') {
            $sql .= " AND FECHA_REGISTRO BETWEEN '" . $desde . " 00:00:00' AND '" . $hasta . " 23:59:59'";
        }

        $sql .= " ORDER BY FECHA_REGISTRO DESC
        CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            echo $sql;
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Actualizar estado del llamamiento si se acepta el llamamiento
    public function update_estado_llama_aceptar($id, $estado, $fecha, $descripcion, $justificante, $id_remesa, $ano_remesa)
    {
        // Conexión a la base de datos
        $conn = $this->ConectarAppReclutamiento();

        // Consulta para obtener los datos del registro existente
        $tsql_select = "SELECT * FROM webphp_registros_llamamientos WHERE ID = $id";
        $query_select = sqlsrv_query($conn, $tsql_select);

        // Si el registro existe, insertamos el nuevo registro
        if ($registro = sqlsrv_fetch_array($query_select, SQLSRV_FETCH_ASSOC)) {
            // Extraer los datos existentes del registro
            $id_registro = $registro['ID'];
            $pernr = $registro['PERNR'];
            $tipo_llamamiento = $registro['TIPO_LLAMAMIENTO'];
            $info_contacto = $registro['INFO_CONTACTO'];
            $id_persona = $_SESSION["id_user_surexport_appreclu"];
            $id_remesa = $registro['ID_REMESA'];
            $ano_remesa = $registro['ANO_REMESA'];

            // Insertar un nuevo registro con los datos existentes más los nuevos valores
            $tsql_insert = "INSERT INTO webphp_registros_llamamientos (PERNR, TIPO_LLAMAMIENTO, INFO_CONTACTO, FECHA_REGISTRO, ESTADO, DESCRIPCION, ID_REGISTRO_RELACION, ID_USUARIO, ID_REMESA, ANO_REMESA, JUSTIFICANTE)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params_insert = array($pernr, $tipo_llamamiento, $info_contacto, $fecha, $estado, $descripcion, $id_registro, $id_persona, $id_remesa, $ano_remesa, $justificante);
            $insert_result = sqlsrv_query($conn, $tsql_insert, $params_insert);

            if ($insert_result) {
                $resultado = "Llamamiento actualizado.";
            } else {
                $resultado = "Error al actualizar el llamamiento.";
            }
        } else {
            $resultado = "El registro original no existe.";
        }
        return $resultado;
    }



    // Actualizar estado del llamamiento Rechazado o Pendiente
    public function update_estado_llama($id, $estado, $fecha, $motivo, $descripcion, $id_remesa, $ano_remesa, $justificante)
    {
        // Conexión a la base de datos
        $conn = $this->ConectarAppReclutamiento();

        // Consulta para obtener los datos del registro existente
        $tsql_select = "SELECT * FROM webphp_registros_llamamientos WHERE ID = ? AND (elim <> '1' OR elim IS NULL)";
        $params = array($id);
        $query_select = sqlsrv_query($conn, $tsql_select, $params);

        // Si el registro existe, insertamos el nuevo registro
        if ($registro = sqlsrv_fetch_array($query_select, SQLSRV_FETCH_ASSOC)) {
            // Extraer los datos existentes del registro
            $pernr = $registro['PERNR'];
            $tipo_llamamiento = $registro['TIPO_LLAMAMIENTO'];
            $info_contacto = $registro['INFO_CONTACTO'];
            $id_persona = $_SESSION["id_user_surexport_appreclu"];

            // Utilizar los parámetros proporcionados
            $id_remesa = $registro['ID_REMESA'];
            $ano_remesa = $registro['ANO_REMESA'];

            // Insertar un nuevo registro con los datos existentes más los nuevos valores
            $tsql_insert = "INSERT INTO webphp_registros_llamamientos (PERNR, TIPO_LLAMAMIENTO, INFO_CONTACTO, FECHA_REGISTRO, ESTADO, MOTIVO, DESCRIPCION, ID_REGISTRO_RELACION, ID_USUARIO, ID_REMESA, ANO_REMESA, JUSTIFICANTE)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params_insert = array($pernr, $tipo_llamamiento, $info_contacto, $fecha, $estado, $motivo, $descripcion, $id, $id_persona, $id_remesa, $ano_remesa, $justificante);
            $insert_result = sqlsrv_query($conn, $tsql_insert, $params_insert);

            if ($insert_result) {
                return true;
            } else {
                return false;
            }
        } else {
            return false; // Registro no encontrado
        }
    }



    //Actualizar Contacto del trabajador
    public function actualizar_contacto($PERNR, $MOVIL, $CORREO, $TELEMPRESA, $TELEMERGENCIAS, $PRE_TELF, $PRE_TELF_EMP, $PRE_TELG_EMER, $PARENT_TELF, $PARENT_TELF_EMP, $PARENT_TELF_EMER)
    {
        // Conectarse a la base de datos
        $conn = $this->conectarMuleSoft();

        // Crear la consulta SQL
        $fecha_in = date("Y-m-d H:i:s");
        $fecha_alta = date("Y-m-d");
        $sql = "INSERT INTO [PA0105] (PERNR, FECHA_IN, BEGDA, ENDDA, MOVIL, CORREO, TELEMPRESA, TELEMERGENCIAS, TIPO, PRE_TELF, PRE_TELF_EMP, PRE_TELF_EMER, PARENT_TELF, PARENT_TELF_EMP, PARENT_TELF_EMER) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'WEB', ?, ?, ?, ?, ?, ?)";

        // Preparar la consulta
        $params = array($PERNR, $fecha_in, $fecha_alta, '9999-12-31', $MOVIL, $CORREO, $TELEMPRESA, $TELEMERGENCIAS, $PRE_TELF, $PRE_TELF_EMP, $PRE_TELG_EMER, $PARENT_TELF, $PARENT_TELF_EMP, $PARENT_TELF_EMER);

        // Opciones para la consulta
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

        // Ejecutar la consulta
        $consulta = sqlsrv_query($conn, $sql, $params, $options);

        // Manejar errores y resultados
        if ($consulta === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        } else {
            return true;  // Devuelve true si la inserción fue exitosa
        }
    }



    // REMESAS
    private function ejecutarConsulta($conn, $sql, $params = [], $options = [])
    {
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta === FALSE) {
            $errors = sqlsrv_errors();
            $errorMessage = "";
            foreach ($errors as $error) {
                $errorMessage .= "SQLSTATE: " . $error['SQLSTATE'] . "\n";
                $errorMessage .= "Code: " . $error['code'] . "\n";
                $errorMessage .= "Message: " . $error['message'] . "\n";
            }
            die($errorMessage);
        }
        return $consulta;
    }



    // Funcion para guardar los resultados para las remesas
    private function obtenerResultados($consulta)
    {
        $resultado = [];
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Obtener llamamientos contestables de una remesa para respuesta masiva
    public function obtenerLlamamientosContestables($id_remesa, $ano_remesa)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;

                SELECT 
                    wrl.[ID],
                    wrl.[PERNR],
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                    wrl.[TIPO_LLAMAMIENTO],
                    wrl.[FECHA_REGISTRO],
                    wrl.[FECHA_LLAMAMIENTO],
                    wrl.[ESTADO],
                    wrl.[INFO_CONTACTO],
                    (SELECT COUNT(*) 
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] AS child 
                        WHERE child.ID_REGISTRO_RELACION = wrl.ID
                        AND (child.elim IS NULL OR child.elim <> '1')) AS NUM_RELACIONES
                FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] wrl
                LEFT JOIN [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0002] p 
                    ON wrl.PERNR = p.PERNR
                WHERE wrl.ID_REMESA = ?
                AND wrl.ANO_REMESA = ?
                AND wrl.ESTADO IN (0, 3)
                AND (wrl.elim IS NULL OR wrl.elim <> '1')
                AND (
                    (wrl.NUM_ENVIO = 1 AND DATEDIFF(HOUR, wrl.FECHA_REGISTRO, GETDATE()) <= 360) OR
                    (wrl.NUM_ENVIO = 2 AND DATEDIFF(HOUR, wrl.FECHA_REGISTRO, GETDATE()) <= 120) OR
                    ((wrl.NUM_ENVIO IS NULL OR wrl.NUM_ENVIO = 0 OR wrl.NUM_ENVIO NOT IN (1, 2)) AND DATEDIFF(HOUR, wrl.FECHA_REGISTRO, GETDATE()) <= 360)
                )
                AND NOT EXISTS (
                    SELECT 1
                    FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] AS child
                    WHERE child.ID_REGISTRO_RELACION = wrl.ID
                    AND (child.elim IS NULL OR child.elim <> '1')
                )
                ORDER BY wrl.FECHA_REGISTRO DESC;

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        // echo $sql;
        // die;

        $params = array($id_remesa, $ano_remesa);
        $consulta = sqlsrv_query($conn, $sql, $params);

        $resultado = array();
        if ($consulta == FALSE) {
            echo "Error en la consulta: ";
            print_r(sqlsrv_errors());
        } else {
            while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
                $resultado[] = $row;
            }
        }

        sqlsrv_close($conn);
        return $resultado;
    }



    // Mostrar todas las remesas disponibles
    public function Remesas_llama()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "WITH UltimosEstados AS (
                    -- Obtener el registro más reciente de cada trabajador por remesa
                    SELECT 
                        reg.ID_REMESA,
                        reg.ANO_REMESA,
                        reg.PERNR,
                        MAX(reg.FECHA_REGISTRO) AS FECHA_ULTIMO,
                        MAX(reg.ID) AS ID_ULTIMO
                    FROM webphp_registros_llamamientos reg
                    WHERE (reg.elim IS NULL OR reg.elim <> '1')
                    GROUP BY reg.ID_REMESA, reg.ANO_REMESA, reg.PERNR
                ),
                EstadosPorRemesa AS (
                    -- Recuperar el estado más reciente de cada trabajador en su respectiva remesa
                    SELECT 
                        ue.ID_REMESA,
                        ue.ANO_REMESA,
                        reg.PERNR,
                        reg.ESTADO
                    FROM UltimosEstados ue
                    INNER JOIN webphp_registros_llamamientos reg
                        ON reg.ID = ue.ID_ULTIMO
                    WHERE (reg.elim IS NULL OR reg.elim <> '1')

                    UNION ALL

                    -- Añadir trabajadores sin registros en webphp_registros_llamamientos
                    SELECT 
                        r.id_remesa,
                        YEAR(r.fecha_remesa) AS ano_remesa,
                        r.PERNR,
                        NULL AS ESTADO
                    FROM webphp_remesas_llamamientos r
                    WHERE NOT EXISTS (
                        SELECT 1 
                        FROM webphp_registros_llamamientos reg
                        WHERE reg.ID_REMESA = r.id_remesa
                        AND reg.PERNR = r.PERNR
                    )
                    AND (r.elim IS NULL OR r.elim <> '1')
                ),
                EstadoRemesas AS (
                    -- Determinar el estado general de cada remesa
                    SELECT 
                        r.id_remesa,
                        YEAR(r.fecha_remesa) AS ano_remesa,
                        -- Lógica para determinar el estado de la remesa
                        CASE 
                            -- Estado 3: Completada (todos los trabajadores tienen estado 1 o 2, y no hay pendientes)
                            WHEN NOT EXISTS (
                                SELECT 1
                                FROM EstadosPorRemesa epr
                                WHERE epr.ID_REMESA = r.id_remesa
                                AND epr.ANO_REMESA = YEAR(r.fecha_remesa)
                                AND (epr.ESTADO = 0 OR epr.ESTADO IS NULL)
                            )
                            AND EXISTS (
                                SELECT 1
                                FROM EstadosPorRemesa epr
                                WHERE epr.ID_REMESA = r.id_remesa
                                AND epr.ANO_REMESA = YEAR(r.fecha_remesa)
                                AND epr.ESTADO IN (1, 2)
                            ) THEN 3

                            -- Estado 1: En Proceso (al menos un trabajador en estado 0 o sin registros)
                            WHEN EXISTS (
                                SELECT 1
                                FROM EstadosPorRemesa epr
                                WHERE epr.ID_REMESA = r.id_remesa
                                AND epr.ANO_REMESA = YEAR(r.fecha_remesa)
                                AND (epr.ESTADO = 0 OR epr.ESTADO IS NULL)
                            ) THEN 1

                            -- Estado 2: Llamamientos sin respuesta (al menos un trabajador en estado 0 fuera del plazo de 15 días)
                            WHEN EXISTS (
                                SELECT 1
                                FROM EstadosPorRemesa epr
                                WHERE epr.ID_REMESA = r.id_remesa
                                AND epr.ANO_REMESA = YEAR(r.fecha_remesa)
                                AND epr.ESTADO = 0
                                AND DATEDIFF(day, r.fecha_remesa, GETDATE()) > 15
                            ) THEN 2

                            -- Estado 4: Remesa manual (sin trabajadores asociados)
                            WHEN NOT EXISTS (
                                SELECT 1
                                FROM EstadosPorRemesa epr
                                WHERE epr.ID_REMESA = r.id_remesa
                                AND epr.ANO_REMESA = YEAR(r.fecha_remesa)
                            ) THEN 4

                            -- Estado 5: Otros casos
                            ELSE 5 
                        END AS estado_remesa
                    FROM webphp_remesas_llamamientos r
                    WHERE (r.elim IS NULL OR r.elim <> '1')
                )
                -- Consulta final para recuperar los datos
                SELECT 
                    wrl.nombre_remesa, 
                    wrl.id_remesa, 
                    wrl.fecha_remesa, 
                    wrl.sms_auto, 
                    wrl.fecha_incorporacion,
                    YEAR(wrl.fecha_remesa) AS ano_remesa, 
                    COUNT(DISTINCT wrl.PERNR) AS trabajadores, 
                    er.estado_remesa 
                FROM webphp_remesas_llamamientos wrl
                LEFT JOIN EstadoRemesas er 
                    ON wrl.id_remesa = er.id_remesa 
                    AND YEAR(wrl.fecha_remesa) = er.ano_remesa
                WHERE (wrl.elim IS NULL OR wrl.elim <> '1')
                GROUP BY 
                    wrl.id_remesa, 
                    YEAR(wrl.fecha_remesa), 
                    wrl.nombre_remesa, 
                    wrl.fecha_remesa, 
                    er.estado_remesa, 
                    wrl.fecha_incorporacion,
                    wrl.sms_auto
                ORDER BY YEAR(wrl.fecha_remesa) DESC, wrl.id_remesa DESC;";

        // echo $sql;
        // die;
        $consulta = $this->ejecutarConsulta($conn, $sql);
        return $this->obtenerResultados($consulta);
    }



    // Mostrar la informacion de las remesas al acceder a ella
    public function InfoRemesa_llama($id, $ano)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG; 

                WITH 
                DatosUnicos AS ( 
                    SELECT 
                        w.id_remesa, 
                        YEAR(w.fecha_remesa) AS ano_remesa, 
                        w.nombre_remesa,
                        w.PERNR, 
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS, 
                        c.MOVIL, 
                        c.PREFIJO, 
                        LOWER(c.CORREO) as CORREO, 
                        CASE 
                            -- 1. Si no hay estados en ninguna tabla, asignar 5 (sin llamamiento)
                            WHEN registros_padre.ESTADO IS NULL AND ultimos_estados.ESTADO IS NULL THEN 5
                            
                            -- 2. Si el estado es 0 o 3, verificar si ha expirado el tiempo
                            WHEN COALESCE(ultimos_estados.ESTADO, registros_padre.ESTADO) IN (0, 3) THEN
                                CASE
                                    -- NUM_ENVIO = 1: límite 360 horas (15 días)
                                    WHEN COALESCE(ultimos_estados.NUM_ENVIO, registros_padre.NUM_ENVIO) = 1 
                                        AND DATEDIFF(HOUR, COALESCE(ultimos_estados.FECHA_REGISTRO, registros_padre.FECHA_REGISTRO), GETDATE()) > 360 THEN 4
                                    -- NUM_ENVIO = 2: límite 120 horas (5 días)
                                    WHEN COALESCE(ultimos_estados.NUM_ENVIO, registros_padre.NUM_ENVIO) = 2 
                                        AND DATEDIFF(HOUR, COALESCE(ultimos_estados.FECHA_REGISTRO, registros_padre.FECHA_REGISTRO), GETDATE()) > 120 THEN 4
                                    -- NUM_ENVIO = NULL, 0 u otro: límite 360 horas (15 días)
                                    WHEN (COALESCE(ultimos_estados.NUM_ENVIO, registros_padre.NUM_ENVIO) IS NULL 
                                        OR COALESCE(ultimos_estados.NUM_ENVIO, registros_padre.NUM_ENVIO) = 0 
                                        OR COALESCE(ultimos_estados.NUM_ENVIO, registros_padre.NUM_ENVIO) NOT IN (1, 2))
                                        AND DATEDIFF(HOUR, COALESCE(ultimos_estados.FECHA_REGISTRO, registros_padre.FECHA_REGISTRO), GETDATE()) > 360 THEN 4
                                    -- Si no se superó el tiempo, mantener el estado original (0 o 3)
                                    ELSE COALESCE(ultimos_estados.ESTADO, registros_padre.ESTADO)
                                END
                            
                            -- 3. Para cualquier otro estado (1=Aceptado, 2=Rechazado), retornar tal cual
                            WHEN ultimos_estados.ESTADO IS NOT NULL THEN ultimos_estados.ESTADO
                            WHEN registros_padre.ESTADO IS NOT NULL THEN registros_padre.ESTADO
                            
                            -- 4. Por defecto, estado desconocido
                            ELSE 5  
                        END as ESTADO, 
                        COALESCE(ultimos_estados.FECHA_REGISTRO, registros_padre.FECHA_REGISTRO) as FECHA_REGISTRO, 
                        ultimos_estados.ID_REGISTRO_RELACION, 
                        ROW_NUMBER() OVER ( PARTITION BY w.PERNR, w.id_remesa ORDER BY COALESCE(ultimos_estados.FECHA_REGISTRO, registros_padre.FECHA_REGISTRO) DESC ) as rn 
                    FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos] w 
                    LEFT JOIN [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0002] p ON w.PERNR = p.PERNR 
                    LEFT JOIN ( 
                        SELECT PERNR, MOVIL, PRE_TELF AS PREFIJO, CORREO
                        FROM (
                            SELECT 
                                PERNR, 
                                MOVIL, 
                                PRE_TELF, 
                                CORREO, 
                                ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY FECHA_IN DESC) AS rn
                            FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0105]
                        ) AS contacto
                        WHERE rn = 1
                    ) c ON w.PERNR = c.PERNR
                    LEFT JOIN (  
                        SELECT pernr, ID_REMESA, ESTADO, FECHA_REGISTRO, ID, NUM_ENVIO
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] 
                        WHERE ID_REGISTRO_RELACION IS NULL 
                        AND (elim IS NULL OR elim <> '1')
                    ) registros_padre ON w.PERNR = registros_padre.pernr 
                                    AND w.id_remesa = registros_padre.ID_REMESA 
                    LEFT JOIN ( 
                        SELECT pernr, ID_REMESA, ESTADO, FECHA_REGISTRO, ID_REGISTRO_RELACION, NUM_ENVIO
                        FROM (
                            SELECT *, ROW_NUMBER() OVER (PARTITION BY pernr, ID_REMESA ORDER BY FECHA_REGISTRO DESC) as rn 
                            FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registros_llamamientos] 
                            WHERE (elim IS NULL OR elim <> '1')
                        ) ranked 
                        WHERE rn = 1
                    ) ultimos_estados ON w.PERNR = ultimos_estados.pernr 
                                    AND w.id_remesa = ultimos_estados.ID_REMESA 
                    WHERE w.id_remesa = ? AND YEAR(w.fecha_remesa) = ? 
                    AND (w.elim IS NULL OR w.elim <> '1')
                )
                

                SELECT 
                    id_remesa, 
                    ano_remesa, 
                    nombre_remesa, 
                    PERNR, 
                    NOMBREYAPELLIDOS, 
                    MOVIL, 
                    PREFIJO, 
                    CORREO, 
                    ESTADO, 
                    FECHA_REGISTRO, 
                    ID_REGISTRO_RELACION 
                FROM DatosUnicos
                WHERE rn = 1 
                ORDER BY FECHA_REGISTRO DESC; 

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        // echo $sql;
        // die;
        $params = [$id, $ano];
        $consulta = $this->ejecutarConsulta($conn, $sql, $params);
        $resultado = $this->obtenerResultados($consulta);

        session_write_close();
        return $resultado;
    }



    // eliminar trabajador de una remesa
    public function EliminarTrabajadorRemesa($pernr, $id_remesa, $ano_remesa)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Verificar si el trabajador tienen un registro de llamamiento en esa remesa
        $sql_check = "SELECT COUNT(*) AS count 
                      FROM webphp_registros_llamamientos
                      WHERE PERNR = '$pernr' 
                        AND id_remesa = '$id_remesa' 
                        AND ano_remesa = '$ano_remesa' 
                        AND (elim IS NULL OR elim <> '1')";
        $consulta_check = $this->ejecutarConsulta($conn, $sql_check);
        $row_check = sqlsrv_fetch_array($consulta_check, SQLSRV_FETCH_ASSOC);

        // Si el trabajador tiene un registro de llamamiento, no se puede eliminar
        if ($row_check['count'] > 0) {
            return false; // No se puede eliminar porque hay un registro de llamamiento
        }

        $sql = "UPDATE webphp_remesas_llamamientos 
                SET elim = '1' 
                WHERE PERNR = '$pernr' 
                AND id_remesa = '$id_remesa' 
                AND YEAR(fecha_remesa) = '$ano_remesa'";

        $consulta = $this->ejecutarConsulta($conn, $sql);

        if ($consulta) {
            return true;
        } else {
            return false;
        }
    }



    // Añadir nueva remesa para llamamiento
    public function nuevaRemesa($nombre, $telefono, $fecha_ini, $sms)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Obtener el año actual (sin importar otras fechas)
        $ano_remesa = date('Y');

        // Consultar el máximo id_remesa del año actual
        $sql = "SELECT MAX(id_remesa) AS id_remesa FROM webphp_remesas_llamamientos WHERE YEAR(fecha_remesa) = ? AND (elim <> '1' OR elim IS NULL)";
        $consulta = $this->ejecutarConsulta($conn, $sql, [$ano_remesa], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);

        // Calcular el siguiente id_remesa
        $id_remesa = ($row && !is_null($row['id_remesa'])) ? $row['id_remesa'] + 1 : 1;

        // Insertar la nueva remesa
        $insert_result = $this->insertarRemesa($conn, $id_remesa, $nombre, $telefono, $fecha_ini, $sms, $ano_remesa);

        sqlsrv_close($conn);

        // Devolver un array con el resultado
        if ($insert_result) {
            return ['success' => true, 'message' => 'Remesa creada correctamente.'];
        } else {
            return ['success' => false, 'message' => 'Error al crear la remesa.'];
        }
    }



    // Insertar la nueva remesa, añadir el trabajador a la remesa y añadir el registro de llamamiento SMS
    public function insertarRemesa($conn, $id_remesa, $nombre_remesa, $telefono, $fecha_ini, $sms, $ano_remesa = null)
    {
        // Si no se proporciona el año, usar el año actual
        if ($ano_remesa === null) {
            $ano_remesa = date('Y');
        }

        // Obtener la fecha de la remesa existente
        $sql_fecha = "SELECT fecha_remesa FROM webphp_remesas_llamamientos WHERE id_remesa = ? AND YEAR(fecha_remesa) = ? AND (elim <> '1' OR elim IS NULL)";
        $params_fecha = [$id_remesa, $ano_remesa];
        $consulta_fecha = $this->ejecutarConsulta($conn, $sql_fecha, $params_fecha);
        $row_fecha = sqlsrv_fetch_array($consulta_fecha, SQLSRV_FETCH_ASSOC);

        // Establecer la fecha de remesa
        if ($row_fecha && isset($row_fecha['fecha_remesa'])) {
            $fecha_remesa = ($row_fecha['fecha_remesa'] instanceof DateTime)
                ? $row_fecha['fecha_remesa']->format('Y-m-d')
                : $row_fecha['fecha_remesa'];
        } else {
            // Si no existe la remesa, crear fecha con el año especificado
            $fecha_remesa = $date = date('Y-m-d');
        }

        // Fecha y hora actual para el registro
        $fecha_registro = date('Y-m-d H:i:s');

        // Para cada trabajador en el array
        foreach ($_POST['user_remesas'] as $pernr) {
            // Obtener datos del trabajador
            $sql_trabajador = "SELECT [PERNR]
                                    ,MAX([MOVIL]) AS MOVIL
                                    ,MAX([PRE_TELF]) AS PREFIJO
                                FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0105]
                                WHERE PERNR = ?
                                GROUP BY PERNR";
            $params_trabajador = [$pernr];
            $consulta_trabajador = $this->ejecutarConsulta($conn, $sql_trabajador, $params_trabajador);
            $datos_trabajador = sqlsrv_fetch_array($consulta_trabajador, SQLSRV_FETCH_ASSOC);

            if ($datos_trabajador) {
                // Insertar en la tabla de remesas
                $sql_remesa = "INSERT INTO webphp_remesas_llamamientos 
                              (id_remesa, fecha_remesa, nombre_remesa, PERNR, id_usuario_creacion, telefono_usuario, fecha_incorporacion, sms_auto)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $params_remesa = [
                    $id_remesa,
                    $fecha_remesa,
                    $nombre_remesa,
                    $pernr,
                    $_SESSION["id_user_surexport_appreclu"],
                    $telefono,
                    $fecha_ini,
                    $sms
                ];
                $this->ejecutarConsulta($conn, $sql_remesa, $params_remesa);

                if ($sms == '1') {
                    // Preparar datos para el registro de llamamiento
                    $tipo_llamamiento = 'SMS';
                    $estado = '0';
                    $info_contacto = $datos_trabajador['MOVIL'];
                    $pre_contacto = $datos_trabajador['PREFIJO'];
                    $status = '0';
                    $num_envio = '1';

                    // Insertar en la tabla de registros de llamamientos
                    $sql_registro = "INSERT INTO webphp_registros_llamamientos 
                                   (PERNR, TIPO_LLAMAMIENTO, FECHA_REGISTRO, 
                                    INFO_CONTACTO, ESTADO, ID_USUARIO, ID_REMESA, ANO_REMESA, STATUS_ENVIO, NUM_ENVIO) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $params_registro = [
                        $pernr,
                        $tipo_llamamiento,
                        $fecha_registro,
                        $pre_contacto . $info_contacto,
                        $estado,
                        $_SESSION["id_user_surexport_appreclu"],
                        $id_remesa,
                        $ano_remesa,
                        $status,
                        $num_envio
                    ];
                    $this->ejecutarConsulta($conn, $sql_registro, $params_registro);
                }
            }
        }

        return true;
    }



    // Añadir trabajador a la remesa seleccionada
    public function anadirCandidatosARemesa($id_remesa, $ano_remesa, $nombre_remesa = null, $telefono, $fecha_ini, $sms)
    {
        $conn = $this->ConectarAppReclutamiento();

        $sql = "SELECT nombre_remesa, fecha_incorporacion, sms_auto FROM webphp_remesas_llamamientos WHERE id_remesa = ? AND YEAR(fecha_remesa) = ? AND (elim <> '1' OR elim IS NULL)";
        $consulta = $this->ejecutarConsulta($conn, $sql, [$id_remesa, $ano_remesa]);
        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        $nombre_remesa = $row['nombre_remesa'];
        $fecha_ini = $row['fecha_incorporacion'];
        $sms = $row['sms_auto'];


        $insert_result = $this->insertarRemesa($conn, $id_remesa, $nombre_remesa, $telefono, $fecha_ini, $sms, $ano_remesa);

        sqlsrv_close($conn);

        // Devolver un array con el resultado
        if ($insert_result) {
            return ['success' => true, 'message' => 'Trabajador/es añadidos correctamente.'];
        } else {
            return ['success' => false, 'message' => 'Error al añadir trabajadores a la remesa.'];
        }
    }



    // RECLUTAMIENTO
    //Insertamos un nuevo candidato
    public function inserCandidato($app)
    {
        $conn = $this->ConectarAppReclutamiento();
        if ($app == 1) {
            //Consultamos que el id no esté ya en el sistema
            $sql = "select * FROM webphp_candidatos WHERE id='" . $_POST['ID'] . "'";
            $params = array();
            $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
            $consulta = sqlsrv_query($conn, $sql, $params, $options);
            if ($consulta == FALSE) {
                die($this->FormatErrors(sqlsrv_errors()));
            } else {
                if ((sqlsrv_num_rows($consulta) == 0) or (is_null($consulta))) {
                    //Consultamos si hay archivos que subir
                    $url_img = array();
                    if (isset($_FILES)) {
                        for ($i = 1; $i <= 5; $i++) {
                            if (isset($_FILES["FOTO" . $i]) and $_FILES["FOTO" . $i]["name"] != "") {
                                //Creamos la carpeta para almacenar las imágenes
                                $url_base = "img/candidatos/" . $_POST['ID'];
                                if (!file_exists($url_base)) {
                                    mkdir($url_base, 0777, true);
                                }
                                //url donde vamos a almacenar la imagen
                                $url_img[$i] = $url_base . "/";
                                $fileName = basename($_FILES["FOTO" . $i]["name"]);
                                $url_img[$i] .= date("YmdHis") . $fileName;
                                $fileType = pathinfo($url_img[$i], PATHINFO_EXTENSION);

                                // Permitimos solo unas extensiones
                                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'PNG', 'JPG');
                                if (in_array($fileType, $allowTypes)) {
                                    // Image temp source
                                    $imageTemp = $_FILES["FOTO" . $i]["tmp_name"];

                                    // Comprimos el fichero
                                    $compressedImage = $this->compressImage($imageTemp, $url_img[$i], 75);
                                    if ($compressedImage) {
                                        $img_upload = true;
                                    } else {
                                        $url_img[$i] = "";
                                        $img_upload = false;
                                    }
                                } else {
                                    $url_img[$i] = "";
                                    $img_upload = false;
                                }
                            } else {
                                $url_img[$i] = "";
                                $img_upload = true;
                            }
                        }
                    }

                    $tsql = "insert into webphp_candidatos (id, nombre, apellido1, apellido2, sexo, fecha_nac, lugar_nac, pais_nac, tipo_doc, valor_doc, tipo_doc_2, valor_doc_2, tipo_doc_3, valor_doc_3, num_hijos, nombre_padre, nombre_madre, nacionalidad, poblacion, cod_postal, sigla_via, calle, num_edificio, distrito, region, estado_civil, experiencia, cualificacion, observaciones, mail, pre_telf, telf, telf_2, sfsf, grupo_id, firma, foto1, foto2, foto3, foto4, foto5, id_usu, fecha, idioma, estado) output inserted.* values ('" . $_POST['ID'] . "','" . utf8_encode($_POST['NOMBRE']) . "','" . utf8_encode($_POST['APELLIDO1']) . "','" . utf8_encode($_POST['APELLIDO2']) . "','" . utf8_encode($_POST['SEXO']) . "','" . $_POST['FECHA_NACIMIENTO'] . "','" . utf8_encode($_POST['LUGAR_NACIMIENTO']) . "','" . utf8_encode($_POST['PAIS_NACIMIENTO']) . "','" . $_POST['TIPO_DOCUMENTO'] . "','" . utf8_encode($_POST['VALOR_DOCUMENTO']) . "','" . $_POST['TIPO_DOC2'] . "','" . $_POST['VALOR_DOC2'] . "','" . $_POST['TIPO_DOC3'] . "','" . $_POST['VALOR_DOC3'] . "','" . $_POST['NUMERO_HIJOS'] . "','" . utf8_encode($_POST['NOMBRE_PADRE']) . "','" . utf8_encode($_POST['NOMBRE_MADRE']) . "','" . utf8_encode($_POST['NACIONALIDAD']) . "','" . utf8_encode($_POST['POBLACION']) . "','" . $_POST['CODIGO_POSTAL'] . "','" . utf8_encode($_POST['SIGLA_VIA']) . "','" . utf8_encode($_POST['CALLE']) . "','" . utf8_encode($_POST['NUM_EDIFICIO']) . "','" . utf8_encode($_POST['DISTRITO']) . "','" . utf8_encode($_POST['REGION']) . "','" . utf8_encode($_POST['ESTADO_CIVIL'] . "','" . $_POST['EXPERIENCIA'] . "','" . $_POST['CUALIFICACION'] . "','" . $_POST['OBSERVACIONES'] . "','" . $_POST['MAIL']) . "','" . $_POST['PREFIJO'] . "','" . $_POST['TELEFONO'] . "','" . $_POST['TELEFONO2'] . "','" . $_POST['SFSF'] . "','" . $_POST['GRUPO_ID'] . "','" . $_POST['FIRMA'] . "','" . $url_img[1] . "','" . $url_img[2] . "','" . $url_img[3] . "','" . $url_img[4] . "','" . $url_img[5] . "','" . $_POST['ID_CREADOR'] . "','" . date("Y-m-d H:i:s") . "','" . $_POST['IDIOMA'] . "', 0)";
                    //Almacenamos el log de la base de datos
                    // $file = fopen("logbbdd.txt", "a");
                    // fwrite($file, date("Y-m-d H:i:s")." ----> ".$tsql.PHP_EOL);
                    // fclose($file);
                    //Final Log
                    $insertfila = sqlsrv_query($conn, $tsql);
                    if ($insertfila == TRUE) {
                        //Obtenemos la fila insertada utilizando la clausula output inserted.*
                        $fila = sqlsrv_fetch_array($insertfila, SQLSRV_FETCH_ASSOC);
                        $resultado = $fila['id'];
                    } else {
                        $resultado = false;
                    }
                } else {
                    //Si existe el registro comprobamos lo que devuelve
                    $fila = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                    //Almacenamos el log de la base de datos
                    $file = fopen("logbbdd.txt", "a");
                    if ($_POST['ID'] == $fila['id'] and $_POST['NOMBRE'] == $fila['nombre'] and $_POST['APELLIDO1'] == $fila['apellido1']) {
                        fwrite($file, date("Y-m-d H:i:s") . " ----> Error al consultar por ID RETURN TRUE: " . $fila['id'] . "--->" . $fila['nombre'] . "--->" . $fila['apellido1'] . PHP_EOL);
                        $resultado = true;
                    } else {
                        fwrite($file, date("Y-m-d H:i:s") . " ----> Error al consultar por ID RETURN FALSE: " . $fila['id'] . "--->" . $fila['nombre'] . "--->" . $fila['apellido1'] . PHP_EOL);
                        $resultado = false;
                    }
                    fclose($file);
                    //Final Log
                }
            }
        } else {
            //Consultamos el proximo id para el dispositivo web con el id 1
            $sql = "select max(id) as id FROM webphp_candidatos WHERE id<'1000000'";
            $params = array();
            $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
            $consulta = sqlsrv_query($conn, $sql, $params, $options);
            if ($consulta == FALSE) {
                die($this->FormatErrors(sqlsrv_errors()));
            } else {
                if ((sqlsrv_num_rows($consulta) == 0) or (is_null($consulta))) {
                    $id = 1;
                } else {
                    $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                    $id = $row['id'] + 1;
                }
            }
            $tsql = "insert into webphp_candidatos (id, nombre, apellido1, apellido2, sexo, fecha_nac, lugar_nac, pais_nac, tipo_doc, valor_doc, num_hijos, nombre_padre, nombre_madre, nacionalidad, poblacion, cod_postal, sigla_via, calle, num_edificio, distrito, region, estado_civil, experiencia, cualificacion, observaciones, mail, telf, sfsf, id_usu, fecha, estado) output inserted.* values ('" . $id . "','" . $_POST['nombre'] . "','" . $_POST['apellido1'] . "','" . $_POST['apellido2'] . "','" . $_POST['sexo'] . "','" . $_POST['fecha_nac'] . "','" . $_POST['lugar_nac'] . "','" . $_POST['pais_nac'] . "','" . $_POST['tipo_doc'] . "','" . $_POST['valor_doc'] . "','" . $_POST['num_hijos'] . "','" . $_POST['nombre_padre'] . "','" . $_POST['nombre_madre'] . "','" . $_POST['nacionalidad'] . "','" . $_POST['poblacion'] . "','" . $_POST['cod_postal'] . "','" . $_POST['sigla_via'] . "','" . $_POST['calle'] . "','" . $_POST['num_edificio'] . "','" . $_POST['distrito'] . "','" . $_POST['region'] . "','" . $_POST['estado_civil'] . "','" . $_POST['experiencia'] . "','" . $_POST['cualificacion'] . "','" . $_POST['observaciones'] . "','" . $_POST['mail'] . "','" . $_POST['telf'] . "','" . $_POST['sfsf'] . "','" . $_SESSION["id_user_surexport_appreclu"] . "','" . date("Y-m-d H:i:s") . "', 0)";
            $insertfila = sqlsrv_query($conn, $tsql);
            if ($insertfila == TRUE) {
                //Obtenemos la fila insertada utilizando la clausula output inserted.*
                $fila = sqlsrv_fetch_array($insertfila, SQLSRV_FETCH_ASSOC);
                $resultado = $fila['id'];
            } else {
                $resultado = false;
            }
        }
        return ($resultado);
    }



    //Update candidato
    public function updateCandidato()
    {
        $conn = $this->ConectarAppReclutamiento();
        //Consultamos si hay archivos que subir
        $url_img = array();
        if (isset($_FILES)) {
            if (isset($_FILES["img"]) and $_FILES["img"]["name"] != "") {
                //Creamos la carpeta para almacenar las imágenes
                $url_base = "img/candidatos/" . $_POST['ID'];
                if (!file_exists($url_base)) {
                    mkdir($url_base, 0777, true);
                }
                //url donde vamos a almacenar la imagen
                $url_img = $url_base . "/";
                // File info 
                $fileName = basename($_FILES["img"]["name"]);
                $url_img .= date("YmdHis") . $fileName;
                $fileType = pathinfo($url_img, PATHINFO_EXTENSION);

                // Permitimos solo unas extensiones
                $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'PNG', 'JPG');
                if (in_array($fileType, $allowTypes)) {
                    // Image temp source 
                    $imageTemp = $_FILES["img"]["tmp_name"];

                    // Comprimos el fichero
                    $compressedImage = $this->compressImage($imageTemp, $url_img, 75);
                    if ($compressedImage) {
                        $img_upload = true;
                    } else {
                        $url_img = "";
                        $img_upload = false;
                    }
                } else {
                    $url_img = "";
                    $img_upload = false;
                }
            } else {
                $url_img = "";
                $img_upload = false;
            }
        }
        $sql_upd = "update webphp_candidatos set nombre='" . $_POST['NOMBRE'] . "', apellido1='" . $_POST['APELLIDO1'] . "', apellido2='" . $_POST['APELLIDO2'] . "', sexo='" . $_POST['SEXO'] . "', fecha_nac='" . $_POST['FECHA_NACIMIENTO'] . "', lugar_nac='" . $_POST['LUGAR_NACIMIENTO'] . "', pais_nac='" . $_POST['PAIS_NACIMIENTO'] . "', tipo_doc='" . $_POST['TIPO_DOCUMENTO'] . "', valor_doc='" . $_POST['VALOR_DOCUMENTO'] . "',tipo_doc_2='" . $_POST['TIPO_DOCUMENTO_2'] . "', valor_doc_2='" . $_POST['VALOR_DOCUMENTO_2'] . "',tipo_doc_3='" . $_POST['TIPO_DOCUMENTO_3'] . "', valor_doc_3='" . $_POST['VALOR_DOCUMENTO_3'] . "', num_hijos='" . $_POST['NUMERO_HIJOS'] . "', nombre_padre='" . $_POST['NOMBRE_PADRE'] . "', nombre_madre='" . $_POST['NOMBRE_MADRE'] . "', nacionalidad='" . $_POST['NACIONALIDAD'] . "', poblacion='" . $_POST['POBLACION'] . "', cod_postal='" . $_POST['CODIGO_POSTAL'] . "', sigla_via='" . $_POST['SIGLA_VIA'] . "', calle='" . $_POST['CALLE'] . "', num_edificio='" . $_POST['NUM_EDIFICIO'] . "', distrito='" . $_POST['DISTRITO'] . "', region='" . $_POST['REGION'] . "', estado_civil='" . $_POST['ESTADO_CIVIL'] . "', experiencia='" . $_POST['EXPERIENCIA'] . "', cualificacion='" . $_POST['CUALIFICACION'] . "', observaciones='" . $_POST['OBSERVACIONES'] . "', mail='" . $_POST['MAIL'] . "', telf='" . $_POST['TELEFONO'] . "', sfsf='" . $_POST['SFSF'] . "', estado='" . $_POST['estado'] . "'";
        //Comprobamos si tiene alguna imagen que actualizar
        if ($url_img != "" and $img_upload == true and $_POST['tipo_img'] != 0) {
            $sql_upd .= ", foto" . $_POST['tipo_img'] . "='" . $url_img . "'";
        }
        //Añadimos el id del usuario a actualizar
        $sql_upd .= " where id=" . $_POST['ID'];
        $updatefila = sqlsrv_query($conn, $sql_upd);
        if ($updatefila == TRUE) {
            $resultado = true;
        } else {
            $resultado = false;
        }
        return ($resultado);
    }



    //Mostramos el listado de candidatos
    public function buscarCandidatos()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "select id, nombre, apellido1, apellido2, tipo_doc, valor_doc, sexo from webphp_candidatos where id is not null ";
        if ((isset($_POST['txt_bus']) and $_POST['txt_bus'] != "") || (isset($_POST['grupo']) and $_POST['grupo'] != "") || (isset($_POST['estado']) and $_POST['estado'] != "")) {
            if ($_POST['txt_bus'] != "") {
                $sql .= "and (nombre like '%" . $_POST['txt_bus'] . "%' or apellido1 like '%" . $_POST['txt_bus'] . "%' or apellido2 like '%" . $_POST['txt_bus'] . "%' or valor_doc like '%" . $_POST['txt_bus'] . "%' or CONCAT(nombre,' ',apellido1,' ',apellido2) like '%" . $_POST['txt_bus'] . "%') ";
            }
            if ($_POST['grupo'] != "") {
                $sql .= "and grupo_id=" . $_POST['grupo'] . " ";
            }
            if ($_POST['estado'] != "") {
                $sql .= "and estado=" . $_POST['estado'] . " ";
            }
        }
        $sql .= "order by nombre, apellido1";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Obtenemos todos los datos de un candidato
    public function infoCandidato($id)
    {
        $conn = $this->ConectarAppReclutamiento();
        if ($conn === false) {
            die($this->FormatErrors(sqlsrv_errors()));
        }

        $sql = "SELECT * FROM webphp_candidatos WHERE id='" . $id . "'";

        // Ejecutar la consulta sin opciones
        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            var_dump(sqlsrv_errors()); // Mostrar los errores
            die("Error al ejecutar la consulta.");
        } else {
            // sqlsrv_num_rows no es compatible con el recurso de consulta de sqlsrv
            // Se puede comprobar si hay resultados usando sqlsrv_fetch_array
            if ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
                return $row;
            } else {
                return false;
            }
        }
    }



    //Eliminamos un candidato
    public function elimCandidato($id)
    {
        $conn = $this->ConectarAppReclutamiento();
        //Eliminamos todas las relaciones que tenga el usuario
        $sql_del_rel = "delete from webphp_relaciones_usuarios_2 where id_usuario='" . $id . "'";
        $fila_del_rel = sqlsrv_query($conn, $sql_del_rel);
        if ($fila_del_rel == TRUE) {
            //Eliminamos toda la información del usuario
            $sql_del_cand = "delete from webphp_candidatos where id='" . $id . "'";
            $fila_del_cand = sqlsrv_query($conn, $sql_del_cand);
            if ($fila_del_cand == TRUE) {
                //Eliminamos todo el contenido de su directorio
                $files = glob('img/candidatos/' . $id . '/*');
                foreach ($files as $file) {
                    if (is_file($file))
                        unlink($file);
                }
                //Eliminamos el directorio
                rmdir('img/candidatos/' . $id);
                $resultado = "Candidato eliminado correctamente.";
            } else {
                $resultado = "Ha ocurrido un error al eliminar el candidato, inténtelo de nuevo más tarde.";
            }
        } else {
            $resultado = "Ha ocurrido un error al eliminar el candidato, inténtelo de nuevo más tarde.";
        }
    }



    //Función para mostrar todos los paises
    public function Paises()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "select UPPER(PAIS_NAC) AS PAIS_NAC from webphp_nacionalidad order by PAIS_NAC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Función para mostrar todos los paises
    public function Nacionalidad()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "select UPPER(GENTILICIO_NAC) AS GENTILICIO_NAC  from webphp_nacionalidad order by GENTILICIO_NAC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Función para mostrar todos los grupos
    public function Grupos()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "select *, (select COUNT(id) from webphp_candidatos where grupo_id=webphp_grupos.id) as cont from webphp_grupos where (elim=0 or elim is null) order by nombre";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Insertamos un nuevo grupo
    public function inserGrupo($nombre, $descrip)
    {
        $conn = $this->ConectarAppReclutamiento();
        $tsql = "insert into webphp_grupos (nombre, descrip, elim) values ('" . $nombre . "','" . $descrip . "', 0)";
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila == TRUE) {
            $resultado = true;
        } else {
            $resultado = false;
        }
        return ($resultado);
    }



    //Función para eliminar un grupo
    public function eliminarGrupo($id)
    {
        $conn = $this->ConectarAppReclutamiento();
        $tsql = "update webphp_grupos set elim=1 where id=" . $id;
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila == TRUE) {
            $resultado = "Grupo eliminado correctamente.";
        } else {
            $resultado = "Ha ocurrido un error al eliminar el grupo, inténtelo de nuevo más tarde";
        }
        return $resultado;
    }



    //Obtenemos todos los datos de un grupo
    public function infoGrupo($id)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "select * FROM webphp_grupos WHERE id='" . $id . "'";
        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        } else {
            if ((sqlsrv_num_rows($consulta) == 0) or (is_null($consulta))) {
                return false;
            } else {
                $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                return $row;
            }
        }
    }



    //Función para actualizar la información de un grupo
    public function updateGrupo($id, $nombre, $descrip)
    {
        $conn = $this->ConectarAppReclutamiento();
        $tsql = "update webphp_grupos set nombre='" . $nombre . "', descrip='" . $descrip . "' where id='" . $id . "'";
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila == TRUE) {
            $resultado = "Grupo actualizado correctamente.";
        } else {
            $resultado = "Ha ocurrido un error al actualizar los datos del grupo, inténtelo de nuevo más tarde.";
        }
        return $resultado;
    }



    //Insertamos una nueva relación entre usuarios
    public function inserRelacion($id_relacion, $id_usuario)
    {
        $conn = $this->ConectarAppReclutamiento();
        //Comprobamos que esa relación no exista
        $sql = "select * FROM webphp_relaciones_usuarios_2 WHERE id_relacion='" . $id_relacion . "' and id_usuario='" . $id_usuario . "'";
        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        } else {
            if ((sqlsrv_num_rows($consulta) == 0) or (is_null($consulta))) {
                $tsql = "insert into webphp_relaciones_usuarios_2 (id_relacion, id_usuario, fecha) values ('" . $id_relacion . "','" . $id_usuario . "','" . date("Y-m-d H:i:s") . "')";
                $insertfila = sqlsrv_query($conn, $tsql);
                if ($insertfila == TRUE) {
                    $resultado = true;
                } else {
                    $resultado = false;
                }
            } else {
                $resultado = true;
                //Almacenamos el log de la base de datos
                $file = fopen("logbbdd.txt", "a");
                fwrite($file, date("Y-m-d H:i:s") . " ---->Error Relacion DUPLICADA ID_RELACION: " . $id_relacion . "--->ID_USUARIO: " . $id_usuario . PHP_EOL);
                fclose($file);
                //Final Log
            }
        }
        return ($resultado);
    }



    //Función para mostrar todas las relaciones
    public function usuariosRelaciones()
    {
        $conn = $this->ConectarAppReclutamiento();
        //Primero mostramos los usuarios que si tienen relación
        $sql = "select id_relacion, id_usuario, 
        (select CONCAT(nombre, ' ', apellido1, ' ', apellido2) from webphp_candidatos where webphp_candidatos.id=rel_usu.id_usuario) as nombre_com,
        (select tipo_doc from webphp_candidatos where webphp_candidatos.id=rel_usu.id_usuario) as tipo_doc,
        (select valor_doc from webphp_candidatos where webphp_candidatos.id=rel_usu.id_usuario) as valor_doc,
        (select top(1) id_remesa from webphp_remesas where webphp_remesas.id_usuario=rel_usu.id_usuario order by fecha desc) as id_remesa,
        (select top(1) ano_remesa from webphp_remesas where webphp_remesas.id_usuario=rel_usu.id_usuario order by fecha desc) as ano_remesa,
        (select estado from webphp_candidatos where webphp_candidatos.id=rel_usu.id_usuario) as estado
        FROM [webphp_relaciones_usuarios_2] as rel_usu order by id_relacion, nombre_com";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        //Después mostramos los usuarios que no tienen ningún tipo de relación
        $sql_2 = "select id_relacion=null, id as id_usuario, CONCAT(nombre, ' ', apellido1, ' ', apellido2) as nombre_com, tipo_doc, valor_doc,
        (select top(1) id_remesa from webphp_remesas where webphp_remesas.id_usuario=webphp_candidatos.id order by fecha desc) as id_remesa,
        (select top(1) ano_remesa from webphp_remesas where webphp_remesas.id_usuario=webphp_candidatos.id order by fecha desc) as ano_remesa,
        estado
        from webphp_candidatos where id NOT IN (select distinct(id_usuario) from webphp_relaciones_usuarios_2) order by nombre_com";
        $consulta_2 = sqlsrv_query($conn, $sql_2);
        if ($consulta_2 == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row_2 = sqlsrv_fetch_array($consulta_2, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row_2;
        }
        //Devolvemos el resultado
        return $resultado;
    }



    //Función para generar remesas
    public function newRemesa()
    {
        $conn = $this->ConectarAppReclutamiento();
        //Buscamos el año si es mayor de diciembre
        if (date('n') == 12) {
            $ano_remesa = date('Y') + 1;
        } else {
            $ano_remesa = date('Y');
        }
        //Consultamos el maximo id de la remesa generada
        $sql = "select max(id_remesa) as id_remesa FROM webphp_remesas WHERE ano_remesa='" . $ano_remesa . "'";
        $params = array();
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        } else {
            if ((sqlsrv_num_rows($consulta) == 0) or (is_null($consulta))) {
                $id_remesa = 1;
            } else {
                $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                $id_remesa = $row['id_remesa'] + 1;
            }
        }
        //Creamos el array de resultados
        $array_candidatos = array();
        //Insertamos la remesa, distinguimos 3 estados de candidato: Pendiente->0, Presentado->1, Rechazado->2
        foreach ($_POST['user_remesas'] as $value) {
            $tsql = "insert into webphp_remesas (id_remesa, ano_remesa, id_usuario, fecha, id_usuario_creacion, estado) values ('" . $id_remesa . "','" . $ano_remesa . "','" . $value . "','" . date("Y-m-d H:i:s") . "','" . $_SESSION["id_user_surexport_appreclu"] . "',1)";
            $insertfila = sqlsrv_query($conn, $tsql);
            if ($insertfila == TRUE) {
                //Actualizamos el estado del candidato
                $sql_upd = "update webphp_candidatos set estado=1 where id=" . $value;
                $fila_upd = sqlsrv_query($conn, $sql_upd);
                //Si es correcto consultamos los datos para generar la remesa
                $array_candidatos[] = $this->infoCandidato($value);
            } else {
                die($this->FormatErrors(sqlsrv_errors()));
            }
        }
        //Mostramos el expediente en pdf
        /*$_SESSION['array_candidatos'] = $array_candidatos;
        session_write_close();
        ?>
        <script type="text/javascript">
            window.open("exportar.php?pdf", "_blank");
            window.open("exportar.php?excel", "_blank");
        </script>
        <?php */
    }



    //Función para añadir usuarios a una remesa
    public function addRemesa()
    {
        $conn = $this->ConectarAppReclutamiento();
        //Creamos el array de resultados
        $array_candidatos = array();
        //Insertamos la remesa, distinguimos 3 estados de candidato: Pendiente->0, Presentado->1, Rechazado->2
        foreach ($_POST['user_remesas'] as $value) {
            $tsql = "insert into webphp_remesas (id_remesa, ano_remesa, id_usuario, fecha, id_usuario_creacion, estado) values ('" . $_POST['id_remesa'] . "','" . $_POST['ano_remesa'] . "','" . $value . "','" . date("Y-m-d H:i:s") . "','" . $_SESSION["id_user_surexport_appreclu"] . "',1)";
            $insertfila = sqlsrv_query($conn, $tsql);
            if ($insertfila == TRUE) {
                //Actualizamos el estado del candidato
                $sql_upd = "update webphp_candidatos set estado=1 where id=" . $value;
                $fila_upd = sqlsrv_query($conn, $sql_upd);
                //Si es correcto consultamos los datos para generar la remesa
                $array_candidatos[] = $this->infoCandidato($value);
            } else {
                die($this->FormatErrors(sqlsrv_errors()));
            }
        }
        //Mostramos el expediente en pdf
        $_SESSION['array_candidatos'] = $array_candidatos;
        session_write_close();
        ?>
        <script type="text/javascript">
            window.open("exportar.php?pdf", "_blank");
        </script>
        <?php
    }



    //Mostramos todas las remesas
    public function Remesas()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "select id_remesa, ano_remesa 
                from webphp_remesas 
                group by id_remesa, ano_remesa 
                order by id_remesa DESC, ano_remesa DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Mostramos todas la información de una remesa
    public function InfoRemesa($id, $ano)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "select webphp_remesas.id_remesa, webphp_remesas.ano_remesa, webphp_remesas.estado as estado_remesa, webphp_remesas.obser, webphp_remesas.id_usuario, webphp_candidatos.* 
                from webphp_remesas, webphp_candidatos 
                where webphp_remesas.id_remesa=" . $id . " 
                and webphp_remesas.ano_remesa=" . $ano . " 
                and webphp_remesas.id_usuario=webphp_candidatos.id";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        unset($_SESSION['array_candidatos']);
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        $_SESSION['array_candidatos'] = $resultado;
        session_write_close();
        return $resultado;
    }



    //Eliminamos un candidato de una remesa
    public function elimCandidatoRemesa($id, $ano, $id_can)
    {
        $conn = $this->ConectarAppReclutamiento();
        //Eliminamos el usuario de la remesa, distinguimos 3 estados de candidato: Pendiente->0, Presentado->1, Rechazado->2
        $sql_del_rel = "delete from webphp_remesas where id_remesa='" . $id . "' and ano_remesa='" . $ano . "' and id_usuario='" . $id_can . "'";
        $fila_del_rel = sqlsrv_query($conn, $sql_del_rel);
        if ($fila_del_rel == TRUE) {
            //Actualizamos el usuario para poder ser añadido en otra remesa
            $sql_del_cand = "update webphp_candidatos set estado='0' where id=" . $id_can;
            $fila_del_cand = sqlsrv_query($conn, $sql_del_cand);
            if ($fila_del_cand == TRUE) {
                $resultado = "Candidato eliminado correctamente de la remesa.";
            } else {
                $resultado = "Ha ocurrido un error al eliminar el candidato, inténtelo de nuevo más tarde.";
            }
        } else {
            $resultado = "Ha ocurrido un error al eliminar el candidato, inténtelo de nuevo más tarde.";
        }
    }



    //Rechazamos un candidato de una remesa
    public function RechazarCandidatoRemesa($id, $ano, $id_can, $motivo)
    {
        $conn = $this->ConectarAppReclutamiento();
        //Rechazamos el usuario de la remesa, distinguimos 3 estados de candidato: Pendiente->0, Presentado->1, Rechazado->2
        $sql_del_rel = "update webphp_remesas set estado=2, obser='" . $motivo . "', id_usuario_estado='" . $_SESSION["id_user_surexport_appreclu"] . "', fecha_estado='" . date("Y-m-d H:i:s") . "' where id_remesa='" . $id . "' and ano_remesa='" . $ano . "' and id_usuario='" . $id_can . "'";
        $fila_del_rel = sqlsrv_query($conn, $sql_del_rel);
        if ($fila_del_rel == TRUE) {
            //Actualizamos el usuario para poder ser añadido en otra remesa
            $sql_del_cand = "update webphp_candidatos set estado='2' where id=" . $id_can;
            $fila_del_cand = sqlsrv_query($conn, $sql_del_cand);
            if ($fila_del_cand == TRUE) {
                $resultado = "Candidato rechazado correctamente.";
            } else {
                $resultado = "Ha ocurrido un error al rechazar el candidato, inténtelo de nuevo más tarde.";
            }
        } else {
            $resultado = "Ha ocurrido un error al rechazar el candidato, inténtelo de nuevo más tarde.";
        }
    }



    // DISPOSITIVOS
    // Lista de dispositivos
    public function dispositivos()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT 
                    d.[id],
                    d.[id_dispositivo],
                    d.[nombre] AS nombre_dispositivo,
                    d.[ubicacion],
                    d.[presencia],
                    d.[activo],
                    u.[sede],
                    u.[nombre] AS nombre_ubicacion
                FROM 
                    [webphp_dispositivos] d
                LEFT JOIN 
                    [webphp_ubicaciones_dispo] u
                    ON d.[ubicacion] = u.[id]
                WHERE 
                    d.[presencia] = 1
                ORDER BY 
                    d.[nombre]";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Informacion de los dispositivos para actualizarlos
    public function infoDispositivo($id)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT d.[id]
                      ,d.[id_dispositivo]
                      ,d.[nombre]
                      ,d.[ubicacion]
                      ,d.[presencia]
                      ,d.[activo]
                      ,u.[sede] 
                      ,u.[nombre] AS nombre_ubi
                      ,u.latitud
                      ,u.longitud
                FROM [webphp_dispositivos] d
                LEFT JOIN 
                    [webphp_ubicaciones_dispo] u
                    ON d.ubicacion = u.id
                WHERE d.id = $id";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();

        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Actualizar el dispositivo, activar o desactivar
    public function updateDispositivo($id, $nombre, $estado)
    {
        $conn = $this->ConectarAppReclutamiento();

        $sql = "UPDATE webphp_dispositivos SET activo = ?, nombre = ? WHERE id = ?";
        $params = array($estado, $nombre, $id);
        $stmt = sqlsrv_prepare($conn, $sql, $params);

        if ($stmt === false) {
            return false;
        }

        $success = sqlsrv_execute($stmt);
        sqlsrv_free_stmt($stmt);

        return $success; // Retorna true o false
    }



    // Eliminar dispositivo
    public function eliminarDispositivo($id)
    {
        $conn = $this->ConectarAppReclutamiento();

        $sql = "DELETE FROM webphp_dispositivos WHERE id = ?";
        $params = array($id);
        $stmt = sqlsrv_prepare($conn, $sql, $params);

        if ($stmt === false) {
            return false;
        }

        $success = sqlsrv_execute($stmt);
        sqlsrv_free_stmt($stmt);

        return $success;
    }



    // Añadir dispositivo
    //Insertamos un nuevo dispositivo
    public function añadir_dispositivo($id, $nombre, $ubicacion)
    {
        $conn = $this->ConectarAppReclutamiento();
        $tsql = "insert into webphp_dispositivos (id_dispositivo, nombre, ubicacion, presencia, activo) 
                 values ('" . $id . "', '" . $nombre . "', '" . $ubicacion . "', 1, 1)";
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila == TRUE) {
            $resultado = true;
        } else {
            $resultado = false;
        }
        return ($resultado);
    }



    // Listado de ubicaciones
    public function ubicaciones()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT [id]
                      ,[sede]
                      ,[nombre]
                FROM [webphp_ubicaciones_dispo]";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Insertamos nueva ubicacion
    public function añadir_ubicacion($nombre, $ubicacion)
    {
        $conn = $this->ConectarAppReclutamiento();
        $tsql = "insert into webphp_ubicaciones_dispo (sede, nombre) 
                 values ('" . $nombre . "', '" . $ubicacion . "')";
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila == TRUE) {
            $resultado = true;
        } else {
            $resultado = false;
        }
        return ($resultado);
    }



    //Función para ver los datos del usuario en mi perfil
    public function datos_usu($id)
    {
        $conn = $this->ConectarWebApp();
        $sql = "SELECT * FROM webphp_Usuarios WHERE id = '" . $id . "'";
        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Función controlar la trazabilidad del acceso al link de la carta del llamamiento
    public function trazabilidad_llama($pernr, $fecha, $num_llama, $ip)
    {
        // Conexión a la base de datos
        $conn = $this->ConectarAppReclutamiento();

        // Escapar el hash MD5 recibido
        $pernr = str_replace("'", "''", $pernr);

        // Consultar el valor de PERNR original basado en el hash MD5
        $sqlSelect = "
            SELECT TOP 1 PERNR
            FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].PA0002
            WHERE HASHBYTES('MD5', CONVERT(VARCHAR(50), PERNR)) = 0x$pernr;
        ";

        // Ejecutar la consulta para obtener el PERNR
        $result = sqlsrv_query($conn, $sqlSelect);

        if ($result === false) {
            // Manejar errores de la consulta
            die("Error al ejecutar la consulta SELECT: " . print_r(sqlsrv_errors(), true));
        }

        // Obtener el PERNR de la consulta
        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        if (!$row || !isset($row['PERNR'])) {
            die("No se encontraron registros para el PERNR proporcionado.");
        }

        // Guardar el valor de PERNR en una variable
        $pernrDescifrado = $row['PERNR'];

        // Escapar otros valores dinámicos
        $fecha = str_replace("'", "''", $fecha);
        $num_llama = (int) $num_llama; // Asegurar que sea un entero
        $ip = str_replace("'", "''", $ip);

        // Construir la consulta de inserción
        $sqlInsert = "
            INSERT INTO webphp_trazabilidad_llama (
                pernr,
                fecha_acceso,
                numero_llamamiento,
                ip_acceso
            )
            VALUES (
                '$pernrDescifrado',
                '$fecha',
                $num_llama,
                '$ip'
            );
        ";

        // Ejecutar la consulta de inserción
        $consulta = sqlsrv_query($conn, $sqlInsert);

        // Validar si la consulta fue exitosa
        if ($consulta === false) {
            die("Error al ejecutar la consulta INSERT: " . print_r(sqlsrv_errors(), true));
        }

        return true;
    }



    // Cartas llamamientos del trabajador
    public function datos_trab_carta($pernr)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG; 

                    SELECT DISTINCT
                        p.PERNR, 
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS, 
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.DNI))) AS DNI, 
                        (SELECT TOP 1 MOVIL 
                        FROM PA0105 
                        WHERE HASHBYTES('MD5', CONVERT(VARCHAR(50), PERNR)) = HASHBYTES('MD5', CONVERT(VARCHAR(50), p.PERNR))
                        ORDER BY MOVIL DESC) AS MOVIL,
                        r.fecha_incorporacion,
                        r.fecha_remesa,
                        r.id_usuario_creacion,
                        u.telf,
                        u.usr_login,
                        u.nombre,
                        u.apellidos
                    FROM PA0002 p
                    LEFT JOIN [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos] r 
                        ON HASHBYTES('MD5', CONVERT(VARCHAR(50), p.PERNR)) = HASHBYTES('MD5', CONVERT(VARCHAR(50), r.PERNR))
                    LEFT JOIN [192.168.200.202].[" . ConfigWebApp::$bdsrx_nombre . "].[dbo].[webphp_Usuarios] u 
                        ON r.id_usuario_creacion = u.id
                    WHERE HASHBYTES('MD5', p.PERNR) = 0x" . $pernr . "
                    AND (r.fecha_remesa = (
                        SELECT MAX(fecha_remesa) 
                        FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_remesas_llamamientos] 
                        WHERE HASHBYTES('MD5', CONVERT(VARCHAR(50), PERNR)) = HASHBYTES('MD5', CONVERT(VARCHAR(50), p.PERNR))
                    ) OR r.fecha_remesa IS NULL)

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Total trabajadores 1A (sin ausencia vigente y en alta real)
    public function trabajadores_1A($fecha)
    {

        $conn = $this->conectarMuleSoft();
        // $sql = "WITH UltimaMedida AS (
        //             SELECT 
        //                 p0.PERNR,
        //                 p0.STAT2,
        //                 p0.BEGDA,
        //                 ROW_NUMBER() OVER (PARTITION BY p0.PERNR ORDER BY p0.BEGDA DESC) AS rn
        //             FROM [PA0000] p0
        //             WHERE p0.BEGDA <= '$fecha'
        //         ),
        //         UltimaAusencia AS (
        //             SELECT 
        //                 pa201.PERNR, 
        //                 pa201.ID, 
        //                 pa201.BEGDA, 
        //                 pa201.ENDDA,
        //                 ROW_NUMBER() OVER (PARTITION BY pa201.PERNR ORDER BY pa201.ID DESC) AS rn
        //             FROM [PA2001] pa201
        //         ),
        //         Trabajadores_1A AS (
        //             SELECT 
        //                 pa.PERNR AS A1_PERNR
        //             FROM [PA_ACTIVOS] pa
        //             INNER JOIN UltimaMedida um
        //                 ON pa.PERNR = um.PERNR
        //                 AND um.rn = 1
        //                 AND um.STAT2 = '3'
        //             LEFT JOIN UltimaAusencia pa201 
        //                 ON pa.PERNR = pa201.PERNR 
        //                 AND pa201.rn = 1 
        //                 -- AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
        //                 AND '$fecha' BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
        //             WHERE pa.PERSK = '1A'
        //             AND pa.STAT2 = '3'
        //             AND pa.ZZWERKS = '1000'
        //             AND pa201.PERNR IS NULL
        //         )

        //         SELECT COUNT(A1_PERNR) AS total_trabajadores
        //         FROM Trabajadores_1A;";



        $sql = "WITH UltimaMedida AS (
                    SELECT 
                        p0.PERNR,
                        p0.STAT2,
                        p0.BEGDA,
                        ROW_NUMBER() OVER (PARTITION BY p0.PERNR ORDER BY p0.BEGDA DESC) AS rn
                    FROM [PA0000] p0
                    WHERE p0.BEGDA <= '$fecha'
                ),
                UltimaAusencia AS (
                    SELECT 
                        pa201.PERNR, 
                        pa201.ID, 
                        pa201.BEGDA, 
                        pa201.ENDDA,
                        ROW_NUMBER() OVER (PARTITION BY pa201.PERNR ORDER BY pa201.ID DESC) AS rn
                    FROM [PA2001] pa201
                ),
                Trabajadores_1A AS (
                    SELECT 
                        pa.PERNR AS A1_PERNR
                    FROM [PA_ACTIVOS] pa
                    INNER JOIN UltimaMedida um
                        ON pa.PERNR = um.PERNR
                        AND um.rn = 1
                        AND um.STAT2 = '3'
                    LEFT JOIN UltimaAusencia pa201 
                        ON pa.PERNR = pa201.PERNR 
                        AND pa201.rn = 1 
                        AND '$fecha' BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
                    LEFT JOIN [" . ConfigPortalEmpleado::$bdsrx_nombre . "].[dbo].[webphp_ausencias] wa
                        ON pa.PERNR = wa.pernr
                        AND wa.estado = 3
                        AND '$fecha' BETWEEN wa.fecha_desde AND wa.fecha_hasta
                    WHERE pa.PERSK = '1A'
                    AND pa.STAT2 = '3'
                    AND pa.ZZWERKS = '1000'
                    AND pa201.PERNR IS NULL
                    AND wa.pernr IS NULL  -- Excluye trabajadores con ausencias aprobadas
                )
                SELECT COUNT(A1_PERNR) AS total_trabajadores
                FROM Trabajadores_1A;";

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row['total_trabajadores'];
    }



    // Total trabajadores 9A (sin ausencia vigente y en alta real)
    public function trabajadores_9A($fecha)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "WITH UltimaMedida AS (
                    SELECT 
                        p0.PERNR,
                        p0.STAT2,
                        p0.BEGDA,
                        ROW_NUMBER() OVER (PARTITION BY p0.PERNR ORDER BY p0.BEGDA DESC) AS rn
                    FROM [PA0000] p0
                    -- WHERE p0.BEGDA <= CONVERT(DATE, '$fecha')
                    WHERE p0.BEGDA <= '$fecha'
                ),
                UltimaAusencia AS (
                    SELECT 
                        pa201.PERNR, 
                        pa201.ID, 
                        pa201.BEGDA, 
                        pa201.ENDDA,
                        ROW_NUMBER() OVER (PARTITION BY pa201.PERNR ORDER BY pa201.ID DESC) AS rn
                    FROM [PA2001] pa201
                ),
                Trabajadores_9A AS (
                    SELECT 
                        pa.PERNR AS A9_PERNR
                    FROM [PA_ACTIVOS] pa
                    INNER JOIN UltimaMedida um
                        ON pa.PERNR = um.PERNR
                        AND um.rn = 1
                        AND um.STAT2 = '3'
                    LEFT JOIN UltimaAusencia pa201 
                        ON pa.PERNR = pa201.PERNR 
                        AND pa201.rn = 1 
                        -- AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
                        AND '$fecha' BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
                    WHERE pa.PERSK = '9A'
                    AND pa.STAT2 = '3'
                    AND pa.ZZWERKS = '1000'
                    AND pa201.PERNR IS NULL
                )

                SELECT COUNT(A9_PERNR) AS total_trabajadores
                FROM Trabajadores_9A;
            ";

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row['total_trabajadores'];
    }



    // Total trabajadores 1E (sin ausencia vigente y en alta real)
    public function trabajadores_1E($fecha)
    {
        $conn = $this->conectarMuleSoft();
        $sql = "WITH UltimaMedida AS (
                    SELECT 
                        p0.PERNR,
                        p0.STAT2,
                        p0.BEGDA,
                        ROW_NUMBER() OVER (PARTITION BY p0.PERNR ORDER BY p0.BEGDA DESC) AS rn
                    FROM [PA0000] p0
                    -- WHERE p0.BEGDA <= CONVERT(DATE, '$fecha')
                    WHERE p0.BEGDA <= '$fecha'
                ),
                UltimaAusencia AS (
                    SELECT 
                        pa201.PERNR, 
                        pa201.ID, 
                        pa201.BEGDA, 
                        pa201.ENDDA,
                        ROW_NUMBER() OVER (PARTITION BY pa201.PERNR ORDER BY pa201.ID DESC) AS rn
                    FROM [PA2001] pa201
                ),
                Trabajadores_1E AS (
                    SELECT 
                        pa.PERNR AS E1_PERNR
                    FROM [PA_ACTIVOS] pa
                    INNER JOIN UltimaMedida um
                        ON pa.PERNR = um.PERNR
                        AND um.rn = 1
                        AND um.STAT2 = '3'
                    LEFT JOIN UltimaAusencia pa201 
                        ON pa.PERNR = pa201.PERNR 
                        AND pa201.rn = 1 
                        -- AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
                        AND '$fecha' BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
                    WHERE pa.PERSK = '1E'
                    AND pa.STAT2 = '3'
                    AND pa.ZZWERKS = '1000'
                    AND pa201.PERNR IS NULL
                )

                SELECT COUNT(E1_PERNR) AS total_trabajadores
                FROM Trabajadores_1E;
            ";

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row['total_trabajadores'];
    }





    // Trabajadores 1A que tienen presencia en la fecha

    public function trabajadores_presencia($fecha, $tipo)
    {

        $conn = $this->ConectarAppReclutamiento();
        $fecha_mas_uno = date('Y-m-d', strtotime($fecha . ' +1 day'));

        // $sql = "SELECT 
        //             COUNT(DISTINCT wh.pernr) as total_trabajadores
        //         FROM [webphp_registro_horario] wh
        //         LEFT JOIN [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA_ACTIVOS] pa
        //             ON pa.PERNR = wh.PERNR 
        //         WHERE fecha LIKE '{$fecha}%'
        //         AND pa.PERSK = '$tipo'
        //         AND wh.tipo_reg = 'entrada'";
    
        $sql = "SELECT 
                    COUNT(DISTINCT wh.pernr) as total_trabajadores
                FROM [webphp_registro_horario] wh
                LEFT JOIN [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA_ACTIVOS] pa
                    ON pa.PERNR = wh.PERNR 
                LEFT JOIN [" . ConfigPortalEmpleado::$bdsrx_nombre . "].[dbo].[webphp_ausencias] wa
                    ON wh.pernr = wa.pernr
                    AND wa.estado = 3
                    AND '$fecha' BETWEEN wa.fecha_desde AND wa.fecha_hasta
                WHERE fecha LIKE '{$fecha}%'
                AND pa.PERSK = '$tipo'
                AND wh.tipo_reg = 'entrada'
                AND wa.pernr IS NULL";

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row['total_trabajadores'];
    }



    // Trabajadores que han registrado presencia en la fecha
    // public function trabajadores_conta($fecha, $tipo, $filtroAsistencia, $buscador)
    // {
    //     $conn = $this->conectarMuleSoft();
    //     $fecha_mas_uno = date('Y-m-d', strtotime($fecha . ' +1 day'));

    //     $sql = "
    //         OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
    //         DECRYPTION BY CERTIFICATE CertificadoPA_REG;

    //         WITH UltimaMedida AS (
    //             SELECT 
    //                 p0.PERNR,
    //                 p0.STAT2,
    //                 p0.BEGDA,
    //                 ROW_NUMBER() OVER (PARTITION BY p0.PERNR ORDER BY p0.BEGDA DESC) AS rn
    //             FROM [PA0000] p0
    //             WHERE p0.BEGDA <= CONVERT(DATE, '$fecha')
    //         ),
    //         UltimaAusencia AS (
    //             SELECT 
    //                 ID,       
    //                 PERNR,     
    //                 BEGDA, 
    //                 ENDDA,
    //                 ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY ID DESC) AS rn
    //             FROM [PA2001]
    //         ),
    //         Trabajadores_Activos AS (
    //             SELECT 
    //                 pa.PERNR AS A1_PERNR,
    //                 pa.PERSK,
    //                 pa.NOMBREYAPELLIDOS
    //             FROM [PA_ACTIVOS] pa
    //             INNER JOIN UltimaMedida um
    //                 ON pa.PERNR = um.PERNR
    //                 AND um.rn = 1
    //                 AND um.STAT2 = '3'
    //             LEFT JOIN UltimaAusencia pa201 
    //                 ON pa.PERNR = pa201.PERNR 
    //                 AND pa201.rn = 1
    //                 AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
    //             WHERE pa.STAT2 = '3'
    //             AND pa.PERSK = '$tipo'
    //             AND pa.ZZWERKS = '1000'
    //             AND pa201.PERNR IS NULL
    //         ),
    //         Trabajadores_QueHanRegistrado AS (
    //             SELECT 
    //                 DISTINCT pernr
    //             FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registro_horario] 
    //             WHERE fecha BETWEEN '$fecha 02:51:00' AND '$fecha_mas_uno 02:50:59'
    //         )

    //         SELECT 
    //             t1.A1_PERNR,
    //             t1.NOMBREYAPELLIDOS,
    //             t1.PERSK,  
    //             CASE 
    //                 WHEN t2.pernr IS NOT NULL THEN '1' 
    //                 ELSE '0'
    //             END AS Estado
    //         FROM Trabajadores_Activos t1
    //         LEFT JOIN Trabajadores_QueHanRegistrado t2
    //             ON t1.A1_PERNR = t2.pernr
    //         WHERE 1 = 1";

    //     if (isset($filtroAsistencia) && $filtroAsistencia === '1') {
    //         $sql .= " AND t2.pernr IS NOT NULL"; // Solo aquellos que han registrado presencia
    //     } elseif (isset($filtroAsistencia) && $filtroAsistencia === '0') {
    //         $sql .= " AND t2.pernr IS NULL"; // Solo aquellos que no han registrado presencia
    //     } elseif (isset($filtroAsistencia) && $filtroAsistencia === 'todos') {

    //     }

    //     if (isset($buscador) && $buscador != '') {
    //         $sql .= " AND (t1.NOMBREYAPELLIDOS LIKE '%$buscador%' OR t1.A1_PERNR LIKE '%$buscador%')";
    //     }

    //     $sql .= " ORDER BY Estado, t1.A1_PERNR;

    //         CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;
    //     ";

    //     $consulta = sqlsrv_query($conn, $sql);

    //     if ($consulta === false) {
    //         die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
    //     }

    //     $resultado = [];
    //     while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
    //         $resultado[] = $row;
    //     }

    //     return $resultado;
    // }

    public function trabajadores_conta($fecha, $tipo, $filtroAsistencia, $buscador)
    {
        $conn = $this->conectarMuleSoft();
        $fecha_mas_uno = date('Y-m-d', strtotime($fecha . ' +1 day'));
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;
                WITH UltimaMedida AS (
                    SELECT 
                        p0.PERNR,
                        p0.STAT2,
                        p0.BEGDA,
                        ROW_NUMBER() OVER (PARTITION BY p0.PERNR ORDER BY p0.BEGDA DESC) AS rn
                    FROM [PA0000] p0
                    WHERE p0.BEGDA <= CONVERT(DATE, '$fecha')
                ),
                UltimaAusencia AS (
                    SELECT 
                        ID,       
                        PERNR,     
                        BEGDA, 
                        ENDDA,
                        ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY ID DESC) AS rn
                    FROM [PA2001]
                ),
                Trabajadores_Activos AS (
                    SELECT 
                        pa.PERNR AS A1_PERNR,
                        pa.PERSK,
                        pa.NOMBREYAPELLIDOS
                    FROM [PA_ACTIVOS] pa
                    INNER JOIN UltimaMedida um
                        ON pa.PERNR = um.PERNR
                        AND um.rn = 1
                        AND um.STAT2 = '3'
                    LEFT JOIN UltimaAusencia pa201 
                        ON pa.PERNR = pa201.PERNR 
                        AND pa201.rn = 1
                        AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND ISNULL(pa201.ENDDA, '9999-12-31')
                    LEFT JOIN [".ConfigPortalEmpleado::$bdsrx_nombre."].[dbo].[webphp_ausencias] wa
                        ON pa.PERNR = wa.pernr
                        AND wa.estado = 3
                        AND CONVERT(DATE, '$fecha') BETWEEN wa.fecha_desde AND wa.fecha_hasta
                    WHERE pa.STAT2 = '3'
                    AND pa.PERSK = '$tipo'
                    AND pa.ZZWERKS = '1000'
                    AND pa201.PERNR IS NULL
                    AND wa.pernr IS NULL
                ),
                Trabajadores_QueHanRegistrado AS (
                    SELECT 
                        DISTINCT pernr
                    FROM [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_registro_horario] 
                    WHERE fecha BETWEEN '$fecha 02:51:00' AND '$fecha_mas_uno 02:50:59'
                )
                SELECT 
                    t1.A1_PERNR,
                    t1.NOMBREYAPELLIDOS,
                    t1.PERSK,  
                    CASE 
                        WHEN t2.pernr IS NOT NULL THEN '1' 
                        ELSE '0'
                    END AS Estado
                FROM Trabajadores_Activos t1
                LEFT JOIN Trabajadores_QueHanRegistrado t2
                    ON t1.A1_PERNR = t2.pernr
                WHERE 1 = 1";
        if (isset($filtroAsistencia) && $filtroAsistencia === '1') {
            $sql .= " AND t2.pernr IS NOT NULL"; // Solo aquellos que han registrado presencia
        } elseif (isset($filtroAsistencia) && $filtroAsistencia === '0') {
            $sql .= " AND t2.pernr IS NULL"; // Solo aquellos que no han registrado presencia
        } elseif (isset($filtroAsistencia) && $filtroAsistencia === 'todos') {
        }
        if (isset($buscador) && $buscador != '') {
            $sql .= " AND (t1.NOMBREYAPELLIDOS LIKE '%$buscador%' OR t1.A1_PERNR LIKE '%$buscador%')";
        }
        $sql .= " ORDER BY Estado, t1.A1_PERNR;
                  CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;
                ";
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }
        $resultado = [];
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    public function grupos_horario()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT id, nombre_grupo, descripcion_grupo, franjas_json, grupo_predeterminado, 
                       fecha_creacion, anio_configuracion
                FROM webphp_grupos_horarios 
                ORDER BY grupo_predeterminado DESC, id DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }


    public function nuevo_grupo_horario($nombre_grupo, $descripcion, $franjas_json, $grupo_predeterminado, $anio_configuracion, $fecha_creacion)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Si se marca como predeterminado, quitamos el predeterminado solo a los grupos del mismo año
        if ($grupo_predeterminado == 1) {
            $tsql_reset = "UPDATE webphp_grupos_horarios SET grupo_predeterminado = 0 WHERE anio_configuracion = ?";
            $params_reset = array($anio_configuracion);
            sqlsrv_query($conn, $tsql_reset, $params_reset);
        }

        $tsql = "INSERT INTO webphp_grupos_horarios (nombre_grupo, descripcion_grupo, franjas_json, grupo_predeterminado, anio_configuracion, fecha_creacion)
                 VALUES ('$nombre_grupo', '$descripcion', '$franjas_json', $grupo_predeterminado, $anio_configuracion, '$fecha_creacion')";


        $insertfila = sqlsrv_query($conn, $tsql);

        if ($insertfila === false) {
            return false;
        }

        return true;
    }



    public function obtenerGrupoHorarioPorId($id)
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT *
                FROM webphp_grupos_horarios 
                WHERE id = ?";
        $params = array($id);
        $consulta = sqlsrv_query($conn, $sql, $params);

        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }

        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row ? $row : null;
    }

    // Función para editar un grupo de horario existente
    public function editar_grupo_horario($id, $nombre_grupo, $descripcion, $franjas_json, $grupo_predeterminado)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Si se marca como predeterminado, quitamos el predeterminado solo a los grupos del mismo año
        if ($grupo_predeterminado == 1) {
            // Primero obtenemos el año de configuración del grupo que se está editando
            $tsql_anio = "SELECT anio_configuracion FROM webphp_grupos_horarios WHERE id = ?";
            $params_anio = array($id);
            $stmt_anio = sqlsrv_query($conn, $tsql_anio, $params_anio);

            if ($stmt_anio && $row = sqlsrv_fetch_array($stmt_anio, SQLSRV_FETCH_ASSOC)) {
                $anio_configuracion = $row['anio_configuracion'];

                // Quitamos el predeterminado solo a los grupos del mismo año (excluyendo el actual)
                $tsql_reset = "UPDATE webphp_grupos_horarios SET grupo_predeterminado = 0 WHERE anio_configuracion = ? AND id != ?";
                $params_reset = array($anio_configuracion, $id);
                sqlsrv_query($conn, $tsql_reset, $params_reset);
            }
        }

        $tsql = "UPDATE webphp_grupos_horarios
                 SET nombre_grupo = ?,
                     descripcion_grupo = ?,
                     franjas_json = ?,
                     grupo_predeterminado = ?
                 WHERE id = ?";

        $params = array(
            $nombre_grupo,
            $descripcion,
            $franjas_json,
            $grupo_predeterminado,
            $id
        );

        $update = sqlsrv_query($conn, $tsql, $params);

        if ($update === false) {
            return false;
        }

        return true;
    }

    // Función para eliminar un grupo de horario
    public function eliminar_grupo_horario($id)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Iniciar transacción
        sqlsrv_begin_transaction($conn);

        try {
            // Verificar que no sea el grupo predeterminado antes de eliminar
            $sql_check = "SELECT grupo_predeterminado FROM webphp_grupos_horarios WHERE id = ?";
            $params_check = array($id);
            $consulta = sqlsrv_query($conn, $sql_check, $params_check);

            if ($consulta === false) {
                throw new Exception("Error al verificar grupo predeterminado");
            }

            $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
            if ($row && $row['grupo_predeterminado'] == 1) {
                // No permitir eliminar el grupo predeterminado
                sqlsrv_rollback($conn);
                sqlsrv_close($conn);
                return false;
            }

            // Primero, eliminar todos los trabajadores asignados a este grupo
            $sql_delete_trabajadores = "DELETE FROM [webphp_trabajadores_grupos_horario] WHERE grupo_horario_id = ?";
            $params_trabajadores = array($id);
            $delete_trabajadores = sqlsrv_query($conn, $sql_delete_trabajadores, $params_trabajadores);

            if ($delete_trabajadores === false) {
                throw new Exception("Error al eliminar trabajadores del grupo");
            }

            // Luego, eliminar el grupo de horario
            $tsql = "DELETE FROM webphp_grupos_horarios WHERE id = ?";
            $params = array($id);
            $delete = sqlsrv_query($conn, $tsql, $params);

            if ($delete === false) {
                throw new Exception("Error al eliminar el grupo de horario");
            }

            // Confirmar transacción
            sqlsrv_commit($conn);
            sqlsrv_close($conn);
            return true;

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            sqlsrv_rollback($conn);
            sqlsrv_close($conn);
            return false;
        }
    }



    // Función para obtener el grupo de horario predeterminado
    public function obtener_grupo_predeterminado()
    {
        $conn = $this->ConectarAppReclutamiento();
        $sql = "SELECT * FROM webphp_grupos_horarios WHERE grupo_predeterminado = 1";
        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            return null;
        }

        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row ? $row : null;
    }



    // Función para establecer un grupo como predeterminado
    public function establecer_grupo_predeterminado($id)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Primero quitar predeterminado a todos
        $tsql_reset = "UPDATE webphp_grupos_horarios SET grupo_predeterminado = 0";
        sqlsrv_query($conn, $tsql_reset);

        // Establecer el nuevo predeterminado
        $tsql = "UPDATE webphp_grupos_horarios SET grupo_predeterminado = 1 WHERE id = ?";
        $params = array($id);
        $update = sqlsrv_query($conn, $tsql, $params);

        return $update !== false;
    }

    // Método para agregar o actualizar festivo en un grupo de horario
    public function agregar_festivo_grupo($grupo_id, $fecha, $tipo_festivo)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Validar tipo de festivo
        if (!in_array($tipo_festivo, ['festivo_nacional', 'festivo_autonomico'])) {
            return ['success' => false, 'message' => 'Tipo de festivo inválido'];
        }

        // Obtener el grupo actual
        $query = "SELECT franjas_json FROM webphp_grupos_horarios WHERE id = ?";
        $params = array($grupo_id);
        $result = sqlsrv_query($conn, $query, $params);

        if (!$result) {
            return ['success' => false, 'message' => 'Error al obtener el grupo'];
        }

        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

        if (!$row) {
            return ['success' => false, 'message' => 'Grupo no encontrado'];
        }

        // Decodificar franjas existentes
        $franjas = json_decode($row['franjas_json'], true);
        if (!is_array($franjas)) {
            $franjas = [];
        }

        // Verificar si ya existe un festivo en esa fecha
        $festivoExistente = false;
        foreach ($franjas as &$franja) {
            if ($franja['inicio_fecha'] === $fecha && $franja['fin_fecha'] === $fecha) {
                $esFestivo = (isset($franja['tipo_jornada']) &&
                    ($franja['tipo_jornada'] === 'festivo_nacional' ||
                        $franja['tipo_jornada'] === 'festivo_autonomico'));

                if ($esFestivo) {
                    // Actualizar el tipo de festivo existente
                    $franja['tipo_jornada'] = $tipo_festivo;
                    $festivoExistente = true;
                    break;
                }
            }
        }

        // Si no existe, agregar nueva franja de festivo
        if (!$festivoExistente) {
            $nuevaFranja = [
                'inicio_fecha' => $fecha,
                'fin_fecha' => $fecha,
                'tipo_jornada' => $tipo_festivo
                // No incluir dias_semana, horarios ni tiempos_gracia para festivos
            ];
            $franjas[] = $nuevaFranja;
        }

        // Codificar franjas actualizadas
        $franjas_json = json_encode($franjas);

        // Actualizar en la base de datos
        $updateQuery = "UPDATE webphp_grupos_horarios SET franjas_json = '$franjas_json' WHERE id = '$grupo_id'";

        $updateResult = sqlsrv_query($conn, $updateQuery);

        if (!$updateResult) {
            return ['success' => false, 'message' => 'Error al actualizar el grupo'];
        }

        return ['success' => true, 'message' => 'Festivo guardado correctamente'];
    }

    // Método para eliminar festivo de un grupo
    public function eliminar_festivo_grupo($grupo_id, $fecha)
    {
        $conn = $this->ConectarAppReclutamiento();

        // Obtener el grupo actual
        $query = "SELECT franjas_json FROM webphp_grupos_horarios WHERE id = ?";
        $params = array($grupo_id);
        $result = sqlsrv_query($conn, $query, $params);

        if (!$result) {
            return ['success' => false, 'message' => 'Error al obtener el grupo'];
        }

        $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

        if (!$row) {
            return ['success' => false, 'message' => 'Grupo no encontrado'];
        }

        // Decodificar franjas existentes
        $franjas = json_decode($row['franjas_json'], true);
        if (!is_array($franjas)) {
            return ['success' => false, 'message' => 'No hay franjas configuradas'];
        }

        // Buscar y eliminar el festivo en esa fecha
        $festivoEncontrado = false;
        $franjas_filtradas = [];

        foreach ($franjas as $franja) {
            // Verificar si es un festivo en la fecha especificada
            $esFestivoEnFecha = (
                $franja['inicio_fecha'] === $fecha &&
                $franja['fin_fecha'] === $fecha &&
                isset($franja['tipo_jornada']) &&
                ($franja['tipo_jornada'] === 'festivo_nacional' ||
                    $franja['tipo_jornada'] === 'festivo_autonomico')
            );

            if ($esFestivoEnFecha) {
                $festivoEncontrado = true;
                // No agregar esta franja al array filtrado (eliminación)
            } else {
                // Mantener esta franja
                $franjas_filtradas[] = $franja;
            }
        }

        if (!$festivoEncontrado) {
            return ['success' => false, 'message' => 'No se encontró un festivo en esa fecha'];
        }

        // Codificar franjas actualizadas
        $franjas_json = json_encode($franjas_filtradas);

        // Actualizar en la base de datos
        $updateQuery = "UPDATE webphp_grupos_horarios SET franjas_json = '$franjas_json' WHERE id = '$grupo_id'";
        $updateResult = sqlsrv_query($conn, $updateQuery);

        if (!$updateResult) {
            return ['success' => false, 'message' => 'Error al actualizar el grupo'];
        }

        return ['success' => true, 'message' => 'Festivo eliminado correctamente'];
    }

    // Método para clonar grupo de horario a otro año
    public function clonar_grupo_horario($grupo_id_original, $anio_destino, $nuevo_nombre, $clonar_trabajadores = false)
    {
        $conn = $this->ConectarAppReclutamiento();

        try {
            // Iniciar transacción
            sqlsrv_begin_transaction($conn);

            // 1. Obtener el grupo original
            $sqlGrupoOriginal = "SELECT * FROM webphp_grupos_horarios WHERE id = ?";
            $stmtOriginal = sqlsrv_prepare($conn, $sqlGrupoOriginal, array(&$grupo_id_original));
            sqlsrv_execute($stmtOriginal);
            $grupoOriginal = sqlsrv_fetch_array($stmtOriginal, SQLSRV_FETCH_ASSOC);
            sqlsrv_free_stmt($stmtOriginal);

            if (!$grupoOriginal) {
                throw new Exception('Grupo original no encontrado');
            }

            $anio_original = $grupoOriginal['anio_configuracion'];

            // 2. Verificar que el año destino sea diferente
            if ($anio_original == $anio_destino) {
                throw new Exception('El año destino debe ser diferente al año original');
            }

            // 3. Adaptar las franjas al nuevo año
            $franjas_originales = json_decode($grupoOriginal['franjas_json'], true);
            $franjas_nuevas = [];

            if (is_array($franjas_originales)) {
                foreach ($franjas_originales as $franja) {
                    $franja_nueva = $franja;

                    // Adaptar fechas al nuevo año
                    if (isset($franja['inicio_fecha'])) {
                        $fecha_inicio = new DateTime($franja['inicio_fecha']);
                        $mes_inicio = $fecha_inicio->format('m');
                        $dia_inicio = $fecha_inicio->format('d');
                        $franja_nueva['inicio_fecha'] = "$anio_destino-$mes_inicio-$dia_inicio";
                    }

                    if (isset($franja['fin_fecha'])) {
                        $fecha_fin = new DateTime($franja['fin_fecha']);
                        $mes_fin = $fecha_fin->format('m');
                        $dia_fin = $fecha_fin->format('d');
                        $franja_nueva['fin_fecha'] = "$anio_destino-$mes_fin-$dia_fin";
                    }

                    $franjas_nuevas[] = $franja_nueva;
                }
            }

            $franjas_json_nuevas = json_encode($franjas_nuevas);

            // 4. Insertar el nuevo grupo y obtener el ID en la misma operación
            $sqlInsertGrupo = "INSERT INTO webphp_grupos_horarios 
                              (nombre_grupo, descripcion_grupo, anio_configuracion, franjas_json, grupo_predeterminado) 
                              OUTPUT INSERTED.id
                              VALUES (?, ?, ?, ?, 0)";

            $paramsInsert = array(
                $nuevo_nombre,
                $grupoOriginal['descripcion_grupo'],
                $anio_destino,
                $franjas_json_nuevas
            );

            $stmtInsert = sqlsrv_prepare($conn, $sqlInsertGrupo, $paramsInsert);

            if (!$stmtInsert) {
                throw new Exception('Error al preparar inserción del nuevo grupo: ' . print_r(sqlsrv_errors(), true));
            }

            if (!sqlsrv_execute($stmtInsert)) {
                throw new Exception('Error al insertar el nuevo grupo: ' . print_r(sqlsrv_errors(), true));
            }

            // Obtener el ID del grupo recién insertado usando OUTPUT
            $rowInserted = sqlsrv_fetch_array($stmtInsert, SQLSRV_FETCH_ASSOC);
            $nuevo_grupo_id = intval($rowInserted['id']);

            sqlsrv_free_stmt($stmtInsert);

            if (!$nuevo_grupo_id || $nuevo_grupo_id <= 0) {
                throw new Exception('No se pudo obtener el ID del nuevo grupo creado');
            }

            // 5. Clonar trabajadores si se solicitó
            $trabajadores_clonados = 0;

            if ($clonar_trabajadores) {

                $sqlTrabajadores = "SELECT pernr, fecha_inicio, fecha_fin 
                                   FROM webphp_trabajadores_grupos_horario
                                   WHERE grupo_horario_id = ?";

                $stmtTrabajadores = sqlsrv_prepare($conn, $sqlTrabajadores, array(&$grupo_id_original));

                if (!$stmtTrabajadores) {
                    throw new Exception('Error al preparar consulta de trabajadores: ' . print_r(sqlsrv_errors(), true));
                }

                if (!sqlsrv_execute($stmtTrabajadores)) {
                    throw new Exception('Error al ejecutar consulta de trabajadores: ' . print_r(sqlsrv_errors(), true));
                }

                $sqlInsertTrabajador = "INSERT INTO webphp_trabajadores_grupos_horario
                                       (grupo_horario_id, pernr, fecha_inicio, fecha_fin, fecha_asignacion) 
                                       VALUES (?, ?, ?, ?, GETDATE())";

                while ($trabajador = sqlsrv_fetch_array($stmtTrabajadores, SQLSRV_FETCH_ASSOC)) {

                    $fecha_inicio_nueva = null;
                    $fecha_fin_nueva = null;

                    // Adaptar fechas de los trabajadores al nuevo año si existen
                    if ($trabajador['fecha_inicio'] instanceof DateTime) {
                        $mes = $trabajador['fecha_inicio']->format('m');
                        $dia = $trabajador['fecha_inicio']->format('d');
                        $fecha_inicio_nueva = "$anio_destino-$mes-$dia";
                    }

                    if ($trabajador['fecha_fin'] instanceof DateTime) {
                        $mes = $trabajador['fecha_fin']->format('m');
                        $dia = $trabajador['fecha_fin']->format('d');
                        $fecha_fin_nueva = "$anio_destino-$mes-$dia";
                    }

                    $paramsTrabajador = array(
                        $nuevo_grupo_id,
                        $trabajador['pernr'],
                        $fecha_inicio_nueva,
                        $fecha_fin_nueva
                    );

                    $stmtInsertTrabajador = sqlsrv_prepare($conn, $sqlInsertTrabajador, $paramsTrabajador);

                    if (!$stmtInsertTrabajador) {
                        continue;
                    }

                    if (sqlsrv_execute($stmtInsertTrabajador)) {
                        $trabajadores_clonados++;
                        sqlsrv_free_stmt($stmtInsertTrabajador);
                    }
                }

                sqlsrv_free_stmt($stmtTrabajadores);
            }

            // Confirmar transacción
            sqlsrv_commit($conn);
            sqlsrv_close($conn);

            $mensaje = "Grupo clonado correctamente al año $anio_destino";
            if ($clonar_trabajadores && $trabajadores_clonados > 0) {
                $mensaje .= " con $trabajadores_clonados trabajador(es) asignado(s)";
            }

            return [
                'success' => true,
                'message' => $mensaje,
                'nuevo_grupo_id' => $nuevo_grupo_id
            ];

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if (isset($conn)) {
                sqlsrv_rollback($conn);
                sqlsrv_close($conn);
            }

            return [
                'success' => false,
                'message' => 'Error al clonar el grupo: ' . $e->getMessage()
            ];
        }
    }

    // Método para obtener áreas de trabajo (PERSK)
    public function obtener_areas_trabajo()
    {
        try {
            $conn = $this->conectarMuleSoft();

            $sql = "SELECT [PERSK]
                    FROM [" . ConfigMuleSoft::$bdsrx_nombre . "].[dbo].[PA0001]
                    GROUP BY [PERSK]
                    ORDER BY [PERSK]";

            $stmt = sqlsrv_query($conn, $sql);

            if ($stmt === false) {
                return [];
            }

            $areas = array();
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $areas[] = array(
                    'id' => $row['PERSK'],
                    'text' => $row['PERSK']
                );
            }

            sqlsrv_free_stmt($stmt);
            return $areas;

        } catch (Exception $e) {
            return [];
        }
    }



    // Método para obtener tipos de contrato
    public function obtener_tipos_contrato()
    {
        try {
            $conn = $this->conectarEmpleado();

            $sql = "SELECT [clave], [descripcion], [tipo]
                    FROM [webphp_ma_contratos]
                    ORDER BY [clave]";

            $stmt = sqlsrv_query($conn, $sql);

            if ($stmt === false) {
                return [];
            }

            $contratos = array();
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $contratos[] = array(
                    'id' => $row['clave'],
                    'text' => $row['clave'] . ' - ' . $row['descripcion'],
                    'tipo' => $row['tipo']
                );
            }

            sqlsrv_free_stmt($stmt);
            return $contratos;

        } catch (Exception $e) {
            return [];
        }
    }

    // Método para obtener trabajadores por áreas y tipos de contrato
    public function obtener_trabajadores_por_areas_y_contratos($areas, $contratos = null)
    {
        try {
            $conn = $this->conectarMuleSoft();

            // Abrir clave simétrica
            $openKey = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG DECRYPTION BY CERTIFICATE CertificadoPA_REG;";
            sqlsrv_query($conn, $openKey);

            // Construir la consulta base con DISTINCT para evitar duplicados
            $sql = "SELECT DISTINCT
                        [PERNR],
                        [NOMBREYAPELLIDOS],
                        [STAT2],
                        [ZZWERKS],
                        [DESC_CENTRO],
                        [PERSK],
                        [TIPO_CONTRATO],
                        [DESC_TIPO_CONTRATO]
                    FROM [PA_ACTIVOS]
                    WHERE [STAT2] = '3'
                      AND [ZZWERKS] NOT IN ('3001', '3000', '2000')";

            $params = array();
            $condiciones = array();

            // Filtro por áreas
            if (!empty($areas) && is_array($areas)) {
                $placeholders = implode(',', array_fill(0, count($areas), '?'));
                $condiciones[] = "[PERSK] IN ($placeholders)";
                $params = array_merge($params, $areas);
            }

            // Filtro por tipos de contrato
            if (!empty($contratos) && is_array($contratos)) {
                $placeholdersContratos = implode(',', array_fill(0, count($contratos), '?'));
                $condiciones[] = "[TIPO_CONTRATO] IN ($placeholdersContratos)";
                $params = array_merge($params, $contratos);
            }

            // Si hay condiciones, agruparlas con OR para que funcione con cualquiera de los filtros
            if (!empty($condiciones)) {
                $sql .= " AND (" . implode(' OR ', $condiciones) . ")";
            }

            $sql .= " ORDER BY PERNR, ZZWERKS";


            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                // Cerrar clave antes de retornar
                sqlsrv_query($conn, "CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;");
                return [];
            }

            $trabajadores = array();
            $trabajadoresUnicos = array(); // Para evitar duplicados por PERNR

            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $pernr = $row['PERNR'];

                // Solo agregar si no existe ya este PERNR
                if (!isset($trabajadoresUnicos[$pernr])) {
                    $trabajadoresUnicos[$pernr] = true;
                    $trabajadores[] = array(
                        'pernr' => $pernr,
                        'nombre' => $row['NOMBREYAPELLIDOS'] ? trim($row['NOMBREYAPELLIDOS']) : 'Desconocido',
                        'estado' => $row['STAT2'],
                        'centro' => $row['ZZWERKS'],
                        'area' => $row['PERSK'],
                        'desc_centro' => $row['DESC_CENTRO'],
                        'tipo_contrato' => $row['TIPO_CONTRATO'],
                        'desc_tipo_contrato' => $row['DESC_TIPO_CONTRATO']
                    );
                }
            }

            sqlsrv_free_stmt($stmt);

            // Cerrar clave simétrica
            sqlsrv_query($conn, "CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;");

            return $trabajadores;

        } catch (Exception $e) {
            return [];
        }
    }



    public function buscar_trabajadores_manual($termino)
    {
        try {
            $conn = $this->conectarMuleSoft();

            // Abrir clave simétrica
            $openKey = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG DECRYPTION BY CERTIFICATE CertificadoPA_REG;";
            sqlsrv_query($conn, $openKey);

            // Preparar el término de búsqueda
            $terminoBusqueda = '%' . $termino . '%';

            // Construir la consulta para buscar por PERNR o nombre/apellidos
            $sql = "SELECT DISTINCT
                        [PERNR],
                        [NOMBREYAPELLIDOS],
                        [STAT2],
                        [ZZWERKS],
                        [DESC_CENTRO],
                        [PERSK],
                        [TIPO_CONTRATO],
                        [DESC_TIPO_CONTRATO]
                    FROM [PA_ACTIVOS]
                    WHERE [STAT2] = '3'
                      AND [ZZWERKS] NOT IN ('3001', '3000', '2000')
                      AND ([PERNR] LIKE ? OR [NOMBREYAPELLIDOS] LIKE ?)
                    ORDER BY PERNR";

            $params = array($terminoBusqueda, $terminoBusqueda);
            $stmt = sqlsrv_query($conn, $sql, $params);

            if ($stmt === false) {
                // Cerrar clave antes de retornar
                sqlsrv_query($conn, "CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;");
                return [];
            }

            $trabajadores = array();
            $trabajadoresUnicos = array(); // Para evitar duplicados por PERNR

            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $pernr = $row['PERNR'];

                // Solo agregar si no existe ya este PERNR
                if (!isset($trabajadoresUnicos[$pernr])) {
                    $trabajadoresUnicos[$pernr] = true;
                    $trabajadores[] = array(
                        'pernr' => $pernr,
                        'nombre' => $row['NOMBREYAPELLIDOS'] ? trim($row['NOMBREYAPELLIDOS']) : 'Desconocido',
                        'estado' => $row['STAT2'],
                        'centro' => $row['ZZWERKS'],
                        'area' => $row['PERSK'],
                        'desc_centro' => $row['DESC_CENTRO'],
                        'tipo_contrato' => $row['TIPO_CONTRATO'],
                        'desc_tipo_contrato' => $row['DESC_TIPO_CONTRATO']
                    );
                }
            }

            sqlsrv_free_stmt($stmt);

            // Cerrar clave simétrica
            sqlsrv_query($conn, "CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;");

            return $trabajadores;

        } catch (Exception $e) {
            return [];
        }
    }

    // Método para guardar asignación de trabajadores a un grupo de horario
    public function guardar_asignacion_trabajadores($grupo_id, $trabajadores)
    {
        try {
            $conn = $this->ConectarAppReclutamiento();

            // Iniciar transacción
            sqlsrv_begin_transaction($conn);

            // Obtener el año del grupo actual
            $sqlGrupoActual = "SELECT anio_configuracion FROM [webphp_grupos_horarios] WHERE id = ?";
            $stmtGrupoActual = sqlsrv_prepare($conn, $sqlGrupoActual, array(&$grupo_id));
            sqlsrv_execute($stmtGrupoActual);
            $grupoActual = sqlsrv_fetch_array($stmtGrupoActual, SQLSRV_FETCH_ASSOC);
            $anioGrupoActual = $grupoActual['anio_configuracion'];
            sqlsrv_free_stmt($stmtGrupoActual);

            // Obtener trabajadores actualmente asignados al grupo
            $sqlAsignacionesActuales = "SELECT pernr, fecha_inicio, fecha_fin FROM [webphp_trabajadores_grupos_horario] WHERE grupo_horario_id = ?";
            $stmtActuales = sqlsrv_prepare($conn, $sqlAsignacionesActuales, array(&$grupo_id));
            sqlsrv_execute($stmtActuales);

            $asignacionesActuales = [];
            while ($row = sqlsrv_fetch_array($stmtActuales, SQLSRV_FETCH_ASSOC)) {
                $fecha_inicio_actual = $row['fecha_inicio'];
                $fecha_fin_actual = $row['fecha_fin'];

                // Convertir DateTime a string si es necesario
                if ($fecha_inicio_actual instanceof DateTime) {
                    $fecha_inicio_actual = $fecha_inicio_actual->format('Y-m-d');
                }
                if ($fecha_fin_actual instanceof DateTime) {
                    $fecha_fin_actual = $fecha_fin_actual->format('Y-m-d');
                }

                $asignacionesActuales[$row['pernr']] = [
                    'fecha_inicio' => $fecha_inicio_actual,
                    'fecha_fin' => $fecha_fin_actual
                ];
            }
            sqlsrv_free_stmt($stmtActuales);

            // Crear array de PERNRs que vienen en la solicitud
            $pernrsNuevos = [];
            foreach ($trabajadores as $trabajador) {
                $pernr = isset($trabajador['pernr']) ? trim($trabajador['pernr']) : '';
                if (!empty($pernr)) {
                    $pernrsNuevos[] = $pernr;
                }
            }

            // Eliminar trabajadores que ya NO están en la lista (fueron desmarcados)
            foreach ($asignacionesActuales as $pernr => $datos) {
                if (!in_array($pernr, $pernrsNuevos)) {
                    $sqlDelete = "DELETE FROM [webphp_trabajadores_grupos_horario] WHERE grupo_horario_id = ? AND pernr = ?";
                    $stmtDelete = sqlsrv_prepare($conn, $sqlDelete, array($grupo_id, $pernr));
                    sqlsrv_execute($stmtDelete);
                    sqlsrv_free_stmt($stmtDelete);
                }
            }

            // Preparar consultas de INSERT y UPDATE
            $sqlInsert = "INSERT INTO [webphp_trabajadores_grupos_horario] (grupo_horario_id, pernr, fecha_inicio, fecha_fin, fecha_asignacion) 
                          VALUES (?, ?, ?, ?, GETDATE())";

            $sqlUpdate = "UPDATE [webphp_trabajadores_grupos_horario] 
                          SET fecha_inicio = ?, fecha_fin = ?, fecha_asignacion = GETDATE() 
                          WHERE grupo_horario_id = ? AND pernr = ?";

            $insertados = 0;
            $actualizados = 0;
            $trabajadores_con_conflicto = [];

            foreach ($trabajadores as $trabajador) {
                // Espera un array tipo ['pernr'=>..., 'fecha_inicio'=>..., 'fecha_fin'=>...]
                $pernr = isset($trabajador['pernr']) ? trim($trabajador['pernr']) : '';
                $fecha_inicio = isset($trabajador['fecha_inicio']) && $trabajador['fecha_inicio'] ? $trabajador['fecha_inicio'] : null;
                $fecha_fin = isset($trabajador['fecha_fin']) && $trabajador['fecha_fin'] ? $trabajador['fecha_fin'] : null;

                if (empty($pernr)) {
                    continue;
                }

                // Verificar si el trabajador ya existe en el grupo
                $yaExiste = isset($asignacionesActuales[$pernr]);

                // Si ya existe, verificar si las fechas cambiaron
                if ($yaExiste) {
                    $fechasIguales = ($asignacionesActuales[$pernr]['fecha_inicio'] == $fecha_inicio) &&
                        ($asignacionesActuales[$pernr]['fecha_fin'] == $fecha_fin);

                    // Si las fechas son iguales, no hacer nada (ya está correctamente asignado)
                    if ($fechasIguales) {
                        continue; // Siguiente trabajador
                    }
                }

                // Si el trabajador NO tiene fechas definidas (grupo comodín)
                if (!$fecha_inicio || !$fecha_fin) {
                    // Buscar si existe otro grupo comodín (sin fechas) en el mismo año
                    $sqlVerificarComodin = "SELECT tgh.grupo_horario_id, gh.nombre_grupo
                                            FROM [webphp_trabajadores_grupos_horario] tgh
                                            INNER JOIN [webphp_grupos_horarios] gh ON tgh.grupo_horario_id = gh.id
                                            WHERE tgh.pernr = ?
                                            AND tgh.grupo_horario_id != ?
                                            AND gh.anio_configuracion = ?
                                            AND (tgh.fecha_inicio IS NULL OR tgh.fecha_fin IS NULL)";

                    $paramsComodin = array($pernr, $grupo_id, $anioGrupoActual);
                    $stmtComodin = sqlsrv_prepare($conn, $sqlVerificarComodin, $paramsComodin);

                    if ($stmtComodin && sqlsrv_execute($stmtComodin)) {
                        $grupoComodinExistente = sqlsrv_fetch_array($stmtComodin, SQLSRV_FETCH_ASSOC);

                        if ($grupoComodinExistente) {
                            // Ya existe un grupo comodín - ELIMINAR LA ASIGNACIÓN ANTERIOR
                            $grupoComodinAnteriorId = $grupoComodinExistente['grupo_horario_id'];
                            $grupoComodinAnteriorNombre = $grupoComodinExistente['nombre_grupo'];

                            $sqlDeleteComodin = "DELETE FROM [webphp_trabajadores_grupos_horario] 
                                                WHERE grupo_horario_id = ? AND pernr = ?";
                            $stmtDeleteComodin = sqlsrv_prepare($conn, $sqlDeleteComodin, array($grupoComodinAnteriorId, $pernr));

                            if ($stmtDeleteComodin && sqlsrv_execute($stmtDeleteComodin)) {
                                // Asignación anterior eliminada exitosamente
                                sqlsrv_free_stmt($stmtDeleteComodin);
                            } else {
                                throw new Exception("Error al eliminar grupo comodín anterior para $pernr: " . print_r(sqlsrv_errors(), true));
                            }
                        }

                        sqlsrv_free_stmt($stmtComodin);
                    }

                    // Permitir la asignación al nuevo grupo comodín
                } else {
                    // El trabajador tiene fechas definidas - Validar solapamiento
                    // Buscar asignaciones del mismo trabajador en grupos del mismo año
                    $sqlVerificarSolapamiento = "SELECT 
                                                    tgh.grupo_horario_id,
                                                    tgh.fecha_inicio,
                                                    tgh.fecha_fin,
                                                    gh.nombre_grupo
                                                 FROM [webphp_trabajadores_grupos_horario] tgh
                                                 INNER JOIN [webphp_grupos_horarios] gh ON tgh.grupo_horario_id = gh.id
                                                 WHERE tgh.pernr = ?
                                                 AND tgh.grupo_horario_id != ?
                                                 AND gh.anio_configuracion = ?";

                    $paramsVerificar = array($pernr, $grupo_id, $anioGrupoActual);
                    $stmtVerificar = sqlsrv_prepare($conn, $sqlVerificarSolapamiento, $paramsVerificar);

                    if (!$stmtVerificar) {
                        throw new Exception("Error al verificar solapamiento: " . print_r(sqlsrv_errors(), true));
                    }

                    sqlsrv_execute($stmtVerificar);

                    $hay_conflicto = false;
                    $grupos_conflicto = [];

                    while ($asignacion = sqlsrv_fetch_array($stmtVerificar, SQLSRV_FETCH_ASSOC)) {
                        $fecha_inicio_existente = $asignacion['fecha_inicio'];
                        $fecha_fin_existente = $asignacion['fecha_fin'];

                        // Convertir fechas DateTime a strings para comparación
                        if ($fecha_inicio_existente instanceof DateTime) {
                            $fecha_inicio_existente = $fecha_inicio_existente->format('Y-m-d');
                        }
                        if ($fecha_fin_existente instanceof DateTime) {
                            $fecha_fin_existente = $fecha_fin_existente->format('Y-m-d');
                        }

                        // Si la asignación existente NO tiene fechas (es comodín), NO hay conflicto
                        // El grupo comodín convive con grupos que tienen fechas específicas
                        if (!$fecha_inicio_existente || !$fecha_fin_existente) {
                            // No hay conflicto: el comodín cubre los días no asignados
                            continue;
                        }

                        // Verificar solapamiento de fechas SOLO con grupos que tienen fechas específicas
                        // Hay solapamiento si: (inicio1 <= fin2) AND (fin1 >= inicio2)
                        if (($fecha_inicio <= $fecha_fin_existente) && ($fecha_fin >= $fecha_inicio_existente)) {
                            $hay_conflicto = true;
                            $grupos_conflicto[] = $asignacion['nombre_grupo'] . " ({$fecha_inicio_existente} - {$fecha_fin_existente})";
                        }
                    }

                    sqlsrv_free_stmt($stmtVerificar);

                    // Si hay conflicto, registrar y continuar con el siguiente
                    if ($hay_conflicto) {
                        $trabajadores_con_conflicto[] = [
                            'pernr' => $pernr,
                            'grupos' => $grupos_conflicto
                        ];
                        continue; // No insertar/actualizar este trabajador
                    }
                }

                // No hay conflicto - Decidir si INSERT o UPDATE
                if ($yaExiste) {
                    // UPDATE: El trabajador ya existe pero con fechas diferentes
                    $paramsUpdate = array($fecha_inicio, $fecha_fin, $grupo_id, $pernr);
                    $stmtUpdate = sqlsrv_prepare($conn, $sqlUpdate, $paramsUpdate);

                    if (!$stmtUpdate) {
                        throw new Exception("Error al preparar actualización: " . print_r(sqlsrv_errors(), true));
                    }

                    if (sqlsrv_execute($stmtUpdate)) {
                        $actualizados++;
                    } else {
                        throw new Exception("Error al actualizar trabajador $pernr: " . print_r(sqlsrv_errors(), true));
                    }

                    sqlsrv_free_stmt($stmtUpdate);
                } else {
                    // INSERT: Trabajador nuevo en el grupo
                    $paramsInsert = array($grupo_id, $pernr, $fecha_inicio, $fecha_fin);
                    $stmtInsert = sqlsrv_prepare($conn, $sqlInsert, $paramsInsert);

                    if (!$stmtInsert) {
                        throw new Exception("Error al preparar inserción: " . print_r(sqlsrv_errors(), true));
                    }

                    if (sqlsrv_execute($stmtInsert)) {
                        $insertados++;
                    } else {
                        throw new Exception("Error al insertar trabajador $pernr: " . print_r(sqlsrv_errors(), true));
                    }

                    sqlsrv_free_stmt($stmtInsert);
                }
            }

            // Confirmar transacción
            sqlsrv_commit($conn);
            sqlsrv_close($conn);

            // Preparar mensaje de respuesta
            $mensaje = "";
            if ($insertados > 0 && $actualizados > 0) {
                $mensaje = "Se insertaron $insertados y actualizaron $actualizados trabajador(es)";
            } elseif ($insertados > 0) {
                $mensaje = "Se insertaron $insertados trabajador(es)";
            } elseif ($actualizados > 0) {
                $mensaje = "Se actualizaron $actualizados trabajador(es)";
            } else {
                $mensaje = "No se realizaron cambios";
            }

            if (count($trabajadores_con_conflicto) > 0) {
                $mensaje .= ". ADVERTENCIA: " . count($trabajadores_con_conflicto) . " trabajador(es) NO se asignaron por conflicto de fechas:";
                foreach ($trabajadores_con_conflicto as $conflicto) {
                    $grupos_str = implode(', ', $conflicto['grupos']);
                    $mensaje .= " • {$conflicto['pernr']} (conflicto con: {$grupos_str})";
                }
            }

            return [
                'success' => true,
                'message' => $mensaje,
                'insertados' => $insertados,
                'conflictos' => $trabajadores_con_conflicto
            ];

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            if (isset($conn)) {
                sqlsrv_rollback($conn);
                sqlsrv_close($conn);
            }

            return [
                'success' => false,
                'message' => 'Error al guardar asignación: ' . $e->getMessage()
            ];
        }
    }

    // Método para obtener trabajadores asignados a un grupo de horario
    public function obtener_trabajadores_asignados($grupo_id)
    {
        try {
            $conn = $this->conectarMuleSoft();

            // Abrir clave simétrica
            $abrirClave = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG DECRYPTION BY CERTIFICATE CertificadoPA_REG";
            sqlsrv_query($conn, $abrirClave);

            $sql = "SELECT 
                        tgh.pernr,
                        NOMBREYAPELLIDOS AS nombre,
                        tgh.fecha_inicio,
                        tgh.fecha_fin
                    FROM [PA_ACTIVOS] pa 
                    INNER JOIN [" . ConfigAppReclutamiento::$bdsrx_nombre . "].[dbo].[webphp_trabajadores_grupos_horario] tgh ON pa.PERNR = tgh.pernr
                    WHERE tgh.grupo_horario_id = ?
                    ORDER BY pa.PERNR";

            $stmt = sqlsrv_prepare($conn, $sql, array(&$grupo_id));

            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . print_r(sqlsrv_errors(), true));
            }

            if (!sqlsrv_execute($stmt)) {
                throw new Exception("Error al ejecutar consulta: " . print_r(sqlsrv_errors(), true));
            }

            $trabajadores = [];
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                // Formatear fechas si existen
                $fecha_inicio = null;
                $fecha_fin = null;

                if (isset($row['fecha_inicio']) && $row['fecha_inicio'] instanceof DateTime) {
                    $fecha_inicio = $row['fecha_inicio']->format('Y-m-d');
                } elseif (isset($row['fecha_inicio']) && is_string($row['fecha_inicio'])) {
                    $fecha_inicio = date('Y-m-d', strtotime($row['fecha_inicio']));
                }

                if (isset($row['fecha_fin']) && $row['fecha_fin'] instanceof DateTime) {
                    $fecha_fin = $row['fecha_fin']->format('Y-m-d');
                } elseif (isset($row['fecha_fin']) && is_string($row['fecha_fin'])) {
                    $fecha_fin = date('Y-m-d', strtotime($row['fecha_fin']));
                }

                $trabajadores[] = [
                    'pernr' => $row['pernr'],
                    'nombre' => $row['nombre'] ?: 'Sin nombre',
                    'fecha_inicio' => $fecha_inicio,
                    'fecha_fin' => $fecha_fin
                ];
            }

            sqlsrv_free_stmt($stmt);

            // Cerrar clave simétrica
            $cerrarClave = "CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG";
            sqlsrv_query($conn, $cerrarClave);

            sqlsrv_close($conn);

            return [
                'success' => true,
                'trabajadores' => $trabajadores
            ];

        } catch (Exception $e) {
            if (isset($conn)) {
                sqlsrv_close($conn);
            }

            return [
                'success' => false,
                'message' => 'Error al obtener trabajadores asignados: ' . $e->getMessage(),
                'trabajadores' => []
            ];
        }
    }



    // Guardar nuevos registros
    public function guardar_nuevo_registro($registro)
    {
        $conn = $this->ConectarAppReclutamiento();

        if (!$conn) {
            error_log("Error de conexión a la base de datos en guardar_nuevo_registro");
            return false;
        }

        // Concatenar fecha y hora en formato SQL Server usando los parámetros pasados
        $fecha = $registro['fecha'] . " " . $registro['hora'];
        $fecha_reg = date('Y-m-d H:i:s');
        $fecha = date('Y-m-d H:i:s', strtotime($fecha));

        $sql = "INSERT INTO webphp_registro_horario (fecha, pernr, fecha_reg, tipo_reg, manual, fecha_utc, utc_api, zona_api ) 
                VALUES ('" . $fecha_reg . "', '" . $registro['pernr'] . "', '" . $fecha . "', '" . $registro['tipo'] . "', 3, NULL, NULL, NULL )";

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            $errors = sqlsrv_errors();
            error_log("Error al insertar registro: " . print_r($errors, true));
            error_log("SQL: " . $sql);
            return false;
        }

        return true;
    }

}

?>