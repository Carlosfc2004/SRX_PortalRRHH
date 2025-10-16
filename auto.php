<?php 
session_start(); 
include_once("config.php");
require_once("models/sqlsrvModel.php");
include_once("idiomas/".$_SESSION['idioma_surexport_appreclu'].".php");
$m = new sqlsrvModel();

//Cambiamos el idioma
if (isset($_GET['idioma'])) {
    $_SESSION["idioma_surexport_appreclu"] = $_GET['idioma'];
}
?>

<?php

	//Cargamos las fincas en exportar datos
	if (isset($_GET['load_fincas_soc']) and $_GET['load_fincas_soc'] != '') {
		$division = $_GET['division'];
		$fincas = $m->fincas_agromobile($_GET['load_fincas_soc'],$_GET['division']);
		?>
		<p style="font-weight: bold;"><?php echo $lang['auto1']; ?></p>
		<input type="checkbox" id="option-all" class="form-check-input" onchange="checkAll(this)" checked>
		<label for="option-all"><?php echo $lang['auto2']; ?></label>
		<br><br>
		<div class="row">
		<?php
			foreach ($fincas as $result) {
				echo '<div class="col-md-3 mb-2">
						<input type="checkbox" name="fincas[]" value="'.$result['ZZCODFI'].'" class="form-check-input" checked> '.$result['DESFI'].'
					</div>';
			}
		echo "</div>";

	?>
	<script>
		function checkAll(mainCheckbox) {
			var checkboxes = document.querySelectorAll("input[type='checkbox'][name='fincas[]']");
			checkboxes.forEach(function(checkbox) {
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
		$operarios = $m->operarios_centro($centro,$division);
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
		$(document).ready(function() {
			$('#operarios').select2({
				placeholder: '<?php echo $lang['auto7']; ?>',
				closeOnSelect: false,
				templateResult: formatState,
				templateSelection: formatState,
				minimumInputLength: 3,
				language: {
					inputTooShort: function() {
						return '<?php echo $lang['auto4']; ?>';
					},
					noResults: function() {
						return '<?php echo $lang['auto5']; ?>';
					},
					searching: function() {
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
			$('#form_export').on('submit', function(event) {
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
				$datosRemesa = $m->trabajadores_baja_rem($_POST['id_remesa'], $_POST['ano_remesa'], $_POST['ubi_trab'], $_POST['fecha_ini'] ?? null, $_POST['fecha_fin'] ?? null);
			} elseif (isset($_POST['id_remesa']) && isset($_POST['ano_remesa'])) {
				$datosRemesa = $m->trabajadores_baja_rem($_POST['id_remesa'], $_POST['ano_remesa']);
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



	if (isset($_GET['rango_fechas'])) {
		header('Content-Type: application/json; charset=utf-8');

		$id = $_GET['id'] ?? null;
		if (!$id) {
			http_response_code(400);
			echo json_encode(['error' => 'ID de rango de fechas no proporcionado']);
			exit;
		}

		$rango = $m->getRangoFechasById($id);

		if ($rango) {
			$response = [
				'id' => $rango['id'],
				'fecha_inicio' => ($rango['fecha_inicio'] instanceof DateTime) ? $rango['fecha_inicio']->format('Y-m-d') : substr($rango['fecha_inicio'], 0, 10),
				'fecha_fin' => ($rango['fecha_fin'] instanceof DateTime) ? $rango['fecha_fin']->format('Y-m-d') : substr($rango['fecha_fin'], 0, 10),
				'tipo' => $rango['tipo']
			];
			echo json_encode($response);
		} else {
			http_response_code(404);
			echo json_encode(['error' => 'Rango de fechas no encontrado']);
		}
	}


// --- BLOQUE NUEVO: Comprobar si una fecha ya está configurada en el calendario laboral ---
// if (isset($_GET['comprobar_rango'])) {
//     $data = json_decode(file_get_contents("php://input"), true);
//     $fechaInicio = $data['inicio'] ?? null;
//     $fechaFin    = $data['fin'] ?? null;
//     $tipoNuevo   = $data['tipo'] ?? null;

//     $existe = false;
//     $mensaje = "";
//     $conflicto = null;

//     if ($fechaInicio && $fechaFin && $tipoNuevo) {
//         $resultados = $m->obtenerRangoFechas(date('Y', strtotime($fechaInicio)));

//         foreach ($resultados as $rango) {
//             $inicio = ($rango['fecha_inicio'] instanceof DateTime)
//                 ? $rango['fecha_inicio']->format('Y-m-d')
//                 : substr($rango['fecha_inicio'], 0, 10);

//             $fin = ($rango['fecha_fin'] instanceof DateTime)
//                 ? $rango['fecha_fin']->format('Y-m-d')
//                 : substr($rango['fecha_fin'], 0, 10);

//             $tipoExistente = $rango['tipo'];

//             $haySolape = !($fechaFin < $inicio || $fechaInicio > $fin);

//             if ($haySolape) {
//                 // --- Festivos ---
//                 if ($tipoNuevo === 'festivo_nacional') {
//                     if (in_array($tipoExistente, ['festivo_nacional', 'festivo_autonomico'])) {
//                         $existe = true;
//                         $mensaje = "❌ Festivo nacional solapa con otro festivo ($tipoExistente) entre $inicio y $fin.";
//                         $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
//                         break;
//                     }
//                     continue; // puede pisar jornadas
//                 }

//                 if ($tipoNuevo === 'festivo_autonomico') {
//                     if ($tipoExistente === 'festivo_nacional') {
//                         $existe = true;
//                         $mensaje = "❌ Festivo autonómico no puede solaparse con festivo nacional ($inicio - $fin).";
//                         $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
//                         break;
//                     }
//                     if ($tipoExistente === 'festivo_autonomico') {
//                         $existe = true;
//                         $mensaje = "❌ Festivo autonómico no puede solaparse con otro festivo autonómico ($inicio - $fin).";
//                         $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
//                         break;
//                     }
//                     continue; // puede pisar jornadas
//                 }

//                 // --- Especial ---
//                 if ($tipoNuevo === 'especial') {
//                     if (in_array($tipoExistente, ['reducida', 'especial'])) {
//                         if ($tipoExistente === 'especial') continue; // mismo tipo permitido
//                         $existe = true;
//                         $mensaje = "❌ Especial no puede solaparse con reducida ($inicio - $fin).";
//                         $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
//                         break;
//                     }
//                     if (in_array($tipoExistente, ['festivo_nacional', 'festivo_autonomico'])) {
//                         $existe = true;
//                         $mensaje = "❌ Especial no puede solaparse con el festivo ($tipoExistente) entre $inicio y $fin.";
//                         $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
//                         break;
//                     }
//                 }

//                 // --- Reducida ---
//                 if ($tipoNuevo === 'reducida') {
//                     if ($tipoExistente !== 'reducida') {
//                         $existe = true;
//                         $mensaje = "❌ Reducida no puede solaparse con $tipoExistente ($inicio - $fin). Solo puede solaparse con otra reducida.";
//                         $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
//                         break;
//                     }
//                     // mismo tipo reducida → permitido
//                 }
//             }
//         }
//     }

//     header('Content-Type: application/json');
//     echo json_encode([
//         'existe' => $existe,
//         'mensaje' => $mensaje,
//         'conflicto' => $conflicto
//     ]);
//     exit;
// }



if (isset($_GET['comprobar_rango'])) {
    $data = json_decode(file_get_contents("php://input"), true);
    $fechaInicio = $data['inicio'] ?? null;
    $fechaFin    = $data['fin'] ?? null;
    $tipoNuevo   = $data['tipo'] ?? null;

    $existe = false;
    $mensaje = "";
    $conflicto = null;

    // Mapeo de tipos legibles
    $tiposLegibles = [
        'festivo_nacional'   => 'Festivo Nacional',
        'festivo_autonomico' => 'Festivo Autonómico',
        'especial'           => 'Jornada Especial (09:00 - 14:00)',
        'reducida'           => 'Jornada Reducida (08:00 - 15:00)'
    ];

    if ($fechaInicio && $fechaFin && $tipoNuevo) {
        $resultados = $m->obtenerRangoFechas(date('Y', strtotime($fechaInicio)));

        // Recorrer cada día del rango
        for ($d = new DateTime($fechaInicio); $d <= new DateTime($fechaFin); $d->modify('+1 day')) {
            $fechaStr = $d->format('Y-m-d');
            $solapamientoEspecialConReducida = false;

            foreach ($resultados as $rango) {
                $inicio = ($rango['fecha_inicio'] instanceof DateTime)
                    ? $rango['fecha_inicio']->format('Y-m-d')
                    : substr($rango['fecha_inicio'], 0, 10);
                $fin = ($rango['fecha_fin'] instanceof DateTime)
                    ? $rango['fecha_fin']->format('Y-m-d')
                    : substr($rango['fecha_fin'], 0, 10);
                $tipoExistente = $rango['tipo'];

                $enRango = ($fechaStr >= $inicio && $fechaStr <= $fin);

                // 1️⃣ Mismo tipo y mismo día → bloquear
                if ($enRango && $tipoNuevo === $tipoExistente) {
                    $existe = true;
                    $mensaje = "❌ Ya existe un día $fechaStr de tipo ".$tiposLegibles[$tipoNuevo].".";
                    $conflicto = ['fecha' => $fechaStr, 'tipo' => $tipoExistente];
                    break 2; // salir de ambos bucles
                }

                // 2️⃣ Regla de prioridad y solapamientos
                if ($enRango) {
                    // Festivos
                    if (in_array($tipoNuevo, ['festivo_nacional', 'festivo_autonomico'])) {
                        if (in_array($tipoExistente, ['festivo_nacional', 'festivo_autonomico'])) {
                            $existe = true;
                            $mensaje = "❌ El rango seleccionado se solapa con otro ".$tiposLegibles[$tipoExistente]." entre $inicio y $fin.";
                            $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
                            break 2;
                        }
                        continue; // puede pisar jornadas
                    }

                    // Especial
                    if ($tipoNuevo === 'especial') {
                        if ($tipoExistente === 'especial') continue; // mismo tipo permitido
                        if (in_array($tipoExistente, ['festivo_nacional', 'festivo_autonomico'])) {
                            $existe = true;
                            $mensaje = "❌ ".$tiposLegibles[$tipoNuevo]." no puede solaparse con ".$tiposLegibles[$tipoExistente]." entre $inicio y $fin.";
                            $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
                            break 2;
                        }
                        if ($tipoExistente === 'reducida') {
                            // Especial sobre Reducida → aviso informativo
                            $solapamientoEspecialConReducida = true;
                        }
                    }

                    // Reducida
                    if ($tipoNuevo === 'reducida') {
                        if ($tipoExistente !== 'reducida') {
                            $existe = true;
                            $mensaje = "❌ ".$tiposLegibles[$tipoNuevo]." no puede solaparse con ".$tiposLegibles[$tipoExistente]." ($inicio - $fin). Solo puede solaparse con otra reducida.";
                            $conflicto = ['inicio' => $inicio, 'fin' => $fin, 'tipo' => $tipoExistente];
                            break 2;
                        }
                    }
                }
            }

            // Si hay solapamiento Especial con Reducida, mostrar aviso pero no bloquear
            if ($solapamientoEspecialConReducida && !$existe) {
                $mensaje = "ℹ️ El día $fechaStr ya es ".$tiposLegibles['reducida'].", pero puedes añadirlo como ".$tiposLegibles['especial'].".";
            }
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'existe' => $existe,
        'mensaje' => $mensaje,
        'conflicto' => $conflicto
    ]);
    exit;
}
?>