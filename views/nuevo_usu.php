<?php 
include_once("header.php");
?>
	<div class="pagetitle">
		<h1><?php echo $lang['titu_new_usu']; ?></h1>
	</div>
	<nav>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=usuarios"><?php echo $lang['menu14']; ?></a></li>
			<li class="breadcrumb-item active"><?php echo $lang['menu16']; ?></li>
		</ol>
	</nav>
<section class="section">
    <div class="row">
      	<div class="col-lg-12">
        	<div class="card">
          		<div class="card-body">
          			<br>
						<form action="admin_cont.php?controller=index&action=new_usuario" method="post" class="row g-3">
							<div class="col-md-3">
								<?php echo $lang['nombre']; ?> <span style="font-size: 18px; font-family: Verdana;">*</span>
								<input type="text" name="nombre" class="form-control" required>
							</div>
							<div class="col-md-3">
								<?php echo $lang['apellidos']; ?> <span style="font-size: 18px; font-family: Verdana;">*</span>
								<input type="text" name="apellidos" class="form-control" required>
							</div>
							<div class="col-md-6">
								<?php echo $lang['email']; ?><span style="font-size: 18px; font-family: Verdana;">*</span>
								<input type="text" name="usr_login" class="form-control" required>
							</div>
							<div class="col-md-3">
								<?php echo $lang['contra']; ?> <span style="font-size: 18px; font-family: Verdana;">*</span>
								<input type="password" name="usr_pass" class="form-control" required>
							</div>
							<div class="col-md-3">
								<?php echo $lang['repetir_contra']; ?> <span style="font-size: 18px; font-family: Verdana;">*</span>
								<input type="password" name="usr_pass_rep" class="form-control" required>
							</div>
							<div class="col-md-3">
								<?php echo $lang['tipo_usu']; ?> <span style="font-size: 18px; font-family: Verdana;">*</span>
								<select name="tipo_usuario" id="tipo_usr" class="form-select" style="width: 100%; display: inline-block;" required>
									<option value=""></option>
									<option value="Administrador"><?php echo $lang['admin']; ?></option>
									<option value="Usuario"><?php echo $lang['usu']; ?></option>
									<option value="Supervisor">Supervisor</option>
								</select>
							</div>
							<div class="col-md-3">
								<?php echo $lang['telefono']; ?> <span style="font-size: 18px; font-family: Verdana;">*</span>
								<input type="number" name="telefono" class="form-control" required>
							</div>
							
							<div class="col-md-12">
								<br>
								<span><b><?php echo $lang['select_perm']; ?></b></span>
							</div>

							<!-- PERMISOS -->
							<div class="col-md-3">
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="1"> <span><?php echo $lang['menu1']; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="2"> <span><?php echo $lang['menu2']; ?></span>
								</div>
								<div class="form-check form-switch mt-1">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="28"> <span>Solicitudes</span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="15,26,27"> <span><?php echo $lang['menu14']; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="25"> <span>Auditoría</span>
								</div>
							</div>
							
							<!-- LLAMAMIENTOS -->
							<div class="col-md-3">	
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="3"> <span><?php echo $lang['menu3']; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="4"> <span><?php echo $lang['menu3'].' ('.$lang['menu20'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="5"> <span><?php echo $lang['menu3'].' ('.$lang['menu4'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="6"> <span><?php echo $lang['menu3'].' ('.$lang['menu5'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="7"> <span><?php echo $lang['menu3'].' ('.$lang['menu6'].')'; ?></span>
								</div>
							</div>

							<!-- RECLUTAMIENTO -->
							<div class="col-md-3">
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="8"> <span><?php echo $lang['menu7']; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="9"> <span><?php echo $lang['menu7'].' ('.$lang['menu8'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="10"> <span><?php echo $lang['menu7'].' ('.$lang['menu9'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="11"> <span><?php echo $lang['menu7'].' ('.$lang['menu10'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="12"> <span><?php echo $lang['menu7'].' ('.$lang['menu11'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="13"> <span><?php echo $lang['menu7'].' ('.$lang['menu12'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="14"> <span><?php echo $lang['menu7'].' ('.$lang['menu13'].')'; ?></span>
								</div>
							</div>

							<!-- CONTROL PRESENCIA -->
							<div class="col-md-3">
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="16"> <span><span><?php echo $lang['menu21']; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="17"> <span><span><?php echo $lang['menu21'].' ('.$lang['menu22'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="18"> <span><span><?php echo $lang['menu21'].' ('.$lang['menu23'].')'; ?></span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="19"> <span><span><?php echo $lang['menu21'].' ('.$lang['menu24'].')'; ?></span>
								</div>
							</div>

							<!-- TESA -->
							<div class="col-md-3">
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="20"> <span><span>TESA</span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="21"> <span><span>TESA (<?php echo $lang['menu14'] ?> TESA)</span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="22"> <span><span>TESA (<?php echo $lang['menu16'] ?>)</span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="24"> <span><span>TESA (Actualizar usuario)</span>
								</div>
								<div class="form-check form-switch">
									<input type="checkbox" class="form-check-input" name="permisos[]" value="23"> <span><span>TESA (Estado puertas)</span>
								</div>
							</div>
							
							<div class="col-md-12">
								<input type="submit" name="insert_cont" value="<?php echo $lang['insert']; ?>" class="btn btn-primary" >
							</div>
						</form>

						<!-- Primero añadimos el HTML del modal -->
						<div class="modal fade" id="errorPermisos" tabindex="-1" aria-labelledby="errorPermisosLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="errorPermisosLabel"><?php echo $lang['form_in'] ?></h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<?php echo $lang['select_perm_1'] ?>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo $lang['ok'] ?></button>
								</div>
								</div>
							</div>
						</div>

						<!-- Luego el JavaScript para la validación -->
						<script>
						document.addEventListener('DOMContentLoaded', function() {
							const form = document.querySelector('form');
							form.id = 'userForm';
							
							// Crear instancia del modal
							const errorModal = new bootstrap.Modal(document.getElementById('errorPermisos'));
							
							form.addEventListener('submit', function(e) {
								// Obtener todos los checkboxes de permisos
								const permisosCheckboxes = document.querySelectorAll('input[name="permisos[]"]');
								
								// Verificar si al menos uno está seleccionado
								const alMenosUnoSeleccionado = Array.from(permisosCheckboxes).some(checkbox => checkbox.checked);
								
								// Si ninguno está seleccionado, prevenir el envío y mostrar modal
								if (!alMenosUnoSeleccionado) {
									e.preventDefault();
									errorModal.show();
									return false;
								}
								
								return true;
							});
						});
						</script>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php include_once("footer.php"); ?>