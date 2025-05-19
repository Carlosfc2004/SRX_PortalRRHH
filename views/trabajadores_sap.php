<?php 
include_once("header.php");
?>
	<div class="pagetitle">
    <h1><?php echo $lang['menu1']; ?></h1>
  </div>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
      <li class="breadcrumb-item active"><?php echo $lang['menu1']; ?></li>
    </ol>
  </nav>
  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
        <h5 class="card-title" style="margin-left: 17px;">Filtros de busqueda trabajadores</h5>
          <div id="formContainer" class="card-body">
            
            <form id="filterForm" class="row g-3" action="admin_cont.php?controller=index&action=trabajadores_sap" method="post">
              <div class="col-md-2">
                <input type="text" name="txt_pernr" class="form-control" placeholder="Cod. Trabajador" value="<?php if (isset($_POST['txt_pernr'])) {echo $_POST['txt_pernr'];} ?>">
              </div>
              <div class="col-md-3">
                <input type="text" name="txt_nombre" class="form-control" placeholder="<?php echo $lang['nom_ape']; ?>" value="<?php if (isset($_POST['txt_nombre'])) {echo $_POST['txt_nombre'];} ?>">
              </div>
              <div class="col-md-2">
                <select name="sociedad" class="form-select">
                  <option value=""><?php echo $lang['sociedad']; ?></option>
                  <?php
                    foreach($params['sociedades'] as $result){
                      echo '<option value="'.$result['ZZWERKS'].'" ';
                      if(isset($_POST['sociedad']) and $_POST['sociedad']==$result['ZZWERKS']) {echo "selected";}
                      echo'>'.$result['DESC_CENTRO'].' - '. $result['ZZWERKS'].'</option>';
                    }
                  ?>
                </select>
              </div>
              <div class="col-md-2">                
                <select name="estado_trab" id="select_Baja" class="form-select">
                  <option value="" <?php if (isset($_POST['estado_trab']) && $_POST['estado_trab'] == "") {echo "selected";} ?>><?php echo $lang['todos']; ?></option>
                  <option value="0" <?php if (isset($_POST['estado_trab']) && $_POST['estado_trab'] == "0") {echo "selected";} ?>><?php echo $lang['baja']; ?></option>
                  <option value="1" <?php if (isset($_POST['estado_trab']) && $_POST['estado_trab'] == "1") {echo "selected";} ?>><?php echo $lang['rel_lab_susp']; ?></option>
                  <option value="2" <?php if (isset($_POST['estado_trab']) && $_POST['estado_trab'] == "2") {echo "selected";} ?>><?php echo $lang['pensionista']; ?></option>
                  <option value="3" <?php if (!isset($_POST['estado_trab']) || $_POST['estado_trab'] == "3") {echo "selected";} ?>><?php echo $lang['activo']; ?></option>
                </select>	              
              </div>
              <div class="clear"></div>
              <div class="col-md-12">
                  <div class="row align-items-center">
                    <!-- Botón de Enviar -->
                    <div id="boton-envio-container" class="col-auto">
                      <input type="hidden" name="buscar" value="<?php echo $lang['buscar']; ?>">
                      <button type="submit" class="btn btn-primary" id="btn-enviar">
                        <?php echo $lang['buscar']; ?>
                      </button>
                    </div>

                    <!-- Botón de Cargando -->
                    <div id="loading-btn-container" class="col-auto" style="display: none;">
                      <button class="btn btn-primary" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Buscando información...
                      </button>
                    </div>

                    <!-- Botón de Reset -->
                    <div class="col-auto">
                      <button type="button" id="clearFilters" class="btn btn-danger">
                        Reset
                      </button>
                    </div>
                  </div>

                  <script>
                    const formulario = document.querySelector('#filterForm');
                    const btnEnviar = document.querySelector('#btn-enviar');
                    const envioBtnContainer = document.querySelector('#boton-envio-container');
                    const loadingBtnContainer = document.querySelector('#loading-btn-container');

                    formulario.addEventListener('submit', function () {
                      btnEnviar.disabled = true;
                      envioBtnContainer.style.display = 'none';
                      loadingBtnContainer.style.display = 'block';
                    });
                  </script>
                </div>
              </form>
            </div>
          </div>

        <?php
          if (!empty($params['trabajadores']) && is_array($params['trabajadores'])) {
          ?>
          <div class="card">
            <div class="card-body">
              <br>
              <table class="table datatable display" id="tabla_trab">
                <thead>
                    <tr>
                      <div class="col-9">
                        <th class="col-2">Cod. Trabajador</th>
                        <th class="col-3"><?php echo $lang['nombre']; ?></th>
                        <th class="col-2">DNI</th>
                        <th class="col-1"></th>
                      </div>
                    </tr>
                </thead>
                <tbody>
                  <?php foreach ($params['trabajadores'] as $resultado) { ?>
                    <tr>
                      <td>
                        <?php
                        if ($resultado['STAT2'] == '3' && $resultado['BEGDA_MEDIDA'] >= date('Y-m-d')) {
                          echo "<i class='bi bi-circle-fill' style='color: green;'></i>";
                        } else {
                          echo "<i class='bi bi-circle-fill' style='color: red;'></i>";
                        }
                        ?>
                        <?php echo $resultado['PERNR']?>
                      </td>
                      <td>
                        <?php 
                          // Mostrar el nombre completo del trabajador
                          if (!empty($resultado['APELLIDO1']) && !empty($resultado['NOMBRE'])) {
                            // Si existen APELLIDO1 y NOMBRE, mostrar en formato: APELLIDO1 APELLIDO2, NOMBRE
                            echo $resultado['APELLIDO1'];

                            if (!empty($resultado['APELLIDO2'])) {
                              echo ' ' . $resultado['APELLIDO2'];
                            }

                            echo ', ' . $resultado['NOMBRE'];
                          } elseif (!empty($resultado['NOMBREYAPELLIDOS'])) {
                            // Si existe el campo NOMBREYAPELLIDOS completo
                            echo $resultado['NOMBREYAPELLIDOS'];
                          } else {
                            // Si no hay datos disponibles
                            echo 'Desconocido';
                          }
                        ?>
                      </td>
                      <td>
                        <?php 
                          if ($resultado['DNI'] != ''){
                            echo $resultado['DNI']; }
                          else {
                            echo "--";
                          }
                        ?>
                      </td>
                      <td>
                        <li class="hvr-icon-forward">
                          <a href="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $resultado['PERNR']; ?>">
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
            </div>
          </div>
          <?php
            } else {
              echo '';
            }
        ?>
      </div>
    </div>
  </section>
<?php include_once("footer.php"); ?>


<!-- <a href="#ancla" class="back-to-top d-flex align-items-center justify-content-center active">
  <i class="bi bi-arrow-up-short"></i>
</a> -->



