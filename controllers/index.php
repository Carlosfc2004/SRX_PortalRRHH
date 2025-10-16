<?php
// En el sistema se diferencian dos tipos de usuarios el administrador y los usuarios simples, 
// ambos utilizan el mismo modelo y controlador pero carpetas de vistas diferentes, esto es simplente por motivos de seguridad
session_start();
date_default_timezone_set("Europe/Madrid");
include_once("seguridad.php");
require_once("models/sqlsrvModel.php");


class index{
	
	private $meses;

	public function __construct() {
		// Inicializar el array de meses según el idioma de la sesión
		if ($_SESSION['idioma_surexport_appreclu'] === 'es') {
			$this->meses = array(
				1 => 'Enero',
				2 => 'Febrero',
				3 => 'Marzo',
				4 => 'Abril',
				5 => 'Mayo',
				6 => 'Junio',
				7 => 'Julio',
				8 => 'Agosto',
				9 => 'Septiembre',
				10 => 'Octubre',
				11 => 'Noviembre',
				12 => 'Diciembre'
			);
		} elseif ($_SESSION['idioma_surexport_appreclu'] === 'en') {
			$this->meses = array(
				1 => 'January',
				2 => 'February',
				3 => 'March',
				4 => 'April',
				5 => 'May',
				6 => 'June',
				7 => 'July',
				8 => 'August',
				9 => 'September',
				10 => 'October',
				11 => 'November',
				12 => 'December'
			);
		} elseif ($_SESSION['idioma_surexport_appreclu'] === 'fr') {
			$this->meses = array(
				1 => 'Janvier',
				2 => 'Février',
				3 => 'Mars',
				4 => 'Avril',
				5 => 'Mai',
				6 => 'Juin',
				7 => 'Juillet',
				8 => 'Août',
				9 => 'Septembre',
				10 => 'Octobre',
				11 => 'Novembre',
				12 => 'Décembre'
			);
		} elseif ($_SESSION['idioma_surexport_appreclu'] === 'pt') {
			$this->meses = array(
				1 => 'Janeiro',
				2 => 'Fevereiro',
				3 => 'Março',
				4 => 'Abril',
				5 => 'Maio',
				6 => 'Junho',
				7 => 'Julho',
				8 => 'Agosto',
				9 => 'Setembro',
				10 => 'Outubro',
				11 => 'Novembro',
				12 => 'Dezembro'
			);
		} 
	}
	


	public function home(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		$m = new sqlsrvModel();
		// $params['total_cont'] = $m->total_contrataciones_mensuales();
		// $params['sociedades'] = $m->Sociedades_graf();
		// $params['sociedad_trab'] = $m->sociedad_trabajador();
		// $params['trabajadoresAct'] = $m->trabajadoresActivos();
		$meses = $this->meses;
		require 'views/index.php';
	}



	// Funcion con llamada a api de Altiria para comprobar los creditos disponibles SMS
	public function consultarCreditos() {
		
        $baseUrl = 'https://www.altiria.net:8443/apirest/ws';

        // Credenciales
        $credentials = array(
            'login' => 'developer@surexport.es', 
            'passwd' => 'xyvaagmy'
        );

        $jsonData = array(
            'credentials' => $credentials
        );

        $jsonDataEncoded = json_encode($jsonData);

        // Inicialización de cURL
        $ch = curl_init($baseUrl . '/getCredit');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json;charset=UTF-8'));

        // Envío de la petición y procesamiento de la respuesta
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($statusCode != 200) {
            return "Error: No se pudo conectar con el servidor. Estado HTTP: $statusCode";
        }

        $json_array = json_decode($response, true);

        // Verifica si la clave "credit" existe
        if (isset($json_array['credit'])) {
            $creditos = intval($json_array['credit']);

            if ($json_array['status'] == '000') {
                if ($creditos > 100) {
                    return "Tienes $creditos créditos disponibles.";
                } elseif ($creditos <= 100 && $creditos > 0) {
                    return "Tienes menos de 100 créditos disponibles.<br>
                    Total $creditos créditos.";
                } elseif ($creditos == 0) {
                    return "No tienes créditos disponibles. <br>
                    Total $creditos créditos.";
                }
            } else {
                return "Error al verificar los créditos. Por favor, intenta nuevamente.";
            }
        } else {
            return "Error: La respuesta no contiene créditos. Respuesta completa: " . var_export($json_array, true) . "";
        }

        curl_close($ch);
    }



	// Método para consultar el estado de la API de WhatsApp
    // public function estadoapiwhatsapp() {
	// 	include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
	// 	$apiUrl = 'https://graph.facebook.com/v20.0/357269130811588'; 
	// 	$accessToken = 'EAAQy3aeDCjsBO15NVDGCtl8tM4ZAxNN3X1FYXjYKY9WNutxgvE7mFQxi6ZBJfz6gQc5xoA4k4BrALZCYYuZCiJp6kbLHZBqFLL8wjW5yj6dnHjPLZCzukF4eZBkbcNufydptfbyWysyrgFIO2sJVcM85L5rlkVJ8cGK8bYzYZCdGEmk3ckKwcIGDxK9pcgzovf6yKAZDZD';
		
	// 	// Inicializa cURL
	// 	$ch = curl_init($apiUrl);
		
	// 	// Configura las opciones de cURL
	// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// 	curl_setopt($ch, CURLOPT_HTTPHEADER, [
	// 		'Authorization: Bearer ' . $accessToken,
	// 		'Content-Type: application/json'
	// 	]);
		
	// 	// Ejecuta la solicitud
	// 	$response = curl_exec($ch);
		
	// 	// Verifica si hubo un error en la solicitud
	// 	if (curl_errno($ch)) {
	// 		return 'Error en la solicitud: ' . curl_error($ch);
	// 	} else {
	// 		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	// 		// Procesa la respuesta
	// 		$jsonResponse = json_decode($response, true);
	
	// 		// Verifica el código de estado HTTP
	// 		if ($httpCode == 200) {
	// 			// Mensaje de éxito con detalles
	// 			$resultado = "Estado de la API de WhatsApp: Correcto ✅ <br>";
	// 			$resultado .= "Nombre verificado: " . ($jsonResponse['verified_name'] ?? 'No disponible') . "<br>";
	// 			$resultado .= "Número mostrado: " . ($jsonResponse['display_phone_number'] ?? 'No disponible') . "<br>";
	// 			//$resultado .= "Estado de verificación del código: " . ($jsonResponse['code_verification_status'] ?? 'No disponible') . "<br>";
	// 			// $resultado .= "Calidad de la cuenta: " . ($jsonResponse['quality_rating'] ?? 'No disponible') . "<br>";
	// 			return $resultado;
	// 		} else {
	// 			// Manejo de errores basado en el código de estado
	// 			$error = "Error al consultar el estado de la API. Código de estado HTTP: " . $httpCode . "<br>";
	// 			$error .= "Mensaje de error: " . ($jsonResponse['error']['message'] ?? 'No se proporcionó un mensaje de error.') . "<br>";
	
	// 			return $error;
	// 		}
	// 	}
	
	// 	// Cierra la sesión cURL
	// 	curl_close($ch);
	// }



