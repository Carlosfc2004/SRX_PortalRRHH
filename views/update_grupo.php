<?php 
include_once("header.php"); 
?>
<div class="pagetitle">
    <h1><?php echo $lang['act_grupo']; ?></h1>
	<button type="submit" class="atras">
		<a class="bi bi-arrow-left-square-fill" href="admin_cont.php?controller=index&action=remesas"></a>
	</button>
</div>
	<nav>
        <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['act_grupo']; ?></li>
        </ol>
    </nav>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
					<br>
			<form action="admin_cont.php?controller=index&action=update_grupo&id=<?php echo $_GET['id'] ?>" method="post">
				<div class="col-md-6">
					<label class="form-label"><b><?php echo $lang['nombre']; ?> *</b></label>
						<input type="text" name="nombre" class="form-control" value="<?php echo $params['info_grupo']['nombre']; ?>" required>
						<input type="hidden" name="id" value="<?php echo $params['info_grupo']['id']; ?>">
						<br>
					<label class="form-label"><b><?php echo $lang['desc']; ?></b></label>
					<textarea name="descrip" style="height: 150px; padding: 5px;" class="form-control"><?php echo $params['info_grupo']['descrip']; ?></textarea>
					<p style="text-align: left; width: 98%;">
						<?php 
							if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
							?>
								<input type="submit" name="guardar" value="<?php echo $lang['guardar']; ?>" class="btn btn-primary mt-3">
							<?php 
							} 
						?>		
					
					</p>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>

<?php 
include_once("footer.php");
?>