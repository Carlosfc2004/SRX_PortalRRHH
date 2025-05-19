<?php 
include_once("header.php");
?>
<div class="pagetitle">
    <h1><?php echo $lang['menu22']; ?></h1>
</div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu21']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['menu22']; ?></li>
        </ol>
    </nav>

<section class="section">
    <?php 
    if (!empty($params['dispositivos'])) { 
    ?>
        <div class="card">
            <br>
            <div class="card-body">
                <table class="table datatable display" id="tabla_dispositivos">
                    <thead>
                        <tr>
                            <th class="col-2">ID <?php echo $lang['dispo']; ?></th>
                            <th class="col-2"><?php echo $lang['nombre']; ?></th>
                            <th class="col-2"><?php echo $lang['sede']; ?></th>
                            <th class="col-2"><?php echo $lang['ubi']; ?></th>
                            <th class="col-1"><?php echo $lang['estado']; ?></th>
                            <th class="col-1"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($params['dispositivos'] as $resultado) {
                        ?>
                            <tr>
                                <td><?php echo $resultado['id_dispositivo']; ?></td>
                                <td><?php echo $resultado['nombre_dispositivo']; ?></td>
                                <td><?php echo $resultado['sede']; ?></td>
                                <td><?php echo $resultado['nombre_ubicacion']; ?></td>
                                <td>
                                    <?php 
                                        if ($resultado['activo'] == '1') {
                                            echo $lang['activo'];
                                        } else {
                                            echo $lang['desact'];
                                        }
                                    ?>
                                </td>
                                <td>
                                    <li class="hvr-icon-back">
                                        <a href="admin_cont.php?controller=index&action=update_dispositivo&id=<?php echo $resultado['id']; ?>">
                                            <div class="hvr-icon" >
                                                <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="hvr-icon-forward">
                                        <button onclick="alertify.confirm('¿Estas seguro de que quieres eliminar el dispositivo?', function(){$(location).attr('href','admin_cont.php?controller=index&action=dispositivos&elim=<?php echo $resultado['id'] ?>')});" style="background-color: transparent; cursor: pointer;">
                                            <div class="hvr-icon">
                                                <i class="bx bxs-trash fs-3" style="color: #99353a;"></i>
                                            </div>
                                        </button>
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
</section>


<script>
    function redirigir() {
        // Obtén los parámetros de la URL actual
        var urlParams = new URLSearchParams(window.location.search);

        // Inicializa la variable para la URL de redirección
        var redirectUrl = '';


        // Verifica si el parámetro 'elim' existe
        if (urlParams.has('elim')) {

            // Construye la URL de redirección para 'dispositivos'
            redirectUrl = `admin_cont.php?controller=index&action=dispositivos`;
        }      
        
        // Si se construyó una URL de redirección, realiza la redirección
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    }

    // Llama a la función con un retraso de 2 segundos
    setTimeout(redirigir, 2000);
</script>

<?php 
include_once("footer.php");
?>