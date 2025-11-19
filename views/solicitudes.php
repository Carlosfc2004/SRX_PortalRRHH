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
                        echo "<td>" . $solicitud['nombre_trabajador'] . "<br>".$solicitud['pernr']."</td>";
                        echo "<td>" . date_format($solicitud['fecha_desde'], 'd-m-Y') . "</td>";
                        echo "<td>" . date_format($solicitud['fecha_hasta'], 'd-m-Y') . "</td>";

                        // El total de días ya viene calculado desde el modelo
                        echo "<td>" . ($solicitud['total_dias'] ?? 0) . "</td>";
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
                            <?php
                                // Botones de acción (si el estado lo permite)
                                if ($solicitud['estado'] != '3' && $solicitud['estado'] != '4' && $solicitud['estado'] != '5' && $solicitud['estado'] != '8') {
                                        echo "<form action='admin_cont.php?controller=index&action=solicitudes&comunicado_rrhh' method='post' id='formAcciones_".$solicitud['id_solicitud']."' style='display: inline-block; margin-right: 5px;'>
                                                <input type='hidden' name='pernr_usu' value='".$solicitud['pernr']."'>
                                                <input type='hidden' name='fecha_res_rrhh' value='".date('Y-m-d')."'>
                                                <input type='hidden' name='firma_rrhh' value='1'>
                                                <input type='hidden' name='id_sol' value='".$solicitud['id_solicitud']."'>
                                                <input type='hidden' name='mail_s' value='".$solicitud['mail_s']."'>
                                                <input type='hidden' name='mail' value='".$solicitud['mail']."'>
                                                <input type='hidden' name='estado' value='".$solicitud['estado']."'>
                                                <input type='hidden' name='fecha_sol' value='".$solicitud['fecha_solicitud']->format("Y-m-d")."'>
                                                <input type='hidden' name='nombre' value='".$solicitud['nombre_trabajador']."'>
                                                <input type='hidden' name='nombre_s' value='".$solicitud['nombre_superior']."'>

                                                <button type='button' class='btn btn-sm btn-success btn-aceptar-solicitud' data-form-id='formAcciones_".$solicitud['id_solicitud']."'><i class='bi bi-check-circle'></i></button>
                                                <button type='button' class='btn btn-sm btn-danger btn-rechazar-solicitud' data-form-id='formAcciones_".$solicitud['id_solicitud']."'><i class='bi bi-x-circle'></i></button>
                                            </form>";
                                }
                            ?>
                            <button type="button" class="btn btn-primary btn-sm btn-ver-detalle"
                                    data-id-solicitud="<?php echo $solicitud['id_solicitud']; ?>"
                                    data-pernr="<?php echo $solicitud['pernr']; ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetallesSolicitud"
                                    style="display: inline-block;">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                        <?php
                        echo "</tr>";
                        $obs++;
                    }
                    ?>                </tbody>
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

<!-- Modal único para detalles de solicitud -->
<div class="modal fade" id="modalDetallesSolicitud" tabindex="-1" aria-labelledby="modalDetallesSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetallesSolicitudLabel">Detalles solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalDetallesContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando detalles...</p>
                </div>
            </div>
        </div>
    </div>
</div>

</section>

