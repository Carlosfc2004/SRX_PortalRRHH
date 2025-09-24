<?php 
include_once("header.php");
?>


        
	<div class="pagetitle">
		<h1><?php echo $lang['menu5']; ?></h1>
	</div>
	<nav>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
			<li class="breadcrumb-item"><?php echo $lang['menu3']; ?></li>
			<li class="breadcrumb-item active"><?php echo $lang['menu5']; ?></li>
		</ol>
	</nav>
		<section class="section">
			<div class="card">
				<div class="card-body">
				<br>	
					<?php				
						if (!empty($params['remesas'])) {
					?>
						<table class="table datatable" id="tabla_rem_llama">
							<thead>
								<tr>
									<div class="col-12">
										<th class="col-2"><?php echo $lang['nombre_rem2']; ?></th>
										<th class="col-2"><?php echo $lang['num_trab_rem']; ?></th>
										<th class="col-2"><?php echo $lang['Fecha_creacion']; ?></th>
										<th class="col-2">Fecha jornada</th>
										<th class="col-2"><?php echo $lang['estado']." ".$lang['rem']; ?></th>
										<th class="col-1"></th>
									</div>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach ($params['remesas'] as $resultado){
								?>
									<tr>		
										<td>
											<?php 
												if ($resultado['sms_auto'] == '0') {echo '<i class="bi bi-hand-index-thumb"></i> '; }
											    echo $resultado['nombre_remesa']." ".$resultado['id_remesa']."/".$resultado['ano_remesa']; 
											?>
										</td>
										<td>
											<?php echo $resultado['trabajadores']; ?>
										</td>
										<td>
											<?php echo date_format($resultado['fecha_remesa'], 'd-m-Y'); ?>
										</td>
										<td>
											<?php echo date_format($resultado['fecha_incorporacion'], 'd-m-Y'); ?>
										</td>
										<td>
											<?php 
												if ($resultado['estado_remesa'] === 1) {
													echo 'En proceso';
												} elseif ($resultado['estado_remesa'] === 2) {
													echo $lang['llama_sin_resp_rem'];
												} elseif ($resultado['estado_remesa'] === 3) {
													echo $lang['comple'];
												} elseif ($resultado['estado_remesa'] === 4) {
													echo 'Sin iniciar';
												} else {
													echo 'estado desconocido';
												}
											?>
										</td>
										<td>
											<li class="hvr-icon-forward">
												<a href="admin_cont.php?controller=index&action=view_remesa_llama&id=<?php echo $resultado['id_remesa']; ?>&ano=<?php echo $resultado['ano_remesa']; ?>&remesa=<?php echo $resultado['sms_auto']; ?>">
													<div class="hvr-icon" >
														<i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
													</div>
												</a>
											</li>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
				<?php	
				} else {
					echo "No existe ninguna remesa";
				}
				?>
				
			</div>
		</div>
	</section>
</div>


<?php 
include_once("footer.php");
?>
