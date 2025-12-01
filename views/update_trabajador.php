<?php
include_once("header.php");

// Verifica si $params['info_trabajador'] es un array y no es falso
if (isset($params['info_trabajador']) && is_array($params['info_trabajador'])) {
	$pernr = isset($params['info_trabajador']['PERNR']) ? htmlspecialchars($params['info_trabajador']['PERNR'], ENT_QUOTES, 'UTF-8') : '';
	$nombreyapellidos = isset($params['info_trabajador']['NOMBREYAPELLIDOS']) ? htmlspecialchars($params['info_trabajador']['NOMBREYAPELLIDOS'], ENT_QUOTES, 'UTF-8') : '';
} else {
	$pernr = '';
	$nombreyapellidos = '';
}
?>




<div class="pagetitle">
	<h1><?php echo $lang['titu_upd_trab']; ?> (<?php echo $pernr . " - " . $nombreyapellidos; ?>)</h1>
	<?php
	// Variables para la navegación
	$id_remesa = $_POST['id_remesa'] ?? $_GET['id_remesa'] ?? $_GET['id_rem'] ?? $_SESSION['id_remesa'] ?? '';
	$ano_remesa = $_POST['ano_remesa'] ?? $_GET['ano_remesa'] ?? $_GET['ano_rem'] ?? $_SESSION['ano_remesa'] ?? '';
	$href = '';

	// Determinar la url para cada acceso
	if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
		if (isset($_POST['datos_remesa']) && $_POST['datos_remesa'] == "1" && isset($_GET['showll']) or isset($_GET['id_rem']) && isset($_GET['ano_rem'])) {
			$href = "admin_cont.php?controller=index&action=view_remesa_llama&id=" . htmlspecialchars($id_remesa) . "&ano=" . htmlspecialchars($ano_remesa) . "&remesa=" . $_GET['remesa'];
		} elseif (isset($_GET['contact']) && isset($_GET['baja'])) {
			$href = "admin_cont.php?controller=index&action=trabajadores_baja";
		} elseif (isset($_GET['presencia'])) {
			// Asegúrate de que los valores de tipo y fecha_inicio estén en $_POST
			$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'presencia';
			$fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : date('Y-m-d');
			$href = "admin_cont.php?controller=index&action=presencia&tipo=" . $tipo . "&fecha_inicio=" . $fecha_inicio;
		} else {
			$href = "admin_cont.php?controller=index&action=trabajadores_sap";
		}
	}
	?>

	<!-- Boton dinamico de navegación -->
	<a class="bi bi-arrow-left-square-fill atras" href="<?php echo $href; ?>" style="text-decoration: none; "></a>

</div>
<nav>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i
					class="bi bi-house-door"></i></a></li>
		<li class="breadcrumb-item"><a
				href="admin_cont.php?controller=index&action=trabajadores_sap"><?php echo $lang['menu1']; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $lang['titu_upd_trab']; ?></li>
	</ol>
