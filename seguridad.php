<?php
//Inicio la sesion


//COMPRUEBA QUE EL USUARIO ESTA AUTENTIFICADO
if ($_SESSION["autentificado_surexport_appreclu"] != "SI") {
    //si no existe, envio a la pagina de autentificacion
    header("Location: index.php");
    //ademas salgo de este script
    exit();
} else {
	//sino, calculamos el tiempo transcurrido
    $fechaGuardada = $_SESSION["ultimoAcceso_surexport_appreclu"];
    $ahora = date("Y-n-j H:i:s");
    $tiempo_transcurrido = (strtotime($ahora)-strtotime($fechaGuardada));

    //comparamos el tiempo transcurrido
     if($tiempo_transcurrido >= 2700) {
      //si pasaron 45 minutos o mas
      header('Cache-Control: no-cache, no-store, must-revalidate');
      header('Pragma: no-cache');
      header('Expires: 0');
      session_destroy(); // destruyo la sesion
      header("Location: index.php"); //envio al usuario a la pag. de autenticacion
      //sino, actualizo la fecha de la sesion
    }else {
    $_SESSION["ultimoAcceso_surexport_appreclu"] = $ahora;
   }
}
?>