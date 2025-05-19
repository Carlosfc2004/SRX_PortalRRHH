<?php 
include_once("header.php"); 
?>
	<div class="pagetitle">
    	<h1><?php echo $lang['act_candi']; ?>
			<a href="exportar.php?pdf&id=<?php echo $_GET['id']; ?>" target="_blank"><img src='img/pdf.png' class='icono'></a>
		</h1>
		<button type="submit" class="atras">
			<?php 
				if (isset($_GET['gen_rem'])) {
					echo "<a class='bi bi-arrow-left-square-fill' href='admin_cont.php?controller=index&action=generar_remesa'></a>";
				} elseif (isset($_GET['rem'])) {
					$id_remesa = isset($_GET['id_rem']);
					$ano_remesa = isset($_GET['ano_rem']);
					echo "<a class='bi bi-arrow-left-square-fill' href='admin_cont.php?controller=index&action=view_remesa&id=".$id_remesa."&ano=".$ano_remesa."'></a>";
				} else {
					echo "<a class='bi bi-arrow-left-square-fill' href='admin_cont.php?controller=index&action=candidatos'></a>";
				}
			?>
		</button>
	</div>
	<nav>
        <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=candidatos"><?php echo $lang['menu8']; ?></a></li>
            <li class="breadcrumb-item active"><?php echo $lang['act_candi']; ?></li>
        </ol>
    </nav>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
					<br>
				<form class="row g-3" action="admin_cont.php?controller=index&action=update_candidato&id=<?php echo $_GET['id'] ?>" method="post" enctype="multipart/form-data">
				<h4><?php echo $lang['datos_personales_contacto']; ?></h4>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['nombre']; ?> *</b></label>
					<input type="text" name="NOMBRE" class="form-control" value="<?php echo $params['info_candidato']['nombre']; ?>" required>
					<input type="hidden" name="ID" value="<?php echo $params['info_candidato']['id']; ?>">					
				</div>
				<br>
				<div class="col-md-3">
				<label class="form-label"><b><?php echo $lang['apellido']; ?> 1 *</b></label>
					<input type="text" name="APELLIDO1" class="form-control" value="<?php echo $params['info_candidato']['apellido1']; ?>" required>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['apellido']; ?> 2 *</b></label>
					<input type="text" name="APELLIDO2" class="form-control" value="<?php echo $params['info_candidato']['apellido2']; ?>">
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['sexo']; ?> *</b></label>
					<select name="SEXO" class="form-select" required>
						<option value=""><?php echo $lang['sexo']; ?></option>
						<option value="Masculino" <?php if($params['info_candidato']['sexo']=="Masculino"){echo "selected";} ?>><?php echo $lang['masculino']; ?></option>
						<option value="Femenino" <?php if($params['info_candidato']['sexo']=="Femenino"){echo "selected";} ?>><?php echo $lang['femenino']; ?></option>
					</select>
				</div>

				<div class="col-md-3">
				<label class="form-label"><b><?php echo $lang['fecha_nac']; ?> *</b></label>
					<input type="date" name="FECHA_NACIMIENTO" class="form-control" value="<?php echo date_format($params['info_candidato']['fecha_nac'], 'Y-m-d'); ?>" required>
				</div>
				<div class="col-md-3">
				<label class="form-label"><b><?php echo $lang['lug_nac']; ?> *</b></label>
					<input type="text" name="LUGAR_NACIMIENTO" class="form-control" value="<?php echo $params['info_candidato']['lugar_nac']; ?>" required>
				</div>
				
				<div class="col-md-3">
				<label class="form-label"><b><?php echo $lang['pais_nac']; ?> *</b></label>
					<select name="PAIS_NACIMIENTO" class="form-select" required>
						<option value=""><?php echo $lang['select_pais']; ?></option>
						<?php 
						foreach ($params['paises'] as $value) {
							echo '<option value="'.$value['PAIS_NAC'].'" ';
							if ($value['PAIS_NAC']==$params['info_candidato']['pais_nac']) {
								echo 'selected';
							}
							echo '>'.$value['PAIS_NAC'].'</option>';
						}
						?>
					</select>
				</div>
				<div class="col-md-3">
				<label class="form-label"><b><?php echo $lang['nacionalidad']; ?> *</b></label>
					<select name="NACIONALIDAD" class="form-select" required>
						<option value=""><?php echo $lang['select_nac']; ?></option>
						<?php 
						foreach ($params['nacionalidad'] as $value) {
							echo '<option value="'.$value['GENTILICIO_NAC'].'" ';
							if ($value['GENTILICIO_NAC']==$params['info_candidato']['nacionalidad']) {
								echo 'selected';
							}
							echo '>'.$value['GENTILICIO_NAC'].'</option>';
						}
						?>
					</select>
				</div><div class="col-md-3">
				<label class="form-label"><b><?php echo $lang['telefono']; ?> *</b></label>
					<input type="text" name="TELEFONO" class="form-control" value="<?php echo $params['info_candidato']['telf']; ?>" required>
				</div>
				
				<div class="col-md-9">
					<label class="form-label"><b><?php echo $lang['email']; ?> *</b></label>
					<input type="text" name="MAIL" class="form-control" value="<?php echo $params['info_candidato']['mail']; ?>" required>
				</div>
				<div class="clear"></div>

				</div>
			</div>

			<div class="card">
            	<div class="card-body">
				<br>
				<h4><?php echo $lang['documentacion']; ?></h4>
				<div class="clear"><br></div>
				<div class="row mb-md-3">
					<div class="col-md-4">
						<label class="form-label"><b><?php echo $lang['tip_doc']; ?> *</b></label>
						<select name="TIPO_DOCUMENTO" class="form-select" required>
							<option value="0" <?php if($params['info_candidato']['tipo_doc']=="0"){echo "selected";} ?>><?php echo $lang['select']; ?></option>
							<option value="1" <?php if($params['info_candidato']['tipo_doc']=="1"){echo "selected";} ?>><?php echo $lang['dni']; ?></option>
							<option value="2" <?php if($params['info_candidato']['tipo_doc']=="2"){echo "selected";} ?>><?php echo $lang['pasaporte']; ?></option>
							<option value="3" <?php if($params['info_candidato']['tipo_doc']=="3"){echo "selected";} ?>><?php echo $lang['tarj_residente']; ?></option>
							<option value="4" <?php if($params['info_candidato']['tipo_doc']=="4"){echo "selected";} ?>><?php echo $lang['perm_residente']; ?></option>
							<option value="5" <?php if($params['info_candidato']['tipo_doc']=="5"){echo "selected";} ?>><?php echo $lang['nie']; ?></option>
							<option value="6" <?php if($params['info_candidato']['tipo_doc']=="6"){echo "selected";} ?>><?php echo $lang['num_segsoc']; ?></option>
							<option value="7" <?php if($params['info_candidato']['tipo_doc']=="7"){echo "selected";} ?>><?php echo $lang['tarj_nac_id']; ?></option>
							<!-- <option value="IN" <?php if($params['info_candidato']['tipo_doc']=="IN"){echo "selected";} ?>>Número de identidad Portugal</option>
							<option value="FN" <?php if($params['info_candidato']['tipo_doc']=="FN"){echo "selected";} ?>>Número fiscal PTR Pasaporte</option>
							<option value="DPO" <?php if($params['info_candidato']['tipo_doc']=="DPO"){echo "selected";} ?>>Documento del país de Origen PRT</option>
							<option value="PAS" <?php if($params['info_candidato']['tipo_doc']=="PAS"){echo "selected";} ?>>Pasaporte PRT</option> -->
						</select>
					</div>
					<div class="col-md-4">
						<label class="form-label"><b><?php echo $lang['num_doc']; ?> *</b></label>
						<input type="text" name="VALOR_DOCUMENTO" class="form-control" value="<?php echo $params['info_candidato']['valor_doc']; ?>" required>
					</div>
				</div>

				<div class="row mb-3">
					<div class="col-md-4">
					<label class="form-label"><b><?php echo $lang['tip_doc']; ?> *</b></label>
						<select name="TIPO_DOCUMENTO_2" class="form-select">
							<option value="0" <?php if($params['info_candidato']['tipo_doc_2']=="0"){echo "selected";} ?>><?php echo $lang['select']; ?></option>
							<option value="1" <?php if($params['info_candidato']['tipo_doc_2']=="1"){echo "selected";} ?>><?php echo $lang['dni']; ?></option>
							<option value="2" <?php if($params['info_candidato']['tipo_doc_2']=="2"){echo "selected";} ?>><?php echo $lang['pasaporte']; ?></option>
							<option value="3" <?php if($params['info_candidato']['tipo_doc_2']=="3"){echo "selected";} ?>><?php echo $lang['tarj_residente']; ?></option>
							<option value="4" <?php if($params['info_candidato']['tipo_doc_2']=="4"){echo "selected";} ?>><?php echo $lang['perm_residente']; ?></option>
							<option value="5" <?php if($params['info_candidato']['tipo_doc_2']=="5"){echo "selected";} ?>><?php echo $lang['nie']; ?></option>
							<option value="SSN" <?php if($params['info_candidato']['tipo_doc_2']=="SSN"){echo "selected";} ?>><?php echo $lang['num_segsoc']; ?></option>
							<option value="CIN" <?php if($params['info_candidato']['tipo_doc_2']=="CIN"){echo "selected";} ?>><?php echo $lang['tarj_nac_id']; ?></option>
						</select>
					</div>
					<div class="col-md-4">
						<label class="form-label"><b><?php echo $lang['num_doc']; ?> *</b></label>
						<input type="text" name="VALOR_DOCUMENTO_2" class="form-control" value="<?php echo $params['info_candidato']['valor_doc_2']; ?>">
					</div>
				</div>


				<div class="row mb-3">
					<div class="col-md-4">
						<label class="form-label"><b><?php echo $lang['tip_doc']; ?> *</b></label>
						<select name="TIPO_DOCUMENTO_3" class="form-select">
							<option value="0" <?php if($params['info_candidato']['tipo_doc_3']=="0"){echo "selected";} ?>><?php echo $lang['select']; ?></option>
							<option value="1" <?php if($params['info_candidato']['tipo_doc_3']=="1"){echo "selected";} ?>><?php echo $lang['dni']; ?></option>
							<option value="2" <?php if($params['info_candidato']['tipo_doc_3']=="2"){echo "selected";} ?>><?php echo $lang['pasaporte']; ?></option>
							<option value="3" <?php if($params['info_candidato']['tipo_doc_3']=="3"){echo "selected";} ?>><?php echo $lang['tarj_residente']; ?></option>
							<option value="4" <?php if($params['info_candidato']['tipo_doc_3']=="4"){echo "selected";} ?>><?php echo $lang['perm_residente']; ?></option>
							<option value="5" <?php if($params['info_candidato']['tipo_doc_3']=="5"){echo "selected";} ?>><?php echo $lang['nie']; ?></option>
							<option value="SSN" <?php if($params['info_candidato']['tipo_doc_3']=="SSN"){echo "selected";} ?>><?php echo $lang['num_segsoc']; ?></option>
							<option value="CIN" <?php if($params['info_candidato']['tipo_doc_3']=="CIN"){echo "selected";} ?>><?php echo $lang['tarj_nac_id']; ?></option>
						</select>
					</div>
					<div class="col-md-4">
						<label class="form-label"><b><?php echo $lang['num_doc']; ?> *</b></label>
						<input type="text" name="VALOR_DOCUMENTO_3" class="form-control" value="<?php echo $params['info_candidato']['valor_doc_3']; ?>">
					</div>
				</div>
			</div>
		</div>






		<div class="card">
            <div class="card-body">
				<br>
				<h4><?php echo $lang['otros_datos']; ?></h4>
				<div class="clear"><br></div>
				<div class="row mb-4">
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['poblacion']; ?></b></label>
						<input type="text" name="POBLACION" class="form-control" value="<?php echo $params['info_candidato']['poblacion']; ?>">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['cod_post']; ?></b></label>
						<input type="text" name="CODIGO_POSTAL" class="form-control" value="<?php echo $params['info_candidato']['cod_postal']; ?>">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['via']; ?></b></label>
						<input type="text" name="SIGLA_VIA" class="form-control" value="<?php echo $params['info_candidato']['sigla_via']; ?>">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['calle']; ?></b></label>
						<input type="text" name="CALLE" class="form-control" value="<?php echo $params['info_candidato']['calle']; ?>">
					</div>
					
					<div class="clear"><br></div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['num_edificio']; ?></b></label>
						<input type="text" name="NUM_EDIFICIO" class="form-control" value="<?php echo $params['info_candidato']['num_edificio']; ?>">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['distrito']; ?></b></label>
						<input type="text" name="DISTRITO" class="form-control" value="<?php echo $params['info_candidato']['distrito']; ?>">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['region']; ?></b></label>
						<input type="text" name="REGION" class="form-control" value="<?php echo $params['info_candidato']['region']; ?>">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['estado_civil']; ?> *</b></label>
						<select name="ESTADO_CIVIL" class="form-select" required>
							<option value=""><?php echo $lang['select']; ?></option>
							<option value="0" <?php if($params['info_candidato']['estado_civil']=="0"){echo "selected";} ?>><?php echo $lang['soltero']; ?></option>
							<option value="1" <?php if($params['info_candidato']['estado_civil']=="1"){echo "selected";} ?>><?php echo $lang['casado']; ?></option>
							<option value="2" <?php if($params['info_candidato']['estado_civil']=="2"){echo "selected";} ?>><?php echo $lang['viudo']; ?></option>
							<option value="3" <?php if($params['info_candidato']['estado_civil']=="3"){echo "selected";} ?>><?php echo $lang['divorciado']; ?></option>
							<option value="4" <?php if($params['info_candidato']['estado_civil']=="4"){echo "selected";} ?>><?php echo $lang['separado']; ?></option>
						</select>
					</div>
					
					<div class="clear"><br></div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['hijos']; ?></b></label>
						<input type="text" name="NUMERO_HIJOS" class="form-control" value="<?php echo $params['info_candidato']['num_hijos']; ?>">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['nom_padre']; ?> *</b></label>
						<input type="text" name="NOMBRE_PADRE" class="form-control" value="<?php echo $params['info_candidato']['nombre_padre']; ?>" required>
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['nom_madre']; ?> *</b></label>
						<input type="text" name="NOMBRE_MADRE" class="form-control" value="<?php echo $params['info_candidato']['nombre_madre']; ?>" required>
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['cuali']; ?></b></label>
						<input type="text" name="CUALIFICACION" class="form-control" value="<?php echo $params['info_candidato']['cualificacion']; ?>">
					</div>
					
					<div class="clear"><br></div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['expe']; ?></b></label><br>
						<input type="radio" class="form-check-input" name="EXPERIENCIA" value="1" <?php if ($params['info_candidato']['experiencia']==1) {echo 'checked';} ?>>
							<label class="form-check-label">
								<?php echo $lang['si']; ?>
							</label>
						<input type="radio" class="form-check-input" name="EXPERIENCIA" value="0" style="margin-left: 10px;" <?php if ($params['info_candidato']['experiencia']==0) {echo 'checked';} ?>>
							<label class="form-check-label">
								<?php echo $lang['no']; ?>
							</label>
					</div>
					<div class="col-md-4">
						<label class="form-label"><b><?php echo $lang['anteriormente']; ?></b></label>
						<br>
						<input type="radio" class="form-check-input" name="SFSF" value="1" <?php if ($params['info_candidato']['sfsf']==1) {echo 'checked';} ?>> 	
						<label class="form-check-label">
							<?php echo $lang['si']; ?>
						</label>
						<input type="radio" class="form-check-input" name="SFSF" value="0" style="margin-left: 10px;" <?php if ($params['info_candidato']['sfsf']==0) {echo 'checked';} ?>>
							<label class="form-check-label">
								<?php echo $lang['no']; ?>
							</label>
					</div>
					<div class="clear"><br></div>
					<div class="col-md-9">
						<label class="form-label"><b><?php echo $lang['obser']; ?></b></label>
						<textarea name="OBSERVACIONES" class="form-control" style="padding: 5px; height: 150px;"><?php echo $params['info_candidato']['observaciones']; ?></textarea>
					</div>
					<div class="clear"></div>
					<br>
					<div class="col-20">
						<label class="form-label"><b><?php echo $lang['act_foto']; ?>: *</b></label>
						<select name="tipo_img" class="form-login" style="width: initial; display: inline-block;">
							<option value="0"><?php echo $lang['select']; ?></option>
							<option value="1"><?php echo $lang['dni']." ".$lang['adverso']; ?></option>
							<option value="2"><?php echo $lang['dni']." ".$lang['reverso']; ?></option>
							<option value="3"><?php echo $lang['nie']." ".$lang['adverso']; ?></option>
							<option value="4"><?php echo $lang['nie']." ".$lang['reverso']; ?></option>
							<option value="5"><?php echo $lang['pasaporte']; ?></option>
						</select>
						<input type="file" name="img" style="display: inline-block;">
					</div>
					<br><br>
						<?php
						//Si el estado es rechazado tenemos la opción de volverlo a presentar cambiando el estado
						//0: Pendiente
						//1: Presentado
						//2: Rechazado
						if ($params['info_candidato']['estado']==2) {
							echo '<p>Cambiar estado:</p>
							<select name="estado" class="form-login" style="width: initial; display: inline-block;">
								<option value="2">"'. $lang['rechazado']. '"</option>
								<option value="0">"'. $lang['pendiente']. '"</option>
							</select><br><br>';
						}else{
							echo '<input type="hidden" name="estado" value="'.$params['info_candidato']['estado'].'">';
						}
						?>
					</div>
						<?php 
							if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
							?>
								<input type="submit" name="guardar" value="<?php echo $lang['guardar']; ?>" class="btn btn-primary mt-3">
							<?php 
							} 
						?>	
						
					</div>
				</div>
			</form>


		<div class="card">
            <div class="card-body">
				<?php 
				if ($params['info_candidato']['firma']!="") {
					echo "<img class='img-responsive' style='max-width: 300px' src='data:image/png;base64,".$params['info_candidato']['firma']."'/;><br><br>";			
				}
				if ($params['info_candidato']['foto1']!="") {
					echo "<div class='img-candidato'><a class='image-popup-no-margins' href='".$params['info_candidato']['foto1']."'>
						<img src='".$params['info_candidato']['foto1']."' style='display: inline-block; width: 100%;'>
					</a></div>";
				}
				if ($params['info_candidato']['foto2']!="") {
					echo "<div class='img-candidato'><a class='image-popup-no-margins' href='".$params['info_candidato']['foto2']."'>
						<img src='".$params['info_candidato']['foto2']."' style='display: inline-block; width: 100%;'>
					</a></div>";
				}
				if ($params['info_candidato']['foto3']!="") {
					echo "<div class='img-candidato'><a class='image-popup-no-margins' href='".$params['info_candidato']['foto3']."'>
						<img src='".$params['info_candidato']['foto3']."' style='display: inline-block; width: 100%;'>
					</a></div>";
				}
				if ($params['info_candidato']['foto4']!="") {
					echo "<div class='img-candidato'><a class='image-popup-no-margins' href='".$params['info_candidato']['foto4']."'>
						<img src='".$params['info_candidato']['foto4']."' style='display: inline-block; width: 100%;'>
					</a></div>";
				}
				if ($params['info_candidato']['foto5']!="") {
					echo "<div class='img-candidato'><a class='image-popup-no-margins' href='".$params['info_candidato']['foto5']."'>
						<img src='".$params['info_candidato']['foto5']."' style='display: inline-block; width: 100%;'>
					</a></div>";
				}
				// if ($params['info_candidato']['foto6']!="") {
				// 	echo "<div class='img-candidato'><a class='image-popup-no-margins' href='".$params['info_candidato']['foto6']."'>
				// 		<img src='".$params['info_candidato']['foto6']."' style='display: inline-block; width: 100%;'>
				// 	</a></div>";
				// }
				?>
			</div>
		</div>
		</div>
	</div>
</div>

<?php 
include_once("footer.php");
?>