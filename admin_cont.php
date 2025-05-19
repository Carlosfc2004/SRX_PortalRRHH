<?php 
// Carga de la configuración BB.DD
require_once 'config.php';

//Comprobamos si existe el controlador, si no existe cargamos el de por defecto
if ((isset($_GET['controller']))&&(file_exists("controllers/".$_GET['controller'].".php"))) {
	$ctl=$_GET['controller'];
}else{
	$ctl="index";
}

//Cargamos el controlador
require_once "controllers/".$ctl.".php";
$controller = new $ctl();

//Comprobamos si se realiza alguna acción
if ((isset($_GET['action']))&&(method_exists($ctl, $_GET['action']))) {
	$action=$_GET['action'];
}else{
	$action='home';
}

//Realizamos la acción
$controller->$action();
?>