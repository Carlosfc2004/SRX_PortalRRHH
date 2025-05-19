<?php 
include_once("header.php");
?>
		<div>
			<?php 
				if (isset($params['user_remesa'])) {
				echo '<h1 style="font-size: 24px; margin-bottom: 0; font-weight: 600; color: #012970;">'.$lang['rem'].' '.$_GET['id'].'/'.$_GET['ano'].' ('.count($params['user_remesa']).' '.$lang['menu8'].')</h1>'
			?>
		</div>
		<br>
			<nav>
				<ol class="breadcrumb">
					<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
					<li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
					<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=remesas"><?php echo $lang['menu12']; ?></a></li>
					<li class="breadcrumb-item active"><?php echo $lang['act_rem']; ?></li>
				</ol>
			</nav>
		
		<section class="section">
			<div class="card">
				<div class="card-body">
				<br>
			<?php
				if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
				echo '
				<b>'.$lang['exp_rem'].'</b>
				<a href="exportar.php?pdf" target="_blank"><img src="img/pdf.png" class="icono"></a>
				<a href="exportar.php?excel" target="_blank"><img src="img/xls.png" class="icono"></a><br><br>
				<form action="admin_cont.php?controller=index&action=generar_remesa" method="post" style="display: inline-block; margin-left: 15px;">
					<input type="hidden" name="id_remesa" value="'.$_GET['id'].'">
					<input type="hidden" name="ano_remesa" value="'.$_GET['ano'].'">
					<input type="submit" name="add_remesa" value="'.$lang['add_candidato'].'" class="btn btn-primary mb-2">
				</form><br>';
				}
				if (!empty($params['user_remesa'])) {
					?>
						<table class="table datatable" id="tabla_gen_emp">
							<thead>
								<tr>
									<th scope="col"><?php echo $lang['nom_candidato']; ?></th>
									<th scope="col"><?php echo $lang['num_doc']; ?></th>
									<th scope="col"></th>
								</tr>
							</thead>
							<tbody>
                                <?php
                                foreach ($params['user_remesa'] as $resultado) {
                                ?>
                                    <tr>
                                        <td>
										<?php 
											echo $resultado['nombre']." ".$resultado['apellido1']; 
											if ($resultado['estado_remesa']==2) {
												echo "<br><span style='color: red;'>".$lang['rechazado'].": ".$resultado['obser']."</span>";
											}
										?>
                                        </td>
                                        <td>
											<?php echo $resultado['valor_doc']; ?>
                                        </td>
                                        <td>
										<li class="hvr-icon-back">
											<a href="admin_cont.php?controller=index&action=update_candidato&id=<?php echo $resultado['id_usuario']; ?>&rem&id_rem=<?php echo $resultado['id_remesa']; ?>&ano_rem=<?php echo $resultado['ano_remesa']; ?>">
												<div class="hvr-icon" >
													<i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
												</div>
											</a>
										</li>
											<?php 
												if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
													if ($resultado['estado_remesa']==1) {
														echo '
														<li class="hvr-icon-forward">
															<a href="admin_cont.php?controller=index&action=view_remesa&id='.$_GET['id'].'&ano='.$_GET['ano'].'&elim_candidato='.$resultado['id_usuario'].'">
																<div class="hvr-icon">
																	<i class="bx bxs-trash fs-3" style="color: #99353a;"></i>
																</div>
															</a>
														</li>';
													}
												}
											?>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
							
					</div>
					<br>
					<?php
				}
			}else{
				?>
				<div class="pagetitle">
					<h1><?php echo $lang['menu5']; ?></h1>
				</div>
				<nav>
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
						<li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
						<li class="breadcrumb-item active"><?php echo $lang['menu12']; ?></li>
					</ol>
				</nav>
			  <section class="section">
					
					<?php
						if (!empty($params['remesas'])) {
							?>
							<div class="card">
								<div class="card-body">
								<br>	
									<table class="table datatable" id="tabla_rem">
                            			<thead>
											<tr>
												<div class="col-15">
													<th class="col-2"><?php echo $lang['num_rem']; ?></th>
													<th class="col-5"></th>
												</div>
											</tr>
										</thead>
									<tbody>
								<?php
							foreach ($params['remesas'] as $resultado){
						?>
							<tr>		
								<td>
									<span><?php echo $lang['rem']; ?> </span><?php echo $resultado['id_remesa']."/".$resultado['ano_remesa']; ?>
								</td>
								<td>
									<a href="admin_cont.php?controller=index&action=view_remesa&id=<?php echo $resultado['id_remesa']; ?>&ano=<?php echo $resultado['ano_remesa']; ?>">
										<div class="hvr-icon" >
											<i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
										</div>
									</a>
								</td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>
					<?php	
					}
				}
				?>
		</div>
	</div>
	</section>
</div>


<?php 
include_once("footer.php");
?>