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
			if (isset($_POST['id_remesa']) and isset($_POST['ano_remesa'])) {
			$datosRemesa = $m->trabajadores_baja_rem($_POST['id_remesa'], $_POST['ano_remesa']);
			} else {
			$datosRemesa = $m->trabajadores_baja();
			}
		} else {
			$datosRemesa = $m->trabajadores_baja();
		}


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
				'BEGDA' => $resultado['BEGDA']->format('Y-m-d'),
				'MOVIL' => $movil,
				'PREFIJO' => $resultado['PRE_TELF'],
				'CORREO' => $resultado['CORREO'],
				'ID_REMESA' => $resultado['id_remesa'],
				'ANO_REMESA' => $resultado['ano_remesa'],
				'NOMBRE_REMESA' => $resultado['nombre_remesa'],
				'FECHA_ULT_LLAMA' => $resultado['FECHA_REGISTRO']
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

		$trabajadores_presencia = $m->trabajadores_presencia($fecha, $tipo);
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
	

?>