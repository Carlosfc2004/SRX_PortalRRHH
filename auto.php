<?php

session_start();
include_once("config.php");
require_once("models/sqlsrvModel.php");
include_once("idiomas/" . $_SESSION['idioma_surexport_appreclu'] . ".php");
require_once __DIR__ . '/core/SAPMuleSoftConnector.php';
$m = new sqlsrvModel();

//Cambiamos el idioma
if (isset($_GET['idioma'])) {
	$_SESSION["idioma_surexport_appreclu"] = $_GET['idioma'];
}

// AJAX: Obtener detalles de una solicitud
if (isset($_GET['obtener_detalle_solicitud']) && !empty($_GET['id_solicitud'])) {
	header('Content-Type: application/json; charset=utf-8');

	$id_solicitud = $_GET['id_solicitud'];
	$detalle = $m->solicitud_detalle($id_solicitud);

	if (isset($detalle['error'])) {
		echo json_encode(array(
			'success' => false,
			'error' => $detalle['error']
		));
	} else {
		// Formatear fechas para JSON
		if (isset($detalle['fecha_desde']) && $detalle['fecha_desde'] instanceof DateTime) {
			$detalle['fecha_desde_formatted'] = $detalle['fecha_desde']->format('d-m-Y');
		}
		if (isset($detalle['fecha_hasta']) && $detalle['fecha_hasta'] instanceof DateTime) {
			$detalle['fecha_hasta_formatted'] = $detalle['fecha_hasta']->format('d-m-Y');
		}
		if (isset($detalle['fecha_solicitud']) && $detalle['fecha_solicitud'] instanceof DateTime) {
			$detalle['fecha_solicitud_formatted'] = $detalle['fecha_solicitud']->format('d-m-Y');
		}
		if (isset($detalle['hora_desde']) && $detalle['hora_desde'] instanceof DateTime) {
			$detalle['hora_desde_formatted'] = $detalle['hora_desde']->format('H:i');
		}
		if (isset($detalle['hora_hasta']) && $detalle['hora_hasta'] instanceof DateTime) {
			$detalle['hora_hasta_formatted'] = $detalle['hora_hasta']->format('H:i');
		}

		// Formatear fechas en observaciones
		if (isset($detalle['observaciones'])) {
			foreach ($detalle['observaciones'] as &$obs) {
				if (isset($obs['fecha_modificacion']) && $obs['fecha_modificacion'] instanceof DateTime) {
					$obs['fecha_modificacion_formatted'] = $obs['fecha_modificacion']->format('d-m-Y H:i:s');
				}
			}
		}

		echo json_encode(array(
			'success' => true,
			'data' => $detalle
		));
	}
	exit;
}

// AJAX: Obtener días disponibles por año para un trabajador
if (isset($_GET['obtener_dias_disponibles_por_anio']) && !empty($_GET['pernr']) && !empty($_GET['anio'])) {
	header('Content-Type: application/json; charset=utf-8');

	$pernr = $_GET['pernr'];
	$anio = $_GET['anio'];

	try {
		// Obtener días disponibles de vacaciones
		$dias_vacaciones = $m->getDiasDisponiblesVacaciones($pernr, $anio);
		$dias_vacaciones = $dias_vacaciones ?: ['dias_gastados' => 0, 'dias_totales' => 0, 'dias_liquidacion_redondeados' => 0];

		// Obtener días disponibles de festivo local
		$dias_festivo_local = $m->getDiasDisponiblesAusencias($pernr, '3', 'anual', $anio);
		$dias_festivo_local = $dias_festivo_local ?: ['gastados' => 0, 'restantes' => 0];

		// Obtener días disponibles de asuntos propios
		$dias_asuntos_propios = $m->getDiasDisponiblesAusencias($pernr, '4', 'anual', $anio);
		$dias_asuntos_propios = $dias_asuntos_propios ?: ['gastados' => 0, 'restantes' => 0];

		echo json_encode(array(
			'success' => true,
			'data' => array(
				'vacaciones' => array(
					'dias_gastados' => $dias_vacaciones['dias_gastados'] ?? 0,
					'dias_totales' => $dias_vacaciones['dias_totales'] ?? 0,
					'dias_liquidacion_redondeados' => $dias_vacaciones['dias_liquidacion_redondeados'] ?? 0,
					'dias_disponibles' => ($dias_vacaciones['dias_totales'] ?? 0) - ($dias_vacaciones['dias_gastados'] ?? 0)
				),
				'festivo_local' => array(
					'gastados' => $dias_festivo_local['gastados'] ?? 0,
					'restantes' => $dias_festivo_local['restantes'] ?? 0
				),
				'asuntos_propios' => array(
					'gastados' => $dias_asuntos_propios['gastados'] ?? 0,
					'restantes' => $dias_asuntos_propios['restantes'] ?? 0
				)
			)
		));
	} catch (Exception $e) {
		echo json_encode(array(
			'success' => false,
			'error' => 'Error al obtener los días disponibles: ' . $e->getMessage()
		));
	}
	exit;
}
?>

<?php

