<?php 
include_once("header.php"); 
?>
<div class="pagetitle">
    <h1><?php echo $lang['insert_grupo']; ?></h1>
</div>
	<nav>
        <ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['menu11']; ?></li>
        </ol>
    </nav>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
					<br>
					<form action="admin_cont.php?controller=index&action=new_grupo" method="post">
						<div class="col-md-6">
							<label class="form-label"><b><?php echo $lang['nombre']; ?> *</b></label>
							<input type="text" name="nombre" class="form-control" value="<?php echo $params['nombre']; ?>" required>
							<br>
							<label class="form-label"><b><?php echo $lang['desc']; ?></b></label>
							<textarea name="descrip" style="height: 150px; padding: 5px;" class="form-control"></textarea>
							<p style="text-align: left; width: 98%;">
								<input type="submit" name="enviar_cont" value="<?php echo $lang['insert']; ?>" class="btn btn-primary mt-3" >
							</p>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>

<?php 
include_once("footer.php");
?>