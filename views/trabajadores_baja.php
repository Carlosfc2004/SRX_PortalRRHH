<?php
include_once("header.php");
$num_trab = 0;
?>
<style>
    /* Asegurar que el select desplegado tenga borde en todos los lados */
    .form-select option {
        border: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6 !important;
    }
    
    /* Alternativa: forzar borde en el select cuando está enfocado/desplegado */
    select.form-select:focus option,
    select.form-select option {
        border-right: 1px solid #ced4da !important;
    }
</style>
<div class="pagetitle">
    <h1><?php echo $lang['tit_trab_baja']; ?></h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i
                    class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><?php echo $lang['menu3']; ?></li>
        <li class="breadcrumb-item active"><?php echo $lang['menu20']; ?></li>
    </ol>
</nav>
<section class="section">
    <div class="card">
        <div class="card-body">
            <form action="admin_cont.php?controller=index&action=trabajadores_baja" method="post">
                <h5 class="card-title">Filtros:</h5>
                <div class="row align-items-end">
                    <!-- Ubicación -->
                    <div class="col-md-3">
                        <label for="ubi_trab" class="form-label">Ubicación:</label>
                        <select class="form-select" name="ubi_trab" id="ubi_trab">
                            <option value=""></option>
                            <?php
                            foreach ($params['fincas_almacenes'] as $key => $value) {
                                echo '<option value="' . $value['ZZLGORT'] . '" ' . (isset($_POST['ubi_trab']) && $_POST['ubi_trab'] == $value['ZZLGORT'] ? 'selected' : '') . '>' . $value['DESC_ALMACEN'] . " (" . $value['ZZLGORT'] . ")" . '</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Botón para abrir modal formateador -->
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                            data-bs-target="#modalFormateadorCodigos">
                            <i class="bi bi-list-ol"></i> Trabajadores
                            <?php if (isset($_POST['codigos_formateados']) && !empty($_POST['codigos_formateados'])) { ?>
                                <span class="badge bg-light ms-1 text-dark" id="badgeTrabajadores">✓</span>
                            <?php } else { ?>
                                <span class="badge bg-light ms-1 text-dark" id="badgeTrabajadores" style="display: none;"></span>
                            <?php } ?>
                        </button>
                        <?php if (isset($_POST['codigos_formateados']) && !empty($_POST['codigos_formateados'])) { ?>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1 w-100"
                                onclick="limpiarCodigosSeleccionados()" title="Limpiar trabajadores seleccionados">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </button>
                        <?php } else { ?>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-1 w-100" id="btnLimpiarCodigos"
                                onclick="limpiarCodigosSeleccionados()" style="display: none;"
                                title="Limpiar trabajadores seleccionados">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </button>
                        <?php } ?>
                    </div>

                    <!-- Campos ocultos para códigos -->
                    <input type="hidden" id="codigos_formateados" name="codigos_formateados"
                        value="<?php echo isset($_POST['codigos_formateados']) ? htmlspecialchars($_POST['codigos_formateados']) : ''; ?>">
                    <input type="hidden" id="codigos_originales" name="codigos_originales"
                        value="<?php echo isset($_POST['codigos_originales']) ? htmlspecialchars($_POST['codigos_originales']) : ''; ?>">

                    <!-- Fecha inicio -->
                    <div class="col-md-3">
                        <label for="fecha_ini" class="form-label">Fecha baja:</label>
                        <input type="date" class="form-control" id="fecha_ini" name="fecha_ini"
                            value="<?php echo isset($_POST['fecha_ini']) ? $_POST['fecha_ini'] : date('Y-m-d', strtotime('-16 months')); ?>">
                    </div>

                    <!-- Separador "a" -->
                    <div class="col-md-1 text-md-center">
                        <span>a</span>
                    </div>

                    <!-- Fecha fin -->
                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label" style="color: white">Fecha baja fin:</label>
                        <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                            value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : date('Y-m-d'); ?>">
                    </div>

                    <!-- Campo para relaciones laborales -->
                    <div class="col-md-3 mt-3">
                        <label for="relacion_laboral" class="form-label">Relación Laboral:</label>
                        <select class="form-select w-100" name="relacion_laboral" id="relacion_laboral">
                            <option value=""></option>
                            <?php
                                foreach ($params['relaciones_laborales'] as $key => $value) {
                                    echo '<option value="' . $value['RELACION_LABORAL'] . '" ' . (isset($_POST['relacion_laboral']) && $_POST['relacion_laboral'] == $value['RELACION_LABORAL'] ? 'selected' : '') . '>' . $value['RELACION_LABORAL'] . " - " . $value['DESC_RELACION_LABORAL'] . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <br>

                <div id="loading" style="display: none;">
                    <button class="btn btn-primary mt-2" type="button" disabled="">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Buscando...
                    </button>
                </div>
                <input type="submit" name="buscar" value="Buscar" class="btn btn-primary mt-2">
                <script>
                    // Mostrar el div de carga y ocultar el botón de búsqueda al enviar el formulario
                    document.querySelector('form').addEventListener('submit', function () {
                        document.getElementById('loading').style.display = 'block';
                        document.querySelector('input[type="submit"]').style.display = 'none';
                    });
                </script>
            </form>

            <form action='' id='exportar' class='mt-3' method='post' style='display: inline-block; margin-left: 15px;'>
                <button type="button" target="_blank"
                    onclick="document.getElementById('exportar').action='exportar.php?informe_trabajadores_baja_excel&ubicacion=' + document.getElementById('ubi_trab').value + '&fecha_ini=' + document.getElementById('fecha_ini').value + '&fecha_fin=' + document.getElementById('fecha_fin').value + '&codigos=' + document.getElementById('codigos_formateados').value + '&relacion_laboral=' + document.getElementById('relacion_laboral').value; document.getElementById('exportar').submit();"
                    style="background-color: white;">
                    <img src="img/xls.png" style="max-width: 100px; width: 35px; margin-top: 10px;">
                </button>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo $lang['titu_llamamiento']; ?> (<span id="num_trab"
                    style="font-weight: bold; color: #012970;"><?php echo count($params['datos_trab_baja'] ?? []); ?></span>)
            </h5>
            <table id="table_info" class="table datatable display" style="width:100%;">
                <thead>
                    <tr>
                        <th>Cod. Trabajador</th>
                        <th style="width: 20%;"><?php echo $lang['nombre']; ?></th>
                        <th style="width: 10%;">Fecha Baja</th>
                        <th>Almacen</th>
                        <th style="width: 15%;">Relacion Laboral</th>
                        <th><?php echo $lang['menu5.1']; ?></th>
                        <th><?php echo $lang['ult_llama']; ?></th>
                        <th data-sortable="false"></th>
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
                                    <?php
                                    if (!empty($resultado['DESC_ALMACEN']) && !empty($resultado['ZZLGORT'])) {
                                        // Si DESC_ALMACEN no está vacío ni es 'NULL', mostrarlo
                                        echo $resultado['DESC_ALMACEN'] . ' (' . $resultado['ZZLGORT'] . ')';
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($resultado['DESC_RELACION_LABORAL'])) {
                                        // Si DESC_RELACION_LABORAL no está vacío ni es 'NULL', mostrarlo
                                        echo $resultado['RELACION_LABORAL'] . ' - ' . $resultado['DESC_RELACION_LABORAL'];
                                    } else {
                                        echo '';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="admin_cont.php?controller=index&action=view_remesa_llama&id=<?php echo $resultado['id_remesa'] ?>&ano=<?php echo $resultado['ano_remesa'] ?>"
                                        target="_blank" style="color:#012970; text-decoration: underline;">
                                        <?php echo $resultado['nombre_remesa'] ?>
                                    </a>
                                </td>
                                <td><?php echo (!empty($resultado['FECHA_REGISTRO']) ? $resultado['FECHA_REGISTRO']->format('Y-m-d H:i:s') : '') ?>
                                </td>
                                <td>
                                    <a href='admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $resultado['PERNR']; ?>&contact'
                                        target="_blank">
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


    <!-- Modal Formateador de Códigos -->
    <div class="modal fade" id="modalFormateadorCodigos" tabindex="-1" aria-labelledby="modalFormateadorCodigosLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalFormateadorCodigosLabel">
                        <i class="bi bi-search me-2"></i>Buscar Trabajadores por Código
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="codigosInput" class="form-label"><strong>Pega aquí los códigos PERNR o DNI/NIE desde
                                Excel:</strong></label>
                        <textarea class="form-control font-monospace" id="codigosInput" rows="10"
                            placeholder="1004207&#10;01005734&#10;...&#10;12345678A&#10;87654321C&#10;...&#10;X0134567A&#10;A0123456Z&#10;..."></textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Los códigos de 7 dígitos se convertirán automáticamente a
                            8 dígitos (añadiendo un 0 a la izquierda).
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="aplicarCodigos()">
                        <i class="bi bi-check-circle"></i> Aplicar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        /**
         * Inicializar badge al cargar la página si hay códigos del POST
         */
        document.addEventListener('DOMContentLoaded', function () {
            const codigosFormateados = document.getElementById('codigos_formateados').value;
            const codigosOriginales = document.getElementById('codigos_originales').value;

            if (codigosFormateados && codigosFormateados.trim()) {
                // Contar cuántos códigos hay (contar las comillas simples y dividir por 2)
                const numCodigos = (codigosFormateados.match(/'/g) || []).length / 2;

                // Actualizar badge
                const badge = document.getElementById('badgeTrabajadores');
                badge.textContent = Math.floor(numCodigos);
                badge.style.display = 'inline-block';

                // Mostrar botón limpiar
                const btnLimpiar = document.getElementById('btnLimpiarCodigos');
                if (btnLimpiar) {
                    btnLimpiar.style.display = 'block';
                }
            }

            // Restaurar códigos originales en el textarea cuando se abre el modal
            const modal = document.getElementById('modalFormateadorCodigos');
            modal.addEventListener('show.bs.modal', function () {
                if (codigosOriginales && codigosOriginales.trim()) {
                    document.getElementById('codigosInput').value = codigosOriginales;
                }
            });
        });

        /**
         * Aplicar códigos formateados (sin enviar el formulario)
         */
        function aplicarCodigos() {
            const input = document.getElementById('codigosInput').value;

            if (!input.trim()) {
                alertify.warning('Por favor, pega códigos PERNR en el área de texto');
                return;
            }

            // Dividir por saltos de línea y eliminar espacios
            const lineas = input.split('\n');
            const codigos = [];
            let procesados = 0;
            let modificados = 0;

            lineas.forEach(linea => {
                let codigo = linea.trim();

                // Ignorar líneas vacías
                if (!codigo) return;

                // Si tiene 7 caracteres, añadir 0 a la izquierda
                if (codigo.length === 7 && /^\d+$/.test(codigo)) {
                    codigo = '0' + codigo;
                    modificados++;
                }

                codigos.push(codigo);
                procesados++;
            });

            if (procesados === 0) {
                alertify.warning('No se encontraron códigos válidos');
                return;
            }

            // Formatear para SQL: 'codigo1', 'codigo2', 'codigo3'
            const resultado = "'" + codigos.join("', '") + "'";

            // Guardar los códigos formateados en el campo oculto
            document.getElementById('codigos_formateados').value = resultado;

            // Guardar los códigos originales (sin formatear) para restaurarlos después
            document.getElementById('codigos_originales').value = input;

            // Mostrar badge con indicador
            const badge = document.getElementById('badgeTrabajadores');
            badge.textContent = procesados;
            badge.style.display = 'inline-block';

            // Mostrar botón de limpiar
            const btnLimpiar = document.getElementById('btnLimpiarCodigos');
            if (btnLimpiar) {
                btnLimpiar.style.display = 'block';
            }

            // Cerrar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalFormateadorCodigos'));
            modal.hide();

            // Notificar al usuario
            let mensaje = `${procesados} trabajador(es) seleccionado(s)`;
            if (modificados > 0) {
                mensaje += ` (${modificados} código(s) convertido(s) de 7 a 8 dígitos)`;
            }
            mensaje += '. Haz clic en "Buscar" para aplicar los filtros.';
            alertify.success(mensaje);
        }

        /**
         * Limpiar códigos seleccionados
         */
        function limpiarCodigosSeleccionados() {
            // Limpiar campos ocultos
            document.getElementById('codigos_formateados').value = '';
            document.getElementById('codigos_originales').value = '';

            // Limpiar textarea del modal
            document.getElementById('codigosInput').value = '';

            // Ocultar badge
            const badge = document.getElementById('badgeTrabajadores');
            badge.style.display = 'none';

            // Ocultar botón de limpiar
            const btnLimpiar = document.getElementById('btnLimpiarCodigos');
            if (btnLimpiar) {
                btnLimpiar.style.display = 'none';
            }

            alertify.success('Trabajadores seleccionados eliminados');
        }
    </script>

    <?php
    include_once("footer.php");
    ?>

    <a href="#ancla" class="back-to-top d-flex align-items-center justify-content-center active">
        <i class="bi bi-arrow-up-short"></i>
    </a>