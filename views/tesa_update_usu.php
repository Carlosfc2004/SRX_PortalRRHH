<?php 
include_once("header.php");

$userData = $params['info_usu']['data'];
?>

<div class="pagetitle">
    <h1>Actualizar Usuario TESA</h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item">TESA</li>
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=tesa_usuarios">Usuarios</a></li>
        <li class="breadcrumb-item active">Actualizar usuario</li>
    </ol>
</nav>

<?php if(!$params['info_usu']['success']) { ?>
    <div class="alert alert-danger">
        <?php echo $params['info_usu']['message']; ?>
    </div>
<?php } else { ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Información del usuario</h5>
            <form action="admin_cont.php?controller=index&action=tesa_update_usu&id=<?php echo $userData['EXTERNALID']; ?>" method="post">
                <input type="hidden" name="actualizado">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><strong>ID Externo (PERNR):</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['EXTERNALID'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><strong>ID Usuario (TESA):</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['USERID'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><strong>Tipo de Usuario:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['USERTYPE'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5 mb-3">
                        <label class="form-label"><strong>Nombre:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['USERNAME'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><strong>ID Tarjeta:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['USERCARDID'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><strong>Tipo de Tarjeta:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['USERCARRIER'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><strong>Grupo:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['USERGROUP'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><strong>Email:</strong></label>
                        <input type="text" class="form-control" value="<?php 
                            echo (!empty($userData) && array_key_exists('USEREMAILADDRESS', $userData)) ? 
                                (is_array($userData['USEREMAILADDRESS']) ? '' : htmlspecialchars($userData['USEREMAILADDRESS'])) : 
                                ''; 
                        ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><strong>Teléfono:</strong></label>
                        <input type="text" class="form-control" value="<?php 
                            echo (!empty($userData) && array_key_exists('USERPHONENUMBER', $userData) && !is_array($userData['USERPHONENUMBER'])) ? 
                                htmlspecialchars($userData['USERPHONENUMBER']) : 
                                ''; 
                        ?>" readonly>
                    </div>
                </div>

                <?php
                    function formatDate($dateString) {
                        if (empty($dateString)) {
                            return '';
                        }
                        
                        try {
                            $date = new DateTime($dateString);
                            return $date->format('d/m/Y H:i:s');
                        } catch (Exception $e) {
                            return $dateString;
                        }
                    }
                ?>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><strong>Fecha de Creación:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars(formatDate($userData['DATEISSUE']) ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><strong>Fecha de Actualización:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars(formatDate($userData['DATEUPDATE']) ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label"><strong>Días UOC:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['UOCDAYS'] ?? ''); ?>" readonly>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label"><strong>Keypad:</strong></label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($userData['KEYPAD'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <?php 
                    if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
                    ?>
                        <!-- <p style="text-align: left; width: 98%;">
                            <input type="submit" name="guardar" value="<?php echo $lang['guardar']; ?>" class="btn btn-primary mt-3">
                        </p> -->
                    <?php 
                    } 
                ?>	
                
            </form>
        </div>
    </div>
<?php } ?>

<?php include_once("footer.php"); ?>