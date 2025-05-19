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
                <h5 class="card-title">Ubicación:</h5>
                <div class="col-md-3">
                    <select class="form-select" name="ubi_trab" id="ubi_trab" >
                        <option value=""></option>
                        <option value="M001" <?php if (isset($_POST['ubi_trab']) && $_POST['ubi_trab'] == 'M001') {echo "selected";} ?>>Almacen Central</option>
                        <option value="M002" <?php if (isset($_POST['ubi_trab']) && $_POST['ubi_trab'] == 'M002') {echo "selected";} ?>>Almacen Industria</option>
                        <option value="F014" <?php if (isset($_POST['ubi_trab']) && $_POST['ubi_trab'] == 'F014') {echo "selected";} ?>>Finca Artana</option>
                    </select>
                </div>
                <br>
                <input type="submit" name="buscar" value="Buscar" class="btn btn-primary mt-2">
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
                        <th style="width: 20%;">Fecha Baja</th>
                        <th><?php echo $lang['menu5.1']; ?></th>
                        <th><?php echo $lang['ult_llama']; ?></th>
                        <th></th>
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