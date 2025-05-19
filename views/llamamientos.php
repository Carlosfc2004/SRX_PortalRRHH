<?php
include_once("header.php");
?>
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
						<th style="width:3%"></th>
						<th style="width:8%">Cod. Trabajador</th>
						<th style="width:28%"><?php echo $lang['nombre']; ?></th>
						<th style="width:15%">Fecha Baja</th>
						<th style="width:12%"><?php echo $lang['telefono']; ?></th>
						<th style="width:15%"><?php echo $lang['correo2']; ?></th>
						<th style="width:6%"></th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

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
		table = $('#table-info').DataTable({
			'processing': true,
			'language': {
				'processing': '<div></div>'
			},
			'ajax': {
				'type': 'POST',
				'url': 'auto.php?datosLlamamiento',
				'data': {
					id_remesa: <?php echo $id_remesa; ?>, 
					ano_remesa: <?php echo $ano_remesa; ?>
				},
				'dataSrc': function(json) {
					// Contar el número de filas en los datos JSON
					rowCount = json.length;
					return json;
				}
			},
			// Definición de anchos de columnas
			'columnDefs': [
				{ 'width': '3%', 'targets': 0 },  // Checkbox column
				{ 'width': '8%', 'targets': 1 },  // PERNR
				{ 'width': '28%', 'targets': 2 }, // Nombre
				{ 'width': '15%', 'targets': 3 }, // BEGDA
				{ 'width': '12%', 'targets': 4 }, // Teléfono
				{ 'width': '15%', 'targets': 5 }, // Correo
				{ 'width': '6%', 'targets': 6 }   // Botón editar
			],
			'columns': [
				// Definición de columnas...
				{ 
					'data': 'PERNR',
					'render': function (data, type, row) {
						const movilValido = row.MOVIL && row.MOVIL.trim() !== '' && /^\d{9}$/.test(row.MOVIL);
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
                            <a href="admin_cont.php?controller=index&action=update_trabajador&id=${row.PERNR}&contact">
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
				"processing": "Procesando...",
				"paginate": {
					"first": "",
					"last": "",
					"next": "›",
					"previous": "‹"
				},
				"emptyTable": "No hay información"
			},
			'initComplete': function () {
				// Restaurar estados de checkboxes y actualizar contador
				restoreCheckboxStates();
				$('#num_trab').text(rowCount);
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
		$('#generar_rem').on('click', function() {
            $("#load_rem_conf").html(`
            <div class="card" style="width: 450px;">
                <div class="card-body">
                    <div class="row">
                        <form id="form-remesa" action="admin_cont.php?controller=index&action=generar_rem_llama&remesa" method="POST">
                            <h4 class="mt-3" style="text-align: center;"><b><?php echo $lang['crear_rem']; ?></b></h4>
                            <input type="hidden" id="generar_rem" name="generar_rem">
                            <div class="col-md-12 mt-3" style="text-align: left;">
                                <label for="nombre_remesa" ><b><?php echo $lang['nombre_rem2']; ?></b></label>
                                <input type="text" id="nombre_remesa" name="nombre_remesa" class="form-control mb-1 mt-1" placeholder="<?php echo $lang['nombre_rem']; ?>" required>
                            </div>

							<div class='col-md-6 mt-3'>
							    <label for="fecha_inc" ><b>Fecha incorporación </b></label>
								<input type='date' id="fecha_inc" name='fecha_inc' class='form-control' value='' required>
							</div>

							<div class='col-md-6 mt-3'>
							    <label for="telefono_rem" ><b><?php echo $lang['telefono']; ?> </b></label>
								<input type='number' id="telefono_rem" name='telefono_rem' class='form-control' value='<?php echo $_SESSION["telefono_user_surexport_appreclu"]; ?>' required>
							</div>

							<div class='col-md-6 mt-3'>
								<div class="form-check form-switch">
									<input class="form-check-input" type="checkbox" name="sms_auto" checked="">
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
            $("#load_rem_conf").lightbox_me({centered: true});
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
				form.append('<input type="hidden" name="telefono_rem" value="' + <?php echo $_SESSION["telefono_user_surexport_appreclu"]; ?> + '">');
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