    // Configuración WEB
    public function configuracion() {
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		$m = new sqlsrvModel();
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			if (isset($_POST['consultar'])) {
				$params['resultado'] = $this->consultarCreditos();
			} elseif (isset($_POST['rango_dias']) && $_POST['rango_dias'] == 1) {
				if ($m->añadirRangoFechas($_POST)) {
					$params['resultado'] = "Rango de fechas añadido correctamente.";
					$m->reg_acciones('Añadir rango de fechas', '', $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = "Error al añadir el rango de fechas.";
					$m->reg_acciones('Añadir rango de fechas', '', $_SESSION["id_user_surexport_appreclu"], 'ERROR');
				}
			} elseif (isset($_POST['editar_rango']) && $_POST['editar_rango'] == 1) {
				if ($m->editarRangoFechas($_POST)) {
					$params['resultado'] = "Rango de fechas editado correctamente.";
					$m->reg_acciones('Editar rango de fechas', $_POST['id_rango'], $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = "Error al editar el rango de fechas.";
					$m->reg_acciones('Editar rango de fechas', $_POST['id_rango'], $_SESSION["id_user_surexport_appreclu"], 'ERROR');
				}
			} elseif (isset($_POST['eliminar_rango']) && $_POST['eliminar_rango'] == 1) {
				if ($m->eliminarRangoFechas($_POST['id_rango'])) {
					$params['resultado'] = "Rango de fechas eliminado correctamente.";
					$m->reg_acciones('Eliminar rango de fechas', '', $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = "Error al eliminar el rango de fechas.";
					$m->reg_acciones('Eliminar rango de fechas', '', $_SESSION["id_user_surexport_appreclu"], 'ERROR');
				}
			}
		}

		if (isset($_POST['filtro']) && $_POST['filtro'] == 1) {
			$params['rango_fechas'] = $m->obtenerRangoFechas($_POST['filtro_anio']);
		} else {
			$params['rango_fechas'] = $m->obtenerRangoFechas(date('Y'));
		}

		$params['años'] = $m->obtenerAñosRangoFechas();
		$params['tipo_jornadas'] = $m->obtenerTipoJornadas();

        require 'views/configuracion.php';
    }



	public function salir(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');
		session_destroy();
		header("Location: https://webcorporativa.surexport.es?sesion_cerrada=si");
		exit();
	}



	//error 404
	public function error404(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		require 'views/error-404.php';
	}



	// Alertas
	public function reg_alertas(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		
		$m = new sqlsrvModel();
		$params['cumples'] = $m->cumple_trabajador();
		$params['dni_caducados'] = $m->dni_caducados();
		$params['trab_sinllama'] = $m->trabajadores_sinllamamiento();
		$params['trab_aceptados_baja'] = $m->total_aceptados_baja();
		
		require 'views/reg_alertas.php';
	}



	// Mostrar Trabajadores SAP
	public function trabajadores_sap(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(1, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}

		// Capturar los valores del formulario
		// los valores quitamos los espacios por delate y por atras
		
		$txt_pernr = isset($_POST['txt_pernr']) ? trim($_POST['txt_pernr']) : '';
		$txt_nombre = isset($_POST['txt_nombre']) ? trim($_POST['txt_nombre']) : '';
		$sociedad = isset($_POST['sociedad']) ? trim($_POST['sociedad']) : '';
		$baja = isset($_POST['estado_trab']) ? $_POST['estado_trab'] : '3';

		$m = new sqlsrvModel();
		if (isset($_POST['buscar'])) {
			$params['trabajadores'] = $m->buscarTrabajadoresSap($txt_pernr, $txt_nombre, $sociedad, $baja);
		}
		$params['sociedades'] = $m->Sociedades();
		require 'views/trabajadores_sap.php';
	}



	//Datos Trabajadores SAP
	public function update_trabajador(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(1, $_SESSION["permisos_surexport_appreclu"])) {
		header("Location: admin_cont.php?controller=index&action=error404");
		}
		$m = new sqlsrvModel();
		$params['info_trabajador'] = $m->info_trabajador($_GET['id']);
		$params['datos_contacto'] = $m->datos_contacto_trabajador($_GET['id']);
		$params['prefijos'] = $m->datos_prefijos();
		$params['parentesco'] = $m->datos_parentesco();
		$params['datos_direccion'] = $m->datos_direccion_trabajador($_GET['id']);
		$params['datos_medidas'] = $m->datos_medidas($_GET['id']);
		$params['datos_contrato'] = $m->datos_contrato_trabajador($_GET['id']);
		$params['datos_contrato2'] = $m->datos_contrato2_trabajador($_GET['id']);
		$params['datos_ausencia'] = $m->datos_ausencia_trabajador($_GET['id']);
		$params['datos_ropo'] = $m->datos_ropo_trabajador($_GET['id']);
		$params['datos_asig'] = $m->datos_asignacion_trabajador($_GET['id']);
		$params['datos_nfc'] = $m->datos_nfc_trabajador($_GET['id']);
		$params['datos_llamamiento'] = $m->llamamientos_trabajador($_GET['id']);
		$params['alertas_trabajador'] = $m->alertas_trabajador($_GET['id']);
		$params['fecha_val_dni'] = $m->fecha_val_dni($_GET['id']);
		$params['motivos_pendiente'] =  $m->motivos_pendiente();

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST["act_cont"]) && $_POST["act_cont"] == 'Actualizar') {
				$pernr = isset($_GET['id']) ? $_GET['id'] : '';
				$movil = isset($_POST['TELEFONO']) ? $_POST['TELEFONO'] : '';
				$correo = isset($_POST['MAIL']) ? strtoupper($_POST['MAIL']) : '';
				$telempresa = isset($_POST['TELEMPRESA']) ? $_POST['TELEMPRESA'] : '';
				$telemergencias = isset($_POST['TELEMERGENCIAS']) ? $_POST['TELEMERGENCIAS'] : '';

				$pre_telf = isset($_POST['PRE_TELF']) ? $_POST['PRE_TELF'] : '';
				$pre_telf_emp = isset($_POST['PRE_TELF_EMP']) ? $_POST['PRE_TELF_EMP'] : '';
				$pre_telf_emer = isset($_POST['PRE_TELF_EMER']) ? $_POST['PRE_TELF_EMER'] : '';

				$parent_telf = isset($_POST['PARENT_TELF']) ? $_POST['PARENT_TELF'] : '';
				$parent_telf_emp = isset($_POST['PARENT_TELF_EMP']) ? $_POST['PARENT_TELF_EMP'] : '';
				$parent_telf_emer = isset($_POST['PARENT_TELF_EMER']) ? $_POST['PARENT_TELF_EMER'] : '';

				$datos = array();
				
				// CURL API mulesoft
				if ($movil != '') {
					$data = array(
						"pernr" => $pernr,
						"tipo" => '9002',
						"valor" => $movil,
					);
					$datos[] = $data;
				}

				if ($telempresa != '') {
					$data = array(
						"pernr" => $pernr,
						"tipo" => '9003',
						"valor" => $telempresa,
					);
					$datos[] = $data;
				}

				if ($telemergencias != '') {
					$data = array(
						"pernr" => $pernr,
						"tipo" => '9004',
						"valor" => $telemergencias,
					);
					$datos[] = $data;
				}

				if ($correo != '') {
					$data = array(
						"pernr" => $pernr,
						"tipo" => '9010',
						"valor" => $correo,
					);
					$datos[] = $data;
				}
				
			
				$path = '/comunicacion';
				$metod = 'PATCH';
				$resultado = $m->curl_api_mulesoft($datos, $metod, $path);

				$mensajes = [];

				if ($resultado['success']) {
			
					// Actualización en Maestro 105
					
					if ($resultadoMaestro = $m->actualizar_contacto($pernr, $movil, $correo, $telempresa, $telemergencias, $pre_telf, $pre_telf_emp, $pre_telf_emer, $parent_telf, $parent_telf_emp, $parent_telf_emer)) {
						$mensajes[] = "✅ Maestro: Actualizado correctamente";

						// REGISTRAR ACCIÓN si todo fue ok
						$m->reg_acciones('Actualizar contacto', $pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
					} else {
						$mensajes[] = "❌ Maestro: Error al actualizar";

						// REGISTRAR ACCIÓN en caso de error en Maestro
						$m->reg_acciones('Actualizar contacto', $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error en Maestro');
					}
			
					// Resultado de SAP
					$mensajes[] = "SAP: " . $resultado['message'];
				} else {
					$mensajes[] = "Error en SAP: " . $resultado['message'];

					// REGISTRAR ACCIÓN en caso de error en SAP
					$m->reg_acciones('Actualizar contacto', $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error en SAP');
				}
			
				$params['resultado'] = implode('<br>', $mensajes);


			// REGISTRAR LLAMAMIENTO
			} elseif (isset($_POST["Tipo_llamamiento"]) && $_POST["Tipo_llamamiento"]== "Telefono") {
				
				$pernr = $_POST['pernr'];
				$tipo_llamamiento = $_POST['Tipo_llamamiento'];
				$pre_contacto = $_POST['prefijo'];
				$fecha_llamamiento = $_POST['fecha_llamamiento'];
				$fecha_registro = date('Y-m-d H:i:s'); 
				$info_contacto = $_POST['contacto'];
				$estado = $_POST['estado'];
				$motivo = $_POST['motivo'];
				$id_usuario = $_SESSION["id_user_surexport_appreclu"];
				$descipcion = $_POST['descripcion'];

				$url_just = null;
				$url_just_bbdd = null;

				if (!empty($_FILES["justificante"]["name"])) {

					$url_base = "/var/www/files/justificantes/" . $pernr;

					if (!file_exists($url_base)) {
						if (!mkdir($url_base, 0777, true)) {
							$error = error_get_last();
							echo "❌ No se pudo crear la carpeta destino: $url_base<br>";
							echo "🔧 Error: " . $error['message'];
							exit;
						}
					}

					$fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", basename($_FILES["justificante"]["name"]));
					$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

					$allowTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
					$maxSize = 5 * 1024 * 1024; // 5MB

					if (!in_array($fileType, $allowTypes)) {
						echo "Formato de archivo no permitido.";
						die;
					}

					if ($_FILES["justificante"]["size"] > $maxSize) {
						echo "El archivo excede el tamaño permitido (5MB).";
						die;
					}

					if ($_FILES["justificante"]["error"] === 0) {
						$newFileName = date("dmY") . "_" . uniqid() . "." . $fileType;
						$url_just = $url_base . "/" . $newFileName;
						$url_just_bbdd = "/justificantes/" . $pernr . "/" . $newFileName;

						if (!is_dir($url_base)) {
							echo "❌ La carpeta destino no existe: $url_base";
							exit;
						}
						if (!is_writable($url_base)) {
							echo "❌ La carpeta destino no es escribible: $url_base";
							exit;
						}

						if (move_uploaded_file($_FILES["justificante"]["tmp_name"], $url_just)) {
							// Archivo subido correctamente
							// Aquí podrías guardar $url_just_bbdd en la base de datos
						} else {

							echo "❌ Error al mover el archivo a: " . $url_just . "<br>";
							echo "🧪 Verifica permisos de escritura del contenedor sobre el volumen montado.<br>";
							exit;
						}
					} else {
						echo "❌ Error en la subida del archivo. Código de error: " . $_FILES["justificante"]["error"];
						exit;
					}
				}

				// Verificación de las variables
				$id_remesa = isset($_POST['id_remesa']) && $_POST['id_remesa'] !== 'undefined' && !empty($_POST['id_remesa']) ? $_POST['id_remesa'] : null;
				$ano_remesa = isset($_POST['ano_remesa']) && $_POST['ano_remesa'] !== 'undefined' && !empty($_POST['ano_remesa']) ? $_POST['ano_remesa'] : null;

				if ($m->reg_llamada($pernr, $tipo_llamamiento, $pre_contacto, $fecha_llamamiento, $info_contacto, $estado, $motivo, $descipcion, $url_just_bbdd, $id_usuario, $id_remesa, $ano_remesa, $fecha_registro)) {
					$params['resultado'] = $lang['index3'];

					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Llamamiento telefono', $pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = $lang['index4'];

					// REGISTRAR ACCIÓN en caso de error al insertar registro
					$m->reg_acciones('Llamamiento telefono', $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error');
				}	

			} elseif (isset($_POST["Tipo_llamamiento"]) && $_POST["Tipo_llamamiento"] == "Correo") { 
				$pernr = $_POST['pernr'];
				$nombre = $_POST['nombre'];
				$tipo_llamamiento2 = $_POST['Tipo_llamamiento'];
				$fecha_registro2 = date('Y-m-d H:i:s'); 
				$info_contacto = $_POST['contacto'];
				$estado = "0";
				$id_usuario = $_SESSION["id_user_surexport_appreclu"];
				// Verificación de las variables
				$id_remesa = isset($_POST['id_remesa']) && $_POST['id_remesa'] !== 'undefined' && !empty($_POST['id_remesa']) ? $_POST['id_remesa'] : null;
				$ano_remesa = isset($_POST['ano_remesa']) && $_POST['ano_remesa'] !== 'undefined' && !empty($_POST['ano_remesa']) ? $_POST['ano_remesa'] : null;

				$mail_usu_web = $_POST['correo_usu_web'];
				$mensaje_usu_web = $_POST['mensaje_mail'];

				if ($m->send_mail($pernr, $nombre, $tipo_llamamiento2, $fecha_registro2, $info_contacto, $estado, $id_usuario, $id_remesa, $ano_remesa, $mail_usu_web, $mensaje_usu_web)) {
					$params['resultado'] = $lang['index3']; // Éxito
					$m->reg_acciones('Llamamiento correo', $pernr, $id_usuario, 'OK');
				} else {
					$params['resultado'] = $lang['index4']; // Error
					$m->reg_acciones('Llamamiento correo', $pernr, $id_usuario, 'Error');
				}

			// ACTUALIZAR RESPUESTA Y MOTIVO DEL TRABAJADOR PARA EL LLAMAMIENTO
			} elseif (isset($_POST["Tipo_respuesta"]) && $_POST["Tipo_respuesta"] == "rechazar") {
				$id_registro = $_POST['id_registro']; 
				$estado = "2"; 
				$fecha = date('Y-m-d H:i:s'); 
				$pernr = $_GET['id'];
				$motivo = $_POST['motivo']; 
				$id_remesa = isset($_POST['id_remesa']) && $_POST['id_remesa'] !== 'undefined' && !empty($_POST['id_remesa']) ? $_POST['id_remesa'] : null;
				$ano_remesa = isset($_POST['ano_remesa']) && $_POST['ano_remesa'] !== 'undefined' && !empty($_POST['ano_remesa']) ? $_POST['ano_remesa'] : null;
				$descipcion = '';

				$url_just = null;
				$url_just_bbdd = null;

				if (!empty($_FILES["justificante"]["name"])) {

					$url_base = "/var/www/files/justificantes/" . $pernr;

					if (!file_exists($url_base)) {
						if (!mkdir($url_base, 0777, true)) {
							$error = error_get_last();
							echo "❌ No se pudo crear la carpeta destino: $url_base<br>";
							echo "🔧 Error: " . $error['message'];
							exit;
						}
					}

					$fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", basename($_FILES["justificante"]["name"]));
					$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

					$allowTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
					$maxSize = 5 * 1024 * 1024; // 5MB

					if (!in_array($fileType, $allowTypes)) {
						echo "Formato de archivo no permitido.";
						die;
					}

					if ($_FILES["justificante"]["size"] > $maxSize) {
						echo "El archivo excede el tamaño permitido (5MB).";
						die;
					}

					if ($_FILES["justificante"]["error"] === 0) {
						$newFileName = date("dmY") . "_" . uniqid() . "." . $fileType;
						$url_just = $url_base . "/" . $newFileName;
						$url_just_bbdd = "/justificantes/" . $pernr . "/" . $newFileName;

						if (!is_dir($url_base)) {
							echo "❌ La carpeta destino no existe: $url_base";
							exit;
						}
						if (!is_writable($url_base)) {
							echo "❌ La carpeta destino no es escribible: $url_base";
							exit;
						}

						if (move_uploaded_file($_FILES["justificante"]["tmp_name"], $url_just)) {
							// Archivo subido correctamente
							// Aquí podrías guardar $url_just_bbdd en la base de datos
						} else {

							echo "❌ Error al mover el archivo a: " . $url_just . "<br>";
							echo "🧪 Verifica permisos de escritura del contenedor sobre el volumen montado.<br>";
							exit;
						}
					} else {
						echo "❌ Error en la subida del archivo. Código de error: " . $_FILES["justificante"]["error"];
						exit;
					}
				}


				if ($m->update_estado_llama($id_registro, $estado, $fecha, $motivo, $descipcion, $id_remesa, $ano_remesa, $url_just_bbdd)) {
					$params['resultado'] = $lang['index5'];

					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Rechazar llamamiento', $pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = $lang['index8'];

					// REGISTRAR ACCIÓN en caso de error
					$m->reg_acciones('Rechazar llamamiento', $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error');
				}	

			} elseif (isset($_POST["Tipo_respuesta"]) && $_POST["Tipo_respuesta"] == "pendiente"){
				$id_registro = $_POST['id_registro'];
				$estado = "3";
				$pernr = $_GET['id'];
				$fecha = date('Y-m-d H:i:s');
				$motivo = $_POST['motivo'];
				$id_remesa = isset($_POST['id_remesa']) && $_POST['id_remesa'] !== 'undefined' && !empty($_POST['id_remesa']) ? $_POST['id_remesa'] : null;
				$ano_remesa = isset($_POST['ano_remesa']) && $_POST['ano_remesa'] !== 'undefined' && !empty($_POST['ano_remesa']) ? $_POST['ano_remesa'] : null;
				$descipcion = $_POST['descripcion'];

				$url_just = null;
				$url_just_bbdd = null;

				if (!empty($_FILES["justificante"]["name"])) {

					$url_base = "/var/www/files/justificantes/" . $pernr;

					if (!file_exists($url_base)) {
						if (!mkdir($url_base, 0777, true)) {
							$error = error_get_last();
							echo "❌ No se pudo crear la carpeta destino: $url_base<br>";
							echo "🔧 Error: " . $error['message'];
							exit;
						}
					}

					$fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", basename($_FILES["justificante"]["name"]));
					$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

					$allowTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
					$maxSize = 5 * 1024 * 1024; // 5MB

					if (!in_array($fileType, $allowTypes)) {
						echo "Formato de archivo no permitido.";
						die;
					}

					if ($_FILES["justificante"]["size"] > $maxSize) {
						echo "El archivo excede el tamaño permitido (5MB).";
						die;
					}

					if ($_FILES["justificante"]["error"] === 0) {
						$newFileName = date("dmY") . "_" . uniqid() . "." . $fileType;
						$url_just = $url_base . "/" . $newFileName;
						$url_just_bbdd = "/justificantes/" . $pernr . "/" . $newFileName;

						if (!is_dir($url_base)) {
							echo "❌ La carpeta destino no existe: $url_base";
							exit;
						}
						if (!is_writable($url_base)) {
							echo "❌ La carpeta destino no es escribible: $url_base";
							exit;
						}

						if (move_uploaded_file($_FILES["justificante"]["tmp_name"], $url_just)) {
							// Archivo subido correctamente
							// Aquí podrías guardar $url_just_bbdd en la base de datos
						} else {

							echo "❌ Error al mover el archivo a: " . $url_just . "<br>";
							echo "🧪 Verifica permisos de escritura del contenedor sobre el volumen montado.<br>";
							exit;
						}
					} else {
						echo "❌ Error en la subida del archivo. Código de error: " . $_FILES["justificante"]["error"];
						exit;
					}
				}

				if ($m->update_estado_llama($id_registro, $estado, $fecha, $motivo, $descipcion, $url_just_bbdd, $id_remesa, $ano_remesa)) {
					$params['resultado'] = $lang['index6'];

					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Pendiente llamamiento', $pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = $lang['index8'];

					// REGISTRAR ACCIÓN en caso de error
					$m->reg_acciones('Pendiente llamamiento', $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error');
				}

			} elseif (isset($_POST['Tipo_respuesta']) && $_POST['Tipo_respuesta'] == "Aceptado") {
				$id_registro = $_POST['id_registro']; 
				$estado = "1";
				$pernr = $_GET['id'];
				$fecha = date('Y-m-d H:i:s'); 
				$id_remesa = $_POST['id_remesa'];  
				$ano_remesa = $_POST['ano_remesa'];  

								$url_just = null;
				$url_just_bbdd = null;

				if (!empty($_FILES["justificante"]["name"])) {

					$url_base = "/var/www/files/justificantes/" . $pernr;

					if (!file_exists($url_base)) {
						if (!mkdir($url_base, 0777, true)) {
							$error = error_get_last();
							echo "❌ No se pudo crear la carpeta destino: $url_base<br>";
							echo "🔧 Error: " . $error['message'];
							exit;
						}
					}

					$fileName = preg_replace("/[^a-zA-Z0-9\.\-_]/", "_", basename($_FILES["justificante"]["name"]));
					$fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

					$allowTypes = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
					$maxSize = 5 * 1024 * 1024; // 5MB

					if (!in_array($fileType, $allowTypes)) {
						echo "Formato de archivo no permitido.";
						die;
					}

					if ($_FILES["justificante"]["size"] > $maxSize) {
						echo "El archivo excede el tamaño permitido (5MB).";
						die;
					}

					if ($_FILES["justificante"]["error"] === 0) {
						$newFileName = date("dmY") . "_" . uniqid() . "." . $fileType;
						$url_just = $url_base . "/" . $newFileName;
						$url_just_bbdd = "/justificantes/" . $pernr . "/" . $newFileName;

						if (!is_dir($url_base)) {
							echo "❌ La carpeta destino no existe: $url_base";
							exit;
						}
						if (!is_writable($url_base)) {
							echo "❌ La carpeta destino no es escribible: $url_base";
							exit;
						}

						if (move_uploaded_file($_FILES["justificante"]["tmp_name"], $url_just)) {
							// Archivo subido correctamente
							// Aquí podrías guardar $url_just_bbdd en la base de datos
						} else {

							echo "❌ Error al mover el archivo a: " . $url_just . "<br>";
							echo "🧪 Verifica permisos de escritura del contenedor sobre el volumen montado.<br>";
							exit;
						}
					} else {
						echo "❌ Error en la subida del archivo. Código de error: " . $_FILES["justificante"]["error"];
						exit;
					}
				}

				if ($m->update_estado_llama_aceptar($id_registro, $estado, $fecha, $url_just_bbdd, $id_remesa, $ano_remesa)) {
					$params['resultado'] = $lang['index7'];

					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Aceptar llamamiento', $pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = $lang['index8'];

					// REGISTRAR ACCIÓN en caso de error
					$m->reg_acciones('Aceptar llamamiento', $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error');
				}    

			} elseif (isset($_POST['nfc'])) {
				// CURL API mulesoft

				$data = array(
					"pernr" => $_GET['id'],
					"nfc" => $_POST['nfc'],
					"fecha" => $_POST['fecha_nfc']
				);

				// var_dump($data);
				// die;
				
				$pernr = $_GET['id'];
				$path = '/update_nfc';
				$metod = 'PATCH';
				$resultado = $m->curl_api_mulesoft($data, $metod, $path);
				
				$mensajes = [];
				
				if ($resultado['success']) {
					$actualizacionExitosa = true; // Variable para verificar el estado global

					// Actualización en Agromobile
					$resultadoAgro = $m->update_nfc_agro($_GET['id'], $_POST['nfc']);
								
					// Actualización en Maestro
					$resultadoMaestro = $m->update_nfc_maestro($_GET['id'], $_POST['nfc']);

					// Actualización en Mantenimiento
					$resultadoMante = $m->update_nfc_mantenimiento($_GET['id'], $_POST['nfc']);

					//Actualización en último registo de PA0001 ----- David Pinilla
					$resultadoPA0001 = $m->update_nfc_pa0001($_GET['id'], $_POST['nfc']);




					$correcto = 0;
					$error = 0;

					// AGROMOBILE
					if ($resultadoAgro['success']) {
						// $mensajes[] = "✅ Agromobile: " . $resultadoAgro['message'];
						// if ($resultadoAgro['details']['nfc_duplicados_limpiados'] > 0) {
						// 	$mensajes[] = "ℹ️ Se han limpiado " . $resultadoAgro['details']['nfc_duplicados_limpiados'] . " NFCs duplicados en Agromobile";
						// }
						$correcto++;
					} else {
						// $mensajes[] = "❌ Agromobile: " . $resultadoAgro['message'];
						$error++;
					}

					// MAESTRO
					if ($resultadoMaestro['success']) {
						// $mensajes[] = "✅ Maestro: " . $resultadoMaestro['message'];
						// if ($resultadoMaestro['details']['nfcs_desasignados'] > 0) {
						// 	$mensajes[] = "ℹ️ Se han desasignado " . $resultadoMaestro['details']['nfcs_desasignados'] . " NFCs anteriores del trabajador";
						// }
						// if ($resultadoMaestro['details']['operacion'] === 'insert') {
						// 	$mensajes[] = "ℹ️ Se ha creado un nuevo registro en el maestro de NFC";
						// }
						$correcto++;
					} else {
						// $mensajes[] = "❌ Maestro: " . $resultadoMaestro['message'];
						$error++;
					}

					// MANTENIMIENTO
					if ($resultadoMante['success']) {
						// $mensajes[] = "✅ Mantenimiento: " . $resultadoMante['message'];
						// if ($resultadoMante['details']['nfcs_desasignados'] > 0) {
						// 	$mensajes[] = "ℹ️ Se han desasignado " . $resultadoMante['details']['nfcs_desasignados'] . " NFCs anteriores del trabajador";
						// }
						// if ($resultadoMante['details']['operacion'] === 'update') {
						// 	$mensajes[] = "ℹ️ Se ha creado un nuevo registro en el maestro de NFC";
						// }
						$correcto++;
					} else {
						// $mensajes[] = "❌ Mantenimiento: " . $resultadoMante['message'];
						$error++;
					} 

					// PA0001
					if ($resultadoPA0001['success']) {
						// $mensajes[] = "✅ Asignación Organizativa: " . $resultadoPA0001['message'];
						// if ($resultadoPA0001['details']['nfc_duplicados_limpiados'] > 0) {
						// 	$mensajes[] = "ℹ️ Se han limpiado " . $resultadoPA0001['details']['nfc_duplicados_limpiados'] . " NFCs duplicados en Agromobile";
						// }
						$correcto++;
					} else {
						// $mensajes[] = "❌ Asignación Organizativa: " . $resultadoPA0001['message'];
						$error++;
					}



					if ($correcto < 4 || $correcto > 0) {
						$mensajes[] = $resultado['message'];
					}
			
					// Resultado de SAP

					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Actualizar NFC', $pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$mensajes[] = "Error en SAP: " . $resultado['message'];

					// REGISTRAR ACCIÓN en caso de error en SAP
					$m->reg_acciones('Actualizar NFC', $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error en SAP');
				}
			
				$params['resultado'] = implode('<br>', $mensajes);
			
			} elseif (isset($_POST['alerta'])) {
				$pernr = $_GET['id'];
				$tipo_alerta = $_POST['alertType'];
				$descripcion = $_POST['description'];
				$fecha_ini = $_POST['startDate'];
				$fecha_fin = $_POST['endDate'];
				
				
				// Para campos de texto opcionales
				$tipo_formacion = !empty($_POST['trainingType']) ? $_POST['trainingType'] : NULL;
				$obligatorio = !empty($_POST['mandatory']) ? $_POST['mandatory'] : NULL;
				$tipo_incidente = !empty($_POST['incidentType']) ? $_POST['incidentType'] : NULL;
				
				// Campos obligatorios
				$prioridad = $_POST['priority'];
				$notificado = $_POST['notifyTo'];
				$frecuencia = $_POST['frequency'];
			
				if ($m->nueva_alerta($pernr, $tipo_alerta, $descripcion, $fecha_ini, $fecha_fin, 
									$tipo_formacion, $obligatorio, $tipo_incidente, $prioridad, 
									$notificado, $frecuencia)) {
					$params['resultado'] = 'Alerta insertada';

					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Alerta '.$tipo_alerta , $pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = 'Error al insertar la alerta';

					// REGISTRAR ACCIÓN en caso de error
					$m->reg_acciones('Alerta'.$tipo_alerta , $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error en SAP');
				}
			} elseif (isset($_POST['validez'])) {
				$fecha_validez = $_POST['validez'];
				$pernr = $_GET['id'];
				$tipo_doc = $_POST['tipo_doc2'];

				if ($m->update_fecha_val_dni($pernr, $tipo_doc, $fecha_validez)) {
					$params['resultado'] = 'Fecha validez actualizada del trabajador '.$pernr;
					
					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Actualizar Validez', $pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = 'Error al actualizar o añadir la fecha de validez';

					// REGISTRAR ACCIÓN en caso de error
					$m->reg_acciones('Actualizar Validez', $pernr, $_SESSION["id_user_surexport_appreclu"], 'Error');
				}
				
			}
		}
		require 'views/update_trabajador.php';
	}



	// Solicitudes
	public function solicitudes(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(28, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		$params['otras_aus'] = $m->getotrasausencias(NULL);
		$params['tipo_ausencias'] = $m->tipos_ausencias();
		$params['trabajadores_solicitudes'] = $m->trabajadores_solicitudes();
		// $params['solicitudes_contestadas'] = $m->solicitudes_contestadas();

		// Definir variables de filtro con valores predeterminados
		$fecha_solic = '';
		$fecha_solic2 = '';
		$pernr = '';
		$tipo_ausencia = '';
		$estado = '1'; // Filtrar siempre pendientes por defecto
		$justificante = '';

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['firma_rrhh'])) {
				$id_solicitud = $_POST['id_sol'];
				$fecha_res_rrhh = $_POST['fecha_res_rrhh'];
				$firma_rrhh = $_POST['firma_rrhh'];
				$id_rrhh = $_SESSION['id_user_surexport_appreclu'];
				$pernr = $_POST['pernr_usu'];
				$mail_s = $_POST['mail_s'];
				$nombre = $_POST['nombre'];
				$nombre_s = $_POST['nombre_s'];
				$mail = $_POST['mail'];
				$fecha_sol = $_POST['fecha_sol'];
				
			
				// Verificar si se ha presionado "aceptar" o "rechazar" y el estado actual para determinar la acción
				if (isset($_POST['aceptar'])) {
					if (isset($_POST['estado']) && $_POST['estado'] == '7') {
						$estado = '5'; 
					} else {
						$estado = '3'; 
					}
				} elseif (isset($_POST['rechazar'])) {
					if (isset($_POST['estado']) && $_POST['estado'] == '7') {
						$estado = '8'; 
					} else {
						$estado = '4'; 
					}
				}
			
				// Llamar a la función para actualizar la solicitud
				if ($m->actualizarSolicitud($id_solicitud, $fecha_res_rrhh, $firma_rrhh, $fecha_sol, $estado, $id_rrhh, $mail_s, $nombre, $nombre_s, $mail)) {
					if ($estado == '3') {
						$params['resultado'] = 'Solicitud aceptada correctamente.';
						$m->reg_acciones('Solicitud aprobada', $pernr." - ".$id_solicitud, $_SESSION["id_user_surexport_appreclu"], 'OK');
					} elseif ($estado == '4') {
						$params['resultado'] = 'Solicitud rechazada correctamente.';
						$m->reg_acciones('Solicitud rechazada', $pernr." - ".$id_solicitud, $_SESSION["id_user_surexport_appreclu"], 'OK');
					} elseif ($estado == '5') {
						$params['resultado'] = 'Solicitud de anulacion aceptada correctamente.';
						$m->reg_acciones('Anulacion aceptada', $pernr." - ".$id_solicitud, $_SESSION["id_user_surexport_appreclu"], 'OK');
					} elseif ($estado == '8') {
						$params['resultado'] = 'Solicitud de anulacion rechazada correctamente.';
						$m->reg_acciones('Anulacion rechazada', $pernr." - ".$id_solicitud, $_SESSION["id_user_surexport_appreclu"], 'OK');
					}
				} else {
					$params['resultado'] = 'Ha ocurrido un error al actualizar la solicitud.';
					$m->reg_acciones('Actualizar solicitud', $pernr." - ".$id_solicitud, $_SESSION["id_user_surexport_appreclu"], 'Error');
				}
			} elseif (isset($_POST['observacion'])) {

				$id_solicitud = $_POST['id_sol'];
				$pernr_obs = $_POST['pernr_mod'];
				$fecha_crea = $_POST['fecha_crea'];
				$pernr_usu = $_POST['pernr_usu'];
				$observacion = $_POST['comentario'];


				if ($m->agregarObservacion($id_solicitud, $pernr_obs, $fecha_crea, $pernr_usu, $observacion)) {
					$params['resultado'] = 'Observación añadida correctamente.';
					$m->reg_acciones('Añadir observación', $pernr_usu." - ".$id_solicitud, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = 'Error al añadir la observación.';
					$m->reg_acciones('Añadir observación', $pernr_usu." - ".$id_solicitud, $_SESSION["id_user_surexport_appreclu"], 'Error');
				}

			} elseif (isset($_POST['filtros_sol'])) {
					$fecha_solic = $_POST['fecha_inicio'] ?? '';
					$fecha_solic2 = $_POST['fecha_fin'] ?? '';
					$pernr = $_POST['pernr_nom_sol'] ?? '';
					$tipo_ausencia = $_POST['tipo_ausencia'] ?? '';
					$estado = $_POST['estado'] ?? ''; // Si no se envía, que siga filtrando por pendientes
					$justificante = $_POST['justificante'] ?? '';
			}
		}

		$params['solicitudes_pendientes'] = $m->solicitudes($fecha_solic, $fecha_solic2, $pernr, $tipo_ausencia, $estado, $justificante);

		require 'views/solicitudes.php';
	}




	// LLAMAMIENTOS

	// Trabajadores baja

	public function trabajadores_baja(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(4, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		$params['fincas_almacenes'] = $m->fincas_almacenes_sociedad();

		if (isset($_POST['buscar'])) {
			$ubi = $_POST['ubi_trab'] ? $_POST['ubi_trab'] : '';
			$fecha_ini = $_POST['fecha_ini'] ? $_POST['fecha_ini'] : '';
			$fecha_fin = $_POST['fecha_fin'] ? $_POST['fecha_fin'] : '';
			$params['datos_trab_baja'] = $m->trabajadores_baja($ubi, $fecha_ini, $fecha_fin);
		} else {
			$ubi = '';
			$params['datos_trab_baja'] = $m->trabajadores_baja($ubi, '', '');
		}
		
		require 'views/trabajadores_baja.php';
	}


	
	// Llamamientos y generacion de remesa

	public function llamamientos(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(5, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		// Cargamos los datos de fincas y almacenes para la vista
		$params['fincas_almacenes'] = $m->fincas_almacenes_sociedad();
		require 'views/llamamientos.php';
	}



	// Actualización del controlador

	public function generar_rem_llama(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		
		if (!in_array(5, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}   
		$m = new sqlsrvModel();
		$resultado = ""; // Inicializamos la variable para el resultado

		
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['generar_rem'])) {
				// Generamos una nueva remesa
				$sms_auto = isset($_POST['sms_auto']) ? 1 : 0;
				
				// Llamada a la función nuevaRemesa() que ahora devuelve un array con 'success' y 'message'
				$resultado = $m->nuevaRemesa($_POST['nombre_remesa'], $_POST['telefono_rem'], $_POST['fecha_inc'], $sms_auto);
				
				// Verificamos si la operación fue exitosa y registramos la acción
				if ($resultado['success']) {
					
					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Generar remesa', $_POST['nombre_remesa'], $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					
					// REGISTRAR ACCIÓN en caso de error
					$m->reg_acciones('Generar remesa', $_POST['nombre_remesa'], $_SESSION["id_user_surexport_appreclu"], 'Error');
				}
				
			} elseif (isset($_POST['add_candidato'])) {
				// Añadimos nuevos candidatos a una remesa ya existente
				$resultado = $m->anadirCandidatosARemesa(
					$_POST['id_remesa'], 
					$_POST['ano_remesa'], 
					$_POST['nombre_remesa'] ?? '', 
					$_POST['telefono_rem'], 
					$_POST['fecha_inc'] ?? '', 
					$_POST['sms_auto'] ?? ''
				);
				
				// Verificamos si la operación fue exitosa y registramos la acción
				if ($resultado['success']) {
					
					// REGISTRAR ACCIÓN todo ok
					$m->reg_acciones('Añadir trab remesa', $_POST['id_remesa'].'/'.$_POST['ano_remesa'], $_SESSION['id_user_surexport_appreclu'], 'OK');
				} else {
					
					// REGISTRAR ACCIÓN en caso de error
					$m->reg_acciones('Añadir trab remesa', $_POST['id_remesa'].'/'.$_POST['ano_remesa'], $_SESSION['id_user_surexport_appreclu'], 'Error');
				}
			}
		}

		// Cargamos los datos de trabajadores dados de baja para la vista
		// $params['datos_trab_baja'] = $m->trabajadores_baja_rem();
		
		// Asignamos el resultado para mostrarlo en la vista
		$params['resultado'] = $resultado['message'] ?? ""; 
	
		// Requiere la vista 'llamamientos.php' para mostrar los resultados
		require 'views/llamamientos.php';
	}



	// Registros de llamamiento

	public function registros_llama(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(7, $_SESSION["permisos_surexport_appreclu"])) {
		header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();

		$txt_pernr = isset($_POST['txt_pernr']) ? $_POST['txt_pernr'] : '';
		$txt_nombre = isset($_POST['txt_nombre']) ? $_POST['txt_nombre'] : '';
		$estado = isset($_POST['estado']) ? $_POST['estado'] : '';
		$tipo_llama = isset($_POST['tipo_llama']) ? $_POST['tipo_llama'] : '';
		$desde = isset($_POST['desde']) ? $_POST['desde'] : '';
		$hasta = isset($_POST['hasta']) ? $_POST['hasta'] : '';
		$filtros = isset($_GET['filtro']) ? $_GET['filtro'] : '';

		$params['llamamientos'] = $m->registros_llamamiento($txt_pernr, $txt_nombre, $estado, $tipo_llama, $desde, $hasta, $filtros);

		require 'views/registros_llama.php';
	}



	// Exportar
	public function exportar(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
			if (!in_array(2, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		$params['trabajadores_auditoria'] = $m->trabajadoresAuditoria();
		$params['trabajadores_almacen'] = $m->trabajadoresAlmacen();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['form_campo'])) {
				if ($_POST['sociedad'] != '' and isset($_POST['fincas'])){
					$fincas = $m->fincas_agromobile($_POST['sociedad']);
					$operarios2 = isset($_POST['operarios']) ? $_POST['operarios'] : null;

					$fecha_inicio = $_POST['fecha_inicio'];
					if ($_POST['fecha_fin'] == '') {
						$fecha_fin = $_POST['fecha_inicio'];
					} else {
						$fecha_fin = $_POST['fecha_fin'];
					}
					
					$params['datos_export'] = $m->informePresencia($_POST['fincas'], $fecha_inicio, $fecha_fin, $_POST['sociedad'], $_POST['division'], $operarios2);
					$operarios = $m->operarios_centro($_POST['sociedad'],$_POST['division']);
				}else{
					$params['resultado'] = $lang['index9'];
				}
			} 
			
			elseif (isset($_POST['form_oficina'])) {
				if (isset($_POST['ubicacion'])) {
					$ubicacion = $_POST['ubicacion'];
				} else {
					$ubicacion = '';
				}

				if (isset($_POST['tipo_reg'])) {
					$tipo = $_POST['tipo_reg'];
				} else {
					$tipo = '';
				}

				if (isset($_POST['pernr_trab'])) {
					$pernr = implode(',', $_POST['pernr_trab']);
				} else {
					$pernr = [];
				}

				if (isset($_POST['sede'])) {
					$sede= $_POST['sede'];
				} else {
					$sede = '';
				}
				$params['datos_export_ofi'] = $m->informePresenciaOficina($_POST['fecha_inicio_ofi'], $_POST['fecha_fin_ofi'], $tipo, $pernr , isset($_POST['reg_manual']), $sede, $ubicacion);
			}

			elseif (isset($_POST['form_almacen'])) {
				if (isset($_POST['pernr_trab_alm'])) {
					$pernr = implode('|', $_POST['pernr_trab_alm']);
				} else {
					$pernr = [];
				}
				$params['datos_export_alm'] = $m->informePresenciaAlmacen($_POST['fecha_inicio_alm'], $_POST['fecha_fin_alm'], $pernr, $_POST['puertaTesa']);
			}
		}
		$params['sociedad'] = $m->Sociedades();
		$params['sedes'] = $m->sedes();
		$params['puertas'] = $m->puertas_tesa();
		require 'views/exportar.php';
	}



	// Registro de las remesas creadas

	public function rem_llama(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
			if (!in_array(6, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		$params['remesas'] = $m->Remesas_llama();
		require 'views/rem_llama.php';
	}



	//Histórico de remesas con sus trabajadores

	public function view_remesa_llama(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(6, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}
		$m = new sqlsrvModel();
		$params['info_remesas'] = $m->InfoRemesa_llama($_GET['id'], $_GET['ano']);

		if (isset($_GET['delete_trab_rem']) && $_GET['delete_trab_rem'] == 1) {
			$pernr = $_POST['pernr'];
			$id_remesa = $_POST['id_remesa'];
			$ano_remesa = $_POST['ano_remesa'];

			if ($m->EliminarTrabajadorRemesa($pernr, $id_remesa, $ano_remesa)) {
				$params['resultado'] = 'Trabajador eliminado de la remesa correctamente';
				$m->reg_acciones('Eliminar trabajador remesa', $pernr." - ".$id_remesa.'/'.$ano_remesa, $_SESSION["id_user_surexport_appreclu"], 'OK');
			} else {
				$params['resultado'] = 'El trabajador tiene un llamamiento para esta remesa, no se puede eliminar';
				$m->reg_acciones('Eliminar trabajador remesa', $pernr." - ".$id_remesa.'/'.$ano_remesa, $_SESSION["id_user_surexport_appreclu"], 'Error');
			}
		}

		require 'views/update_remesa_llama.php';
	}




	// AUDITOR

	public function auditor(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(25, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}
		$m = new sqlsrvModel();
		$params['trabajadores_auditoria'] = $m->trabajadoresAuditoria();
		
			if (isset($_POST['form_oficina'])) {
				$params['datos_export_ofi'] = $m->informePresenciaOficina2($_POST);		
			
			} elseif (isset($_GET['modificar'])) {
				$fecha = $_POST['fecha_mod'];
				$id = $_POST['id'];
				$estado = 3;
				$pernr = $_POST['pernr'];
				$motivo = isset($_POST['comentario']) ? $_POST['comentario'] : NULL;

				// var_dump($_POST);
				// die;

				$params['datos_export_ofi'] = $m->informePresenciaOficina2($_POST);

				// Convertir fecha al formato compatible con SQL Server
				$fecha = str_replace('T', ' ', $fecha);

				if ($m->validar_registro($id, $fecha, $estado, $motivo, true)) {
					$params['resultado'] = 'Registro modificado correctamente';
					$m->reg_acciones('Modificar jornada presencia por RRHH', $id." ".$pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = 'Error al modificar el registro';
					$m->reg_acciones('Modificar jornada presencia por RRHH', $id." ".$pernr, $_SESSION["id_user_surexport_appreclu"], 'ERROR');
				}

			} elseif (isset($_POST['fecha_valida'])) {
				$fecha = $_POST['fecha_valida'];
				$id = $_POST['id'];
				$estado = $_POST['estado'];
				$pernr = $_POST['pernr'];
				$motivo = isset($_POST['motivo']) ? $_POST['motivo'] : NULL;

				// Convertir fecha al formato compatible con SQL Server
				$fecha = str_replace('T', ' ', $fecha);

				if ($m->validar_registro($id, $fecha, $estado, $motivo)) {
					$params['resultado'] = 'Registro validado correctamente';
					$m->reg_acciones('Validar jornada presencia', $id." ".$pernr, $_SESSION["id_user_surexport_appreclu"], 'OK');
				} else {
					$params['resultado'] = 'Error al validar el registro';
					$m->reg_acciones('Validar jornada presencia', $id." ".$pernr, $_SESSION["id_user_surexport_appreclu"], 'ERROR');
				}
			} elseif (isset($_GET['guardar_nuevos_registros'])) {
				$ok = true;
				
				// Decodificar los datos JSON que llegan como string
				$registros = json_decode($_POST['registros'], true);
				
				if (!$registros) {
					$params['resultado'] = 'Error al procesar los datos';
					http_response_code(400);
				} else {
					foreach($registros as $registro) {
						if($m->guardar_nuevo_registro($registro)) {
							$m->reg_acciones('Registro creado por RRHH', $registro['pernr'], $_SESSION["id_user_surexport_appreclu"], 'OK');
						} else {
							$m->reg_acciones('Registro creado por RRHH', $registro['pernr'], $_SESSION["id_user_surexport_appreclu"], 'ERROR');
							$ok = false;
						}
					}
					
					if($ok) {
						$params['resultado'] = 'Registros guardados correctamente';
						http_response_code(200);
					} else {
						$params['resultado'] = 'Error al guardar los registros';
						http_response_code(500);
					}
				}
			}
		require 'views/auditor.php';
	}
	



	// PRESENCIA
	public function presencia(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(25, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}
	
		$m = new sqlsrvModel();
		
		// Determinar si los valores vienen de POST o GET
		$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : (isset($_GET['tipo']) ? $_GET['tipo'] : 'presencia');
		$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : (isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d'));
	
		if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'GET') {
			// Llamar al modelo con los valores correspondientes
			if (in_array($tipo, ['1A', '1E', '9A'])) {
				$params['trabajadores_presencia'] = $m->trabajadores_conta($fecha_inicio, $tipo, $_POST['filtroAsistencia'] ?? null, $_POST['buscador'] ?? null);
			}
		}
	
		require 'views/presencia.php';
	}




	// RECLUTAMIENTO

	//Mostramos todos los candidatos

	public function candidatos(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(9, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		unset($_SESSION['array_candidatos']);
		//Comprobamos si ha querido eliminar algún candidato
		if (isset($_GET['elim']) and $_GET['elim']!="") {
			$params['resultado'] = $m->elimCandidato($_GET['elim']);
		}
		$params['candidatos'] = $m->buscarCandidatos();
		$params['grupos'] = $m->Grupos();
		require 'views/candidatos.php';
	}



	//Actualizamos un candidato

	public function update_candidato(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(9, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['ID']!="") {
				if ($m->updateCandidato()) {
					$params['resultado'] = $lang['index10'];
				}else{
					$params['resultado'] = $lang['index11'];
				}
			}
		}
		$params['paises'] = $m->Paises();
		$params['nacionalidad'] = $m->Nacionalidad();
		$params['info_candidato'] = $m->infoCandidato($_GET['id']);
		require 'views/update_candidato.php';
	}



	//Insertamos un candidato

	public function new_candidato(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(10, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['nombre']!="") {
				if ($m->inserCandidato(0)) {
					$params['resultado'] = $lang['index12'];
					
				}else{
					$params['resultado'] = $lang['index13'];
				}
			}
		}
		$params['paises'] = $m->Paises();
		$params['nacionalidad'] = $m->Nacionalidad();
		require 'views/nuevo_candidato.php';
	}



	//Mostramos todos los grupos
	public function grupos(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(11, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		if (isset($_GET['elim'])) {
			$params['resultado'] = $m->eliminarGrupo($_GET['elim']);
		}
		$params['grupos'] = $m->Grupos();
		require 'views/grupos.php';
	}



	//Insertamos un nuevo grupo
	public function new_grupo(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(12, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		$params['nombre'] = "";
		$params['descrip'] = "";
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$params['nombre'] = $_POST['nombre'];
			$params['descrip'] = $_POST['descrip'];
			if ($_POST['nombre']!="") {
				if ($m->inserGrupo($params['nombre'], $params['descrip'])) {
					$params['resultado'] = $lang['index14'];
				}else{
					$params['resultado'] = $lang['index15'];
				}
				$params['grupos'] = $m->Grupos();
				require 'views/grupos.php';
			}else{
				$params['resultado'] = $lang['index16'];
				require 'views/nuevo_grupo.php';
			}
		}else{
			require 'views/nuevo_grupo.php';
		}
	}



	//Actualizamos un grupo
	public function update_grupo(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(12, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if ($_POST['nombre']!="") {
				if ($m->updateGrupo($_POST['id'], $_POST['nombre'], $_POST['descrip'])) {
					$params['resultado'] = $lang['index17'];
				}else{
					$params['resultado'] = $lang['index18'];
				}
			}
		}
		$params['info_grupo'] = $m->infoGrupo($_GET['id']);
		require 'views/update_grupo.php';
	}



	//Histórico de remesas
	public function remesas(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(13, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		unset($_SESSION['array_candidatos']);
		$m = new sqlsrvModel();
		$params['remesas'] =$m->Remesas();
		require 'views/remesas.php';
	}



	//Mostrar información de una remesa
	public function view_remesa(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(13, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		unset($_SESSION['array_candidatos']);
		$m = new sqlsrvModel();
		if (isset($_GET['id']) and isset($_GET['ano'])) {
			//Si hemos pulsado sobre el botón de eliminar el candidato de la remesa, hay que indicar si eliminar o rechazar
			if (isset($_GET['elim_candidato']) and $_GET['elim_candidato']!="") {
				$params['resultado'] = '
					<h3>'.$lang['index19'].'</h3>
					<br>
					<form action="admin_cont.php?controller=index&action=view_remesa&id='.$_GET['id'].'&ano='.$_GET['ano'].'" method="post">
						<input type="hidden" name="id_can" value="'.$_GET['elim_candidato'].'">
						<input type="text" name="motivo" class="form-login" placeholder="'.$lang['index20'].'" required>
						<br>
						<input type="submit" class="submit" name="eliminar_can_rem" value="'.$lang['index21'].'">
						<input type="submit" class="submit" name="rechazar_can_rem" value="'.$lang['rechazar'].'">
					</form>';
			}
			//Actualizamos el estado despues de contestar
			if ($_SERVER['REQUEST_METHOD'] == 'POST') {
				if ($_POST['id_can']!="") {
					if (isset($_POST['eliminar_can_rem'])) {
						// Hemos seleccionado eliminar candidato
						$params['resultado'] = $m->elimCandidatoRemesa($_GET['id'], $_GET['ano'], $_POST['id_can']);
					}elseif (isset($_POST['rechazar_can_rem'])) {
						$params['resultado'] = $m->RechazarCandidatoRemesa($_GET['id'], $_GET['ano'], $_POST['id_can'], $_POST['motivo']);
					}
				}
			}
			$params['user_remesa'] =$m->InfoRemesa($_GET['id'], $_GET['ano']);
			require 'views/remesas.php';
		}else{
			$params['remesas'] =$m->Remesas();
			require 'views/remesas.php';
		}
	}



	//Generar remesas
	public function generar_remesa(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(14, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		unset($_SESSION['array_candidatos']);
		$m = new sqlsrvModel();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['generar_rem'])) {
				//Generamos una nueva remesa
				$m->newRemesa();
			}elseif (isset($_POST['add_candidato'])) {
				//Añadimos un nuevo candidato a una remesa ya existente
				$m->addRemesa();
			}
		}
		$params['user_rel'] = $m->usuariosRelaciones();
		require 'views/generar_remesa.php';
	}




	// DISPOSITIVOS
	// Mostrar lista de dispositivos
	public function dispositivos(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
			if (!in_array(17, $_SESSION["permisos_surexport_appreclu"])) {
				header("Location: admin_cont.php?controller=index&action=error404");
			}	
		$m = new sqlsrvModel();
		$params['dispositivos'] = $m->dispositivos();
		if (isset($_GET['elim']) and $_GET['elim']!="") {
			$id = $_GET['elim'];

			// Eliminar dispositivo
			if ($m->eliminarDispositivo($id)) {
				$params['resultado'] = 'Dispositivo eliminado correctamente';
			} else {
				$params['resultado'] = 'Error al eliminar dispositivo';
			}
		}
		require 'views/dispositivos.php';
	}



	// Informacion dispositivos y actualizar estado
	public function update_dispositivo() {
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		
		// Verificación de permisos
		if (!in_array(17, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
			exit(); // Añadir exit después del redirect
		}    
		
		$m = new sqlsrvModel();
		$params = array();
		
		// Validar el ID
		$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
		if (!$id) {
			$params['resultado'] = "ID de dispositivo inválido";
			require 'views/update_dispositivo.php';
			return;
		}
		
		// Procesar POST
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['activo_dispositivo'])) {
				$estado = $_POST['activo_dispositivo'];
				$nombre = $_POST['nombre_dispositivo'];
				if($m->updateDispositivo($id, $nombre, $estado)) {
					$params['resultado'] = "Dispositivo Actualizado";
				} else {
					$params['resultado'] = "Error al actualizar dispositivo";
				}
			}
		}
		
		// Obtener información actual del dispositivo
		$params['info_dispositivo'] = $m->infoDispositivo($id);
		
		require 'views/update_dispositivo.php';
	}

	
	
	// Nuevo dispositivo
	public function new_dispositivo(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
			if (!in_array(18, $_SESSION["permisos_surexport_appreclu"])) {
				header("Location: admin_cont.php?controller=index&action=error404");
			}	
		$m = new sqlsrvModel();
		$params['sedes'] = $m->sedes();
		
		//Insertamos el dispositivo configurado
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['nuevo_dispositivo'])) {

				$id = $_POST['id_dispo'];
				$nombre = $_POST['nombre_dispo'];
				$ubicacion = $_POST['ubicacion'];

				if ($m->añadir_dispositivo($id, $nombre, $ubicacion)) {
					$params['resultado'] = $lang['index31'];
				} else {
					$params['resultado'] = $lang['index32'];
				}
			}
		}

		require 'views/nuevo_dispositivo.php';
	}



	// Nueva ubicación
	public function new_ubicacion(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
			if (!in_array(19, $_SESSION["permisos_surexport_appreclu"])) {
				header("Location: admin_cont.php?controller=index&action=error404");
			}	
		$m = new sqlsrvModel();

		$params['ubicaciones'] = $m->ubicaciones();

		//Insertamos nueva ubicación
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['nueva_ubicacion'])) {

				// echo "<pre>";
				// print_r($_POST);
				// echo "</pre>";

				$nombre = $_POST['sede'];
				$ubicacion = $_POST['ubicacion'];
			

				if ($m->añadir_ubicacion($nombre, $ubicacion)) {
					$params['resultado'] = $lang['index33'];
				} else {
					$params['resultado'] = $lang['index34'];
				}
			}
		}

		require 'views/nuevo_ubicacion.php';
	}