</nav>
<section class="section profile">
	<div class="row">
		<div class="col-xl-12">
			<div class="card">
				<div class="card-body pt-3">
					<!-- NOMBRE PESTAÑAS -->
					<ul class="nav nav-tabs nav-tabs-bordered d-flex" id="borderedTabJustified" role="tablist">
						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link <?php if (!isset($_GET['showll']) && !isset($_GET['contact']) && !isset($_GET['alertas']))
								echo 'active'; ?>" data-bs-toggle="tab" data-bs-target="#datos-personales">
								<i class="bi bi-person-fill me-2"></i>
								<?php echo $lang['pestaña_1']; ?>
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link <?php if (isset($_GET['contact']))
								echo 'active'; ?>" data-bs-toggle="tab" data-bs-target="#datos-contacto">
								<i class="bi bi-telephone-fill me-2"></i>
								<?php echo $lang['pestaña_2']; ?>
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#datos-direccion">
								<i class="bi bi-geo-alt-fill me-2"></i>
								<?php echo $lang['pestaña_3']; ?>
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#datos-contrato">
								<i class="bi bi-file-text-fill me-2"></i>
								<?php echo $lang['pestaña_4']; ?>
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#datos-ropo">
								<i class="bi bi-card-checklist me-2"></i>
								<?php echo $lang['pestaña_5']; ?>
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#datos-ausencia">
								<i class="bi bi-calendar-x-fill me-2"></i>
								<?php echo $lang['pestaña_6']; ?>
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#datos-asignacion">
								<i class="bi bi-briefcase-fill me-2"></i>
								<?php echo $lang['pestaña_7']; ?>
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link" data-bs-toggle="tab" data-bs-target="#datos-medidas">
								<i class="bi bi-clipboard2-data-fill me-2"></i>
								<?php echo $lang['pestaña_9']; ?>
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link <?php if (isset($_GET['alertas']))
								echo 'active'; ?>" data-bs-toggle="tab" data-bs-target="#alertas">
								<i class="bi bi-exclamation-triangle-fill me-2"></i>
								Alertas
							</button>
						</li>

						<li class="nav-item flex-fill" role="presentation">
							<button class="nav-link <?php if (isset($_GET['showll']))
								echo 'active'; ?>" data-bs-toggle="tab" data-bs-target="#llamamientos">
								<i class="bi bi-bell-fill me-2"></i>
								<?php echo $lang['pestaña_8']; ?>
							</button>
						</li>

					</ul>


					<!-- PESTAÑA 1 DATOS PERSONALES -->
					<div class="tab-content pt-1">
						<div class="tab-pane <?php if (isset($_GET['showll']) or isset($_GET['contact']) or isset($_GET['alertas'])) {echo '';} else {echo 'fade show active profile-overview';} ?> " id="datos-personales">
							<?php
							// Al inicio del archivo o en un archivo de configuración
							$nombreCompleto = '';

							if ($params['info_trabajador']['NACHN'] && $params['info_trabajador']['VORNA']) {
								$nombreCompleto = $params['info_trabajador']['NACHN'];

								// Añadir segundo apellido si existe
								if (!empty($params['info_trabajador']['NACH2'])) {
									$nombreCompleto .= ' ' . $params['info_trabajador']['NACH2'];
								}

								// Añadir nombre
								$nombreCompleto .= ', ' . $params['info_trabajador']['VORNA'];
							} elseif (!empty($params['info_trabajador']['NOMBREYAPELLIDOS'])) {
								$nombreCompleto = $params['info_trabajador']['NOMBREYAPELLIDOS'];
							}

							?>


							<div class="row mb-3 mt-3">
								<div class="col-md-6">
									<label class="form-label"><b><?php echo $lang['nombre']; ?></b></label>
									<input type="text" name="nombre" id="nombre" class="form-control"
										value="<?php echo $nombreCompleto; ?>" readonly>

								</div>
								<div class="col-md-3">
									<label class="form-label"><b>Cod. Trabajador</b></label>
									<input type="text" name="ID" id="pernr" class="form-control"
										value="<?php echo isset($params['info_trabajador']['PERNR']) ? $params['info_trabajador']['PERNR'] : ''; ?>"
										readonly>
								</div>
								<div class="col-md-3">
									<label class="form-label"><b><?php echo $lang['sexo']; ?></b></label>
									<input type="text" name="SEXO" class="form-control"
										value="<?php echo isset($params['info_trabajador']['GESCH']) ? ($params['info_trabajador']['GESCH'] == '1' ? 'Masculino' : 'Femenino') : ''; ?>"
										readonly>
								</div>
							</div>

							<div class="row mb-3">
								<div class="col-md-3">
									<label class="form-label"><b><?php echo $lang['fecha_nac']; ?></b></label>
									<?php 
										// Formatear la fecha de nacimiento de 99999999 a dd/mm/yyyy
										$fecha_nac = isset($params['info_trabajador']['GBDAT']) ? $params['info_trabajador']['GBDAT'] : '';
										if (strlen($fecha_nac) == 8) {
											$year = substr($fecha_nac, 0, 4);
											$month = substr($fecha_nac, 4, 2);
											$day = substr($fecha_nac, 6, 2);
											$fecha_nac_formateada = $day . '/' . $month . '/' . $year;
										} else {
											$fecha_nac_formateada = $fecha_nac; // Mantener el valor original si no tiene 8 caracteres
										}
									?>
									<input type="text" name="FECHA_NACIMIENTO" class="form-control" value="<?php echo $fecha_nac_formateada; ?>"
										readonly>
								</div>
								<div class="col-md-4">
									<label class="form-label"><b><?php echo $lang['lug_nac']; ?></b></label>
									<?php
									$lugar_nac = isset($params['info_trabajador']['GBORT']) ? $params['info_trabajador']['GBORT'] : '';
									?>
									<input type="text" name="lugar_nac" id="lugar_nac" class="form-control"
										value="<?php echo $lugar_nac; ?>" readonly>
								</div>
								<div class="col-md-3">
									<label class="form-label"><b><?php echo $lang['pais_nac']; ?></b></label>
									<?php
										$desc_pais_na = isset($params['info_trabajador']['GBLND']) ? $params['info_trabajador']['GBLND'] : '';
									?>
									<input type="text" name="pais_nac" id="pais_nac" class="form-control"
										value="<?php echo $desc_pais_na; ?>" readonly>
								</div>
								<div class="col-md-2">
									<label class="form-label"><b><?php echo $lang['nacionalidad']; ?></b></label>
									<?php
									$nacionalidad = isset($params['info_trabajador']['NATIO']) ? $params['info_trabajador']['NATIO'] : '';
									?>
									<input type="text" name="nacionalidad" id="nacionalidad" class="form-control"
										value="<?php echo $nacionalidad; ?>" readonly>
								</div>
							</div>

							<!-- DNI y Fecha de Validez -->
							<div class="row mb-3">
								<div class="col-md-3">
									<label class="form-label"><b><?php echo $lang['tip_doc']; ?></b></label>
									<input type="text" name="tipo_doc" class="form-control"
										value="<?php echo isset($params['info_trabajador']['TIPODOCUMENTO']) ? $params['info_trabajador']['TIPODOCUMENTO'] : ''; ?>"
										readonly>
								</div>
								<div class="col-md-3">
									<label class="form-label"><b><?php echo $lang['num_doc']; ?></b></label>
									<input type="text" name="dni" class="form-control"
										value="<?php echo isset($params['info_trabajador']['PERID']) ? $params['info_trabajador']['PERID'] : ''; ?>"
										readonly>
								</div>
								<div class="col-md-6">
									<form
										action="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $params['info_trabajador']['PERNR']; ?>&fecha"
										method="post" class="d-flex align-items-end"
										onsubmit="return confirmarEnvio(event)">

										<input type="hidden" name="tipo_doc2"
											value="<?php echo isset($params['info_trabajador']['TIPODOCUMENTO']) ? $params['info_trabajador']['TIPODOCUMENTO'] : ''; ?>">
										<div class="col-md-4 me-2">
											<label class="form-label"><b>Fecha Validez</b></label>
											<input type="date" name="validez" class="form-control"
												value="<?php echo isset($params['fecha_val_dni'][0]['fecha_validez']) ? $params['fecha_val_dni'][0]['fecha_validez']->format('Y-m-d') : ''; ?>"
												required>
										</div>
										<div class="col-md-5">
											<?php
											if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
												?>
												<input type="submit" name="act_cont"
													value="<?php echo $lang['actualizar'] . ' Validez'; ?>"
													class="btn btn-primary">
												<?php
											}
											?>
										</div>
									</form>

									<script>
										function confirmarEnvio(event) {
											event.preventDefault();
											const form = event.target.closest('form');

											alertify.confirm('Confirmación',
												'¿Está seguro que desea actualizar la fecha de validez?',
												function () {
													form.submit();
												},
												function () {
													alertify.error('Operación cancelada');
												}
											).set('labels', { ok: 'Aceptar', cancel: 'Cancelar' });

											return false;
										}
									</script>
								</div>
							</div>
							<br>

							<!-- NFC -->
							<div class="row mb-3">
								<div class="col-md-3">
									<label class="form-label"><b><?php echo $lang['nfc_tarj']; ?></b></label>
									<input type="text" class="form-control" name="nfc" value="<?php $nfc = isset($params['datos_nfc'][0]['ULTIMO_NFC']) ? $params['datos_nfc'][0]['ULTIMO_NFC'] : '';
									echo $nfc; ?>" readonly>
									<?php
									if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
										?>
										<br>
										<!-- Vertically centered Modal Para actualizar NFC -->
										<button type="button" class="btn btn-primary" data-bs-toggle="modal"
											data-bs-target="#Actualizar_NFC_modal">
											Actualizar NFC
										</button>
										<div class="modal fade" id="Actualizar_NFC_modal" tabindex="-1">
											<div class="modal-dialog modal-dialog-centered">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title">Actualizar NFC </h5>
														<button type="button" class="btn-close" data-bs-dismiss="modal"
															aria-label="Close"></button>
													</div>
													<?php
													if (isset($_GET['id_rem']) && isset($_GET['ano_rem'])) {
														$url = '&id_rem=' . $_GET['id_rem'] . '&ano_rem=' . $_GET['ano_rem'];
													} else {
														$url = '';
													}
													?>
													<form
														action="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $params['info_trabajador']['PERNR']; ?>&NFC<?php echo $url; ?>"
														method="post" id="form_nfc" onsubmit="return validarNFC()">
														<div class="modal-body">
															<label
																class="form-label"><b><?php echo $lang['nfc_tarj']; ?></b></label>
															<input type="text" class="form-control" name="nfc" id="nfc"
																value="<?php $nfc = isset($params['datos_nfc'][0]['ULTIMO_NFC']) ? $params['datos_nfc'][0]['ULTIMO_NFC'] : '';
																echo $nfc; ?>">
															<div id="error-nfc" class="text-danger"
																style="display: none; margin-top: 5px;">
																<?php echo $lang['nfc_vacio']; ?>
															</div>
															<div id="error-nfc-igual" class="text-danger"
																style="display: none; margin-top: 5px;">
																<?php echo $lang['nfc_igual']; ?>
															</div>

															<label class="form-label mt-3">
																<b>Fecha </b>
																<button type="button" class=""
																	style="background-color: transparent;"
																	data-bs-toggle="tooltip" data-bs-placement="right"
																	data-bs-original-title="Indica la fecha de alta del trabajador o la actual si ya esta de alta">
																	<i class="ri-question-line fs-5"></i>
																</button>
															</label>
															<input type="date" class="form-control" name="fecha_nfc"
																value="" required>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary"
																data-bs-dismiss="modal">Cerrar</button>
															<button type="subimt" class="btn btn-primary">Actualizar
																NFC</button>
														</div>

													</form>
												</div>
											</div>
										</div>
										<?php
									}
									?>
								</div>

								<form action="" id="exportar" method="post" style="display: inline-block;">
									<input type="hidden" name="documento"
										value="<?php echo $params['info_trabajador']['PERID']; ?>">
									<input type="hidden" name="nombre"
										value="<?php echo $params['info_trabajador']['NOMBREYAPELLIDOS']; ?>">
									<input type="hidden" name="pernr"
										value="<?php echo $params['info_trabajador']['PERNR']; ?>">
									<?php
									echo "<button type='button' 
													  target='_blank' 
													  onclick=\"document.getElementById('exportar').action='exportar.php?etiqueta_nfc'; 
													  			document.getElementById('exportar').target='_blank'; 
																document.getElementById('exportar').submit();\" 
													  style='background-color: white; margin-right: 60px; margin-top: 10px;'
													  data-bs-toggle='tooltip' 
													  data-bs-placement='right'
														title='Imprimir Tarjeta'>
											<img src='img/pdf.png' style='max-width: 100px; width: 40px;'>
										</button>";
									?>
								</form>



								<script>
									function validarNFC() {
										// Obtener el valor actual del campo NFC
										var nfcNuevo = document.getElementById('nfc').value.trim();
										var nfcActual = '<?php echo $nfc; ?>'.trim();

										// Obtener los elementos de error
										var errorNFC = document.getElementById('error-nfc');
										var errorNFCIgual = document.getElementById('error-nfc-igual');

										var formularioValido = true;

										// Ocultar mensajes de error previos
										errorNFC.style.display = 'none';
										errorNFCIgual.style.display = 'none';

										// Validar que el campo no esté vacío
										if (nfcNuevo === '') {
											errorNFC.style.display = 'block';
											formularioValido = false;
										}

										// Validar que el nuevo NFC sea diferente al actual
										else if (nfcNuevo === nfcActual) {
											errorNFCIgual.style.display = 'block';
											formularioValido = false;
										}

										// Si el formulario es válido, mostrar confirmación
										if (formularioValido) {
											var nombretrabajador = '<?php echo $params['info_trabajador']['NOMBREYAPELLIDOS']; ?>';
											var pernrtrabajador = '<?php echo $pernr; ?>';
											alertify.confirm()
												.setting({
													'title': 'Confirmar actualización',
													'message': '¿Desea actualizar la tarjeta NFC del trabajador ' + nombretrabajador + ' con pernr ' + pernrtrabajador + '?',
													'labels': {
														ok: 'Aceptar',
														cancel: 'Cancelar'
													},
													'closable': true,
													'transition': 'flipx',
													'movable': true,
													'closeOnEscape': true,
													'onok': function () {
														// Enviar el formulario cuando el usuario acepta
														document.getElementById('form_nfc').submit();
													},
													'oncancel': function () {
														alertify.confirm().close(); // Cerrar la ventana de confirmación
													}
												})
												.show();
										}

										return false; // Prevenir el envío por defecto
									}

									// Opcional: Validar el campo NFC mientras el usuario escribe
									document.getElementById('nfc').addEventListener('input', function () {
										var nfcNuevo = this.value.trim();
										var nfcActual = '<?php echo $nfc; ?>'.trim();
										var errorNFC = document.getElementById('error-nfc');
										var errorNFCIgual = document.getElementById('error-nfc-igual');

										// Ocultar mensajes de error previos
										errorNFC.style.display = 'none';
										errorNFCIgual.style.display = 'none';

										// Mostrar errores en tiempo real
										if (nfcNuevo === '') {
											errorNFC.style.display = 'block';
										}
										else if (nfcNuevo === nfcActual) {
											errorNFCIgual.style.display = 'block';
										}
									});
								</script>
							</div>
						</div>


						<!-- PESTAÑA 2 DATOS DE CONTACTO -->

						<div class="tab-pane <?php if (isset($_GET['contact'])) {echo 'show active';} else {echo '';} ?> pt-2" id="datos-contacto">
							<div class="col-md-12">
								<form
									action="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo htmlspecialchars($_GET['id']); ?>&actualizacion&contact<?php echo $url; ?>"
									method="post" id="contactForm">
									<!-- Primera fila: dos campos -->
									<div class="row mb-3">
										<div class="col-md-6">
											<label class="form-label"><b>Email</b></label>
											<?php
											$email = "";
											foreach ($params['datos_contacto'] as $value) {
												if (!empty($value['CORREO'])) {
													$email = htmlspecialchars($value['CORREO']);
												}
											}
											?>
											<input type="email" name="MAIL" id="email" class="form-control"
												value="<?php echo $email; ?>">

										</div>
									</div>












									<!-- Telefono -->
									<div class="row mb-4">
										<label class="form-label"><b><?php echo $lang['telefono']; ?></b></label>
										<div class="col-md-2">
											<?php
											// Inicializamos las variables
											$movil = "";
											$prefijoSeleccionado = "";
											$parentSeleccionado = "";

											// Asegúrate de que el array de datos de contacto no esté vacío
											if (!empty($params['datos_contacto'])) {
												// Recorremos los datos de contacto para encontrar el valor de MOVIL más reciente
												foreach ($params['datos_contacto'] as $value) {
													if (!empty($value['MOVIL'])) {
														$movil = $value['MOVIL']; // Asignamos el valor de MOVIL
														$prefijoSeleccionado = !empty($value['PRE_TELF']) ? htmlspecialchars($value['PRE_TELF']) : $prefijoSeleccionado;
														$parentSeleccionado = !empty($value['PARENT_TELF']) ? htmlspecialchars($value['PARENT_TELF']) : $parentSeleccionado;
														break; // Salimos del loop al encontrar el valor más reciente
													}
												}
											}
											?>
											<label for="PRE_TELF"><?php echo $lang['prefijo']; ?></label><br>

											<select class="form-select" name="PRE_TELF" id="PRE_TELF">
												<option value=""></option> <!-- Opción por defecto vacía -->
												<?php
												foreach ($params['prefijos'] as $value) {
													echo '<option value="' . $value['prefijo'] . '"';
													// Comparamos con el prefijo seleccionado para marcar como 'selected'
													if ($value['prefijo'] == $prefijoSeleccionado) {
														echo ' selected';
													}
													echo '> +' . $value['prefijo'] . ' | ' . $value['nombre'] . '</option>';
												}
												?>
											</select>


										</div>
										<br>

										<div class="col-md-2">
											<label><?php echo $lang['numero']; ?></label>
											<input type="text" name="TELEFONO" id="telf" class="form-control"
												pattern="\d*" value="<?php echo $movil; ?>"
												onkeypress="return event.charCode >= 48 && event.charCode <= 57">
										</div>
										<div class="col-md-2">
											<label for="PARENT_TELF"><?php echo $lang['parentesco']; ?></label>
											<select name="PARENT_TELF" id="PARENT_TELF" class="form-select">
												<option value=""></option> <!-- Opción por defecto vacía -->
												<?php
												// Recorremos los parentescos y creamos opciones
												foreach ($params['parentesco'] as $value) {
													echo '<option value="' . $value['PARENTESCO'] . '"';
													// Comparamos el parentesco seleccionado con la variable $parentSeleccionado
													if ($value['PARENTESCO'] == $parentSeleccionado) {
														echo ' selected';
													}
													echo '>' . $value['PARENTESCO'] . '</option>';
												}
												?>
											</select>
										</div>
									</div>





















									<!-- Telefono empresa -->
									<div class="row mb-4">
										<label class="form-label"><b><?php echo $lang['telf_emp']; ?></b></label>

										<div class="col-md-2">
											<?php
											// Inicializamos las variables
											$movilemp = "";
											$prefijoEmpSeleccionado = "";
											$parentEmpSeleccionado = "";

											// Asegúrate de que el array de datos de contacto no esté vacío
											if (!empty($params['datos_contacto'])) {
												// Recorremos los datos de contacto para encontrar el valor de TELEMPRESA más reciente
												foreach ($params['datos_contacto'] as $value) {
													if (!empty($value['TELEMPRESA'])) {
														$movilemp = preg_replace('/[^0-9]/', '', $value['TELEMPRESA']); // Asignamos el valor de TELEMPRESA
														$prefijoEmpSeleccionado = !empty($value['PRE_TELF_EMP']) ? htmlspecialchars($value['PRE_TELF_EMP']) : $prefijoEmpSeleccionado;
														$parentEmpSeleccionado = !empty($value['PARENT_TELF_EMP']) ? htmlspecialchars($value['PARENT_TELF_EMP']) : $parentEmpSeleccionado;
														break; // Salimos del loop al encontrar el valor más reciente
													}
												}
											}
											?>
											<label for="PRE_TELF_EMP"><?php echo $lang['prefijo']; ?></label>
											<select name="PRE_TELF_EMP" id="PRE_TELF_EMP">
												<option value=""></option> <!-- Opción por defecto vacía -->
												<?php
												foreach ($params['prefijos'] as $value) {
													echo '<option value="' . $value['prefijo'] . '"';
													// Comparamos con el prefijo seleccionado para marcar como 'selected'
													if ($value['prefijo'] == $prefijoEmpSeleccionado) {
														echo ' selected';
													}
													echo '> +' . $value['prefijo'] . ' | ' . $value['nombre'] . '</option>';
												}
												?>
											</select>
										</div>

										<br>
										<div class="col-md-2">
											<label><?php echo $lang['numero']; ?></label>
											<input type="text" name="TELEMPRESA" class="form-control" pattern="\d*"
												value="<?php echo !empty($movilemp) ? $movilemp : ''; ?>"
												onkeypress="return event.charCode >= 48 && event.charCode <= 57">
										</div>
										<div class="col-md-2">
											<label for="PARENT_TELF_EMP"><?php echo $lang['parentesco']; ?></label>
											<select name="PARENT_TELF_EMP" id="PARENT_TELF_EMP" class="form-select">
												<option value=""></option> <!-- Opción por defecto vacía -->
												<?php
												// Recorre los parentescos y crea opciones
												foreach ($params['parentesco'] as $value) {
													echo '<option value="' . $value['PARENTESCO'] . '"';
													// Compara el parentesco seleccionado con la variable $parentEmpSeleccionado
													if ($value['PARENTESCO'] == $parentEmpSeleccionado) {
														echo ' selected';
													}
													echo '>' . $value['PARENTESCO'] . '</option>';
												}
												?>
											</select>
										</div>
									</div>


















									<!-- telefono emergencia -->
									<div class="row mb-4">
										<label class="form-label"><b><?php echo $lang['telf_emer']; ?></b></label>
										<div class="col-md-2">
											<?php
											// Inicializamos las variables
											$movilemer = "";
											$prefijoEmerSeleccionado = "";
											$parentEmerSeleccionado = "";

											// Asegúrate de que el array de datos de contacto no esté vacío
											if (!empty($params['datos_contacto'])) {
												// Recorremos los datos de contacto para encontrar el valor de TELEMERGENCIAS más reciente
												foreach ($params['datos_contacto'] as $value) {
													if (!empty($value['TELEMERGENCIAS'])) {
														$movilemer = htmlspecialchars($value['TELEMERGENCIAS']); // Asignamos el valor de TELEMERGENCIAS
														$prefijoEmerSeleccionado = !empty($value['PRE_TELF_EMER']) ? htmlspecialchars($value['PRE_TELF_EMER']) : $prefijoEmerSeleccionado;
														$parentEmerSeleccionado = !empty($value['PARENT_TELF_EMER']) ? htmlspecialchars($value['PARENT_TELF_EMER']) : $parentEmerSeleccionado;
														break; // Salimos del loop al encontrar el valor más reciente
													}
												}
											}
											?>
											<label for="PRE_TELF_EMER"><?php echo $lang['prefijo']; ?></label>
											<select name="PRE_TELF_EMER" id="PRE_TELF_EMER">
												<option value=""></option> <!-- Opción por defecto vacía -->
												<?php
												foreach ($params['prefijos'] as $value) {
													echo '<option value="' . $value['prefijo'] . '"';
													// Comparamos con el prefijo seleccionado para marcar como 'selected'
													if ($value['prefijo'] == $prefijoEmerSeleccionado) {
														echo ' selected';
													}
													echo '> +' . $value['prefijo'] . ' | ' . $value['nombre'] . '</option>';
												}
												?>
											</select>
										</div>
										<br>

										<div class="col-md-2">
											<label><?php echo $lang['numero']; ?></label>
											<input type="text" name="TELEMERGENCIAS" class="form-control" pattern="\d*"
												value="<?php echo $movilemer; ?>"
												onkeypress="return event.charCode >= 48 && event.charCode <= 57">
										</div>
										<div class="col-md-2">
											<label for="PARENT_TELF_EMER"><?php echo $lang['parentesco']; ?></label>
											<select name="PARENT_TELF_EMER" id="PARENT_TELF_EMER" class="form-select">
												<option value=""></option> <!-- Opción por defecto vacía -->
												<?php
												// Recorremos los parentescos y creamos opciones
												foreach ($params['parentesco'] as $value) {
													echo '<option value="' . $value['PARENTESCO'] . '"';
													// Comparamos el parentesco seleccionado con la variable $parentEmerSeleccionado
													if ($value['PARENTESCO'] == $parentEmerSeleccionado) {
														echo ' selected';
													}
													echo '>' . $value['PARENTESCO'] . '</option>';
												}
												?>
											</select>
										</div>
									</div>














									<div class="col-md-12">
										<p style="text-align: left;">
											<?php
											if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
												?>
												<input type="hidden" name="act_cont" value="Actualizar">
												<button type="button" id="confirmBtn"
													class="btn btn-primary mt-3"><?php echo $lang['actualizar']; ?></button>
												<?php
											}
											?>

										</p>
									</div>
								</form>
								<script>
									// Cuando se hace clic en el botón de confirmación
									document.getElementById("confirmBtn").onclick = function () {
										var nombreyapellidos = "<?php echo $nombreyapellidos; ?>"; // Aquí se usa la variable PHP
										alertify.confirm(
											'Actualización de contacto', // Título del cuadro de confirmación
											'¿Realizar actualización de contacto para ' + nombreyapellidos + '?', // Mensaje
											function () { // Si el usuario confirma
												document.getElementById('contactForm').submit(); // Enviar el formulario
											},
											function () { // Si el usuario cancela
												alertify.error('Actualización cancelada');
											}
										);
									};
								</script>

							</div>
						</div>


						<!-- PESTAÑA 3 DIRECCION -->

						<div class="tab-pane pt-2" id="datos-direccion">
							<!-- Loader -->
							<div id="loader-direccion" class="text-center" style="padding: 40px;">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Cargando...</span>
								</div>
								<p class="mt-3">Cargando datos de dirección...</p>
							</div>

							<!-- Contenido -->
							<div id="contenido-direccion" style="display: none;">
								<div class="col-md-12">
									<div class="row mb-3">
										<div class="col-md-3">
											<label class="form-label"><b><?php echo $lang['pais']; ?></b></label>
											<input type="text" name="pais" id="pais" class="form-control" value=""
												readonly>
										</div>
										<div class="col-md-3">
											<label class="form-label"><b><?php echo $lang['provincia']; ?></b></label>
											<input type="text" name="provincia" id="provincia" class="form-control"
												value="" readonly>
										</div>
										<div class="col-md-4">
											<label class="form-label"><b><?php echo $lang['municipio']; ?></b></label>
											<input type="text" name="municipio" id="municipio" class="form-control"
												value="" readonly>
										</div>
										<div class="col-md-2">
											<label class="form-label"><b><?php echo $lang['cod_post']; ?></b></label>
											<input type="text" name="cp" id="cp" class="form-control" value="" readonly>
										</div>
									</div>

									<div class="row mb-3">
										<div class="col-md-3">
											<label class="form-label"><b><?php echo $lang['tipo']; ?></b></label>
											<input type="text" name="tipo" id="tipo" class="form-control" value=""
												readonly>
										</div>
										<div class="col-md-6">
											<label class="form-label"><b><?php echo $lang['calle']; ?></b></label>
											<input type="text" name="calle" id="calle" class="form-control" value=""
												readonly>
										</div>
										<div class="col-md-3">
											<label class="form-label"><b><?php echo $lang['numero']; ?></b></label>
											<input type="text" name="numero" id="numero" class="form-control" value=""
												readonly>
										</div>
									</div>

									<div class="row mb-3">
										<div class="col-md-6">
											<label class="form-label"><b><?php echo $lang['clase']; ?></b></label>
											<input type="text" name="clase" id="clase" class="form-control" value=""
												readonly>
										</div>
									</div>
								</div>
							</div>
						</div>

						<script>
							// Obtener el ID del trabajador desde la URL
							const urlParams = new URLSearchParams(window.location.search);
							const trabajadorId = urlParams.get('id');


							// Función para cargar datos de dirección
							let datosDireccionCargados = false;

							function cargarDatosDireccion() {
								if (datosDireccionCargados || !trabajadorId) return;

								fetch(`auto.php?datos_direccion=1&id=${trabajadorId}`)
									.then(response => response.json())
									.then(result => {
										// Ocultar loader siempre
										document.getElementById('loader-direccion').style.display = 'none';

										if (result.success && result.data) {
											const data = result.data;
											document.getElementById('pais').value = (data.PAIS || '') + (data.DESC_PAIS ? ' - ' + data.DESC_PAIS : '');
											document.getElementById('provincia').value = data.DESC_REGION || '';
											document.getElementById('municipio').value = data.POBLACION || '';
											document.getElementById('cp').value = data.COD_POSTAL || '';
											document.getElementById('tipo').value = (data.SIGLAS_VP || '') /*+ (data.DESC_SIGLAS_VP ? ' - ' + data.DESC_SIGLAS_VP : '')*/;
											document.getElementById('calle').value = data.CALLE_NUMERO || '';
											document.getElementById('numero').value = data.N_EDIFICIO || '';
											document.getElementById('clase').value = data.DESC_CLASE_DIRECCION || '';
											document.getElementById('contenido-direccion').style.display = 'block';
										} else {
											// Mostrar alerta de no datos
											const alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">No hay datos de dirección disponibles.</div>';
											document.getElementById('loader-direccion').innerHTML = alertHtml;
											document.getElementById('loader-direccion').style.display = 'block';
										}

										datosDireccionCargados = true;
									})
									.catch(error => {
										console.error('Error:', error);
										document.getElementById('loader-direccion').innerHTML =
											'<div class="alert alert-danger alert-dismissible fade show" role="alert">Error al cargar los datos de dirección.</div>';
										document.getElementById('loader-direccion').style.display = 'block';
									});
							}
						</script>


						<!-- PESTAÑA 4 DATOS CONTRATO -->

						<div class="tab-pane pt-2" id="datos-contrato">
							<!-- Loader -->
							<div id="loader-contrato" class="text-center" style="padding: 40px;">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Cargando...</span>
								</div>
								<p class="mt-3">Cargando datos de contrato...</p>
							</div>

							<!-- Contenido -->
							<div id="contenido-contrato" style="display: none;">
								<div class="col-md-12">
									<h5
										style="padding: 5px 0 10px 0; font-size: 18px; font-weight: 500; color: #012970; font-family: Poppins, sans-serif;">
										<?php echo $lang['contratos']; ?>
									</h5>
									<div class="row mb-3">
										<div class="col-md-6">
											<label class="form-label"><b><?php echo $lang['tipo_cont']; ?></b></label>
											<input type="text" name="tipo_con" id="tipo_con" class="form-control"
												value="" readonly>
										</div>
										<div class="col-md-6">
											<label class="form-label"><b><?php echo $lang['cla_cont']; ?></b></label>
											<input type="text" name="clave_con" id="clave_con" class="form-control"
												value="" readonly>
										</div>

										<div class="col-md-3 mt-3">
											<label class="form-label"><b><?php echo $lang['alta']; ?>:</b></label>
											<input type="text" name="Alta" id="Alta" class="form-control" value=""
												readonly>
										</div>
										<div class="col-md-3 mt-3">
											<label class="form-label"><b><?php echo $lang['baja']; ?>:</b></label>
											<input type="text" name="Baja" id="Baja" class="form-control" value=""
												readonly>
										</div>
									</div>
									<br>
									<h5
										style="padding: 5px 0 10px 0; font-size: 18px; font-weight: 500; color: #012970; font-family: Poppins, sans-serif;">
										<?php echo $lang['seg_soc']; ?>
									</h5>
									<div class="row mb-3">
										<div class="col-md-5">
											<label class="form-label"><b><?php echo $lang['tipo_cont']; ?></b></label>
											<input type="text" name="tipo_con2" id="tipo_con2" class="form-control"
												value="" readonly>
										</div>
										<div class="col-md-3">
											<label class="form-label"><b><?php echo $lang['alta']; ?>:</b></label>
											<input type="text" name="Alta"  class="form-control" value=""
												readonly>
										</div>
										<div class="col-md-3">
											<label class="form-label"><b><?php echo $lang['baja']; ?>:</b></label>
											<input type="text" name="Baja" class="form-control" value=""
												readonly>
										</div>
									</div>
								</div>
							</div>
						</div>

						<script>
							// Función para cargar datos de contrato
							let datosContratoCargados = false;

							function cargarDatosContrato() {
								if (datosContratoCargados || !trabajadorId) return;

								fetch(`auto.php?datos_contrato=1&id=${trabajadorId}`)
									.then(response => response.json())
									.then(result => {
										// Ocultar loader siempre
										document.getElementById('loader-contrato').style.display = 'none';

										if (result.success && result.data && (result.data.contrato1 || result.data.contrato2)) {
											const data = result.data;

											// Contrato 1
											if (data.contrato1) {
												document.getElementById('tipo_con').value = (data.contrato1.TIPO_CONTRATO || '') + (data.contrato1.DESC_TIPO_CONTRATO ? ' - ' + data.contrato1.DESC_TIPO_CONTRATO : '');
												document.getElementById('clave_con').value = (data.contrato1.CLAVE_CONTRATO || '') + (data.contrato1.DESC_CLAVE_CONTRATO ? ' - ' + data.contrato1.DESC_CLAVE_CONTRATO : '');
												document.getElementById('Alta').value = data.contrato1.BEGDA || '';
												document.getElementById('Baja').value = data.contrato1.ENDDA || '';
											}

											// Contrato 2
											if (data.contrato2) {
												document.getElementById('tipo_con2').value = (data.contrato2.RELACION_LABORAL || '') + (data.contrato2.DESC_RELACION_LABORAL ? ' - ' + data.contrato2.DESC_RELACION_LABORAL : '');
												const altaElem = document.querySelector('input[name="Alta"][readonly]:not(#Alta)');
												const bajaElem = document.querySelector('input[name="Baja"][readonly]:not(#Baja)');
												if (altaElem) altaElem.value = data.contrato2.BEGDA || '';
												if (bajaElem) bajaElem.value = data.contrato2.ENDDA || '';
											}

											document.getElementById('contenido-contrato').style.display = 'block';
										} else {
											// Mostrar alerta de no datos
											const alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">No hay datos de contrato disponibles.</div>';
											document.getElementById('loader-contrato').innerHTML = alertHtml;
											document.getElementById('loader-contrato').style.display = 'block';
										}

										datosContratoCargados = true;
									})
									.catch(error => {
										console.error('Error:', error);
										document.getElementById('loader-contrato').innerHTML =
											'<div class="alert alert-danger alert-dismissible fade show" role="alert">Error al cargar los datos de contrato.</div>';
										document.getElementById('loader-contrato').style.display = 'block';
									});
							}
						</script>


						<!-- PESTAÑA 5 DATOS ROPO -->

						<div class="tab-pane fade pt-2" id="datos-ropo">
							<!-- Loader -->
							<div id="loader-ropo" class="text-center" style="padding: 40px;">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Cargando...</span>
								</div>
								<p class="mt-3">Cargando datos ROPO...</p>
							</div>

							<!-- Contenido -->
							<div id="contenido-ropo" style="display: none;">
								<div class="row mb-3">
									<div class="col-md-3">
										<label class="form-label"><b><?php echo $lang['alta']; ?></b></label>
										<input type="text" name="FechaAlta" class="form-control" value="" readonly>
									</div>
									<div class="col-md-3">
										<label class="form-label"><b><?php echo $lang['baja']; ?></b></label>
										<input type="text" name="FechaBaja" class="form-control" value="" readonly>
									</div>
									<div class="clear"><br></div>
									<div class="col-md-3">
										<label class="form-label"><b>ROPO</b></label>
										<input type="text" name="ROPO" class="form-control" value="" readonly>
									</div>
									<div class="col-md-3">
										<label class="form-label"><b><?php echo $lang['num_carnet']; ?></b></label>
										<input type="text" name="NumCarnet" class="form-control" value="" readonly>
									</div>
									<div class="col-md-3">
										<label class="form-label"><b><?php echo $lang['fecha_carnet']; ?></b></label>
										<input type="text" name="FechaCarnet" class="form-control" value="" readonly>
									</div>
								</div>
							</div>
						</div>

						<script>
							// Función para cargar datos ROPO
							let datosRopoCargados = false;

							function cargarDatosRopo() {
								if (datosRopoCargados || !trabajadorId) return;

								fetch(`auto.php?datos_ropo=1&id=${trabajadorId}`)
									.then(response => response.json())
									.then(result => {
										// Ocultar loader siempre
										document.getElementById('loader-ropo').style.display = 'none';

										if (result.success && result.data) {
											const data = result.data;
											document.querySelector('input[name="FechaAlta"]').value = data.BEGDA || '';
											document.querySelector('input[name="FechaBaja"]').value = data.ENDDA || '';
											document.querySelector('input[name="ROPO"]').value = data.ZZCARNET || '';
											document.querySelector('input[name="NumCarnet"]').value = data.ZZROPO || '';
											document.querySelector('input[name="FechaCarnet"]').value = data.ZZFECHA || '';
											document.getElementById('contenido-ropo').style.display = 'block';
										} else {
											// Mostrar alerta de no datos
											const alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">No hay datos ROPO disponibles.</div>';
											document.getElementById('loader-ropo').innerHTML = alertHtml;
											document.getElementById('loader-ropo').style.display = 'block';
										}

										datosRopoCargados = true;
									})
									.catch(error => {
										console.error('Error:', error);
										document.getElementById('loader-ropo').innerHTML =
											'<div class="alert alert-danger alert-dismissible fade show" role="alert">Error al cargar los datos ROPO.</div>';
										document.getElementById('loader-ropo').style.display = 'block';
									});
							}
						</script>


						<!-- PESTAÑA 6 DATOS AUSENCIA -->

						<div class="tab-pane fade pt-2" id="datos-ausencia">
							<!-- Loader -->
							<div id="loader-ausencia" class="text-center" style="padding: 40px;">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Cargando...</span>
								</div>
								<p class="mt-3">Cargando datos de ausencia...</p>
							</div>

							<!-- Contenido tabla -->
							<div id="tabla-ausencia-container" style="display: none;">
								<table class="table" id="tabla_trab">
									<thead>
										<tr>
											<div class="col-10">
												<th class="col-3"><?php echo $lang['baja']; ?></th>
												<th class="col-3"><?php echo $lang['alta']; ?></th>
												<th class="col-4"><?php echo $lang['motivo']; ?></th>
											</div>
										</tr>
									</thead>
									<tbody id="tbody-ausencia">
									</tbody>
								</table>
							</div>
						</div>

						<script>
							// Función para cargar datos de ausencia
							let datosAusenciaCargados = false;

							function cargarDatosAusencia() {
								if (datosAusenciaCargados || !trabajadorId) return;

								fetch(`auto.php?datos_ausencia=1&id=${trabajadorId}`)
									.then(response => response.json())
									.then(result => {
										// Ocultar loader siempre
										document.getElementById('loader-ausencia').style.display = 'none';

										if (result.success && result.data && result.data.length > 0) {
											const tbody = document.getElementById('tbody-ausencia');
											tbody.innerHTML = '';

											result.data.forEach(row => {
												const tr = document.createElement('tr');
												tr.innerHTML = `
													<td>${row.BEGDA}</td>
													<td>${row.ENDDA}</td>
													<td>${row.AUSENCIA} - ${row.DESC_AUSENCIA} </td>
												`;
												tbody.appendChild(tr);
											});

											document.getElementById('tabla-ausencia-container').style.display = 'block';

											// Inicializar DataTable si está disponible
											if (typeof $.fn.DataTable !== 'undefined') {
												$('#tabla_trab').DataTable({
													pageLength: 10,
													language: {
														url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
													}
												});
											}
										} else {
											// Mostrar alerta de no datos
											const alertHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert">No hay datos de ausencia disponibles.</div>';
											document.getElementById('loader-ausencia').innerHTML = alertHtml;
											document.getElementById('loader-ausencia').style.display = 'block';
										}

										datosAusenciaCargados = true;
									})
									.catch(error => {
										console.error('Error:', error);
										document.getElementById('loader-ausencia').innerHTML =
											'<div class="alert alert-danger alert-dismissible fade show" role="alert">Error al cargar los datos de ausencia.</div>';
										document.getElementById('loader-ausencia').style.display = 'block';
									});
							}
						</script>


						<!-- PESTAÑA 7 DATOS ASIGNACION -->

						<div class="tab-pane fade pt-2" id="datos-asignacion">
							<!-- Loader -->
							<div id="loader-asignacion" class="text-center" style="padding: 40px;">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Cargando...</span>
								</div>
								<p class="mt-3">Cargando datos de asignación...</p>
							</div>

							<!-- Tabla (inicialmente oculta) -->
							<div id="tabla-asignacion-container" style="display: none;">
								<table class="table" id="tabla_asig">
									<thead>
										<tr>
											<div class="col-10">
												<th class="col-1"><?php echo $lang['alta']; ?></th>
												<th class="col-1"><?php echo $lang['baja']; ?></th>
												<th class="col-1"><?php echo $lang['posicion']; ?></th>
												<th class="col-1"><?php echo $lang['puesto']; ?></th>
												<th class="col-1"><?php echo $lang['alamacen']; ?></th>
												<th class="col-1"><?php echo $lang['finca']; ?></th>
												<th class="col-1"><?php echo $lang['centro']; ?></th>
												<th class="col-1"><?php echo $lang['division']; ?></th>
												<th class="col-1">NFC</th>
											</div>
										</tr>
									</thead>
									<tbody id="tbody-asignacion">
										<!-- Los datos se cargarán dinámicamente -->
									</tbody>
								</table>
							</div>
						</div>

						<script>
							let datosMedidasCargados = false;

							// Función para cargar datos de medidas
							function cargarDatosMedidas() {
								if (datosMedidasCargados || !trabajadorId) return;

								fetch(`auto.php?datos_medidas=1&id=${trabajadorId}`)
									.then(response => response.json())
									.then(result => {
										if (result.success) {
											const tbody = document.getElementById('tbody-medidas');
											tbody.innerHTML = '';

											result.data.forEach(row => {
												const tr = document.createElement('tr');
												tr.innerHTML = `
												<td>${row.BEGDA}</td>
												<td>${row.ENDDA}</td>
												<td>${row.STAT2 ?? ''}</td>
												<td>${row.MEDIDA}</td>
												<td>${row.MOTIVO}</td>
											`;
												tbody.appendChild(tr);
											});

											// Ocultar loader y mostrar tabla
											document.getElementById('loader-medidas').style.display = 'none';
											document.getElementById('tabla-medidas-container').style.display = 'block';
											datosMedidasCargados = true;
											// Inicializar DataTable si está disponible
											if (typeof $.fn.DataTable !== 'undefined') {
												$('#tabla_medidas').DataTable({
													pageLength: 10,
													language: {
														url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
													}
												});
											}
										} else {
											document.getElementById('loader-medidas').innerHTML =
												'<p class="text-danger">Error al cargar los datos</p>';
										}
									})
									.catch(error => {
										console.error('Error:', error);
										document.getElementById('loader-medidas').innerHTML =
											'<p class="text-danger">Error al cargar los datos</p>';
									});
							}
						</script>


						<!-- PESTAÑA 8 MEDIDAS -->

						<div class="tab-pane fade pt-2" id="datos-medidas">
							<!-- Loader -->
							<div id="loader-medidas" class="text-center" style="padding: 40px;">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Cargando...</span>
								</div>
								<p class="mt-3">Cargando datos de medidas...</p>
							</div>

							<!-- Tabla (inicialmente oculta) -->
							<div id="tabla-medidas-container" style="display: none;">
								<table class="table" id="tabla_medidas">
									<thead>
										<tr>
											<div class="col-12">
												<th class="col-2"><?php echo $lang['alta']; ?></th>
												<th class="col-2"><?php echo $lang['baja']; ?></th>
												<th class="col-2"><?php echo $lang['status']; ?></th>
												<th class="col-3"><?php echo $lang['medida']; ?></th>
												<th class="col-3"><?php echo $lang['motivo']; ?></th>
											</div>
										</tr>
									</thead>
									<tbody id="tbody-medidas">
										<!-- Los datos se cargarán dinámicamente -->
									</tbody>
								</table>
							</div>
						</div>

						<script>
							// Cargar datos de asignación y medidas de forma dinámica
							let datosAsignacionCargados = false;

							// Función para cargar datos de asignación
							function cargarDatosAsignacion() {
								if (datosAsignacionCargados || !trabajadorId) return;

								fetch(`auto.php?datos_asig=1&id=${trabajadorId}`)
									.then(response => response.json())
									.then(result => {
										if (result.success) {
											const tbody = document.getElementById('tbody-asignacion');
											tbody.innerHTML = '';

											result.data.forEach(row => {
												const tr = document.createElement('tr');
												tr.innerHTML = `
												<td>${row.BEGDA}</td>
												<td>${row.ENDDA}</td>
												<td>${row.PLANS ?? ''}</td>
												<td>${row.STEXT_PLANS ?? ''}</td>
												<td>${row.ALMACEN ?? ''}</td>
												<td>${row.FINCA ?? ''}</td>
												<td>${row.CENTRO ?? ''}</td>
												<td>${row.DIVISION ?? ''}</td>
												<td>${row.NFC ?? ''}</td>
											`;
												tbody.appendChild(tr);
											});

											// Ocultar loader y mostrar tabla
											document.getElementById('loader-asignacion').style.display = 'none';
											document.getElementById('tabla-asignacion-container').style.display = 'block';
											datosAsignacionCargados = true;
											// Inicializar DataTable si está disponible
											if (typeof $.fn.DataTable !== 'undefined') {
												$('#tabla_asig').DataTable({
													pageLength: 10,
													language: {
														url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
													}
												});
											}
										} else {
											document.getElementById('loader-asignacion').innerHTML =
												'<p class="text-danger">Error al cargar los datos</p>';
										}
									})
									.catch(error => {
										console.error('Error:', error);
										document.getElementById('loader-asignacion').innerHTML =
											'<p class="text-danger">Error al cargar los datos</p>';
									});
							}
						</script>


						<!-- PESTAÑA 9 ALERTAS -->

						<div class="tab-pane <?php if (isset($_GET['alertas'])) {echo 'show active';} else {echo '';} ?> pt-2" id="alertas">
							<h5
								style="padding: 5px 0 10px 0; font-size: 18px; font-weight: 500; color: #012970; font-family: Poppins, sans-serif;">
								Alertas del trabajador</h5>

							<!-- Botón para abrir modal -->
							<button type="button" class="btn btn-primary mb-4" data-bs-toggle="modal"
								data-bs-target="#verticalycentered">
								Nueva Alerta
							</button>

							<!-- Modal -->
							<div class="modal fade" id="verticalycentered" tabindex="-1" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered" style="max-width: 900px;">
									<div class="modal-content">
										<!-- Modal Header -->
										<div class="modal-header">
											<h5 class="modal-title">Nueva Alerta</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal"
												aria-label="Close"></button>
										</div>

										<!-- Modal Body -->
										<div class="modal-body">

											<form
												action="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo htmlspecialchars($_GET['id']); ?>&nuevaalerta"
												method="post" id="userForm">
												<div class="card mb-4">
													<div class="card-header bg-primary text-white">
														Datos del Empleado
													</div>
													<input type="hidden" name="alerta" value="1">
													<div class="card-body mt-2">
														<div class="row">
															<div class="col-md-6 mb-2">
																<label for="employeeId" class="form-label">ID
																	Empleado*</label>
																<input type="text" class="form-control"
																	style="background-color: #ebebeb;" name="employeeId"
																	id="employeeId"
																	value="<?php echo $params['info_trabajador']['PERNR']; ?>"
																	readonly>
															</div>
															<div class="col-md-6 mb-2">
																<label for="employeeName" class="form-label">Nombre
																	Completo*</label>
																<input type="text" class="form-control"
																	style="background-color: #ebebeb;"
																	name="employeeName" id="employeeName"
																	value="<?php echo $nombreCompleto; ?>" readonly>
															</div>
														</div>
													</div>
												</div>

												<div class="mb-4">
													<label for="alertType" class="form-label fw-bold">Tipo de
														Alerta*</label>
													<select class="form-select" name="alertType" id="alertType"
														required>
														<option value="">Seleccione el tipo de alerta</option>
														<option value="Disciplina">Alerta de Disciplina</option>
														<option value="Asistencia">Alerta de Asistencia</option>
														<option value="Contrato">Alerta de Renovación de Contrato
														</option>
														<option value="Formacion">Alerta de Formación Pendiente</option>
														<option value="Evaluacion">Alerta de Evaluación de Desempeño
														</option>
														<option value="Seguridad">Alerta de Salud y Seguridad</option>
														<option value="Medico">Alerta de Seguimiento Médico</option>
														<option value="Salario">Alerta de Revisión Salarial</option>
													</select>
												</div>

												<div class="card mb-4" id="detallesAlerta">
													<div class="card-header bg-primary text-white">
														Detalles de la Alerta
													</div>
													<div class="card-body mt-2">
														<!-- Campos comunes -->
														<div class="mb-2">
															<label for="description"
																class="form-label">Descripción*</label>
															<textarea class="form-control" name="description"
																id="description" rows="3" required></textarea>
														</div>

														<div class="row">
															<div class="col-md-6 mb-2">
																<label for="startDate" class="form-label">Fecha de
																	Inicio*</label>
																<input type="date" class="form-control" name="startDate"
																	id="startDate" required>
															</div>
															<div class="col-md-6 mb-2">
																<label for="endDate" class="form-label">Fecha de
																	Vencimiento*</label>
																<input type="date" class="form-control" name="endDate"
																	id="endDate" required>
															</div>
														</div>

														<!-- Campos específicos por tipo de alerta -->
														<div id="specificFields">

															<!-- Formación -->
															<div class="specific-field" data-type="Formacion">
																<div class="row">
																	<div class="col-md-6 mb-2">
																		<label for="trainingType"
																			class="form-label">Tipo de Formación</label>
																		<input type="text" class="form-control"
																			name="trainingType" id="trainingType">
																	</div>
																	<div class="col-md-6 mb-2">
																		<label for="mandatory" class="form-label">¿Es
																			obligatoria?</label>
																		<select class="form-select" name="mandatory"
																			id="mandatory">
																			<option value="si">Sí</option>
																			<option value="no">No</option>
																		</select>
																	</div>
																</div>
															</div>

															<!-- Salud y Seguridad -->
															<div class="specific-field" data-type="Seguridad">
																<div class="row">
																	<div class="col-md-6 mb-2">
																		<label for="incidentType"
																			class="form-label">Tipo de Incidente</label>
																		<select class="form-select" name="incidentType"
																			id="incidentType">
																			<option value="embarazo">Embarazo</option>
																			<option value="epi">Actualización EPI
																			</option>
																			<option value="accidente">Accidente Laboral
																			</option>
																		</select>
																	</div>
																</div>
															</div>
															<div class="col-md-6 mb-2">
																<label for="priority"
																	class="form-label">Prioridad</label>
																<select class="form-select" name="priority"
																	id="priority" required>
																	<option value=""></option>
																	<option value="alta">Alta</option>
																	<option value="media">Media</option>
																	<option value="baja">Baja</option>
																</select>
															</div>
														</div>
													</div>
												</div>

												<!-- Notificaciones -->
												<div class="card mb-4" id="configuracionNotificaciones">
													<div class="card-header bg-primary text-white">
														Configuración de Notificaciones
													</div>
													<div class="card-body mt-2">
														<div class="row">
															<div class="col-md-6 mb-2">
																<label for="notifyTo" class="form-label">Notificar
																	a*</label>
																<input type="email" class="form-control" name="notifyTo"
																	id="notifyTo" required>
															</div>
															<div class="col-md-6 mb-2">
																<label for="frequency" class="form-label">Frecuencia de
																	Notificación</label>
																<select class="form-select" name="frequency"
																	id="frequency">
																	<option value="unica">Única vez</option>
																	<option value="diaria">Diaria</option>
																	<option value="semanal">Semanal</option>
																	<option value="mensual">Mensual</option>
																</select>
															</div>
														</div>
													</div>
												</div>
										</div>

										<!-- Modal Footer -->
										<div class="modal-footer">
											<button type="button" class="btn btn-secondary"
												data-bs-dismiss="modal">Cerrar</button>
											<button type="submit" form="userForm"
												class="btn btn-primary">Guardar</button>
										</div>
										</form>

										<script>
											document.addEventListener('DOMContentLoaded', function () {
												// Obtener referencias a las secciones principales
												const detallesAlertaCard = document.querySelector('#detallesAlerta'); // Añade este ID a la card de Detalles
												const notificacionesCard = document.querySelector('#configuracionNotificaciones'); // Añade este ID a la card de Notificaciones

												// Inicialmente ocultar las secciones
												detallesAlertaCard.style.display = 'none';
												notificacionesCard.style.display = 'none';

												// Manejar cambio en tipo de alerta
												document.getElementById('alertType').addEventListener('change', function () {
													// Mostrar/ocultar sección de detalles según si hay selección
													detallesAlertaCard.style.display = this.value ? 'block' : 'none';

													// Ocultar sección de notificaciones cuando cambia el tipo
													notificacionesCard.style.display = 'none';

													// Limpiar campos cuando se cambia el tipo de alerta
													document.getElementById('description').value = '';
													document.getElementById('startDate').value = '';
													document.getElementById('endDate').value = '';

													// Mostrar/ocultar campos específicos
													const specificFields = document.querySelectorAll('.specific-field');
													specificFields.forEach(field => {
														field.style.display = 'none';
														// Limpiar campos específicos
														field.querySelectorAll('input, select').forEach(input => {
															input.value = '';
														});
													});

													const relevantField = document.querySelector(`.specific-field[data-type="${this.value}"]`);
													if (relevantField) {
														relevantField.style.display = 'block';
													}
												});

												// Función para verificar si todos los campos requeridos de la sección detalles están completos
												function checkRequiredFields() {
													// Verificar campos comunes
													const description = document.getElementById('description').value;
													const startDate = document.getElementById('startDate').value;
													const endDate = document.getElementById('endDate').value;

													if (!description || !startDate || !endDate) {
														return false;
													}

													// Verificar campos específicos según el tipo de alerta seleccionado
													const selectedType = document.getElementById('alertType').value;
													const specificField = document.querySelector(`.specific-field[data-type="${selectedType}"]`);

													if (specificField) {
														const specificInputs = specificField.querySelectorAll('input, select');
														for (let input of specificInputs) {
															if (!input.value) {
																return false;
															}
														}
													}

													return true;
												}

												// Monitorear cambios en los campos de detalles
												detallesAlertaCard.addEventListener('change', function (e) {
													if (checkRequiredFields()) {
														notificacionesCard.style.display = 'block';
													} else {
														notificacionesCard.style.display = 'none';
													}
												});

												// También monitorear la entrada de texto en tiempo real
												detallesAlertaCard.addEventListener('input', function (e) {
													if (checkRequiredFields()) {
														notificacionesCard.style.display = 'block';
													} else {
														notificacionesCard.style.display = 'none';
													}
												});

												// Validación del formulario al enviar
												document.getElementById('userForm').addEventListener('submit', function (event) {
													if (!checkRequiredFields()) {
														event.preventDefault();
														alert('Por favor, complete todos los campos en la sección de detalles');
														return;
													}

													// Validar campos de notificaciones
													const notifyTo = document.getElementById('notifyTo').value;
													const frequency = document.getElementById('frequency').value;

													if (!notifyTo || !frequency) {
														event.preventDefault();
														alert('Por favor, complete todos los campos de notificaciones');
														return;
													}

													// Si todo está correcto, el formulario se enviará
													return true;

													// Aquí iría el código para enviar el formulario
													console.log('Formulario enviado correctamente');

													// Opcional: cerrar el modal después del envío exitoso
													const modal = bootstrap.Modal.getInstance(document.getElementById('verticalycentered'));
													modal.hide();
												});
											});
										</script>
									</div>
								</div>


							</div>

							<h5
								style="margin-top: 30px; padding: 5px 0 10px 0; font-size: 18px; font-weight: 500; color: #012970; font-family: Poppins, sans-serif;">
								Registros</h5>

							<table class="table datatable">
								<thead>
									<tr>
										<div class="col-12">
											<th class="col-2">Tipo Alerta</th>
											<th class="col-4">Descripción</th>
											<th class="col-2">Fecha Inicio</th>
											<th class="col-2">Fecha Fin</th>
											<th class="col-1">Prioridad</th>
										</div>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach ($params['alertas_trabajador'] as $resultado) {
										?>
										<tr>
											<td>
												<?php
												if ($resultado['tipo_alerta'] == 'Disciplina') {
													echo "<i class='ri-ruler-line'></i>";
												} elseif ($resultado['tipo_alerta'] == 'Asistencia') {
													echo "<i class='bi bi-person-badge'></i>";
												} elseif ($resultado['tipo_alerta'] == 'Contrato') {
													echo "<i class='ri-file-settings-line'></i>";
												} elseif ($resultado['tipo_alerta'] == 'Formacion') {
													echo "<i class='ri-psychotherapy-line'></i>";
												} elseif ($resultado['tipo_alerta'] == 'Evaluacion') {
													echo "<i class='ri-draft-line'></i>";
												} elseif ($resultado['tipo_alerta'] == 'Seguridad') {
													echo "<i class='ri-shield-check-line'></i>";
												} elseif ($resultado['tipo_alerta'] == 'Medico') {
													echo "<i class='bi bi-file-earmark-medical'></i>";
												} elseif ($resultado['tipo_alerta'] == 'Salario') {
													echo "<i class='ri-money-euro-circle-line'></i>";
												}
												echo " " . $resultado['tipo_alerta'];
												?>
											</td>
											<td>
												<?php
												if ($resultado['tipo_alerta'] == 'Seguridad') {
													echo $resultado['descripcion'] . " (" . $resultado['tipo_incidente'] . ") ";
												} elseif ($resultado['tipo_alerta'] == 'Formacion') {
													echo $resultado['descripcion'] . " (" . $resultado['tipo_formacion'] .
														($resultado['obligatoria'] == 'si' ? ' | Obligatoria' : '') . ")";
												} else {
													echo $resultado['descripcion'];
												}
												?>
											</td>
											<td>
												<?php echo $resultado['fecha_ini']->format('Y-m-d'); ?>
											</td>
											<td>
												<?php echo $resultado['fecha_fin']->format('Y-m-d'); ?>
											</td>
											<td>
												<?php echo $resultado['prioridad']; ?>
											</td>
										</tr>
										<?php
									}
									?>
								</tbody>
							</table>



						</div>


						<!-- PESTAÑA 10 LLAMAMIENTOS -->

						<div class="tab-pane fade <?php echo isset($_GET['showll']) ? 'show active' : ''; ?> pt-6"
							id="llamamientos">
							<br>
							<?php
							if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
								?>

								<?php
								// Inicializar las variables
								$id_remesa = isset($id_remesa) ? $id_remesa : '';
								$ano_remesa = isset($ano_remesa) ? $ano_remesa : '';
								if ($_SERVER['REQUEST_METHOD'] == 'POST' || isset($_GET['remesa'])) {
									if (isset($_POST["datos_remesa"]) && $_POST['datos_remesa'] == "1" || isset($_GET['remesa'])) {
										$datosEncontrados = true;
										foreach ($params['datos_contacto'] as $value) {
											if ($value['MOVIL'] != "" || $value['CORREO'] != "" || $value['TELEMERGENCIAS'] != "" || $value['TELEMPRESA'] != "") {
												$datosEncontrados = false;
												break;
											}
										}
										if ($datosEncontrados) {
											echo $lang['0_contactos'];
										}
										?>
										<div class="row">
											<div class="col-md-2"></div>
											<?php


											$TelefonoBtn = false;
											foreach ($params['datos_contacto'] as $value) {
												if ($value['MOVIL'] != "" || $value['TELEMPRESA'] != "" || $value['TELEMERGENCIAS'] != "") {
													$TelefonoBtn = true;
													break;
												}
											}
											if ($TelefonoBtn) {
												if (!empty($id_remesa) && !empty($ano_remesa) || isset($_GET['remesa'])) {
													$fecha_actual = date('Y-m-d\TH:i');
													$fecha_actual2 = date('Y-m-d H:i:s');
													echo "
													<div class='col-md-3 align-c'>
														<label class='form-label mt_div_llama' style='text-align: center;'><b>" . $lang['llamada'] . "</b></label><br>
																									
														<a data-bs-toggle='modal' data-bs-target='#llamada_modal'>
															<img src='img/llamada.png' class='icono_llamamientos'>
														</a>

														<div class='modal fade' id='llamada_modal' tabindex='-1' style='display: none;' aria-hidden='true'>
															<div class='modal-dialog modal-dialog-centered'>
																<div class='modal-content' style='width: 80%; margin-left: 10%;'>
																	<div class='modal-header'>
																		<h5 class='modal-title'>" . $lang['llamada'] . "</h5>
																		<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
																	</div>
																<div class='modal-body'>
																	<div class='row'>
																		<form method='POST' enctype='multipart/form-data' action='admin_cont.php?controller=index&action=update_trabajador&id=" . $pernr . "&llamamiento&showll&id_remesa=" . $id_remesa . "&ano_remesa=" . $ano_remesa . "' id='form_llamamiento_telefono'>

																			<input type='hidden' name='Tipo_llamamiento' value='Telefono'>
																			<input type='hidden' name='pernr' value='" . $pernr . "'>
																			<input type='hidden' name='contacto' value='" . $value['MOVIL'] . "'>
																			<input type='hidden' name='id_remesa' value='" . $id_remesa . "'>
																			<input type='hidden' name='ano_remesa' value='" . $ano_remesa . "'>
																			<input type='hidden' name='prefijo' value='" . $value['PRE_TELF'] . "'>

																			<div class='col-md-9' style='text-align: left;'>
																				<label>" . $lang['fecha_llamada'] . ":</label><br>
																				<input type='datetime-local' name='fecha_llamamiento' style='width: 100%;' class='form-date' max='" . $fecha_actual . "' value='" . $fecha_actual . "' required>
																			</div>

																			<div class='col-md-12' style='text-align: left; margin-top: 15px;'>
																				<label>" . $lang['estado'] . ":</label><br>
																				<select name='estado' id='estado' style='width: 100%;' required>
																					<option value='' selected>" . $lang['estado'] . "</option>
																					<option value='1'>" . $lang['aceptado'] . "</option>
																					<option value='2'>" . $lang['rechazado'] . "</option>
																					<option id='pendi' value='3'>" . $lang['pendiente'] . "</option>
																				</select>
																			</div>
																			<div id='error-estado' style='color: red; display: none; margin-top: 5px;'>
																				" . $lang['select_estado'] . "
																			</div>

																			
																			<div class='col-md-12' style='text-align: left; margin-top: 15px;'>
																				<label>" . $lang['motivo'] . ":</label><br>
																				<select name='motivo' id='motivo' style='width: 100%;' required>
																					<option value=''>Seleccione un motivo</option>";
													foreach ($params['motivos_pendiente'] as $motivo) {
														echo '<option value="' . htmlspecialchars($motivo['id_motivo']) . '">' .
															htmlspecialchars($motivo['desc_motivo']) . '</option>';
													}
													;

													echo "</select>
																					</div>
																					<div id='error-motivo' style='color: red; display: none; margin-top: 5px;'>
																						" . $lang['select_motivo'] . "
																					</div>
																					
																					<div class='col-md-12' id='descripcion-container' style='text-align: left; margin-top: 0px;'>
																						<label>" . $lang['desc'] . ":</label><br>
																						<textarea name='descripcion' id='descripcion' style='width: 100%;' rows='3' class='form-control'></textarea>
																					</div>
																					<div class='clear'></div>

																					<div class='col-md-12' id='justificante-container' style='text-align: left; margin-top: 15px;'>
																						<label>Justificante (opcional):</label><br>
																						<input type='file' name='justificante' id='justificante' class='form-control'>
																					</div>
																							
																					<script>
																						function validarFormulario2() {
																							var estado = document.getElementById('estado').value;
																							var motivo = document.getElementById('motivo').value;
																							var descripcion = document.getElementById('descripcion').value;
																							var nombreyapellidos = '$nombreyapellidos';
																							var errorEstado = document.getElementById('error-estado');
																							var errorMotivo = document.getElementById('error-motivo');
																							var formularioValido = true;

																							// Validar estado
																							if (estado === '') {
																								errorEstado.style.display = 'block';
																								formularioValido = false;
																							} else {
																								errorEstado.style.display = 'none';
																							}

																							if ((estado === '2' || estado === '3') && motivo === '') {
																								errorMotivo.style.display = 'block';
																								formularioValido = false;
																							} else {
																								errorMotivo.style.display = 'none';
																							}

																							if (formularioValido) {
																								alertify.confirm('¿' + '" . $lang["realizar_llama"] . "' + ' ' + '" . $nombreyapellidos . "' + '?', 
																									function() {
																										document.getElementById('form_llamamiento_telefono').submit();
																									},
																									function() {
																										return false;
																									}
																								);
																							}
																							return false;
																						}

																						// Evento para el cambio de estado
																						document.addEventListener('DOMContentLoaded', function() {
																							var estadoSelect = document.getElementById('estado');
																							var motivoContainer = document.getElementById('motivo').parentElement;
																							var descripcionContainer = document.getElementById('descripcion-container');
																							var descripcionField = document.getElementById('descripcion');
																							var errorMotivo = document.getElementById('error-motivo');

																							// Ocultar el campo de motivo por defecto
																							motivoContainer.style.display = 'none';

																							estadoSelect.addEventListener('change', function() {
																								if (this.value === '2' || this.value === '3') {
																									motivoContainer.style.display = 'block';
																									document.getElementById('motivo').required = true;
																								} else {
																									motivoContainer.style.display = 'none';
																									document.getElementById('motivo').value = '';
																									document.getElementById('motivo').required = false;
																									errorMotivo.style.display = 'none';
																								}
																							});
																						});
																					</script>
																				</div>
																			</div>
																			<div class='modal-footer'>
																					<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" . $lang['cerrar'] . "</button>
																					<button type='button' onclick='validarFormulario2()' class='btn btn-primary'>
																						<span>" . $lang['enviar'] . "</span>
																					</button>
																				</form>
																			</div>
																		</div>
																	</div>
																</div>
															</div>
															";
												} else {
													echo "ERROR: update_trabajador.php";
												}
											}









											//! CORREO
											$MailBtn = false;
											foreach ($params['datos_contacto'] as $value) {
												if ($value['CORREO'] != "") {
													$MailBtn = true;
													break;
												}
											}

											if ($MailBtn) {
												if (!empty($id_remesa) && !empty($ano_remesa) || isset($_GET['remesa'])) {
													$fecha_actual3 = date('Y-m-d H:i:s');
													echo "
													<div class='col-md-3 align-c'>
														<label class='form-label mt_div_llama' style='text-align: center;'><b>" . $lang['correo'] . "</b></label><br>
																								
														<a data-bs-toggle='modal' data-bs-target='#correo_modal'>
															<img src='img/correo.png' class='icono_llamamientos'>
														</a>

														<div class='modal fade' id='correo_modal' tabindex='-1' style='display: none;' aria-hidden='true'>
															<div class='modal-dialog modal-dialog-centered'>
																<div class='modal-content'>
																	<div class='modal-header'>
																		<h5 class='modal-title'>" . $lang['enviar'] . " " . $lang['correo'] . "</h5>
																		<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
																	</div>

																	<div class='modal-body'>
																		<div class='row'>
																			<form method='POST' action='admin_cont.php?controller=index&action=update_trabajador&id=" . $pernr . "&llamamiento&showll&id_remesa=" . $id_remesa . "&ano_remesa=" . $ano_remesa . "' id='form_llamamiento_correo'>
																			<input type='hidden' name='Tipo_llamamiento' value='Correo'>
																			<input type='hidden' name='pernr' value=" . $pernr . ">
																			<input type='hidden' name='nombre' value='" . $nombreyapellidos . "'>
																			<input type='hidden' name='contacto' value=" . $email . ">
																			<input type='hidden' name='fecha_registro' value=" . $fecha_actual3 . ">
																			<input type='hidden' name='id_remesa' value=" . $id_remesa . ">
																			<input type='hidden' name='ano_remesa' value=" . $ano_remesa . ">
																			<input type='hidden' name='correo_usu_web' value=" . $_SESSION["correo_user_surexport_appreclu"] . ">

																			<div class='col-md-12' style='text-align: left; width: 300px;'>
																				<label for='asunto_mail'>" . $lang['asunto'] . ":</label>
																				<input type='text' id='asunto_mail' name='asunto_mail' value='Llamamiento Surexport S.L.' class='form-control mt-1' readonly>
																			</div>

																			<script type='text/javascript'>
																				// Función para actualizar el texto del mensaje en la previsualización
																				function liveComment_text(texto) {
																					document.getElementById('preview_text').innerHTML = texto;
																				}

																				// Inicialización de los valores en la previsualización al cargar la página
																				window.onload = function () {
																					const defaultMessage = document.getElementById('mensaje_pre').value;
																					liveComment_text(defaultMessage);
																				};
																			</script>

																			<br>
																			<div id='contenedor'>
																				<label for='mensaje'>Su mensaje</label>
																				<textarea class='form-control mt-1' id='mensaje_pre' name='mensaje_mail' onkeyup='liveComment_text(this.value)' required></textarea>
																				<br>

																				<div id='previsualizacion' class='flotando'>
																					<fieldset>
																						<h5 class='modal-title'>Previsualización de mensaje:</h5><br>
																						<h4>Llamamiento Surexport S.L.</h4>
																						<p>" . $nombreyapellidos . ", Surexport le comunica su llamamiento para su puesto de trabajo.</p>
																						<p id='preview_text'></p>
																						<p>Accede al siguiente Link para mas info: <br>
																							<a href='https://aplicaciones.surexport.es:1110/portal_rrhh/cartas/llama1_base.htm' Target='_blank'>Link</a>
																						</p>
																						<img src='https://surexport.es/es/wp-content/themes/SurExport/images/logo-home.png' alt='Logotipo Surexport' style='width: 250px; height: auto;'>
																					</fieldset>
																				</div>
																			</div>
																		</div>
																	</div>
																	
																	<div class='modal-footer'>
																		<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" . $lang['cerrar'] . "</button>
																		<button type='button' 
																			onclick=\"
																			if (validateForm()) {
																				alertify.confirm('¿" . $lang['realizar_llama'] . " " . $nombreyapellidos . "?', 
																				function() { 
																					document.getElementById('form_llamamiento_correo').submit(); 
																				}); 
																			}\" 
																			class='btn btn-primary'>
																			<span>" . $lang['enviar'] . "</span>
																		</button>
																	</div>
																</form>
																<script>
																	function validateForm() {
																		const mensaje = document.getElementById('mensaje_pre').value.trim();
																		if (!mensaje) {
																			alertify.error('Por favor, completa el mensaje antes de enviar.');
																			return false; // Evita el envío del formulario
																		}
																		return true; // Permite el envío
																	}
																</script>
															</div>
														</div>
													</div>
													";
												} else {
													echo "ERROR: update_trabajador.php";
												}
											}
											?>

											<div class="col-md-1"></div>
										</div>
										<div class="clear"><br></div>

										<div id="load_form_llam" style="display: none;"></div>
										<br><br>
										<?php
									}
								} else {
									echo $lang['remesa_rem'] . "<br><br>";
								}
							}
							?>






							<h5
								style="font-size: 18px; font-weight: 500; color: #012970; font-family: Poppins, sans-serif;">
								<?php echo $lang['registros_llama']; ?>
							</h5>

							<?php
							// Determinar qué columnas se muestran
							$haydescripcion = false;
							if (!empty($params['datos_llamamiento'])) {
								foreach ($params['datos_llamamiento'] as $resultado) {
									if (isset($resultado['DESCRIPCION']) && $resultado['DESCRIPCION'] !== null && $resultado['DESCRIPCION'] !== '') {
										$haydescripcion = true;
										break;
									}
								}
							}

							$hayJustificante = false;
							if (!empty($params['datos_llamamiento'])) {
								foreach ($params['datos_llamamiento'] as $resultado) {
									if (!empty($resultado['JUSTIFICANTE']) && file_exists($resultado['JUSTIFICANTE'])) {
										$hayJustificante = true;
										break;
									}
								}
							}

							// Determinar si hay registros con modales de respuesta disponibles
							$hayRespuesta = false;
							if (!empty($params['datos_llamamiento'])) {
								foreach ($params['datos_llamamiento'] as $resultado) {
									$current_date = new DateTime("now", new DateTimeZone('Europe/Madrid'));
									$registro_date = ($resultado['FECHA_REGISTRO'] instanceof DateTime)
										? $resultado['FECHA_REGISTRO']
										: new DateTime($resultado['FECHA_REGISTRO'], new DateTimeZone('Europe/Madrid'));
									$diff_seconds = $current_date->getTimestamp() - $registro_date->getTimestamp();
									$diff_hours = $diff_seconds / 3600;

									// Verificar si el registro tiene opciones de respuesta disponibles
									$sinRespuesta15dias = ($diff_hours > 360 && $resultado['ESTADO'] == "0" && $resultado['NUM_ENVIO'] == "1")
										|| ($diff_hours > 360 && $resultado['ESTADO'] == "3" && $resultado['NUM_ENVIO'] == "1");
									$sinRespuesta5dias = ($diff_hours > 120 && $resultado['ESTADO'] == "0" && $resultado['NUM_ENVIO'] == "2")
										|| ($diff_hours > 120 && $resultado['ESTADO'] == "3" && $resultado['NUM_ENVIO'] == "2");

									// Si tiene modales disponibles (estado 0 o 3, sin relaciones, y no ha pasado el tiempo límite)
									if (
										(($resultado['ESTADO'] == "0" || $resultado['ESTADO'] == "3") && $resultado['NUM_RELACIONES'] == "0")
										&& !$sinRespuesta15dias && !$sinRespuesta5dias
									) {
										$hayRespuesta = true;
										break;
									}
								}
							}

							$esSupervisor = ($_SESSION["tipo_user_surexport_appreclu"] == 'Supervisor');
							?>

							<table class="table datatable">
								<thead>
									<tr>
										<th>Fecha llamamiento</th>
										<th><?php echo $lang['tipo_llama']; ?></th>
										<th><?php echo $lang['estado']; ?></th>
										<?php
										if ($haydescripcion) {
											echo '<th>Descripción</th>';
										}
										?>
										<th><?php echo $lang['persona_cont']; ?></th>
										<?php
										if (!$esSupervisor && $hayRespuesta) {
											?>
											<th><?php echo $lang['respuesta']; ?></th>
											<?php
										}
										?>

										<?php
										if ($hayJustificante) {
											echo '<th>Justificante</th>';
										}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
									if (!empty($params['datos_llamamiento'])) {
										foreach ($params['datos_llamamiento'] as $resultado) {
											?>
											<tr>
												<td>
													<?php
													if ($resultado['TIPO_LLAMAMIENTO'] == 'Telefono') {
														$fecha = $resultado['FECHA_LLAMAMIENTO'] ?? $resultado['FECHA_REGISTRO'];
														echo date_format($fecha, 'Y-m-d H:i');
													} else {
														echo date_format($resultado['FECHA_REGISTRO'], 'Y-m-d H:i');
													}
													?>
												</td>
												<td>
													<?php echo $resultado['TIPO_LLAMAMIENTO']; ?>

													<!-- mostrar solo si el tipo de llamamiento es SMS -->
													<?php if ($resultado['TIPO_LLAMAMIENTO'] == 'SMS') { ?>
														<button type="button" class="btn"
															onclick="toggleAccordion('<?php echo $resultado['ID']; ?>')">
															(Ver SMS)
														</button>
														<div id="accordion_<?php echo $resultado['ID']; ?>"
															style="display: none; margin-top: 10px; padding: 10px;">
															<?php
															if (!empty($resultado['MSG_ENVIO'])) {
																$mensaje = $resultado['MSG_ENVIO'];

																// Buscar la posición de "MAS INFO" o "MÁS INFO"
																$pos = stripos($mensaje, 'MAS INFO');
																$pos2 = stripos($mensaje, 'MÁS INFO');

																// Determinar cuál posición usar (la que exista y sea más baja)
																if ($pos !== false && $pos2 !== false) {
																	$posicion = min($pos, $pos2);
																} elseif ($pos !== false) {
																	$posicion = $pos;
																} elseif ($pos2 !== false) {
																	$posicion = $pos2;
																} else {
																	$posicion = false;
																}

																// Cortar si encontramos alguna coincidencia
																if ($posicion !== false) {
																	$mensaje = substr($mensaje, 0, $posicion);
																}

																echo trim($mensaje);
															} else {
																echo "No hay mensaje disponible.";
															}
															?>
														</div>

														<script>
															function toggleAccordion(id) {
																var acc = document.getElementById("accordion_" + id);
																acc.style.display = acc.style.display === "none" ? "block" : "none";
															}
														</script>
													<?php } ?>

												</td>
												<td>
													<?php
													if ($resultado['ESTADO'] == 0) {
														echo $lang['enviado'];
													} elseif ($resultado['ESTADO'] == 1) {
														echo $lang['aceptado'];
													} elseif ($resultado['ESTADO'] == 2) {
														echo $lang['rechazado'] . ' (' . $resultado['desc_motivo'] . ')';
													} else { // Estado 3
														echo $lang['pendiente'] . ' (' . $resultado['desc_motivo'] . ')';
													}
													?>
												</td>
												<?php
												if ($haydescripcion) {
													echo '<td>';
													if (!empty($resultado['DESCRIPCION'])) {
														echo $resultado['DESCRIPCION'];
													} else {
														echo '';
													}
													echo '</td>';
												}
												?>
												<td>
													<?php echo $resultado['NOMBRE_USUARIO']; ?>
												</td>
												<?php
												if (!$esSupervisor && $hayRespuesta) {
													?>
													<td>
														<?php
														$id_registro = $resultado['ID'];
														$fecha = date('Ymd H:i:s');
														$nombreTrabajador = isset($params['info_trabajador']['NOMBREYAPELLIDOS']) ? $params['info_trabajador']['NOMBREYAPELLIDOS'] : '';
														$pernr = isset($params['info_trabajador']['PERNR']) ? $params['info_trabajador']['PERNR'] : '';
														// $id_remesa = $id_remesa;
														// $ano_remesa = $ano_remesa;
											
														$current_date = new DateTime("now", new DateTimeZone('Europe/Madrid'));

														$registro_date = ($resultado['FECHA_REGISTRO'] instanceof DateTime)
															? $resultado['FECHA_REGISTRO']
															: new DateTime($resultado['FECHA_REGISTRO'], new DateTimeZone('Europe/Madrid'));

														$diff_seconds = $current_date->getTimestamp() - $registro_date->getTimestamp();
														$diff_hours = $diff_seconds / 3600;

														if (
															($diff_hours > 360 && $resultado['ESTADO'] == "0" && $resultado['NUM_ENVIO'] == "1")
															|| ($diff_hours > 360 && $resultado['ESTADO'] == "3" && $resultado['NUM_ENVIO'] == "1")
														) {
															echo "Sin respuesta 15 días";
														} elseif (
															($diff_hours > 120 && $resultado['ESTADO'] == "0" && $resultado['NUM_ENVIO'] == "2")
															|| ($diff_hours > 120 && $resultado['ESTADO'] == "3" && $resultado['NUM_ENVIO'] == "2")
														) {
															echo "Sin respuesta 5 días";
														} else {
															if ($resultado['ESTADO'] == "0" && $resultado['NUM_RELACIONES'] == "0") {
																echo "
																		<ul class='icon-list'>
																			<li>
																				<div class='col-md-3 align-c'>
																					
																					<a data-bs-toggle='modal' data-bs-target='#aceptar_modal_" . $resultado['ID'] . "'>
																						<i class='bi bi-check-circle aceptar'></i>
																					</a>

																					<div class='modal fade' id='aceptar_modal_" . $resultado['ID'] . "' tabindex='-1' style='display: none;' aria-hidden='true'>
																						<div class='modal-dialog modal-dialog-centered'>
																							<div class='modal-content'>
																								<div class='modal-header'>
																									<h5 class='modal-title'>" . $lang['aceptar'] . " " . $lang['llama'] . "</h5>
																									<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
																								</div>

																								<div class='modal-body'>
																									<div class='row'>
																										<form method='POST' enctype='multipart/form-data' action='admin_cont.php?controller=index&action=update_trabajador&id=" . $pernr . "&showll&remesa&respuesta&id_remesa=" . $id_remesa . "&ano_remesa=" . $ano_remesa . "' id='form_respuesta_acep_" . $resultado['ID'] . "'>
																											<input type='hidden' name='Tipo_respuesta' value='Aceptado'>
																											<input type='hidden' name='id_registro' value='" . $resultado['ID'] . "'>
																											<input type='hidden' name='id_remesa' value='" . $id_remesa . "'>
																											<input type='hidden' name='ano_remesa' value='" . $ano_remesa . "'>
																											<input type='hidden' name='pernr' value='" . $pernr . "'>
																											
																											<div class='col-md-12' style='text-align: left; margin-top: 0px;'>
																												<label>" . $lang['desc'] . ":</label><br>
																												<textarea name='descripcion' id='descripcion_acep_" . $resultado['ID'] . "' style='width: 100%;' rows='3' class='form-control'></textarea>
																											</div>

																											<div class='col-md-12' id='justificante-container' style='text-align: left; margin-top: 15px;'>
																												<label>Justificante (opcional):</label><br>
																												<input type='file' name='justificante' id='justificante' class='form-control'>
																											</div>

																										</form>
																									</div>
																								</div>

																								<div class='modal-footer'>
																									<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" . $lang['cerrar'] . "</button>
																									<button type='button' onclick='validarFormulario(\"acep\", \"" . $resultado['ID'] . "\")' class='btn btn-primary'>
																										<span>" . $lang['confirm'] . "</span>
																									</button>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</li>

																			<li>
																				<div class='col-md-3 align-c'>
																					
																					<a data-bs-toggle='modal' data-bs-target='#rechazar_modal_" . $resultado['ID'] . "'>
																						<i class='bi bi-x-circle rechazar'></i>
																					</a>

																					<div class='modal fade' id='rechazar_modal_" . $resultado['ID'] . "' tabindex='-1' style='display: none;' aria-hidden='true'>
																						<div class='modal-dialog modal-dialog-centered'>
																							<div class='modal-content'>
																								<div class='modal-header'>
																									<h5 class='modal-title'>" . $lang['rechazar'] . " " . $lang['llama'] . "</h5>
																									<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
																								</div>

																								<div class='modal-body'>
																									<div class='row'>
																										<form method='POST' enctype='multipart/form-data' action='admin_cont.php?controller=index&action=update_trabajador&id=" . $pernr . "&showll&remesa&respuesta&id_remesa=" . $id_remesa . "&ano_remesa=" . $ano_remesa . "' id='form_respuesta_rech_" . $resultado['ID'] . "'>
																											<input type='hidden' name='Tipo_respuesta' value='rechazar'>
																											<input type='hidden' name='id_registro' value='" . $resultado['ID'] . "'>
																											<input type='hidden' name='id_remesa' value='" . $id_remesa . "'>
																											<input type='hidden' name='ano_remesa' value='" . $ano_remesa . "'>
																											<input type='hidden' name='pernr' value='" . $pernr . "'>

																											<div class='col-md-9' style='text-align: left;'>
																												<label>" . $lang['select_moti_2'] . ":</label><br>
																												<select name='motivo' id='motivo_rech_" . $resultado['ID'] . "' style='width: 350px;' required>
																													<option value=''>Seleccione un motivo</option>";
																foreach ($params['motivos_pendiente'] as $motivo) {
																	echo '<option value="' . htmlspecialchars($motivo['id_motivo']) . '">' .
																		htmlspecialchars($motivo['desc_motivo']) . '</option>';
																}
																;

																echo "</select>
																											</div>
																											<div id='error-motivo_rech_" . $resultado['ID'] . "' style='color: red; display: none;'>
																												" . $lang['select_motivo'] . "
																											</div>

																											<div class='col-md-12' style='text-align: left; margin-top: 0px;'>
																												<label>" . $lang['desc'] . ":</label><br>
																												<textarea name='descripcion' id='descripcion_rech_" . $resultado['ID'] . "' style='width: 100%;' rows='3' class='form-control'></textarea>
																											</div>

																											<div class='col-md-12' id='justificante-container' style='text-align: left; margin-top: 15px;'>
																												<label>Justificante (opcional):</label><br>
																												<input type='file' name='justificante' id='justificante' class='form-control'>
																											</div>
																										</form>
																									</div>
																								</div>

																								<div class='modal-footer'>
																									<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" . $lang['cerrar'] . "</button>
																									<button type='button' onclick='validarFormulario(\"rech\", \"" . $resultado['ID'] . "\")' class='btn btn-primary'>
																										<span>" . $lang['confirm'] . "</span>
																									</button>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</li>";
																if ($resultado['TIPO_LLAMAMIENTO'] != 'SMS') {
																	echo "
																				<li>
																					<div class='col-md-3 align-c'>
																					
																					<a data-bs-toggle='modal' data-bs-target='#pendiente_modal_" . $resultado['ID'] . "'>
																						<i class='bi bi-question-circle pendiente'></i>
																					</a>

																					<div class='modal fade' id='pendiente_modal_" . $resultado['ID'] . "' tabindex='-1' style='display: none;' aria-hidden='true'>
																						<div class='modal-dialog modal-dialog-centered'>
																							<div class='modal-content'>
																								<div class='modal-header'>
																									<h5 class='modal-title'>" . $lang['llama'] . " " . $lang['pendiente'] . "</h5>
																									<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
																								</div>

																								<div class='modal-body'>
																									<div class='row'>
																										<form method='POST' enctype='multipart/form-data' action='admin_cont.php?controller=index&action=update_trabajador&id=" . $pernr . "&showll&remesa&respuesta&id_remesa=" . $id_remesa . "&ano_remesa=" . $ano_remesa . "' id='form_respuesta_pen_" . $resultado['ID'] . "'>
																											<input type='hidden' name='Tipo_respuesta' value='pendiente'>
																											<input type='hidden' name='id_registro' value='" . $resultado['ID'] . "'>
																											<input type='hidden' name='id_remesa' value='" . $id_remesa . "'>
																											<input type='hidden' name='ano_remesa' value='" . $ano_remesa . "'>
																											<input type='hidden' name='pernr' value='" . $pernr . "'>

																											<div class='col-md-12' style='text-align: left;'>
																												<label>" . $lang['select_moti_2'] . ":</label><br>
																												<select name='motivo' id='motivo_pen_" . $resultado['ID'] . "' style='width: 100%;' required>

																													<option value=''>Seleccione un motivo</option>";
																	foreach ($params['motivos_pendiente'] as $motivo) {
																		echo '<option value="' . htmlspecialchars($motivo['id_motivo']) . '">' .
																			htmlspecialchars($motivo['desc_motivo']) . '</option>';
																	}
																	;

																	echo "</select>
																											</div>
																											<div id='error-motivo_pen_" . $resultado['ID'] . "' style='color: red; display: none; margin-top: 5px;'>
																												" . $lang['select_motivo'] . "
																											</div>

																											<div class='col-md-12' id='descripcion-container' style='text-align: left; margin-top: 0px;'>
																												<label>" . $lang['desc'] . ":</label><br>
																												<textarea name='descripcion' id='descripcion' style='width: 100%;' rows='3' class='form-control'></textarea>
																											</div>

																											<div class='col-md-12' id='justificante-container' style='text-align: left; margin-top: 15px;'>
																												<label>Justificante (opcional):</label><br>
																												<input type='file' name='justificante' id='justificante' class='form-control'>
																											</div>
																										</form>
																									</div>
																								</div>

																								<div class='modal-footer'>
																									<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" . $lang['cerrar'] . "</button>
																									<button type='button' onclick='validarFormulario(\"pen\", \"" . $resultado['ID'] . "\")' class='btn btn-primary'>
																										<span>" . $lang['confirm'] . "</span>
																									</button>
																								</div>
																									
																							</div>
																						</div>
																					</div>
																				</div>
																			</li>
																		</ul>";

																}
															} elseif ($resultado['ESTADO'] == "3" && $resultado['NUM_RELACIONES'] == "0") {
																echo "
																		<ul class='icon-list'>
																			<li>
																				<div class='col-md-3 align-c'>
																					
																					<a data-bs-toggle='modal' data-bs-target='#aceptar_modal_" . $resultado['ID'] . "'>
																						<i class='bi bi-check-circle aceptar'></i>
																					</a>

																					<div class='modal fade' id='aceptar_modal_" . $resultado['ID'] . "' tabindex='-1' style='display: none;' aria-hidden='true'>
																						<div class='modal-dialog modal-dialog-centered'>
																							<div class='modal-content'>
																								<div class='modal-header'>
																									<h5 class='modal-title'>" . $lang['aceptar'] . " " . $lang['llama'] . "</h5>
																									<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
																								</div>

																								<div class='modal-body'>
																									<div class='row'>
																										<form method='POST' enctype='multipart/form-data' action='admin_cont.php?controller=index&action=update_trabajador&id=" . $pernr . "&showll&remesa&respuesta&id_remesa=" . $id_remesa . "&ano_remesa=" . $ano_remesa . "' id='form_respuesta_acep_" . $resultado['ID'] . "'>
																											<input type='hidden' name='Tipo_respuesta' value='Aceptado'>
																											<input type='hidden' name='id_registro' value='" . $resultado['ID'] . "'>
																											<input type='hidden' name='id_remesa' value='" . $id_remesa . "'>
																											<input type='hidden' name='ano_remesa' value='" . $ano_remesa . "'>

																											<div class='col-md-12' style='text-align: left; margin-top: 0px;'>
																												<label>" . $lang['desc'] . ":</label><br>
																												<textarea name='descripcion' id='descripcion_acep_pend_" . $resultado['ID'] . "' style='width: 100%;' rows='3' class='form-control'></textarea>
																											</div>

																											<div class='col-md-12' id='justificante-container' style='text-align: left; margin-top: 15px;'>
																												<label>Justificante (opcional):</label><br>
																												<input type='file' name='justificante' id='justificante' class='form-control'>
																											</div>

																										</form>
																									</div>
																								</div>

																								<div class='modal-footer'>
																									<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" . $lang['cerrar'] . "</button>
																									<button type='button' onclick='validarFormulario(\"acep\", \"" . $resultado['ID'] . "\")' class='btn btn-primary'>
																										<span>" . $lang['confirm'] . "</span>
																									</button>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</li>



																			<li>
																				<div class='col-md-3 align-c'>
																					
																					<a data-bs-toggle='modal' data-bs-target='#rechazar_modal_" . $resultado['ID'] . "'>
																						<i class='bi bi-x-circle rechazar'></i>
																					</a>

																					<div class='modal fade' id='rechazar_modal_" . $resultado['ID'] . "' tabindex='-1' style='display: none;' aria-hidden='true'>
																						<div class='modal-dialog modal-dialog-centered'>
																							<div class='modal-content'>
																								<div class='modal-header'>
																									<h5 class='modal-title'>" . $lang['rechazar'] . " " . $lang['llama'] . "</h5>
																									<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
																								</div>

																								<div class='modal-body'>
																									<div class='row'>
																										<form method='POST' enctype='multipart/form-data' action='admin_cont.php?controller=index&action=update_trabajador&id=" . $pernr . "&showll&remesa&respuesta&id_remesa=" . $id_remesa . "&ano_remesa=" . $ano_remesa . "' id='form_respuesta_rech_" . $resultado['ID'] . "'>
																											<input type='hidden' name='Tipo_respuesta' value='rechazar'>
																											<input type='hidden' name='id_registro' value='" . $resultado['ID'] . "'>
																											<input type='hidden' name='id_remesa' value='" . $id_remesa . "'>
																											<input type='hidden' name='ano_remesa' value='" . $ano_remesa . "'>

																											<div class='col-md-9' style='text-align: left;'>
																												<label>" . $lang['select_moti_2'] . ":</label><br>
																												<select name='motivo' id='motivo_rech_" . $resultado['ID'] . "' style='width: 350px;' required>
																													<option value=''>Seleccione un motivo</option>";
																foreach ($params['motivos_pendiente'] as $motivo) {
																	echo '<option value="' . htmlspecialchars($motivo['id_motivo']) . '">' .
																		htmlspecialchars($motivo['desc_motivo']) . '</option>';
																}
																;

																echo "</select>
																											</div>
																											<div id='error-motivo_rech_" . $resultado['ID'] . "' style='color: red; display: none; margin-top: 5px;'>
																												" . $lang['select_motivo'] . "
																											</div>

																											<div class='col-md-12' style='text-align: left; margin-top: 15px;'>
																												<label>" . $lang['desc'] . ":</label><br>
																												<textarea name='descripcion' id='descripcion_rech_pend_" . $resultado['ID'] . "' style='width: 100%;' rows='3' class='form-control'></textarea>
																											</div>

																											<div class='col-md-12' id='justificante-container' style='text-align: left; margin-top: 15px;'>
																												<label>Justificante (opcional):</label><br>
																												<input type='file' name='justificante' id='justificante' class='form-control'>
																											</div>
																										</form>
																									</div>
																								</div>

																								<div class='modal-footer'>
																									<button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>" . $lang['cerrar'] . "</button>
																									<button type='button' onclick='validarFormulario(\"rech\", \"" . $resultado['ID'] . "\")' class='btn btn-primary'>
																										<span>" . $lang['confirm'] . "</span>
																									</button>
																								</div>
																							</div>
																						</div>
																					</div>
																				</div>
																			</li>
																		</ul>";
															}
														}
														?>
													</td>

													<?php
													if ($hayJustificante) {
														?>
														<td>
															<?php if (!empty($resultado['JUSTIFICANTE'])) { ?>
																<a href="<?php echo htmlspecialchars($resultado['JUSTIFICANTE']); ?>"
																	target="_blank" class="btn btn-sm btn-primary" download>
																	Descargar
																</a>
															<?php } else {
																echo '';
															} ?>
														</td>
														<?php
													}
													?>
													<?php
												}
												?>
											</tr>
											<?php
										}
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function validarFormulario(tipo, idRegistro) {
				var nombreyapellidos = '<?php echo $nombreyapellidos; ?>';
				var formId = 'form_respuesta_' + tipo + '_' + idRegistro;
				var mensajes = {
					'acep': '¿Aceptar llamamiento a ',
					'rech': '¿Rechazar llamamiento a ',
					'pen': '¿Marcar como pendiente el llamamiento a '
				};

				// Solo validar motivo para rechazar y pendiente
				if (tipo !== 'acep') {
					var motivo = document.getElementById('motivo_' + tipo + '_' + idRegistro).value;
					var errorMotivo = document.getElementById('error-motivo_' + tipo + '_' + idRegistro);

					if (motivo === '') {
						errorMotivo.style.display = 'block';
						return false;
					}
					errorMotivo.style.display = 'none';
				}

				alertify.confirm(mensajes[tipo] + nombreyapellidos + '?', function () {
					document.getElementById(formId).submit();
				});
			}



			function redirigir() {
				// Obtén los parámetros de la URL actual
				var urlParams = new URLSearchParams(window.location.search);

				// Obtén los valores de id_remesa y ano_remesa (asumiendo que están en la URL actual)
				var id_remesa = urlParams.get('id_remesa');
				var ano_remesa = urlParams.get('ano_remesa');

				// Inicializa la variable para la URL de redirección
				var redirectUrl = '';


				// Verifica si el parámetro 'tipo_llamamiento' existe
				if (urlParams.has('llamamiento')) {
					// Obtén el parámetro 'id'
					var pernr = urlParams.get('id');

					// Construye la URL de redirección para 'llamamiento'
					redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}&showll&remesa&id_remesa=${id_remesa}&ano_remesa=${ano_remesa}`;
				}

				// Verifica si el parámetro 'id_registro' existe para actualizar el llamamiento
				else if (urlParams.has('respuesta')) {
					// Obtén el parámetro 'id'
					var pernr = urlParams.get('id');
					// Construye la URL de redirección para 'id_registro'
					redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}&showll&remesa&id_remesa=${id_remesa}&ano_remesa=${ano_remesa}`;
				}

				// Verifica si el parámetro 'nuevaalerta' existe por añadir alerta
				else if (urlParams.has('nuevaalerta')) {
					// Obtén el parámetro 'id'
					var pernr = urlParams.get('id');
					// Construye la URL de redirección para 'id_registro'
					redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}&alertas`;
				}

				// Verifica si el parametro 'fecha'
				else if (urlParams.has('fecha')) {
					// Obtén el parámetro 'id'
					var pernr = urlParams.get('id');
					// Construye la URL de redirección
					redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}`;
				}


				// Si se construyó una URL de redirección, realiza la redirección
				if (redirectUrl) {
					window.location.href = redirectUrl;
				}
			}

			// Llama a la función con un retraso de 2 segundos
			setTimeout(redirigir, 2000);


			function redirigir2() {
				// Obtén los parámetros de la URL actual
				var urlParams = new URLSearchParams(window.location.search);

				var id_remesa = urlParams.get('id_rem') || '';
				var ano_remesa = urlParams.get('ano_rem') || '';

				// Inicializa la variable para la URL de redirección
				var redirectUrl = '';

				// Verifica si el parámetro 'NFC' existe por actualizacion del contacto
				if (urlParams.has('NFC')) {
					// Obtén el parámetro 'id'
					var pernr = urlParams.get('id');

					if (id_remesa && ano_remesa) {
						// Construye la URL de redirección'
						redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}&remesa&id_rem=${id_remesa}&ano_rem=${ano_remesa}`;
					} else {
						redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}`;
					}
				}

				// Verifica si el parámetro 'actualizacion' existe por actualizacion del contacto
				else if (urlParams.has('actualizacion')) {
					// Obtén el parámetro 'id'
					var pernr = urlParams.get('id');
					// Construye la URL de redirección para 'id_registro'
					if (id_remesa && ano_remesa) {
						redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}&contact&remesa&id_rem=${id_remesa}&ano_rem=${ano_remesa}`;
					} else {
						redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}&contact`;
					}
				}

				// Si se construyó una URL de redirección, realiza la redirección
				if (redirectUrl) {
					window.location.href = redirectUrl;
				}
			}

			// Llama a la función con un retraso de 2 segundos
			setTimeout(redirigir2, 4000);
		</script>
	</div>

