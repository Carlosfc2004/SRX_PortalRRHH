<?php
include_once("header.php");
?>
<style>
    /* Asegurar que el select desplegado tenga borde en todos los lados */
    .form-select option {
        border: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6 !important;
    }
    
    /* Alternativa: forzar borde en el select cuando está enfocado/desplegado */
    select.form-select:focus option,
    select.form-select option {
        border-right: 1px solid #ced4da !important;
    }
</style>
<div class="pagetitle">
	<?php
		if (isset($_POST['add_remesa'])) {
			echo '<h1>'.$lang['titu_llamamiento_add'].' '.$_POST['id_remesa'].'/'.$_POST['ano_remesa'].'</h1>';
		} else {
			echo '<h1>'.$lang['menu4'].'</h1>';
		}
	?>
	
</div>
	<nav>
        <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu3']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['menu4']; ?></li>
        </ol>
    </nav>
	<section class="section">
        <div class="card">
			<div class="card-body">
				<h5 class="card-title"><?php echo $lang['titu_llamamiento']; ?> (<span id="num_trab" style="font-weight: bold; color: #012970;"></span>)</h5>
				<?php
				if (!isset($_POST['add_remesa'])) { ?>
					<button id="btn_rem_conf" class="btn btn-primary mt-2"><?php echo $lang['select_num_trab']; ?></button>
					<div id="load_rem_conf" style="display: none;"></div>
				<?php
				} 
			?>

			<form action="admin_cont.php?controller=index&action=llamamientos" method="post">
				<h5 class="card-title">Filtros:</h5>
                <div class="row align-items-end">
					<!-- Ubicación -->
					<div class="col-md-3">
						<label for="ubi_trab" class="form-label">Ubicación:</label>
						<select class="form-select" name="ubi_trab" id="ubi_trab">
							<option value=""></option>
							<?php
								foreach ($params['fincas_almacenes'] as $key => $value) {
									echo '<option value="' . $value['ZZLGORT'] . '" ' . (isset($_POST['ubi_trab']) && $_POST['ubi_trab'] == $value['ZZLGORT'] ? 'selected' : '') . '>' . $value['DESC_ALMACEN'] ." (".$value['ZZLGORT'].")". '</option>';
								}
							?>
						</select>
					</div>

					<!-- Botón para abrir modal formateador -->
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                            data-bs-target="#modalFormateadorCodigos">
                            <i class="bi bi-list-ol"></i> Trabajadores
                            <?php if (isset($_POST['codigos_formateados']) && !empty($_POST['codigos_formateados'])) { ?>
                                <span class="badge bg-light ms-1 text-dark" id="badgeTrabajadores">✓</span>
                            <?php } else { ?>
                                <span class="badge bg-light ms-1 text-dark" id="badgeTrabajadores" style="display: none;"></span>
                            <?php } ?>
                        </button>
                        <?php if (isset($_POST['codigos_formateados']) && !empty($_POST['codigos_formateados'])) { ?>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1 w-100"
                                onclick="limpiarCodigosSeleccionados()" title="Limpiar trabajadores seleccionados">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1 w-100" id="btnLimpiarCodigos"
                                onclick="limpiarCodigosSeleccionados()" style="display: none;"
                                title="Limpiar trabajadores seleccionados">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </button>
                        <?php } ?>
                    </div>

                    <!-- Campos ocultos para códigos -->
                    <input type="hidden" id="codigos_formateados" name="codigos_formateados"
                        value="<?php echo isset($_POST['codigos_formateados']) ? htmlspecialchars($_POST['codigos_formateados']) : ''; ?>">
                    <input type="hidden" id="codigos_originales" name="codigos_originales"
                        value="<?php echo isset($_POST['codigos_originales']) ? htmlspecialchars($_POST['codigos_originales']) : ''; ?>">

					<!-- Fecha inicio -->
					<div class="col-md-3">
						<label for="fecha_ini" class="form-label">Fecha baja:</label>
						<input type="date" class="form-control" id="fecha_ini" name="fecha_ini" value="<?php echo isset($_POST['fecha_ini']) ? $_POST['fecha_ini'] : date('Y-m-d', strtotime('-16 months')); ?>">
					</div>

					<!-- Separador "a" -->
					<div class="col-md-1 text-md-center">
						<span>a</span>
					</div>

					<!-- Fecha fin -->
					<div class="col-md-3">
						<label for="fecha_fin" class="form-label" style="color: white">Fecha baja fin:</label>
						<input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : date('Y-m-d'); ?>">
					</div>

					<!-- Campo para relaciones laborales -->
					<div class="col-md-3 mt-3">
						<label for="relacion_laboral" class="form-label">Relación Laboral:</label>
						<select class="form-select w-100" name="relacion_laboral" id="relacion_laboral">
							<option value=""></option>
							<?php
								if (isset($params['relaciones_laborales'])) {
									foreach ($params['relaciones_laborales'] as $key => $value) {
										echo '<option value="' . $value['RELACION_LABORAL'] . '" ' . (isset($_POST['relacion_laboral']) && $_POST['relacion_laboral'] == $value['RELACION_LABORAL'] ? 'selected' : '') . '>' . $value['RELACION_LABORAL'] . " - " . $value['DESC_RELACION_LABORAL'] . '</option>';
									}
								}
							?>
						</select>
					</div>
				</div>
				<br>
			
                <div id="loading" style="display: none;">
                    <button class="btn btn-primary mt-2" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Buscando...
                    </button>
                </div>
                <input type="submit" name="buscar" value="Buscar" class="btn btn-primary mt-2">
                <script>
                    // Mostrar el div de carga y ocultar el botón de búsqueda al enviar el formulario
                    document.querySelector('form').addEventListener('submit', function() {
                        document.getElementById('loading').style.display = 'block';
						document.querySelector('input[type="submit"]').style.display = 'none';
                    });
                </script>
			</form>

			<!-- <form action='' id='exportar' method='post' style='display: inline-block; margin-left: 15px;'>
                <input type="hidden" name="ubicacion" value="<?php echo isset($_POST['ubi_trab']) ? $_POST['ubi_trab'] : ''; ?>">
				<input type="hidden" name="fecha_ini" value="<?php echo isset($_POST['fecha_ini']) ? $_POST['fecha_ini'] : ''; ?>">
				<input type="hidden" name="fecha_fin" value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>">

                <button type="button" target="_blank"
                    onclick="document.getElementById('exportar').action='exportar.php?informe_trabajadores_baja_excel&ubicacion=' + document.getElementById('ubi_trab').value + '&fecha_ini=' + document.getElementById('fecha_ini').value + '&fecha_fin=' + document.getElementById('fecha_fin').value; document.getElementById('exportar').submit();"
                    style="background-color: white;">
                    <img src="img/xls.png" style="max-width: 100px; width: 35px; margin-top: 10px;">
                </button>
            </form>
			 -->

			<div class="col-lg-12" style="margin-top: 30px;">
				<span id="user_selec" style="font-size: 16px; font-weight: bold;"></span>
				<?php 
					if (isset($_POST['add_remesa'])) {
						// variables para pasar por ajax 
						$id_remesa = $_POST['id_remesa'];
						$ano_remesa = $_POST['ano_remesa'];
						echo '<input type="hidden" name="id_remesa" value="'.$_POST['id_remesa'].'">
						<input type="hidden" name="ano_remesa" value="'.$_POST['ano_remesa'].'">
						<input type="button" name="add_candidato" id="add_candidato" value="Añadir Trabajador" class="btn btn-primary mt-auto" style="margin-top: 0 !important; display: none;">';
					} else {
						$id_remesa = 0;
						$ano_remesa = 0;
						echo '<input type="button" id="generar_rem" name="generar_rem" value="'.$lang['generar_rem'].'" class="btn btn-primary" style="margin-top: 0 !important; display: none; margin-left: 10px;">';
					}
				?>
			</div>
			<br>
			<table id="table-info" class="table datatable display" style="width:100%;">
				<thead>
					<tr>
						<th style="width:2%"></th>
						<th style="width:7%">Cod. Trabajador</th>
						<th style="width:20%"><?php echo $lang['nombre']; ?></th>
						<th style="width:11%">Almacen</th>
						<th style="width:18%">Relacion Laboral</th>
						<th style="width:10%">Fecha Baja</th>
						<th style="width:10%"><?php echo $lang['telefono']; ?></th>
						<th style="width:18%"><?php echo $lang['correo2']; ?></th>
						<th style="width:4%"></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>


    <!-- Modal Formateador de Códigos -->
    <div class="modal fade" id="modalFormateadorCodigos" tabindex="-1" aria-labelledby="modalFormateadorCodigosLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalFormateadorCodigosLabel">
                        <i class="bi bi-search me-2"></i>Buscar Trabajadores por Código
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="codigosInput" class="form-label"><strong>Pega aquí los códigos PERNR o DNI/NIE desde
                                Excel:</strong></label>
                        <textarea class="form-control font-monospace" id="codigosInput" rows="10"
                            placeholder="1004207&#10;01005734&#10;...&#10;12345678A&#10;87654321C&#10;...&#10;X0134567A&#10;A0123456Z&#10;..."></textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Los códigos de 7 dígitos se convertirán automáticamente a
                            8 dígitos (añadiendo un 0 a la izquierda).
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="aplicarCodigos()">
                        <i class="bi bi-check-circle"></i> Aplicar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Inicializar badge al cargar la página si hay códigos del POST
         */
        document.addEventListener('DOMContentLoaded', function () {
            const codigosFormateados = document.getElementById('codigos_formateados').value;
            const codigosOriginales = document.getElementById('codigos_originales').value;

            if (codigosFormateados && codigosFormateados.trim()) {
                // Contar cuántos códigos hay (contar las comillas simples y dividir por 2)
                const numCodigos = (codigosFormateados.match(/'/g) || []).length / 2;

                // Actualizar badge
                const badge = document.getElementById('badgeTrabajadores');
                badge.textContent = Math.floor(numCodigos);
                badge.style.display = 'inline-block';

                // Mostrar botón limpiar
                const btnLimpiar = document.getElementById('btnLimpiarCodigos');
                if (btnLimpiar) {
                    btnLimpiar.style.display = 'block';
                }
            }

            // Restaurar códigos originales en el textarea cuando se abre el modal
            const modal = document.getElementById('modalFormateadorCodigos');
            modal.addEventListener('show.bs.modal', function () {
                if (codigosOriginales && codigosOriginales.trim()) {
                    document.getElementById('codigosInput').value = codigosOriginales;
                }
            });
        });

        /**
         * Aplicar códigos formateados (sin enviar el formulario)
         */
        function aplicarCodigos() {
            const input = document.getElementById('codigosInput').value;

            if (!input.trim()) {
                alertify.warning('Por favor, pega códigos PERNR en el área de texto');
                return;
            }

            // Dividir por saltos de línea y eliminar espacios
            const lineas = input.split('\n');
            const codigos = [];
            let procesados = 0;
            let modificados = 0;

            lineas.forEach(linea => {
                let codigo = linea.trim();

                // Ignorar líneas vacías
                if (!codigo) return;

                // Si tiene 7 caracteres, añadir 0 a la izquierda
                if (codigo.length === 7 && /^\d+$/.test(codigo)) {
                    codigo = '0' + codigo;
                    modificados++;
                }

                codigos.push(codigo);
                procesados++;
            });

            if (procesados === 0) {
                alertify.warning('No se encontraron códigos válidos');
                return;
            }

            // Formatear para SQL: 'codigo1', 'codigo2', 'codigo3'
            const resultado = "'" + codigos.join("', '") + "'";

            // Guardar los códigos formateados en el campo oculto
            document.getElementById('codigos_formateados').value = resultado;

            // Guardar los códigos originales (sin formatear) para restaurarlos después
            document.getElementById('codigos_originales').value = input;

            // Mostrar badge con indicador
            const badge = document.getElementById('badgeTrabajadores');
            badge.textContent = procesados;
            badge.style.display = 'inline-block';

            // Mostrar botón de limpiar
            const btnLimpiar = document.getElementById('btnLimpiarCodigos');
            if (btnLimpiar) {
                btnLimpiar.style.display = 'block';
            }

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalFormateadorCodigos'));
            modal.hide();

            // Notificar al usuario
            let mensaje = `${procesados} trabajador(es) seleccionado(s)`;
            if (modificados > 0) {
                mensaje += ` (${modificados} código(s) convertido(s) de 7 a 8 dígitos)`;
            }
            mensaje += '. Haz clic en "Buscar" para aplicar los filtros.';
            alertify.success(mensaje);
        }

        /**
         * Limpiar códigos seleccionados
         */
        function limpiarCodigosSeleccionados() {
            // Limpiar campos ocultos
            document.getElementById('codigos_formateados').value = '';
            document.getElementById('codigos_originales').value = '';

            // Limpiar textarea del modal
            document.getElementById('codigosInput').value = '';

            // Ocultar badge
            const badge = document.getElementById('badgeTrabajadores');
            badge.style.display = 'none';

            // Ocultar botón de limpiar
            const btnLimpiar = document.getElementById('btnLimpiarCodigos');
            if (btnLimpiar) {
                btnLimpiar.style.display = 'none';
            }

            alertify.success('Trabajadores seleccionados eliminados');
        }
    </script>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>

<script>
	// Objeto para almacenar los estados de los checkboxes
	var checkboxStates = {};
	// Variable para la tabla DataTable
	var table;
	// Contador de trabajadores seleccionados
	var numeroTrabajadoresSeleccionados = 0;

	// Función para inicializar la tabla
	function initializeTable() {
		let rowCount = 0;
		
		$('#ubi_trab').closest('form').on('submit', function(e) {
			e.preventDefault(); // Evita recargar la página
			table.ajax.reload(); // Recarga el DataTable con el nuevo filtro
		});
		table = $('#table-info').DataTable({
			'ajax': {
				'type': 'POST',
				'url': 'auto.php?datosLlamamiento',
				'data': function(d) {
					d.id_remesa = <?php echo $id_remesa; ?>;
					d.ano_remesa = <?php echo $ano_remesa; ?>;
					d.ubi_trab = $('#ubi_trab').val(); // valor dinámico del filtro
					d.fecha_ini = $('#fecha_ini').val(); // valor dinámico del filtro
					d.fecha_fin = $('#fecha_fin').val(); // valor dinámico del filtro
					d.codigos_formateados = $('#codigos_formateados').val(); // valor dinámico del filtro de códigos
					d.relacion_laboral = $('#relacion_laboral').val(); // valor dinámico del filtro de relación laboral
				},
				'dataSrc': function(json) {
					rowCount = json.length;
					return json;
				}
			},
			// Definición de anchos de columnas
			'columnDefs': [
				{ 'width': '2%', 'targets': 0 },   // Checkbox column
				{ 'width': '7%', 'targets': 1 },   // PERNR
				{ 'width': '20%', 'targets': 2 },  // Nombre
				{ 'width': '11%', 'targets': 3 },  // Almacen
				{ 'width': '18%', 'targets': 4 },  // Relacion Laboral
				{ 'width': '10%', 'targets': 5 },   // BEGDA (Fecha Baja)
				{ 'width': '10%', 'targets': 6 },  // Teléfono
				{ 'width': '18%', 'targets': 7 },  // Correo
				{ 'width': '4%', 'targets': 8 }    // Botón editar
			],
			'columns': [
				// Definición de columnas...
				{ 
					'data': 'PERNR',
					'render': function (data, type, row) {
						// Eliminamos todos los espacios en el móvil
						const movilLimpio = row.MOVIL ? row.MOVIL.replace(/\s+/g, '') : '';
						// Validación
						const movilValido = movilLimpio !== '' && /^\d{9}$/.test(movilLimpio);
						
						const prefijoValido = row.PREFIJO && row.PREFIJO.trim() !== '';
						
						return movilValido && prefijoValido
							? `<input type="checkbox" name="user_remesas[]" class="form-check-input myCheckbox" value="${data}" id="checkbox_${data}">`
							: '';
					},
					'orderable': true,
					'searchable': false,
					'className': 'dt-body-center' 
				},
				{ 'data': 'PERNR' },
				{ 'data': 'NOMBRE' },
				{ 'data': 'ZZLGORT' },
				{ 
					'data': 'RELACION_LABORAL',
					'render': function(data, type, row) {
						if (row.DESC_RELACION_LABORAL) {
							return data + ' - ' + row.DESC_RELACION_LABORAL;
						}
						return data || '';
					}
				},
				{ 'data': 'BEGDA' },
				{ 
					'data': 'MOVIL',
					'render': function(data, type, row) {
						const prefijo = row.PREFIJO ? row.PREFIJO.trim() : '';
						const movil = data ? data.trim() : '';
						
						if (prefijo && movil) {
							return `+${prefijo}${movil}`;
						}
						return movil; // Devuelve solo el móvil si no hay prefijo
					}
				},
				{ 'data': 'CORREO' },
				{ 
					'render': function (data, type, row) {
						return `
						<li class="hvr-icon-back">
                            <a href="admin_cont.php?controller=index&action=update_trabajador&id=${row.PERNR}&contact" target="_blank">
                                <div class="hvr-icon" >
                                    <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                </div>
                            </a>
                        </li>`;
					},
					'orderable': false,
				}
			],
			'order': [[3, 'asc']],
			'lengthMenu': [[100, 500, -1], [100, 500, "All"]],
			'language': {
				"search": "Buscar ",
				"info": "Mostrando _START_ a _END_ de _TOTAL_ resultados ",
				"infoFiltered": "(filtrado de _MAX_ registros totales)",
				"zeroRecords": "Sin resultados encontrados",
				"lengthMenu": "_MENU_ Resultados por página",
				"paginate": {
					"first": "",
					"last": "",
					"next": "›",
					"previous": "‹"
				},
				"emptyTable": "No hay información"
			},
			'initComplete': function () {
				restoreCheckboxStates();
				$('#num_trab').text(rowCount);
			},
			'drawCallback': function(settings) {
				$('#num_trab').text(rowCount);
			},
			drawCallback: function(settings) {
				// una vez cargado ocultar el boton de carga y volver a mostrar el boton de envio del formulario
				document.getElementById('loading').style.display = 'none';
				document.querySelector('input[type="submit"]').style.display = 'block';
			}
		});
	}

	// Función para restaurar los estados de los checkboxes
	function restoreCheckboxStates() {
		var rows = table.rows({ 'search': 'applied' }).nodes();
		$('input[type="checkbox"]', rows).each(function () {
			var checkbox = $(this);
			var idUsuario = checkbox.val();
			if (checkboxStates[idUsuario]) {
				checkbox.prop('checked', true);
			}
		});
	}

	// Función para actualizar los estados de los checkboxes
	function updateCheckboxStates() {
		checkboxStates = {};
		var count = 0;
		$('input[type="checkbox"]:checked').each(function() {
			checkboxStates[$(this).val()] = true;
			count++;
		});
		numeroTrabajadoresSeleccionados = count;
		updateUI();
	}

	// Función para actualizar la interfaz de usuario
	function updateUI() {
        var count = Object.keys(checkboxStates).filter(key => checkboxStates[key]).length;
        if (count > 0) {
            $('#user_selec').html("<?php echo $lang['traba_select'] ?>" + count);
            $('#generar_rem, #add_candidato').css({'display' : 'inline'});
        } else {
            $('#user_selec').html("");
            $('#generar_rem, #add_candidato').css({'display' : 'none'});
        }
    }

	// Función para manejar el cambio en el número de trabajadores de la remesa
	function handleNumeroTrabRemesaChange() {
		var numeroTrab = parseInt($('#numero_trab_remesa').val()) || 0;
		var totalTrabajadores = $('input[type="checkbox"]').length;

		// Verificar si hay suficientes trabajadores
		if (numeroTrab > totalTrabajadores) {
			$('#alertMessage').text('<?php echo $lang['no_trabajadores'] ?>' + totalTrabajadores + '<?php echo $lang['disponibles'] ?>.');
			$('#alertModal').show();
			numeroTrab = totalTrabajadores;
			$('#numero_trab_remesa').val(numeroTrab);
		}

		// Seleccionar/deseleccionar checkboxes
		var checkboxes = $('input[type="checkbox"]').slice(0, numeroTrab);
		checkboxes.prop('checked', true);
		$('input[type="checkbox"]').not(checkboxes).prop('checked', false);

		updateCheckboxStates();

		// Cierra el popup completamente
		$("#load_rem_conf").trigger('close');
	}

	

	// Función para manejar el cambio en el número de trabajadores de la remesa debug, trabajadores seleccionados
	// $(document).ready(function () {
	// 	// Capturar cambios en los checkboxes
	// 	$(document).on('change', 'input[type="checkbox"]', function () {

	// 		// Crear array de checkboxes seleccionados
	// 		var selectedCheckboxes = $('input[type="checkbox"]:checked').map(function () {
	// 			return this.id || '(sin ID)'; // Guarda el ID del checkbox o '(sin ID)' si no tiene
	// 		}).get(); // Convierte a array de JavaScript

	// 		console.log(selectedCheckboxes);
	// 	});
	// });



	
	$(document).ready(function () {
		// Configurar el placeholder del campo de búsqueda
        $('input[type="search"]').attr('placeholder', 'Buscar...');
        
		// Inicializar la tabla
        initializeTable();
        
		// Configurar evento de cambio para los checkboxes
        $('#table-info tbody').on('change', 'input[type="checkbox"]', function () {
			updateCheckboxStates();
        });
        
		// Evento para cerrar el modal de alerta
		$('#closeAlertModal').on('click', function() {
			$('#alertModal').hide(); // Cierra el modal al hacer clic en "Aceptar"
		});

		// Restaurar estados de checkboxes cuando se redibuja la tabla
        table.on('draw', function () {
            restoreCheckboxStates();
        });
        
		// Configurar el botón de remesa
        $("#btn_rem_conf").on('click', function(event) {
			// Generar HTML para el popup de generación de remesa
            $("#load_rem_conf").html(`
                <div class="card" style="width: 300px;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mt-4" style="text-align: left;">
                                <label for="numero_trab_remesa"><b><?php echo $lang['num_trab']; ?></b></label>
                                <input type="number" id="numero_trab_remesa" name="numero_trab_remesa" value="${numeroTrabajadoresSeleccionados}" class="form-control mt-2 mb-3" style="width: 80px;">
                            </div>
                            <button type="button" class="btn btn-primary mt-3" onclick="handleNumeroTrabRemesaChange()"><?php echo $lang['guardar']; ?></button>
                        </div>
                    </div>
                </div>
            `);
            
            $("#load_rem_conf").lightbox_me({centered: true});
        });
		
		// Generar HTML para el popup de generación de remesa final y generación
		$('#generar_rem').on('click', function () {
			$("#load_rem_conf").html(`
				<div class="card" style="width: 450px;">
					<div class="card-body">
						<div class="row">
							<form id="form-remesa" action="admin_cont.php?controller=index&action=generar_rem_llama&remesa" method="POST">
								<h4 class="mt-3" style="text-align: center;"><b><?php echo $lang['crear_rem']; ?></b></h4>
								<input type="hidden" id="generar_rem" name="generar_rem">

								<div class="col-md-12 mt-3" style="text-align: left;">
									<label for="nombre_remesa"><b><?php echo $lang['nombre_rem2']; ?></b></label>
									<input type="text" id="nombre_remesa" name="nombre_remesa" class="form-control mb-1 mt-1" placeholder="<?php echo $lang['nombre_rem']; ?>" required>
								</div>

								<!-- Fecha con restricción dinámica y texto que se oculta -->
								<div class='col-md-12 mt-3'>
									<label for="fecha_inc"><b>Fecha incorporación <br>
										<span id="min_dias_msg">(Mínimo 20 días para realizar la remesa)</span>
									</b></label>
									<input type='date' id="fecha_inc" name='fecha_inc' class='form-control' value='' required>
								</div>

								<div class='col-md-6 mt-3'>
									<label for="telefono_rem"><b><?php echo $lang['telefono']; ?></b></label>
									<input type='text' id="telefono_rem" name='telefono_rem' class='form-control' value='<?php echo $_SESSION["telefono_user_surexport_appreclu"]; ?>' required>
								</div>

								<div class='col-md-6 mt-3'>
									<div class="form-check form-switch">
										<input class="form-check-input" type="checkbox" name="sms_auto" id="sms_auto" checked>
										<label class="form-check-label">SMS Automático</label>
									</div>
								</div>

								<div id="usuarios-seleccionados"></div>
								<button type="submit" class="btn btn-primary mt-3"><?php echo $lang['generar']; ?></button>
							</form>
						</div>
					</div>
				</div>
			`);

			// Agregar los usuarios seleccionados al formulario
			for (var key in checkboxStates) {
				if (checkboxStates.hasOwnProperty(key) && checkboxStates[key]) {
					$('#usuarios-seleccionados').append(`<input type="hidden" name="user_remesas[]" value="${key}">`);
				}
			}

			// Mostrar el popup
			$("#load_rem_conf").lightbox_me({ centered: true });

			// Función para actualizar min de fecha y mostrar/ocultar mensaje
			function actualizarMinFecha() {
				const smsCheckbox = document.getElementById('sms_auto');
				const fechaInput = document.getElementById('fecha_inc');
				const mensajeMinDias = document.getElementById('min_dias_msg');

				if (smsCheckbox.checked) {
					const hoy = new Date();
					hoy.setDate(hoy.getDate() + 20);
					const minFecha = hoy.toISOString().split('T')[0];
					fechaInput.min = minFecha;
					mensajeMinDias.style.display = 'inline';  // Mostrar mensaje

					if (fechaInput.value && fechaInput.value < minFecha) {
						fechaInput.value = '';
					}
				} else {
					fechaInput.removeAttribute('min');
					mensajeMinDias.style.display = 'none';   // Ocultar mensaje
				}
			}

			// Ejecutar al cargar y al cambiar checkbox
			actualizarMinFecha();
			document.getElementById('sms_auto').addEventListener('change', actualizarMinFecha);
		});

		// Configurar el botón de añadir candidato
        $('#add_candidato').on('click', function() {
            var selectedUsers = [];
            for (var key in checkboxStates) {
                if (checkboxStates.hasOwnProperty(key) && checkboxStates[key]) {
                    selectedUsers.push(key);
                }
            }

            if (selectedUsers.length > 0) {
				// Crear y enviar un formulario con los usuarios seleccionados
                var form = $('<form action="admin_cont.php?controller=index&action=generar_rem_llama&añadir" method="POST"></form>');
                form.append('<input type="hidden" name="add_candidato" value="1">');
                form.append('<input type="hidden" name="id_remesa" value="' + $('input[name="id_remesa"]').val() + '">');
                form.append('<input type="hidden" name="ano_remesa" value="' + $('input[name="ano_remesa"]').val() + '">');
				// form.append('<input type="hidden" name="telefono_rem" value="' + <?php echo $_SESSION["telefono_user_surexport_appreclu"]; ?> + '">');
				form.append('<input type="hidden" name="telefono_rem" value="' + <?php
                    // Normalizar el número de teléfono eliminando caracteres especiales
                    $telefono = $_SESSION["telefono_user_surexport_appreclu"];
                    // Eliminar espacios, paréntesis, guiones y otros caracteres no numéricos excepto el +
                    $telefono_limpio = preg_replace('/[^\d+]/', '', $telefono);
                    // Escapar para JavaScript
                    echo json_encode($telefono_limpio);
                ?> + '">');
                selectedUsers.forEach(function(user) {
                    form.append('<input type="hidden" name="user_remesas[]" value="' + user + '">');
                });
                $('body').append(form);
                form.submit();
            } else {
                alert("Por favor, seleccione al menos un trabajador.");
            }
        });
	});
	

	function redirigir() {
		// Obtén los parámetros de la URL actual
		var urlParams = new URLSearchParams(window.location.search);

		// Inicializa la variable para la URL de redirección
		var redirectUrl = '';

		// Verifica si el parámetro 'tipo_llamamiento' existe
		if (urlParams.has('remesa')) {
			// Construye la URL de redirección para 'llamamiento'
			redirectUrl = `admin_cont.php?controller=index&action=rem_llama`;
		} 

		// Verifica si el parámetro 'id_registro' existe para actualizar el llamamiento
		else if (urlParams.has('añadir')) {
			// Construye la URL de redirección para 'id_registro'
			redirectUrl = `admin_cont.php?controller=index&action=view_remesa_llama&id=<?php echo isset($_POST['id_remesa']) ? $_POST['id_remesa'] : ''; ?>&ano=<?php echo isset($_POST['ano_remesa']) ? $_POST['ano_remesa'] : ''; ?>`;		
		}
		 
		// Verifica si el parámetro 'actualizacion' existe por actualizacion del contacto
		else if (urlParams.has('actualizacion')) {
			// Obtén el parámetro 'id'
			var pernr = urlParams.get('id');
			// Construye la URL de redirección para 'id_registro'
			redirectUrl = `admin_cont.php?controller=index&action=update_trabajador&id=${pernr}`;
		}
		
		// Si se construyó una URL de redirección, realiza la redirección
		if (redirectUrl) {
			window.location.href = redirectUrl;
		}
	}

	// Llama a la función con un retraso de 2 segundos
	setTimeout(redirigir, 3000);
	
</script>



<?php 
//Si hay algún mensaje emergente, lo mostramos
if (isset($params['resultado']) and $params['resultado']!="") {
	echo '<div id="emergente">'.$params['resultado'].'</div>';
}
?>

</main>

	<!-- Ventana de Alerta -->
	<div id="alertModal" class="modal" style="display: none; background-color: rgba(0, 0, 0, 0.6);">
		<div class="modal-dialog">
			<div class="modal-content">
				<!-- Header -->
				<div class="modal-header">
					<h5 class="modal-title"><?php echo $lang['alerta'] ?></h5>
				</div>
				<!-- Body -->
				<div class="modal-body">
					<p id="alertMessage" style="text-align: justify;">No hay suficientes trabajadores disponibles.</p>
					<p id="alertMessage" style="text-align: justify;"><?php echo $lang['utiliza_filtro'] ?></p>
					<p id="alertMessage" style="text-align: justify;">Para que el trabajador este disponible, es necesario que tenga relleno el prefijo y el teléfono en su ficha</p>
				</div>
				<!-- Footer -->
				<div class="modal-footer">
					<button type="button" id="closeAlertModal" class="btn btn-primary"><?php echo $lang['aceptar'] ?></button>
				</div>
			</div>
		</div>
	</div>
</body>


<!-- Vendor JS Files -->
	<script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
	<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script src="assets/vendor/chart.js/chart.umd.js"></script>
	<script src="assets/vendor/echarts/echarts.min.js"></script>
	<script src="assets/vendor/quill/quill.js"></script>
	<script src="assets/vendor/tinymce/tinymce.min.js"></script>
	<script src="assets/vendor/php-email-form/validate.js"></script>

<!-- Template Main JS File -->
  	<script src="assets/js/main.js"></script>

<!-- Mis JS -->
	<script type="text/javascript" src="js/jquery.lightbox_me.js"></script>
	<script type="text/javascript" src="js/script.js?ver=1.6"></script>
	<script type="text/javascript" src="js/script.js"></script>
	<script src="js/alertify.min.js"></script>
	<script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>

</html>


<a href="#ancla" class="back-to-top d-flex align-items-center justify-content-center active">
  <i class="bi bi-arrow-up-short"></i>
</a>