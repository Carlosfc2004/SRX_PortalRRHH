<?php 
include_once("header.php");
?>

<div class="pagetitle">
    <h1><?php echo $lang['menu23']; ?></h1>
</div>
<nav>
	<ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><?php echo $lang['menu21']; ?></a></li>
		<li class="breadcrumb-item active"><?php echo $lang['menu23']; ?></li>
	</ol>
</nav>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
					<br>
					<form action="admin_cont.php?controller=index&action=new_dispositivo&añadido" method="post">
                        <input type="hidden" name="nuevo_dispositivo">
                        <!-- ID dispositivo -->
						<div class="col-md-4">
							<label class="form-label"><b>ID <?php echo $lang['dispo']; ?> *</b></label>
							<input type="text" name="id_dispo" class="form-control" value="" required>
                        </div>
                        <br>

                        <!-- Nombre Dispositivo -->
                        <div class="col-md-4">
                            <label class="form-label"><b><?php echo $lang['nombre']; ?> *</b></label>
							<input type="text" name="nombre_dispo" class="form-control" value="" required>
                        </div>
                        <br>
    
                        <!-- Sede -->
                        <div id="sede" class="col-md-4">
                            <span style="font-weight: bold;"><?php echo $lang['sede']; ?>: </span>
                            <select name="sede" id="nombre_sede" class="form-select">
                                <option value="">--</option>
                                <?php
                                    foreach ($params['sedes'] as $resultado) {
                                ?>
                                    <option value="<?php echo $resultado['sede']; ?>"><?php echo $resultado['sede']; ?></option>
                                <?php
                                    }
                                ?>
                            </select>                        
                        </div>

                        <!-- Ubicación -->
                         <br>
                        <div id="ubicacion" class="col-md-4" style="display: none;">
                            <span style="font-weight: bold;"><?php echo $lang['ubi']; ?>: </span>
                            <select name="ubicacion" id="nombre_ubi" class="form-select">
                                <option value="">--</option>
                            </select>         
                            <p style="text-align: left; width: 98%; margin-top: 15px;">
                                <button class="btn btn-primary mt-3" data-bs-toggle="tooltip" data-bs-placement="right" data-bs-original-title="<?php echo $lang['new_ubi_if']; ?>">
                                    <a style="color: #fff" href="admin_cont.php?controller=index&action=new_ubicacion"><?php echo $lang['menu24']; ?></a>
                                </button>
                            </p>               
                        </div>

                        <p style="text-align: left; width: 98%; margin-top: 15px;">
                            <input type="submit" name="enviar_cont" value="<?php echo $lang['insert']; ?>" class="btn btn-primary mt-3" >
                        </p>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener referencias a los elementos
        const sedeSelect = document.getElementById('nombre_sede');
        const ubicacionDiv = document.getElementById('ubicacion');

        // Función para verificar y mostrar/ocultar ubicación
        function checkSedeAndShowUbicacion() {
            if(sedeSelect.value != "") {
                ubicacionDiv.style.display = 'block';
            } else {
                ubicacionDiv.style.display = 'none';
            }
        }

        // Verificar el estado inicial al cargar la página
        checkSedeAndShowUbicacion();

        // Añadir el evento change al select de sede
        sedeSelect.addEventListener('change', checkSedeAndShowUbicacion);
    });

    document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('dispositivoForm');
    const ubicacionDiv = document.getElementById('ubicacion');
    const ubicacionSelect = document.getElementById('nombre_ubi');
    const sedeDiv = document.getElementById('sede');
    const sedeSelect = document.getElementById('nombre_sede');
    
    // Observer para el campo ubicación
    const observerUbicacion = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.target.style.display !== 'none') {
                ubicacionSelect.setAttribute('required', '');
            } else {
                ubicacionSelect.removeAttribute('required');
            }
        });
    });

    // Observer para el campo sede
        const observerSede = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.target.style.display !== 'none') {
                    sedeSelect.setAttribute('required', '');
                } else {
                    sedeSelect.removeAttribute('required');
                }
            });
        });

        // Observar cambios en ambos divs
        observerUbicacion.observe(ubicacionDiv, { 
            attributes: true, 
            attributeFilter: ['style'] 
        });

        observerSede.observe(sedeDiv, { 
            attributes: true, 
            attributeFilter: ['style'] 
        });

        // Establecer required inicial para sede si está visible
        if (sedeDiv.style.display !== 'none') {
            sedeSelect.setAttribute('required', '');
        }
    });


    function redirigir() {
        // Obtén los parámetros de la URL actual
        var urlParams = new URLSearchParams(window.location.search);

        // Inicializa la variable para la URL de redirección
        var redirectUrl = '';

        // Verifica si el parámetro 'añadido' existe
        if (urlParams.has('añadido')) {
            // Obtén el parámetro 'id'
            // Construye la URL de redirección para 'nuevo dispositivo'
            redirectUrl = `admin_cont.php?controller=index&action=new_dispositivo`;
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