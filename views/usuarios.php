<?php 
include_once("header.php");
?>
		<div class="div_cont">
			<div class="pagetitle">
				<h1><?php echo $lang['usu_sist']; ?></h1>
			</div>
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
					<li class="breadcrumb-item"><?php echo $lang['menu14']; ?></li>
					<li class="breadcrumb-item active"><?php echo $lang['menu15']; ?></li>
				</ol>
			</nav>
				<section class="section">
					<div class="row">
						<div class="col-lg-12">
							<div class="card">
								<div class="card-body">
          							<br>
          							<table class="table datatable" id="tabla_user">
	            						<thead>
											<tr>
												<div class="col-9">
													<th class="col-3"><?php echo $lang['nombre']; ?></th>
													<th class="col-2"><?php echo $lang['tipo']; ?></th>
													<th class="col-4"></th>
												</div>
											</tr>
										</thead>
									<tbody>
								<?php
									if (!empty($params['usuarios'])) {
										foreach ($params['usuarios'] as $resultado){
												?>
												<tr>
													<td>
														<?php echo $resultado['nombre']." ".$resultado['apellidos']; ?>
													</td>
													<td>
														<?php 
															if ($resultado['tipo'] === 'Usuario') {
																echo $lang['usu'];
															} elseif ($resultado['tipo'] === 'Administrador') {
																echo $lang['admin']; 
															} else {
																echo "Supervisor";
															}
														?>
													</td>
													<td>
														<li class="hvr-icon-back">
															<a href="admin_cont.php?controller=index&action=update_usu&id=<?php echo $resultado['id']; ?>">
																<div class="hvr-icon" >
																	<i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
																</div>
															</a>
														</li>
														<li class="hvr-icon-forward">
															<button onclick="alertify.confirm('¿Estas seguro de que quieres eliminar el usuario?', function(){$(location).attr('href','admin_cont.php?controller=index&action=usuarios&elim=<?php echo $resultado['id'] ?>')});" style="background-color: transparent; cursor: pointer;">
																<div class="hvr-icon">
																	<i class="bx bxs-trash fs-3" style="color: #99353a;"></i>
																</div>
															</button>
														</li>
													</td>
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
		</section>

<?php 
include_once("footer.php");
?>