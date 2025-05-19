<?php 
include_once("header.php");
?>

    <div class="pagetitle">
        <h1><?php echo $lang['menu8']; ?></h1>
    </div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['menu8']; ?></li>
        </ol>
    </nav>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <button id="toggleButton" class="filtros btn"><?php echo $lang['filtros']; ?></button>
                <div id="formContainer" class="card-body" style="display:none;">
                    <form id="filterForm" class="row g-3" action="admin_cont.php?controller=index&action=candidatos" method="post" style="margin-top: -20px">
                        <div class="col-md-3">  
                            <input type="text" name="txt_bus" class="form-control" placeholder="<?php echo $lang['filtro_cand']; ?>" style="display: inline-block;" value="<?php if (isset($_POST['txt_bus'])) {echo $_POST['txt_bus'];} ?>">
                        </div>
                        <div class="col-md-2">                            
                            <select name="grupo" class="form-select" >
                                <option value=""><?php echo $lang['grupo']; ?></option>
                                <?php
                                    if (!empty($params['grupos'])) {
                                        foreach ($params['grupos'] as $value) {
                                            echo '<option value="'.$value['id'].'"';
                                            if (isset($_POST['grupo'])) {
                                                if ($_POST['grupo']==$value['id']) {echo "selected";}
                                            }
                                            echo '>'.$value['nombre'].'</option>';
                                        }
                                    }
                                ?>
                            </select>                        
                        </div>
					
                        <div class="col-md-2">  
							<select name="estado" class="form-select">
								<option value=""><?php echo $lang['estado']; ?></option>
								<option value="0" <?php if(isset($_POST['estado']) and $_POST['estado']=="0"){echo "selected";} ?>><?php echo $lang['pendiente']; ?></option>
								<option value="1" <?php if(isset($_POST['estado']) and $_POST['estado']=="1"){echo "selected";} ?>><?php echo $lang['solicitado']; ?></option>
								<option value="2" <?php if(isset($_POST['estado']) and $_POST['estado']=="2"){echo "selected";} ?>><?php echo $lang['rechazado']; ?></option>
							</select>
                        </div>
                        <div class="clear"></div>
                        <div class="col-md-2">  
							<input type="submit" class="btn btn-primary mt-auto" name="buscar" value="<?php echo $lang['buscar']; ?>">
                            <button type="button" id="clearFilters" class="btn btn-secondary mt-auto" style="background-color: #dc3545; border: none;">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
                <script>
                	// FILTROS pagina Registros llamamiento
                    // Mostrar/Ocultar filtros
                    var toggleButton = document.getElementById('toggleButton');
                    if (toggleButton) {  // Verificar si el botón existe
                        toggleButton.addEventListener('click', function() {
                            var formContainer = document.getElementById('formContainer');
                            if (formContainer) {  // Verificar si el contenedor del formulario existe
                                if (formContainer.style.display === 'none') {
                                    formContainer.style.display = 'block';  // Mostrar el formulario
                                } else {
                                    formContainer.style.display = 'none';   // Ocultar el formulario
                                }
                            }
                        });
                    }

                    // Limpiar el formulario al hacer clic en el botón de limpiar
                    var clearFilters = document.getElementById('clearFilters');
                    if (clearFilters) {  // Verificar si el botón de limpiar existe
                        clearFilters.addEventListener('click', function() {
                            var filterForm = document.getElementById('filterForm');
                            if (filterForm) {  // Verificar si el formulario existe
                                // Restablecer el formulario a sus valores predeterminados
                                filterForm.reset();

                                // También puedes deseleccionar cualquier opción en los select
                                var selects = document.querySelectorAll('#filterForm select');
                                selects.forEach(function(select) {
                                    select.selectedIndex = 0; // Restablece la selección al primer elemento
                                });

                                // Limpiar todos los campos de texto
                                var inputs = document.querySelectorAll('#filterForm input[type="text"], #filterForm input[type="date"]');
                                inputs.forEach(function(input) {
                                    input.value = ''; // Limpia el valor de los campos de texto y de fecha
                                });
                            }
                        });
                    }
                </script>
            </div>
            <?php
            if (!empty($params['candidatos'])) {
            ?>
                <div class="card">
					<br>
                    <div class="card-body">
                        <table class="table datatable" id="tabla_emp">
                            <thead>
                                <tr>
                                    <div class="col-9">
                                        <th class="col-4"><?php echo $lang['nombre']; ?></th>
                                        <th class="col-3"><?php echo $lang['num_doc']; ?></th>
                                        <th class="col-2"></th>
                                    </div>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($params['candidatos'] as $resultado) {
                                ?>
                                    <tr>
                                        <td>
                                            <?php echo $resultado['nombre'] . " " . $resultado['apellido1'] . " " . $resultado['apellido2']; ?>
                                        </td>
                                        <td>
                                            <?php echo $resultado['valor_doc']; ?>
                                        </td>
                                        <td>
                                            <li class="hvr-icon-back">
                                                <a href="admin_cont.php?controller=index&action=update_candidato&id=<?php echo $resultado['id']; ?>">
                                                    <div class="hvr-icon" >
                                                        <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                                    </div>
                                                </a>
                                            </li>
                                            <?php 
                                                if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
                                                ?>
                                                    <li class="hvr-icon-forward">
                                                        <button onclick="alertify.confirm('<?php echo $lang['confirm_elim_cand']; ?>', function(){$(location).attr('href','admin_cont.php?controller=index&action=candidatos&elim=<?php echo $resultado['id'] ?>')});" style="background-color: transparent; cursor: pointer;">
                                                            <div class="hvr-icon">
                                                                <i class="bx bxs-trash fs-3" style="color: #99353a;"></i>
                                                            </div>
                                                        </button>
                                                    </li>
                                                <?php 
                                                } 
                                            ?>	
                                            
                                        
                                            <li class="hvr-icon-forward">
                                                <a href="exportar.php?pdf&id=<?php echo $resultado['id']; ?>" target="_blank">
                                                    <div class="hvr-icon">
                                                        <i class="bx bxs-file-pdf fs-3 hvr-icon" style="color: #db0001;"></i>
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
            }
            ?>
        </div>
    </div>
</section>

<?php 
include_once("footer.php");
?>
