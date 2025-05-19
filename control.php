<?php
//conecto con la base de datos
include_once("config.php");
require_once("models/sqlsrvModel.php");
$con_bdsrx = new sqlsrvModel();

if(isset($_POST["tipo"]) && $_POST["tipo"] == 'rrhh'){
    if (!isset($_POST['token'])) {
        die("No se ha proporcionado un token de acceso.");
    } else {
        // Guardamos el token de acceso en la sesión
        $_SESSION['token_azure_acceso'] = $_POST['token'];
    }

    if (!validateAccessToken($_SESSION['token_azure_acceso'])) {
        // Si el token no es válido, redirigir a la página de error
        header("Location: https://webcorporativa.surexport.es?tokenvalido=no");
        exit();
    } else {
        // Obtener información del usuario
        $_SESSION["usuario_web_azure"] = getUserData($_SESSION['token_azure_acceso']);
        $usuario = $_SESSION["usuario_web_azure"]["mail"];

        // Obtener grupos del usuario
        $_SESSION["usuario_web_azure_grupos"] = getUserGroups($_SESSION['token_azure_acceso']); 
    }


    if (isset($_SESSION["token_azure_acceso"]) && isset($_SESSION["usuario_web_azure"]) && isset($_SESSION["usuario_web_azure_grupos"])) {
        if (in_array("4237b8ee-1381-48aa-842d-f4abbb347aae", $_SESSION["usuario_web_azure_grupos"])) {

            //Recepcion del Usuario y Contraseña
            if (isset($usuario) && $usuario != "") {
                $resul = $con_bdsrx->loginUser($usuario);

                if ($resul!=false) {
                    //usuario y contraseña válidos, almaceno el acceso en la base de datos
                    $con_bdsrx->AccesoUser($resul['id'], $usuario);
                    //defino una sesion y guardo datos
                    session_start();
                    $_SESSION["idioma_surexport_appreclu"] = "es";
                    $_SESSION["autentificado_surexport_appreclu"] = "SI";
                    $_SESSION["ultimoAcceso_surexport_appreclu"] = date("Y-n-j H:i:s");
                    $_SESSION["id_user_surexport_appreclu"] = $resul['id'];
                    $_SESSION["tipo_user_surexport_appreclu"] = $resul['tipo'];
                    $_SESSION["telefono_user_surexport_appreclu"] = $resul['telf'];
                    $_SESSION["correo_user_surexport_appreclu"] = $resul['usr_login'];
                    $_SESSION["nombre_user_surexport_appreclu"] = $resul['nombre']." ".$resul['apellidos'];
                    if (!is_null($resul['portal_rrhh']) || $resul['portal_rrhh']!="") {
                        $_SESSION["permisos_surexport_appreclu"] = explode(",", $resul['portal_rrhh']);
                    }else{
                        $_SESSION["permisos_surexport_appreclu"] = array(1,2);
                    }

                    //Consultamos el menú de la aplicación
                    $menu_app = $con_bdsrx->menuPortal();
                    $_SESSION["menu_surexport_appreclu"] = $menu_app;
                    
                    // if (in_array(3, $_SESSION["permisos_surexport_appreclu"])) {

                    //     $resul2 = $con_bdsrx->total_trabajadores_sinrespuesta();
                    //     $_SESSION["trab_sinrespuesta"] = $resul2;

                    //     $resul6 = $con_bdsrx->total_aceptados_baja_total();
                    //     $_SESSION["trab_aceptados_baja"] = $resul6;

                    //     $resul5 = $con_bdsrx->trabajadores_sinllamamiento();
                    //     $_SESSION["trab_sinllama"] = $resul5;
                        
                    // }

                    // $resul3 = $con_bdsrx->cumple_trabajador();
                    // $_SESSION["cumples"] = $resul3;

                    // $resul4 = $con_bdsrx->dni_caducados();
                    // $_SESSION["dni_caducados"] = $resul4;

                    header ("Location: admin_cont.php?controller=index&action=home");
                    exit();
                } else {
                    //si no existe le mando otra vez a la portada
                    header("Location: https://webcorporativa.surexport.es/?errorusuario=si&1");
                    exit();
                }
            } else {
                //si no existe le mando otra vez a la portada
                header("Location: https://webcorporativa.surexport.es/?errorusuario=si&2");
                exit();
            }
        } else {
            //si no existe le mando otra vez a la portada
            header("Location: https://webcorporativa.surexport.es/?accesodenegado=si");
            exit();
        }
    }
}











// Función para validar el token de acceso con Microsoft Graph
function validateAccessToken($token) {
    $url = "https://graph.microsoft.com/v1.0/me";
    $response = makeGraphRequest($url, $token);
    return isset($response['id']);
}

// Función para obtener datos del usuario
function getUserData($token) {
    $url = "https://graph.microsoft.com/v1.0/me?\$select=id,displayName,givenName,surname,mail,jobTitle,department,mobilePhone,companyName,employeeId&\$expand=manager(\$select=id,displayName,jobTitle,mail)";
    return makeGraphRequest($url, $token);
}

// Función para obtener los grupos del usuario con paginación
function getUserGroups($token) {
    $grupos = [];
    $url = "https://graph.microsoft.com/v1.0/me/memberOf?\$select=id,displayName";
    
    $response = makeGraphRequest($url, $token);
    do {
        if (isset($response['value'])) {
            foreach ($response['value'] as $group) {
                $grupos[] = $group['id'];
            }
        }
        
        $url = $response['@odata.nextLink'] ?? null;
    } while ($url);
    
    return $grupos;
}

// Función genérica para realizar solicitudes a Microsoft Graph usando cURL
function makeGraphRequest($url, $token) {
    $headers = [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode !== 200) {
        return null;
    }
    
    return json_decode($response, true);
}

?>  