<?php
session_start();
include_once("config.php");
require_once("models/sqlsrvModel.php");
include_once("idiomas/" . $_SESSION['idioma_surexport_appreclu'] . ".php");
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
?>

<?php

//Cargamos las fincas en exportar datos
if (isset($_GET['load_fincas_soc']) and $_GET['load_fincas_soc'] != '') {
	$division = $_GET['division'];
	$fincas = $m->fincas_agromobile($_GET['load_fincas_soc'], $_GET['division']);
	?>
	<p style="font-weight: bold;"><?php echo $lang['auto1']; ?></p>
	<input type="checkbox" id="option-all" class="form-check-input" onchange="checkAll(this)" checked>
	<label for="option-all"><?php echo $lang['auto2']; ?></label>
	<br><br>
	<div class="row">
		<?php
		foreach ($fincas as $result) {
			echo '<div class="col-md-3 mb-2">
						<input type="checkbox" name="fincas[]" value="' . $result['ZZCODFI'] . '" class="form-check-input" checked> ' . $result['DESFI'] . '
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
	?>