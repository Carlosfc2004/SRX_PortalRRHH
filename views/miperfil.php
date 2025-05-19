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
            <!-- <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit" aria-selected="false" role="tab" tabindex="-1">Editar perfil</button>
            </li> -->
            <!-- <li class="nav-item" role="presentation">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password" aria-selected="false" role="tab" tabindex="-1">Cambiar contraseña</button>
            </li> -->
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

            <!-- Pestaña Editar Perfil -->
            <!-- <div class="tab-pane fade profile-edit pt-3" id="profile-edit" role="tabpanel"> -->
              <!-- Profile Edit Form -->
              <!-- <form action="admin_cont.php?controller=index&action=miperfil" method="post">
                <div class="row mb-3">
                  <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Nombre completo</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="fullName" type="text" class="form-control" id="fullName" value="<?php echo $_SESSION["nombre_user_surexport_appreclu"]; ?>" readonly style="background-color: #dedede;"> 
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="company" class="col-md-4 col-lg-3 col-form-label">Compañía</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="company" type="text" class="form-control" id="company" value="Surexport C.A. S.L." readonly style="background-color: #dedede;">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="email" type="email" class="form-control" id="Email" 
                    value="
                    <?php 
                      // if (isset($params['datos_usu'][0]['usr_login'])) {
                      //   echo $params['datos_usu'][0]['usr_login'];
                      // } 
                    ?>" readonly style="background-color: #dedede;">
                  </div>
                </div>

                <input type="hidden" name="id_usu" value="<?php echo $_SESSION["id_user_surexport_appreclu"]; ?>">
                <div class="row mb-3">
                  <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Telefono</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="telefono" type="text" class="form-control" id="Phone" value="<?php echo isset($params['datos_usu'][0]['telefono']) ? trim($params['datos_usu'][0]['telefono']) : ''; ?>">
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="Job" class="col-md-4 col-lg-3 col-form-label">Departamento</label>
                  <div class="col-md-8 col-lg-9">
                    <input name="departamento" type="text" class="form-control" id="Job" value="<?php echo isset($params['datos_usu'][0]['departamento']) ? trim($params['datos_usu'][0]['departamento']) : ''; ?>">
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
              </form> -->
              <!-- End Profile Edit Form -->
            <!-- </div> -->
            <!-- Fin Pestaña Editar Perfil -->


				    <!-- <div class="tab-pane fade" id="profile-change-password" role="tabpanel"> -->
              <!-- Cambio de contraseña -->
              <!-- <h5 class="card-title"><?php echo $lang['mod_contra']; ?></h5>
              <form action="admin_cont.php?controller=index&action=miperfil" method="post">
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-3 col-form-label"><?php echo $lang['contra_act']; ?><span style="font-size: 18px; font-family: Verdana;">*</span></label>
                  <div class="col-sm-3">
                  <input type="password" name="usr_pass" class="form-control">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-3 col-form-label"><?php echo $lang['new_contra']; ?><span style="font-size: 18px; font-family: Verdana;">*</span></label>
                  <div class="col-sm-3">
                  <input type="password" name="usr_pass_new" class="form-control">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-3 col-form-label"><?php echo $lang['rep_new_contra']; ?><span style="font-size: 18px; font-family: Verdana;">*</span></label>
                  <div class="col-sm-3">
                  <input type="password" name="usr_pass_new_rep" class="form-control">
                  </div>
                </div>
                <input type="submit" name="enviar_cont" value="<?php echo $lang['modificar']; ?>" class="btn btn-primary" style="width: auto;">
              </form>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include_once("footer.php"); ?>