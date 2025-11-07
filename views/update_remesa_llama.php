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

        echo '<h1 style="font-size: 24px; margin-bottom: 0; font-weight: 600; color: #012970;">'.$nombre_remesa.' (Trabajadores: '.count($info_remesas).')</h1>';
    ?>
    
    <?php
    // Mostrar mensaje de resultado de respuesta masiva
    if (isset($params['resultado'])) {
        $es_error = strpos($params['resultado'], 'errores') !== false || strpos($params['resultado'], 'Error') !== false;
        $clase_alerta = $es_error ? 'alert-warning' : 'alert-success';
        echo '<div class="alert '.$clase_alerta.' alert-dismissible fade show mt-3" role="alert">';
        echo '<i class="bi '.($es_error ? 'bi-exclamation-triangle' : 'bi-check-circle').'"></i> '.$params['resultado'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
        echo '</div>';
    }
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

            <!-- Botón para responder llamamientos masivamente -->
            <button type="button" class="btn btn-success mb-2" data-bs-toggle="modal" data-bs-target="#modalRespuestaMasiva" style="margin-left: 5px;">
                <i class="bi bi-check2-all"></i> Responder llamamientos
            </button>

            
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
                                    <div style="display: flex; gap: 5px; align-items: center;">
                                        <!-- Botón editar -->
                                        <form action="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $remesa['PERNR']; ?>&showll&id_rem=<?php echo $remesa['id_remesa']; ?>&ano_rem=<?php echo $remesa['ano_remesa']; ?>&remesa=<?php echo $_GET['remesa']; ?>" method="post">
                                            <input type="hidden" value="<?php echo $remesa['id_remesa']; ?>" name="id_remesa">
                                            <input type="hidden" value="<?php echo $remesa['ano_remesa']; ?>" name="ano_remesa">
                                            <input type="hidden" name="datos_remesa" value="1">
                                            <input type="hidden" name="remesa" value="<?php echo $_GET['remesa']; ?>">
                                            <button type="submit" class="icono hvr-icon" style="background: none; border: none; cursor: pointer;">
                                                <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                            </button>
                                        </form>

                                        <?php 
                                            if (isset($_GET['remesa']) && $_GET['remesa'] == 1) {

                                            } else {
                                            ?>
                                            <!-- Botón eliminar -->
                                            <form action="admin_cont.php?controller=index&action=view_remesa_llama&delete_trab_rem=1&id=<?php echo $remesa['id_remesa']; ?>&ano=<?php echo $remesa['ano_remesa']; ?>&remesa=<?php echo $_GET['remesa']; ?>" method="post" class="form-eliminar">
                                                <input type="hidden" name="id_remesa" value="<?php echo $remesa['id_remesa']; ?>">
                                                <input type="hidden" name="ano_remesa" value="<?php echo $remesa['ano_remesa']; ?>">
                                                <input type="hidden" name="pernr" value="<?php echo $remesa['PERNR']; ?>">
                                                <button type="button" class="icono hvr-icon btn-eliminar" style="background: none; border: none; cursor: pointer;">
                                                    <i class="bx bxs-trash fs-3" style="color: #99353a;"></i>
                                                </button>
                                            </form>

                                            <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    const botonesEliminar = document.querySelectorAll('.btn-eliminar');

                                                    botonesEliminar.forEach(function(boton) {
                                                        boton.addEventListener('click', function() {
                                                            const form = this.closest('form');
                                                            alertify.confirm(
                                                                'Confirmar eliminación',
                                                                '¿Está seguro de eliminar este trabajador de la remesa?',
                                                                function() {
                                                                    form.submit(); // ✅ Si acepta, envía el formulario
                                                                },
                                                                function() {
                                                                    // ❌ Si cancela, no hace nada
                                                                }
                                                            );
                                                        });
                                                    });
                                                });
                                            </script>

                                        <?php } ?>
                                    </div>
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

            <!-- botones de pdf y excel para exportar -->
            <h5 class="mt-4 card-title" style="color: #012970;">Exportar remesa llamamiento</h5>
            <form action='' id='exportar' method='post' style='display: inline-block; margin-left: 15px;'>
                <input type="hidden" name="id_remesa" value="<?php echo $id_remesa; ?>">
                <input type="hidden" name="ano_remesa" value="<?php echo $ano_remesa; ?>">
                <input type="hidden" name="nombre_remesa" value="<?php echo $nombre_remesa; ?>">
                
                <button type="button" target="_blank" onclick="document.getElementById('exportar').action='exportar.php?informe_trabajadores_remesa_pdf'; document.getElementById('exportar').target='_blank'; document.getElementById('exportar').submit();" style="background-color: white; margin-right: 30px;">
                    <img src="img/pdf.png" style="max-width: 100px; width: 35px;">
                </button>

                <button type="button" target="_blank" onclick="document.getElementById('exportar').action='exportar.php?informe_trabajadores_remesa_excel'; document.getElementById('exportar').submit();" style="background-color: white;">
                    <img src="img/xls.png" style="max-width: 100px; width: 35px;">
                </button>
            </form>

        </div>
    </div>