	// TESA
	// Mostrar Usuarios de TESA
	public function tesa_usuarios(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
			if (!in_array(21, $_SESSION["permisos_surexport_appreclu"])) {
				header("Location: admin_cont.php?controller=index&action=error404");
			}	
		
			if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newusu_tesa'])) {
				// Recoger variables del formulario de nuevo usuario para insertarlos en TESA
			}
		require 'views/tesa_usuarios.php';
	}



	// Estado puertas
	public function estado_puertas(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
			if (!in_array(24, $_SESSION["permisos_surexport_appreclu"])) {
				header("Location: admin_cont.php?controller=index&action=error404");
			}	
		
		require 'views/estado_puertas.php';
	}



	// Estado puertas
	public function tesa_update_usu(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
			if (!in_array(21, $_SESSION["permisos_surexport_appreclu"])) {
				header("Location: admin_cont.php?controller=index&action=error404");
			}	
			
		$externalId = isset($_GET['id']) ? $_GET['id'] : null;
		$m = new sqlsrvModel();
		$params['info_usu'] = $m->tesa_info_usu($_GET['id']);

		if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['actualizado'])) {
			// Recoger variables del formulario de actualizacion de usuario para actualizarlo en TESA
		}

		require 'views/tesa_update_usu.php';
	}




	// USUARIOS
	//Mostramos todos los usuarios
	public function usuarios(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(15, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		if (isset($_GET['elim'])) {
			$params['resultado'] = $m->eliminarUser($_GET['elim']);
		}
		$params['usuarios'] = $m->Usuarios();
		require 'views/usuarios.php';
	}



	//Insertamos un nuevo usuario
	public function new_usuario(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(15, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();
		$params['nombre'] = "";
		$params['apellidos'] = "";
		$params['usr_login'] = "";
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$params['nombre'] = $_POST['nombre'];
			$params['apellidos'] = $_POST['apellidos'];
			$params['usr_login'] = $_POST['usr_login'];
			$params['usr_pass'] = $_POST['usr_pass'];
			$params['usr_pass_rep'] = $_POST['usr_pass_rep'];
			$params['tipo_usuario'] = $_POST['tipo_usuario'];
			//Comprobamos que todos los datos están rellenos
			if ($_POST['nombre']!="" and $_POST['apellidos']!="" and $_POST['usr_login']!="" and $_POST['tipo_usuario']!="" and $params['usr_pass']!="" and $params['usr_pass_rep']!="" and $_POST['permisos']!="" and $_POST['telefono']!="") {
				//Comprobamos que las dos contraseñas introducidas coinciden para evitar errores en la insercción e insertamos el usuario
				if ($params['usr_pass']==$params['usr_pass_rep']) {
					$params['resultado'] = $m->inserUsuario($params['nombre'], $params['apellidos'], $params['usr_login'], $params['usr_pass'], $params['tipo_usuario'], $_POST['permisos'], $_POST['telefono']);
					$params['usuarios'] = $m->Usuarios();
					require 'views/usuarios.php';
				}else{
					$params['resultado'] = $lang['index22'];
					require 'views/nuevo_usu.php';
				}	
			}else{
				$params['resultado'] = $lang['index23'];
				require 'views/nuevo_usu.php';
			}
		}else{
			require 'views/nuevo_usu.php';
		}
		
	}



	//actualizamos el usuario
	public function update_usu(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		if (!in_array(15, $_SESSION["permisos_surexport_appreclu"])) {
			header("Location: admin_cont.php?controller=index&action=error404");
		}	
		$m = new sqlsrvModel();


		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			//Comprobamos si lo que queremos es actualizar, generar contraseña o activar un usuario que ha sido eliminado
			if (isset($_POST['datos'])) {
				if ($_POST['nombre']!="" and $_POST['apellidos']!="" and $_POST['tipo_usuario']!="" and $_POST['id_usu']!="" and $_POST['departamento']!="" and $_POST['telefono']!="") {
					$params['resultado'] = $m->updateUsuario($_POST['id_usu'], $_POST['nombre'], $_POST['apellidos'], $_POST['tipo_usuario'], $_POST['departamento'], $_POST['telefono']);
				} else {
					$params['resultado'] = "Error al modificar los datos del usuario";
				}
			} elseif (isset($_POST['permisos'])) {
				if ($m->updateUsuarioPermisos($_POST['id_usu'], $_POST['permisos'])) {
					$params['resultado'] = "Permisos actualizados correctamente";
				} else {
					$params['resultado'] = "Error al actualizar los permisos del usuario";
				}
			}
		}

		if(isset($_GET['renew_pass'])){
			$params['resultado'] = $m->renewPass($_GET['id']);
		}

		$params['info_user'] = $m->infoUsuario($_GET['id']);
		$params['permisos'] = explode(',', $params['info_user']['permisos']);
		require 'views/update_usu.php';
	}



	//Editar perfil del usuario propio desde su web
	public function miperfil(){
		include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
		$m = new sqlsrvModel();
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['departamento'])) {
				$telefono = $_POST['telefono'];
				$departamento = $_POST['departamento'];
				$id = $_POST['id_usu'];

				if ($m->UpdateUsuarioPerfil($id, $telefono, $departamento)) {
					$params['resultado'] = 'Datos actualizados correctamente';
				} else {
					$params['resultado'] = 'Error al actualizar los datos. Intentelo de nuevo mas tarde.';
				}
			}
		}
		
		$params['datos_usu'] = $m->datos_usu($_SESSION["id_user_surexport_appreclu"]);

		require 'views/miperfil.php';
	}

}
?>