<?php 
include_once("header.php");
?>
<div class="pagetitle">
    <h1><?php echo $lang['menu24']; ?></h1>
</div>
<nav>
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><?php echo $lang['menu21']; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $lang['menu13']; ?></li>
	</ol>
</nav>
<section class="section">
	<div class="row align-items-top">
		<!-- Listado de todas las ubicaciones como guia -->
		<div class="col-lg-6">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title"><?php echo $lang['list_ubi']; ?></h5>
					<?php
					if (!empty($params['ubicaciones'])) {
					?>
						<table class="table datatable">
							<thead>
								<tr>
									<div class="col-9">
										<th class="col-md-1">ID</th>
										<th class="col-md-2"><?php echo $lang['sede']; ?></th>
										<th class="col-md-2"><?php echo $lang['ubi']; ?></th>
									</div>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ($params['ubicaciones'] as $resultado) {
								?>
									<tr>
										<td>
											<?php 
												echo $resultado['id']; 
											?>
										</td>
										<td>
											<?php 
												echo $resultado['sede']; 
											?>
										</td>
										<td>
											<?php 
												echo $resultado['nombre']; 
											?>
										</td>
									</tr>
								<?php
								}
								?>
							</tbody>
						</table>
					<?php
					}
					?>
				</div>
			</div>
		</div>

		<!-- Parte de añadir ubicación -->
		<div class="col-lg-6">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title"><?php echo $lang['add_ubi']; ?></h5>
					<form action="admin_cont.php?controller=index&action=new_ubicacion&añadido" method="post">
						<div class="col-md-8">
							<input type="hidden" name="nueva_ubicacion">

							<label class="form-label"><b><?php echo $lang['sede']; ?> *</b></label>
							<input type="text" name="sede" class="form-control" value="" required>
							<br>

							<label class="form-label"><b><?php echo $lang['ubi']; ?> *</b></label>
							<input type="text" name="ubicacion" class="form-control" value="" required>
							<br>
							
							<p style="text-align: left; width: 100%;">
								<input type="submit" name="enviar_cont" value="<?php echo $lang['insert']; ?>" class="btn btn-primary mt-3" >
							</p>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
	function redirigir() {
        // Obtén los parámetros de la URL actual
        var urlParams = new URLSearchParams(window.location.search);

        // Inicializa la variable para la URL de redirección
        var redirectUrl = '';

        // Verifica si el parámetro 'añadido' existe
        if (urlParams.has('añadido')) {
            // Obtén el parámetro 'id'
            // Construye la URL de redirección para 'nueva ubicacion'
            redirectUrl = `admin_cont.php?controller=index&action=new_ubicacion`;
        } 

        // Si se construyó una URL de redirección, realiza la redirección
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    }

    // Llama a la función con un retraso de 2 segundos
    setTimeout(redirigir, 2000);
</script>

<?php 
include_once("footer.php");
?>