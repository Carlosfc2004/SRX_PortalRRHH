<?php 
include_once("header.php");
?>
	<div class="pagetitle">
	  <h1>Mi perfil</h1>
	</div>
	  <nav>
      <ol class="breadcrumb">
			  <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>

      </ol>
    </nav>





<section class="section profile">
  <div class="row">
    <div class="col-xl-4">
      <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
          <i class="bi bi-person-circle fs-1" style="color: #2c384e;"></i>
          <h2><?php echo $params['datos_usu'][0]['nombre'] ." ". $params['datos_usu'][0]['apellidos']; ?></h2>
        </div>
      </div>
    </div>

    <div class="col-xl-8">
      <div class="card">
        <div class="card-body pt-3">
          <!-- Bordered Tabs -->
          <ul class="nav nav-tabs nav-tabs-bordered" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#datos_perfil" aria-selected="true" role="tab">Datos</button>
            </li>
          </ul>

          
          <div class="tab-content pt-2">
            <!-- Pestaña de datos -->
            <div class="tab-pane fade profile-overview active show" id="datos_perfil" role="tabpanel">
              <h5 class="card-title">Detalles del perfil</h5>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Nombre completo</div>
                <div class="col-lg-9 col-md-8"><?php echo $_SESSION["nombre_user_surexport_appreclu"]; ?></div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Compañia</div>
                <div class="col-lg-9 col-md-8">Surexport C.A. S.L.</div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Email</div>
                <div class="col-lg-9 col-md-8">
                  <?php 
                    if (isset($params['datos_usu'][0]['usr_login'])) {
                      echo $params['datos_usu'][0]['usr_login'];
                    } 
                  ?>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-4 label">Teléfono</div>
                <div class="col-lg-9 col-md-8">
                  <?php 
                    if (isset($params['datos_usu'][0]['telf'])) {
                      echo $params['datos_usu'][0]['telf'];
                    }
                  ?>
                </div>
              </div>
              
            </div>
            <!-- Fin Pestaña de datos -->
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include_once("footer.php"); ?>