<?php
include_once("header.php");
$num_trab = 0;
?>
<div class="pagetitle">
    <h1><?php echo $lang['tit_trab_baja']; ?></h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><?php echo $lang['menu3']; ?></li>
        <li class="breadcrumb-item active"><?php echo $lang['menu20']; ?></li>
    </ol>
</nav>
<section class="section">
    <div class="card">
        <div class="card-body">
            <form action="admin_cont.php?controller=index&action=trabajadores_baja" method="post">
                <h5 class="card-title">Filtros:</h5>
                <div class="row align-items-end">
					<!-- Ubicación -->
					<div class="col-md-3">
						<label for="ubi_trab" class="form-label">Ubicación:</label>
						<select class="form-select" name="ubi_trab" id="ubi_trab">
							<option value=""></option>
							<?php
								foreach ($params['fincas_almacenes'] as $key => $value) {
									echo '<option value="' . $value['ZZLGORT'] . '" ' . (isset($_POST['ubi_trab']) && $_POST['ubi_trab'] == $value['ZZLGORT'] ? 'selected' : '') . '>' . $value['DESC_ALMACEN'] ." (".$value['ZZLGORT'].")". '</option>';
								}
							?>
						</select>
					</div>

					<!-- Fecha inicio -->
					<div class="col-md-3">
						<label for="fecha_ini" class="form-label">Fecha baja:</label>
						<input type="date" class="form-control" id="fecha_ini" name="fecha_ini" value="<?php echo isset($_POST['fecha_ini']) ? $_POST['fecha_ini'] : date('Y-m-d', strtotime('-18 months')); ?>">
					</div>

					<!-- Separador "a" -->
					<div class="col-md-1 text-md-center">
						<span>a</span>
					</div>

					<!-- Fecha fin -->
					<div class="col-md-3">
						<label for="fecha_fin" class="form-label" style="color: white">Fecha baja fin:</label>
						<input type="date" class="form-control" id="fecha_fin" name="fecha_fin" value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : date('Y-m-d'); ?>">
					</div>
				</div>
                <br>
             
                <div id="loading" style="display: none;">
                    <button class="btn btn-primary mt-2" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Buscando...
                    </button>
                </div>
                <input type="submit" name="buscar" value="Buscar" class="btn btn-primary mt-2">
                <script>
                    // Mostrar el div de carga y ocultar el botón de búsqueda al enviar el formulario
                    document.querySelector('form').addEventListener('submit', function() {
                        document.getElementById('loading').style.display = 'block';
                        document.querySelector('input[type="submit"]').style.display = 'none';
                    });
                </script>
            </form>

            <form action='' id='exportar' class='mt-3' method='post' style='display: inline-block; margin-left: 15px;'>
                <input type="hidden" name="ubicacion" value="<?php echo isset($_POST['ubi_trab']) ? $_POST['ubi_trab'] : ''; ?>">
                <input type="hidden" name="fecha_ini" value="<?php echo isset($_POST['fecha_ini']) ? $_POST['fecha_ini'] : ''; ?>">
                <input type="hidden" name="fecha_fin" value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>">

                <button type="button" target="_blank"
                    onclick="document.getElementById('exportar').action='exportar.php?informe_trabajadores_baja_excel&ubicacion=' + document.getElementById('ubi_trab').value + '&fecha_ini=' + document.getElementById('fecha_ini').value + '&fecha_fin=' + document.getElementById('fecha_fin').value; document.getElementById('exportar').submit();"
                    style="background-color: white;">
                    <img src="img/xls.png" style="max-width: 100px; width: 35px; margin-top: 10px;">
                </button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo $lang['titu_llamamiento']; ?> (<span id="num_trab" style="font-weight: bold; color: #012970;"><?php echo count($params['datos_trab_baja'] ?? []); ?></span>)</h5>
            <table id="table_info" class="table datatable display" style="width:100%;">
                <thead>
                    <tr>
                        <th>Cod. Trabajador</th>
                        <th style="width: 30%;"><?php echo $lang['nombre']; ?></th>
                        <th style="width: 15%;">Fecha Baja</th>
                        <th>Almacen</th>
                        <th><?php echo $lang['menu5.1']; ?></th>
                        <th><?php echo $lang['ult_llama']; ?></th>
                        <th data-sortable="false"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (isset($params['datos_trab_baja'])) {
                            foreach ($params['datos_trab_baja'] as $resultado) {
                                $num_trab++;
                                if (!empty($resultado['APELLIDO1']) && !empty($resultado['NOMBRE'])) {
                                    // Si existen APELLIDO1 y NOMBRE, mostrar en formato: APELLIDO1 APELLIDO2, NOMBRE
                                    $nombre = $resultado['APELLIDO1'];
                        
                                    if (!empty($resultado['APELLIDO2'])) {
                                        $nombre .= ' ' . $resultado['APELLIDO2'];
                                    }
                        
                                    $nombre .= ', ' . $resultado['NOMBRE'];
                                } elseif (!empty($resultado['NOMBREYAPELLIDOS'])) {
                                    // Si existe el campo NOMBREYAPELLIDOS completo
                                    $nombre = $resultado['NOMBREYAPELLIDOS'];
                                }

                        ?>
                        <tr>
                            <td><?php echo $resultado['PERNR']; ?></td>
                            <td><?php echo $nombre; ?></td>
                            <td><?php echo (!empty($resultado['BEGDA']) ? $resultado['BEGDA']->format('Y-m-d') : '') ?></td>
                            <td>
                                <?php
                                if (!empty($resultado['DESC_ALMACEN']) && !empty($resultado['ZZLGORT'])) {
                                    // Si DESC_ALMACEN no está vacío ni es 'NULL', mostrarlo
                                    echo $resultado['DESC_ALMACEN']. ' (' . $resultado['ZZLGORT'] . ')';
                                } else {
                                    echo '';
                                }
                                ?>
                            <td>
                                <a href="admin_cont.php?controller=index&action=view_remesa_llama&id=<?php echo $resultado['id_remesa'] ?>&ano=<?php echo $resultado['ano_remesa'] ?>" target="_blank" style="color:#012970; text-decoration: underline;">
                                    <?php echo $resultado['nombre_remesa'] ?>
                                </a>    
                            </td>
                            <td><?php echo (!empty($resultado['FECHA_REGISTRO']) ? $resultado['FECHA_REGISTRO']->format('Y-m-d H:i:s') : '') ?></td>
                            <td>
                                <a href='admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $resultado['PERNR']; ?>&contact' target="_blank">
                                    <i class='bi bi-pencil-square fs-4' style="color: #012970;"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>





<?php
include_once("footer.php");
?>

<a href="#ancla" class="back-to-top d-flex align-items-center justify-content-center active">
  <i class="bi bi-arrow-up-short"></i>
</a>