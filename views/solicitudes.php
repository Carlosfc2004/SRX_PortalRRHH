<?php
    // var_dump($params['solicitudes_pendientes']);
    // die;
    include("header.php");

?>

<div class="pagetitle">
    <h1>Solicitudes</h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active">Solicitudes</li>
    </ol>
</nav>

<section class="section">

<!-- Filtros -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Selecciona los filtros para buscar solicitudes</h5>
            <form action="admin_cont.php?controller=index&action=solicitudes" method="post" id="formFiltros" class="row">
                <input type="hidden" name="filtros_sol" value="1">

                <!-- Fecha inicio -->
                <div class="col-md-3 mb-2">
                    <label for="fecha_inicio" class="form-label"><b>Fecha solicitud</b></label>
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" 
                        value="<?php echo isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : ''; ?>">
                </div>

                <!-- Fecha fin -->
                <div class="col-md-3 mb-2">
                    <label for="fecha_fin" class="form-label"><b>Hasta</b></label>
                    <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" 
                        value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>">
                </div>

                <!-- Usuario -->
                <div class="col-md-4 mb-2">
                    <div class="col-md-12">
                        <span style="font-weight: bold;">Cod. Trabajador, Nombre: </span>
                        <?php
                            $selectedValues = isset($_POST['pernr_nom_sol']) ? (array) $_POST['pernr_nom_sol'] : [];
                        ?>
                        <p></p>
                        <select class="form-select select2 h-50" name="pernr_nom_sol[]" id="pernr_nom_sol" multiple>
                            <?php
                            foreach ($params['trabajadores_solicitudes'] as $trabajador) {
                                $selected = in_array($trabajador['pernr'], $selectedValues) ? 'selected' : '';
                                echo '<option value="' . $trabajador['pernr'] . '" ' . $selected . '>' . $trabajador['pernr'] . ' - ' . $trabajador['NOMBREYAPELLIDOS'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="clear m-2"></div>

                <!-- Tipo de asuencia -->
                <div class="col-md-3 mb-2">
                    <label for="tipo_ausencia" class="form-label"><b>Tipo de ausencia</b></label>
                    <select class="form-select" id="tipo_ausencia" name="tipo_ausencia">
                        <option value=""></option>
                        <?php 
                            foreach ($params['tipo_ausencias'] as $tipo) {
                                $selected = (isset($_POST['tipo_ausencia']) && $_POST['tipo_ausencia'] == $tipo['id']) ? 'selected' : '';
                                echo "<option value='" . $tipo['id'] . "' " . $selected . ">" . $tipo['valor'] . "</option>";
                            }
                        ?>
                    </select>
                </div>

                <!-- Estado -->
                <div class="col-md-3 mb-2">
                    <label for="estado" class="form-label"><b>Estado</b></label>
                    <select class="form-select" id="estado" name="estado">
                        <option value="" <?php echo (!isset($_POST['estado']) || $_POST['estado'] == '') ? 'selected' : ''; ?>>Todas</option>
                        <option value="1" <?php echo (!isset($_POST['estado']) || $_POST['estado'] == '1') ? 'selected' : ''; ?>>Pendiente</option>
                        <option value="3" <?php echo (isset($_POST['estado']) && $_POST['estado'] == '3') ? 'selected' : ''; ?>>Aceptada</option>
                        <option value="4" <?php echo (isset($_POST['estado']) && $_POST['estado'] == '4') ? 'selected' : ''; ?>>Rechazada</option>
                        <option value="5" <?php echo (isset($_POST['estado']) && $_POST['estado'] == '5') ? 'selected' : ''; ?>>Anulada</option>
                        <!-- <option value="6" <?php echo (isset($_POST['estado']) && $_POST['estado'] == '6') ? 'selected' : ''; ?>>Pendiente</option> -->
                    </select>
                </div>

                <!-- Justificante -->
                <div class="col-md-3 mb-2">
                    <label class="form-check-label" for="justificante"><b>Justificante</b></label><br>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" id="justificante" name="justificante" <?php echo (isset($_POST['justificante']) && $_POST['justificante'] == 'on') ? 'checked' : ''; ?>>
                    </div>
                </div>

                <div class="clear"><br></div>

                <!-- Botón buscar -->
                <div class="col-md-12">
                    <div class="row align-items-center">
                        <!-- Botón de Exportar -->
                        <div id="boton-exportar-container" class="col-auto">
                            <input type="submit" name="enviar_cont" id="submit_export" value="<?php echo $lang['buscar']; ?>" class="btn btn-primary">
                        </div>

                        <!-- Botón de Cargando Exportar -->
                        <div id="loading-exportar-container" class="col-auto" style="display: none;">
                            <button class="btn btn-primary" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Exportando...
                            </button>
                        </div>
                    </div>
                </div>
                <script>
                    const formulario = document.querySelector('#formFiltros');
                    const btnEnviar = document.querySelector('#submit_export');
                    const envioBtnContainer = document.querySelector('#boton-exportar-container');
                    const loadingBtnContainer = document.querySelector('#loading-exportar-container');

                    formulario.addEventListener('submit', function () {
                        btnEnviar.disabled = true;
                        envioBtnContainer.style.display = 'none';
                        loadingBtnContainer.style.display = 'block';
                    });
                </script>
            </form>
        </div>
    </div>

<!-- FIN Filtros -->

















<!-- Solicitudes  -->


    <?php 
    if (isset($params['solicitudes_pendientes']) && count($params['solicitudes_pendientes']) > 0) {
    ?>
        
    <div class="card">
        <br>
        <div class="card-body">
            <!-- Formulario con los datos de los filtros para exportar en pdf -->
            <h5 style='color: #012970; margin-top: 10px;'><?php echo $lang['metodo_exp']; ?></h5><br>
            <form action="" method="post" id="exportar" style='display: inline-block; margin-left: 15px; margin-bottom: 20px;'>
                <input type="hidden" name="filtros_sol" value="1">
                <input type="hidden" name="fecha_inicio" value="<?php echo isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : ''; ?>">
                <input type="hidden" name="fecha_fin" value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>">
                <input type="hidden" name="pernr_nom_sol" value="<?php echo isset($_POST['pernr_nom_sol']) ? implode(',', $_POST['pernr_nom_sol']) : ''; ?>">
                <input type="hidden" name="tipo_ausencia" value="<?php echo isset($_POST['tipo_ausencia']) ? $_POST['tipo_ausencia'] : ''; ?>">
                <input type="hidden" name="estado" value="<?php echo isset($_POST['estado']) ? $_POST['estado'] : ''; ?>">
                <input type="hidden" name="justificante" value="<?php echo (isset($_POST['justificante']) && $_POST['justificante'] == 'on') ? 'on' : ''; ?>">
                <button type="button" target="_blank" onclick="document.getElementById('exportar').action='exportar.php?solicitudes_pdf'; document.getElementById('exportar').target='_blank'; document.getElementById('exportar').submit();" style="background-color: white; margin-right: 60px;">
                    <img src="img/pdf.png" style="max-width: 100px; width: 50px;">
                </button>

                <button type="button" target="_blank" onclick="document.getElementById('exportar').action='exportar.php?solicitudes_excel'; document.getElementById('exportar').submit();" style="background-color: white;">
                    <img src="img/xls.png" style="max-width: 100px; width: 50px;">
                </button>
            </form>




            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th scope="col">Usuario</th>
                        <th scope="col">Fecha de inicio</th>
                        <th scope="col">Fecha de fin</th>
                        <th scope="col">Total Días</th>
                        <th scope="col">Tipo de ausencia</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Justificante</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $obs = 0;
                    foreach ($params['solicitudes_pendientes'] as $solicitud) {
                        echo "<tr>";
                        echo "<td>" . $solicitud['nombre']. " " . $solicitud['apellidos'] . "<br>".$solicitud['pernr']."</td>";
                        echo "<td>" . date_format($solicitud['fecha_desde'], 'd-m-Y') . "</td>";
                        echo "<td>" . date_format($solicitud['fecha_hasta'], 'd-m-Y') . "</td>";
                        $fecha_desde_t = clone($solicitud['fecha_desde']);
                        $fecha_hasta_t = clone($solicitud['fecha_hasta']);
                        $total_dias = 0;
                        // Iterar a través de las fechas
                        while ($fecha_desde_t <= $fecha_hasta_t) {
                            // Si el día no es sábado (6) ni domingo (7), contar como día laboral
                            if ($fecha_desde_t->format('N') < 6) { // 'N' devuelve el número del día de la semana (1 = lunes, ..., 7 = domingo)
                                $total_dias++;
                            }

                            // Avanzar al siguiente día
                            $fecha_desde_t->modify('+1 day');
                        }

                        echo "<td>" . $total_dias . "</td>";
                        switch ($solicitud['tipo']) {
                            case '1':
                                echo "<td>Vacaciones</td>";
                                break;
                            case '2':
                                echo "<td>Otras ausencias</td>";
                                break;
                            case '3':
                                echo "<td>Festivo local</td>";
                                break;
                            case '4':
                                echo "<td>Asuntos propios</td>";
                                break;
                        }
                        ?>
                        <?php
                        switch ($solicitud['estado']) {
                            case '1':
                                echo "<td style='color: #ffc107'>Pendiente</td>";
                                break;
                            case '2':
                                echo "<td style='color: #dc3545'>Rechazada</td>";
                                break;
                            case '3':
                                echo "<td style='color: #198754'>Aceptada</td>";
                                break;
                            case '4':
                                echo "<td style='color: #dc3545'>Rechazada</td>";
                                break;
                            case '5':
                                echo "<td style='color:#979797'>Anulada</td>";
                                break;
                            case '6':
                                echo "<td style='color: #ffc107'>Pendiente</td>";
                                break;
                            case '7':
                                echo "<td style='color: #ffc107'>Pendiente anulación</td>";
                                break;
                            case '8':
                                echo "<td style='color:#40bc17'>Anulación rechazada, en curso</td>";
                                break;

                        }
                        ?>
                        <td>
                            <?php
                                $justifi = '';

                                // Buscar el nombre del motivo por ID
                                foreach ($params['otras_aus'] as $justi) {
                                    if ($justi['id'] == $solicitud['motivo']) {
                                        $justifi = $justi['Justificante'];
                                        break;
                                    }
                                }
                            ?> 
                            <?php
                                if ($solicitud['tipo'] == 2) {
                                    if ($justifi == 1) {
                                        if ($solicitud['justificante'] != '') {
                                            echo "Entregado";
                                        } else {
                                            echo "No entregado";
                                        }
                                    } else {
                                        echo "No aplica";
                                    }
                                } else {
                                    echo "No aplica";
                                }
                            ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetallesSolicitud_<?php echo $solicitud['id_solicitud']; ?>">
                                <i class="bi bi-eye"></i>
                                <span class="d-none d-sm-inline">Ver detalles</span>
                            </button>
                            <div class="modal fade" id="modalDetallesSolicitud_<?php echo $solicitud['id_solicitud']; ?>" tabindex="-1" aria-labelledby="crearEventoLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Detalles solicitud</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="FechaSol" class="form-label"><b>Fecha Solicitud</b></label>
                                                        <input type="text" class="form-control" value='<?php echo date_format($solicitud['fecha_solicitud'], 'd-m-Y'); ?>' name="FechaSol" id="FechaSol" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="FechaInicioSol" class="form-label"><b>Inicio</b></label>
                                                        <input type="text" class="form-control" value="<?php echo date_format($solicitud['fecha_desde'], 'd-m-Y'); if (isset($solicitud['hora_desde'])) echo " " . $solicitud['hora_desde']; ?>" name="FechaInicioSol" id="FechaInicioSol" disabled>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="FechaFinSol" class="form-label"><b>Fin</b></label>
                                                        <input type="text" class="form-control" value="<?php echo date_format($solicitud['fecha_hasta'], 'd-m-Y'); if (isset($solicitud['hora_hasta'])) echo " " . $solicitud['hora_hasta']; ?>" name="FechaFinSol" id="FechaFinSol" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="tipoSol" class="form-label"><b>Tipo</b></label>
                                                        <input type="text" class="form-control" value='<?php echo $solicitud['tipo'] == '1' ? 'Vacaciones' : ($solicitud['tipo'] == '2' ? 'Otras ausencias' : ($solicitud['tipo'] == '3' ? 'Festivo local' : ($solicitud['tipo'] == '4' ? 'Asuntos propios' : 'Otro'))); ?>' name="tipoSol" id="tipoSol" disabled>
                                                    </div>
                                                    <?php
                                                        $nombreMotivo = '';

                                                        // Buscar el nombre del motivo por ID
                                                        foreach ($params['otras_aus'] as $motivo) {
                                                            if ($motivo['id'] == $solicitud['motivo']) {
                                                                $nombreMotivo = $motivo['tipo_ausencia'];
                                                                break;
                                                            }
                                                        }
                                                    ?> 

                                                    <?php 
                                                        if ($nombreMotivo != '') {
                                                        ?>
                                                            <div class="col-md-6 mb-3">
                                                                <label for="motivo" class="form-label"><b>Motivo</b></label>
                                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($nombreMotivo); ?>" name="motivo" id="motivo" disabled>
                                                            </div>
                                                        <?php
                                                        } else {
                                                            echo '<div class="clear"></div>';
                                                        }
                                                    ?>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="estadoSol" class="form-label"><b>Estado</b></label>
                                                        <input type="text" class="form-control" value='<?php echo $solicitud['estado'] == '1' ? 'Pendiente' : 
                                                                                                                 ($solicitud['estado'] == '3' ? 'Aceptada' : 
                                                                                                                 ($solicitud['estado'] == '4' ? 'Rechazada' : 
                                                                                                                 ($solicitud['estado'] == '5' ? 'Anulada' : 
                                                                                                                 ($solicitud['estado'] == '6' ? 'Pendiente' :
                                                                                                                 ($solicitud['estado'] == '7' ? 'Pendiente anulación' :
                                                                                                                 ($solicitud['estado'] == '8' ? 'Anulación rechazada, en curso' :
                                                                                                                 'Desconocido')))))); ?>' name="estadoSol" id="estadoSol" disabled>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="superior" class="form-label"><b>Superior</b></label>
                                                        <input type="text" class="form-control" value='<?php echo $solicitud['nombre_superior'] ." ". $solicitud['apellidos_superior']; ?>' name="superior" id="superior" disabled>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 mt-2">
                                                        <label for="justificante" class="form-label"><b>Justificante</b></label>
                                                        <?php if ($solicitud['tipo'] == 2) { ?>
                                                            <?php if ($justifi == 1) { ?>
                                                                <?php if ($solicitud['justificante'] != '') { ?>
                                                                    <div class="alert alert-success" role="alert">
                                                                        <b>Justificante entregado</b>
                                                                        <div class="mt-2">
                                                                            <a href="<?php echo $solicitud['justificante']; ?>" target="_blank" class="btn btn-success btn-sm">
                                                                                Ver justificante
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <div class="alert alert-danger" role="alert">
                                                                        <b>Justificante no entregado</b>
                                                                    </div>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <div class="alert alert-info" role="alert">
                                                                    <b>Justificante no aplica</b>
                                                                </div>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <div class="alert alert-info" role="alert">
                                                                <b>Justificante no aplica</b>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>


                                                <div class="row" id="observaciones" style="margin-left: 2%;">
                                                    <div class="col-md-12">
                                                        <b>Observaciones</b>
                                                        <ul class="timeline" id="timeline">
                                                            <?php  
                                                                for ($i = 0; $i < 3; $i++) {
                                                                    if (isset($solicitud['observaciones'][$i])) { ?>
                                                                        <li class="timeline-item mb-4 mt-2" id="obs_item_<?php echo $i + 1; ?>">
                                                                            <p class="fw-bold" style="font-size: 17px;" id="nombre_<?php echo $i + 1; ?>">
                                                                                <?php 
                                                                                    if (isset($solicitud['observaciones'][$i]['tipo_coment']) && $solicitud['observaciones'][$i]['tipo_coment'] == 'RRHH') {
                                                                                        echo "RRHH";
                                                                                    } else {
                                                                                        echo !empty($solicitud['observaciones'][$i]['nombre']) ? $solicitud['observaciones'][$i]['nombre'] : ''; 
                                                                                    }
                                                                                ?>
                                                                            </p>
                                                                            <p class="text-muted mb-2 fw-bold" id="fecha_mod_<?php echo $i + 1; ?>">
                                                                                <?php echo !empty($solicitud['observaciones'][$i]['fecha_modificacion']) ? $solicitud['observaciones'][$i]['fecha_modificacion']->format('Y-m-d H:i') : ''; ?>
                                                                            </p>
                                                                            <p class="text-muted" id="observacion_<?php echo $i + 1; ?>">
                                                                                <?php echo !empty($solicitud['observaciones'][$i]['comentario']) ? $solicitud['observaciones'][$i]['comentario'] : ''; ?>
                                                                            </p>
                                                                        </li>
                                                                    <?php 
                                                                    }  
                                                                } 
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </div>   
                                            </div>
                                            
                                            <?php 
                                                $tipo_rrhh = false;

                                                // Verificamos la existencia de los índices antes de acceder a ellos
                                                for ($i = 0; $i < 3; $i++) {
                                                    if (isset($solicitud['observaciones'][$i]['tipo_coment']) && $solicitud['observaciones'][$i]['tipo_coment'] == 'RRHH') {
                                                        $tipo_rrhh = true;
                                                        break;
                                                    }
                                                }
                                                if (!$tipo_rrhh) {
                                                ?>
                                                    <form class="mb-3" action="admin_cont.php?controller=index&action=solicitudes&addComentario" method="post" id="formComentario">
                                                        <input type="hidden" name="observacion">
                                                        <input type="hidden" name="id_sol" value="<?php echo $solicitud['id_solicitud']; ?>">
                                                        <input type="hidden" name="pernr_mod" value="<?php echo $_SESSION['id_user_surexport_appreclu']; ?>">
                                                        <input type="hidden" name="fecha_crea" value="<?php echo date('Y-m-d H:i:s'); ?>">
                                                        <input type="hidden" name="fecha" value="<?php echo date('Y-m-d'); ?>">
                                                        <input type="hidden" name="pernr_usu" id="pernr_usu" value="<?php echo $solicitud['pernr']; ?>">
                                                        <div class="row">
                                                            <div class="col-md-9">
                                                                <input placeholder="Añadir comentario..." class="form-control" name="comentario" id="comentario" required>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <button type="submit" value="enviar" class="btn btn-primary">Añadir</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                <?php
                                                } else {
                                                    
                                                }
                                            ?>

                                            <?php 
                                            if ($solicitud['estado'] == '3' || $solicitud['estado'] == '4') {

                                            } else {
                                                if ($solicitud['estado'] != '5' && $solicitud['estado'] != '8') {
                                            ?>
                                                <!-- div con linea separatoria del body con el footer del modal -->
                                                <div class="dropdown-divider" style="background-color: #dee2e6; height: 1px; width: calc(100% + 2rem); margin-left: -1rem;" id="linea_div"></div>

                                                <div class="mt-3" id="acciones">
                                                    <form action="admin_cont.php?controller=index&action=solicitudes&comunicado_rrhh" method="post" id="formAcciones">
                                                        <input type="hidden" name="pernr_usu" id="pernr_usu" value="<?php echo $solicitud['pernr']; ?>">
                                                        <input type="hidden" name="fecha_res_rrhh" value="<?= date('Y-m-d'); ?>">
                                                        <input type="hidden" name="firma_rrhh" value="1">
                                                        <input type="hidden" name="id_sol" value="<?php echo $solicitud['id_solicitud']; ?>" id="id_sol">
                                                        <input type="hidden" name="mail_s" value="<?php echo $solicitud['mail_s']; ?>">
                                                        <input type="hidden" name="nombre_s" value="<?php echo $solicitud['nombre_superior'] . " " . $solicitud['apellidos_superior']; ?>">
                                                        <input type="hidden" name="nombre" value="<?php echo $solicitud['nombre'] . " " . $solicitud['apellidos']; ?>">
                                                        <input type="hidden" name="mail" value="<?php echo $solicitud['mail']; ?>">
                                                        <input type="hidden" name="estado" value="<?php echo $solicitud['estado']; ?>">
                                                        <input type="hidden" name="fecha_sol" value="<?php echo date_format($solicitud['fecha_solicitud'], 'd-m-Y'); ?>">
                                                        <button type="submit" class='btn btn-success' name="aceptar" id="btnAceptar">Aprobar</button>
                                                        <button type="submit" class='btn btn-danger' name="rechazar" id="btnRechazar">Rechazar</button>
                                                    </form>
                                                    <script>
                                                        document.addEventListener('DOMContentLoaded', function () {
                                                            const form = document.getElementById('formAcciones');
                                                            const btnAceptar = document.getElementById('btnAceptar');
                                                            const btnRechazar = document.getElementById('btnRechazar');

                                                            let accion = ''; // "aceptar" o "rechazar"

                                                            btnAceptar.addEventListener('click', function (e) {
                                                                e.preventDefault();
                                                                accion = 'aceptar';
                                                                confirmarEnvio();
                                                            });

                                                            btnRechazar.addEventListener('click', function (e) {
                                                                e.preventDefault();
                                                                accion = 'rechazar';
                                                                confirmarEnvio();
                                                            });

                                                            function confirmarEnvio() {
                                                                const mensaje = (accion === 'aceptar') 
                                                                    ? '¿Estás seguro de que deseas aprobar esta solicitud?' 
                                                                    : '¿Estás seguro de que deseas rechazar esta solicitud?';

                                                                alertify.confirm('Confirmar acción', mensaje,
                                                                    function () {
                                                                        // Crear input dinámico con el name de la acción que se confirmó
                                                                        const inputAccion = document.createElement('input');
                                                                        inputAccion.type = 'hidden';
                                                                        inputAccion.name = accion;
                                                                        form.appendChild(inputAccion);

                                                                        form.submit();
                                                                    },
                                                                    function () {
                                                                        alertify.error('Acción cancelada');
                                                                    }
                                                                );
                                                            }
                                                        });
                                                        </script>
                                                </div>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <?php
                        echo "</tr>";
                        $obs++;
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>

    <?php
    } else {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filtros_sol'])) {
            if (isset($_POST['filtros_sol'])) {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        No se han encontrado solicitudes con los filtros seleccionados.
                    </div>";
            }
        } else {

        }
    } 
    ?>

<!-- FIN Solicitudes  -->


</section>


<script>
    function redirigir() {
        // Obtén los parámetros de la URL actual
        var urlParams = new URLSearchParams(window.location.search);

        // Inicializa la variable para la URL de redirección
        var redirectUrl = '';

        // Verifica si el parámetro 'comunicado_rrhh' existe
        if (urlParams.has('comunicado_rrhh')) { 
            // Construye la URL de redirección para 'llamamiento'
            redirectUrl = `admin_cont.php?controller=index&action=solicitudes`;
        } else if (urlParams.has('addComentario')) {
            // Construye la URL de redirección para 'llamamiento'
            redirectUrl = `admin_cont.php?controller=index&action=solicitudes`;
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
    include("footer.php");
?>