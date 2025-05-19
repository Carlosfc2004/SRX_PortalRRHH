<?php 
include_once("header.php");
?>


<div class="pagetitle">
    <h1><?php echo $lang['menu18']; ?></h1>
</div>
	<nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active"><?php echo $lang['menu18']; ?></li>
        </ol>
    </nav>
<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <!-- Formulario para consultar los créditos SMS -->
            <div class="card">
                <div class="card-body">
                    <br>
                    <h5 style="color: #012970;"><?php echo $lang['credit_disp']; ?></h5>
                    <form method="POST" action="">
                        <button type="submit" class="btn btn-primary mt-2" name="consultar"><?php echo $lang['consult_credit']; ?></button>
                    </form>
                </div>	
            </div>

            <!-- Formulario para consultar estado API WhatsApp -->
            <!-- <div class="card">
                <div class="card-body">
                    <br>
                    <h5 style="color: #012970;">API WhatsApp</h5>
                    <form method="POST" action="">
                        <button type="submit" class="btn btn-primary mt-2" name="consultar_meta"><?php echo $lang['consult_api_whats']; ?></button>
                    </form>
                </div>	
            </div> -->

        </div>
    </div>
</section>



<?php 
include_once("footer.php");
?>