<script>
    // AJAX para cargar detalles de solicitud
    document.addEventListener('DOMContentLoaded', function() {
        const modalContent = document.getElementById('modalDetallesContent');
        const otrasAusencias = <?php echo json_encode($params['otras_aus']); ?>;

        // Usar delegación de eventos en el documento para que funcione con DataTables
        document.addEventListener('click', function(e) {
            // Verificar si el click fue en un botón con la clase .btn-ver-detalle
            if (e.target.closest('.btn-ver-detalle')) {
                const boton = e.target.closest('.btn-ver-detalle');
                const idSolicitud = boton.getAttribute('data-id-solicitud');
                const pernr = boton.getAttribute('data-pernr');
                
                // Mostrar spinner
                modalContent.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando detalles...</p>
                    </div>
                `;
                
                // Petición AJAX
                fetch('auto.php?obtener_detalle_solicitud&id_solicitud=' + idSolicitud)
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            const solicitud = result.data;
                            
                            // Obtener nombre del motivo
                            let nombreMotivo = '';
                            let justifi = '';
                            if (solicitud.motivo) {
                                const motivo = otrasAusencias.find(m => m.id == solicitud.motivo);
                                if (motivo) {
                                    nombreMotivo = motivo.tipo_ausencia;
                                    justifi = motivo.Justificante;
                                }
                            }
                            
                            // Mapear tipo de ausencia
                            const tipoTexto = {
                                '1': 'Vacaciones',
                                '2': 'Otras ausencias',
                                '3': 'Festivo local',
                                '4': 'Asuntos propios'
                            }[solicitud.tipo] || 'Otro';
                            
                            // Mapear estado
                            const estadoTexto = {
                                '1': 'Pendiente',
                                '3': 'Aceptada',
                                '4': 'Rechazada',
                                '5': 'Anulada',
                                '6': 'Pendiente',
                                '7': 'Pendiente anulación',
                                '8': 'Anulación rechazada, en curso'
                            }[solicitud.estado] || 'Desconocido';
                            
                            // Construir HTML
                            let html = `
                                <div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><b>Fecha Solicitud</b></label>
                                            <input type="text" class="form-control" value="${solicitud.fecha_solicitud_formatted || ''}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><b>Inicio</b></label>
                                            <input type="text" class="form-control" value="${solicitud.fecha_desde_formatted || ''} ${solicitud.hora_desde_formatted || ''}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><b>Fin</b></label>
                                            <input type="text" class="form-control" value="${solicitud.fecha_hasta_formatted || ''} ${solicitud.hora_hasta_formatted || ''}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><b>Tipo</b></label>
                                            <input type="text" class="form-control" value="${tipoTexto}" disabled>
                                        </div>`;
                            
                            if (nombreMotivo) {
                                html += `
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><b>Motivo</b></label>
                                            <input type="text" class="form-control" value="${nombreMotivo}" disabled>
                                        </div>`;
                            }
                            
                            html += `
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><b>Estado</b></label>
                                            <input type="text" class="form-control" value="${estadoTexto}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><b>Superior</b></label>
                                            <input type="text" class="form-control" value="${solicitud.nombre_superior || ''}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mt-2">
                                            <label class="form-label"><b>Justificante</b></label>`;
                            
                            // Justificante
                            if (solicitud.tipo == 2) {
                                if (justifi == 1) {
                                    if (solicitud.justificante && solicitud.justificante != '') {
                                        html += `
                                            <div class="alert alert-success" role="alert">
                                                <b>Justificante entregado</b>
                                                <div class="mt-2">
                                                    <a href="${solicitud.justificante}" target="_blank" class="btn btn-success btn-sm">Ver justificante</a>
                                                </div>
                                            </div>`;
                                    } else {
                                        html += `<div class="alert alert-danger" role="alert"><b>Justificante no entregado</b></div>`;
                                    }
                                } else {
                                    html += `<div class="alert alert-info" role="alert"><b>Justificante no aplica</b></div>`;
                                }
                            } else {
                                html += `<div class="alert alert-info" role="alert"><b>Justificante no aplica</b></div>`;
                            }
                            
                            html += `
                                        </div>
                                    </div>
                                    
                                    <div class="row" style="margin-left: 2%;">
                                        <div class="col-md-12">
                                            <b>Observaciones</b>
                                            <ul class="timeline">`;
                            
                            // Observaciones
                            let tipoRrhh = false;
                            if (solicitud.observaciones && solicitud.observaciones.length > 0) {
                                solicitud.observaciones.slice(0, 3).forEach(function(obs) {
                                    if (obs.tipo_coment == 'RRHH') {
                                        tipoRrhh = true;
                                    }
                                    // tipo_coment Todo en minúsculas menos la primera que debe ser mayúscula si es EMPLEADO O SUPERIOR, no mostrar si es RRHH
                                    const tipoObs = obs.tipo_coment === 'RRHH' ? '' : ' (' + obs.tipo_coment.charAt(0).toUpperCase() + obs.tipo_coment.slice(1).toLowerCase() + ')';

                                    html += `
                                        <li class="timeline-item mb-4 mt-2">
                                            <span class="fw-bold">
                                                ${obs.tipo_coment == 'RRHH' ? 'RRHH' : (obs.nombre || '')}${tipoObs}
                                            </span>
                                            <p class="text-muted mb-0 fw-bold">
                                                ${obs.fecha_modificacion_formatted || ''}
                                            </p>
                                            <p class="text-muted">
                                                ${obs.comentario || ''}
                                            </p>
                                        </li>`;
                                });
                            }
                            
                            html += `
                                            </ul>
                                        </div>
                                    </div>
                                </div>`;
                            
                            // Formulario para añadir comentario (si no es RRHH)
                            if (!tipoRrhh) {
                                html += `
                                    <form class="mb-3" action="admin_cont.php?controller=index&action=solicitudes&addComentario" method="post">
                                        <input type="hidden" name="observacion">
                                        <input type="hidden" name="id_sol" value="${solicitud.id_solicitud}">
                                        <input type="hidden" name="pernr_mod" value="<?php echo $_SESSION['id_user_surexport_appreclu']; ?>">
                                        <input type="hidden" name="pernr_usu" value="${solicitud.pernr}">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <input placeholder="Añadir comentario..." class="form-control" name="comentario" required>
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary">Añadir</button>
                                            </div>
                                        </div>
                                    </form>`;
                            }
                            
                            // Botones de acción (si el estado lo permite)
                            if (solicitud.estado != '3' && solicitud.estado != '4' && solicitud.estado != '5' && solicitud.estado != '8') {
                                html += `
                                    <div class="dropdown-divider" style="background-color: #dee2e6; height: 1px; width: calc(100% + 2rem); margin-left: -1rem;"></div>
                                    <div class="mt-3">
                                        <form action="admin_cont.php?controller=index&action=solicitudes&comunicado_rrhh" method="post" id="formAcciones_${solicitud.id_solicitud}">
                                            <input type="hidden" name="pernr_usu" value="${solicitud.pernr}">
                                            <input type="hidden" name="fecha_res_rrhh" value="${new Date().toISOString().slice(0, 10)}">
                                            <input type="hidden" name="firma_rrhh" value="1">
                                            <input type="hidden" name="id_sol" value="${solicitud.id_solicitud}">
                                            <input type="hidden" name="mail_s" value="${solicitud.mail_s || ''}">
                                            <input type="hidden" name="nombre_s" value="${solicitud.nombre_superior || ''} ${solicitud.apellidos_superior || ''}">
                                            <input type="hidden" name="nombre" value="${solicitud.nombre || ''} ${solicitud.apellidos || ''}">
                                            <input type="hidden" name="mail" value="${solicitud.mail || ''}">
                                            <input type="hidden" name="estado" value="${solicitud.estado}">
                                            <input type="hidden" name="fecha_sol" value="${solicitud.fecha_solicitud_formatted || ''}">
                                            <button type="button" class="btn btn-success btn-aceptar-solicitud" data-form-id="formAcciones_${solicitud.id_solicitud}">Aprobar</button>
                                            <button type="button" class="btn btn-danger btn-rechazar-solicitud" data-form-id="formAcciones_${solicitud.id_solicitud}">Rechazar</button>
                                        </form>
                                    </div>`;
                            }
                            
                            modalContent.innerHTML = html;
                            
                        } else {
                            modalContent.innerHTML = `
                                <div class="alert alert-danger" role="alert">
                                    <strong>Error:</strong> ${result.error || 'No se pudieron cargar los detalles'}
                                </div>`;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        modalContent.innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                <strong>Error:</strong> Error al cargar los detalles de la solicitud
                            </div>`;
                    });
            }
        });
        
        // Event listeners globales para botones de aceptar/rechazar (funcionan en tabla y modal)
        document.addEventListener('click', function(e) {
            // Botón Aceptar
            if (e.target.closest('.btn-aceptar-solicitud')) {
                e.preventDefault();
                const boton = e.target.closest('.btn-aceptar-solicitud');
                const formId = boton.getAttribute('data-form-id');
                const form = document.getElementById(formId);
                
                if (form) {
                    alertify.confirm('Confirmar acción', '¿Estás seguro de que deseas aprobar esta solicitud?',
                        function() {
                            const inputAccion = document.createElement('input');
                            inputAccion.type = 'hidden';
                            inputAccion.name = 'aceptar';
                            form.appendChild(inputAccion);
                            form.submit();
                        },
                        function() {
                            alertify.error('Acción cancelada');
                        }
                    );
                }
            }
            
            // Botón Rechazar
            if (e.target.closest('.btn-rechazar-solicitud')) {
                e.preventDefault();
                const boton = e.target.closest('.btn-rechazar-solicitud');
                const formId = boton.getAttribute('data-form-id');
                const form = document.getElementById(formId);
                
                if (form) {
                    alertify.confirm('Confirmar acción', '¿Estás seguro de que deseas rechazar esta solicitud?',
                        function() {
                            const inputAccion = document.createElement('input');
                            inputAccion.type = 'hidden';
                            inputAccion.name = 'rechazar';
                            form.appendChild(inputAccion);
                            form.submit();
                        },
                        function() {
                            alertify.error('Acción cancelada');
                        }
                    );
                }
            }
        });
    });
    
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
    // setTimeout(redirigir, 2000);
</script>


<?php
    include("footer.php");
?>