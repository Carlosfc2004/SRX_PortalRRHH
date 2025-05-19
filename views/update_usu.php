<?php 
include_once("header.php");
?>
	<div class="pagetitle">
		<h1><?php echo $lang['titu_mod_usu']; ?></h1>
		<button type="submit" class="atras">
			<a class="bi bi-arrow-left-square-fill" style="margin-top: -80px;" href="admin_cont.php?controller=index&action=usuarios"></a>
		</button>
	</div>
	<nav>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=usuarios"><?php echo $lang['menu14']; ?></a></li>
			<li class="breadcrumb-item active"><?php echo $lang['titu_raiz_usu']; ?></li>
		</ol>
	</nav>

	


	<section class="section profile">
		<div class="row">
			<div class="col-xl-4">
				<div class="card">
					<div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
						<i class="bi bi-person-circle fs-1" style="color: #2c384e;"></i>
						<h2><?php echo $params['info_user']['nombre'] ." ". $params['info_user']['apellidos']; ?></h2>
						<h3 class="mt-2">
							<?php 
								if (isset($params['info_user']['departamento'])) {
									echo $params['info_user']['departamento'];
								}
							?>
						</h3>
					</div>
				</div>
			</div>

			<div class="col-xl-8">
				<div class="card">
					<div class="card-body pt-3">
						<!-- Pestañas -->
							<ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link active" data-bs-toggle="tab" data-bs-target="#datos_perfil" aria-selected="true" role="tab">Datos</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit" aria-selected="false" role="tab" tabindex="-1">Editar perfil</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-permisos" aria-selected="false" role="tab" tabindex="-1">Permisos</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password" aria-selected="false" role="tab" tabindex="-1">Cambiar contraseña</button>
								</li>
							</ul>
						<!-- Fin Pestañas -->

						<div class="tab-content pt-2">
							<!-- Pestaña de datos -->
								<div class="tab-pane fade profile-overview active show" id="datos_perfil" role="tabpanel">
									<h5 class="card-title">Detalles del perfil</h5>
									<form action="" method="post">
										<div class="row">
											<div class="col-lg-3 col-md-4 label">Nombre completo</div>
											<div class="col-lg-9 col-md-8"><?php echo $params['info_user']['nombre'] ." ". $params['info_user']['apellidos']; ?></div>
										</div>

										<div class="row">
											<div class="col-lg-3 col-md-4 label">Compañia</div>
											<div class="col-lg-9 col-md-8">Surexport C.A. S.L.</div>
										</div>

										<div class="row">
											<div class="col-lg-3 col-md-4 label">Email</div>
											<div class="col-lg-9 col-md-8">
											<?php 
												if (isset($params['info_user']['usr_login'])) {
													echo $params['info_user']['usr_login'];
												} 
											?>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-3 col-md-4 label">Teléfono</div>
											<div class="col-lg-9 col-md-8">
											<?php 
												if (isset($params['info_user']['telefono'])) {
												echo $params['info_user']['telefono'];
												}
											?>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-3 col-md-4 label">Departamento</div>
											<div class="col-lg-9 col-md-8">
												<?php 
													if (isset($params['info_user']['departamento'])) {
													echo $params['info_user']['departamento'];
													}
												?>
											</div>
										</div>

										<div class="row">
											<div class="col-lg-3 col-md-4 label">Tipo Usuario</div>
											<div class="col-lg-9 col-md-8">
												<?php 
													if (isset($params['info_user']['tipo'])) {
													echo $params['info_user']['tipo'];
													}
												?>
											</div>
										</div>
									</form>
								</div>
							<!-- Fin Pestaña de datos -->


							<!-- Pestaña Editar Perfil -->
								<div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel">
									<form action="admin_cont.php?controller=index&action=update_usu&id=<?php echo $_GET['id']; ?>" method="post">
										<input type="hidden" name="datos" value="1">
										<input type="hidden" name="id_usu" value="<?php echo $_GET['id']; ?>">
										<div class="row mb-3">
											<label for="nombre" class="col-md-4 col-lg-3 col-form-label">Nombre *</label>
											<div class="col-md-8 col-lg-9">
												<input name="nombre" type="text" class="form-control" id="nombre" value="<?php echo $params['info_user']['nombre']; ?>" required> 
											</div>
										</div>

										<div class="row mb-3">
											<label for="apellidos" class="col-md-4 col-lg-3 col-form-label">Apellidos *</label>
											<div class="col-md-8 col-lg-9">
												<input name="apellidos" type="text" class="form-control" id="apellidos" value="<?php echo $params['info_user']['apellidos']; ?>" required> 
											</div>
										</div>

										<div class="row mb-3">
											<label for="company" class="col-md-4 col-lg-3 col-form-label">Compañía</label>
											<div class="col-md-8 col-lg-9">
												<input name="compañia" type="text" class="form-control" id="compañia" value="Surexport C.A. S.L." readonly style="background-color: #dedede;">
											</div>
										</div>

										<div class="row mb-3">
											<label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
											<div class="col-md-8 col-lg-9">
												<input name="email" type="email" class="form-control" id="Email" 
												value="
												<?php 
												if (isset($params['info_user']['usr_login'])) {
													echo $params['info_user']['usr_login'];
												} 
												?>" readonly style="background-color: #dedede;">
											</div>
										</div>

										<div class="row mb-3">
											<label for="Email" class="col-md-4 col-lg-3 col-form-label">Tipo Usuario *</label>
											<div class="col-md-8 col-lg-9">
												<select name="tipo_usuario" id="tipo_usr" class="form-select" required>
													<option value="Administrador" <?php if ($params['info_user']['tipo']=="Administrador") {echo "selected";} ?>><?php echo $lang['admin']; ?></option>
													<option value="Usuario" <?php if ($params['info_user']['tipo']=="Usuario") {echo "selected";} ?>><?php echo $lang['usu']; ?></option>
													<option value="Supervisor" <?php if ($params['info_user']['tipo']=="Supervisor") {echo "selected";} ?>>Supervisor</option>
												</select>
											</div>
										</div>

										<div class="row mb-3">
											<label for="Job" class="col-md-4 col-lg-3 col-form-label">Departamento</label>
											<div class="col-md-8 col-lg-9">
												<input name="departamento" type="text" class="form-control" id="departamento" value="<?php echo isset($params['info_user']['departamento']) ? trim($params['info_user']['departamento']) : ''; ?>">
											</div>
										</div>

										<div class="row mb-3">
											<label for="Phone" class="col-md-4 col-lg-3 col-form-label">Telefono</label>
											<div class="col-md-8 col-lg-9">
												<input name="telefono" type="text" class="form-control" id="telefono" value="<?php echo isset($params['info_user']['telefono']) ? trim($params['info_user']['telefono']) : ''; ?>" required>
											</div>
										</div>

										<div class="text-center">
											<button type="submit" class="btn btn-primary">Guardar cambios</button>
										</div>
									</form>
									<!-- End Profile Edit Form -->
								</div>
							<!-- Fin Pestaña Editar Perfil -->


							<!-- Pestaña permisos -->
								<div class="tab-pane fade" id="profile-permisos" role="tabpanel">
									<h5 class="card-title"><?php echo $lang['select_perm']; ?></h5>
									<form action="admin_cont.php?controller=index&action=update_usu&id=<?php echo $_GET['id']; ?>" method="post">
										<input type="hidden" name="permisos" value="1">
										<input type="hidden" name="id_usu" value="<?php echo $_GET['id']; ?>">
										<ul class="nav nav-tabs d-flex" id="myTabjustified" role="tablist">
											<li class="nav-item flex-fill" role="presentation">
												<button class="nav-link w-100 active" id="home-tab" data-bs-toggle="pill" data-bs-target="#home-permisos" type="button" role="tab" aria-controls="home-permisos" aria-selected="true">Permisos</button>
											</li>
											<li class="nav-item flex-fill" role="presentation">
												<button class="nav-link w-100" id="home-tab" data-bs-toggle="pill" data-bs-target="#home-llamamientos" type="button" role="tab" aria-controls="home-llamamientos" aria-selected="false" tabindex="-1">Llamamiento</button>
											</li>
											<li class="nav-item flex-fill" role="presentation">
												<button class="nav-link w-100" id="home-tab" data-bs-toggle="pill" data-bs-target="#home-reclutamiento" type="button" role="tab" aria-controls="home-reclutamiento" aria-selected="false" tabindex="-1">Reclutamiento</button>
											</li>
											<li class="nav-item flex-fill" role="presentation">
												<button class="nav-link w-100" id="home-tab" data-bs-toggle="pill" data-bs-target="#home-presencia" type="button" role="tab" aria-controls="home-presencia" aria-selected="false" tabindex="-1">Presencia</button>
											</li>
											<li class="nav-item flex-fill" role="presentation">
												<button class="nav-link w-100" id="home-tab" data-bs-toggle="pill" data-bs-target="#home-tesa" type="button" role="tab" aria-controls="home-tesa" aria-selected="false" tabindex="-1">TESA</button>
											</li>
										</ul>

										<div class="tab-content pt-2" id="myTabContent">
											<!-- Permisos -->
											<div class="tab-pane fade show active" id="home-permisos" role="tabpanel" aria-labelledby="home-tab">
												<div class="row mt-3">
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="1" <?php if (in_array(1, $params['permisos'])) {echo "checked";} ?> ><span><?php echo $lang['menu1']; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="2" <?php if (in_array(2, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu2']; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="28" <?php if (in_array(28, $params['permisos'])) {echo "checked";} ?>> <span>Solicitudes</span>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="15,26,27" <?php if (in_array(15, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu14']; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="25" <?php if (in_array(25, $params['permisos'])) {echo "checked";} ?>> <span>Auditoría</span>
														</div>
													</div>
												</div>
											</div>

											<!-- Llamamientos -->
											<div class="tab-pane fade" id="home-llamamientos" role="tabpanel" aria-labelledby="profile-tab">
												<div class="row mt-3">
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="3" <?php if (in_array(3, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu3']; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="4" <?php if (in_array(4, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu3'].' ('.$lang['menu20'].')'; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="5" <?php if (in_array(5, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu3'].' ('.$lang['menu4'].')'; ?></span>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="6" <?php if (in_array(6, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu3'].' ('.$lang['menu5'].')'; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="7" <?php if (in_array(7, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu3'].' ('.$lang['menu6'].')'; ?></span>
														</div>
													</div>
												</div>
											</div>

											<!-- Reclutamiento -->
											<div class="tab-pane fade" id="home-reclutamiento" role="tabpanel" aria-labelledby="contact-tab">
												<div class="row mt-3">
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="8" <?php if (in_array(8, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu7']; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="9" <?php if (in_array(9, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu7'].' ('.$lang['menu8'].')'; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="10" <?php if (in_array(10, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu7'].' ('.$lang['menu9'].')'; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="11" <?php if (in_array(11, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu7'].' ('.$lang['menu10'].')'; ?></span>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="12" <?php if (in_array(12, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu7'].' ('.$lang['menu11'].')'; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="13" <?php if (in_array(13, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu7'].' ('.$lang['menu12'].')'; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="14" <?php if (in_array(13, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu7'].' ('.$lang['menu13'].')'; ?></span>
														</div>
													</div>
												</div>
											</div>

											<!-- Control de presencia -->
											<div class="tab-pane fade" id="home-presencia" role="tabpanel" aria-labelledby="profile-tab">
												<div class="row mt-3">
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="16" <?php if (in_array(16, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu21']; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="17" <?php if (in_array(17, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu21'].' ('.$lang['menu22'].')'; ?></span>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="18" <?php if (in_array(18, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu21'].' ('.$lang['menu23'].')'; ?></span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="19" <?php if (in_array(19, $params['permisos'])) {echo "checked";} ?>> <span><?php echo $lang['menu21'].' ('.$lang['menu24'].')'; ?></span>
														</div>
													</div>
												</div>
											</div>

											<!-- TESA -->
											<div class="tab-pane fade" id="home-tesa" role="tabpanel" aria-labelledby="contact-tab">
												<div class="row mt-3">
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="20" <?php if (in_array(20, $params['permisos'])) {echo "checked";} ?>> <span>TESA</span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="21" <?php if (in_array(21, $params['permisos'])) {echo "checked";} ?>> <span>TESA (<?php echo $lang['menu14'] ?> TESA)</span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="22" <?php if (in_array(22, $params['permisos'])) {echo "checked";} ?>> <span>TESA (<?php echo $lang['menu16'] ?>)</span>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="23" <?php if (in_array(23, $params['permisos'])) {echo "checked";} ?>> <span>TESA (Estado puertas)</span>
														</div>
														<div class="form-check form-switch mt-1">
															<input type="checkbox" class="form-check-input" name="permisos[]" value="24" <?php if (in_array(24, $params['permisos'])) {echo "checked";} ?>> <span>TESA (Actualizar usuario)</span>
														</div>
													</div>
												</div>
											</div>
										</div>
										<input type="submit" value="Modificar permisos" class="btn btn-primary mt-4" >
									</form>
								</div>
							<!-- Fin pestaña permisos -->


							<!-- Pestaña cambio de contraseña -->
								<div class="tab-pane fade" id="profile-change-password" role="tabpanel">
									<h5 class="card-title"><?php echo $lang['mod_contra']; ?></h5>
									<button type="button" onclick="alertify.confirm('¿Realmente deseas generar nuevas contraseñas para este usuario?', function(){$(location).attr('href','admin_cont.php?controller=index&action=update_usu&id=<?php echo $params['info_user']['id']; ?>&renew_pass')});" class="btn btn-primary">
										<span><?php echo $lang['gen_contra']; ?></span>
									</button>
								</div>
							<!-- Fin pestaña cambio de contraseña -->
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>


<?php include_once("footer.php"); ?>