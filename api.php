<?php 
//conecto con la base de datos
include_once("config.php");
require_once("models/sqlsrvModel.php");
$con_bdsrx = new sqlsrvModel();
date_default_timezone_set('Europe/Madrid');

//Registramos un nuevo dispositivo
if (isset($_GET['reg_dispo']) and $_GET['token']=='6be7349363169f31e68d5d44730cc1e0') {
	$id = $con_bdsrx->RegistrarDispositivo($_POST['IDTABLET']);
	if ($id!=null) {
		$resul['respuesta'] = "true";
		$resul['id'] = $id;
	}else{
		$resul['respuesta'] = "false";
		$resul['id'] = null;
	}
	echo json_encode($resul, JSON_UNESCAPED_UNICODE);
}


//Devolvemos la lista de grupos
if (isset($_GET['grupos']) and $_GET['token']=='6be7349363169f31e68d5d44730cc1e0') {
	$grupos = $con_bdsrx->Grupos();
	$resul = array();
	$cont = 0;
	foreach ($grupos as $value) {
		$resul[$cont]['id']=$value['id'];
		$resul[$cont]['nombre']=$value['nombre'];
		$resul[$cont]['descrip']=$value['descrip'];
		$cont++;
	}
	echo json_encode($resul, JSON_UNESCAPED_UNICODE);
}

//Devolvemos la lista de usuarios
if (isset($_GET['users']) and $_GET['token']=='6be7349363169f31e68d5d44730cc1e0') {
	$usuarios = $con_bdsrx->Usuarios();
	$resul = array();
	$cont = 0;
	foreach ($usuarios as $value) {
		$resul[$cont]['id']=$value['id'];
		$resul[$cont]['nombre']=$value['nombre'];
		$resul[$cont]['apellidos']=$value['apellidos'];
		$resul[$cont]['usr_login']=$value['usr_login'];
		$resul[$cont]['usr_pass']=$value['usr_pass'];
		$resul[$cont]['tipo']=$value['tipo'];
		$cont++;
	}
	echo json_encode($resul, JSON_UNESCAPED_UNICODE);
}

//Creamos un nuevo grupo
// if (isset($_GET['new_grupo']) and $_GET['token']=='6be7349363169f31e68d5d44730cc1e0') {
// 	$resul = array();
// 	if (isset($_POST['nombre']) and isset($_POST['descrip'])) {
// 		if ($_POST['nombre']!=null and $_POST['descrip']!=null) {
// 			$resul_inser = $con_bdsrx->inserGrupo($_POST['nombre'], $_POST['descrip']);
// 			if ($resul_inser != false) {
// 				$resul['respuesta'] = "true";
// 	    		echo json_encode($resul);
// 			}else{
// 				$resul['respuesta'] = "false";
// 	    		echo json_encode($resul);
// 			}	
// 		}else{
// 			$resul['respuesta'] = "false";
// 			$resul['motivo'] = "datos_incompletos";
// 	    	echo json_encode($resul);
// 		}
// 	}else{
// 		$resul['respuesta'] = "false";
// 		$resul['motivo'] = "datos_incompletos";
//     	echo json_encode($resul);
// 	}
// }

//Creamos un nuevo trabajador
if (isset($_GET['new_trabajador']) and $_GET['token']=='6be7349363169f31e68d5d44730cc1e0') {
	$params = array();
	if (isset($_POST['TELEFONO']) and isset($_POST['VALOR_DOCUMENTO'])) {
		$file = fopen("log.txt", "a");
		$resul_inser = $con_bdsrx->inserCandidato(1);
		if ($resul_inser != false) {
			$params['respuesta'] = "true";
			$params['id'] = $resul_inser;
			echo json_encode($params, JSON_UNESCAPED_UNICODE);
			//Almacenamos el log
		    fwrite($file, date("Y-m-d H:i:s")." ---> Datos: ".$_POST['FECHA_CREACION_REGISTRO']."-->".$_POST['HORA_CREACION_REGISTRO']."-->".$_POST['SEXO']."-->".$_POST['NOMBRE']."-->".$_POST['APELLIDO1']."-->".$_POST['APELLIDO2']."-->".$_POST['TIPO_DOCUMENTO']."-->".$_POST['VALOR_DOCUMENTO']."-->".$_POST['ID_CREADOR']."-->".$_POST['NACIONALIDAD']."-->ID:".$resul_inser.PHP_EOL);
		}else{
			$params['id'] = null;
			$params['respuesta'] = "false";
			echo json_encode($params, JSON_UNESCAPED_UNICODE);
			fwrite($file, date("Y-m-d H:i:s")." ---> Intento Insercción Fallido: ".$_POST['FECHA_CREACION_REGISTRO']."-->".$_POST['HORA_CREACION_REGISTRO']."-->".$_POST['SEXO']."-->".$_POST['NOMBRE']."-->".$_POST['APELLIDO1']."-->".$_POST['APELLIDO2']."-->".$_POST['TIPO_DOCUMENTO']."-->".$_POST['VALOR_DOCUMENTO']."-->".$_POST['ID_CREADOR']."-->ID:".$_POST['ID'].PHP_EOL);
		}
	    fclose($file);
	}else{
		$params['respuesta'] = "false";
		$params['motivo'] = "datos_incompletos";
    	echo json_encode($params, JSON_UNESCAPED_UNICODE);
	}
}

//Relaciones de usuarios
if (isset($_GET['new_relacion']) and $_GET['token']=='6be7349363169f31e68d5d44730cc1e0') {
	$params = array();
	if (isset($_POST['ID_RELACION']) and isset($_POST['ID_USUARIO'])) {
		$file = fopen("log.txt", "a");
		$resul_inser = $con_bdsrx->inserRelacion($_POST['ID_RELACION'], $_POST['ID_USUARIO']);
		if ($resul_inser == true) {
			$params['respuesta'] = "true";
			echo json_encode($params, JSON_UNESCAPED_UNICODE);
			//Almacenamos el log
		    fwrite($file, date("Y-m-d H:i:s")." ---> Nueva Relacion: ".$_POST['ID_RELACION']."-->".$_POST['ID_USUARIO'].PHP_EOL);
		}else{
			$params['respuesta'] = "false";
			echo json_encode($params, JSON_UNESCAPED_UNICODE);
			fwrite($file, date("Y-m-d H:i:s")." ---> Intento Insercción Relacion Fallido: ".$_POST['ID_RELACION']."-->".$_POST['ID_USUARIO'].PHP_EOL);
		}
	    fclose($file);
	}else{
		$params['respuesta'] = "false";
		$params['motivo'] = "datos_incompletos";
    	echo json_encode($params, JSON_UNESCAPED_UNICODE);
	}
}
?> 
