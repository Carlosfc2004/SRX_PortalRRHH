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
class sqlsrvModel{
    protected $conn;

    //Función para conectar con la base de datos de la aplicación web
    public function conectarWebApp(){
        try{  
            $connectionInfo = array("Database"=>ConfigWebApp::$bdsrx_nombre, "UID"=>ConfigWebApp::$bdsrx_usuario, "PWD"=>ConfigWebApp::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigWebApp::$bdsrx_hostname, $connectionInfo);
            if($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            }else{
                return $conn;
            }
        }catch(Exception $e){  
            echo("Error!");  
        }
    }



    //Función para conectar con la base de datos de la aplicación web (Mulesoft)
    public function conectarMuleSoft(){
        try{  
            $connectionInfo = array("Database"=>ConfigMuleSoft::$bdsrx_nombre, "UID"=>ConfigMuleSoft::$bdsrx_usuario, "PWD"=>ConfigMuleSoft::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigMuleSoft::$bdsrx_hostname, $connectionInfo);
            if($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            }else{
                return $conn;
            }
        }catch(Exception $e){  
            echo("Error!");  
        } 
    }



    //Función para conectar con la base de datos de Mantenimiento (SistemaMantenimiento)
    public function conectarMante(){
        try{  
            $connectionInfo = array("Database"=>ConfigMante::$bdsrx_nombre, "UID"=>ConfigMante::$bdsrx_usuario, "PWD"=>ConfigMante::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigMante::$bdsrx_hostname, $connectionInfo);
            if($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            }else{
                return $conn;
            }
        }catch(Exception $e){  
            echo("Error!");  
        } 
    }



    //Función para conectar con la base de datos de Portal del Empleado
    public function conectarEmpleado() {
        try{  
            $connectionInfo = array("Database"=>ConfigPortalEmpleado::$bdsrx_nombre, "UID"=>ConfigPortalEmpleado::$bdsrx_usuario, "PWD"=>ConfigPortalEmpleado::$bdsrx_clave, "CharacterSet" => "UTF-8", "Encrypt" => "no");
            $conn = sqlsrv_connect(ConfigPortalEmpleado::$bdsrx_hostname, $connectionInfo);
            if($conn === false) {
                die(print_r(sqlsrv_errors(), true));
            }else{
                return $conn;
            }
        }catch(Exception $e){  
            echo("Error: " . $e->getMessage());  
        } 
    }



    function FormatErrors($errors) {
        /* Display SQL error messages */
        echo "Error information: ";
        
        foreach ($errors as $error) {
            echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
            echo "Code: " . $error['code'] . "<br />";
            echo "Message: " . $error['message'] . "<br />";
        }
    }



    // Funcion para el menu del portal
    public function menuPortal() {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT * FROM [192.168.200.202].[".Config192168200202::$bdsrx_nombre."].[dbo].[webphp_menu_apps] WHERE app = 'portal_rrhh' ORDER BY id_padre, ord";
        $consulta = sqlsrv_query($conn, $sql);
       
        if ($consulta === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        }
       
        $menu_data = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            if($row['id_padre']==0){
                $menu_data[$row['id_hijo']] = $row;
            }else{
                $menu_data[$row['id_padre']]['children'][$row['id_hijo']] = $row;
            }
        }
 
