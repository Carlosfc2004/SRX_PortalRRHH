<?php 
include_once("header.php");
?>

<div>
    <?php 
        $id_remesa = isset($_GET['id']) ? $_GET['id'] : '';
        $ano_remesa = isset($_GET['ano']) ? $_GET['ano'] : '';
        $info_remesas = isset($params['info_remesas']) ? $params['info_remesas'] : [];
        $nombre_remesa = !empty($info_remesas) && isset($info_remesas[0]['nombre_remesa']) ? $info_remesas[0]['nombre_remesa'] : '';

        $sinllama = 0;
        $enviado = 0;
        $aceptado = 0;
        $rechazado = 0;
        $pendiente = 0;
        $sinrespuesta = 0;

        foreach ($info_remesas as $remesa) {
            if ($remesa['ESTADO'] === 5) {
                $sinllama++;
            } elseif ($remesa['ESTADO'] === 0) {
                $enviado++;
            } elseif ($remesa['ESTADO'] === 1) {
                $aceptado++;
            } elseif ($remesa['ESTADO'] === 2) {
                $rechazado++;
            } elseif ($remesa['ESTADO'] === 3) {
                $pendiente++;
            } elseif ($remesa['ESTADO'] === 4) {
                $sinrespuesta++;
            }
        }

        echo '<h1 style="font-size: 24px; margin-bottom: 0; font-weight: 600; color: #012970;">'.$nombre_remesa.' ('.count($info_remesas).' candidatos)</h1>';
    ?>
</div>
<br>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><?php echo $lang['menu3']; ?></li>
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=rem_llama"><?php echo $lang['menu5']; ?></a></li>
        <li class="breadcrumb-item active"><?php echo $lang['menu5.1'].' '.$id_remesa.'/'.$ano_remesa ?></li>
    </ol>
</nav>

<section class="section">
    <div class="card">
        <div class="card-body">
            <br>
            <form action="admin_cont.php?controller=index&action=llamamientos" method="post" style="display: inline-block; margin-left: 5px;">
                <input type="hidden" name="id_remesa" value="<?php echo $_GET['id']; ?>">
                <input type="hidden" name="ano_remesa" value="<?php echo $_GET['ano']; ?>">
                <input type="submit" name="add_remesa" value="<?php echo $lang['add_trab']; ?>" class="btn btn-primary mb-2">
            </form>

            
            <!-- Informacion del estado del llamamiento de todos los trabajadores de la remesa -->
            <?php 
                if ($sinllama > 0) {
                    echo "<button class='btn btn-secondary mb-2 sinllama'>
                                ".$lang['sin_llama']."
                            <span class='badge bg-white text-secondary'>" . $sinllama . "</span>
                          </button>";
                }
                if ($enviado > 0) {
                    echo "<button class='btn btn-warning mb-2' style='margin-left: 5px;'>
                                ".$lang['enviado']."
                            <span class='badge bg-white text-warning'>" . $enviado . "</span>
                          </button>";
                }
                if ($aceptado > 0) {
                    echo "<button class='btn btn-success mb-2' style='margin-left: 5px;'>
                                ".$lang['aceptado']."
                            <span class='badge bg-white text-success'>" . $aceptado . "</span>
                          </button>";
                }
                if ($rechazado > 0) {
                    echo "<button class='btn btn-danger mb-2' style='margin-left: 5px;'>
                                ".$lang['rechazado']."
                            <span class='badge bg-white text-danger'>" . $rechazado . "</span>
                          </button>";
                }
                if ($pendiente > 0) {
                    echo "<button class='btn btn-info mb-2' style='margin-left: 5px;'>
                                ".$lang['pendiente']."
                            <span class='badge bg-white text-info'>" . $pendiente . "</span>
                          </button>";
                }
                if ($sinrespuesta > 0) {
                    echo "<button class='btn btn-light mb-2' style='margin-left: 5px;'>
                                Sin respuesta
                            <span class='badge bg-secondary text-light'>" . $sinrespuesta . "</span>
                          </button>";
                }
            ?>
            <br>
            <table class="table datatable" id="tabla_rem_view_llama">
                <thead>
                    <tr>
                        <th style="width: 30%;"><?php echo $lang['nombre_trab']; ?></th>
                        <th style="width: 10%;">Cod. Trabajador</th>
                        <th style="width: 15%;"><?php echo $lang['telefono']; ?></th>
                        <th style="width: 25%;"><?php echo $lang['correo']; ?></th>
                        <th style="width: 5%;"><?php echo $lang['estado']; ?></th>
                        <th style="width: 10%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (!empty($info_remesas)) {
                        foreach ($info_remesas as $remesa) {
                            ?>
                            <tr>
                                <td class="col-3"><?php echo $remesa['NOMBREYAPELLIDOS']; ?></td>
                                <td class="col-1"><?php echo $remesa['PERNR']; ?></td>
                                <td class="col-1"><?php echo $remesa['PREFIJO'].$remesa['MOVIL']; ?></td>
                                <td class="col-3"><?php echo $remesa['CORREO']; ?></td>
                                <td class="col-2">
                                    <?php 
                                        if ($remesa['ESTADO'] === 5) {
                                            echo $lang['sin_llama'];
                                        } elseif ($remesa['ESTADO'] === 0) {  
                                            echo $lang['enviado'];
                                        } elseif ($remesa['ESTADO'] === 1) {  
                                            echo $lang['aceptado'];
                                        } elseif ($remesa['ESTADO'] === 2) {  
                                            echo $lang['rechazado'];
                                        } elseif ($remesa['ESTADO'] === 3) {  
                                            echo $lang['pendiente'];
                                        } elseif ($remesa['ESTADO'] === 4) {  
                                            echo $lang['sin_respuesta'];
                                        } else {
                                            echo $lang['estado_desc'];
                                        }
                                    ?>
                                </td>
                                <td class="col-2">
                                    <form action="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $remesa['PERNR']; ?>&showll&id_rem=<?php echo $remesa['id_remesa']; ?>&ano_rem=<?php echo $remesa['ano_remesa']; ?>&remesa" method="post">
                                        <input type="hidden" value="<?php echo $remesa['id_remesa']; ?>" name="id_remesa">
                                        <input type="hidden" value="<?php echo $remesa['ano_remesa']; ?>" name="ano_remesa">
                                        <input type="hidden" name="datos_remesa" value="1">
                                        <button type="submit" class="icono hvr-icon" style="background: none; border: none; cursor: pointer;">
                                            <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo $lang['no_trab_disp'];
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php 
include_once("footer.php");
?>