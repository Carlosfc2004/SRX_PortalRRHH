<?php
include_once("header.php");
?>
<div class="pagetitle">
    <h1>Gestión del horario laboral</h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="admin_cont.php?controller=index&action=home">
                <i class="bi bi-house-door"></i>
            </a>
        </li>
        <li class="breadcrumb-item active">Horario laboral</li>
    </ol>
</nav>

<style>
    /* Estilo para días sin configurar en el calendario */
    .calendar td.sin_configurar {
        background-color: #f0f0f0 !important;
        color: #666;
    }

    /* Estilos para botones de Alertify */
    .ajs-footer .ajs-buttons .ajs-button {
        padding: 6px 16px;
        margin: 4px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .ajs-footer .ajs-buttons .ajs-button.ajs-ok {
        background-color: #0d6efd;
        color: white;
        border: 1px solid #0d6efd;
    }

    .ajs-footer .ajs-buttons .ajs-button.ajs-ok:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }

    .ajs-footer .ajs-buttons .ajs-button.ajs-cancel {
        background-color: #6c757d;
        color: white;
        border: 1px solid #6c757d;
    }

    .ajs-footer .ajs-buttons .ajs-button.ajs-cancel:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    /* Estilo para hover en elementos */
    .hover-bg:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
</style>

<section class="section">
    <div class="card">
        <br>
        <div class="card-body">
            <!-- Botón para añadir nuevo grupo -->
            <button type="button" class="btn btn-success mb-3" data-bs-toggle="modal"
                data-bs-target="#modalNuevoGrupoHorario">
                Añadir nuevo grupo
            </button>

            <!-- Tabla de grupos existentes -->
            <h5 class="card-title mb-0">Grupos de horario existentes</h5>
            <div class="table-responsive">
                <table class="table table-hover datatable">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Dias configurados</th>
                            <th>Predeterminado</th>
                            <th data-sortable="false">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($params['grupos_horario']) && is_array($params['grupos_horario']) && count($params['grupos_horario']) > 0) {
                            foreach ($params['grupos_horario'] as $grupo) { ?>
                                <tr <?php if ($grupo['grupo_predeterminado'] == 1)
                                    echo 'class="table-success" title="Grupo predeterminado"'; ?>>
                                    <td class="fw-bold">
                                        <?= htmlspecialchars($grupo['nombre_grupo']) ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($grupo['descripcion_grupo'] ?? '') ?>
                                    </td>
                                    <td>
                                        <?php
                                        $franjas = json_decode($grupo['franjas_json'], true);
                                        if (is_array($franjas) && count($franjas) > 0) {
                                            // Determinar el año a evaluar
                                            $añoActual = null;

                                            // Primero intentar usar el año de configuración del grupo
                                            if (isset($grupo['anio_configuracion']) && !empty($grupo['anio_configuracion'])) {
                                                $añoActual = $grupo['anio_configuracion'];
                                            } else {
                                                // Si no hay año configurado, extraerlo de la primera franja
                                                foreach ($franjas as $franja) {
                                                    if (isset($franja['inicio_fecha'])) {
                                                        $añoActual = date('Y', strtotime($franja['inicio_fecha']));
                                                        break;
                                                    }
                                                }
                                                // Si tampoco hay franjas con fecha, usar año actual
                                                if (!$añoActual) {
                                                    $añoActual = date('Y');
                                                }
                                            }

                                            $diasDelAño = (date('L', strtotime("$añoActual-01-01")) ? 366 : 365);
                                            $diasUnicos = [];
                                            $diasSemanaCubiertos = []; // Para rastrear qué días de la semana están cubiertos
                                
                                            foreach ($franjas as $franja) {
                                                if (isset($franja['inicio_fecha']) && isset($franja['fin_fecha'])) {
                                                    $inicio = new DateTime($franja['inicio_fecha']);
                                                    $fin = new DateTime($franja['fin_fecha']);
                                                    $fin->modify('+1 day'); // Para incluir el último día
                                
                                                    $periodo = new DatePeriod($inicio, new DateInterval('P1D'), $fin);

                                                    // Verificar si es festivo (no tiene días de semana configurados)
                                                    $esFestivo = isset($franja['tipo_jornada']) &&
                                                        ($franja['tipo_jornada'] === 'festivo_nacional' ||
                                                            $franja['tipo_jornada'] === 'festivo_autonomico');

                                                    foreach ($periodo as $fecha) {
                                                        $fechaStr = $fecha->format('Y-m-d');
                                                        if ($esFestivo) {
                                                            // Para festivos, marcar todos los días del rango
                                                            $diasUnicos[$fechaStr] = true;
                                                        } else {
                                                            // Para jornadas normales, verificar días de la semana
                                                            if (isset($franja['dias_semana']) && is_array($franja['dias_semana'])) {
                                                                $diaSemana = (int) $fecha->format('w'); // 0=domingo, 1=lunes, ..., 6=sábado
                                                                if (in_array($diaSemana, $franja['dias_semana'])) {
                                                                    $diasUnicos[$fechaStr] = true;
                                                                    $diasSemanaCubiertos[$diaSemana] = true;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            $diasConfigurados = count($diasUnicos);

                                            // Identificar días de la semana no configurados
                                            $nombresDias = [
                                                0 => 'Domingo',
                                                1 => 'Lunes',
                                                2 => 'Martes',
                                                3 => 'Miércoles',
                                                4 => 'Jueves',
                                                5 => 'Viernes',
                                                6 => 'Sábado'
                                            ];

                                            $diasNoConfigurados = [];
                                            $contadorDiasNoConfig = [];
                                            $fechasNoConfiguradas = []; // Para guardar las fechas exactas
                                
                                            // Recorrer todo el año y contar qué días no están configurados
                                            $inicioAño = new DateTime("$añoActual-01-01");
                                            $finAño = new DateTime("$añoActual-12-31");
                                            $finAño->modify('+1 day');

                                            $periodoAño = new DatePeriod($inicioAño, new DateInterval('P1D'), $finAño);

                                            foreach ($periodoAño as $fecha) {
                                                $fechaStr = $fecha->format('Y-m-d');
                                                if (!isset($diasUnicos[$fechaStr])) {
                                                    $diaSemana = (int) $fecha->format('w');
                                                    if (!isset($contadorDiasNoConfig[$diaSemana])) {
                                                        $contadorDiasNoConfig[$diaSemana] = 0;
                                                        $fechasNoConfiguradas[$diaSemana] = [];
                                                    }
                                                    $contadorDiasNoConfig[$diaSemana]++;
                                                    $fechasNoConfiguradas[$diaSemana][] = $fechaStr;
                                                }
                                            }

                                            // Crear el texto del tooltip
                                            $tooltipText = '';
                                            if (count($contadorDiasNoConfig) > 0) {
                                                $tooltipText = '<strong>Días sin configurar:</strong><br>';
                                                // Ordenar los días de la semana
                                                $ordenDias = [1, 2, 3, 4, 5, 6, 0]; // Lunes a Domingo
                                                foreach ($ordenDias as $diaSemana) {
                                                    if (isset($contadorDiasNoConfig[$diaSemana])) {
                                                        $cantidad = $contadorDiasNoConfig[$diaSemana];
                                                        $tooltipText .= "• $cantidad " . ($cantidad == 1 ? $nombresDias[$diaSemana] : $nombresDias[$diaSemana]) . "<br>";
                                                    }
                                                }
                                            } else {
                                                $tooltipText = '<strong>✓ Todos los días del año están configurados</strong>';
                                            }
                                            ?>
                                            <span class="badge bg-primary cursor-pointer" data-bs-toggle="tooltip"
                                                data-bs-placement="top" data-bs-html="true"
                                                title="<?= htmlspecialchars($tooltipText, ENT_QUOTES, 'UTF-8') ?>"
                                                data-grupo-id="<?= $grupo['id'] ?>"
                                                data-grupo-nombre="<?= htmlspecialchars($grupo['nombre_grupo'], ENT_QUOTES, 'UTF-8') ?>"
                                                data-fechas-no-config='<?= json_encode($fechasNoConfiguradas) ?>'
                                                onclick="mostrarDiasNoConfiguradosClick(this)" style="cursor: pointer;">
                                                <?= $diasConfigurados ?> / <?= $diasDelAño ?> días
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge bg-warning" data-bs-toggle="tooltip" data-bs-placement="top"
                                                data-bs-html="true" title="<strong>⚠ No hay días configurados</strong>">
                                                0 / <?= (date('L') ? 366 : 365) ?> días
                                            </span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($grupo['grupo_predeterminado'] == 1) { ?>
                                            <span class="badge bg-success" title="Grupo predeterminado para el año <?= $grupo['anio_configuracion'] ?>">
                                                Sí (<?= $grupo['anio_configuracion'] ?>)
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-info btn-sm" title="Ver calendario"
                                                onclick="abrirCalendarioGrupo(<?= htmlspecialchars(json_encode($grupo), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="bi bi-calendar4-week"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-primary btn-sm" title="Editar grupo"
                                                onclick="abrirModalEditar(<?= htmlspecialchars(json_encode($grupo), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-success btn-sm" title="Acción múltiple"
                                                onclick="accionMultipleGrupo(<?= $grupo['id'] ?>, '<?= htmlspecialchars($grupo['nombre_grupo']) ?>', <?= $grupo['anio_configuracion'] ?>)">
                                                <i class="bi bi-people-fill"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm"
                                                title="Clonar grupo a otro año"
                                                onclick="abrirModalClonarGrupo(<?= htmlspecialchars(json_encode($grupo), ENT_QUOTES, 'UTF-8') ?>)">
                                                <i class="bi bi-files"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" title="Eliminar grupo"
                                                onclick="confirmarEliminar(<?= $grupo['id'] ?>, '<?= htmlspecialchars($grupo['nombre_grupo']) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-clock-history fs-1 d-block mb-2"></i>
                                    No hay grupos de horario configurados<br>
                                    <small>Haz clic en "Añadir nuevo grupo" para crear tu primer grupo</small>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>



            <!-- Modal para crear grupo de horario laboral -->
            <div class="modal fade" id="modalNuevoGrupoHorario" tabindex="-1"
                aria-labelledby="modalNuevoGrupoHorarioLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <form action="admin_cont.php?controller=horarios&action=horario" method="POST"
                            id="form-nuevo-grupo-horario">
                            <input type="hidden" name="nuevo_grupo" value="1">
                            <input type="hidden" id="franjas_json" name="franjas_json" value="[]">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalNuevoGrupoHorarioLabel">Crear grupo de horario laboral
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Cerrar"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nombre_grupo" class="form-label fw-bold">Nombre del horario</label>
                                    <input type="text" class="form-control" id="nombre_grupo" name="nombre_grupo"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="descripcion_grupo" class="form-label fw-bold">Descripción</label>
                                    <input type="text" class="form-control" id="descripcion_grupo"
                                        name="descripcion_grupo">
                                </div>
                                <div class="mb-3">
                                    <label for="anio_configuracion" class="form-label fw-bold">Año de
                                        configuración</label>
                                    <select class="form-select" id="anio_configuracion" name="anio_configuracion"
                                        required>
                                        <option value="" selected>Seleccione un año</option>
                                        <?php
                                        $anioActual = date('Y');
                                        for ($i = $anioActual - 1; $i <= $anioActual + 5; $i++) {
                                            echo "<option value='$i'>$i</option>";
                                        }
                                        ?>
                                    </select>
                                    <small class="text-muted">Las franjas solo podrán configurarse para este año</small>
                                </div>
                                <div class="mb-3">
                                    <label for="max_dias_vacaciones" class="form-label fw-bold">Días máximos de vacaciones</label>
                                    <input type="number" class="form-control" id="max_dias_vacaciones"
                                        name="max_dias_vacaciones" min="0" step="1" placeholder="Ej: 22" required>
                                    <small class="text-muted">Configure el máximo de días de vacaciones para este grupo</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Franjas horarias anuales</label>
                                    <div id="franjas-horarias-lista"></div>
                                    <button type="button" class="btn btn-primary btn-sm mt-2" id="btn-add-franja"
                                        disabled>Añadir franja</button>
                                    <small class="text-muted d-block mt-1" id="mensaje-seleccionar-anio">Primero
                                        seleccione un año de configuración</small>
                                </div>
                                <div class="form-check mb-3">
                                    <input type="hidden" name="grupo_predeterminado" value="0">
                                    <input class="form-check-input" type="checkbox" id="grupo_predeterminado"
                                        name="grupo_predeterminado" value="1">
                                    <label class="form-check-label" for="grupo_predeterminado">Grupo
                                        predeterminado</label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar grupo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Asignar Trabajadores -->
            <div class="modal fade" id="modalAsignarTrabajadores" tabindex="-1"
                aria-labelledby="modalAsignarTrabajadoresLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title" id="modalAsignarTrabajadoresLabel">
                                <i class="bi bi-people-fill me-2"></i>Asignar trabajadores al grupo
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="formAsignarTrabajadores">
                                <input type="hidden" id="grupo_id_asignar" name="grupo_id" value="">

                                <div class="mb-3">
                                    <label for="areas_trabajo" class="form-label fw-bold">
                                        <i class="bi bi-building me-1"></i>Áreas de trabajo
                                    </label>
                                    <select class="form-select" id="areas_trabajo" name="areas_trabajo[]"
                                        multiple="multiple" style="width: 100%">
                                        <option value="">Cargando...</option>
                                    </select>
                                    <small class="text-muted">Selecciona una o varias áreas de trabajo para filtrar
                                        trabajadores</small>
                                </div>

                                <div class="mb-3">
                                    <label for="tipos_contrato" class="form-label fw-bold">
                                        <i class="bi bi-file-text me-1"></i>Tipos de contrato
                                    </label>
                                    <select class="form-select" id="tipos_contrato" name="tipos_contrato[]"
                                        multiple="multiple" style="width: 100%">
                                        <option value="">Cargando...</option>
                                    </select>
                                    <small class="text-muted">Selecciona uno o varios tipos de contrato</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-person-plus me-1"></i>Búsqueda manual de trabajadores
                                    </label>
                                    <small class="text-muted d-block mb-2">Busca y añade trabajadores manualmente
                                        (mínimo 4 caracteres)</small>

                                    <!-- Buscador manual de trabajadores -->
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" id="buscadorManualTrabajadores" class="form-control"
                                            placeholder="Buscar por PERNR o nombre/apellidos...">
                                        <button class="btn btn-outline-secondary" type="button"
                                            id="btnLimpiarBuscadorManual">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>

                                    <!-- Resultados de búsqueda manual -->
                                    <div id="resultadosBusquedaManual" class="border rounded p-2 mb-3"
                                        style="display: none; max-height: 200px; overflow-y: auto;">
                                        <!-- Se llenará dinámicamente con los resultados -->
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-people me-1"></i>Trabajadores seleccionados
                                    </label>

                                    <!-- Buscador de trabajadores en la lista -->
                                    <div class="input-group mb-2">
                                        <span class="input-group-text">
                                            <i class="bi bi-funnel"></i>
                                        </span>
                                        <input type="text" id="buscadorTrabajadores" class="form-control"
                                            placeholder="Filtrar trabajadores en la lista..." disabled>
                                        <button class="btn btn-outline-secondary" type="button" id="btnLimpiarBuscador"
                                            disabled>
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>

                                    <div id="listaTrabajadoresSeleccionados" class="border rounded p-3"
                                        style="min-height: 100px; max-height: 300px; overflow-y: auto;">
                                        <div class="text-center text-muted py-3">
                                            <i class="bi bi-info-circle"></i>
                                            <p class="mb-0">Selecciona áreas de trabajo para ver trabajadores</p>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-success" id="btnGuardarAsignacion">
                                <i class="bi bi-check-circle me-1"></i>Guardar asignación
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Días No Configurados -->
            <div class="modal fade" id="modalDiasNoConfigurados" tabindex="-1"
                aria-labelledby="modalDiasNoConfiguradosLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="modalDiasNoConfiguradosLabel">
                                <i class="bi bi-calendar-x me-2"></i>Días sin configurar
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div id="contenidoDiasNoConfigurados">
                                <!-- Se llenará dinámicamente -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Clonar Grupo -->
            <div class="modal fade" id="modalClonarGrupo" tabindex="-1" aria-labelledby="modalClonarGrupoLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title" id="modalClonarGrupoLabel">
                                <i class="bi bi-files me-2"></i>Clonar grupo a otro año
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="grupo_id_clonar" value="">

                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Información:</strong> Esta acción creará una copia completa del grupo con todas
                                sus franjas horarias adaptadas al año seleccionado.
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Grupo a clonar:</label>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title mb-1" id="nombre_grupo_clonar"></h6>
                                        <p class="card-text text-muted mb-1" id="descripcion_grupo_clonar"></p>
                                        <span class="badge bg-primary" id="anio_actual_grupo_clonar"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="anio_destino_clonar" class="form-label fw-bold">
                                    <i class="bi bi-calendar-check me-1"></i>Año destino
                                </label>
                                <select class="form-select" id="anio_destino_clonar" required>
                                    <option value="" selected>Seleccione el año destino</option>
                                    <?php
                                    $anioActual = date('Y');
                                    for ($i = $anioActual - 1; $i <= $anioActual + 5; $i++) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                    ?>
                                </select>
                                <small class="text-muted">Todas las franjas horarias se adaptarán automáticamente a este
                                    año</small>
                            </div>

                            <div class="mb-3">
                                <label for="nuevo_nombre_grupo_clonar" class="form-label fw-bold">
                                    <i class="bi bi-pencil me-1"></i>Nombre del nuevo grupo
                                </label>
                                <input type="text" class="form-control" id="nuevo_nombre_grupo_clonar"
                                    placeholder="Ej: Horario Verano 2026" required>
                                <small class="text-muted">Puedes modificar el nombre o dejar el mismo</small>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="clonar_trabajadores">
                                <label class="form-check-label" for="clonar_trabajadores">
                                    <i class="bi bi-people me-1"></i>Clonar también los trabajadores asignados
                                </label>
                                <small class="text-muted d-block ms-4">Si está marcado, los trabajadores del grupo
                                    original también se asignarán al nuevo grupo</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-warning" id="btnConfirmarClonar">
                                <i class="bi bi-files me-1"></i>Clonar grupo
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Calendario del Grupo -->
            <div class="modal fade" id="modalCalendarioGrupo" tabindex="-1" aria-labelledby="modalCalendarioGrupoLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title" id="modalCalendarioGrupoLabel">
                                <i class="bi bi-calendar4-week me-2"></i>Calendario
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <div id="calendarios" class="d-flex flex-wrap justify-content-center gap-3">
                                <div id="loading" class="d-flex justify-content-center" style="width: 100%;">
                                    <div class="spinner-border" style="width: 50px; height: 50px;" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                            </div>

                            <div class="leyenda mt-4">
                                <h5>Leyenda</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="leyenda-item mb-2">
                                            <span class="color-box me-2"
                                                style="background-color: #ff0000; display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc;"></span>
                                            Festivo Nacional
                                        </div>
                                        <div class="leyenda-item mb-2">
                                            <span class="color-box me-2"
                                                style="background-color: #00b050; display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc;"></span>
                                            Festivo Autonómico
                                        </div>
                                        <div class="leyenda-item mb-2">
                                            <span class="color-box me-2"
                                                style="background-color: #e2efda; display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc;"></span>
                                            Jornada Reducida
                                        </div>
                                        <div class="leyenda-item mb-2">
                                            <span class="color-box me-2"
                                                style="background-color: #feccd4; display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc;"></span>
                                            Jornada Especial
                                        </div>
                                        <div class="leyenda-item mb-2">
                                            <span class="color-box me-2"
                                                style="background-color: #ffffff; display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc;"></span>
                                            Jornada Normal
                                        </div>
                                        <div class="leyenda-item mb-2">
                                            <span class="color-box me-2"
                                                style="background-color: #f0f0f0; display: inline-block; width: 20px; height: 20px; border: 1px solid #ccc;"></span>
                                            Día sin configurar
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

<script>
    console.log('Script horario.php cargado correctamente');

    // Mostrar mensaje de resultado si existe (después de eliminación, por ejemplo)
    // Esperar a que alertify esté disponible
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (isset($params['resultado']) && !empty($params['resultado'])): ?>
            <?php
            $tipoMensaje = isset($params['tipo_resultado']) && $params['tipo_resultado'] === 'success' ? 'success' : 'error';
            ?>
            if (typeof alertify !== 'undefined') {
                alertify.<?= $tipoMensaje ?>('<?= addslashes($params['resultado']) ?>');
            }
        <?php endif; ?>
    });

    // Función para mostrar días no configurados (llamada desde el onclick)
    function mostrarDiasNoConfiguradosClick(element) {
        const grupoId = element.getAttribute('data-grupo-id');
        const nombreGrupo = element.getAttribute('data-grupo-nombre');
        const fechasNoConfiguradas = JSON.parse(element.getAttribute('data-fechas-no-config') || '{}');

        const nombresDias = {
            0: 'Domingo',
            1: 'Lunes',
            2: 'Martes',
            3: 'Miércoles',
            4: 'Jueves',
            5: 'Viernes',
            6: 'Sábado'
        };

        mostrarDiasNoConfigurados(grupoId, nombreGrupo, fechasNoConfiguradas, nombresDias);
    }

    // Función para mostrar días no configurados
    function mostrarDiasNoConfigurados(grupoId, nombreGrupo, fechasNoConfiguradas, nombresDias) {
        const contenido = document.getElementById('contenidoDiasNoConfigurados');
        const modalLabel = document.getElementById('modalDiasNoConfiguradosLabel');

        modalLabel.innerHTML = `<i class="bi bi-calendar-x me-2"></i>Días sin configurar - ${nombreGrupo}`;

        if (Object.keys(fechasNoConfiguradas).length === 0) {
            contenido.innerHTML = `
                <div class="alert alert-success text-center">
                    <i class="bi bi-check-circle fs-1"></i>
                    <h5 class="mt-2">¡Todos los días están configurados!</h5>
                    <p class="mb-0">Este grupo tiene horarios para todos los días del año.</p>
                </div>
            `;
        } else {
            let html = '<div class="accordion" id="accordionDiasNoConfigurados">';

            // Orden: Lunes a Domingo
            const ordenDias = [1, 2, 3, 4, 5, 6, 0];

            ordenDias.forEach((diaSemana, index) => {
                if (fechasNoConfiguradas[diaSemana] && fechasNoConfiguradas[diaSemana].length > 0) {
                    const fechas = fechasNoConfiguradas[diaSemana];
                    const nombreDia = nombresDias[diaSemana];
                    const cantidad = fechas.length;

                    html += `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading${diaSemana}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${diaSemana}">
                                    <strong>${nombreDia}</strong>&nbsp;-&nbsp;<span class="badge bg-warning text-dark">${cantidad} ${cantidad === 1 ? 'día' : 'días'}</span>
                                </button>
                            </h2>
                            <div id="collapse${diaSemana}" class="accordion-collapse collapse" data-bs-parent="#accordionDiasNoConfigurados">
                                <div class="accordion-body">
                                    <div class="row g-2">
                    `;

                    // Mostrar las fechas en formato de tarjetas pequeñas
                    fechas.forEach(fecha => {
                        const fechaObj = new Date(fecha + 'T00:00:00');
                        const dia = fechaObj.getDate();
                        const mes = fechaObj.toLocaleDateString('es-ES', { month: 'short' });

                        html += `
                            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                                <div class="card text-center">
                                    <div class="card-body p-2">
                                        <h6 class="mb-0">${dia}</h6>
                                        <small class="text-muted">${mes}</small>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    html += `
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            html += '</div>';
            contenido.innerHTML = html;
        }

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalDiasNoConfigurados'));
        modal.show();
    }

    // Variable global para el grupo actual del calendario
    let grupoCalendarioActual = null;

    // Función para abrir el calendario del grupo
    function abrirCalendarioGrupo(grupoData) {
        // Guardar datos del grupo actual
        grupoCalendarioActual = grupoData;

        const modalLabel = document.getElementById('modalCalendarioGrupoLabel');
        modalLabel.innerHTML = `<i class="bi bi-calendar4-week me-2"></i>Calendario - ${grupoData.nombre_grupo}`;

        // Parsear las franjas
        let franjas = [];
        try {
            franjas = JSON.parse(grupoData.franjas_json);
        } catch (e) {
            franjas = [];
        }

        // Determinar los años que cubren las franjas
        let años = new Set();
        franjas.forEach(franja => {
            if (franja.inicio_fecha && franja.fin_fecha) {
                const añoInicio = parseInt(franja.inicio_fecha.split('-')[0]);
                const añoFin = parseInt(franja.fin_fecha.split('-')[0]);
                for (let año = añoInicio; año <= añoFin; año++) {
                    años.add(año);
                }
            }
        });

        // Si no hay años, usar el año actual
        if (años.size === 0) {
            años.add(new Date().getFullYear());
        }

        // Convertir a array y ordenar
        const añosArray = Array.from(años).sort();

        // Generar calendarios para todos los años
        generarCalendarios(añosArray, franjas);

        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('modalCalendarioGrupo'));
        modal.show();
    }

    // Función para generar calendarios
    function generarCalendarios(años, franjas) {
        const container = document.getElementById('calendarios');

        // Limpiar contenedor y mostrar spinner
        container.innerHTML = '<div id="loading" class="d-flex justify-content-center" style="width: 100%;"><div class="spinner-border" style="width: 50px; height: 50px;" role="status"><span class="visually-hidden">Cargando...</span></div></div>';

        // Usamos setTimeout para que el spinner se renderice antes de generar los calendarios
        setTimeout(() => {
            container.innerHTML = ''; // Limpiar el spinner

            const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            const diasSemana = ["L", "M", "M", "J", "V", "S", "D"];

            // Función para obtener el tipo de día
            function getTipoDia(fechaStr) {
                const fecha = new Date(fechaStr + "T00:00:00");
                const diaSemana = fecha.getDay(); // 0=domingo, 1=lunes, ..., 6=sábado
                let tipo = "";

                for (let franja of franjas) {
                    if (franja.inicio_fecha && franja.fin_fecha) {
                        const inicio = new Date(franja.inicio_fecha + "T00:00:00");
                        const fin = new Date(franja.fin_fecha + "T00:00:00");

                        if (fecha >= inicio && fecha <= fin) {
                            // Para festivos, retornar inmediatamente (prioridad más alta)
                            // Los festivos no requieren verificación de días de la semana
                            if (franja.tipo_jornada === 'festivo_nacional') return 'festivo_nacional';
                            if (franja.tipo_jornada === 'festivo_autonomico') return 'festivo_autonomico';

                            // Para otros tipos de jornada, verificar si el día de la semana está incluido
                            if (franja.dias_semana && Array.isArray(franja.dias_semana)) {
                                if (franja.dias_semana.includes(diaSemana)) {
                                    // El día de la semana está configurado en esta franja
                                    if (franja.tipo_jornada === 'especial') tipo = 'especial';
                                    else if (franja.tipo_jornada === 'reducida' && tipo === '') tipo = 'reducida';
                                    else if (franja.tipo_jornada === 'normal' && tipo === '') tipo = 'normal';
                                }
                            }
                        }
                    }
                }

                // Si no se encontró ningún tipo, es un día sin configurar
                return tipo || 'sin_configurar';
            }

            // Función para verificar si un año es bisiesto
            function esBisiesto(year) {
                return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
            }

            // Generar calendarios para cada año
            años.forEach(year => {
                // Agregar título del año
                const añoTitulo = document.createElement('div');
                añoTitulo.className = 'w-100 text-center mb-0';
                añoTitulo.innerHTML = `<h4 class="fw-bold">Año ${year}</h4>`;
                container.appendChild(añoTitulo);

                // Generar 12 meses
                for (let m = 0; m < 12; m++) {
                    let daysInMonth = (m === 1) ? (esBisiesto(year) ? 29 : 28) : new Date(year, m + 1, 0).getDate();
                    const firstDay = new Date(year, m, 1).getDay();

                    const calendarDiv = document.createElement('div');
                    calendarDiv.className = 'calendar';

                    let table = `<h3>${meses[m]}</h3><table><tr>`;
                    diasSemana.forEach(d => table += `<th>${d}</th>`);
                    table += `</tr><tr>`;

                    let dayOfWeek = (firstDay === 0 ? 6 : firstDay - 1);
                    for (let i = 0; i < dayOfWeek; i++) table += `<td></td>`;

                    for (let day = 1; day <= daysInMonth; day++) {
                        const fechaStr = `${year}-${String(m + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        let tipo = getTipoDia(fechaStr);

                        // Hacer la celda clicable - título depende del tipo
                        let titulo = 'Click para agregar festivo';
                        if (tipo === 'festivo_nacional' || tipo === 'festivo_autonomico') {
                            titulo = 'Click para eliminar festivo';
                        }

                        table += `<td class="${tipo} calendario-dia-clicable" data-fecha="${fechaStr}" data-tipo="${tipo}" style="cursor: pointer;" title="${titulo}">${day}</td>`;

                        dayOfWeek++;
                        if (dayOfWeek % 7 === 0 && day !== daysInMonth) table += `</tr><tr>`;
                    }

                    table += `</tr></table>`;
                    calendarDiv.innerHTML = table;
                    container.appendChild(calendarDiv);
                }

                // Agregar event listeners a todas las celdas clicables
                setTimeout(() => {
                    const celdasClicables = document.querySelectorAll('.calendario-dia-clicable');
                    celdasClicables.forEach(celda => {
                        celda.addEventListener('click', function () {
                            const fecha = this.getAttribute('data-fecha');
                            const tipo = this.getAttribute('data-tipo');

                            // Si es festivo, mostrar modal para eliminar
                            if (tipo === 'festivo_nacional' || tipo === 'festivo_autonomico') {
                                mostrarModalEliminarFestivo(fecha, tipo);
                            } else {
                                // Si no es festivo, mostrar modal para agregar
                                mostrarModalAgregarFestivo(fecha);
                            }
                        });
                    });
                }, 50);
            });
        }, 100); // 100ms para que el spinner se muestre antes de renderizar
    }
    console.log('Función abrirCalendarioGrupo definida');

    // Función para mostrar modal de agregar festivo
    function mostrarModalAgregarFestivo(fecha) {
        const fechaObj = new Date(fecha + 'T00:00:00');
        const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        alertify.confirm(
            'Agregar festivo',
            `<div class="mb-3">
                <p><strong>Fecha seleccionada:</strong><br>${fechaFormateada}</p>
                <div class="form-group">
                    <label for="tipo_festivo_select" class="form-label fw-bold">Tipo de festivo:</label>
                    <select id="tipo_festivo_select" class="form-select">
                        <option value="festivo_nacional">Festivo Nacional</option>
                        <option value="festivo_autonomico">Festivo Autonómico</option>
                    </select>
                </div>
            </div>`,
            function () {
                // Usuario confirmó
                const tipoFestivo = document.getElementById('tipo_festivo_select').value;
                guardarFestivo(fecha, tipoFestivo);
            },
            function () {
                // Usuario canceló
            }
        ).set('labels', { ok: 'Guardar festivo', cancel: 'Cancelar' })
            .set('closable', true)
            .set('reverseButtons', true);
    }

    // Función para mostrar modal de eliminar festivo
    function mostrarModalEliminarFestivo(fecha, tipoFestivo) {
        const fechaObj = new Date(fecha + 'T00:00:00');
        const fechaFormateada = fechaObj.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const nombreTipoFestivo = tipoFestivo === 'festivo_nacional' ? 'Festivo Nacional' : 'Festivo Autonómico';

        alertify.confirm(
            'Eliminar festivo',
            `<div class="mb-3">
                <p><strong>Fecha:</strong><br>${fechaFormateada}</p>
                <p><strong>Tipo:</strong><br><span class="badge ${tipoFestivo === 'festivo_nacional' ? 'bg-danger' : 'bg-success'}">${nombreTipoFestivo}</span></p>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    ¿Estás seguro de que deseas eliminar este festivo del grupo?
                </div>
            </div>`,
            function () {
                // Usuario confirmó
                eliminarFestivo(fecha);
            },
            function () {
                // Usuario canceló
            }
        ).set('labels', { ok: 'Sí, eliminar', cancel: 'Cancelar' })
            .set('closable', true)
            .set('reverseButtons', true);
    }

    // Función para guardar festivo en la base de datos
    function guardarFestivo(fecha, tipoFestivo) {
        if (!grupoCalendarioActual) {
            alertify.error('Error: No se ha seleccionado ningún grupo');
            return;
        }

        // Mostrar loading
        alertify.notify('Guardando festivo...', 'info', 2);

        const payload = {
            action: 'agregar_festivo_grupo',
            grupo_id: grupoCalendarioActual.id,
            fecha: fecha,
            tipo_festivo: tipoFestivo
        };

        // Enviar petición al servidor
        fetch('auto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertify.success('Festivo agregado correctamente');
                    // Recargar el calendario
                    location.reload();
                } else {
                    alertify.error('Error al guardar el festivo: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alertify.error('Error al comunicarse con el servidor');
            });
    }
    console.log('Función guardarFestivo definida');

    // Función para eliminar festivo de la base de datos
    function eliminarFestivo(fecha) {
        if (!grupoCalendarioActual) {
            alertify.error('Error: No se ha seleccionado ningún grupo');
            return;
        }

        // Mostrar loading
        alertify.notify('Eliminando festivo...', 'info', 2);

        // Enviar petición al servidor
        fetch('auto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'eliminar_festivo_grupo',
                grupo_id: grupoCalendarioActual.id,
                fecha: fecha
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alertify.success('Festivo eliminado correctamente');
                    // Recargar el calendario
                    location.reload();
                } else {
                    alertify.error('Error al eliminar el festivo: ' + (data.message || 'Error desconocido'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alertify.error('Error al comunicarse con el servidor');
            });
    }
    console.log('Función eliminarFestivo definida');

    // Función para abrir modal de clonar grupo
    function abrirModalClonarGrupo(grupoData) {
        console.log('abrirModalClonarGrupo llamada con:', grupoData);

        // Llenar los datos del grupo a clonar
        document.getElementById('grupo_id_clonar').value = grupoData.id;
        document.getElementById('nombre_grupo_clonar').textContent = grupoData.nombre_grupo;
        document.getElementById('descripcion_grupo_clonar').textContent = grupoData.descripcion_grupo || 'Sin descripción';
        document.getElementById('anio_actual_grupo_clonar').textContent = 'Año ' + grupoData.anio_configuracion;

        // Establecer el nombre sugerido para el nuevo grupo
        const anioActual = grupoData.anio_configuracion;
        const nombreSugerido = grupoData.nombre_grupo.replace(anioActual, '{AÑO}');
        document.getElementById('nuevo_nombre_grupo_clonar').value = nombreSugerido;

        // Resetear selección de año y checkbox
        document.getElementById('anio_destino_clonar').value = '';
        document.getElementById('clonar_trabajadores').checked = false;

        // Actualizar el nombre sugerido cuando cambie el año destino
        document.getElementById('anio_destino_clonar').addEventListener('change', function () {
            const anioDestino = this.value;
            if (anioDestino) {
                const nombreConAnio = nombreSugerido.replace('{AÑO}', anioDestino);
                document.getElementById('nuevo_nombre_grupo_clonar').value = nombreConAnio;
            }
        });

        // Mostrar el modal
        const modal = new bootstrap.Modal(document.getElementById('modalClonarGrupo'));
        modal.show();
    }
    console.log('Función abrirModalClonarGrupo definida');

    // Variables globales para el modal de asignación de trabajadores
    let anioGrupoActual = null;
    let cargandoModal = false; // Variable para controlar la carga inicial del modal

    // Función global para abrir modal de asignación de trabajadores
    function accionMultipleGrupo(grupoId, nombreGrupo, anioGrupo) {
        console.log('accionMultipleGrupo llamada con:', grupoId, nombreGrupo, anioGrupo);

        // Marcar que estamos cargando el modal
        cargandoModal = true;

        // Guardar el año del grupo globalmente
        anioGrupoActual = anioGrupo;

        document.getElementById('modalAsignarTrabajadoresLabel').innerHTML = '<i class="bi bi-people-fill me-2"></i>Asignar trabajadores al grupo: ' + nombreGrupo;
        document.getElementById('grupo_id_asignar').value = grupoId;

        // Limpiar buscador
        $('#buscadorTrabajadores').val('').prop('disabled', true);
        $('#btnLimpiarBuscador').prop('disabled', true);

        // Limpiar lista de trabajadores
        $('#listaTrabajadoresSeleccionados').html('<p class="text-muted text-center py-3">Seleccione al menos un área de trabajo o tipo de contrato</p>');

        // Destruir Select2 existentes para reiniciarlos desde cero
        if ($('#areas_trabajo').hasClass("select2-hidden-accessible")) {
            $('#areas_trabajo').select2('destroy');
        }
        if ($('#tipos_contrato').hasClass("select2-hidden-accessible")) {
            $('#tipos_contrato').select2('destroy');
        }

        // Limpiar selecciones y eventos previos
        $('#areas_trabajo').val(null).off('change');
        $('#tipos_contrato').val(null).off('change');

        // Inicializar select2 de áreas de trabajo
        $('#areas_trabajo').select2({
            placeholder: 'Selecciona áreas de trabajo',
            allowClear: true,
            dropdownParent: $('#modalAsignarTrabajadores'),
            language: {
                noResults: function () {
                    return "No se encontraron resultados";
                },
                searching: function () {
                    return "Buscando...";
                }
            }
        });

        // Evento cuando cambian las áreas seleccionadas
        $('#areas_trabajo').on('change', function () {
            // Ignorar eventos durante la carga inicial del modal
            if (cargandoModal) return;
            
            const areasSeleccionadas = $(this).val();
            const contratosSeleccionados = $('#tipos_contrato').val();

            // Cargar trabajadores si hay áreas o contratos seleccionados
            if ((areasSeleccionadas && areasSeleccionadas.length > 0) || (contratosSeleccionados && contratosSeleccionados.length > 0)) {
                cargarTrabajadoresPorFiltros(areasSeleccionadas, contratosSeleccionados, grupoId);
            } else {
                // Guardar trabajadores especiales antes de limpiar (manuales y asignados previamente)
                const listaTrabajadores = $('#listaTrabajadoresSeleccionados');
                const trabajadoresEspeciales = [];
                listaTrabajadores.find('.list-group-item').each(function () {
                    const badge = $(this).find('.badge.bg-success, .badge.bg-info');
                    if (badge.length > 0 && (badge.text().includes('Añadido manualmente') || badge.text().includes('Asignado previamente'))) {
                        trabajadoresEspeciales.push($(this).prop('outerHTML'));
                    }
                });

                // Si hay trabajadores especiales, mantenerlos
                if (trabajadoresEspeciales.length > 0) {
                    listaTrabajadores.html('<div class="list-group">' + trabajadoresEspeciales.join('') + '</div>');
                    $('#buscadorTrabajadores').prop('disabled', false);
                    $('#btnLimpiarBuscador').prop('disabled', false);
                } else {
                    listaTrabajadores.html('<p class="text-muted text-center py-3">Seleccione al menos un área de trabajo o tipo de contrato</p>');
                    $('#buscadorTrabajadores').val('').prop('disabled', true);
                    $('#btnLimpiarBuscador').prop('disabled', true);
                }
            }
        });

        // Inicializar select2 de tipos de contrato
        $('#tipos_contrato').select2({
            placeholder: 'Selecciona tipos de contrato',
            allowClear: true,
            dropdownParent: $('#modalAsignarTrabajadores'),
            language: {
                noResults: function () {
                    return "No se encontraron resultados";
                },
                searching: function () {
                    return "Buscando...";
                }
            }
        });

        // Evento cuando cambian los tipos de contrato seleccionados
        $('#tipos_contrato').on('change', function () {
            // Ignorar eventos durante la carga inicial del modal
            if (cargandoModal) return;
            
            const areasSeleccionadas = $('#areas_trabajo').val();
            const contratosSeleccionados = $(this).val();

            // Cargar trabajadores si hay áreas o contratos seleccionados
            if ((areasSeleccionadas && areasSeleccionadas.length > 0) || (contratosSeleccionados && contratosSeleccionados.length > 0)) {
                cargarTrabajadoresPorFiltros(areasSeleccionadas, contratosSeleccionados, grupoId);
            } else {
                // Guardar trabajadores especiales antes de limpiar (manuales y asignados previamente)
                const listaTrabajadores = $('#listaTrabajadoresSeleccionados');
                const trabajadoresEspeciales = [];
                listaTrabajadores.find('.list-group-item').each(function () {
                    const badge = $(this).find('.badge.bg-success, .badge.bg-info');
                    if (badge.length > 0 && (badge.text().includes('Añadido manualmente') || badge.text().includes('Asignado previamente'))) {
                        trabajadoresEspeciales.push($(this).prop('outerHTML'));
                    }
                });

                // Si hay trabajadores especiales, mantenerlos
                if (trabajadoresEspeciales.length > 0) {
                    listaTrabajadores.html('<div class="list-group">' + trabajadoresEspeciales.join('') + '</div>');
                    $('#buscadorTrabajadores').prop('disabled', false);
                    $('#btnLimpiarBuscador').prop('disabled', false);
                } else {
                    listaTrabajadores.html('<p class="text-muted text-center py-3">Seleccione al menos un área de trabajo o tipo de contrato</p>');
                    $('#buscadorTrabajadores').val('').prop('disabled', true);
                    $('#btnLimpiarBuscador').prop('disabled', true);
                }
            }
        });

        // Cargar áreas de trabajo
        cargarAreasTrabajoSelect2();

        // Cargar tipos de contrato
        cargarTiposContratoSelect2();

        // Cargar trabajadores ya asignados al grupo
        cargarTrabajadoresAsignados(grupoId);

        var modal = new bootstrap.Modal(document.getElementById('modalAsignarTrabajadores'));
        modal.show();
    }
    console.log('Función accionMultipleGrupo definida');

    // Función para cargar trabajadores ya asignados al grupo
    function cargarTrabajadoresAsignados(grupoId) {
        const listaTrabajadores = $('#listaTrabajadoresSeleccionados');

        // Mostrar loading
        listaTrabajadores.html(`
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mb-0 mt-2">Cargando trabajadores asignados...</p>
            </div>
        `);

        // Consultar trabajadores asignados
        fetch('auto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'obtener_trabajadores_asignados',
                grupo_id: grupoId
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.trabajadores && data.trabajadores.length > 0) {
                    // Ordenar por PERNR
                    data.trabajadores.sort((a, b) => a.pernr.localeCompare(b.pernr));

                    let html = '<div class="list-group">';
                    data.trabajadores.forEach(trabajador => {
                        const fechaInicio = trabajador.fecha_inicio || '';
                        const fechaFin = trabajador.fecha_fin || '';

                        html += `
                        <div class="list-group-item">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div>
                                    <input class="form-check-input me-2" type="checkbox" 
                                           value="${trabajador.pernr}" 
                                           data-nombre="${trabajador.nombre}"
                                           checked>
                                    <strong>${trabajador.pernr}</strong> - ${trabajador.nombre}
                                    <span class="badge bg-info ms-2">Asignado previamente</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary toggle-fechas-btn" 
                                        data-pernr="${trabajador.pernr}">
                                    <i class="bi bi-calendar-range"></i> Fechas
                                </button>
                            </div>
                            <div class="fechas-trabajador" id="fechas-${trabajador.pernr}" style="display: none;">
                                <div class="row g-2 mt-1">
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Fecha Inicio</label>
                                        <input type="date" class="form-control form-control-sm fecha-inicio" 
                                               data-pernr="${trabajador.pernr}"
                                               value="${fechaInicio}"
                                               min="${anioGrupoActual}-01-01"
                                               max="${anioGrupoActual}-12-31">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small mb-1">Fecha Fin</label>
                                        <input type="date" class="form-control form-control-sm fecha-fin" 
                                               data-pernr="${trabajador.pernr}"
                                               value="${fechaFin}"
                                               min="${anioGrupoActual}-01-01"
                                               max="${anioGrupoActual}-12-31">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    });
                    html += '</div>';

                    listaTrabajadores.html(html);

                    // Habilitar el buscador
                    $('#buscadorTrabajadores').prop('disabled', false);
                    $('#btnLimpiarBuscador').prop('disabled', false);

                    alertify.success(`${data.trabajadores.length} trabajador(es) asignado(s) al grupo`);
                } else {
                    // No hay trabajadores asignados
                    listaTrabajadores.html('<p class="text-muted text-center py-3">No hay trabajadores asignados. Seleccione áreas de trabajo o busque manualmente.</p>');
                }
                
                // Desactivar bandera de carga después de cargar trabajadores
                setTimeout(() => {
                    cargandoModal = false;
                }, 500);
            })
            .catch(error => {
                console.error('Error:', error);
                listaTrabajadores.html('<p class="text-danger text-center py-3">Error al cargar trabajadores asignados</p>');
                
                // Desactivar bandera de carga incluso si hay error
                setTimeout(() => {
                    cargandoModal = false;
                }, 500);
            });
    }

    // Función para cargar áreas de trabajo en el select2
    function cargarAreasTrabajoSelect2() {
        fetch('auto.php?obtener_areas_trabajo')
            .then(response => response.json())
            .then(data => {
                if (data.areas && data.areas.length > 0) {
                    $('#areas_trabajo').empty();
                    data.areas.forEach(area => {
                        const option = new Option(area.text, area.id, false, false);
                        $('#areas_trabajo').append(option);
                    });
                    $('#areas_trabajo').trigger('change');
                } else {
                    $('#areas_trabajo').html('<option value="">Sin áreas disponibles</option>');
                }
            })
            .catch(error => {
                console.error('Error al cargar áreas:', error);
                alertify.error('Error al cargar áreas de trabajo');
            });
    }

    // Función para cargar tipos de contrato en el select2
    function cargarTiposContratoSelect2() {
        fetch('auto.php?obtener_tipos_contrato')
            .then(response => response.json())
            .then(data => {
                if (data.contratos && data.contratos.length > 0) {
                    $('#tipos_contrato').empty();
                    data.contratos.forEach(contrato => {
                        const option = new Option(contrato.text, contrato.id, false, false);
                        $('#tipos_contrato').append(option);
                    });
                    $('#tipos_contrato').trigger('change');
                } else {
                    $('#tipos_contrato').html('<option value="">Sin tipos de contrato disponibles</option>');
                }
            })
            .catch(error => {
                console.error('Error al cargar tipos de contrato:', error);
                alertify.error('Error al cargar tipos de contrato');
            });
    }

    // Función para cargar trabajadores por áreas y tipos de contrato seleccionados
    function cargarTrabajadoresPorFiltros(areas, contratos, grupoId) {
        const listaTrabajadores = $('#listaTrabajadoresSeleccionados');

        // Validar que al menos haya un filtro activo
        const tieneAreas = areas && areas.length > 0;
        const tieneContratos = contratos && contratos.length > 0;

        if (!tieneAreas && !tieneContratos) {
            // Guardar trabajadores especiales antes de limpiar (manuales y asignados previamente)
            const trabajadoresEspeciales = [];
            listaTrabajadores.find('.list-group-item').each(function () {
                const badge = $(this).find('.badge.bg-success, .badge.bg-info');
                if (badge.length > 0 && (badge.text().includes('Añadido manualmente') || badge.text().includes('Asignado previamente'))) {
                    trabajadoresEspeciales.push($(this).prop('outerHTML'));
                }
            });

            // Si hay trabajadores especiales, mantenerlos
            if (trabajadoresEspeciales.length > 0) {
                listaTrabajadores.html('<div class="list-group">' + trabajadoresEspeciales.join('') + '</div>');
                $('#buscadorTrabajadores').prop('disabled', false);
                $('#btnLimpiarBuscador').prop('disabled', false);
            } else {
                listaTrabajadores.html('<p class="text-muted text-center py-3">Seleccione al menos un área de trabajo o tipo de contrato</p>');
                $('#buscadorTrabajadores').val('').prop('disabled', true);
                $('#btnLimpiarBuscador').prop('disabled', true);
            }
            return;
        }

        // Guardar trabajadores especiales antes de cargar nuevos (manuales y asignados previamente)
        const trabajadoresEspeciales = [];
        listaTrabajadores.find('.list-group-item').each(function () {
            const badge = $(this).find('.badge.bg-success, .badge.bg-info');
            if (badge.length > 0 && (badge.text().includes('Añadido manualmente') || badge.text().includes('Asignado previamente'))) {
                const checkbox = $(this).find('input[type="checkbox"]');
                trabajadoresEspeciales.push({
                    pernr: checkbox.val(),
                    nombre: checkbox.data('nombre'),
                    area: checkbox.data('area'),
                    html: $(this).prop('outerHTML')
                });
            }
        });

        // Mostrar loading
        listaTrabajadores.html(`
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-success" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mb-0 mt-2">Cargando trabajadores...</p>
            </div>
        `);

        // Limpiar buscador mientras carga
        $('#buscadorTrabajadores').val('').prop('disabled', true);
        $('#btnLimpiarBuscador').prop('disabled', true);

        // Consultar con filtros de áreas y contratos
        fetch('auto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'obtener_trabajadores_por_areas',
                areas: tieneAreas ? areas : null,
                contratos: tieneContratos ? contratos : null
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.trabajadores && data.trabajadores.length > 0) {
                    // Ordenar por PERNR
                    data.trabajadores.sort((a, b) => a.pernr.localeCompare(b.pernr));

                    // Crear set de PERNRs especiales para evitar duplicados
                    const pernrsEspeciales = new Set(trabajadoresEspeciales.map(t => t.pernr));

                    let html = '<div class="list-group">';

                    // Primero añadir los trabajadores especiales (manuales y asignados previamente)
                    trabajadoresEspeciales.forEach(trabajador => {
                        html += trabajador.html;
                    });

                    // Luego añadir los trabajadores del filtro (excluyendo duplicados)
                    data.trabajadores.forEach(trabajador => {
                        // Solo añadir si no es un trabajador especial
                        if (!pernrsEspeciales.has(trabajador.pernr)) {
                            html += `
                            <div class="list-group-item">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <input class="form-check-input me-2" type="checkbox" 
                                               value="${trabajador.pernr}" 
                                               data-nombre="${trabajador.nombre}"
                                               data-area="${trabajador.area}"
                                               checked>
                                        <strong>${trabajador.pernr}</strong> - ${trabajador.nombre}
                                        <small class="text-muted">(${trabajador.area})</small>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-fechas-btn" 
                                            data-pernr="${trabajador.pernr}">
                                        <i class="bi bi-calendar-range"></i> Fechas
                                    </button>
                                </div>
                                <div class="fechas-trabajador" id="fechas-${trabajador.pernr}" style="display: none;">
                                    <div class="row g-2 mt-1">
                                        <div class="col-md-6">
                                            <label class="form-label small mb-1">Fecha Inicio</label>
                                            <input type="date" class="form-control form-control-sm fecha-inicio" 
                                                   data-pernr="${trabajador.pernr}"
                                                   min="${anioGrupoActual}-01-01"
                                                   max="${anioGrupoActual}-12-31">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small mb-1">Fecha Fin</label>
                                            <input type="date" class="form-control form-control-sm fecha-fin" 
                                                   data-pernr="${trabajador.pernr}"
                                                   min="${anioGrupoActual}-01-01"
                                                   max="${anioGrupoActual}-12-31">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        }
                    });
                    html += '</div>';
                    listaTrabajadores.html(html);

                    // Habilitar buscador
                    $('#buscadorTrabajadores').prop('disabled', false);
                    $('#btnLimpiarBuscador').prop('disabled', false);
                } else {
                    // Si no hay trabajadores del filtro pero hay especiales, mantener los especiales
                    if (trabajadoresEspeciales.length > 0) {
                        let html = '<div class="list-group">';
                        trabajadoresEspeciales.forEach(trabajador => {
                            html += trabajador.html;
                        });
                        html += '</div>';
                        listaTrabajadores.html(html);
                        $('#buscadorTrabajadores').prop('disabled', false);
                        $('#btnLimpiarBuscador').prop('disabled', false);
                    } else {
                        listaTrabajadores.html('<p class="text-muted text-center py-3">No se encontraron trabajadores con los filtros seleccionados</p>');
                        $('#buscadorTrabajadores').val('').prop('disabled', true);
                        $('#btnLimpiarBuscador').prop('disabled', true);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                listaTrabajadores.html('<p class="text-danger text-center py-3">Error al cargar trabajadores</p>');
                $('#buscadorTrabajadores').val('').prop('disabled', true);
                $('#btnLimpiarBuscador').prop('disabled', true);
            });
    }

    // Función para filtrar trabajadores en tiempo real
    function filtrarTrabajadores(terminoBusqueda) {
        const termino = terminoBusqueda.toLowerCase().trim();
        const items = $('#listaTrabajadoresSeleccionados .list-group-item');

        if (termino === '') {
            // Mostrar todos si el buscador está vacío
            items.show();
        } else {
            items.each(function () {
                const texto = $(this).text().toLowerCase();
                if (texto.includes(termino)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    }

    // Variable para controlar el timeout de búsqueda
    let timeoutBusquedaManual = null;

    // Función para buscar trabajadores manualmente
    function buscarTrabajadoresManual(termino) {
        const resultadosDiv = $('#resultadosBusquedaManual');

        // Si el término tiene menos de 4 caracteres, ocultar resultados
        if (termino.length < 4) {
            resultadosDiv.hide().html('');
            return;
        }

        // Mostrar loading
        resultadosDiv.show().html(`
            <div class="text-center py-2">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Buscando...</span>
                </div>
                <small class="d-block mt-1">Buscando trabajadores...</small>
            </div>
        `);

        // Realizar búsqueda
        fetch('auto.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'buscar_trabajadores_manual',
                termino: termino
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resultadosDiv.html(`
                    <div class="text-danger text-center py-2">
                        <i class="bi bi-exclamation-circle"></i>
                        <small>${data.error}</small>
                    </div>
                `);
                    return;
                }

                if (data.mensaje) {
                    resultadosDiv.html(`
                    <div class="text-muted text-center py-2">
                        <small>${data.mensaje}</small>
                    </div>
                `);
                    return;
                }

                if (data.trabajadores && data.trabajadores.length > 0) {
                    let html = '<div class="list-group list-group-flush">';
                    data.trabajadores.forEach(trabajador => {
                        const contratoInfo = trabajador.desc_tipo_contrato ?
                            `<br><small class="text-info">Contrato: ${trabajador.desc_tipo_contrato}</small>` : '';

                        html += `
                        <a href="#" class="list-group-item list-group-item-action py-2 agregar-trabajador-manual"
                           data-pernr="${trabajador.pernr}"
                           data-nombre="${trabajador.nombre}"
                           data-area="${trabajador.area || 'Sin área'}">
                            <strong>${trabajador.pernr}</strong> - ${trabajador.nombre}
                            <small class="text-muted d-block">${trabajador.area || 'Sin área'}</small>
                            ${contratoInfo}
                        </a>
                    `;
                    });
                    html += '</div>';
                    resultadosDiv.html(html);

                    // Añadir eventos click a cada resultado
                    $('.agregar-trabajador-manual').on('click', function (e) {
                        e.preventDefault();
                        const pernr = $(this).data('pernr');
                        const nombre = $(this).data('nombre');
                        const area = $(this).data('area');

                        agregarTrabajadorALista(pernr, nombre, area);

                        // Limpiar búsqueda
                        $('#buscadorManualTrabajadores').val('');
                        resultadosDiv.hide().html('');
                    });
                } else {
                    resultadosDiv.html(`
                    <div class="text-muted text-center py-2">
                        <i class="bi bi-search"></i>
                        <small>No se encontraron trabajadores</small>
                    </div>
                `);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                resultadosDiv.html(`
                <div class="text-danger text-center py-2">
                    <i class="bi bi-exclamation-circle"></i>
                    <small>Error al buscar trabajadores</small>
                </div>
            `);
            });
    }

    // Función para agregar un trabajador a la lista de seleccionados
    function agregarTrabajadorALista(pernr, nombre, area) {
        const listaTrabajadores = $('#listaTrabajadoresSeleccionados');

        // Verificar si el trabajador ya está en la lista
        const yaExiste = listaTrabajadores.find(`input[value="${pernr}"]`).length > 0;

        if (yaExiste) {
            alertify.warning('El trabajador ya está en la lista');
            return;
        }

        // Si la lista está vacía o solo tiene el mensaje inicial, limpiarla
        if (listaTrabajadores.find('.list-group').length === 0) {
            listaTrabajadores.html('<div class="list-group"></div>');
        }

        // Añadir el trabajador a la lista
        const nuevoTrabajador = `
            <div class="list-group-item">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <input class="form-check-input me-2" type="checkbox" 
                               value="${pernr}" 
                               data-nombre="${nombre}"
                               data-area="${area}"
                               checked>
                        <strong>${pernr}</strong> - ${nombre}
                        <span class="badge bg-success ms-2">Añadido manualmente</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-fechas-btn" 
                            data-pernr="${pernr}">
                        <i class="bi bi-calendar-range"></i> Fechas
                    </button>
                </div>
                <div class="fechas-trabajador" id="fechas-${pernr}" style="display: none;">
                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Fecha Inicio</label>
                            <input type="date" class="form-control form-control-sm fecha-inicio" 
                                   data-pernr="${pernr}"
                                   min="${anioGrupoActual}-01-01"
                                   max="${anioGrupoActual}-12-31">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1">Fecha Fin</label>
                            <input type="date" class="form-control form-control-sm fecha-fin" 
                                   data-pernr="${pernr}"
                                   min="${anioGrupoActual}-01-01"
                                   max="${anioGrupoActual}-12-31">
                        </div>
                    </div>
                </div>
            </div>
        `;

        listaTrabajadores.find('.list-group').prepend(nuevoTrabajador);

        // Habilitar el buscador de filtrado si estaba deshabilitado
        $('#buscadorTrabajadores').prop('disabled', false);
        $('#btnLimpiarBuscador').prop('disabled', false);

        alertify.success('Trabajador añadido correctamente');
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Event listener para el botón de confirmar clonar grupo
        $('#btnConfirmarClonar').on('click', function () {
            const grupoIdOriginal = $('#grupo_id_clonar').val();
            const anioDestino = $('#anio_destino_clonar').val();
            const nuevoNombre = $('#nuevo_nombre_grupo_clonar').val().trim();
            const clonarTrabajadores = $('#clonar_trabajadores').is(':checked');

            // Validar datos
            if (!anioDestino) {
                alertify.error('Debe seleccionar un año destino');
                return;
            }

            if (!nuevoNombre) {
                alertify.error('Debe especificar un nombre para el nuevo grupo');
                return;
            }

            // Confirmar la acción
            alertify.confirm(
                'Confirmar clonación',
                `¿Deseas clonar el grupo al año <strong>${anioDestino}</strong>?<br><br>` +
                `<strong>Nuevo nombre:</strong> ${nuevoNombre}<br>` +
                `<strong>Clonar trabajadores:</strong> ${clonarTrabajadores ? 'Sí' : 'No'}`,
                function () {
                    // Usuario confirmó - proceder con la clonación
                    const btnClonar = $('#btnConfirmarClonar');
                    const textoOriginal = btnClonar.html();

                    // Deshabilitar botón y mostrar loading
                    btnClonar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Clonando...');

                    // Enviar petición al servidor
                    fetch('auto.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'clonar_grupo_horario',
                            grupo_id_original: grupoIdOriginal,
                            anio_destino: anioDestino,
                            nuevo_nombre: nuevoNombre,
                            clonar_trabajadores: clonarTrabajadores
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Restaurar botón
                            btnClonar.prop('disabled', false).html(textoOriginal);

                            if (data.success) {
                                // Cerrar el modal
                                $('#modalClonarGrupo').modal('hide');

                                // Mostrar mensaje de éxito y recargar
                                alertify.success(data.message || 'Grupo clonado correctamente');
                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                alertify.error(data.message || 'Error al clonar el grupo');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            btnClonar.prop('disabled', false).html(textoOriginal);
                            alertify.error('Error al comunicarse con el servidor');
                        });
                },
                function () {
                    // Usuario canceló
                    alertify.message('Clonación cancelada');
                }
            ).set('labels', { ok: 'Sí, clonar', cancel: 'Cancelar' })
                .set('closable', true)
                .set('reverseButtons', true);
        });

        // Event listener para los botones de toggle de fechas (delegación de eventos)
        $(document).on('click', '.toggle-fechas-btn', function () {
            const pernr = $(this).data('pernr');
            const fechasDiv = $(`#fechas-${pernr}`);
            const icon = $(this).find('i');

            if (fechasDiv.is(':visible')) {
                fechasDiv.slideUp(200);
                $(this).removeClass('btn-primary').addClass('btn-outline-secondary');
            } else {
                fechasDiv.slideDown(200);
                $(this).removeClass('btn-outline-secondary').addClass('btn-primary');
            }
        });

        // Event listener para el buscador manual de trabajadores
        $('#buscadorManualTrabajadores').on('input', function () {
            const termino = $(this).val().trim();

            // Cancelar búsqueda anterior si existe
            if (timeoutBusquedaManual) {
                clearTimeout(timeoutBusquedaManual);
            }

            // Esperar 500ms después de que el usuario deje de escribir
            timeoutBusquedaManual = setTimeout(() => {
                buscarTrabajadoresManual(termino);
            }, 500);
        });

        // Event listener para limpiar el buscador manual
        $('#btnLimpiarBuscadorManual').on('click', function () {
            $('#buscadorManualTrabajadores').val('');
            $('#resultadosBusquedaManual').hide().html('');
        });

        // Event listener para el buscador de trabajadores en la lista
        $('#buscadorTrabajadores').on('input', function () {
            filtrarTrabajadores($(this).val());
        });

        // Event listener para limpiar el buscador de la lista
        $('#btnLimpiarBuscador').on('click', function () {
            $('#buscadorTrabajadores').val('');
            filtrarTrabajadores('');
        });

        // Event listener para guardar asignación de trabajadores
        $('#btnGuardarAsignacion').on('click', function () {
            const grupoId = $('#grupo_id_asignar').val();
            const trabajadoresSeleccionados = [];

            // Obtener todos los checkboxes marcados con sus fechas
            $('#listaTrabajadoresSeleccionados input[type="checkbox"]:checked').each(function () {
                const pernr = $(this).val();
                const fechaInicio = $(`#fechas-${pernr} .fecha-inicio`).val() || null;
                const fechaFin = $(`#fechas-${pernr} .fecha-fin`).val() || null;

                trabajadoresSeleccionados.push({
                    pernr: pernr,
                    fecha_inicio: fechaInicio,
                    fecha_fin: fechaFin
                });
            });

            // Preparar mensaje de confirmación
            let mensajeConfirmacion = '';
            if (trabajadoresSeleccionados.length === 0) {
                mensajeConfirmacion = '¿Deseas <strong>eliminar todos los trabajadores</strong> de este grupo de horario?<br><br><small class="text-warning">El grupo quedará sin trabajadores asignados.</small>';
            } else {
                mensajeConfirmacion = `¿Deseas asignar <strong>${trabajadoresSeleccionados.length} trabajador(es)</strong> a este grupo de horario?<br><br><small class="text-muted">Esta acción reemplazará las asignaciones anteriores de este grupo.</small>`;
            }

            // Confirmar la acción
            alertify.confirm(
                'Confirmar asignación',
                mensajeConfirmacion,
                function () {
                    // Usuario confirmó - proceder con el guardado
                    const btnGuardar = $('#btnGuardarAsignacion');
                    const textoOriginal = btnGuardar.html();

                    // Deshabilitar botón y mostrar loading
                    btnGuardar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Guardando...');

                    // Enviar petición al servidor
                    fetch('auto.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            action: 'guardar_asignacion_trabajadores',
                            grupo_id: grupoId,
                            trabajadores: trabajadoresSeleccionados
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Restaurar botón
                            btnGuardar.prop('disabled', false).html(textoOriginal);

                            if (data.success) {
                                // Cerrar el modal primero
                                $('#modalAsignarTrabajadores').modal('hide');

                                // Verificar si hay conflictos
                                if (data.conflictos && data.conflictos.length > 0) {
                                    // Hay trabajadores con conflictos de fechas
                                    let mensajeConflictos = data.message + '<br><br><strong>Trabajadores con conflicto de fechas:</strong><br>';
                                    data.conflictos.forEach(conflicto => {
                                        mensajeConflictos += `<br>• <strong>${conflicto.pernr}</strong>: Conflicto con ${conflicto.grupos.join(', ')}`;
                                    });

                                    // Mostrar alerta y recargar SOLO cuando el usuario la cierre
                                    alertify.alert('⚠️ Asignación completada con advertencias', mensajeConflictos, function () {
                                        location.reload();
                                    })
                                        .set('label', 'Entendido')
                                        .set('closable', false);
                                } else {
                                    // No hay conflictos, mostrar mensaje de éxito y recargar
                                    alertify.success(data.message || 'Asignación guardada correctamente');
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1500);
                                }
                            } else {
                                alertify.error(data.message || 'Error al guardar la asignación');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            btnGuardar.prop('disabled', false).html(textoOriginal);
                            alertify.error('Error al comunicarse con el servidor');
                        });
                },
                function () {
                    // Usuario canceló
                    alertify.message('Asignación cancelada');
                }
            ).set('labels', { ok: 'Sí, asignar', cancel: 'Cancelar' })
                .set('closable', true)
                .set('reverseButtons', true);
        });

        // Serializar datos a JSON antes de enviar el formulario
        var form = document.getElementById('form-nuevo-grupo-horario');
        if (form) {
            form.addEventListener('submit', function (e) {
                const franjaBlocks = document.querySelectorAll('#franjas-horarias-lista > .mb-2');
                const franjas = [];
                franjaBlocks.forEach(block => {
                    // Tiempos de gracia por franja
                    const graciaBlocks = block.querySelectorAll('.tiempos-gracia-lista .mb-2');
                    const tiempos_gracia = [];
                    graciaBlocks.forEach(graciaBlock => {
                        const graciaInicio = graciaBlock.querySelector('input[data-field="gracia_inicio"]');
                        const graciaFin = graciaBlock.querySelector('input[data-field="gracia_fin"]');
                        if (graciaInicio && graciaFin) {
                            tiempos_gracia.push({
                                inicio: graciaInicio.value,
                                fin: graciaFin.value
                            });
                        }
                    });

                    // Días de la semana seleccionados
                    const diasSemana = [];
                    const checkboxesDias = block.querySelectorAll('input[data-field="dias_semana"]:checked');
                    checkboxesDias.forEach(checkbox => {
                        diasSemana.push(parseInt(checkbox.value));
                    });

                    // Buscar elementos dentro de la estructura anidada
                    const franjaInicio = block.querySelector('input[data-field="franja_inicio"]');
                    const franjaFin = block.querySelector('input[data-field="franja_fin"]');
                    const tipoJornada = block.querySelector('select[data-field="tipo_jornada"]');
                    const horaInicio = block.querySelector('input[data-field="hora_inicio"]');
                    const horaFin = block.querySelector('input[data-field="hora_fin"]');
                    const maxJornada = block.querySelector('input[data-field="max_jornada"]');
                    const umbralAviso = block.querySelector('input[data-field="umbral_aviso"]');

                    if (franjaInicio && franjaFin && tipoJornada) {
                        const esFestivo = tipoJornada.value === 'festivo_nacional' || tipoJornada.value === 'festivo_autonomico';

                        const franja = {
                            inicio_fecha: franjaInicio.value,
                            fin_fecha: franjaFin.value,
                            tipo_jornada: tipoJornada.value
                        };

                        // Solo añadir campos de horario, días y tiempos de gracia si no es festivo
                        if (!esFestivo) {
                            franja.dias_semana = diasSemana;
                            franja.tiempos_gracia = tiempos_gracia;
                            franja.inicio_hora = horaInicio.value;
                            franja.fin_hora = horaFin.value;
                            franja.max_jornada = maxJornada.value;
                            franja.umbral_aviso = umbralAviso.value;
                        }

                        franjas.push(franja);
                    }
                });
                document.getElementById('franjas_json').value = JSON.stringify(franjas);
            });
        }
        // Franjas horarias dinámicas con jornada, umbral y tiempos de gracia propios
        const franjasLista = document.getElementById('franjas-horarias-lista');
        const btnAddFranja = document.getElementById('btn-add-franja');
        const selectAnio = document.getElementById('anio_configuracion');
        const mensajeSeleccionarAnio = document.getElementById('mensaje-seleccionar-anio');

        // Habilitar botón cuando se seleccione un año
        if (selectAnio) {
            selectAnio.addEventListener('change', function () {
                if (this.value) {
                    btnAddFranja.disabled = false;
                    mensajeSeleccionarAnio.style.display = 'none';
                } else {
                    btnAddFranja.disabled = true;
                    mensajeSeleccionarAnio.style.display = 'block';
                }
            });
        }

        if (btnAddFranja && franjasLista) {
            let franjaCount = 0;
            btnAddFranja.addEventListener('click', function () {
                const anioSeleccionado = document.getElementById('anio_configuracion').value;
                if (!anioSeleccionado) {
                    alertify.error('Primero debe seleccionar un año de configuración');
                    return;
                }

                const fechaMin = `${anioSeleccionado}-01-01`;
                const fechaMax = `${anioSeleccionado}-12-31`;

                franjaCount++;
                const franjaDiv = document.createElement('div');
                franjaDiv.className = 'mb-2';
                franjaDiv.innerHTML = `
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="row g-3 align-items-end">
                        <div class="col-6 col-md-3">
                            <label class="form-label mb-1">Fecha inicio</label>
                            <input type="date" class="form-control form-control-sm" data-field="franja_inicio" 
                                   min="${fechaMin}" max="${fechaMax}" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label mb-1">Fecha fin</label>
                            <input type="date" class="form-control form-control-sm" data-field="franja_fin" 
                                   min="${fechaMin}" max="${fechaMax}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1">Tipo de jornada</label>
                            <select class="form-select form-select-sm" data-field="tipo_jornada" required>
                                <option value="normal">Jornada normal</option>
                                <option value="reducida">Jornada reducida</option>
                                <option value="especial">Jornada especial</option>
                                <option value="festivo_nacional">Festivo nacional</option>
                                <option value="festivo_autonomico">Festivo autonómico</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-end mt-2 campos-horario">
                        <div class="col-6 col-md-2">
                            <label class="form-label mb-1">Hora inicio</label>
                            <input type="time" class="form-control form-control-sm" data-field="hora_inicio" required>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label mb-1">Hora fin</label>
                            <input type="time" class="form-control form-control-sm" data-field="hora_fin" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label mb-1">Jornada máxima (h/día)</label>
                            <input type="number" class="form-control form-control-sm" data-field="max_jornada" min="1" step="0.1" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label mb-1">Umbral aviso (h)</label>
                            <input type="number" class="form-control form-control-sm" data-field="umbral_aviso" min="1" step="0.1" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-bold">Días de la semana aplicables</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="1" id="lun_${franjaCount}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="lun_${franjaCount}" style="min-width: 40px;">L</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="2" id="mar_${franjaCount}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="mar_${franjaCount}" style="min-width: 40px;">M</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="3" id="mie_${franjaCount}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="mie_${franjaCount}" style="min-width: 40px;">X</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="4" id="jue_${franjaCount}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="jue_${franjaCount}" style="min-width: 40px;">J</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="5" id="vie_${franjaCount}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="vie_${franjaCount}" style="min-width: 40px;">V</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="6" id="sab_${franjaCount}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-success" for="sab_${franjaCount}" style="min-width: 40px;">S</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="0" id="dom_${franjaCount}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-danger" for="dom_${franjaCount}" style="min-width: 40px;">D</label>
                        </div>
                        <small class="text-muted">Haz clic en los días para activarlos/desactivarlos</small>
                    </div>
                    <div class="mt-3 seccion-tiempos-gracia">
                        <label class="form-label fw-bold">Tiempos de gracia</label>
                        <div class="tiempos-gracia-lista"></div>
                        <button type="button" class="btn btn-primary btn-sm mt-1 btn-add-gracia">Añadir tiempo de gracia</button>
                    </div>
                    <div class="row mt-2">
                        <div class="col text-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarBloqueConLinea(this)">
                                <i class="fs-5 bi bi-trash-fill"></i> Eliminar franja
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
                `;
                franjasLista.appendChild(franjaDiv);

                // Evento para mostrar/ocultar campos según tipo de jornada
                const selectTipoJornada = franjaDiv.querySelector('select[data-field="tipo_jornada"]');
                const camposHorario = franjaDiv.querySelector('.campos-horario');
                const diasSemanaDiv = franjaDiv.querySelector('.mt-3');
                const seccionTiemposGracia = franjaDiv.querySelector('.seccion-tiempos-gracia');
                const camposRequeridos = camposHorario.querySelectorAll('input[required]');

                selectTipoJornada.addEventListener('change', function () {
                    const valor = this.value;
                    if (valor === 'festivo_nacional' || valor === 'festivo_autonomico') {
                        // Ocultar campos de horario
                        camposHorario.style.display = 'none';
                        // Ocultar días de la semana
                        diasSemanaDiv.style.display = 'none';
                        // Ocultar tiempos de gracia
                        seccionTiemposGracia.style.display = 'none';
                        // Quitar required de los campos ocultos
                        camposRequeridos.forEach(input => {
                            input.removeAttribute('required');
                        });
                    } else {
                        // Mostrar campos de horario
                        camposHorario.style.display = '';
                        // Mostrar días de la semana
                        diasSemanaDiv.style.display = '';
                        // Mostrar tiempos de gracia
                        seccionTiemposGracia.style.display = '';
                        // Añadir required a los campos visibles
                        camposRequeridos.forEach(input => {
                            input.setAttribute('required', 'required');
                        });
                    }
                });

                // Tiempos de gracia dinámicos por franja
                const tiemposLista = franjaDiv.querySelector('.tiempos-gracia-lista');
                const btnAddGracia = franjaDiv.querySelector('.btn-add-gracia');
                if (btnAddGracia && tiemposLista) {
                    btnAddGracia.addEventListener('click', function () {
                        const graciaDiv = document.createElement('div');
                        graciaDiv.className = 'mb-2';
                        graciaDiv.innerHTML = `
                        <div class="border rounded p-3 mb-3 bg-light">
                            <div class="row g-3 align-items-end">
                                <div class="col-6 col-md-5">
                                    <label class="form-label mb-1">Inicio</label>
                                    <input type="time" class="form-control form-control-sm" data-field="gracia_inicio" required>
                                </div>
                                <div class="col-6 col-md-5">
                                    <label class="form-label mb-1">Fin</label>
                                    <input type="time" class="form-control form-control-sm" data-field="gracia_fin" required>
                                </div>
                                <div class="col-12 col-md-2 text-end">
                                    <button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.closest('.mb-2').remove()">
                                        <i class="fs-5 bi bi-trash-fill"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <hr class="my-2">
                        `;
                        tiemposLista.appendChild(graciaDiv);
                    });
                }
            });
        }
        // Función para eliminar bloque y su línea divisoria <hr>
        window.eliminarBloqueConLinea = function (btn) {
            const bloque = btn.closest('.mb-2');
            if (bloque) {
                const nextHr = bloque.nextElementSibling;
                if (nextHr && nextHr.tagName === 'HR') {
                    nextHr.remove();
                }
                bloque.remove();
            }
        }

        // Función para confirmar eliminación de grupo
        window.confirmarEliminar = function (id, nombre) {
            alertify.confirm(
                'Confirmar eliminación',
                `¿Estás seguro de que deseas eliminar el grupo <strong>"${nombre}"</strong>?
                <br><br>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>¡Atención!</strong> Esta acción eliminará:
                    <ul class="mb-0 mt-2">
                        <li>El grupo de horario</li>
                        <li>Todas las franjas horarias configuradas</li>
                        <li>Todos los trabajadores asignados a este grupo</li>
                    </ul>
                </div>
                <br>
                <small class="text-danger">Esta acción no se puede deshacer.</small>`,
                function () {
                    // Usuario confirmó - proceder con la eliminación
                    window.location.href = `admin_cont.php?controller=horarios&action=horario&eliminar=${id}`;
                },
                function () {
                    // Usuario canceló - cerrar diálogo
                    alertify.message('Eliminación cancelada');
                }
            ).set('labels', { ok: 'Sí, eliminar', cancel: 'Cancelar' })
                .set('closable', true)
                .set('reverseButtons', true);
        }

        // Variables globales para el modal de edición
        let franjaCountEditar = 0;

        // Función para abrir modal de edición con datos precargados
        window.abrirModalEditar = function (grupoData) {
            // Eliminar modal existente si lo hay
            const modalExistente = document.getElementById('modalEditarGrupoHorario');
            if (modalExistente) {
                modalExistente.remove();
            }

            // Verificar si hay franjas configuradas
            let tieneFranjas = false;
            try {
                const franjas = JSON.parse(grupoData.franjas_json);
                tieneFranjas = Array.isArray(franjas) && franjas.length > 0;
            } catch (e) {
                tieneFranjas = false;
            }

            // Generar el campo de año según si tiene franjas o no
            let campoAnio = '';
            if (tieneFranjas) {
                // Si tiene franjas: campo readonly
                campoAnio = `
                    <input type="text" class="form-control" id="anio_configuracion_editar" 
                        name="anio_configuracion" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                    <small class="text-muted">El año de configuración no se puede modificar cuando hay franjas configuradas</small>
                `;
            } else {
                // Si NO tiene franjas: select editable
                campoAnio = `
                    <select class="form-select" id="anio_configuracion_editar" name="anio_configuracion" required>
                        <option value="" selected>Seleccione un año</option>
                    </select>
                    <small class="text-muted">Las franjas solo podrán configurarse para este año</small>
                `;
            }

            // Crear el modal dinámicamente
            const modalHTML = `
                <div class="modal fade" id="modalEditarGrupoHorario" tabindex="-1"
                    aria-labelledby="modalEditarGrupoHorarioLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <form action="admin_cont.php?controller=horarios&action=horario&editar" method="POST"
                                id="form-editar-grupo-horario">
                                <input type="hidden" name="editar_grupo" value="1">
                                <input type="hidden" id="grupo_id_editar" name="grupo_id" value="${grupoData.id}">
                                <input type="hidden" id="franjas_json_editar" name="franjas_json" value="[]">
                                <div class="modal-header bg-primary text-white">
                                    <h5 class="modal-title" id="modalEditarGrupoHorarioLabel">
                                        <i class="bi bi-pencil me-2"></i>
                                        Editar grupo de horario laboral: ${grupoData.nombre_grupo}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Cerrar"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="nombre_grupo_editar" class="form-label fw-bold">Nombre del horario</label>
                                        <input type="text" class="form-control" id="nombre_grupo_editar" name="nombre_grupo"
                                            value="${grupoData.nombre_grupo}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="descripcion_grupo_editar" class="form-label fw-bold">Descripción</label>
                                        <input type="text" class="form-control" id="descripcion_grupo_editar"
                                            name="descripcion_grupo" value="${grupoData.descripcion_grupo || ''}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="anio_configuracion_editar" class="form-label fw-bold">Año de configuración</label>
                                        ${campoAnio}
                                    </div>
                                    <div class="mb-3">
                                        <label for="max_dias_vacaciones_editar" class="form-label fw-bold">Días máximos de vacaciones</label>
                                        <input type="number" class="form-control" id="max_dias_vacaciones_editar"
                                            name="max_dias_vacaciones" min="0" step="1" value="" placeholder="Ej: 30">
                                        <small class="text-muted">Configure el máximo de días de vacaciones para este grupo</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Franjas horarias anuales</label>
                                        <div id="franjas-horarias-lista-editar"></div>
                                        <button type="button" class="btn btn-primary btn-sm mt-2"
                                            id="btn-add-franja-editar" ${!tieneFranjas ? 'disabled' : ''}>Añadir franja</button>
                                        <small class="text-muted d-block mt-1" id="mensaje-seleccionar-anio-editar" ${tieneFranjas ? 'style="display: none;"' : ''}>Primero seleccione un año de configuración</small>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input type="hidden" name="grupo_predeterminado" value="0">
                                        <input class="form-check-input" type="checkbox" id="grupo_predeterminado_editar"
                                            name="grupo_predeterminado" value="1" ${grupoData.grupo_predeterminado == 1 ? 'checked' : ''}>
                                        <label class="form-check-label" for="grupo_predeterminado_editar">Grupo
                                            predeterminado</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Actualizar grupo</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            `;

            // Añadir modal al DOM
            document.body.insertAdjacentHTML('beforeend', modalHTML);

            // Configurar eventos del modal recién creado
            configurarModalEditar(grupoData);

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('modalEditarGrupoHorario'));
            modal.show();

            // Limpiar modal del DOM cuando se cierre
            document.getElementById('modalEditarGrupoHorario').addEventListener('hidden.bs.modal', function () {
                this.remove();
            });
        }

        // Función para configurar eventos del modal de edición
        function configurarModalEditar(grupoData) {
            franjaCountEditar = 0;

            // Determinar el año de las franjas existentes
            const campoAnioEditar = document.getElementById('anio_configuracion_editar');
            const anioActual = new Date().getFullYear();
            let anioExistente = anioActual;
            let tieneFranjas = false;

            try {
                const franjas = JSON.parse(grupoData.franjas_json);
                tieneFranjas = Array.isArray(franjas) && franjas.length > 0;
                if (tieneFranjas && franjas[0].inicio_fecha) {
                    anioExistente = parseInt(franjas[0].inicio_fecha.split('-')[0]);
                }
            } catch (e) {
                anioExistente = anioActual;
            }

            const btnAddFranjaEditar = document.getElementById('btn-add-franja-editar');
            const mensajeSeleccionarAnioEditar = document.getElementById('mensaje-seleccionar-anio-editar');

            // Si es un select (sin franjas), poblar opciones
            if (campoAnioEditar.tagName === 'SELECT') {
                // Agregar opciones de años
                for (let i = anioActual - 1; i <= anioActual + 5; i++) {
                    const option = document.createElement('option');
                    option.value = i;
                    option.textContent = i;
                    campoAnioEditar.appendChild(option);
                }

                // Event listener para habilitar/deshabilitar botón
                campoAnioEditar.addEventListener('change', function () {
                    if (this.value) {
                        btnAddFranjaEditar.disabled = false;
                        mensajeSeleccionarAnioEditar.style.display = 'none';
                    } else {
                        btnAddFranjaEditar.disabled = true;
                        mensajeSeleccionarAnioEditar.style.display = 'block';
                    }
                });

                // Habilitar botón si hay año seleccionado
                if (campoAnioEditar.value) {
                    btnAddFranjaEditar.disabled = false;
                    mensajeSeleccionarAnioEditar.style.display = 'none';
                }
            } else {
                // Si es un input readonly (con franjas), solo establecer valor
                campoAnioEditar.value = anioExistente;
                btnAddFranjaEditar.disabled = false;
                mensajeSeleccionarAnioEditar.style.display = 'none';
            }

            // Configurar serialización del formulario
            const formEditar = document.getElementById('form-editar-grupo-horario');
            formEditar.addEventListener('submit', function (e) {
                const franjaBlocks = document.querySelectorAll('#franjas-horarias-lista-editar > .mb-2');
                const franjas = [];
                franjaBlocks.forEach(block => {
                    // Tiempos de gracia por franja
                    const graciaBlocks = block.querySelectorAll('.tiempos-gracia-lista .mb-2');
                    const tiempos_gracia = [];
                    graciaBlocks.forEach(graciaBlock => {
                        const graciaInicio = graciaBlock.querySelector('input[data-field="gracia_inicio"]');
                        const graciaFin = graciaBlock.querySelector('input[data-field="gracia_fin"]');
                        if (graciaInicio && graciaFin) {
                            tiempos_gracia.push({
                                inicio: graciaInicio.value,
                                fin: graciaFin.value
                            });
                        }
                    });

                    // Días de la semana seleccionados
                    const diasSemana = [];
                    const checkboxesDias = block.querySelectorAll('input[data-field="dias_semana"]:checked');
                    checkboxesDias.forEach(checkbox => {
                        diasSemana.push(parseInt(checkbox.value));
                    });

                    // Buscar elementos dentro de la estructura anidada
                    const franjaInicio = block.querySelector('input[data-field="franja_inicio"]');
                    const franjaFin = block.querySelector('input[data-field="franja_fin"]');
                    const tipoJornada = block.querySelector('select[data-field="tipo_jornada"]');
                    const horaInicio = block.querySelector('input[data-field="hora_inicio"]');
                    const horaFin = block.querySelector('input[data-field="hora_fin"]');
                    const maxJornada = block.querySelector('input[data-field="max_jornada"]');
                    const umbralAviso = block.querySelector('input[data-field="umbral_aviso"]');

                    if (franjaInicio && franjaFin && tipoJornada) {
                        const esFestivo = tipoJornada.value === 'festivo_nacional' || tipoJornada.value === 'festivo_autonomico';

                        const franja = {
                            inicio_fecha: franjaInicio.value,
                            fin_fecha: franjaFin.value,
                            tipo_jornada: tipoJornada.value
                        };

                        // Solo añadir campos de horario, días y tiempos de gracia si no es festivo
                        if (!esFestivo) {
                            franja.dias_semana = diasSemana;
                            franja.tiempos_gracia = tiempos_gracia;
                            franja.inicio_hora = horaInicio.value;
                            franja.fin_hora = horaFin.value;
                            franja.max_jornada = maxJornada.value;
                            franja.umbral_aviso = umbralAviso.value;
                        }

                        franjas.push(franja);
                    }
                });
                document.getElementById('franjas_json_editar').value = JSON.stringify(franjas);
            });

            // Configurar botón añadir franja
            btnAddFranjaEditar.addEventListener('click', function () {
                const anioSeleccionado = document.getElementById('anio_configuracion_editar').value;
                if (!anioSeleccionado) {
                    alertify.error('Primero debe seleccionar un año de configuración');
                    return;
                }
                crearFranjaEditar();
            });

            // Cargar franjas existentes
            let franjas = [];
            try {
                franjas = JSON.parse(grupoData.franjas_json);
            } catch (e) {
                franjas = [];
            }

            // Cargar el valor de max_dias_vacaciones desde grupoData
            if (grupoData.max_dias_vacaciones !== null && grupoData.max_dias_vacaciones !== undefined) {
                document.getElementById('max_dias_vacaciones_editar').value = grupoData.max_dias_vacaciones;
            }

            if (Array.isArray(franjas)) {
                franjas.forEach(franja => {
                    crearFranjaEditar(franja);
                });
            }
        }

        // Función para crear una franja en el modal de edición
        function crearFranjaEditar(datosPrecargar = null) {
            const anioSeleccionado = document.getElementById('anio_configuracion_editar').value;
            const fechaMin = `${anioSeleccionado}-01-01`;
            const fechaMax = `${anioSeleccionado}-12-31`;

            franjaCountEditar++;
            const franjaDiv = document.createElement('div');
            franjaDiv.className = 'mb-2';
            franjaDiv.innerHTML = `
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="row g-3 align-items-end">
                        <div class="col-6 col-md-3">
                            <label class="form-label mb-1">Fecha inicio</label>
                            <input type="date" class="form-control form-control-sm" data-field="franja_inicio" 
                                   min="${fechaMin}" max="${fechaMax}" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label mb-1">Fecha fin</label>
                            <input type="date" class="form-control form-control-sm" data-field="franja_fin" 
                                   min="${fechaMin}" max="${fechaMax}" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label mb-1">Tipo de jornada</label>
                            <select class="form-select form-select-sm" data-field="tipo_jornada" required>
                                <option value="normal">Jornada normal</option>
                                <option value="reducida">Jornada reducida</option>
                                <option value="especial">Jornada especial</option>
                                <option value="festivo_nacional">Festivo nacional</option>
                                <option value="festivo_autonomico">Festivo autonómico</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-end mt-2 campos-horario">
                        <div class="col-6 col-md-2">
                            <label class="form-label mb-1">Hora inicio</label>
                            <input type="time" class="form-control form-control-sm" data-field="hora_inicio" required>
                        </div>
                        <div class="col-6 col-md-2">
                            <label class="form-label mb-1">Hora fin</label>
                            <input type="time" class="form-control form-control-sm" data-field="hora_fin" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label mb-1">Jornada máxima (h/día)</label>
                            <input type="number" class="form-control form-control-sm" data-field="max_jornada" min="1" step="0.1" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label mb-1">Umbral aviso (h)</label>
                            <input type="number" class="form-control form-control-sm" data-field="umbral_aviso" min="1" step="0.1" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label fw-bold">Días de la semana aplicables</label>
                        <div class="d-flex flex-wrap gap-2">
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="1" id="lun_editar_${franjaCountEditar}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="lun_editar_${franjaCountEditar}" style="min-width: 40px;">L</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="2" id="mar_editar_${franjaCountEditar}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="mar_editar_${franjaCountEditar}" style="min-width: 40px;">M</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="3" id="mie_editar_${franjaCountEditar}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="mie_editar_${franjaCountEditar}" style="min-width: 40px;">X</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="4" id="jue_editar_${franjaCountEditar}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="jue_editar_${franjaCountEditar}" style="min-width: 40px;">J</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="5" id="vie_editar_${franjaCountEditar}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-primary" for="vie_editar_${franjaCountEditar}" style="min-width: 40px;">V</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="6" id="sab_editar_${franjaCountEditar}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-success" for="sab_editar_${franjaCountEditar}" style="min-width: 40px;">S</label>
                            
                            <input type="checkbox" class="btn-check" data-field="dias_semana" value="0" id="dom_editar_${franjaCountEditar}" autocomplete="off">
                            <label class="btn btn-sm btn-outline-danger" for="dom_editar_${franjaCountEditar}" style="min-width: 40px;">D</label>
                        </div>
                        <small class="text-muted">Haz clic en los días para activarlos/desactivarlos</small>
                    </div>
                    <div class="mt-3 seccion-tiempos-gracia">
                        <label class="form-label fw-bold">Tiempos de gracia</label>
                        <div class="tiempos-gracia-lista"></div>
                        <button type="button" class="btn btn-primary btn-sm mt-1 btn-add-gracia-editar">Añadir tiempo de gracia</button>
                    </div>
                    <div class="row mt-2">
                        <div class="col text-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarBloqueConLinea(this)">
                                <i class="fs-5 bi bi-trash-fill"></i> Eliminar franja
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
            `;

            document.getElementById('franjas-horarias-lista-editar').appendChild(franjaDiv);

            // Si hay datos para precargar, llenar los campos
            if (datosPrecargar) {
                franjaDiv.querySelector('input[data-field="franja_inicio"]').value = datosPrecargar.inicio_fecha || '';
                franjaDiv.querySelector('input[data-field="franja_fin"]').value = datosPrecargar.fin_fecha || '';
                franjaDiv.querySelector('select[data-field="tipo_jornada"]').value = datosPrecargar.tipo_jornada || 'normal';
                franjaDiv.querySelector('input[data-field="hora_inicio"]').value = datosPrecargar.inicio_hora || '';
                franjaDiv.querySelector('input[data-field="hora_fin"]').value = datosPrecargar.fin_hora || '';
                franjaDiv.querySelector('input[data-field="max_jornada"]').value = datosPrecargar.max_jornada || '';
                franjaDiv.querySelector('input[data-field="umbral_aviso"]').value = datosPrecargar.umbral_aviso || '';

                // Marcar días de la semana
                if (Array.isArray(datosPrecargar.dias_semana)) {
                    datosPrecargar.dias_semana.forEach(dia => {
                        const checkbox = franjaDiv.querySelector(`input[data-field="dias_semana"][value="${dia}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                }

                // Cargar tiempos de gracia
                if (Array.isArray(datosPrecargar.tiempos_gracia)) {
                    const tiemposLista = franjaDiv.querySelector('.tiempos-gracia-lista');
                    datosPrecargar.tiempos_gracia.forEach(tiempo => {
                        crearTiempoGraciaEditar(tiemposLista, tiempo);
                    });
                }
            }

            // Evento para mostrar/ocultar campos según tipo de jornada
            const selectTipoJornada = franjaDiv.querySelector('select[data-field="tipo_jornada"]');
            const camposHorario = franjaDiv.querySelector('.campos-horario');
            const diasSemanaDiv = franjaDiv.querySelectorAll('.mt-3')[0]; // Primer .mt-3 es días de la semana
            const seccionTiemposGracia = franjaDiv.querySelector('.seccion-tiempos-gracia');
            const camposRequeridos = camposHorario.querySelectorAll('input[required]');

            // Función para actualizar visibilidad
            const actualizarVisibilidadCampos = () => {
                const valor = selectTipoJornada.value;
                if (valor === 'festivo_nacional' || valor === 'festivo_autonomico') {
                    // Ocultar campos de horario
                    camposHorario.style.display = 'none';
                    // Ocultar días de la semana
                    if (diasSemanaDiv) diasSemanaDiv.style.display = 'none';
                    // Ocultar tiempos de gracia
                    if (seccionTiemposGracia) seccionTiemposGracia.style.display = 'none';
                    // Quitar required de los campos ocultos
                    camposRequeridos.forEach(input => {
                        input.removeAttribute('required');
                    });
                } else {
                    // Mostrar campos de horario
                    camposHorario.style.display = '';
                    // Mostrar días de la semana
                    if (diasSemanaDiv) diasSemanaDiv.style.display = '';
                    // Mostrar tiempos de gracia
                    if (seccionTiemposGracia) seccionTiemposGracia.style.display = '';
                    // Añadir required a los campos visibles
                    camposRequeridos.forEach(input => {
                        input.setAttribute('required', 'required');
                    });
                }
            };

            selectTipoJornada.addEventListener('change', actualizarVisibilidadCampos);

            // Aplicar visibilidad inicial si hay datos precargados
            if (datosPrecargar && (datosPrecargar.tipo_jornada === 'festivo_nacional' || datosPrecargar.tipo_jornada === 'festivo_autonomico')) {
                actualizarVisibilidadCampos();
            }

            // Configurar botón de añadir tiempo de gracia
            const btnAddGracia = franjaDiv.querySelector('.btn-add-gracia-editar');
            const tiemposLista = franjaDiv.querySelector('.tiempos-gracia-lista');
            btnAddGracia.addEventListener('click', function () {
                crearTiempoGraciaEditar(tiemposLista);
            });
        }

        // Función para crear tiempo de gracia en edición
        function crearTiempoGraciaEditar(tiemposLista, datosPrecargar = null) {
            const graciaDiv = document.createElement('div');
            graciaDiv.className = 'mb-2';
            graciaDiv.innerHTML = `
                <div class="border rounded p-3 mb-3 bg-light">
                    <div class="row g-3 align-items-end">
                        <div class="col-6 col-md-5">
                            <label class="form-label mb-1">Inicio</label>
                            <input type="time" class="form-control form-control-sm" data-field="gracia_inicio" required>
                        </div>
                        <div class="col-6 col-md-5">
                            <label class="form-label mb-1">Fin</label>
                            <input type="time" class="form-control form-control-sm" data-field="gracia_fin" required>
                        </div>
                        <div class="col-12 col-md-2 text-end">
                            <button type="button" class="btn btn-danger btn-sm mt-2" onclick="this.closest('.mb-2').remove()">
                                <i class="fs-5 bi bi-trash-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <hr class="my-2">
            `;

            tiemposLista.appendChild(graciaDiv);

            // Si hay datos para precargar
            if (datosPrecargar) {
                graciaDiv.querySelector('input[data-field="gracia_inicio"]').value = datosPrecargar.inicio || '';
                graciaDiv.querySelector('input[data-field="gracia_fin"]').value = datosPrecargar.fin || '';
            }
        } // Cierre de crearTiempoGraciaEditar

        // Inicializar tooltips de Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                html: true
            });
        });
    });
</script>

<?php
include_once("footer.php");
?>