        return $menu_data;
    } 



    //Comprobamos los datos de acceso a los usuarios al ingresar al sistema

    public function loginUser($user){
        $conn = $this->conectarWebApp();  
        $sql = "select * FROM webphp_Usuarios WHERE usr_login='".$user."' and (elim=0 or elim is null)";
        $params = array();
        $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE){
            die($this->FormatErrors(sqlsrv_errors()));
        }else{
            if ((sqlsrv_num_rows($consulta)==0) or (is_null($consulta))) {
                return false;
            }else{
                $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                return $row;
            }
        }
        sqlsrv_free_stmt($consulta);  
        sqlsrv_close($conn);
    }
  
    

    //Insertamos una acceso de un usuario
    public function AccesoUser($id_usuario, $login){
        $conn = $this->conectarWebApp();
        //Insertamos el acceso
        $sql = "insert into webphp_Accesos (id_usuario, login, fecha) values ('".$id_usuario."','".$login."','".date("Y-m-d H:i:s")."')";  
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta==TRUE) {
            return true;
        }else{
            return false;
        }
        sqlsrv_free_stmt($consulta);  
        sqlsrv_close($conn); 
    }



    // Registro de acciones en la web
    public function reg_acciones($accion, $referencia, $id_usuario, $estado){
        $conn = $this->conectarWebApp();

        $fecha_accion = date("Y-m-d H:i:s");

        //Insertamos el acceso
        $sql = "insert into webphp_auditor (accion, fecha_accion, referencia, id_usuario, estado) values ('".$accion."', '".$fecha_accion."', '".$referencia."', '".$id_usuario."', '".$estado."')";  

        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta==TRUE) {
            return true;
        }else{
            return false;
        }
        sqlsrv_free_stmt($consulta);  
        sqlsrv_close($conn); 
    }
    
    

    //Datos trabajadores sin respuesta en 15 días
    public function total_trabajadores_sinrespuesta() {
        $conn = $this->conectarWebApp();
        $sql = "WITH UltimosRegistros AS (
                    SELECT 
                        ID,
                        PERNR, 
                        FECHA_REGISTRO, 
                        ID_REGISTRO_RELACION,
                        ESTADO,
                        NUM_ENVIO
                    FROM [webphp_registros_llamamientos]
                    WHERE NUM_ENVIO = 2  -- Filtramos solo llamamiento 2
                )
                , RegistrosConHijos AS (
                    -- Identificamos los IDs que aparecen como ID_REGISTRO_RELACION
                    SELECT DISTINCT ID_REGISTRO_RELACION
                    FROM [webphp_registros_llamamientos]
                    WHERE ID_REGISTRO_RELACION IS NOT NULL
                )
                SELECT 
                    COUNT(*) as TotalRegistrosSinRespuesta
                FROM UltimosRegistros ur
                LEFT JOIN RegistrosConHijos rch ON ur.ID = rch.ID_REGISTRO_RELACION
                WHERE 
                    ur.FECHA_REGISTRO <= DATEADD(DAY, -5, GETDATE())  -- Han pasado más de 5 días
                    AND ur.ESTADO IN (0, 3)  -- Solo estados 0 (enviado) o 3
                    AND ur.ID_REGISTRO_RELACION IS NULL  -- No está relacionado con un llamamiento anterior
                    AND rch.ID_REGISTRO_RELACION IS NULL;  -- No tiene llamamientos hijos";
        
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
    public function total_aceptados_baja_total() {
        $conn = $this->conectarWebApp();
        $sql = "WITH UltimoRegistro AS (
                    SELECT 
                        pa.PERNR,
                        pa.STAT2,
                        ROW_NUMBER() OVER (PARTITION BY pa.PERNR ORDER BY pa.ID DESC) AS rn
                    FROM 
                        [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0000] pa
                )
                SELECT COUNT(DISTINCT pa.PERNR) AS total_aceptados_baja
                FROM 
                    UltimoRegistro pa
                JOIN 
                    [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] wl 
                    ON pa.PERNR = wl.PERNR
                WHERE 
                    pa.rn = 1
                    AND pa.STAT2 = 0
                    AND wl.ESTADO = 1
                    AND wl.ID_REMESA = (
                        SELECT MAX(ID_REMESA)
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] 
                        WHERE PERNR = wl.PERNR
                        AND ANO_REMESA = ( 
                            SELECT MAX(ANO_REMESA)
                            FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos]
                            WHERE PERNR = wl.PERNR
                        )
                    )";
        
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
    public function total_aceptados_baja(){
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG; 

                WITH UltimoRegistro AS (
                    SELECT 
                        pa.PERNR,
                        pa.STAT2,
                        ROW_NUMBER() OVER (PARTITION BY pa.PERNR ORDER BY pa.ID DESC) AS rn
                    FROM 
                        [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0000] pa
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
                    [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] wl 
                    ON pa.PERNR = wl.PERNR
                JOIN 
                     [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos] rl 
                    ON pa.PERNR = rl.PERNR
                JOIN 
                    [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0002] pa2
                    ON pa2.PERNR = pa.PERNR
                WHERE 
                    pa.rn = 1
                    AND pa.STAT2 = '0' 
                    AND wl.ESTADO = 1  
                    AND wl.ID_REMESA = (
                        SELECT MAX(ID_REMESA)
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] 
                        WHERE PERNR = wl.PERNR 
                        AND ANO_REMESA = ( 
                            SELECT MAX(ANO_REMESA)
                            FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos]
                            WHERE PERNR = wl.PERNR
                        )
                    )
                GROUP BY pa.PERNR, pa2.NOMBREYAPELLIDOS, pa2.NOMBRE, pa2.APELLIDO1, pa2.APELLIDO2, wl.ID_REMESA, wl.ANO_REMESA, rl.nombre_remesa;


                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Cumpleaños de trabajador
    public function cumple_trabajador(){
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos opciones sociedades
    public function dni_caducados() {
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Trabajadores en una remesa sin llamamientos realizados
    public function trabajadores_sinllamamiento() {
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
            FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos] r
            LEFT JOIN [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] l
                ON r.PERNR = l.PERNR
            LEFT JOIN pa0002 pa2 ON r.PERNR = pa2.PERNR
            WHERE r.sms_auto = 0
            AND l.PERNR IS NULL; 

            CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;  ";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //total contrataciones mensuales
    public function total_contrataciones_mensuales(){
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //total trabajadores por sociedad
    public function sociedad_trabajador() {
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
    public function trabajadoresActivos(){
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            if ($row['STAT2'] == '3' && ($row['ZZWERKS'] == '1000' || $row['ZZWERKS'] == '1700' || $row['ZZWERKS'] == '2000')) {
                $resultado[] = $row;
            }
        }
        return $resultado;
    }



    //Datos opciones sociedades
    public function Sociedades_graf(){
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Mostramos el listado de Trabajadores de SAP
    public function buscarTrabajadoresSap($txt_pernr, $txt_nombre, $sociedad, $baja) {
        $conn = $this->conectarMuleSoft();

        // Verificar si se busca por trabajadores no activos.
        $sql = "
        OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
        DECRYPTION BY CERTIFICATE CertificadoPA_REG;
        SELECT TOP (1000) 
            *
        FROM PA_ACTIVOS WHERE 1=1 "; 
    
        // Aplicar los filtros solo si hay valores no vacíos
        if ($txt_pernr != '') {
            $sql .= "AND PERNR LIKE '%" . $txt_pernr . "%' ";
        }
        if ($txt_nombre != '') {
            $sql .= "AND NOMBREYAPELLIDOS LIKE '%" . $txt_nombre . "%' ";
        }
        if ($sociedad != '') {
            $sql .= "AND ZZWERKS = '" . $sociedad . "' ";
        }
        if ($baja != '') {
            $sql .= "AND STAT2 = '" . $baja . "' ";
        } 
        if ($baja == '') {
            $sql .= "ORDER BY ZZWERKS ";
        } 
    
        // Ordenar los resultados
        $sql .= " CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
    
        // echo $sql;
        // die;

        // Ejecutar la consulta SQL
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === false) {
            die("SQL Error: " . print_r(sqlsrv_errors(), true));
        }
    
        // Recoger los resultados
        $resultado = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
    
        return $resultado;
    }



    //Datos opciones sociedades
    public function Sociedades(){
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT DISTINCT(ZZWERKS), DESC_CENTRO
                FROM PA0001
                WHERE DESC_CENTRO!=''
                ORDER BY ZZWERKS ASC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Obtenemos todos los datos de un trabajador SAP
    public function info_trabajador($PERNR) {
        $conn = $this->conectarMuleSoft();
        
        // Consulta SQL con parámetros
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;
                SELECT 
                    BEGDA AS BEGDA_ORIGIN,
                    [ID],
                    [PERNR],
                    [FECHA_IN],
                    CONVERT(varchar, [BEGDA], 103) AS BEGDA,
                    CONVERT(varchar, [ENDDA], 103) AS ENDDA,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(DNI))) AS DNI,
                    [TIPODOCUMENTO],
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(FECHANACIMIENTO))) AS FECHANACIMIENTO,
                    [SEXO],
                    [LUGAR_NACIMIENTO],
                    [PAIS_NACIMIENTO],
                    [DESC_PAIS_NACIMIENTO],
                    [NACIONALIDAD],
                    UPPER(LEFT([DESC_NACIONALIDAD], 1)) + LOWER(SUBSTRING([DESC_NACIONALIDAD], 2, LEN([DESC_NACIONALIDAD]))) AS DESC_NACIONALIDAD,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(NOMBRE))) AS NOMBRE,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(APELLIDO1))) AS APELLIDO1,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(APELLIDO2))) AS APELLIDO2
                FROM [PA0002]
                WHERE PERNR = $PERNR
                ORDER BY BEGDA_ORIGIN DESC;
                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
    
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
    public function datos_contacto_trabajador($PERNR){
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT 
                    [id],
                    [PERNR],
                    [FECHA_IN],
                    [BEGDA],
                    [ENDDA],
                    MAX([MOVIL]) AS MOVIL,
                    LOWER([CORREO]) AS CORREO,
                    [TELEMPRESA],
                    [TELEMERGENCIAS],
                    [TIPO],
                    MAX([PRE_TELF]) AS PRE_TELF,
                    [PRE_TELF_EMP],
                    [PRE_TELF_EMER],
                    [PARENT_TELF],
                    [PARENT_TELF_EMP],
                    [PARENT_TELF_EMER]
                FROM [PA0105]
                WHERE PERNR = $PERNR  
                GROUP BY 
                    [id],
                    [PERNR],
                    [FECHA_IN],
                    [BEGDA],
                    [ENDDA],
                    [TELEMPRESA],
                    [TELEMERGENCIAS],
                    CORREO,
                    [TIPO],
                    [PRE_TELF_EMP],
                    [PRE_TELF_EMER],
                    [PARENT_TELF],
                    [PARENT_TELF_EMP],
                    [PARENT_TELF_EMER]
                ORDER BY FECHA_IN DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos prefijos
    public function datos_prefijos(){
        $conn = $this->conectarWebApp();
        $sql = "SELECT [id]
                      ,[nombre]
                      ,[prefijo]
                FROM [webphp_prefijos]";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos parentesco para el numero telefono del trabajador
    public function datos_parentesco(){
        $conn = $this->conectarWebApp(); 
        $sql = "SELECT [ID], [PARENTESCO] FROM [webphp_parentesco]"; 
    
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
    
        if ($consulta == FALSE) // Verifica errores en la consulta
            die($this->FormatErrors(sqlsrv_errors()));
    
        // Recorre los resultados y los agrega al array
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
    
        return $resultado; // Devuelve el array con los resultados
    }



    // Tipo de ausencias
    public function tipos_ausencias(){
        $conn = $this->conectarWebApp();
        $sql = "SELECT [id], [valor] FROM [webphp_tipo_ausencias]";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Trabajdores con solicitudes
    public function trabajadores_solicitudes() {
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;

                SELECT TOP (1000) 
                    wa.[pernr],
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(pa2.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS
                FROM [".ConfigPortalEmpleado::$bdsrx_nombre."].[dbo].[webphp_ausencias] wa
                LEFT JOIN pa0002 pa2 ON pa2.PERNR = wa.pernr
                GROUP BY wa.pernr, NOMBREYAPELLIDOS 

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        }
        $resultado = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    } 



    // Solicitudes de ausencias pendientes
    public function solicitudes($fecha_solic, $fecha_solic2, $pernr, $tipo_ausencia, $estado, $justificante) {

        $conn = $this->conectarMuleSoft();
        
        // Primera consulta: Obtener solicitudes
        $sql = "SELECT TOP (1000) 
                    wu.nombre,
                    wu.apellidos,
                    wu.usr_login AS mail,
                    wu_s.nombre AS nombre_superior,
                    wu_s.apellidos AS apellidos_superior,
                    wu_s.usr_login AS mail_s,
                    pe.*
                FROM [".ConfigPortalEmpleado::$bdsrx_nombre."].[dbo].[webphp_ausencias] pe
                LEFT JOIN [192.168.200.202].[".Config192168200202::$bdsrx_nombre."].[dbo].[webphp_Usuarios] wu 
                    ON pe.pernr = wu.pernr COLLATE Modern_Spanish_CI_AS
                LEFT JOIN [192.168.200.202].[".Config192168200202::$bdsrx_nombre."].[dbo].[webphp_Usuarios] wu_s 
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
            $sql .= " AND (pe.firma_superior = '1' AND pe.estado = '$estado')";
        } else {
            $sql .= " AND pe.estado IN ('1', '3', '4', '5', '6', '7', '8')";
        }
        if ($justificante == 'on') {
            $sql .= " AND pe.justificante != ''";
        }

        $sql .= " ORDER BY pe.fecha_solicitud DESC;";

        // echo $sql; 
        // die;

        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === FALSE) {
            die($this->FormatErrors(sqlsrv_errors()));
        }

        $resultado = array();
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $id_solicitud = $row['id_solicitud'];

            // Segunda consulta: Obtener observaciones por id_solicitud
            $sql_obs = "SELECT id_solicitud_ausencia, wu.nombre, wu.apellidos, pernr_solicitante, pernr_mod, comentario, fecha_modificacion, tipo_coment
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_estado_solicitud_ausencias]  sa
                        LEFT JOIN [192.168.200.202].[".Config192168200202::$bdsrx_nombre."].[dbo].[webphp_Usuarios] wu 
                            ON sa.pernr_mod = wu.pernr COLLATE Modern_Spanish_CI_AS
                        WHERE id_solicitud_ausencia = '$id_solicitud'
                        ORDER BY fecha_modificacion ASC"; 
            
            $consulta_obs = sqlsrv_query($conn, $sql_obs);
            
            $observaciones = array();
            while ($obs = sqlsrv_fetch_array($consulta_obs, SQLSRV_FETCH_ASSOC)) {
                $observaciones[] = array(
                    'id_solicitud_ausencia' => $obs['id_solicitud_ausencia'],
                    'nombre'                => $obs['nombre'].' '.$obs['apellidos'],
                    'pernr_solicitante'     => $obs['pernr_solicitante'],
                    'pernr_mod'             => $obs['pernr_mod'],
                    'comentario'            => $obs['comentario'],
                    'fecha_modificacion'    => $obs['fecha_modificacion'],
                    'tipo_coment'           => $obs['tipo_coment']
                );
            }
            
            $row['observaciones'] = $observaciones;
            $resultado[] = $row;

        }
        return $resultado;
    }



    public function getotrasausencias($id_padre) {
        $conn = $this->conectarWebApp();
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
                FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_tipo_otras_ausencias]";
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



    // Actualizar solicitud de ausencia del empleado
    // public function actualizarSolicitud($id_solicitud, $fecha_res_rrhh, $firma_rrhh, $estado, $id_rrhh, $mail_s, $nombre, $nombre_s, $mail) {
    //     $conn = $this->conectarEmpleado();
    //     $sql = "UPDATE webphp_ausencias SET fecha_res_rrhh = '$fecha_res_rrhh', firma_rrhh = '$firma_rrhh', estado = '$estado', comunicado_rrhh = '$id_rrhh' WHERE id_solicitud = '$id_solicitud';";
    //     $consulta = sqlsrv_query($conn, $sql);
    
    //     if ($consulta == TRUE) {
    //         return true;
    //     } else {
    //         return false;
    //     } 
    // }



    public function actualizarSolicitud($id_solicitud, $fecha_res_rrhh, $firma_rrhh, $fecha_sol, $estado, $id_rrhh, $mail_s, $nombre, $nombre_s, $mail_emp) {
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

        // Construir el mensaje
        $mensaje = '
        <html>
            <head><title>Actualizacion de solicitud de ausencia</title></head>
            <body>
                <p>Estimado/a ' . $nombre . '</p>
                <p>La solicitud realizada el dia ' . date_format(new DateTime($fecha_sol), "d-m-Y") . ' ha sido ' . $respuesta . '</p>';
    
        $mensaje .= '
                <ul>
                    <li>Fecha de respuesta: ' . date_format(new DateTime($fecha_res_rrhh), "d-m-Y") . '</li>
                </ul>
                <p>Por favor, accede al portal del empleado para revisarlas con el siguiente enlace.</p>
                <p><a href="https://portalempleado.surexport.es">Acceder al portal del empleado</a></p>
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
        $mail->isSMTP();
        $mail->Host = 'smtp.office365.com';
        $mail->Port = 587;
        $mail->SMTPAuth = true;
        $mail->Username = 'itcomunication@surexport.es';
        $mail->Password = 'Surxp2021+';
        $mail->setFrom('itcomunication@surexport.es', 'Surexport');
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
    public function agregarObservacion($id_solicitud, $pernr_obs, $fecha_crea, $pernr_usu, $observacion) {
        $conn2 = $this->conectarWebApp();

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
    public function datos_medidas($PERNR){
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT BEGDA AS BEGDA_ORING
                    ,CONVERT(varchar, [BEGDA], 103) as BEGDA
                    ,CONVERT(varchar, [ENDDA], 103) as ENDDA
                    ,[STAT2]
                    ,[MASSN]
                    ,[MNTXT]
                    ,[MASSG]
                    ,[MGTXT] 
                FROM [PA0000]
                WHERE PERNR = $PERNR 
                order by BEGDA_ORING desc";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos direccion trabajador
    public function datos_direccion_trabajador($PERNR){
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT BEGDA AS BEGDA_ORIGIN
                      ,[ID]
                      ,[FECHA_IN]
                      ,[PERNR]
                      ,[BEGDA]
                      ,[ENDDA]
                      ,[CLASE_DIRECCION]
                      ,[DESC_CLASE_DIRECCION]
                      ,[CALLE_NUMERO]
                      ,[POBLACION]
                      ,[COD_POSTAL]
                      ,[PAIS]
                      ,[DESC_PAIS]
                      ,[DISTANCIA1_KM]
                      ,[DISTANCIA2_KM]
                      ,[REGION]
                      ,[DESC_REGION]
                      ,[N_EDIFICIO]
                      ,[SIGLAS_VP]
                      ,[DESC_SIGLAS_VP]
                      ,[N_TELEFONO]
                      ,[COMUNICACION1]
                      ,[NUMERO1]
                      ,[COMUNICACION2]
                      ,[NUMERO2]
                      ,[COMUNICACION3]
                      ,[NUMERO3]
                      ,[COMUNICACION4]
                      ,[NUMERO4]
                FROM [PA0006]
                WHERE PERNR = $PERNR
                ORDER BY BEGDA_ORIGIN DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos contratos empresa trabajador
    public function datos_contrato_trabajador($PERNR){
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT BEGDA AS BEGDA_ORIGIN
                      ,[ID]
                      ,[PERNR]
                      ,[FECHA_IN]
                      ,CONVERT(varchar, [BEGDA], 103) as BEGDA
                      ,CONVERT(varchar, [ENDDA], 103) as ENDDA
                      ,[TIPO_CONTRATO]
                      ,[DESC_TIPO_CONTRATO]
                      ,[CLAVE_CONTRATO]
                      ,[DESC_CLAVE_CONTRATO]
                FROM [PA0480]
                WHERE PERNR = $PERNR
                ORDER BY BEGDA_ORIGIN DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos contrato seguridad social trabajador
    public function datos_contrato2_trabajador($PERNR){
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT BEGDA AS BEGDA_ORIGIN
                      ,[ID]
                      ,[PERNR]
                      ,[FECHA_IN]
                      ,CONVERT(varchar, [BEGDA], 103) as BEGDA
                      ,CONVERT(varchar, [ENDDA], 103) as ENDDA
                      ,[RELACION_LABORAL]
                      ,[DESC_RELACION_LABORAL]
                FROM [PA0061]
                WHERE PERNR = $PERNR
                ORDER BY BEGDA_ORIGIN DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }
    


    // Datos carnet manipulador
    public function datos_ropo_trabajador($PERNR){
        $conn = $this->conectarMuleSoft();  
        $sql = "SELECT TOP (1) 
                       PERNR
                      ,BEGDA AS BEGDA_ORING
                      ,CONVERT(varchar, [BEGDA], 103) as BEGDA
                      ,CONVERT(varchar, [ENDDA], 103) as ENDDA
                      ,ZZROPO
                      ,ZZCARNET
                      ,CONVERT(varchar, ZZFECHA, 103) as ZZFECHA
                FROM [PA0032] 
                WHERE PERNR = $PERNR 
                ORDER BY BEGDA_ORING DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Datos para tabla asusencia trabajador
    public function datos_ausencia_trabajador($PERNR){
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT PERNR
                      ,BEGDA AS BEGDA_ORING
                      ,CONVERT(varchar, BEGDA, 103) as BEGDA
                      ,CONVERT(varchar, ENDDA, 103) as ENDDA
                      ,SUBTY
                      ,ATEXT
                FROM [PA2001]
                WHERE PERNR = $PERNR
                ORDER BY BEGDA_ORING DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos asignación trabajador
    public function datos_asignacion_trabajador($PERNR){
        $conn = $this->conectarMuleSoft();  
        $sql = "SELECT [PERNR]
                       ,BEGDA AS BEGDA_ORING
                       ,[PLANS]
                       ,[STEXT_PLANS]
                       ,[ZZLGORT]
                       ,[DESC_ALMACEN]
                       ,[FINCA]
                       ,[DESC_FINCA]
                       ,[ZZWERKS]
                       ,[DESC_CENTRO]
                       ,CONVERT(varchar, [BEGDA], 103) as BEGDA
                       ,CONVERT(varchar, [ENDDA], 103) as ENDDA
                       ,[WERKS]
                       ,[DESC_DIVISION]
                       ,[ORGEH]
                       ,[STEXT]
                       ,[ZZROLAGR]
                       ,[ZZNFC]
                FROM [PA0001]
                WHERE PERNR = $PERNR
                ORDER BY BEGDA_ORING DESC, ID DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos NFC tarjeta trabajador
    public function datos_nfc_trabajador($PERNR){
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Datos fecha validez documento identificacion nacional
    public function fecha_val_dni($pernr){
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Actualizar o añadir fecha validez documento identificacion nacional al maestro
    public function update_fecha_val_dni($pernr, $tipo_doc, $fecha_validez) {
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
    public function curl_api_mulesoft($data, $metod, $path) {
        // URL de la API
        $url = ConfigApiMulesoft::$api_url.$path;

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
    public function update_nfc_agro($pernr, $nfc) {
        $conn = $this->conectarWebApp();
        $response = [
            'success' => false,
            'message' => '',
            'details' => []
        ];
    
        // SQL para comprobar que el trabajador esta en agromobile
        $sql = "SELECT [PERNR], [ZZRFID]
                FROM [192.168.200.202].[".ConfigAgromobile::$bdsrx_nombre."].[dbo].[ZHRT0001]
                WHERE PERNR = '".$pernr."'";
        
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
            $updateSql = "UPDATE [192.168.200.202].[".ConfigAgromobile::$bdsrx_nombre."].[dbo].[ZHRT0001]
                          SET ZZRFID = '".strtolower($nfc)."'
                          WHERE PERNR = '".$pernr."'";

            $updateConsulta = sqlsrv_query($conn, $updateSql);
            
            if ($updateConsulta === false) {
                $response['message'] = 'Error al actualizar NFC: ' . print_r(sqlsrv_errors(), true);
                return $response;
            }
        
            // Eliminar NFC duplicados
            $sql_elim = "UPDATE [192.168.200.202].[".ConfigAgromobile::$bdsrx_nombre."].[dbo].[ZHRT0001] 
                         SET ZZRFID = '' 
                         WHERE ZZRFID = '".strtolower($nfc)."' 
                         AND PERNR != '".$pernr."'";
            
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
    public function update_nfc_maestro($pernr, $nfc) {
        $conn = $this->conectarMuleSoft();
        $response = [
            'success' => false,
            'message' => '',
            'details' => []
        ];
    
        // Paso 1: Verificar si el NFC existe en la tabla
        $sql = "SELECT PERNR, ZZNFC
                FROM MAESTRO_NFC
                WHERE ZZNFC = '".strtolower($nfc)."'";
        
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === false) {
            $response['message'] = 'Error al verificar NFC existente: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }
        
        $resultado = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        $fechaActual = date("Y-m-d H:i:s");
    
        // Paso 2: Desasignar el PERNR de otros registros con distintos ZZNFC
        $sql_desasignar = "UPDATE MAESTRO_NFC
                           SET PERNR = NULL, FECHA_NFC = '".$fechaActual."'
                           WHERE ZZNFC != '".strtolower($nfc)."' 
                           AND PERNR = '".$pernr."'";
        
        $desasignarConsulta = sqlsrv_query($conn, $sql_desasignar);
        if ($desasignarConsulta === false) {
            $response['message'] = 'Error al desasignar NFCs anteriores: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }
    
        $nfcsDesasignados = sqlsrv_rows_affected($desasignarConsulta);
        
        if ($resultado) {
            // Paso 3: Si el NFC existe, actualizarlo con el nuevo PERNR
            $updateSql = "UPDATE MAESTRO_NFC
                          SET PERNR = '".$pernr."', FECHA_NFC = '".$fechaActual."'
                          WHERE ZZNFC = '".strtolower($nfc)."'";
            
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
                          VALUES ('".$pernr."', '".strtolower($nfc)."', '".$fechaActual."')";
            
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
    public function update_nfc_mantenimiento($pernr, $nfc) {
        $conn = $this->conectarMante();
        $response = [
            'success' => false,
            'message' => '',
            'details' => []
        ];
    
        // Paso 1: Verificar si el pernr existe en la tabla
        $sql = "SELECT id_objetive
                FROM webphp_Usuarios
                WHERE id_objetive LIKE '%".($pernr+0)."%'";


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
                           WHERE tarjeta = '".strtolower($nfc)."'";
        
        $desasignarConsulta = sqlsrv_query($conn, $sql_desasignar);
        if ($desasignarConsulta === false) {
            $response['message'] = 'Error al desasignar NFCs anteriores: ' . print_r(sqlsrv_errors(), true);
            return $response;
        }

        $nfcsDesasignados = sqlsrv_rows_affected($desasignarConsulta);
        
        if ($resultado) {
            // Paso 3: Actualizar el trabajador con la nueva tarjeta
            $updateSql = "UPDATE webphp_Usuarios
                          SET tarjeta = '".strtolower($nfc)."' 
                          WHERE id_objetive LIKE '%".($pernr+0)."%'";
            
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
    public function update_nfc_pa0001($pernr, $nfc){
        $conn = $this->conectarMuleSoft();
        $response = [
            'success' => false,
            'message' => '',
            'details' => []
        ];

        $updateSql = "UPDATE PA0001 set ZZNFC='".strtolower($nfc)."' where ID=(SELECT TOP(1) ID FROM PA0001 where PERNR='".$pernr."' order by BEGDA desc, FECHA_IN desc) and PERNR='".$pernr."'";

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
    public function motivos_pendiente(){
        $conn = $this->conectarWebApp();  
        $sql = "SELECT id_motivo
                      ,desc_motivo
                FROM webphp_motivos_pendiente
                ORDER BY id_motivo DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Fincas agromobile
    public function fincas_agromobile($centro){
        $conn = $this->conectarWebApp();
        $sql = "select ZZCODFI, DESFI FROM [192.168.200.202].[SUREXPORT_AGROMOBILE].[dbo].[ZZMT0002] WHERE WERKS=$centro ORDER BY DESFI";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Función para generar el informe de presencia
    public function informePresencia($fincas, $fecha_ini, $fecha_fin, $centro, $division, $operario) {
        // Conectar a la base de datos
        $conn = $this->conectarWebApp();
     
        // Definir la consulta para ejecutar el procedimiento almacenado
        $sql = "EXEC [192.168.200.202].[SUREXPORT_AGROMOBILE].[dbo].[PROC_INFORME_PRESENCIA_CENTRO] ?, ?, ?, ?, ?, ?";
     
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
    public function informePresenciaOficina($fecha_ini, $fecha_fin, $tipo, $pernr, $manual, $sede, $ubicacion){
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
                        [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registro_horario] AS reg
                    LEFT JOIN
                        [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_dispositivos] AS dispo ON reg.id_dispo = dispo.id_dispositivo
                    LEFT JOIN
                        [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_ubicaciones_dispo] AS ubi ON dispo.ubicacion = ubi.id
                    LEFT JOIN
                        [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0002] AS emp ON reg.pernr = emp.PERNR WHERE 1=1 ";
                        
                if ($tipo != '') {
                    $sql .= "AND tipo_reg = '".$tipo."' ";
                }
    

                if ($fecha_ini != '' && $fecha_fin != '') {
                        $fecha_fin_dt = new DateTime($fecha_fin);
                        $fecha_fin_dt->modify('+1 day');
                        $sql .= "AND CAST(COALESCE(reg.fecha_reg, reg.fecha) AS DATE) BETWEEN '".$fecha_ini." 03:00:00' AND '".$fecha_fin_dt->format("Y-m-d")." 03:00:00'";
                    } else if ($fecha_ini != '') {
                        $fecha_ini_dt = new DateTime($fecha_ini);
                        $fecha_ini_dt->modify('+1 day'); 
                        $sql .= "AND CAST(COALESCE(reg.fecha_reg, reg.fecha) AS DATE) BETWEEN '".$fecha_ini." 03:00:00' AND '".$fecha_ini_dt->format("Y-m-d")." 03:00:00'";
                    }

    
                $sql .= " AND (
                    reg.tipo_reg <> 'salida' 
                    OR reg.id > (
                        SELECT MIN(id) 
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registro_horario] 
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
                        $pernr = array_filter($pernr, function($value) {
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
                    $sql .= "AND ubi.sede = '".$sede."' ";
                }
    
                if ($ubicacion != '') {
                    $sql .= "AND ubi.id = '".$ubicacion."' ";
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Trabjarores para el filtro de auditoria
    public function trabajadoresAuditoria(){
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;

                WITH 	
                    DatosOrdenados AS ( 
                        SELECT 
                            DISTINCT w.pernr, 
                            CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS, 
                            w.fecha 
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registro_horario] w
                        INNER JOIN [Mulesoft].[dbo].[PA0002] p ON w.pernr = p.PERNR
                    )
                	
                SELECT 
                    pernr, 
                    NOMBREYAPELLIDOS 
                FROM DatosOrdenados
                GROUP BY pernr, NOMBREYAPELLIDOS
                ORDER BY pernr ASC;


                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Auditoria
    public function informePresenciaOficina2($filtros) {
        
        $fecha_ini = $filtros['fecha_inicio_ofi'] ?? null;
        $fecha_fin = $filtros['fecha_fin_ofi'] ?? null;
        $pernr_nom = $filtros['pernr_nom_trab'] ?? null;
    
        function timeToMinutes($tiempo) {
            if (empty($tiempo)) return 0;
            $partes = explode(':', $tiempo);
            return (count($partes) == 2) ? intval($partes[0]) * 60 + intval($partes[1]) : 0;
        }
    
        $filtroCondiciones = [];
        for ($i = 1; $i <= 6; $i++) {
            if (!empty($filtros["campo1_$i"]) && !empty($filtros["campo2_$i"]) && !empty($filtros["campo3_$i"])) {
                $minutos = timeToMinutes($filtros["campo3_$i"]);
                $operador = $filtros["campo2_$i"];
                
                $campos = [
                    '1' => 'desayuno_segundos',
                    '2' => 'almuerzo_segundos',
                    '3' => 'otros_segundos',
                    '4' => '(desayuno_segundos + almuerzo_segundos + otros_segundos)',
                    '5' => '(jornada_total_segundos - desayuno_segundos - otros_segundos - almuerzo_segundos)',
                    '6' => 'jornada_total_segundos'
                ];
    
                if (isset($campos[$filtros["campo1_$i"]])) {
                    $filtroCondiciones[] = "({$campos[$filtros["campo1_$i"]]} / 60) $operador $minutos";
                }
                
                if (!empty($filtros["conector_$i"]) && $filtros["conector_$i"] !== '0') {
                    $filtroCondiciones[] = $filtros["conector_$i"];
                }
            }
        }
        
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG DECRYPTION BY CERTIFICATE CertificadoPA_REG;";
        
        $sql .= "WITH registros AS (
                    SELECT 
                        hor.pernr, 
                        CASE 
                            WHEN CAST(fecha_reg AS TIME) BETWEEN '00:00:00' AND '03:00:00' 
                            THEN CAST(DATEADD(DAY, -1, fecha_reg) AS DATE) 
                            ELSE CAST(fecha_reg AS DATE) 
                        END AS fecha,  
                        tipo_reg, 
                        fecha_reg
                    FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registro_horario] hor
                    LEFT JOIN PA0002 AS emp ON hor.pernr = emp.PERNR";
        
        if ($fecha_ini && $fecha_fin) {
            $fecha_fin_dt = new DateTime($fecha_fin);
            $fecha_fin_dt->modify('+1 day');
            $sql .= " WHERE fecha_reg BETWEEN '".$fecha_ini." 03:00:00' AND '".$fecha_fin_dt->format("Y-m-d")." 03:00:00'";
        } else if ($fecha_ini) {
            $fecha_ini_dt = new DateTime($fecha_ini);
            $fecha_ini_dt->modify('+1 day');
            $sql .= " WHERE fecha_reg BETWEEN '".$fecha_ini." 03:00:00' AND '".$fecha_ini_dt->format("Y-m-d")." 03:00:00'";
        }

        $sql .= " AND emp.pernr != ''";
        
        // if ($pernr_nom) {
        //     $sql .= ($fecha_ini ? " AND" : " WHERE")." (CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.NOMBREYAPELLIDOS))) LIKE '%$pernr_nom%' 
        //               OR hor.pernr LIKE '%$pernr_nom%') ";
        // }

        // Si $pernr_nom es una cadena, convertirlo a un array
        if (is_string($pernr_nom)) {
            $pernr_nom = explode(',', $pernr_nom); // Convierte la cadena separada por comas en un array
        }

        // Asegúrate de que $pernr_nom sea un array
        if (is_array($pernr_nom)) {
            // Limpia los valores del array para quitar espacios innecesarios
            $pernr_nom = array_map('trim', $pernr_nom);

            // Filtra los valores vacíos
            $pernr_nom = array_filter($pernr_nom, function($value) {
                return !empty($value);
            });

            if (!empty($pernr_nom)) {
                // Convierte el array a una lista de valores SQL
                $pernr_nom_str = "'" . implode("','", array_map('addslashes', $pernr_nom)) . "'";

                // Construye la parte de la consulta
                $sql .= ($fecha_ini ? " AND" : " WHERE") . " (
                    hor.pernr IN ($pernr_nom_str)
                ) ";
            }
        }


        $sql .= "), eventos AS (
                    SELECT pernr, fecha, fecha_reg, tipo_reg,
                           LEAD(fecha_reg) OVER (PARTITION BY pernr, fecha ORDER BY fecha_reg) AS siguiente_reg,
                           LEAD(tipo_reg) OVER (PARTITION BY pernr, fecha ORDER BY fecha_reg) AS siguiente_tipo
                    FROM registros
                ), descansos AS (
                    SELECT pernr, fecha,
                           SUM(CASE WHEN tipo_reg = 'inicio-desayuno' AND siguiente_tipo = 'fin-desayuno' THEN DATEDIFF(SECOND, fecha_reg, siguiente_reg) ELSE 0 END) AS desayuno_segundos,
                           SUM(CASE WHEN tipo_reg = 'inicio-otros' AND siguiente_tipo = 'fin-otros' THEN DATEDIFF(SECOND, fecha_reg, siguiente_reg) ELSE 0 END) AS otros_segundos,
                           SUM(CASE WHEN tipo_reg = 'inicio-almuerzo' AND siguiente_tipo = 'fin-almuerzo' THEN DATEDIFF(SECOND, fecha_reg, siguiente_reg) ELSE 0 END) AS almuerzo_segundos
                    FROM eventos
                    WHERE siguiente_reg IS NOT NULL
                    GROUP BY pernr, fecha
                ), jornada AS (
                    SELECT pernr, fecha,
                           MIN(CASE WHEN tipo_reg = 'entrada' THEN fecha_reg END) AS entrada_jornada,
                           MAX(CASE WHEN tipo_reg = 'salida' THEN fecha_reg END) AS salida_jornada
                    FROM registros
                    GROUP BY pernr, fecha
                ), calculos AS (
                    SELECT j.pernr, j.fecha,
                           ISNULL(d.desayuno_segundos, 0) AS desayuno_segundos,
                           ISNULL(d.otros_segundos, 0) AS otros_segundos,
                           ISNULL(d.almuerzo_segundos, 0) AS almuerzo_segundos,
                           ISNULL(DATEDIFF(SECOND, j.entrada_jornada, j.salida_jornada), 0) AS jornada_total_segundos
                    FROM jornada j
                    LEFT JOIN descansos d ON j.pernr = d.pernr AND j.fecha = d.fecha
                )
                SELECT 
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.NOMBRE))) AS NOMBRE,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.APELLIDO1))) AS APELLIDO1,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(emp.APELLIDO2))) AS APELLIDO2,
                    c.pernr, c.fecha,
                    FORMAT(c.desayuno_segundos / 3600, '00') + ':' + FORMAT((c.desayuno_segundos % 3600) / 60, '00') AS horas_desayuno, 
                    FORMAT(c.otros_segundos / 3600, '00') + ':' + FORMAT((c.otros_segundos % 3600) / 60, '00') AS horas_otros, 
                    FORMAT(c.almuerzo_segundos / 3600, '00') + ':' + FORMAT((c.almuerzo_segundos % 3600) / 60, '00') AS horas_almuerzo, 
                    FORMAT((c.desayuno_segundos + c.otros_segundos + c.almuerzo_segundos) / 3600, '00') + ':' + FORMAT(((c.desayuno_segundos + c.otros_segundos + c.almuerzo_segundos) % 3600) / 60, '00') AS horas_descanso, 
                    FORMAT( CASE WHEN (c.jornada_total_segundos - c.desayuno_segundos - c.otros_segundos - c.almuerzo_segundos) < 0 THEN 0 ELSE (c.jornada_total_segundos - c.desayuno_segundos - c.otros_segundos - c.almuerzo_segundos) END / 3600, '00' ) + ':' + FORMAT( CASE WHEN (c.jornada_total_segundos - c.desayuno_segundos - c.otros_segundos - c.almuerzo_segundos) < 0 THEN 0 ELSE (c.jornada_total_segundos - c.desayuno_segundos - c.otros_segundos - c.almuerzo_segundos) END % 3600 / 60, '00' ) AS horas_producido, 
                    FORMAT(c.jornada_total_segundos / 3600, '00') + ':' + FORMAT((c.jornada_total_segundos % 3600) / 60, '00') AS horas_totales, 
                    c.desayuno_segundos AS segundos_desayuno, 
                    c.otros_segundos AS segundos_otros, 
                    c.almuerzo_segundos AS segundos_almuerzo, 
                    (c.desayuno_segundos + c.otros_segundos + c.almuerzo_segundos) AS segundos_descanso, 
                    CASE WHEN (c.jornada_total_segundos - c.desayuno_segundos - c.otros_segundos - c.almuerzo_segundos) < 0 THEN 0 ELSE (c.jornada_total_segundos - c.desayuno_segundos - c.otros_segundos - c.almuerzo_segundos) END AS segundos_producido,
                    c.jornada_total_segundos AS segundos_totales
                FROM calculos c 
                LEFT JOIN PA0002 AS emp ON c.pernr = emp.PERNR";
    
        if (!empty($filtroCondiciones)) {
            $sql .= " WHERE " . implode(' ', $filtroCondiciones);
        }
        
        $sql .= " ORDER BY c.fecha DESC, c.pernr; CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
    
        // echo $sql;
        // die;
    
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = [];
    
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
    
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }
    


    // Auditoria datos por trabajador
    public function informePresenciaOficinaDatos($fecha_ini, $pernr) {
        $conn = $this->conectarWebApp();
        $fecha_siguiente = date('Y-m-d', strtotime($fecha_ini . ' +1 day')); // Calcula el día siguiente

        $sql = "SELECT 
                    reg.id,
                    reg.pernr, 
                    reg.tipo_reg, 
                    CAST(reg.fecha_reg AS DATETIME) AS fecha_reg, 
                    reg.id_dispo, 
                    reg.fecha,
                    reg.manual, 
                    ubi.sede, 
                    ubi.nombre as nombre_ubi,
                    reg.motivo
            FROM webphp_registro_horario reg
            LEFT JOIN webphp_dispositivos AS dispo ON reg.id_dispo = dispo.id_dispositivo
            LEFT JOIN webphp_ubicaciones_dispo AS ubi ON dispo.ubicacion = ubi.id
            WHERE 
                (
                    (CAST(COALESCE(reg.fecha_reg, reg.fecha) AS DATE) = '$fecha_ini'
                    AND COALESCE(reg.fecha_reg, reg.fecha) >= '$fecha_ini 03:00:00' 
                    AND reg.pernr = '$pernr')

                    OR (COALESCE(reg.fecha_reg, reg.fecha) >= '$fecha_siguiente 00:00:00'
                    AND COALESCE(reg.fecha_reg, reg.fecha) < '$fecha_siguiente 03:00:00'
                    AND reg.pernr = '$pernr')
                )

                AND (
                    reg.tipo_reg <> 'salida' 
                    OR reg.id > (
                        SELECT MIN(id) 
                        FROM webphp_registro_horario 
                        WHERE pernr = reg.pernr 
                            AND tipo_reg = 'entrada' 
                            AND CAST(COALESCE(fecha_reg, fecha) AS DATE) = '$fecha_ini'
                        )
                    )
            ORDER BY reg.id ASC";
        
        // echo $sql;
        // die;

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
    
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Validar registro de presencia oficina
    public function validar_registro($id, $fecha, $estado, $motivo) {
        $conn = $this->conectarWebApp();  

        $sql = "UPDATE webphp_registro_horario SET fecha_reg = '".$fecha."', manual = '".$estado."', motivo = '".$motivo."' WHERE id=".$id;

        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta === false) {
            return false;
        }
        return true;
    }



    // Trabajadores Almacen
    public function trabajadoresAlmacen() {
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT DISTINCT EXTERNALID, USERNAME FROM [SistemaProduccion].[dbo].[Aperturas] ORDER BY USERNAME ASC;";
    
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }
    


    // Informe presencia almacen
    public function informePresenciaAlmacen($fecha_ini, $fecha_fin, $pernr, $puerta){
        $conn = $this->conectarMuleSoft();
        $sql = "SELECT [ID]
                      ,[USERID]
                      ,[EXTERNALID]
                      ,[USERNAME]
                      ,[DOORID]
                      ,[DOORNAME]
                      ,[EVENTTYPE]
                      ,[OPENINGDATE]
                FROM [SistemaProduccion].[dbo].[Aperturas] WHERE 1=1 ";
                        

            if ($fecha_ini != '' && $fecha_fin != '') {
                $sql .= "AND OPENINGDATE BETWEEN '".$fecha_ini." 00:00:00' AND '".$fecha_fin." 23:59:59'";
            } else if ($fecha_ini != '') {
                $sql .= "AND OPENINGDATE BETWEEN '".$fecha_ini." 00:00:00' AND '".$fecha_ini." 23:59:59'";
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
                $sql .= "AND DOORID = '".$puerta."' ";
            }
    
            $sql .= "ORDER BY OPENINGDATE DESC;";
                  
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();

        if ($consulta == FALSE)
            die($this->FormatErrors(sqlsrv_errors()));
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Puertas para informe
    public function puertas_tesa(){
        $conn = $this->conectarWebApp();  
        $sql = "SELECT [DOORID], [DOORNAME]
                FROM [SistemaProduccion].[dbo].[Aperturas]
                GROUP BY [DOORID], [DOORNAME]
                ORDER BY DOORID ASC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Info de un usuario en tesa
    public function tesa_info_usu($id){
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
        if($response === false) {
            $result['message'] = 'Error de conexión: ' . curl_error($curl);
            $result['debug_info']['curl_error'] = curl_error($curl);
            curl_close($curl);
            return $result;
        }

        curl_close($curl);

        // Verificar el código de respuesta HTTP
        if($httpCode !== 200) {
            $result['message'] = "Error en la llamada de la API. Código: $httpCode";
            return $result;
        }

        // Convertir XML a array
        $xmlObject = simplexml_load_string($response);
        if($xmlObject === false) {
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
    public function sedes(){
        $conn = $this->conectarWebApp();  
        $sql = "SELECT [sede]
                FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_ubicaciones_dispo]
                GROUP BY sede";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        // echo $sql;
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Ubicaciones por sede
    public function obtener_ubicaciones_por_sede($sede){
        $conn = $this->conectarWebApp();  
        $sql = "SELECT [id]
                      ,[sede]
                      ,[nombre]
                FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_ubicaciones_dispo]
                WHERE sede = '".$sede."'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }
    


    //Operarios por centro y division de trabajo
    public function operarios_centro($centro,$division){
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Añadir nueva alerta al trabajador
    public function nueva_alerta($pernr, $tipo_alerta, $descripcion, $fecha_ini, $fecha_fin, $tipo_formacion, $obligatorio, $tipo_incidente, $prioridad, $notificado, $frecuencia) {
        $conn = $this->conectarWebApp();  

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
    public function alertas_trabajador($pernr){
        $conn = $this->conectarWebApp();  
        $sql = "SELECT * FROM webphp_alertas_trabajador 
                WHERE pernr = '".$pernr."'";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  

        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Trabajadores de baja para llamamientos todos
    public function trabajadores_baja($ubi_trab){
        try {
        $conn = $this->conectarMuleSoft();

        $sql = "
                OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;    
                with LatestRemesa AS (
                        SELECT PERNR, id_remesa, fecha_remesa, nombre_remesa, ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY id_remesa DESC) as id_1
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos]
                    ),
                    LatestRegistro AS (
                        SELECT PERNR, MAX(FECHA_REGISTRO) as FECHA_REGISTRO
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos]
                        GROUP BY PERNR
                    )
                    SELECT a.*,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS, 
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.NOMBRE))) AS NOMBRE,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.APELLIDO1))) AS APELLIDO1,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.APELLIDO2))) AS APELLIDO2,
                        c.SEXO, d.MOVIL, d.PRE_TELF, LOWER(d.[CORREO]) as CORREO, 
                        r.id_remesa, YEAR(r.fecha_remesa) as ano_remesa, r.nombre_remesa,
                        lr.FECHA_REGISTRO, b.ZZLGORT
                    FROM (
                        SELECT pa0.PERNR, pa0.MASSN, pa0.BEGDA, pa0.ENDDA
                        FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0000] pa0
                        JOIN (
                            SELECT pernr, MAX(begda) AS begda
                            FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0000]
                            GROUP BY PERNR
                        ) AS pa00 ON pa0.PERNR = pa00.PERNR AND pa0.BEGDA = pa00.begda
                        WHERE pa0.MASSN = 'E2' AND pa0.STAT2 = 0 AND pa0.MASSG = '20'
                    ) a
                    LEFT JOIN (
                        SELECT pa1.PERNR, pa1.PLANS, pa1.STEXT_PLANS, pa1.ZZLGORT, pa1.ZZWERKS
                        FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0001] pa1
                        JOIN (
                            SELECT pernr, MAX(begda) AS begda
                            FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0001]
                            GROUP BY PERNR
                        ) AS pa01 ON pa1.PERNR = pa01.PERNR AND pa1.BEGDA = pa01.begda
                        WHERE pa1.ZZWERKS LIKE '1000%'
                    ) b ON a.PERNR = b.PERNR
                    LEFT JOIN [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0002] c ON a.PERNR = c.PERNR
                    LEFT JOIN (
                        SELECT PERNR, MAX(MOVIL) AS MOVIL, MAX(PRE_TELF) AS PRE_TELF, MAX(CORREO) AS CORREO
                        FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0105]
                        GROUP BY PERNR
                    ) d ON a.PERNR = d.PERNR
                    LEFT JOIN LatestRemesa r
                        ON a.PERNR = r.PERNR AND r.id_1 = 1
                    LEFT JOIN LatestRegistro lr
                        ON a.PERNR = lr.PERNR ";

                    if ($ubi_trab != '') {
                        $sql .= "WHERE b.ZZLGORT = '$ubi_trab'";
                    }
        
                    $sql .= "GROUP BY a.PERNR, a.MASSN, a.BEGDA, a.ENDDA, 
                             c.NOMBREYAPELLIDOS, c.NOMBRE, c.APELLIDO1, c.APELLIDO2, c.SEXO, d.MOVIL, d.PRE_TELF, d.CORREO, 
                             r.id_remesa, r.fecha_remesa, r.nombre_remesa, lr.FECHA_REGISTRO, b.ZZLGORT
                    ORDER BY a.BEGDA ASC;
                    CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

                    // echo $sql;
                    // die;

        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === FALSE) {
            throw new Exception(print_r(sqlsrv_errors(), true));
        }
        
        $resultado = array();
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        
        sqlsrv_free_stmt($consulta);
        sqlsrv_close($conn);
        
        return $resultado;
        } catch (Exception $e) {
            die("Error en trabajadores_baja: " . $e->getMessage());
        }
    }



    //Trabajadores disponibles para remesa de llamamientos que no esten en una o que su estado en esa sea rechazado
    public function trabajadores_baja_rem($id_remesa = null, $ano_remesa = null){
        if ($id_remesa == 0 && $ano_remesa == 0) {
            $id_remesa = null; 
            $ano_remesa = null;
        }
        try {
            $conn = $this->conectarMuleSoft();
    
            // Subconsulta para obtener el último estado de llamamiento para cada trabajador
            $subquery_ultimo_estado = "
            SELECT wrl.PERNR, wrl.ESTADO, wrl.FECHA_REGISTRO
                FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] wrl
                INNER JOIN (
                    SELECT PERNR, MAX(FECHA_REGISTRO) AS UltimaFecha
                    FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos]
                    GROUP BY PERNR
                ) ult ON wrl.PERNR = ult.PERNR AND wrl.FECHA_REGISTRO = ult.UltimaFecha
            ";
    
            // Cláusula WHERE actualizada
            $where = "
                        WHERE (a.PERNR NOT IN (
                                    SELECT PERNR
                                    FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos]
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
                                    FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos]
                                    WHERE id_remesa = ?
                                    AND YEAR(fecha_remesa) = ?
                                )
                            OR a.PERNR IN (
                                        SELECT PERNR
                                        FROM ($subquery_ultimo_estado) AS UltimoEstado
                                        WHERE UltimoEstado.ESTADO = 2
                                        OR UltimoEstado.ESTADO = 1
                                        AND EXISTS (
                                            SELECT 1
                                            FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] wrl
                                            WHERE wrl.PERNR = UltimoEstado.PERNR
                                            AND wrl.id_remesa = ?
                                            AND wrl.ano_remesa = ?
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
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos]
                    ),
                    UltimaRemesa AS (
                        SELECT PERNR, id_remesa, YEAR(fecha_remesa) AS ano_remesa,
                            ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY id_remesa DESC, fecha_remesa DESC) AS rn
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos]
                    ),
                    LatestRemesa AS (
                        SELECT PERNR, id_remesa, fecha_remesa, nombre_remesa, ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY id_remesa DESC) AS id_1
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos]
                    )
                    SELECT a.*,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.NOMBRE))) AS NOMBRE,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.APELLIDO1))) AS APELLIDO1,
                        CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(c.APELLIDO2))) AS APELLIDO2,
                         c.SEXO, d.MOVIL, d.PRE_TELF, LOWER(d.[CORREO]) as CORREO,
                        CASE WHEN url.ESTADO IS NOT NULL THEN url.ESTADO ELSE 0 END as ESTADO_LLAMAMIENTO,
                        url.FECHA_REGISTRO,
                        url.id_remesa AS ultimo_id_remesa, url.ano_remesa AS ultimo_ano_remesa,
                        r.id_remesa, r.nombre_remesa, YEAR(r.fecha_remesa) as ano_remesa,
                        CASE
                            WHEN url.id_remesa = ur.id_remesa AND url.ano_remesa = ur.ano_remesa THEN 1
                            ELSE 0
                        END AS coincide_ultimo_registro
                    FROM (
                        SELECT pa0.PERNR, pa0.MASSN, pa0.BEGDA, pa0.ENDDA
                        FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0000] pa0
                        JOIN (
                            SELECT pernr, MAX(begda) AS begda
                            FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0000]
                            GROUP BY PERNR
                        ) AS pa00 ON pa0.PERNR = pa00.PERNR AND pa0.BEGDA = pa00.begda
                        WHERE pa0.MASSN = 'E2' AND pa0.STAT2 = 0 AND pa0.MASSG = '20'
                    ) a
                    LEFT JOIN (
                        SELECT pa1.PERNR, pa1.PLANS, pa1.STEXT_PLANS, pa1.ZZLGORT, pa1.ZZWERKS
                        FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0001] pa1
                        JOIN (
                            SELECT pernr, MAX(begda) AS begda
                            FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0001]
                            GROUP BY PERNR
                        ) AS pa01 ON pa1.PERNR = pa01.PERNR AND pa1.BEGDA = pa01.begda
                        WHERE pa1.ZZWERKS LIKE '1000%'
                    ) b ON a.PERNR = b.PERNR
                    LEFT JOIN [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0002] c ON a.PERNR = c.PERNR
                    LEFT JOIN (
                        SELECT PERNR, MOVIL, PRE_TELF, CORREO
                        FROM (
                            SELECT 
                                PERNR, 
                                MOVIL, 
                                PRE_TELF, 
                                CORREO, 
					            ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY FECHA_IN DESC) AS rn
                        FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0105] )
                        d WHERE rn = 1 ) d ON a.PERNR = d.PERNR
                    LEFT JOIN UltimoRegistroLlamamiento url
                        ON a.PERNR = url.PERNR AND url.rn = 1
                    LEFT JOIN LatestRemesa r
                        ON a.PERNR = r.PERNR AND r.id_1 = 1
                    LEFT JOIN UltimaRemesa ur ON a.PERNR = ur.PERNR AND ur.rn = 1
                    $where
                    GROUP BY a.PERNR, a.MASSN, a.BEGDA, a.ENDDA,
                            c.NOMBREYAPELLIDOS, c.NOMBRE, c.APELLIDO1, c.APELLIDO2, c.SEXO, d.MOVIL, d.PRE_TELF, d.CORREO,
                            url.ESTADO, url.FECHA_REGISTRO, url.id_remesa, url.ano_remesa,
                            r.id_remesa, r.nombre_remesa, r.fecha_remesa,
                            ur.id_remesa, ur.ano_remesa
                    ORDER BY a.BEGDA ASC;
                    CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
    
                    // echo $sql;
                    // die;
            // Ejecutar consulta
            $consulta = sqlsrv_query($conn, $sql, $params);
            
            if ($consulta === FALSE) {
                throw new Exception(print_r(sqlsrv_errors(), true));
            }
    
            $resultado = array();
            while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
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

    public function send_mail($pernr, $nombre, $tipo_llamamiento2, $fecha_registro, $info_contacto, $estado, $id_usuario, $id_remesa, $ano_remesa, $mail_usu_web, $mensaje_usu_web){
        //Insertamos el registro y enviamos el correo
        $conn = $this->conectarWebApp();
        
        // Verificar si se tienen todos los datos para enviar el correo
        $sql = "INSERT INTO webphp_registros_llamamientos (PERNR, TIPO_LLAMAMIENTO, INFO_CONTACTO, FECHA_REGISTRO, ESTADO, ID_USUARIO, ID_REMESA, ANO_REMESA) 
                VALUES (
                '".$pernr."',  
                '".$tipo_llamamiento2."', 
                '".$info_contacto."', 
                '".$fecha_registro."',
                '".$estado."',
                '".$id_usuario."',
                ".(is_null($id_remesa) ? 'NULL' : "'".$id_remesa."'").", 
                ".(is_null($ano_remesa) ? 'NULL' : "'".$ano_remesa."'")."
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
            $mail->Username = 'llamamientorrhh@surexport.es';
            $mail->Password = 'Ducu545130';
        
            // Correo de envio a correo de trabajador
            $mail->setFrom('llamamientorrhh@surexport.es', 'Surexport');
            $mail->addAddress($info_contacto, $nombre);
            // $mail->addAddress($mail_usu_web, 'Prueba Correo'); // Para realizar pruebas (Email Raúl)
            $mail->addCC($mail_usu_web); 
        
            // Contenido mensaje
            $mail->isHTML(true); // Formato del correo HTML
            $mail->Subject = 'Surexport';
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
    public function reg_llamada($pernr, $tipo_llamamiento, $pre_contacto, $fecha_registro, $info_contacto, $estado, $motivo, $descipcion, $id_usuario, $id_remesa, $ano_remesa){

        // Conectar a la base de datos
        $conn = $this->conectarWebApp();  
        $sql = "INSERT INTO webphp_registros_llamamientos (PERNR, TIPO_LLAMAMIENTO, INFO_CONTACTO, FECHA_REGISTRO, ESTADO, MOTIVO, DESCRIPCION, ID_USUARIO, ID_REMESA, ANO_REMESA) 
                VALUES (
                '".$pernr."',  
                '".$tipo_llamamiento."', 
                '".$pre_contacto.$info_contacto."', 
                '".$fecha_registro."',
                '".$estado."',
                '".$motivo."',
                '".$descipcion."',
                '".$id_usuario."',
                '".$id_remesa."', 
                '".$ano_remesa."'
                );";
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta === false) {
            return false;
        } else {
            return true;
        }
    }
    


    //Datos de los registros de llamamiento por trabajador

    public function llamamientos_trabajador($pernr){
        $conn = $this->conectarWebApp();
        $sql = "SELECT [ID]
                      ,[PERNR]
                      ,[TIPO_LLAMAMIENTO]
                      ,[INFO_CONTACTO]
                      ,[FECHA_REGISTRO]
                      ,[ESTADO]
                      ,[MOTIVO]
                      ,[ID_USUARIO]
                      ,[ID_REGISTRO_RELACION]
                      ,(SELECT CONCAT(nombre,' ',apellidos) FROM webphp_usuarios WHERE id = webphp_registros_llamamientos.ID_USUARIO) AS NOMBRE_USUARIO
                      ,(SELECT COUNT(*) FROM webphp_registros_llamamientos AS child WHERE child.ID_REGISTRO_RELACION = webphp_registros_llamamientos.ID) AS NUM_RELACIONES
                      ,NUM_ENVIO
                FROM [webphp_registros_llamamientos]
                WHERE PERNR = $pernr 
                ORDER BY FECHA_REGISTRO DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            echo $sql;
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Registros de llamamiento para la pagina Registros
    public function registros_llamamiento($txt_pernr, $txt_nombre, $estado, $tipo_llama, $desde, $hasta, $filtros){
        $conn = $this->conectarMuleSoft();
        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;

                SELECT 
                    a.[ID],
                    a.[PERNR],
                    a.[TIPO_LLAMAMIENTO],
                    a.[INFO_CONTACTO],
                    a.[FECHA_REGISTRO],
                    a.[ESTADO],
                    a.[MOTIVO],
                    a.[ID_USUARIO],
                    a.[ID_REGISTRO_RELACION],
                    a.NUM_ENVIO,
                    (SELECT CONCAT(nombre,' ',apellidos) 
                    FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].webphp_usuarios 
                    WHERE id = a.[ID_USUARIO]) AS NOMBRE_USUARIO,
                    (SELECT COUNT(*) 
                    FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].webphp_registros_llamamientos AS child 
                    WHERE child.ID_REGISTRO_RELACION = a.[ID]) AS NUM_RELACIONES,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBREYAPELLIDOS))) AS NOMBREYAPELLIDOS,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.NOMBRE))) AS NOMBRE,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.APELLIDO1))) AS APELLIDO1,
                    CONVERT(VARCHAR(MAX), DECOMPRESS(DecryptByKey(p.APELLIDO2))) AS APELLIDO2
                FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] a
                LEFT JOIN [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0002] p 
                    ON a.PERNR = p.PERNR
                WHERE 1=1";

        // Aplicar los filtros solo si hay valores no vacíos
        if ($txt_pernr != '') {
            $sql .= " AND p.PERNR LIKE '%" . $txt_pernr . "%'";
        }

        if ($filtros == 'sin_respuesta') {
            $sql .= " AND (ESTADO = '0' OR ESTADO = '3') 
                  AND NUM_ENVIO = '2'
                  AND FECHA_REGISTRO < DATEADD(DAY, -5, GETDATE())";
        }

        if ($estado != '') {
            $sql .= " AND ESTADO = '" . $estado . "'";
        }
        if ($tipo_llama != '') {
            $sql .= " AND TIPO_LLAMAMIENTO = '" . $tipo_llama . "'";
        }
        if ($desde != '' && $hasta != '') {
            $sql .= " AND FECHA_REGISTRO BETWEEN '" .$desde. " 00:00:00' AND '" .$hasta. " 23:59:59'";
        }
        

        $sql .= " ORDER BY FECHA_REGISTRO DESC
        CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";

        // echo $sql;
        // die;

        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        if ($consulta == FALSE)
            echo $sql;
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Actualizar estado del llamamiento si se acepta el llamamiento
    public function update_estado_llama_aceptar($id, $estado, $fecha, $id_remesa, $ano_remesa){
        // Conexión a la base de datos
        $conn = $this->conectarWebApp();

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
            $tsql_insert = "INSERT INTO webphp_registros_llamamientos (PERNR, TIPO_LLAMAMIENTO, INFO_CONTACTO, FECHA_REGISTRO, ESTADO, ID_REGISTRO_RELACION, ID_USUARIO, ID_REMESA, ANO_REMESA)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params_insert = array($pernr, $tipo_llamamiento, $info_contacto, $fecha, $estado, $id_registro, $id_persona, $id_remesa, $ano_remesa);
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
    public function update_estado_llama($id, $estado, $fecha, $motivo, $descripcion, $id_remesa, $ano_remesa){
        // Conexión a la base de datos
        $conn = $this->conectarWebApp();
    
        // Consulta para obtener los datos del registro existente
        $tsql_select = "SELECT * FROM webphp_registros_llamamientos WHERE ID = ?";
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
            $tsql_insert = "INSERT INTO webphp_registros_llamamientos (PERNR, TIPO_LLAMAMIENTO, INFO_CONTACTO, FECHA_REGISTRO, ESTADO, MOTIVO, DESCRIPCION, ID_REGISTRO_RELACION, ID_USUARIO, ID_REMESA, ANO_REMESA)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params_insert = array($pernr, $tipo_llamamiento, $info_contacto, $fecha, $estado, $motivo, $descripcion, $id, $id_persona, $id_remesa, $ano_remesa);
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
    public function actualizar_contacto($PERNR, $MOVIL, $CORREO, $TELEMPRESA, $TELEMERGENCIAS, $PRE_TELF, $PRE_TELF_EMP, $PRE_TELG_EMER, $PARENT_TELF, $PARENT_TELF_EMP, $PARENT_TELF_EMER) {
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
    private function ejecutarConsulta($conn, $sql, $params = [], $options = []) {
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta === FALSE) {
            $errors = sqlsrv_errors();
            $errorMessage = "";
            foreach ($errors as $error) {
                $errorMessage .= "SQLSTATE: ".$error['SQLSTATE']."\n";
                $errorMessage .= "Code: ".$error['code']."\n";
                $errorMessage .= "Message: ".$error['message']."\n";
            }
            die($errorMessage);
        }
        return $consulta;
    }



    // Funcion para guardar los resultados para las remesas
    private function obtenerResultados($consulta) {
        $resultado = [];
        while ($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Mostrar todas las remesas disponibles
    public function Remesas_llama() {
        $conn = $this->conectarWebApp();
        $sql = "WITH UltimosEstados AS (
                    -- Obtener el registro más reciente de cada trabajador por remesa
                    SELECT 
                        reg.ID_REMESA,
                        reg.ANO_REMESA,
                        reg.PERNR,
                        MAX(reg.FECHA_REGISTRO) AS FECHA_ULTIMO,
                        MAX(reg.ID) AS ID_ULTIMO
                    FROM webphp_registros_llamamientos reg
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
                GROUP BY 
                    wrl.id_remesa, 
                    YEAR(wrl.fecha_remesa), 
                    wrl.nombre_remesa, 
                    wrl.fecha_remesa, 
                    er.estado_remesa, 
                    wrl.fecha_incorporacion,
                    wrl.sms_auto
                ORDER BY wrl.id_remesa DESC, YEAR(wrl.fecha_remesa) DESC;";
                                
            // echo $sql;
            // die;
        $consulta = $this->ejecutarConsulta($conn, $sql);
        return $this->obtenerResultados($consulta);
    }

    

    // Mostrar la informacion de las remesas al acceder a ella
    public function InfoRemesa_llama($id, $ano) {
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
                            -- 1. Si hay un último estado registrado, usarlo
                            WHEN ultimos_estados.ESTADO IS NOT NULL THEN ultimos_estados.ESTADO
                            -- 2. Si no hay en ultimos_estados, usar el de registros_padre
                            WHEN registros_padre.ESTADO IS NOT NULL THEN registros_padre.ESTADO
                            -- 3. Si no hay estados en ninguna tabla, asignar 5
                            WHEN registros_padre.ESTADO IS NULL AND ultimos_estados.ESTADO IS NULL THEN 5
                            -- 4. Si el estado es 0 y pasaron más de 15 días desde el último registro, asignar 4
                            WHEN COALESCE(registros_padre.ESTADO, ultimos_estados.ESTADO) = 0 
                                AND DATEDIFF(day, COALESCE(registros_padre.FECHA_REGISTRO, ultimos_estados.FECHA_REGISTRO), GETDATE()) > 15 THEN 4
                            ELSE 5  
                        END as ESTADO, 
                        COALESCE(ultimos_estados.FECHA_REGISTRO, registros_padre.FECHA_REGISTRO) as FECHA_REGISTRO, 
                        ultimos_estados.ID_REGISTRO_RELACION, 
                        ROW_NUMBER() OVER ( PARTITION BY w.PERNR, w.id_remesa ORDER BY COALESCE(ultimos_estados.FECHA_REGISTRO, registros_padre.FECHA_REGISTRO) DESC ) as rn 
                    FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos] w 
                    LEFT JOIN [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0002] p ON w.PERNR = p.PERNR 
                    LEFT JOIN ( 
                        SELECT PERNR, MOVIL, PRE_TELF AS PREFIJO, CORREO
                        FROM (
                            SELECT 
                                PERNR, 
                                MOVIL, 
                                PRE_TELF, 
                                CORREO, 
                                ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY BEGDA DESC) AS rn
                            FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0105]
                        ) AS contacto
                        WHERE rn = 1
                    ) c ON w.PERNR = c.PERNR
                    LEFT JOIN (  
                        SELECT pernr, ID_REMESA, ESTADO, FECHA_REGISTRO, ID 
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] 
                        WHERE ID_REGISTRO_RELACION IS NULL 
                    ) registros_padre ON w.PERNR = registros_padre.pernr 
                                    AND w.id_remesa = registros_padre.ID_REMESA 
                    LEFT JOIN ( 
                        SELECT pernr, ID_REMESA, ESTADO, FECHA_REGISTRO, ID_REGISTRO_RELACION 
                        FROM (
                            SELECT *, ROW_NUMBER() OVER (PARTITION BY pernr, ID_REMESA ORDER BY FECHA_REGISTRO DESC) as rn 
                            FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registros_llamamientos] 
                        ) ranked 
                        WHERE rn = 1
                    ) ultimos_estados ON w.PERNR = ultimos_estados.pernr 
                                    AND w.id_remesa = ultimos_estados.ID_REMESA 
                    WHERE w.id_remesa = ? AND YEAR(w.fecha_remesa) = ? 
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

        $params = [$id, $ano];
        $consulta = $this->ejecutarConsulta($conn, $sql, $params);
        $resultado = $this->obtenerResultados($consulta);
        
        session_write_close();
        return $resultado;
    }


    
    // Añadir nueva remesa para llamamiento
    public function nuevaRemesa($nombre, $telefono, $fecha_ini, $sms) {
        $conn = $this->conectarWebApp();
        
        // Obtener el año actual (sin importar otras fechas)
        $ano_remesa = date('Y');
        
        // Consultar el máximo id_remesa del año actual
        $sql = "SELECT MAX(id_remesa) AS id_remesa FROM webphp_remesas_llamamientos WHERE YEAR(fecha_remesa) = ?";
        $consulta = $this->ejecutarConsulta($conn, $sql, [$ano_remesa], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        
        // Calcular el siguiente id_remesa
        $id_remesa = ($row && !is_null($row['id_remesa'])) ? $row['id_remesa'] + 1 : 1;
        
        // Insertar la nueva remesa
        $insert_result = $this->insertarRemesa($conn, $id_remesa, $nombre, $telefono, $fecha_ini, $sms);
        
        sqlsrv_close($conn);

        // Devolver un array con el resultado
        if ($insert_result) {
            return ['success' => true, 'message' => 'Remesa creada correctamente.'];
        } else {
            return ['success' => false, 'message' => 'Error al crear la remesa.'];
        }

        
    }



    // Insertar la nueva remesa, añadir el trabajador a la remesa y añadir el registro de llamamiento SMS
    public function insertarRemesa($conn, $id_remesa, $nombre_remesa, $telefono, $fecha_ini, $sms) {
        // Obtener la fecha de la remesa existente
        $sql_fecha = "SELECT fecha_remesa FROM webphp_remesas_llamamientos WHERE id_remesa = ?";
        $params_fecha = [$id_remesa];
        $consulta_fecha = $this->ejecutarConsulta($conn, $sql_fecha, $params_fecha);
        $row_fecha = sqlsrv_fetch_array($consulta_fecha, SQLSRV_FETCH_ASSOC);
        
        // Establecer la fecha de remesa
        if ($row_fecha && isset($row_fecha['fecha_remesa'])) {
            $fecha_remesa = ($row_fecha['fecha_remesa'] instanceof DateTime) 
                ? $row_fecha['fecha_remesa']->format('Y-m-d')
                : $row_fecha['fecha_remesa'];
        } else {
            $fecha_remesa = date('Y-m-d');
        }
        
        // Fecha y hora actual para el registro
        $fecha_registro = date('Y-m-d H:i:s');
        
        // Obtener el año de la remesa directamente de la fecha formateada
        $ano_remesa = date('Y', strtotime($fecha_remesa));
        
        // Para cada trabajador en el array
        foreach ($_POST['user_remesas'] as $pernr) {
            // Obtener datos del trabajador
            $sql_trabajador = "SELECT [PERNR]
                                    ,MAX([MOVIL]) AS MOVIL
                                    ,MAX([PRE_TELF]) AS PREFIJO
                                FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA0105]
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
    public function anadirCandidatosARemesa($id_remesa, $ano_remesa, $nombre_remesa = null, $telefono, $fecha_ini, $sms) {
        $conn = $this->conectarWebApp();
        
        if (empty($nombre_remesa)) {
            $sql = "SELECT nombre_remesa, fecha_incorporacion, sms_auto FROM webphp_remesas_llamamientos WHERE id_remesa = ?";
            $consulta = $this->ejecutarConsulta($conn, $sql, [$id_remesa]);
            $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
            $nombre_remesa = $row['nombre_remesa'];
            $fecha_ini = $row['fecha_incorporacion'];
            $sms = $row['sms_auto'];
        }

        $insert_result = $this->insertarRemesa($conn, $id_remesa, $nombre_remesa, $telefono, $fecha_ini, $sms);
        
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
    public function inserCandidato($app){
        $conn = $this->conectarWebApp();
        if ($app == 1) {
            //Consultamos que el id no esté ya en el sistema
            $sql = "select * FROM webphp_candidatos WHERE id='".$_POST['ID']."'";
            $params = array();
            $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
            $consulta = sqlsrv_query($conn, $sql, $params, $options);
            if ($consulta == FALSE){
                die($this->FormatErrors(sqlsrv_errors()));
            }else{
                if ((sqlsrv_num_rows($consulta)==0) or (is_null($consulta))) {
                    //Consultamos si hay archivos que subir
                    $url_img = array();
                    if (isset($_FILES)) {
                        for ($i=1; $i <=5 ; $i++) {
                            if (isset($_FILES["FOTO".$i]) and $_FILES["FOTO".$i]["name"]!="") {
                                //Creamos la carpeta para almacenar las imágenes
                                $url_base = "img/candidatos/".$_POST['ID'];
                                if (!file_exists($url_base)) {
                                    mkdir($url_base, 0777, true);
                                }
                                //url donde vamos a almacenar la imagen
                                $url_img[$i] = $url_base."/";
                                $fileName = basename($_FILES["FOTO".$i]["name"]);
                                $url_img[$i] .= date("YmdHis") . $fileName;
                                $fileType = pathinfo($url_img[$i], PATHINFO_EXTENSION);

                                // Permitimos solo unas extensiones
                                $allowTypes = array('jpg','png','jpeg','gif','PNG','JPG');
                                if(in_array($fileType, $allowTypes)){
                                    // Image temp source
                                    $imageTemp = $_FILES["FOTO".$i]["tmp_name"];

                                    // Comprimos el fichero
                                    $compressedImage = $this->compressImage($imageTemp, $url_img[$i], 75);
                                    if($compressedImage){
                                        $img_upload = true;
                                    }else{
                                        $url_img[$i]="";
                                        $img_upload = false;
                                    }
                                }else{
                                    $url_img[$i]="";
                                    $img_upload = false; 
                                } 
                            }else{
                                $url_img[$i]="";
                                $img_upload = true;
                            }
                        }
                    }


                    $tsql = "insert into webphp_candidatos (id, nombre, apellido1, apellido2, sexo, fecha_nac, lugar_nac, pais_nac, tipo_doc, valor_doc, tipo_doc_2, valor_doc_2, tipo_doc_3, valor_doc_3, num_hijos, nombre_padre, nombre_madre, nacionalidad, poblacion, cod_postal, sigla_via, calle, num_edificio, distrito, region, estado_civil, experiencia, cualificacion, observaciones, mail, pre_telf, telf, telf_2, sfsf, grupo_id, firma, foto1, foto2, foto3, foto4, foto5, id_usu, fecha, idioma, estado) output inserted.* values ('".$_POST['ID']."','".utf8_encode($_POST['NOMBRE'])."','".utf8_encode($_POST['APELLIDO1'])."','".utf8_encode($_POST['APELLIDO2'])."','".utf8_encode($_POST['SEXO'])."','".$_POST['FECHA_NACIMIENTO']."','".utf8_encode($_POST['LUGAR_NACIMIENTO'])."','".utf8_encode($_POST['PAIS_NACIMIENTO'])."','".$_POST['TIPO_DOCUMENTO']."','".utf8_encode($_POST['VALOR_DOCUMENTO'])."','".$_POST['TIPO_DOC2']."','".$_POST['VALOR_DOC2']."','".$_POST['TIPO_DOC3']."','".$_POST['VALOR_DOC3']."','".$_POST['NUMERO_HIJOS']."','".utf8_encode($_POST['NOMBRE_PADRE'])."','".utf8_encode($_POST['NOMBRE_MADRE'])."','".utf8_encode($_POST['NACIONALIDAD'])."','".utf8_encode($_POST['POBLACION'])."','".$_POST['CODIGO_POSTAL']."','".utf8_encode($_POST['SIGLA_VIA'])."','".utf8_encode($_POST['CALLE'])."','".utf8_encode($_POST['NUM_EDIFICIO'])."','".utf8_encode($_POST['DISTRITO'])."','".utf8_encode($_POST['REGION'])."','".utf8_encode($_POST['ESTADO_CIVIL']."','".$_POST['EXPERIENCIA']."','".$_POST['CUALIFICACION']."','".$_POST['OBSERVACIONES']."','".$_POST['MAIL'])."','".$_POST['PREFIJO']."','".$_POST['TELEFONO']."','".$_POST['TELEFONO2']."','".$_POST['SFSF']."','".$_POST['GRUPO_ID']."','".$_POST['FIRMA']."','".$url_img[1]."','".$url_img[2]."','".$url_img[3]."','".$url_img[4]."','".$url_img[5]."','".$_POST['ID_CREADOR']."','".date("Y-m-d H:i:s")."','".$_POST['IDIOMA']."', 0)";
                    //Almacenamos el log de la base de datos
                    // $file = fopen("logbbdd.txt", "a");
                    // fwrite($file, date("Y-m-d H:i:s")." ----> ".$tsql.PHP_EOL);
                    // fclose($file);
                    //Final Log
                    $insertfila = sqlsrv_query($conn, $tsql);
                    if ($insertfila==TRUE) {
                        //Obtenemos la fila insertada utilizando la clausula output inserted.*
                        $fila = sqlsrv_fetch_array($insertfila, SQLSRV_FETCH_ASSOC);
                        $resultado = $fila['id'];
                    }else{
                        $resultado = false;
                    }
                }else{
                    //Si existe el registro comprobamos lo que devuelve
                    $fila = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                    //Almacenamos el log de la base de datos
                    $file = fopen("logbbdd.txt", "a");                    
                    if ($_POST['ID']==$fila['id'] and $_POST['NOMBRE']==$fila['nombre'] and $_POST['APELLIDO1']==$fila['apellido1']) {
                        fwrite($file, date("Y-m-d H:i:s")." ----> Error al consultar por ID RETURN TRUE: ".$fila['id']."--->".$fila['nombre']."--->".$fila['apellido1'].PHP_EOL);
                        $resultado = true;
                    }else{
                        fwrite($file, date("Y-m-d H:i:s")." ----> Error al consultar por ID RETURN FALSE: ".$fila['id']."--->".$fila['nombre']."--->".$fila['apellido1'].PHP_EOL);
                        $resultado = false;
                    }
                    fclose($file);
                    //Final Log
                }
            }
        }else{
            //Consultamos el proximo id para el dispositivo web con el id 1
            $sql = "select max(id) as id FROM webphp_candidatos WHERE id<'1000000'";
            $params = array();
            $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
            $consulta = sqlsrv_query($conn, $sql, $params, $options);
            if ($consulta == FALSE){
                die($this->FormatErrors(sqlsrv_errors()));
            }else{
                if ((sqlsrv_num_rows($consulta)==0) or (is_null($consulta))) {
                    $id=1;
                }else{
                    $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                    $id = $row['id']+1;
                }
            }
            $tsql = "insert into webphp_candidatos (id, nombre, apellido1, apellido2, sexo, fecha_nac, lugar_nac, pais_nac, tipo_doc, valor_doc, num_hijos, nombre_padre, nombre_madre, nacionalidad, poblacion, cod_postal, sigla_via, calle, num_edificio, distrito, region, estado_civil, experiencia, cualificacion, observaciones, mail, telf, sfsf, id_usu, fecha, estado) output inserted.* values ('".$id."','".$_POST['nombre']."','".$_POST['apellido1']."','".$_POST['apellido2']."','".$_POST['sexo']."','".$_POST['fecha_nac']."','".$_POST['lugar_nac']."','".$_POST['pais_nac']."','".$_POST['tipo_doc']."','".$_POST['valor_doc']."','".$_POST['num_hijos']."','".$_POST['nombre_padre']."','".$_POST['nombre_madre']."','".$_POST['nacionalidad']."','".$_POST['poblacion']."','".$_POST['cod_postal']."','".$_POST['sigla_via']."','".$_POST['calle']."','".$_POST['num_edificio']."','".$_POST['distrito']."','".$_POST['region']."','".$_POST['estado_civil']."','".$_POST['experiencia']."','".$_POST['cualificacion']."','".$_POST['observaciones']."','".$_POST['mail']."','".$_POST['telf']."','".$_POST['sfsf']."','".$_SESSION["id_user_surexport_appreclu"]."','".date("Y-m-d H:i:s")."', 0)";
            $insertfila = sqlsrv_query($conn, $tsql);
            if ($insertfila==TRUE) {
                //Obtenemos la fila insertada utilizando la clausula output inserted.*
                $fila = sqlsrv_fetch_array($insertfila, SQLSRV_FETCH_ASSOC);
                $resultado = $fila['id'];
            }else{
                $resultado = false;
            }
        }
        return ($resultado);
    }



    //Update candidato
    public function updateCandidato(){
        $conn = $this->conectarWebApp();
        //Consultamos si hay archivos que subir
        $url_img = array();
        if (isset($_FILES)) {
            if (isset($_FILES["img"]) and $_FILES["img"]["name"]!="") {
                //Creamos la carpeta para almacenar las imágenes
                $url_base = "img/candidatos/".$_POST['ID'];
                if (!file_exists($url_base)) {
                    mkdir($url_base, 0777, true);
                }
                //url donde vamos a almacenar la imagen
                $url_img = $url_base."/";
                // File info 
                $fileName = basename($_FILES["img"]["name"]); 
                $url_img .= date("YmdHis") . $fileName; 
                $fileType = pathinfo($url_img, PATHINFO_EXTENSION); 

                // Permitimos solo unas extensiones
                $allowTypes = array('jpg','png','jpeg','gif','PNG','JPG');
                if(in_array($fileType, $allowTypes)){ 
                    // Image temp source 
                    $imageTemp = $_FILES["img"]["tmp_name"]; 

                    // Comprimos el fichero
                    $compressedImage = $this->compressImage($imageTemp, $url_img, 75); 
                    if($compressedImage){ 
                        $img_upload = true; 
                    }else{ 
                        $url_img="";
                        $img_upload = false;
                    } 
                }else{ 
                    $url_img="";
                    $img_upload = false; 
                } 
            }else{
                $url_img="";
                $img_upload = false;
            }
        }
        $sql_upd = "update webphp_candidatos set nombre='".$_POST['NOMBRE']."', apellido1='".$_POST['APELLIDO1']."', apellido2='".$_POST['APELLIDO2']."', sexo='".$_POST['SEXO']."', fecha_nac='".$_POST['FECHA_NACIMIENTO']."', lugar_nac='".$_POST['LUGAR_NACIMIENTO']."', pais_nac='".$_POST['PAIS_NACIMIENTO']."', tipo_doc='".$_POST['TIPO_DOCUMENTO']."', valor_doc='".$_POST['VALOR_DOCUMENTO']."',tipo_doc_2='".$_POST['TIPO_DOCUMENTO_2']."', valor_doc_2='".$_POST['VALOR_DOCUMENTO_2']."',tipo_doc_3='".$_POST['TIPO_DOCUMENTO_3']."', valor_doc_3='".$_POST['VALOR_DOCUMENTO_3']."', num_hijos='".$_POST['NUMERO_HIJOS']."', nombre_padre='".$_POST['NOMBRE_PADRE']."', nombre_madre='".$_POST['NOMBRE_MADRE']."', nacionalidad='".$_POST['NACIONALIDAD']."', poblacion='".$_POST['POBLACION']."', cod_postal='".$_POST['CODIGO_POSTAL']."', sigla_via='".$_POST['SIGLA_VIA']."', calle='".$_POST['CALLE']."', num_edificio='".$_POST['NUM_EDIFICIO']."', distrito='".$_POST['DISTRITO']."', region='".$_POST['REGION']."', estado_civil='".$_POST['ESTADO_CIVIL']."', experiencia='".$_POST['EXPERIENCIA']."', cualificacion='".$_POST['CUALIFICACION']."', observaciones='".$_POST['OBSERVACIONES']."', mail='".$_POST['MAIL']."', telf='".$_POST['TELEFONO']."', sfsf='".$_POST['SFSF']."', estado='".$_POST['estado']."'";
        //Comprobamos si tiene alguna imagen que actualizar
        if ($url_img!="" and $img_upload==true and $_POST['tipo_img']!=0) {
            $sql_upd .= ", foto".$_POST['tipo_img']."='".$url_img."'";
        }
        //Añadimos el id del usuario a actualizar
        $sql_upd .= " where id=".$_POST['ID'];
        $updatefila = sqlsrv_query($conn, $sql_upd);
        if ($updatefila==TRUE) {
            $resultado = true;
        }else{
            $resultado = false;
        }
        return ($resultado);
    }



    //Mostramos el listado de candidatos
    public function buscarCandidatos(){
        $conn = $this->conectarWebApp(); 
        $sql = "select id, nombre, apellido1, apellido2, tipo_doc, valor_doc, sexo from webphp_candidatos where id is not null ";
        if ((isset($_POST['txt_bus']) and $_POST['txt_bus']!="") || (isset($_POST['grupo']) and $_POST['grupo']!="") || (isset($_POST['estado']) and $_POST['estado']!="")) {
            if ($_POST['txt_bus']!="") {
                $sql .= "and (nombre like '%".$_POST['txt_bus']."%' or apellido1 like '%".$_POST['txt_bus']."%' or apellido2 like '%".$_POST['txt_bus']."%' or valor_doc like '%".$_POST['txt_bus']."%' or CONCAT(nombre,' ',apellido1,' ',apellido2) like '%".$_POST['txt_bus']."%') ";
            }
            if ($_POST['grupo']!="") {
                $sql .= "and grupo_id=".$_POST['grupo']." ";
            }
            if ($_POST['estado']!="") {
                $sql .= "and estado=".$_POST['estado']." ";
            }
        }
        $sql .= "order by nombre, apellido1";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Obtenemos todos los datos de un candidato
    public function infoCandidato($id){
        $conn = $this->conectarWebApp();
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
    public function elimCandidato($id){
        $conn = $this->conectarWebApp();
        //Eliminamos todas las relaciones que tenga el usuario
        $sql_del_rel = "delete from webphp_relaciones_usuarios_2 where id_usuario='".$id."'";  
        $fila_del_rel = sqlsrv_query($conn, $sql_del_rel);
        if ($fila_del_rel==TRUE) {
            //Eliminamos toda la información del usuario
            $sql_del_cand = "delete from webphp_candidatos where id='".$id."'";
            $fila_del_cand = sqlsrv_query($conn, $sql_del_cand);
            if ($fila_del_cand==TRUE) {
                //Eliminamos todo el contenido de su directorio
                $files = glob('img/candidatos/'.$id.'/*');
                foreach($files as $file){
                    if(is_file($file))
                    unlink($file);
                }
                //Eliminamos el directorio
                rmdir('img/candidatos/'.$id);
                $resultado = "Candidato eliminado correctamente.";
            }else{
                $resultado = "Ha ocurrido un error al eliminar el candidato, inténtelo de nuevo más tarde.";
            }
        }else{
            $resultado = "Ha ocurrido un error al eliminar el candidato, inténtelo de nuevo más tarde.";
        }
    }



    //Función para mostrar todos los paises
    public function Paises(){
        $conn = $this->conectarWebApp();  
        $sql = "select UPPER(PAIS_NAC) AS PAIS_NAC from webphp_nacionalidad order by PAIS_NAC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Función para mostrar todos los paises
    public function Nacionalidad(){
        $conn = $this->conectarWebApp();  
        $sql = "select UPPER(GENTILICIO_NAC) AS GENTILICIO_NAC  from webphp_nacionalidad order by GENTILICIO_NAC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Función para mostrar todos los grupos
    public function Grupos(){
        $conn = $this->conectarWebApp();  
        $sql = "select *, (select COUNT(id) from webphp_candidatos where grupo_id=webphp_grupos.id) as cont from webphp_grupos where (elim=0 or elim is null) order by nombre";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Insertamos un nuevo grupo
    public function inserGrupo($nombre, $descrip){
        $conn = $this->conectarWebApp();  
        $tsql = "insert into webphp_grupos (nombre, descrip, elim) values ('".$nombre."','".$descrip."', 0)";  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            $resultado = true;
        }else{
            $resultado = false;
        }
        return ($resultado);
    }



    //Función para eliminar un grupo
    public function eliminarGrupo($id){
        $conn = $this->conectarWebApp();  
        $tsql = "update webphp_grupos set elim=1 where id=".$id;  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            $resultado = "Grupo eliminado correctamente.";
        }else{
            $resultado = "Ha ocurrido un error al eliminar el grupo, inténtelo de nuevo más tarde";
        }  
        return $resultado;
    }



    //Obtenemos todos los datos de un grupo
    public function infoGrupo($id){
        $conn = $this->conectarWebApp();  
        $sql = "select * FROM webphp_grupos WHERE id='".$id."'";
        $params = array();
        $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE){
            die($this->FormatErrors(sqlsrv_errors()));
        }else{
            if ((sqlsrv_num_rows($consulta)==0) or (is_null($consulta))) {
                return false;
            }else{
                $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                return $row;
            }
        }
    }



    //Función para actualizar la información de un grupo
    public function updateGrupo($id, $nombre, $descrip){
        $conn = $this->conectarWebApp();  
        $tsql = "update webphp_grupos set nombre='".$nombre."', descrip='".$descrip."' where id='".$id."'";  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            $resultado = "Grupo actualizado correctamente.";
        }else{
            $resultado = "Ha ocurrido un error al actualizar los datos del grupo, inténtelo de nuevo más tarde.";
        } 
        return $resultado;
    }

    

    //Insertamos una nueva relación entre usuarios
    public function inserRelacion($id_relacion, $id_usuario){
        $conn = $this->conectarWebApp();
        //Comprobamos que esa relación no exista
        $sql = "select * FROM webphp_relaciones_usuarios_2 WHERE id_relacion='".$id_relacion."' and id_usuario='".$id_usuario."'";
        $params = array();
        $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE){
            die($this->FormatErrors(sqlsrv_errors()));
        }else{
            if ((sqlsrv_num_rows($consulta)==0) or (is_null($consulta))) {
                $tsql = "insert into webphp_relaciones_usuarios_2 (id_relacion, id_usuario, fecha) values ('".$id_relacion."','".$id_usuario."','".date("Y-m-d H:i:s")."')";  
                $insertfila = sqlsrv_query($conn, $tsql);
                if ($insertfila==TRUE) {
                    $resultado = true;
                }else{
                    $resultado = false;
                }
            }else{
                $resultado = true;
                //Almacenamos el log de la base de datos
                $file = fopen("logbbdd.txt", "a");                    
                fwrite($file, date("Y-m-d H:i:s")." ---->Error Relacion DUPLICADA ID_RELACION: ".$id_relacion."--->ID_USUARIO: ".$id_usuario.PHP_EOL); 
                fclose($file);
                //Final Log
            }
        }
        return ($resultado);
    }



    //Función para mostrar todas las relaciones
    public function usuariosRelaciones(){
        $conn = $this->conectarWebApp();  
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
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
        while($row_2 = sqlsrv_fetch_array($consulta_2, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row_2;
        }
        //Devolvemos el resultado
        return $resultado;
    }



    //Función para generar remesas

    public function newRemesa(){
        $conn = $this->conectarWebApp();
        //Buscamos el año si es mayor de diciembre
        if (date('n')==12) {
            $ano_remesa = date('Y')+1;
        }else{
            $ano_remesa = date('Y');
        }
        //Consultamos el maximo id de la remesa generada
        $sql = "select max(id_remesa) as id_remesa FROM webphp_remesas WHERE ano_remesa='".$ano_remesa."'";
        $params = array();
        $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE){
            die($this->FormatErrors(sqlsrv_errors()));
        }else{
            if ((sqlsrv_num_rows($consulta)==0) or (is_null($consulta))) {
                $id_remesa=1;
            }else{
                $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                $id_remesa = $row['id_remesa']+1;
            }
        }
        //Creamos el array de resultados
        $array_candidatos = array();
        //Insertamos la remesa, distinguimos 3 estados de candidato: Pendiente->0, Presentado->1, Rechazado->2
        foreach ($_POST['user_remesas'] as $value) {
            $tsql = "insert into webphp_remesas (id_remesa, ano_remesa, id_usuario, fecha, id_usuario_creacion, estado) values ('".$id_remesa."','".$ano_remesa."','".$value."','".date("Y-m-d H:i:s")."','".$_SESSION["id_user_surexport_appreclu"]."',1)";  
            $insertfila = sqlsrv_query($conn, $tsql);
            if ($insertfila==TRUE) {
                //Actualizamos el estado del candidato
                $sql_upd = "update webphp_candidatos set estado=1 where id=".$value;
                $fila_upd = sqlsrv_query($conn, $sql_upd);
                //Si es correcto consultamos los datos para generar la remesa
                $array_candidatos[] = $this->infoCandidato($value);
            }else{
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
    public function addRemesa(){
        $conn = $this->conectarWebApp();
        //Creamos el array de resultados
        $array_candidatos = array();
        //Insertamos la remesa, distinguimos 3 estados de candidato: Pendiente->0, Presentado->1, Rechazado->2
        foreach ($_POST['user_remesas'] as $value) {
            $tsql = "insert into webphp_remesas (id_remesa, ano_remesa, id_usuario, fecha, id_usuario_creacion, estado) values ('".$_POST['id_remesa']."','".$_POST['ano_remesa']."','".$value."','".date("Y-m-d H:i:s")."','".$_SESSION["id_user_surexport_appreclu"]."',1)";  
            $insertfila = sqlsrv_query($conn, $tsql);
            if ($insertfila==TRUE) {
                //Actualizamos el estado del candidato
                $sql_upd = "update webphp_candidatos set estado=1 where id=".$value;
                $fila_upd = sqlsrv_query($conn, $sql_upd);
                //Si es correcto consultamos los datos para generar la remesa
                $array_candidatos[] = $this->infoCandidato($value);
            }else{
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
    public function Remesas(){
        $conn = $this->conectarWebApp();  
        $sql = "select id_remesa, ano_remesa 
                from webphp_remesas 
                group by id_remesa, ano_remesa 
                order by id_remesa DESC, ano_remesa DESC";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Mostramos todas la información de una remesa
    public function InfoRemesa($id, $ano){
        $conn = $this->conectarWebApp();  
        $sql = "select webphp_remesas.id_remesa, webphp_remesas.ano_remesa, webphp_remesas.estado as estado_remesa, webphp_remesas.obser, webphp_remesas.id_usuario, webphp_candidatos.* 
                from webphp_remesas, webphp_candidatos 
                where webphp_remesas.id_remesa=".$id." 
                and webphp_remesas.ano_remesa=".$ano." 
                and webphp_remesas.id_usuario=webphp_candidatos.id";  
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();
        unset($_SESSION['array_candidatos']);        
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        $_SESSION['array_candidatos'] = $resultado;
        session_write_close();
        return $resultado;
    }



    //Eliminamos un candidato de una remesa
    public function elimCandidatoRemesa($id, $ano, $id_can){
        $conn = $this->conectarWebApp();
        //Eliminamos el usuario de la remesa, distinguimos 3 estados de candidato: Pendiente->0, Presentado->1, Rechazado->2
        $sql_del_rel = "delete from webphp_remesas where id_remesa='".$id."' and ano_remesa='".$ano."' and id_usuario='".$id_can."'";  
        $fila_del_rel = sqlsrv_query($conn, $sql_del_rel);
        if ($fila_del_rel==TRUE) {
            //Actualizamos el usuario para poder ser añadido en otra remesa
            $sql_del_cand = "update webphp_candidatos set estado='0' where id=".$id_can;
            $fila_del_cand = sqlsrv_query($conn, $sql_del_cand);
            if ($fila_del_cand==TRUE) {
                $resultado = "Candidato eliminado correctamente de la remesa.";
            }else{
                $resultado = "Ha ocurrido un error al eliminar el candidato, inténtelo de nuevo más tarde.";
            }
        }else{
            $resultado = "Ha ocurrido un error al eliminar el candidato, inténtelo de nuevo más tarde.";
        }
    }



    //Rechazamos un candidato de una remesa
    public function RechazarCandidatoRemesa($id, $ano, $id_can, $motivo){
        $conn = $this->conectarWebApp();
        //Rechazamos el usuario de la remesa, distinguimos 3 estados de candidato: Pendiente->0, Presentado->1, Rechazado->2
        $sql_del_rel = "update webphp_remesas set estado=2, obser='".$motivo."', id_usuario_estado='".$_SESSION["id_user_surexport_appreclu"]."', fecha_estado='".date("Y-m-d H:i:s")."' where id_remesa='".$id."' and ano_remesa='".$ano."' and id_usuario='".$id_can."'"; 
        $fila_del_rel = sqlsrv_query($conn, $sql_del_rel);
        if ($fila_del_rel==TRUE) {
            //Actualizamos el usuario para poder ser añadido en otra remesa
            $sql_del_cand = "update webphp_candidatos set estado='2' where id=".$id_can;
            $fila_del_cand = sqlsrv_query($conn, $sql_del_cand);
            if ($fila_del_cand==TRUE) {
                $resultado = "Candidato rechazado correctamente.";
            }else{
                $resultado = "Ha ocurrido un error al rechazar el candidato, inténtelo de nuevo más tarde.";
            }
        }else{
            $resultado = "Ha ocurrido un error al rechazar el candidato, inténtelo de nuevo más tarde.";
        }
    }


    
    // DISPOSITIVOS
    // Lista de dispositivos
    public function dispositivos(){
        $conn = $this->conectarWebApp();  
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
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }


    
    // Informacion de los dispositivos para actualizarlos
    public function infoDispositivo($id){
        $conn = $this->conectarWebApp();  
        $sql = "SELECT [id]
                      ,[id_dispositivo]
                      ,[nombre]
                      ,[ubicacion]
                      ,[presencia]
                      ,[activo]
                      ,(select sede from webphp_ubicaciones_dispo WHERE id = ubicacion) AS sede
                      ,(select nombre from webphp_ubicaciones_dispo WHERE id = ubicacion) AS nombre_ubi
                FROM [webphp_dispositivos]
                WHERE id = $id";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        // echo $sql;
        // var_dump($resultado);
        // die;
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Actualizar el dispositivo, activar o desactivar
    public function updateDispositivo($id, $nombre, $estado) {
        $conn = $this->conectarWebApp();  
        
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
    public function eliminarDispositivo($id) {
        $conn = $this->conectarWebApp();  
        
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
    public function añadir_dispositivo($id, $nombre, $ubicacion){
        $conn = $this->conectarWebApp();  
        $tsql = "insert into webphp_dispositivos (id_dispositivo, nombre, ubicacion, presencia, activo) 
                 values ('".$id."', '".$nombre."', '".$ubicacion."', 1, 1)";  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            $resultado = true;
        }else{
            $resultado = false;
        }
        return ($resultado);
    }



    // Listado de ubicaciones
    public function ubicaciones(){
        $conn = $this->conectarWebApp();  
        $sql = "SELECT [id]
                      ,[sede]
                      ,[nombre]
                FROM [webphp_ubicaciones_dispo]";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Insertamos nueva ubicacion
    public function añadir_ubicacion($nombre, $ubicacion){
        $conn = $this->conectarWebApp();  
        $tsql = "insert into webphp_ubicaciones_dispo (sede, nombre) 
                 values ('".$nombre."', '".$ubicacion."')";  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            $resultado = true;
        }else{
            $resultado = false;
        }
        return ($resultado);
    }



    // USUARIOS 
    //Función para mostrar todos los usuarios del sistema
    public function Usuarios(){
        $conn = $this->conectarWebApp();  
        $sql = "select * from webphp_Usuarios where (elim=0 or elim is null) order by nombre, apellidos";
        $consulta = sqlsrv_query($conn, $sql);
        $resultado = array();  
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Obtenemos todos los datos de un usuario para modificarlo
    public function infoUsuario($id){
        if ($_SESSION["tipo_user_surexport_appreclu"]!='Administrador') {
            exit("Permisos insuficientes: usted no tiene permiso para gestionar los usuarios.");
        }
        $conn = $this->conectarWebApp();  
        $sql = "select * FROM webphp_Usuarios WHERE id='".$id."'";
        $params = array();
        $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE){
            die($this->FormatErrors(sqlsrv_errors()));
        }else{
            if ((sqlsrv_num_rows($consulta)==0) or (is_null($consulta))) {
                return false;
            }else{
                $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
                return $row;
            }
        }
    }



    //Función para insertar un nuevo usuario del sistema
    public function inserUsuario($nombre, $apellidos, $usr_login, $usr_pass, $tipo_usuario, $permisos, $telefono){
        if ($_SESSION["tipo_user_surexport_appreclu"]!='Administrador') {
            exit("Permisos insuficientes: usted no tiene permiso para gestionar los usuarios.");
        }
        $conn = $this->conectarWebApp();  
        $sql = "select * from webphp_Usuarios where usr_login='".$usr_login."'";
        $params = array();
        $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $consulta = sqlsrv_query($conn, $sql, $params, $options);
        if ($consulta == FALSE){
            die($this->FormatErrors(sqlsrv_errors()));
        }else{
            if ((sqlsrv_num_rows($consulta)==0) or (is_null($consulta))) {
                //El usuario se inserta con la contraseña encriptada con md5 y procedemos a enviarle la información por email
                $tsql = "insert into webphp_Usuarios (nombre, apellidos, usr_login, usr_pass, tipo, elim, permisos, telefono) values ('".$nombre."','".$apellidos."','".$usr_login."','".md5($usr_pass)."','".$tipo_usuario."', 0, '".implode(',', $permisos)."', '".$telefono."')";  
                $insertfila = sqlsrv_query($conn, $tsql);
                if ($insertfila==TRUE) {
                    //Por último, enviamos el correo con los datos de acceso
                    $mensaje = '
                        <html>
                            <head>
                            <title>Surexport</title> 
                            </head>
                            <body>
                                <p>Estimado/a '.$nombre.' '.$apellidos.'</p>
                                <p>Nos complace darle la más cordial bienvenida al Portal de Recursos Humanos.</p>
                                
                                <p>Para acceder al portal, simplemente siga estos pasos:</p>

                                <ol>
                                    <li>Visite https://aplicaciones.surexport.es:1110/portal_rrhh/</li>
                                    <li>Nombre de usuario: '.$usr_login.'</li>
                                    <li>Contraseña: '.$usr_pass.'</li>
                                    <li>Explore todas las funciones y servicios que hemos diseñado para usted.</li>
                                </ol>
                                <p>Para cambiar la contraseña entre en su perfil arriba a la derecha.</p>
                                <br>

                                <p>Atentamente,</p>
                                <p><b>El equipo de IT</b></p>
                                <br>
                                <img src="https://surexport.es/es/wp-content/themes/SurExport/images/logo-home.png" alt="Logotipo Surexport">
                            </body>
                        </html>';
                    //Enviamos el correo con la clase phpmailer
                    $mail = new PHPMailer();
                    $mail->CharSet = 'UTF-8';
                    $mail->isSMTP();
                    $mail->Host = 'smtp.office365.com';
                    $mail->Port = 587;
                    $mail->SMTPAuth = true;
                    $mail->Username = 'itcomunication@surexport.es';
                    $mail->Password = 'Surxp2021+';
                    $mail->setFrom('itcomunication@surexport.es', 'Surexport');
                    $mail->addAddress($usr_login, $nombre);
                    $mail->Subject = 'Surexport';
                    $mail->Body = $mensaje;
                    $mail->AltBody = $mensaje;
                    if ($mail->send()) {
                        $resultado = "Usuario insertado correctamente, le hemos enviado un email con los datos de acceso.";
                    }else{
                        $resultado = "Usuario insertado correctamente, pero ha ocurrido un error al enviar el email con los datos de acceso.";
                    }
                }else{
                    $resultado = "Ha ocurrido un error al insertar el usuario, inténtelo de nuevo más tarde.";
                }
            }else{
                $resultado = "La dirección de correo introducida ya existe en el sistema.";
            }
        }
        return ($resultado);
    }

    

    //Función para actualizar la información de un usuario
    public function updateUsuario($id_usuario, $nombre, $apellidos, $tipo_usuario, $departamento, $telefono){
        $conn = $this->conectarWebApp();  
        $tsql = "update webphp_Usuarios set nombre='".$nombre."', apellidos='".$apellidos."', tipo='".$tipo_usuario."', departamento='".$departamento."', telefono='".$telefono."' where id='".$id_usuario."'";  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            $resultado = "Usuario actualizado correctamente.";
        }else{
            $resultado = "Ha ocurrido un error al actualizar los datos del usuario, inténtelo de nuevo más tarde.";
        } 
        return $resultado;
    }



    //Función para actualizar los permisos del usuario
    public function updateUsuarioPermisos($id_usuario, $permisos){
        $conn = $this->conectarWebApp();  
        $tsql = "update webphp_Usuarios set permisos='".implode(',', $permisos)."' where id='".$id_usuario."'";  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            $resultado = "Permisos del usuario actualizado correctamente.";
        }else{
            $resultado = "Ha ocurrido un error al actualizar los permisos del usuario, inténtelo de nuevo más tarde.";
        } 
        return $resultado;
    }



    //Función para restablecer la contraseña del usuario
    public function renewPass($id){
        //Consultamos el email del usuario
        $conn = $this->conectarWebApp();  
        $sql = "select * from webphp_Usuarios where id='".$id."'";
        $consulta = sqlsrv_query($conn, $sql); 
        if ($consulta == FALSE){
            die($this->FormatErrors(sqlsrv_errors()));  
        }else{
            $fila = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        }
        //Generamos una contraseña nueva de forma aleatoria
        $pass = "";
        $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
        for($i=0;$i<12;$i++) {
            $pass .= substr($str,rand(0,62),1);
        }
        $mensaje = '
        <html>
        <head>
           <title>Surexport</title> 
        </head>
        <body>
            <p>Estimado/a '.$fila['nombre'].' '.$fila['apellidos'].'</p>
            <br>

            <p>Le informamos que su contraseña para acceder al Portal de Recursos Humanos ha sido restablecida exitosamente.
            
            <p>Nuevos datos de acceso:</p>
            <ul>
                <li>Visite https://aplicaciones.surexport.es:1110/portal_rrhh/</li>
                <li>Nombre de usuario: '.$fila['usr_login'].'</li>
                <li>Contraseña: '.$pass.'</li>
            </ul>
            <p>Para cambiar la contraseña entre en su perfil arriba a la derecha.</p>

            <p>Atentamente,</p>
            <p><b>El equipo de IT</b></p>
            <br>
            <img src="https://surexport.es/es/wp-content/themes/SurExport/images/logo-home.png" alt="Logotipo Surexport">
            <br>
        </body>
        </html>';
        //Actualizamos la contraseña en la base de datos y enviamos el correo
        $tsql = "update webphp_Usuarios set usr_pass='".md5($pass)."' where id='".$id."'";  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            //Enviamos el correo con la clase phpmailer
            $mail = new PHPMailer();
            $mail->CharSet = 'UTF-8';
            //$mail->SMTPDebug = 1;
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = 'itcomunication@surexport.es';
            $mail->Password = 'Surxp2021+';
            $mail->setFrom('itcomunication@surexport.es', 'Surexport');
            $mail->addAddress($fila['usr_login'], $fila['nombre']);
            $mail->Subject = 'Surexport';
            $mail->Body = $mensaje;
            $mail->AltBody = $mensaje;
            if ($mail->send()) {
                $resultado = "Hemos enviado un correo electrónico con las nuevas contraseñas.";
            }else{
                $resultado = "Ha ocurrido un error al generar las nuevas contraseñas, inténtelo de nuevo más tarde.";
            }
        }else{
            $resultado = "Ha ocurrido un error al generar las nuevas contraseñas, inténtelo de nuevo más tarde.";
        }
        return $resultado;
    }

    

    //Función para eliminar un usuario de la base de datos
    public function eliminarUser($id){
        if ($_SESSION["tipo_user_surexport_appreclu"]!='Administrador') {
            exit("Permisos insuficientes: usted no tiene permiso para gestionar los usuarios.");
        }
        $conn = $this->conectarWebApp();  
        $tsql = "update webphp_Usuarios set elim=1 where id=".$id;  
        $insertfila = sqlsrv_query($conn, $tsql);
        if ($insertfila==TRUE) {
            $resultado = "Usuario eliminado correctamente.";
        }else{
            $resultado = "Ha ocurrido un error al eliminar un usuario, inténtelo de nuevo más tarde";
        }  
        return $resultado;
    }
 


    //Función para actualizar la contraseña del usuario que ha accedido al sistema
    public function updatePassUser($id, $usr_pass, $usr_pass_new){
        //Consultamos el email del usuario
        $conn = $this->conectarWebApp();  
        $sql = "select * from webphp_Usuarios where id='".$id."'";
        $consulta = sqlsrv_query($conn, $sql); 
        if ($consulta == FALSE){
            die($this->FormatErrors(sqlsrv_errors()));  
        }else{
            $fila = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
            if ($fila['usr_pass']==md5($usr_pass)) {            
                //Actualizamos la contraseña en la base de datos
                $tsql = "update webphp_Usuarios set usr_pass='".md5($usr_pass_new)."' where id='".$id."'";  
                $insertfila = sqlsrv_query($conn, $tsql);
                if ($insertfila==TRUE) {
                    $resultado = "Contraseña actualizada correctamente.";
                }else{
                    $resultado = "Ha ocurrido un error al actualizar la nueva contraseña, inténtelo de nuevo más tarde.";
                } 
            }else{
                $resultado = "La contraseña actual no es correcta.";
            }
        }
        return $resultado;
    }



    //Función para eliminar un usuario de la base de datos
    public function datos_usu($id){
        $conn = $this->conectarWebApp();  
        $sql = "SELECT * FROM webphp_usuarios WHERE id = '".$id."'";  
        $consulta = sqlsrv_query($conn, $sql);

        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    //Función para actualizar la información de un usuario
    public function UpdateUsuarioPerfil($id, $telefono, $departamento){
        $conn = $this->conectarWebApp();  
        $sql = "update webphp_usuarios set telefono='".$telefono."', departamento='".$departamento."' where id='".$id."'";  
        $consulta = sqlsrv_query($conn, $sql);
        if ($consulta==TRUE) {
            return true;
        }else{
            return false;
        } 
    }
    


    //Función controlar la trazabilidad del acceso al link de la carta del llamamiento
    public function trazabilidad_llama($pernr, $fecha, $num_llama, $ip) {
        // Conexión a la base de datos
        $conn = $this->conectarWebApp();
    
        // Escapar el hash MD5 recibido
        $pernr = str_replace("'", "''", $pernr);
    
        // Consultar el valor de PERNR original basado en el hash MD5
        $sqlSelect = "
            SELECT TOP 1 PERNR
            FROM [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].PA0002
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
            die("No se encontró un valor coincidente para el hash MD5 proporcionado.");
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
    public function datos_trab_carta($pernr){
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
                        u.telefono,
                        u.usr_login,
                        u.nombre,
                        u.apellidos
                    FROM PA0002 p
                    LEFT JOIN [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos] r 
                        ON HASHBYTES('MD5', CONVERT(VARCHAR(50), p.PERNR)) = HASHBYTES('MD5', CONVERT(VARCHAR(50), r.PERNR))
                    LEFT JOIN [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_usuarios] u 
                        ON r.id_usuario_creacion = u.id
                    WHERE HASHBYTES('MD5', p.PERNR) = 0x".$pernr."
                    AND (r.fecha_remesa = (
                        SELECT MAX(fecha_remesa) 
                        FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_remesas_llamamientos] 
                        WHERE HASHBYTES('MD5', CONVERT(VARCHAR(50), PERNR)) = HASHBYTES('MD5', CONVERT(VARCHAR(50), p.PERNR))
                    ) OR r.fecha_remesa IS NULL)

                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;"; 

        $consulta = sqlsrv_query($conn, $sql);
        
        if ($consulta == FALSE)  
            die($this->FormatErrors(sqlsrv_errors()));  
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }



    // Total trabajadores 1A
    public function trabajadores_1A($fecha){

        $conn = $this->conectarMuleSoft();
        $sql = "WITH UltimaAusencia AS (
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
                        pa.[PERNR] AS A1_PERNR
                    FROM [PA_ACTIVOS] pa
                    LEFT JOIN UltimaAusencia pa201 
                        ON pa.PERNR = pa201.PERNR 
                        AND pa201.rn = 1 
                        AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND pa201.ENDDA
                    WHERE pa.PERSK = '1A'
                    AND pa.STAT2 = '3'
                    AND pa.ZZWERKS = '1000'
                    AND pa201.PERNR IS NULL 
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



    // Total trabajadores 9A
    public function trabajadores_9A($fecha){

        $conn = $this->conectarMuleSoft();
        $sql = "WITH UltimaAusencia AS (
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
                        pa.[PERNR] AS A9_PERNR
                    FROM [PA_ACTIVOS] pa
                    LEFT JOIN UltimaAusencia pa201 
                        ON pa.PERNR = pa201.PERNR 
                        AND pa201.rn = 1 
                        AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND pa201.ENDDA
                    WHERE pa.PERSK = '9A'
                    AND pa.STAT2 = '3'
                    AND pa.ZZWERKS = '1000'
                    AND pa201.PERNR IS NULL 
                )

                SELECT COUNT(A9_PERNR) AS total_trabajadores
                FROM Trabajadores_9A;";
        
        $consulta = sqlsrv_query($conn, $sql);
        
        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }
        
        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row['total_trabajadores'];
    }



    // Total trabajadores 1E
    public function trabajadores_1E($fecha){

        $conn = $this->conectarMuleSoft();
        $sql = "WITH UltimaAusencia AS (
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
                        pa.[PERNR] AS E1_PERNR
                    FROM [PA_ACTIVOS] pa
                    LEFT JOIN UltimaAusencia pa201 
                        ON pa.PERNR = pa201.PERNR 
                        AND pa201.rn = 1 
                        AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND pa201.ENDDA 
                    WHERE pa.PERSK = '1E'
                    AND pa.STAT2 = '3'
                    AND pa.ZZWERKS = '1000'
                    AND pa201.PERNR IS NULL 
                )

                SELECT COUNT(E1_PERNR) AS total_trabajadores
                FROM Trabajadores_1E;";
        
        $consulta = sqlsrv_query($conn, $sql);
        
        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }
        
        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row['total_trabajadores'];
    }



    // // Total trabajadores 1D
    // public function trabajadores_1D($fecha){

    //     $conn = $this->conectarMuleSoft();
    //     $sql = "WITH UltimaAusencia AS (
    //                 SELECT 
    //                     pa201.PERNR, 
    //                     pa201.ID, 
    //                     pa201.BEGDA, 
    //                     pa201.ENDDA,
    //                     ROW_NUMBER() OVER (PARTITION BY pa201.PERNR ORDER BY pa201.ID DESC) AS rn
    //                 FROM [PA2001] pa201
    //             ),
    //             Trabajadores_D1 AS (
    //                 SELECT 
    //                     pa.[PERNR] AS D1_PERNR
    //                 FROM [PA_ACTIVOS] pa
    //                 LEFT JOIN UltimaAusencia pa201 
    //                     ON pa.PERNR = pa201.PERNR 
    //                     AND pa201.rn = 1 
    //                     AND CONVERT(DATE, '$fecha') BETWEEN pa201.BEGDA AND pa201.ENDDA 
    //                 WHERE pa.PERSK = '1D'
    //                 AND pa.STAT2 = '3'
    //                 AND pa.ZZWERKS = '1000'
    //                 AND pa201.PERNR IS NULL 
    //             )

    //             SELECT COUNT(D1_PERNR) AS total_trabajadores
    //             FROM Trabajadores_D1;";
        
    //     $consulta = sqlsrv_query($conn, $sql);
        
    //     if ($consulta === false) {
    //         die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
    //     }
        
    //     $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
    //     return $row['total_trabajadores'];
    // }




    // Trabajadores 1A que tienen presencia en la fecha
    
    public function trabajadores_presencia($fecha, $tipo){

        $conn = $this->conectarWebApp();
        $fecha_mas_uno = date('Y-m-d', strtotime($fecha . ' +1 day'));

        $sql = "SELECT 
                    count(DISTINCT wh.pernr) as total_trabajadores
                FROM [webphp_registro_horario] wh
                LEFT JOIN [".ConfigMuleSoft::$bdsrx_nombre."].[dbo].[PA_ACTIVOS] pa
                    ON pa.PERNR = wh.PERNR 
                WHERE fecha BETWEEN '{$fecha} 02:51:00' AND '{$fecha_mas_uno} 02:50:59'
                AND pa.PERSK = '$tipo'";
        
        $consulta = sqlsrv_query($conn, $sql);
        
        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }
        
        $row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC);
        return $row['total_trabajadores'];
    }


    
    // Trabajdores con asistencia o no
    public function trabajadores_conta($fecha, $tipo){
            
        $conn = $this->conectarMuleSoft();
        $fecha_mas_uno = date('Y-m-d', strtotime($fecha . ' +1 day'));

        $sql = "OPEN SYMMETRIC KEY ClaveSimétricaPA_REG 
                DECRYPTION BY CERTIFICATE CertificadoPA_REG;
                WITH UltimaAusencia AS (
                    SELECT 
                        ID,       
                        PERNR,     
                        BEGDA, 
                        ENDDA,
                        ROW_NUMBER() OVER (PARTITION BY PERNR ORDER BY ID DESC) AS rn
                    FROM [PA2001]
                ),
                Trabajadores_1A AS (
                    SELECT 
                        pa.[PERNR] AS A1_PERNR,
                        pa.PERSK,
                        pa.NOMBREYAPELLIDOS
                    FROM [PA_ACTIVOS] pa
                    LEFT JOIN UltimaAusencia pa201 
                        ON pa.PERNR = pa201.PERNR 
                        AND pa201.rn = 1
                        AND '$fecha' BETWEEN pa201.BEGDA AND pa201.ENDDA
                    WHERE pa.STAT2 = '3'
                    AND pa.PERSK = '$tipo'
                    AND pa.ZZWERKS = '1000'
                    AND pa201.PERNR IS NULL
                ),
                Trabajadores_QueHanRegistrado AS (
                    SELECT 
                        DISTINCT pernr
                    FROM [".ConfigWebApp::$bdsrx_nombre."].[dbo].[webphp_registro_horario] 
                    WHERE fecha BETWEEN '$fecha 02:51:00' AND '$fecha_mas_uno 02:50:59'
                )

                SELECT 
                    t1.A1_PERNR,
                    t1.NOMBREYAPELLIDOS,
                    t1.PERSK,  
                    CASE 
                        WHEN t2.pernr IS NOT NULL THEN 'Ha venido' 
                        ELSE 'No ha venido'
                    END AS Estado
                FROM Trabajadores_1A t1
                LEFT JOIN Trabajadores_QueHanRegistrado t2
                    ON t1.A1_PERNR = t2.pernr
                ORDER BY estado, t1.A1_PERNR
                CLOSE SYMMETRIC KEY ClaveSimétricaPA_REG;";
        
        $consulta = sqlsrv_query($conn, $sql);
        
        if ($consulta === false) {
            die("Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true));
        }
        while($row = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)){  
            $resultado[] = $row;
        }
        return $resultado;
    }

}

?>