//Cargamos las fincas en exportar datos
if (isset($_GET['load_fincas_soc']) and $_GET['load_fincas_soc'] != '') {
	$division = $_GET['division'];
	$fincas = $m->fincas_agromobile($_GET['load_fincas_soc'], $_GET['division']);
	?>
	<p style="font-weight: bold;"><?php echo $lang['auto1']; ?></p>
	<input type="checkbox" id="option-all" class="form-check-input" onchange="checkAll(this)">
	<label for="option-all"><?php echo $lang['auto2']; ?></label>
	<br><br>
	<div class="row">
		<?php
		foreach ($fincas as $result) {
			echo '<div class="col-md-3 mb-2">
						<input type="checkbox" name="fincas[]" value="' . $result['ZZCODFI'] . '" class="form-check-input"> ' . $result['DESFI'] . '
					</div>';
		}
		echo "</div>";

		?>
		<script>
			function checkAll(mainCheckbox) {
				var checkboxes = document.querySelectorAll("input[type='checkbox'][name='fincas[]']");
				checkboxes.forEach(function (checkbox) {
					checkbox.checked = mainCheckbox.checked;
				});
			}
		</script>
		<?php
}

?>



	<?php

	// Cargamos los operarios de un centro
	if (isset($_GET['load_operarios']) && $_GET['load_operarios'] != '') {
		$centro = $_GET['load_operarios'];
		$division = $_GET['division'];
		$operarios = $m->operarios_centro($centro, $division);
		?>
		<div class="col-md-12">
			<span style="font-weight: bold; width: 100%;"><?php echo $lang['auto3']; ?></span>
		</div>
		<br>
		<div class="col-md-4">
			<select style="width: 100%;" class="form-control" name="operarios[]" id="operarios" multiple>
				<?php
				foreach ($operarios as $result) {
					// Generar la opción
					if (!empty($result['APELLIDO1']) && !empty($result['NOMBRE'])) {
						// Si existen APELLIDO1 y NOMBRE, mostrar en formato: APELLIDO1 APELLIDO2, NOMBRE
						$nombre = $result['APELLIDO1'];

						if (!empty($result['APELLIDO2'])) {
							$nombre .= ' ' . $result['APELLIDO2'];
						}

						$nombre .= ', ' . $result['NOMBRE'];
					} elseif (!empty($result['NOMBREYAPELLIDOS'])) {
						// Si existe el campo NOMBREYAPELLIDOS completo
						$nombre = $result['NOMBREYAPELLIDOS'];
					}

					echo '<option value="' . $result['PERNR'] . '">' . $result['PERNR'] . ' - ' . $nombre . '</option>';
				}
				?>
			</select>
		</div>

		<script>
			$(document).ready(function () {
				$('#operarios').select2({
					placeholder: '<?php echo $lang['auto7']; ?>',
					closeOnSelect: false,
					templateResult: formatState,
					templateSelection: formatState,
					minimumInputLength: 3,
					language: {
						inputTooShort: function () {
							return '<?php echo $lang['auto4']; ?>';
						},
						noResults: function () {
							return '<?php echo $lang['auto5']; ?>';
						},
						searching: function () {
							return '<?php echo $lang['auto6']; ?>';
						}
					},
				});

				function formatState(state) {
					if (state.id) {
						return state.text;
					}
				}


				// Manejar el envío del formulario
				$('#form_export').on('submit', function (event) {
					event.preventDefault(); // Evitar el envío por defecto

					// Obtener las selecciones de Select2
					var usuariosSeleccionados = $('#operarios').val();

					// Convertir el array de usuarios a una cadena separada por comas
					var usuariosString = usuariosSeleccionados.join(',');

					// Enviar el formulario
					this.submit();
				});
			});
		</script>

		<?php
	}

	?>



	<?php

	// Verificar si la solicitud para cargar las ubicaciones se ha realizado
	if (isset($_GET['load_ubicaciones']) && $_GET['load_ubicaciones'] != '') {
		$sede = $_GET['load_ubicaciones'];

		// Obtener las ubicaciones según la sede
		$ubicaciones = $m->obtener_ubicaciones_por_sede($sede);

		// Generar las opciones del select de ubicaciones
		echo "<option value=''>--</option>";
		if (!empty($ubicaciones)) {
			foreach ($ubicaciones as $ubicacion) {
				echo '<option value="' . $ubicacion['id'] . '">' . $ubicacion['nombre'] . '</option>';
			}
		} else {
			echo '<option value="">No hay ubicaciones disponibles</option>';
		}
	}

	?>



	<?php

	//Mostramos los datos para generar remesa
	if (isset($_GET['datosGenerarRemesas'])) {
		$datosRemesa = $m->usuariosRelaciones();
		header('Content-Type: application/json');

		$data = array();
		foreach ($datosRemesa as $resultado) {
			$row = array(
				'id_usuario' => $resultado['id_usuario'],
				'estado' => $resultado['estado'],
				'id_relacion' => $resultado['id_relacion'],
				'nombre_com' => $resultado['nombre_com'],
				'id_remesa' => $resultado['id_remesa'],
				'ano_remesa' => $resultado['ano_remesa'],
				'valor_doc' => $resultado['valor_doc']
			);
			array_push($data, $row);
		}
		echo json_encode($data);
	}


	//Mostramos los datos para generar remesa
	if (isset($_GET['datosLlamamiento'])) {

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if (isset($_POST['id_remesa']) && isset($_POST['ano_remesa']) && isset($_POST['ubi_trab'])) {
				$datosRemesa = $m->trabajadores_baja_rem($_POST['id_remesa'], $_POST['ano_remesa'], $_POST['ubi_trab'], $_POST['fecha_ini'] ?? null, $_POST['fecha_fin'] ?? null, $_POST['codigos_formateados'] ?? null, $_POST['relacion_laboral'] ?? null);
			} elseif (isset($_POST['id_remesa']) && isset($_POST['ano_remesa'])) {
				$datosRemesa = $m->trabajadores_baja_rem($_POST['id_remesa'], $_POST['ano_remesa'], null, null, null, $_POST['codigos_formateados'] ?? null, $_POST['relacion_laboral'] ?? null);
			}
			// else {
			// 	$datosRemesa = $m->trabajadores_baja();
			// }
		}
		// else {
		// 	$datosRemesa = $m->trabajadores_baja();
		// }
	

		// $datosRemesa = $m->trabajadores_baja();
		header('Content-Type: application/json');

		$data = array();
		foreach ($datosRemesa as $resultado) {
			// Verifica si el valor de MOVIL empieza con "99999"
			$movil = $resultado['MOVIL'];
			if (strpos($movil, '99999') === 0) {
				$movil = ''; // Si empieza con "99999", establece el valor como vacío --> strpos = start position
			}

			if (!empty($resultado['APELLIDO1']) && !empty($resultado['NOMBRE'])) {
				// Si existen APELLIDO1 y NOMBRE, mostrar en formato: APELLIDO1 APELLIDO2, NOMBRE
				$nombre = $resultado['APELLIDO1'];

				if (!empty($resultado['APELLIDO2'])) {
					$nombre .= ' ' . $resultado['APELLIDO2'];
				}

				$nombre .= ', ' . $resultado['NOMBRE'];
			} elseif (!empty($resultado['NOMBREYAPELLIDOS'])) {
				// Si existe el campo NOMBREYAPELLIDOS completo
				$nombre = $resultado['NOMBREYAPELLIDOS'];
			}

			$row = array(
				'PERNR' => $resultado['PERNR'],
				'NOMBRE' => $nombre,
				'ZZLGORT' => $resultado['ZZLGORT'],
				'BEGDA' => $resultado['BEGDA']->format('Y-m-d'),
				'MOVIL' => $movil,
				'PREFIJO' => $resultado['PRE_TELF'],
				'CORREO' => $resultado['CORREO'],
				'ID_REMESA' => $resultado['id_remesa'],
				'ANO_REMESA' => $resultado['ano_remesa'],
				'NOMBRE_REMESA' => $resultado['nombre_remesa'],
				'FECHA_ULT_LLAMA' => $resultado['FECHA_REGISTRO'],
				'RELACION_LABORAL' => $resultado['RELACION_LABORAL'],
				'DESC_RELACION_LABORAL' => $resultado['DESC_RELACION_LABORAL']
			);
			array_push($data, $row);
		}
		echo json_encode($data);
	}

	?>



	<?php

	//Cargamos los datos para los informacion de trabajadores con registro de presencia
	if (isset($_GET['trab_1A'])) {
		ob_clean();
		header('Content-Type: application/json');
		$fecha = $_POST['fecha_inicio'] ?? date('Y-m-d');
		$tipo = $_POST['tipo'] ?? '1A';

		// Obtener los trabajadores con presencia
		$trabajadores_presencia = $m->trabajadores_presencia($fecha, $tipo);

		// Obtener el total de trabajadores de tipo 1A
		$total_trabajadores = $m->trabajadores_1A($fecha);

		// Devolver JSON
		echo json_encode([
			'total_presencia' => $trabajadores_presencia,
			'total_trabajadores' => $total_trabajadores
		]);
		exit;
	}


	if (isset($_GET['trab_1E'])) {
		ob_clean();
		header('Content-Type: application/json');
		$fecha = $_POST['fecha_inicio'] ?? date('Y-m-d');
		$tipo = $_POST['tipo'] ?? '1E';

		$trabajadores_presencia = $m->trabajadores_presencia($fecha, $tipo);
		$total_trabajadores = $m->trabajadores_1E($fecha);

		// Devolver JSON
		echo json_encode([
			'total_presencia' => $trabajadores_presencia,
			'total_trabajadores' => $total_trabajadores
		]);
		exit;
	}


	if (isset($_GET['trab_9A'])) {
		ob_clean();
		header('Content-Type: application/json');

		$fecha = $_POST['fecha_inicio'] ?? date('Y-m-d');
		$tipo = $_POST['tipo'] ?? '9A';

		$trabajadores_presencia = $m->trabajadores_presencia($fecha, $tipo);
		$total_trabajadores = $m->trabajadores_9A($fecha);

		echo json_encode([
			'total_presencia' => $trabajadores_presencia,
			'total_trabajadores' => $total_trabajadores
		]);
		exit;
	}


	// if (isset($_GET['trab_1D'])) {
	// 	ob_clean();
	// 	header('Content-Type: application/json');
	
	// 	try {
	// 		$fecha = $_POST['fecha_inicio'] ?? date('Y-m-d');
	// 		$tipo = $_POST['tipo'] ?? '1D';
	
	// 		$trabajadores_presencia = $m->trabajadores_presencia($fecha, $tipo);
	// 		$total_presencia = is_array($trabajadores_presencia) ? count($trabajadores_presencia) : 0;
	
	// 		$total_trabajadores = $m->trabajadores_1D($fecha);
	
	// 		echo json_encode([
	// 			'total_presencia' => $total_presencia,
	// 			'total_trabajadores' => $total_trabajadores
	// 		]);
	// 	} catch (Exception $e) {
	// 		echo json_encode([
	// 			'error' => 'Error interno: ' . $e->getMessage()
	// 		]);
	// 	}
	// 	exit;
	// }
	

















	// Cargamos los datos de presencia de oficina por fecha y pernr 
	if (isset($_GET['detalles_presencia'])) {
		$fecha = $_GET['fecha'];
		$pernr = $_GET['pernr'];

		try {
			$datos = $m->informePresenciaOficinaDatos($fecha, $pernr);
			echo json_encode($datos);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => $e->getMessage()]);
		}
	}



	if (isset($_GET['registros_trabajador'])) {
		$fecha = $_GET['fecha'] ?? date('Y-m-d');
		$pernr = $_GET['pernr'] ?? '';

		try {
			$datos = $m->informePresenciaOficinaDatos($fecha, $pernr);
			echo json_encode([
				'success' => true,
				'data' => $datos
			]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => $e->getMessage()]);
		}
	}


	// Verificar registros existentes para múltiples trabajadores en una fecha
	if (isset($_GET['verificar_registros_existentes'])) {
		header('Content-Type: application/json');

		$data = json_decode(file_get_contents("php://input"), true);
		$fecha = $data['fecha'] ?? date('Y-m-d');
		$trabajadores = $data['trabajadores'] ?? [];

		if (empty($trabajadores)) {
			echo json_encode([
				'success' => false,
				'error' => 'No se proporcionaron trabajadores'
			]);
			exit;
		}

		try {
			$registros_por_trabajador = [];

			foreach ($trabajadores as $pernr) {
				$datos = $m->informePresenciaOficinaDatos($fecha, $pernr);
				$registros_por_trabajador[$pernr] = $datos;
			}

			echo json_encode([
				'success' => true,
				'fecha' => $fecha,
				'registros' => $registros_por_trabajador
			]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode([
				'success' => false,
				'error' => $e->getMessage()
			]);
		}
	}





	if (isset($_GET['grupo_horario'])) {
		$grupoId = $_GET['grupo_horario'];

		try {
			$datosGrupo = $m->obtenerGrupoHorarioPorId($grupoId);
			if ($datosGrupo) {
				header('Content-Type: application/json');
				echo json_encode($datosGrupo);
			} else {
				http_response_code(404);
				echo json_encode(['error' => 'Grupo no encontrado']);
			}
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => 'Error interno: ' . $e->getMessage()]);
		}
	}



	// Endpoint: Obtener áreas de trabajo (PERSK)
	if (isset($_GET['obtener_areas_trabajo'])) {
		header('Content-Type: application/json');

		try {
			$areas = $m->obtener_areas_trabajo();
			echo json_encode(['areas' => $areas]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => 'Error al obtener áreas: ' . $e->getMessage()]);
		}
		exit;
	}

	// Endpoint: Obtener tipos de contrato
	if (isset($_GET['obtener_tipos_contrato'])) {
		header('Content-Type: application/json');

		try {
			$contratos = $m->obtener_tipos_contrato();
			echo json_encode(['contratos' => $contratos]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => 'Error al obtener tipos de contrato: ' . $e->getMessage()]);
		}
		exit;
	}

	// Endpoint: Obtener trabajadores por áreas seleccionadas
	$data = json_decode(file_get_contents('php://input'), true);
	if (isset($data['action']) && $data['action'] === 'obtener_trabajadores_por_areas') {
		header('Content-Type: application/json');

		try {
			$areas = isset($data['areas']) ? $data['areas'] : [];
			$contratos = isset($data['contratos']) ? $data['contratos'] : null;

			// Validar que al menos haya un filtro activo
			if (empty($areas) && empty($contratos)) {
				echo json_encode(['trabajadores' => []]);
				exit;
			}

			$trabajadores = $m->obtener_trabajadores_por_areas_y_contratos($areas, $contratos);
			echo json_encode(['trabajadores' => $trabajadores]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => 'Error al obtener trabajadores: ' . $e->getMessage()]);
		}
		exit;
	}

	// Endpoint para buscar trabajadores manualmente
	if (isset($data['action']) && $data['action'] === 'buscar_trabajadores_manual') {
		header('Content-Type: application/json');

		try {
			$termino = isset($data['termino']) ? trim($data['termino']) : '';
			$trabajadores = $m->buscar_trabajadores_manual($termino);
			echo json_encode(['trabajadores' => $trabajadores]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['error' => 'Error al buscar trabajadores: ' . $e->getMessage()]);
		}
		exit;
	}

	// Endpoint para agregar festivo a un grupo de horario
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$data = json_decode(file_get_contents("php://input"), true);

		if (isset($data['action']) && $data['action'] === 'agregar_festivo_grupo') {
			header('Content-Type: application/json');

			$grupo_id = $data['grupo_id'] ?? null;
			$fecha = $data['fecha'] ?? null;
			$tipo_festivo = $data['tipo_festivo'] ?? null;

			// Validar datos
			if (!$grupo_id || !$fecha || !$tipo_festivo) {
				echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
				exit;
			}

			// Usar el método del modelo
			$resultado = $m->agregar_festivo_grupo($grupo_id, $fecha, $tipo_festivo);
			echo json_encode($resultado);
			exit;
		}

		// Endpoint para eliminar festivo de un grupo
		if (isset($data['action']) && $data['action'] === 'eliminar_festivo_grupo') {
			header('Content-Type: application/json');

			$grupo_id = $data['grupo_id'] ?? null;
			$fecha = $data['fecha'] ?? null;

			// Validar datos
			if (!$grupo_id || !$fecha) {
				echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
				exit;
			}

			// Usar el método del modelo
			$resultado = $m->eliminar_festivo_grupo($grupo_id, $fecha);
			echo json_encode($resultado);
			exit;
		}

		// Endpoint para clonar grupo de horario a otro año
		if (isset($data['action']) && $data['action'] === 'clonar_grupo_horario') {
			header('Content-Type: application/json');

			$grupo_id_original = $data['grupo_id_original'] ?? null;
			$anio_destino = $data['anio_destino'] ?? null;
			$nuevo_nombre = $data['nuevo_nombre'] ?? null;
			$clonar_trabajadores = isset($data['clonar_trabajadores']) ? (bool) $data['clonar_trabajadores'] : false;

			// Validar datos
			if (!$grupo_id_original || !$anio_destino || !$nuevo_nombre) {
				echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
				exit;
			}

			// Usar el método del modelo
			$resultado = $m->clonar_grupo_horario($grupo_id_original, $anio_destino, $nuevo_nombre, $clonar_trabajadores);

			echo json_encode($resultado);
			exit;
		}		// Endpoint para guardar asignación de trabajadores a grupo de horario
		if (isset($data['action']) && $data['action'] === 'guardar_asignacion_trabajadores') {
			header('Content-Type: application/json');

			$grupo_id = $data['grupo_id'] ?? null;
			$trabajadores = $data['trabajadores'] ?? [];

			// Validar datos - Permitir array vacío para eliminar todos los trabajadores del grupo
			if (!$grupo_id || !is_array($trabajadores)) {
				echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
				exit;
			}

			// Usar el método del modelo
			$resultado = $m->guardar_asignacion_trabajadores($grupo_id, $trabajadores);
			echo json_encode($resultado);
			exit;
		}

		// Endpoint para obtener trabajadores asignados a un grupo de horario
		if (isset($data['action']) && $data['action'] === 'obtener_trabajadores_asignados') {
			header('Content-Type: application/json');

			$grupo_id = $data['grupo_id'] ?? null;

			// Validar datos
			if (!$grupo_id) {
				echo json_encode(['success' => false, 'message' => 'ID de grupo no proporcionado']);
				exit;
			}

			// Usar el método del modelo
			$resultado = $m->obtener_trabajadores_asignados($grupo_id);
			echo json_encode($resultado);
			exit;
		}
	}


















	// Endpoint para cargar datos de medidas de un trabajador
	if (isset($_GET['datos_medidas']) && isset($_GET['id'])) {
		header('Content-Type: application/json');

		try {
			$id_trabajador = $_GET['id'];
			$datos_medidas = $m->datos_medidas($id_trabajador);

			$data = array();
			foreach ($datos_medidas as $resultado) {
				// Formatear fechas
				$fecha_alta = DateTime::createFromFormat('Ymd', $resultado['BEGDA'])->format('d/m/Y');
				$fecha_baja = DateTime::createFromFormat('Ymd', $resultado['ENDDA'])->format('d/m/Y');

				// Determinar el status
				$status = '';
				if ($resultado['STAT2'] == 0) {
					$status = $lang['baja'];
				} elseif ($resultado['STAT2'] == 1) {
					$status = $lang['rel_lab_sus'];
				} elseif ($resultado['STAT2'] == 2) {
					$status = $lang['pensionista'];
				} elseif ($resultado['STAT2'] == 3) {
					$status = $lang['activo'];
				} else {
					$status = $resultado['STAT2'];
				}

				$row = array(
					'BEGDA' => $fecha_alta,
					'ENDDA' => $fecha_baja,
					'STAT2' => $status,
					'MEDIDA' => $resultado['MASSN'] . " - " . $resultado['MNTXT'],
					'MOTIVO' => $resultado['MASSG'] . " - " . $resultado['MGTXT']
				);
				array_push($data, $row);
			}

			echo json_encode(['success' => true, 'data' => $data]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => $e->getMessage()]);
		}
		exit;
	}

	// Endpoint para cargar datos de asignación de un trabajador
	if (isset($_GET['datos_asig']) && isset($_GET['id'])) {
		header('Content-Type: application/json');

		try {
			$id_trabajador = $_GET['id'];
			$datos_asig = $m->datos_asignacion_trabajador($id_trabajador);

			$data = array();
			foreach ($datos_asig as $resultado) {
				// Formatear fechas
				$fecha_alta = DateTime::createFromFormat('Ymd', $resultado['BEGDA'])->format('d/m/Y');
				$fecha_baja = DateTime::createFromFormat('Ymd', $resultado['ENDDA'])->format('d/m/Y');

				// Formatear almacén
				$almacen = $resultado['DESC_ALMACEN'] != "" ?
					$resultado['ZZLGORT'] . " - " . $resultado['DESC_ALMACEN'] :
					$resultado['ZZLGORT'];

				// Formatear finca
				$finca = $resultado['DESCR'] != "" ?
					$resultado['ALTRN'] . " - " . $resultado['DESCR'] :
					$resultado['ALTRN'];

				// Formatear centro
				$centro = $resultado['DESC_CENTRO'] != "" ?
					$resultado['ZZWERKS'] . " - " . $resultado['DESC_CENTRO'] :
					$resultado['ZZWERKS'];

				// Formatear división
				$division = $resultado['DESC_DIVISION'] != "" ?
					$resultado['WERKS'] . " - " . $resultado['DESC_DIVISION'] :
					$resultado['WERKS'];

				$row = array(
					'BEGDA' => $fecha_alta,
					'ENDDA' => $fecha_baja,
					'PLANS' => $resultado['PLANS'],
					'STEXT_PLANS' => $resultado['STEXT_PLANS'],
					'ALMACEN' => $almacen,
					'FINCA' => $finca,
					'CENTRO' => $centro,
					'DIVISION' => $division,
					'NFC' => $resultado['ZZNFC']
				);
				array_push($data, $row);
			}

			echo json_encode(['success' => true, 'data' => $data]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => $e->getMessage()]);
		}
		exit;
	}

	// Endpoint para cargar datos de dirección de un trabajador
	if (isset($_GET['datos_direccion']) && isset($_GET['id'])) {
		header('Content-Type: application/json');

		try {
			$id_trabajador = $_GET['id'];
			$datos_direccion = $m->datos_direccion_trabajador($id_trabajador);

			if (!empty($datos_direccion)) {
				$resultado = $datos_direccion[0];
				$data = array(
					'PAIS' => isset($resultado['LAND1']) ? $resultado['LAND1'] : '',
					'DESC_PAIS' => isset($resultado['LANDX']) ? $resultado['LANDX'] : '',
					'DESC_REGION' => isset($resultado['BEZEI']) ? $resultado['BEZEI'] : '',
					'POBLACION' => isset($resultado['ORT01']) ? $resultado['ORT01'] : '',
					'COD_POSTAL' => isset($resultado['PSTLZ']) ? $resultado['PSTLZ'] : '',
					'SIGLAS_VP' => isset($resultado['STRDS']) ? $resultado['STRDS'] : '',
					'DESC_SIGLAS_VP' => isset($resultado['VIAPT']) ? $resultado['VIAPT'] : '',
					'CALLE_NUMERO' => isset($resultado['STRAS']) ? $resultado['STRAS'] : '',
					'N_EDIFICIO' => isset($resultado['HSNMR']) ? $resultado['HSNMR'] : '',
					'DESC_CLASE_DIRECCION' => isset($resultado['STEXT']) ? $resultado['STEXT'] : ''
				);
				echo json_encode(['success' => true, 'data' => $data]);
			} else {
				echo json_encode(['success' => true, 'data' => null]);
			}
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => $e->getMessage()]);
		}
		exit;
	}

	// Endpoint para cargar datos de contrato de un trabajador
	if (isset($_GET['datos_contrato']) && isset($_GET['id'])) {
		header('Content-Type: application/json');

		try {
			$id_trabajador = $_GET['id'];
			$datos_contrato = $m->datos_contrato_trabajador($id_trabajador);
			$datos_contrato2 = $m->datos_contrato2_trabajador($id_trabajador);

			$data = array();

			// Función para convertir fecha de YYYYMMDD a DD/MM/YYYY
			function formatearFecha($fecha)
			{
				if (empty($fecha) || strlen($fecha) != 8) {
					return $fecha;
				}
				$year = substr($fecha, 0, 4);
				$month = substr($fecha, 4, 2);
				$day = substr($fecha, 6, 2);
				return $day . '/' . $month . '/' . $year;
			}

			if (!empty($datos_contrato)) {
				$resultado = $datos_contrato[0];
				$data['contrato1'] = array(
					'TIPO_CONTRATO' => isset($resultado['IDCON']) ? $resultado['IDCON'] : '',
					'DESC_TIPO_CONTRATO' => isset($resultado['TTEXT']) ? $resultado['TTEXT'] : '',
					'CLAVE_CONTRATO' => isset($resultado['IDSEG']) ? $resultado['IDSEG'] : '',
					'DESC_CLAVE_CONTRATO' => isset($resultado['DESC_CLAVE_CONTR']) ? $resultado['DESC_CLAVE_CONTR'] : '',
					'BEGDA' => isset($resultado['BEGDA']) ? formatearFecha($resultado['BEGDA']) : '',
					'ENDDA' => isset($resultado['ENDDA']) ? formatearFecha($resultado['ENDDA']) : ''
				);
			} else {
				$data['contrato1'] = null;
			}

			if (!empty($datos_contrato2)) {
				$resultado2 = $datos_contrato2[0];
				$data['contrato2'] = array(
					'RELACION_LABORAL' => isset($resultado2['EMPL_RELATION']) ? $resultado2['EMPL_RELATION'] : '',
					'DESC_RELACION_LABORAL' => isset($resultado2['DESCRIPTION']) ? $resultado2['DESCRIPTION'] : '',
					'BEGDA' => isset($resultado2['BEGDA']) ? formatearFecha($resultado2['BEGDA']) : '',
					'ENDDA' => isset($resultado2['ENDDA']) ? formatearFecha($resultado2['ENDDA']) : ''
				);
			} else {
				$data['contrato2'] = null;
			}

			echo json_encode(['success' => true, 'data' => $data]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => $e->getMessage()]);
		}
		exit;
	}

	// Endpoint para cargar datos ROPO de un trabajador
	if (isset($_GET['datos_ropo']) && isset($_GET['id'])) {
		header('Content-Type: application/json');

		function formatearFecha($fecha){
			if (empty($fecha) || strlen($fecha) != 8) {
				return $fecha;
			}
			$year = substr($fecha, 0, 4);
			$month = substr($fecha, 4, 2);
			$day = substr($fecha, 6, 2);
			return $day . '/' . $month . '/' . $year;
		}

		try {
			$id_trabajador = $_GET['id'];
			$datos_ropo = $m->datos_ropo_trabajador($id_trabajador);

			if (!empty($datos_ropo)) {
				$resultado = $datos_ropo[0];
				$data = array(
					'BEGDA' => isset($resultado['BEGDA']) ? formatearFecha($resultado['BEGDA']) : '',
					'ENDDA' => isset($resultado['ENDDA']) ? formatearFecha($resultado['ENDDA']) : '',
					'ZZCARNET' => isset($resultado['ZZCARNET']) ? $resultado['ZZCARNET'] : '',
					'ZZROPO' => isset($resultado['ZZROPO']) ? $resultado['ZZROPO'] : '',
					'ZZFECHA' => isset($resultado['ZFECHAC']) ? formatearFecha($resultado['ZFECHAC']) : ''
				);
				echo json_encode(['success' => true, 'data' => $data]);
			} else {
				echo json_encode(['success' => true, 'data' => null]);
			}
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => $e->getMessage()]);
		}
		exit;
	}

	// Endpoint para cargar datos de ausencia de un trabajador
	if (isset($_GET['datos_ausencia']) && isset($_GET['id'])) {
		header('Content-Type: application/json');

		try {
			$id_trabajador = $_GET['id'];
			$datos_ausencia = $m->datos_ausencia_trabajador($id_trabajador);

			$data = array();
			foreach ($datos_ausencia as $resultado) {
				$resultado['BEGDA'] = DateTime::createFromFormat('Ymd', $resultado['BEGDA'])->format('d/m/Y');
				$resultado['ENDDA'] = DateTime::createFromFormat('Ymd', $resultado['ENDDA'])->format('d/m/Y');
				$row = array(
					'BEGDA' => isset($resultado['BEGDA']) ? $resultado['BEGDA'] : '',
					'ENDDA' => isset($resultado['ENDDA']) ? $resultado['ENDDA'] : '',
					'AUSENCIA' => isset($resultado['SUBTY']) ? $resultado['SUBTY'] : '',
					'DESC_AUSENCIA' => isset($resultado['ATEXT']) ? $resultado['ATEXT'] : ''
				);
				array_push($data, $row);
			}

			echo json_encode(['success' => true, 'data' => $data]);
		} catch (Exception $e) {
			http_response_code(500);
			echo json_encode(['success' => false, 'error' => $e->getMessage()]);
		}
		exit;
	}

	// Endpoint para listar documentos de un trabajador
	if (isset($_GET['listar_documentos_sap']) && !empty($_GET['pernr'])) {
		$pernr = trim($_GET['pernr']);
		$sap = new SAPMuleSoftConnector();
		$docs = $sap->listarDocumentos($pernr);
		
		header('Content-Type: application/json');
		echo json_encode($docs);
		exit;
	}

	// Endpoint para descargar y previsualizar documento
	if ((isset($_GET['descargar_documento']) || isset($_GET['preview_documento'])) && !empty($_GET['pernr']) && !empty($_GET['doknr'])) {
		$pernr = trim($_GET['pernr']);
		$doknr = trim($_GET['doknr']);
		$inline = isset($_GET['preview_documento']);

		$sap  = new SAPMuleSoftConnector();
		$data = $sap->obtenerDocumento($pernr, $doknr);

		if (isset($data['error'])) {
			http_response_code(500);
			header('Content-Type: application/json');
			echo json_encode(['error' => $data['error']]);
			exit;
		}

		$disposition = $inline ? 'inline' : 'attachment';

		ob_clean();
		header('Content-Type: '. $data['mimetype']);
		header('Content-Disposition: '. $disposition . '; filename="' . $data['filename'] . '"');
		header('Content-Length: '. strlen($data['content']));
		echo $data['content'];
		flush();
		exit;
	}

	// Endpoint para subir documento de un trabajador
	if (isset($_POST['subir_documento_sap']) && !empty($_POST['pernr']) && isset($_FILES['documento'])) {
		$sap = new SAPMuleSoftConnector();
		$pernr = trim($_POST['pernr']);
		$descripcion = $_POST['descripcion'] ?? 'Documento sin descripción';
		
		// Verificar que el archivo se haya subido correctamente
		if ($_FILES['documento']['error'] === UPLOAD_ERR_OK) {
			$filePath = $_FILES['documento']['tmp_name'];
			$originalName = $_FILES['documento']['name'];
			$fileContent = file_get_contents($filePath);
			$result = $sap->subirDocumento($pernr, $fileContent, $originalName, $descripcion);
		} else {
			$result = ['E_SUBRC' => 4, 'E_MESSAGE' => 'Error al subir el archivo al servidor temporal'];
		}

		// Devolver resultado de la operación
		header('Content-Type: application/json');
		echo json_encode($result);
		exit;
	}

	// Endpoint para eliminar documento 
	if (isset($_GET['eliminar_documento']) && !empty($_GET['doknr']) && !empty($_GET['pernr'])) {
		header('Content-Type: application/json; charset=utf-8');
		$sap = new SAPMuleSoftConnector();
		$result = $sap->eliminarDocumento(trim($_GET['pernr']), trim($_GET['doknr']));
		echo json_encode($result);
		exit;
	}

	// Endpoint: Cargar catálogos para alta/baja
	if (isset($_GET['catalogos_altabajas'])) {
		header('Content-Type: application/json');
		try {
			$posiciones    = $m->curl_api_mulesoft([], 'GET', '/catalogos/posiciones');
			$motivo_medida = $m->curl_api_mulesoft([], 'GET', '/catalogos/motivos-medida');
			$tipo_contrato = $m->curl_api_mulesoft([], 'GET', '/catalogos/tipos-contrato');
			$via_pago      = $m->curl_api_mulesoft([], 'GET', '/catalogos/vias-pago');
			$almacenes     = $m->curl_api_mulesoft([], 'GET', '/catalogos/almacenes');
			$area_personal = $m->curl_api_mulesoft([], 'GET', '/catalogos/area-personal');

			echo json_encode([
				'success'       => true,
				'posiciones'    => $posiciones['data']    ?? [],
				'motivo_medida' => $motivo_medida['data'] ?? [],
				'tipo_contrato' => $tipo_contrato['data'] ?? [],
				'via_pago'      => $via_pago['data']      ?? [],
				'almacenes'     => $almacenes['data']     ?? [],
				'area_personal' => $area_personal['data'] ?? [],
			]);
		} catch (Exception $e) {
			echo json_encode(['success' => false, 'error' => $e->getMessage()]);
		}
		exit;
	}

	// Endpoint: Crear solicitud de alta o baja
	$body = json_decode(file_get_contents('php://input'), true) ?? [];
	if (isset($body['crear_solicitud_altabajas'])) {
		header('Content-Type: application/json');

		$tipo    = $body['tipo']    ?? '';
		$subtipo = $body['subtipo'] ?? '';

		if (empty($tipo) || empty($subtipo)) {
			echo json_encode(['success' => false, 'error' => 'Tipo y subtipo obligatorios']);
			exit;
		}

		// TODO: Implementar cuando MuleSoft tenga el endpoint
		echo json_encode(['success' => true, 'message' => 'Solicitud creada correctamente']);
		exit;
	}

	// Endpoint: Buscar trabajador para alta/baja
	if (isset($_GET['buscar_trabajador_altabajas'])) {
		header('Content-Type: application/json');
		$pernr    = trim($_GET['pernr']    ?? '');
		$nombre   = trim($_GET['nombre']   ?? '');
		$sociedad = trim($_GET['sociedad'] ?? '');
		$subtipo  = trim($_GET['subtipo']  ?? '');

		// Baja = todos los estados, Alta = solo activos
		$estado = ($subtipo === 'BA') ? '' : '3';

		$trabajadores = $m->buscarTrabajadoresSap($pernr, $nombre, $sociedad, $estado) ?? [];

		echo json_encode([
			'success' => true,
			'data'    => $trabajadores ?? []
		]);
		exit;
	}
?>
	