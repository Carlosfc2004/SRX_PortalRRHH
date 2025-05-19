<?php 
include_once("header.php");
?>
<div class="pagetitle">
	<?php 
		if (isset($_POST['add_remesa'])) {
			echo '<h1>'.$lang['menu7'].' '.$_POST['id_remesa'].'/'.$_POST['ano_remesa'].'</h1>';
		}else{
			echo '<h1>Generar nueva remesa</h1>';
		}
	?>
</div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['menu13']; ?></li>
        </ol>
    </nav>
	<section class="section">
        <div class="card">
                <div class="card-body">
					<br>
					<h5 style="color: #012970;"><?php echo $lang['candidatos_rem']; ?></h5>
			
			<form action="admin_cont.php?controller=index&action=generar_remesa" method="post" class="row g-3">
			<div class="col-lg-12">
				<span id="user_selec" style="font-size: 16px; font-weight: bold;"></span>
					<?php 
					if (isset($_POST['add_remesa'])) {
						echo '<input type="hidden" name="id_remesa" value="'.$_POST['id_remesa'].'">
						<input type="hidden" name="ano_remesa" value="'.$_POST['ano_remesa'].'">
						<input type="submit" name="add_candidato" id="generar_rem" value="'.$lang['add_candidato'].'" class="btn btn-primary mt-auto" style="margin-top: 0 !important; display: none;">';
					}else{
						echo '<input type="submit" name="generar_rem" id="generar_rem" value="'.$lang['generar'].'" class="btn btn-primary mt-3" style="margin-top: 0 !important; display: none;">';
					}
					?>
			</div>	
				<br>
				<table id="demo-table" class="table datatable display" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th><?php echo $lang['grupo']; ?></th>
                            <th><?php echo $lang['nombre']; ?></th>
                            <th><?php echo $lang['valor_doc']; ?></th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
			</form>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script>
        $(document).ready(function () {
			$(".loader").fadeOut("slow");
			$(document).ready(function() {
				$('input[type="search"]').attr('placeholder', 'Buscar...');
			});
            var checkboxStates = {};

            var table = $('#demo-table').DataTable({
                'ajax': {
                    'url': 'auto.php?datosGenerarRemesas',
                    'dataSrc': ''
                },
                'columns': [
                    {
                        'data': 'id_usuario',
                        'render': function (data, type, row) {
                            return row.estado === 0 ? `<input type="checkbox" name="user_remesas[]" class="form-check-input" value="${data}" id="checkbox_${data}">` : '';
                        },
                        'orderable': true,
                        'searchable': false,
                        'className': 'dt-body-center'
                    },
                    {
                        'data': 'id_relacion',
                        'render': function (data) {
                            return data ? `<?php echo $lang['grupo']; ?>: ${data}` : '<?php echo $lang['usu_sin_rel']; ?>';
                        }
                    },
                    {
                        'data': 'nombre_com',
                        'render': function (data, type, row) {
                            if (row.estado === 0) {
                                return `<label class="nombre_user_rem" style="padding: 5px;">${data}</label>`;
                            } else if (row.estado === 2) {
                                return `${data} <span><a href="admin_cont.php?controller=index&action=view_remesa&id=${row.id_remesa}&ano=${row.ano_remesa}" style="color: red; text-decoration: underline;"><br>(Rechazado Remesa ${row.id_remesa}/${row.ano_remesa})</a></span>`;
                            } else {
                                return `${data} <span><a href="admin_cont.php?controller=index&action=view_remesa&id=${row.id_remesa}&ano=${row.ano_remesa}" style="color: #012970; text-decoration: underline;"><br>(Remesa ${row.id_remesa})</a></span>`;
                            }
                        }
                    },
                    { 'data': 'valor_doc' },
                    {
                        'data': 'id_usuario',
                        'render': function (data) {
                            return `
                            <a href="admin_cont.php?controller=index&action=update_candidato&id=${data}&gen_rem">
                                <div class="hvr-icon" >
                                    <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                </div>
                            </a>`;
                        },
                        'orderable': false
                    }
                ],
				'lengthMenu': [[15, 30, -1], [15, 30, "All"]],
                'language': {
                    "search": "",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ resultados",
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
                'order': [1, 'asc'],
                'initComplete': function () {
                    restoreCheckboxStates();
                }
            });

            // Handle click on checkbox to set state
            $('#demo-table tbody').on('change', 'input[type="checkbox"]', function () {
                var checkbox = $(this);
                var idUsuario = checkbox.val();
                checkboxStates[idUsuario] = checkbox.prop('checked');
                console.log(checkboxStates);
                console.log("<?php echo $lang['traba_select']; ?> " + countSelectedCheckboxes());
                if (countSelectedCheckboxes()>0) {
                    $('#user_selec').html(countSelectedCheckboxes() + " <?php echo $lang['cand_select']; ?>");
                    $('#generar_rem').css({'display' : 'inline'});
                }else{
                    $('#user_selec').html("");
                    $('#generar_rem').css({'display' : 'none'});
                }
                
            });

            // Handle table draw event to restore state of checkboxes
            table.on('draw', function () {
                restoreCheckboxStates();
            });

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

            function countSelectedCheckboxes() {
                let count = 0;
                for (const id in checkboxStates) {
                    if (checkboxStates[id]) {
                        count++;
                    }
                }
                return count;
            }
        });
    </script>

</main>

<?php 
//Si hay algún mensaje emergente, lo mostramos
if (isset($params['resultado']) and $params['resultado']!="") {
	echo '<div id="emergente">'.$params['resultado'].'</div>';
}
?>
  <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Template Main JS File -->
    <script src="assets/vendor/tinymce/tinymce.min.js"></script>
    <script src="assets/js/main.js"></script>

  <!-- Mis JS -->
	<script type="text/javascript" src="js/jquery.lightbox_me.js"></script>
	<script type="text/javascript" src="js/script.js?ver=1.6"></script>
	<script src="js/alertify.min.js"></script>
	<script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>
</body>

</html>