</section>


<?php
include("footer.php");

?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
	$(document).ready(function () {
		$('#PRE_TELF').select2({
			placeholder: 'Seleccionar un prefijo',
			closeOnSelect: true,
			language: {
				noResults: function () {
					return 'No se encontraron resultados.';
				},
				searching: function () {
					return 'Buscando...';
				}
			}
		});

		$('#PRE_TELF_EMP').select2({
			placeholder: 'Seleccionar un prefijo',
			closeOnSelect: true,
			language: {
				noResults: function () {
					return 'No se encontraron resultados.';
				},
				searching: function () {
					return 'Buscando...';
				}
			}
		});

		$('#PRE_TELF_EMER').select2({
			placeholder: 'Seleccionar un prefijo',
			closeOnSelect: true,
			language: {
				noResults: function () {
					return 'No se encontraron resultados.';
				},
				searching: function () {
					return 'Buscando...';
				}
			}
		});

		$('#PARENT_TELF').select2({
			closeOnSelect: true,
			language: {
				noResults: function () {
					return 'No se encontraron resultados.';
				},
				searching: function () {
					return 'Buscando...';
				}
			}
		});

		$('#PARENT_TELF_EMP').select2({
			closeOnSelect: true,
			language: {
				noResults: function () {
					return 'No se encontraron resultados.';
				},
				searching: function () {
					return 'Buscando...';
				}
			}
		});

		$('#PARENT_TELF_EMER').select2({
			closeOnSelect: true,
			language: {
				noResults: function () {
					return 'No se encontraron resultados.';
				},
				searching: function () {
					return 'Buscando...';
				}
			}
		});

		// Detectar cuando se abren las pestañas usando jQuery
		$('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
			const targetId = $(e.target).data('bs-target');

			if (targetId === '#datos-asignacion') {
				cargarDatosAsignacion();
			} else if (targetId === '#datos-medidas') {
				cargarDatosMedidas();
			} else if (targetId === '#datos-direccion') {
				cargarDatosDireccion();
			} else if (targetId === '#datos-contrato') {
				cargarDatosContrato();
			} else if (targetId === '#datos-ropo') {
				cargarDatosRopo();
			} else if (targetId === '#datos-ausencia') {
				cargarDatosAusencia();
			}
		});
	});
</script>