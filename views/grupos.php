<?php 
include_once("header.php");
?>
<div class="pagetitle">
    <h1><?php echo $lang['menu10']; ?></h1>
</div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu7']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['menu10']; ?></li>
        </ol>
    </nav>

<section class="section">
    <?php
    if (!empty($params['grupos'])) {
    ?>
        <div class="card">
            <br>
            <div class="card-body">
                <table class="table datatable" id="tabla_groups">
                    <thead>
                        <tr>
                            <div class="col-9">
                                <th class="col-3"><?php echo $lang['nom_grupo']; ?></th>
                                <th class="col-6"></th>
                            </div>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($params['grupos'] as $resultado) {
                        ?>
                            <tr>
                                <td>
                                    <?php 
                                    echo $resultado['nombre']; 
                                    if ($resultado['cont'] > 0) {
                                        echo " (" . $resultado['cont'] . ")";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="col-10">
                                        <li class="hvr-icon-back">
                                            <a href="admin_cont.php?controller=index&action=update_grupo&id=<?php echo $resultado['id']; ?>">
                                                <div class="hvr-icon" >
                                                    <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                                </div>
                                            </a>
                                        </li>
                                        <?php 
                                            if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
                                            ?>
                                                <li class="hvr-icon-forward">
                                                    <button onclick="alertify.confirm('<?php echo $lang['conf_elim_grupo']; ?>', function(){$(location).attr('href','admin_cont.php?controller=index&action=grupos&elim=<?php echo $resultado['id'] ?>')});" style="background-color: transparent; cursor: pointer;">
                                                        <div class="hvr-icon">
                                                            <i class="bx bxs-trash fs-3" style="color: #99353a;"></i>
                                                        </div>
                                                    </button>
                                                </li>
                                            <?php 
                                            } 
                                        ?>	
                                        
                                    </div>
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
</section>

<?php 
include_once("footer.php");
?>