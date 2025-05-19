<?php 
include_once("header.php"); 
?>
	<div class="pagetitle">
    	<h1><?php echo $lang['menu9']; ?></h1>
	</div>
    <nav>
        <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['menu9']; ?></li>
        </ol>
    </nav>
<section class="section">
    <div class="row">
		<div class="col-lg-12">
            <div class="card">
                <div class="card-body">
					<br>
				<form class="row g-3" action="admin_cont.php?controller=index&action=new_candidato" method="post">
				<h5 style="color: #012970;"><?php echo $lang['datos_personales_contacto']; ?></h5>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['nombre']; ?> *</b></label>
					<input type="text" name="nombre" class="form-control" required>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['apellido']; ?> 1 *</b></label>
					<input type="text" name="apellido1" class="form-control" required>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['apellido']; ?> 2 *</b></label>
					<input type="text" name="apellido2" class="form-control">
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['sexo']; ?> *</b></label>
					<select name="sexo" class="form-select" required>
						<option value="Masculino"><?php echo $lang['masculino']; ?></option>
						<option value="Femenino"><?php echo $lang['femenido']; ?></option>
					</select>
				</div>


				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['fecha_nac']; ?> *</b></label>
					<input type="date" name="fecha_nac" class="form-control" required>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['lug_nac']; ?> *</b></label>
					<input type="text" name="lugar_nac" class="form-control" required>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['pais_nac']; ?> *</b></label>
					<select name="pais_nac" class="form-select" required>
						<option value=""><?php echo $lang['select_pais']; ?></option>
						<?php 
						foreach ($params['paises'] as $value) {
							echo '<option value="'.$value['PAIS_NAC'].'">'.$value['PAIS_NAC'].'</option>';
						}
						?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['tip_doc']; ?> *</b></label>
					<select name="tipo_doc" class="form-select" required>
						<option value="0"><?php echo $lang['select']; ?></option>
						<option value="1"><?php echo $lang['dni']; ?></option>
						<option value="2"><?php echo $lang['pasaporte']; ?></option>
						<option value="3"><?php echo $lang['tarj_residente']; ?></option>
						<option value="4"><?php echo $lang['perm_residente']; ?></option>
						<option value="5"><?php echo $lang['nie']; ?></option>
						<option value="6"><?php echo $lang['num_segsoc']; ?></option>
						<option value="7"><?php echo $lang['tarj_nac_id']; ?></option>
					</select>
				</div>


				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['num_doc']; ?> *</b></label>
					<input type="text" name="valor_doc" class="form-control" required>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['nacionalidad']; ?> *</b></label>
					<select name="nacionalidad" class="form-select" required>
						<option value=""><?php echo $lang['select_nac']; ?></option>
						<?php 
						foreach ($params['nacionalidad'] as $value) {
							echo '<option value="'.$value['GENTILICIO_NAC'].'">'.$value['GENTILICIO_NAC'].'</option>';
						}
						?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['telefono']; ?> *</b></label>
					<input type="text" name="telf" class="form-control" required>
				</div>
				<div class="col-md-3">
					<label class="form-label"><b><?php echo $lang['email']; ?> *</b></label>
					<input type="text" name="mail" class="form-control" required>
				</div>
				<br><br>
				</div>
			</div>
		</div>

		<div class="col-lg-12">
		<div class="card">
			<div class="card-body">
				<br>
				<h5 style="color: #012970;"><?php echo $lang['otros_datos']; ?></h5>
				<br>
				<div class="row mb-4">
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['poblacion']; ?></b></label>
						<input type="text" name="poblacion" class="form-control">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['cod_post']; ?></b></label>
						<input type="text" name="cod_postal" class="form-control">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['via']; ?></b></label>
						<input type="text" name="sigla_via" class="form-control">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['calle']; ?></b></label>
						<input type="text" name="calle" class="form-control">
					</div>

					<div class="clear"><br></div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['num_edificio']; ?></b></label>
						<input type="text" name="num_edificio" class="form-control">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['distrito']; ?></b></label>
						<input type="text" name="distrito" class="form-control">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['region']; ?></b></label>
						<input type="text" name="region" class="form-control">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['estado_civil']; ?> *</b></label>
						<select name="estado_civil" class="form-select" required>
							<option value=""><?php echo $lang['select']; ?></option>
							<option value="0"><?php echo $lang['soltero']; ?></option>
							<option value="1"><?php echo $lang['casado']; ?></option>
							<option value="2"><?php echo $lang['viudo']; ?></option>
							<option value="3"><?php echo $lang['divorciado']; ?></option>
							<option value="4"><?php echo $lang['separado']; ?></option>
						</select>
					</div>

					<div class="clear"><br></div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['hijos']; ?></b></label>
						<input type="text" name="num_hijos" class="form-control">
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['nom_padre']; ?> *</b></label>
						<input type="text" name="nombre_padre" class="form-control" required>
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['nom_madre']; ?> *</b></label>
						<input type="text" name="nombre_madre" class="form-control" required>
					</div>
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['cuali']; ?></b></label>
						<input type="text" name="cualificacion" class="form-control">
					</div>


					<div class="clear"><br></div>
					
					<div class="col-md-3">
						<label class="form-label"><b><?php echo $lang['expe']; ?></b></label>
						<br>
						<input type="radio" class="form-check-input" name="experiencia" value="1">
							<label class="form-check-label">
								<?php echo $lang['si']; ?>
							</label>
						<input type="radio" class="form-check-input" style="margin-left: 10px;" name="experiencia" value="0">
							<label class="form-check-label">
								<?php echo $lang['no']; ?>
							</label>
					</div>
					<div class="col-md-5">
						<label class="form-label"><b><?php echo $lang['anteriormente']; ?></b></label>
						<br>
						<input type="radio" class="form-check-input" name="sfsf" value="1">
							<label class="form-check-label">
								<?php echo $lang['si']; ?>
							</label>
						<input type="radio" class="form-check-input" style="margin-left: 10px;" name="sfsf" value="0">
							<label class="form-check-label">
								<?php echo $lang['no']; ?>
							</label>
					</div>
					<div class="clear"><br></div>
					<div class="col-md-9">
						<label class="form-label"><b><?php echo $lang['obser']; ?></b></label>
						<textarea name="observaciones" class="form-control" style="padding: 5px; height: 150px;"></textarea>
					</div>
				</div>
				<input type="submit" name="insertar" value="<?php echo $lang['insert']; ?>" class="btn btn-primary mt-3">
				</div>
				<div class="clear"><br></div>
			</form>
		</div>
	</div>
</div>
</body>
</html>


<?php 
include_once("footer.php");
?>