</section>

<!-- Modal para respuesta masiva de llamamientos -->
<div class="modal fade" id="modalRespuestaMasiva" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Responder llamamientos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="admin_cont.php?controller=index&action=view_remesa_llama&respuesta_masiva=1&id=<?php echo $id_remesa; ?>&ano=<?php echo $ano_remesa; ?>&remesa=<?php echo isset($_GET['remesa']) ? $_GET['remesa'] : '0'; ?>" id="formRespuestaMasiva" enctype="multipart/form-data">
                    <input type="hidden" name="id_remesa" value="<?php echo $id_remesa; ?>">
                    <input type="hidden" name="ano_remesa" value="<?php echo $ano_remesa; ?>">
                    
                    <!-- Selección de acción -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><b>Acción a realizar:</b></label>
                            <select name="accion_masiva" id="accion_masiva" class="form-select" required>
                                <option value="">Seleccione una acción</option>
                                <option value="1">Aceptar</option>
                                <option value="2">Rechazar</option>
                                <option value="3">Pendiente</option>
                            </select>
                        </div>
                        
                        <!-- Motivo (solo para Rechazar y Pendiente) -->
                        <div class="col-md-4" id="contenedor_motivo" style="display: none;">
                            <label class="form-label"><b>Motivo:</b></label>
                            <select name="motivo_masivo" id="motivo_masivo" class="form-select">
                                <option value="">Seleccione un motivo</option>
                                <?php
                                if (!empty($params['motivos_pendiente'])) {
                                    foreach ($params['motivos_pendiente'] as $motivo) {
                                        echo '<option value="' . htmlspecialchars($motivo['id_motivo']) . '">' .
                                            htmlspecialchars($motivo['desc_motivo']) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Descripción -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label"><b>Descripción (opcional):</b></label>
                            <textarea name="descripcion_masiva" id="descripcion_masiva" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <!-- Tabla de trabajadores con llamamientos contestables -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Seleccione los trabajadores:</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="seleccionar_todos">
                                <label class="form-check-label" for="seleccionar_todos">
                                    <b>Seleccionar todos</b>
                                </label>
                            </div>
                            <div style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light" style="position: sticky; top: 0; z-index: 10;">
                                        <tr>
                                            <th style="width: 5%;">
                                                <input type="checkbox" id="check_all_header" class="form-check-input">
                                            </th>
                                            <th style="width: 30%;">Trabajador</th>
                                            <th style="width: 10%;">PERNR</th>
                                            <th style="width: 15%;">Tipo Llamamiento</th>
                                            <th style="width: 15%;">Fecha Llamamiento</th>
                                            <th style="width: 15%;">Estado Actual</th>
                                            <th style="width: 10%;">Contacto</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbody_trabajadores">
                                        <?php
                                        // Obtener llamamientos contestables
                                        if (!empty($params['llamamientos_contestables'])) {
                                            foreach ($params['llamamientos_contestables'] as $llamamiento) {
                                                $estado_texto = '';
                                                if ($llamamiento['ESTADO'] == 0) {
                                                    $estado_texto = 'Enviado';
                                                } elseif ($llamamiento['ESTADO'] == 3) {
                                                    $estado_texto = 'Pendiente';
                                                }
                                                
                                                $fecha_llama = '';
                                                if ($llamamiento['TIPO_LLAMAMIENTO'] == 'Telefono' && !empty($llamamiento['FECHA_LLAMAMIENTO'])) {
                                                    $fecha_llama = date_format($llamamiento['FECHA_LLAMAMIENTO'], 'Y-m-d H:i');
                                                } elseif (!empty($llamamiento['FECHA_REGISTRO'])) {
                                                    $fecha_llama = date_format($llamamiento['FECHA_REGISTRO'], 'Y-m-d H:i');
                                                }
                                                ?>
                                                <tr>
                                                    <td class="text-center">
                                                        <input type="checkbox" name="trabajadores_seleccionados[]" 
                                                               value="<?php echo $llamamiento['ID']; ?>" 
                                                               class="form-check-input checkbox-trabajador"
                                                               data-pernr="<?php echo $llamamiento['PERNR']; ?>">
                                                    </td>
                                                    <td><?php echo $llamamiento['NOMBREYAPELLIDOS']; ?></td>
                                                    <td><?php echo $llamamiento['PERNR']; ?></td>
                                                    <td><?php echo $llamamiento['TIPO_LLAMAMIENTO']; ?></td>
                                                    <td><?php echo $fecha_llama; ?></td>
                                                    <td><?php echo $estado_texto; ?></td>
                                                    <td><?php echo $llamamiento['INFO_CONTACTO']; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="7" class="text-center">No hay llamamientos contestables en esta remesa</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarRespuestaMasiva">
                    Guardar respuestas
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mostrar/ocultar campo de motivo según la acción seleccionada
        document.getElementById('accion_masiva').addEventListener('change', function() {
            const contenedorMotivo = document.getElementById('contenedor_motivo');
            const selectMotivo = document.getElementById('motivo_masivo');
            
            if (this.value === '2' || this.value === '3') { // Rechazar o Pendiente
                contenedorMotivo.style.display = 'block';
                selectMotivo.required = true;
            } else {
                contenedorMotivo.style.display = 'none';
                selectMotivo.required = false;
                selectMotivo.value = '';
            }
        });
        
        // Seleccionar todos los checkboxes
        document.getElementById('seleccionar_todos').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.checkbox-trabajador');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
        
        // Header checkbox también selecciona todos
        document.getElementById('check_all_header').addEventListener('change', function() {
            document.getElementById('seleccionar_todos').checked = this.checked;
            document.getElementById('seleccionar_todos').dispatchEvent(new Event('change'));
        });
        
        // Actualizar estado del checkbox "seleccionar todos" cuando cambian los individuales
        document.querySelectorAll('.checkbox-trabajador').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const total = document.querySelectorAll('.checkbox-trabajador').length;
                const checked = document.querySelectorAll('.checkbox-trabajador:checked').length;
                const selectTodos = document.getElementById('seleccionar_todos');
                const checkAllHeader = document.getElementById('check_all_header');
                
                selectTodos.checked = (total === checked);
                checkAllHeader.checked = (total === checked);
            });
        });
        
        // Validar y enviar formulario
        document.getElementById('btnGuardarRespuestaMasiva').addEventListener('click', function() {
            const accion = document.getElementById('accion_masiva').value;
            const motivo = document.getElementById('motivo_masivo').value;
            const checkboxes = document.querySelectorAll('.checkbox-trabajador:checked');
            
            // Quitar resaltado previo
            document.getElementById('accion_masiva').classList.remove('is-invalid');
            document.getElementById('motivo_masivo').classList.remove('is-invalid');
            document.querySelectorAll('.checkbox-trabajador').forEach(cb => {
                cb.classList.remove('is-invalid');
            });

            let error = false;

            // Validar que se haya seleccionado una acción
            if (!accion) {
                alertify.error('Debe seleccionar una acción');
                document.getElementById('accion_masiva').classList.add('is-invalid');
                error = true;
            }

            // Validar que se haya seleccionado al menos un trabajador
            if (checkboxes.length === 0) {
                alertify.error('Debe seleccionar al menos un trabajador');
                document.querySelectorAll('.checkbox-trabajador').forEach(cb => {
                    cb.classList.add('is-invalid');
                });
                error = true;
            }

            // Validar motivo para Rechazar y Pendiente
            if ((accion === '2' || accion === '3') && !motivo) {
                alertify.error('Debe seleccionar un motivo');
                document.getElementById('motivo_masivo').classList.add('is-invalid');
                error = true;
            }

            if (error) {
                return;
            }

            // Confirmar acción
            const accionTexto = accion === '1' ? 'aceptar' : (accion === '2' ? 'rechazar' : 'poner como pendiente');
            const mensaje = `¿Está seguro de ${accionTexto} ${checkboxes.length} llamamiento(s)?`;

            alertify.confirm(
                'Confirmar respuesta masiva',
                mensaje,
                function() {
                    document.getElementById('formRespuestaMasiva').submit();
                },
                function() {
                    // Cancelar
                }
            );
        });
    });
    </script>

    <style>
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25) !important;
        }
    </style>

    <!-- redireccion de 4 segundos si encuentra en la url delete_trab_rem -->
    <?php if (isset($_GET['delete_trab_rem']) || (isset($_GET['respuesta_masiva']) && $_GET['respuesta_masiva'] == 1)) { ?>
        <script>
            setTimeout(function() {
                window.location.href = "admin_cont.php?controller=index&action=view_remesa_llama&id=<?php echo $id_remesa; ?>&ano=<?php echo $ano_remesa; ?>&remesa=<?php echo isset($_GET['remesa']) ? $_GET['remesa'] : '0'; ?>";
            }, 4000);
        </script>
    <?php } ?>
    


<?php 
include_once("footer.php");
?>