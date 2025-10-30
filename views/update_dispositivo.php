<?php 
include_once("header.php"); 
?>
<div class="pagetitle">
    <h1><?php echo $lang['act_dispo']; ?></h1>
	<button type="submit" class="atras">
		<a class="bi bi-arrow-left-square-fill" href="admin_cont.php?controller=index&action=dispositivos"></a>
	</button>
</div>
	<nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><?php echo $lang['menu21']; ?></li>
            <li class="breadcrumb-item active"><?php echo $lang['act_dispo']; ?></li>
        </ol>
    </nav>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                <h5 class="card-title">Campos disponibles para actualizar: Nombre dispositivo, Estado</h5>
                    
                    <form action="admin_cont.php?controller=index&action=update_dispositivo&id=<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>&actualizado" method="post" class="needs-validation" novalidate>
                        <div class="row">
                            <!-- ID y nombre -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_dispositivo" class="form-label fw-bold">ID <?php echo $lang['dispo']; ?></label>
                                    <input type="text" 
                                        class="form-control" 
                                        name="id_dispositivo" 
                                        id="id_dispositivo" 
                                        style="background-color: #dee2e6ff;"
                                        value="<?php echo htmlspecialchars($params['info_dispositivo'][0]['id_dispositivo']); ?>" 
                                        readonly>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nombre_dispositivo" class="form-label fw-bold"><?php echo $lang['nombre']." ".$lang['dispo']; ?></label>
                                    <input type="text" 
                                        class="form-control" 
                                        name="nombre_dispositivo" 
                                        id="nombre_dispositivo" 
                                        value="<?php echo htmlspecialchars($params['info_dispositivo'][0]['nombre']); ?>">
                                </div>
                            </div>

                            <!-- Localizacion -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ubi_dispositivo" class="form-label fw-bold"><?php echo $lang['ubi']; ?></label>
                                    <input type="text" 
                                        class="form-control" 
                                        name="ubi_dispositivo" 
                                        id="ubi_dispositivo" 
                                        style="background-color: #dee2e6ff;"
                                        value="<?php echo htmlspecialchars($params['info_dispositivo'][0]['ubicacion']); ?>"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="sede_dispositivo" class="form-label fw-bold"><?php echo $lang['sede']; ?></label>
                                    <input type="text" 
                                        class="form-control" 
                                        name="sede_dispositivo" 
                                        id="sede_dispositivo" 
                                        style="background-color: #dee2e6ff;"
                                        value="<?php echo htmlspecialchars($params['info_dispositivo'][0]['sede']); ?>"
                                        readonly>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ubicacion_dispositivo" class="form-label fw-bold"><?php echo $lang['nombre']." ".$lang['ubi']; ?></label>
                                    <input type="text" 
                                        class="form-control" 
                                        name="ubicacion_dispositivo" 
                                        id="ubicacion_dispositivo" 
                                        style="background-color: #dee2e6ff;"
                                        value="<?php echo htmlspecialchars($params['info_dispositivo'][0]['nombre_ubi']); ?>"
                                        readonly>
                                </div>
                            </div>

                            <!-- Estado -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="activo_dispositivo" class="form-label fw-bold"><?php echo $lang['estado']; ?></label>
                                    <select class="form-select" name="activo_dispositivo" id="activo_dispositivo">
                                        <option value="1" <?php echo $params['info_dispositivo'][0]['activo'] == 1 ? 'selected' : ''; ?>><?php echo $lang['activo']; ?></option>
                                        <option value="0" <?php echo $params['info_dispositivo'][0]['activo'] == 0 ? 'selected' : ''; ?>><?php echo $lang['desact']; ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Boton enviar -->
                        <?php 
                            if ($_SESSION["tipo_user_surexport_appreclu"] != 'Supervisor') {
                            ?>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" name="guardar" class="btn btn-primary">
                                            <?php echo $lang['guardar']; ?>
                                        </button>
                                    </div>
                                </div>
                            <?php 
                            } 
                        ?>	
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    (function () {
        'use strict'
        
        // Fetch all forms that need validation
        var forms = document.querySelectorAll('.needs-validation')
        
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

    function redirigir() {
        // Obtén los parámetros de la URL actual
        var urlParams = new URLSearchParams(window.location.search);

        // Inicializa la variable para la URL de redirección
        var redirectUrl = '';

        // Verifica si el parámetro 'tipo_llamamiento' existe
        if (urlParams.has('actualizado')) {
            // Obtén el parámetro 'id'
            var pernr = urlParams.get('id');
            
            // Construye la URL de redirección para 'llamamiento'
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