<?php 
include_once("header.php");
?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>

    <style>
        .select2-container .select2-search--inline .select2-search__field {
            margin-top: -14px;
            margin-bottom: 3px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__display {
            padding-left: 15px;
        }
    </style>

    <div class="pagetitle">
        <h1><?php echo $lang['menu2']; ?></h1>
    </div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item active"><?php echo $lang['exportar_raiz']; ?></li>
        </ol>
    </nav>
    <section class="section">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <!-- Seleccionar tipo de informe -->
                    <div class="col-md-3">
                        <h5 class="card-title"><?php echo $lang['select_inf']; ?></h5>
                        <select id="tipoInforme" name="tipoInforme" class="form-select" required>
                            <option value="" <?php if (isset($_POST['tipoInforme']) && $_POST['tipoInforme'] == '') { echo 'selected'; } ?>><?php echo $lang['select_tipo_inf']; ?></option>
                            <option value="campo" <?php if (isset($_POST['form_campo'])) { echo 'selected'; } ?>><?php echo $lang['inf_cam']; ?></option>
                            <option value="oficina" <?php if (isset($_POST['form_oficina']) OR isset($_GET['oficina'])) { echo 'selected'; } ?>><?php echo $lang['inf_ofi']; ?></option>
                            <option value="almacen" <?php if (isset($_POST['form_almacen']) OR isset($_GET['almacen'])) { echo 'selected'; } ?>><?php echo $lang['inf_alm']; ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-lg-12" id="div_campo" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title" ><?php echo $lang['inf_cam2']; ?></h5>
                        <form action="admin_cont.php?controller=index&action=exportar" method="post" id="form_export" class="row g-3">
                            <input type="hidden" name="form_campo">

                            <!-- Fechas -->
                            <div class="col-md-3">
                                <span id="name_fecha_ini" style="font-weight: bold;"><?php echo $lang['desde']; ?>: *</span>
                                <input type="date" max='<?php echo date('Y-m-d'); ?>' class="form-control" name="fecha_inicio" id="fecha_inicio_inf" <?php if (isset($_POST['fecha_inicio']) && $_POST['fecha_inicio'] != "") { echo "value='" . $_POST['fecha_inicio'] . "'"; } ?> required>
                            </div>
                            <div id="fecha_fin_informe" class="col-md-3">
                                <span style="font-weight: bold;"><?php echo $lang['hasta']; ?>:</span>
                                <input type="date" max='<?php echo date('Y-m-d'); ?>' class="form-control" name="fecha_fin" <?php if (isset($_POST['fecha_fin']) && $_POST['fecha_fin'] != "") { echo "value='" . $_POST['fecha_fin'] . "'"; } ?>>
                            </div>
                            
                            <!-- Pais -->
                            <div id="finca" class="col-md-3">
                                <span style="font-weight: bold;"><?php echo $lang['pais']; ?>: *</span>
                                <select name="sociedad" id="sociedad" class="form-select" required>
                                    <option value=""><?php echo $lang['pais']; ?></option>
                                    <option value="1000" <?php if (isset($_POST['sociedad']) && $_POST['sociedad'] == '1000') { echo 'selected'; } ?>><?php echo $lang['es']; ?></option>
                                    <!-- <option value="3001" <?php if (isset($_POST['sociedad']) && $_POST['sociedad'] == '3001') { echo 'selected'; } ?>>Marruecos</option> -->
                                </select>
                            </div>

                            <!-- Sociedad -->
                            <div id="division-div" class="col-md-3">
                                <span style="font-weight: bold;"><?php echo $lang['division']; ?>: </span>
                                <select name="division" id="division" class="form-select">
                                    <option value=""><?php echo $lang['todos']; ?></option>
                                    <?php
                                        if (isset($_POST['sociedad'])) {
                                            $sociedadSeleccionada = $_POST['sociedad'];
                                        } else {
                                            $sociedadSeleccionada = '';
                                        }

                                        if (isset($_POST['division'])) {
                                            $divisionSeleccionada = $_POST['division'];
                                        } else {
                                            $divisionSeleccionada = '';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="clear"></div>
                            <div id="fincas_informe" class="col-md-12">
                                
                            </div>		

                            <div class="clear"></div>
                            <div id="operario_centro" style="display: block;" class="col-md-12">

                            </div>
                            <div class="clear"></div>
                            <div class="clear"></div>
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <!-- Botón de Exportar -->
                                    <div id="boton-exportar-container-campo" class="col-auto">
                                        <input type="submit" name="enviar_cont" id="submit_export_campo" value="<?php echo $lang['exportar']; ?>" class="btn btn-primary">
                                    </div>

                                    <!-- Botón de Cargando Exportar -->
                                    <div id="loading-exportar-container-campo" class="col-auto" style="display: none;">
                                        <button class="btn btn-primary" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Exportando...
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>


                <?php
                    if (isset($params['datos_export'])) {
                    ?>
                        <div class="card">
                            <div class="card-body">
                                <br>
                                <?php 
                                    // Verifica si $_POST['operarios'] es un array
                                    $operarios_value = '';
                                    if (isset($_POST['operarios'])) {
                                        if (is_array($_POST['operarios'])) {
                                            $operarios_value = implode(',', $_POST['operarios']);
                                        } else {
                                            $operarios_value = $_POST['operarios'];
                                        }
                                    }

                                    if (!empty($params['datos_export'])) {
                                        if ($_POST['fecha_fin'] == '') {
                                            $fecha_fin = $_POST['fecha_inicio'];
                                        } else {
                                            $fecha_fin = $_POST['fecha_fin'];
                                        }
                                        echo "<div class='card-body'>
                                        <h5 style='color: #012970; margin-top: 10px;'>".$lang['metodo_exp']."</h5><br>
                                        <form action='' id='exportar' method='post' style='display: inline-block; margin-left: 15px;'>
                                            <input type='hidden' name='desde_informe' value='". $_POST['fecha_inicio'] ."'>
                                            <input type='hidden' name='hasta_informe' value='". $fecha_fin ."'>
                                            <input type='hidden' name='sociedad_informe' value='". $_POST['sociedad'] ."'>
                                            <input type='hidden' name='division_informe' value='". $_POST['division'] ."'>
                                            <input type='hidden' name='operario_informe' value='". $operarios_value ."'>
                                            <input type='hidden' name='fincas_informe' value='". implode(',', $_POST['fincas']) ."'>
                                            <button type='button' target='_blank' onclick=\"document.getElementById('exportar').action='exportar.php?informe_presencia_pdf'; document.getElementById('exportar').target='_blank'; document.getElementById('exportar').submit();\" style='background-color: white; margin-right: 60px;'>
                                                <img src='img/pdf.png' style='max-width: 100px; width: 50px;'>
                                            </button>
                                            <button type='button' target='_blank' onclick=\"document.getElementById('exportar').action='exportar.php?informe_presencia_excel'; document.getElementById('exportar').submit();\" style='background-color: white;'>
                                                <img src='img/xls.png' style='max-width: 100px; width: 50px;'>
                                            </button>
                                        </form><br><br>
                                    </div>";
                                    } else {
                                        
                                    }
                                ?>

                                <table class="table datatable" id="exportar_datos">
                                    <thead>
                                        <tr>
                                            <th class="col-1">Cod. Trabajdor</th>
                                            <th class="col-2"><?php echo $lang['nombre']; ?></th>
                                            <th class="col-2"><?php echo $lang['hora_ini']; ?></th>
                                            <th class="col-2"><?php echo $lang['hora_fin']; ?></th>
                                            <th class="col-1"><?php echo $lang['hora_neta']; ?></th>
                                            <th class="col-1"><?php echo $lang['descanso']; ?></th>
                                            <th class="col-1"><?php echo $lang['finca']; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($params['datos_export'] as $resultado) {
                                        ?>
                                            <tr>
                                                <td><?php echo $resultado['CodOperario']; ?></td>
                                                <td><?php echo $resultado['NombreOperario']; ?></td>
                                                <td><?php echo substr($resultado['InicioPresencia'], 0, 19); ?></td>
                                                <td><?php echo substr($resultado['FinPresencia'], 0, 19); ?></td>
                                                <td><?php echo $resultado['HorasNetas']; ?></td>
                                                <td><?php echo $resultado['MinutosDescanso']; ?></td>
                                                <td><?php echo $resultado['Finca']; ?></td>
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

            </div>

            <div class="col-lg-12" id="div_oficina" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $lang['inf_ofi2']; ?></h5>

                        <form action="admin_cont.php?controller=index&action=exportar" method="post" id="form_oficina" class="row g-3">
                            <input type="hidden" name="form_oficina">

                            <!-- fechas -->
                            <div class="col-md-2">
                                <span id="name_fecha_ini" style="font-weight: bold;"><?php echo $lang['desde']; ?>: *</span>
                                <input type="date" max='<?php echo date('Y-m-d'); ?>' class="form-control" name="fecha_inicio_ofi" id="fecha_inicio_inf" <?php if (isset($_POST['fecha_inicio_ofi']) && $_POST['fecha_inicio_ofi'] != "") { echo "value='" . $_POST['fecha_inicio_ofi'] . "'"; } ?> required>
                            </div>
                            <div id="fecha_fin_informe" class="col-md-2">
                                <span style="font-weight: bold;"><?php echo $lang['hasta']; ?>:</span>
                                <input type="date" max='<?php echo date('Y-m-d'); ?>' class="form-control" name="fecha_fin_ofi" <?php if (isset($_POST['fecha_fin_ofi']) && $_POST['fecha_fin_ofi'] != "") { echo "value='" . $_POST['fecha_fin_ofi'] . "'"; } ?>>
                            </div>

                            <!-- Tipo -->
                            <div id="tipo_registro" class="col-md-2">
                                <span style="font-weight: bold;"><?php echo $lang['tipo']; ?>: </span>
                                <select name="tipo_reg" id="tipo_reg" class="form-select">
                                    <option value="">--</option>
                                    <option value="entrada" <?php if (isset($_POST['tipo_reg']) && $_POST['tipo_reg'] == 'entrada') { echo 'selected'; } ?>><?php echo $lang['entrada']; ?></option>
                                    <option value="salida" <?php if (isset($_POST['tipo_reg']) && $_POST['tipo_reg'] == 'salida') { echo 'selected'; } ?>><?php echo $lang['salida']; ?></option>
                                    <option value="inicio-desayuno" <?php if (isset($_POST['tipo_reg']) && $_POST['tipo_reg'] == 'inicio-desayuno') { echo 'selected'; } ?>>Inicio Desayuno</option>
                                    <option value="fin-desayuno" <?php if (isset($_POST['tipo_reg']) && $_POST['tipo_reg'] == 'fin-desayuno') { echo 'selected'; } ?>>Fin Desayuno</option>
                                    <option value="inicio-almuerzo" <?php if (isset($_POST['tipo_reg']) && $_POST['tipo_reg'] == 'inicio-almuerzo') { echo 'selected'; } ?>>Inicio Almuerzo</option>
                                    <option value="fin-almuerzo" <?php if (isset($_POST['tipo_reg']) && $_POST['tipo_reg'] == 'fin-almuerzo') { echo 'selected'; } ?>>Fin Almuerzo</option>
                                    <option value="inicio-otros" <?php if (isset($_POST['tipo_reg']) && $_POST['tipo_reg'] == 'inicio-otros') { echo 'selected'; } ?>>Inicio Otros</option>
                                    <option value="fin-otros" <?php if (isset($_POST['tipo_reg']) && $_POST['tipo_reg'] == 'fin-otros') { echo 'selected'; } ?>>Fin Otros</option>

                                </select>                        
                            </div>

                            <!-- Sede -->
                            <div id="sede" class="col-md-3">
                                <span style="font-weight: bold;"><?php echo $lang['sede']; ?>: </span>
                                <select name="sede" id="nombre_sede" class="form-select">
                                    <option value="">--</option>
                                    <?php
                                        foreach ($params['sedes'] as $resultado) {
                                        ?>
                                            <option value="<?php echo $resultado['sede']; ?>" <?php if (isset($_POST['sede']) && $_POST['sede'] == $resultado['sede']) { echo 'selected'; } ?>><?php echo $resultado['sede']; ?></option>
                                        <?php
                                        }
                                    ?>
                                </select>
                            </div>

                            <!-- Ubicación -->
                            <div id="ubicacion" class="col-md-3" style="display:none;">
                                <span style="font-weight: bold;"><?php echo $lang['ubi']; ?>: </span>
                                <select name="ubicacion" id="nombre_ubi" class="form-select">
  
                                </select>
                            </div>
                            <div class="clear"></div>

                            <?php
                                $selectedValues = isset($_POST['pernr_trab']) ? (array) $_POST['pernr_trab'] : [];
                            ?>
                            <div class="col-md-5">
                                <span style="font-weight: bold;">Cod. Trabajador, Nombre: </span>
                                <select class="form-select select2" name="pernr_trab[]" id="pernr_nom_trab" multiple>
                                    <?php
                                    foreach ($params['trabajadores_auditoria'] as $trabajador) {
                                        $selected = in_array($trabajador['pernr'], $selectedValues) ? 'selected' : '';
                                        echo '<option value="' . $trabajador['pernr'] . '" ' . $selected . '>' . $trabajador['pernr'] . ' - ' . $trabajador['NOMBREYAPELLIDOS'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>


                            <div class="clear"></div>
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <!-- Botón de Exportar -->
                                    <div id="boton-exportar-container-oficina" class="col-auto">
                                        <input type="submit" name="enviar_cont" id="submit_export_oficina" value="<?php echo $lang['exportar']; ?>" class="btn btn-primary">
                                    </div>

                                    <!-- Botón de Cargando Exportar -->
                                    <div id="loading-exportar-container-oficina" class="col-auto" style="display: none;">
                                        <button class="btn btn-primary" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Exportando...
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php 
                if (isset($params['datos_export_ofi'])) {
                ?>
                    <div class="card">
                        <div class="card-body">
                            <br>
                            <?php 
                                // if (!empty($params['datos_export_ofi'])) {
                                //     if (isset($_POST['ubicacion'])) {
                                //         $ubicacion = $_POST['ubicacion'];
                                //     } else {
                                //         $ubicacion = '';
                                //     }
                                //     echo "<div class='card-body'>
                                //     <h5 style='color: #012970; margin-top: 10px;'>".$lang['metodo_exp']."</h5><br>
                                //     <form action='' id='exportar' method='post' style='display: inline-block; margin-left: 15px;'>
                                //         <input type='hidden' name='desde_informe_ofi' value='". $_POST['fecha_inicio_ofi'] ."'>
                                //         <input type='hidden' name='hasta_informe_ofi' value='". $_POST['fecha_fin_ofi'] ."'>
                                //         <input type='hidden' name='tipo_reg_informe_ofi' value='". $_POST['tipo_reg'] ."'>
                                //         <input type='hidden' name='pernr_informe_ofi' value='". $_POST['pernr_trab'] ."'>
                                //         <input type='hidden' name='reg_manual_ofi' value='". isset($_POST['reg_manual']) ."'>
                                //         <input type='hidden' name='sede_informe_ofi' value='". $_POST['sede'] ."'>
                                //         <input type='hidden' name='ubi_informe_ofi' value='". $ubicacion ."'>
                                //         <button type='button' target='_blank' onclick=\"document.getElementById('exportar').action='exportar.php?informe_presencia_ofi_pdf'; document.getElementById('exportar').target='_blank'; document.getElementById('exportar').submit();\" style='background-color: white; margin-right: 60px;'>
                                //             <img src='img/pdf.png' style='max-width: 100px; width: 50px;'>
                                //         </button>
                                //         <button type='button' target='_blank' onclick=\"document.getElementById('exportar').action='exportar.php?informe_presencia_ofi_excel'; document.getElementById('exportar').submit();\" style='background-color: white;'>
                                //             <img src='img/xls.png' style='max-width: 100px; width: 50px;'>
                                //         </button>
                                //     </form><br><br>
                                // </div>";
                                // } else {
                                    
                                // }
                            ?>
                            <table class="table datatable" id="exportar_datos">
                                <thead>
                                    <tr>
                                        <th class="col-1">Cod. Trabajador</th>
                                        <th class="col-2"><?php echo $lang['nombre']; ?></th>
                                        <th class="col-1"><?php echo $lang['fecha_reg']; ?></th>
                                        <th class="col-1"><?php echo $lang['tipo']; ?></th>
                                        <th class="col-1"><?php echo $lang['sede']; ?></th>
                                        <th class="col-1"><?php echo $lang['ubi']; ?></th>
                                        <th class="col-1"><?php echo $lang['manual']; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($params['datos_export_ofi'] as $resultado) {
                                    ?>
                                        <tr>
                                            <td><?php echo $resultado['pernr']; ?></td>
                                            <td>
                                            <?php 
                                            // Mostrar el nombre completo del trabajador
                                                if (!empty($resultado['APELLIDO1']) && !empty($resultado['NOMBRE'])) {
                                                    // Si existen APELLIDO1 y NOMBRE, mostrar en formato: APELLIDO1 APELLIDO2, NOMBRE
                                                    echo $resultado['APELLIDO1'];

                                                    if (!empty($resultado['APELLIDO2'])) {
                                                        echo ' ' . $resultado['APELLIDO2'];
                                                    }

                                                    echo ', ' . $resultado['NOMBRE'];
                                                } elseif (!empty($resultado['NOMBREYAPELLIDOS'])) {
                                                    // Si existe el campo NOMBREYAPELLIDOS completo
                                                    echo $resultado['NOMBREYAPELLIDOS'];
                                                } else {
                                                    // Si no hay datos disponibles
                                                    echo 'Desconocido';
                                                } 
                                            ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if (isset($resultado['fecha_reg'])) {
                                                        echo $resultado['fecha_reg']->format('Y-m-d H:i:s');
                                                    } else {
                                                        echo '';
                                                    }
                                                 
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if ($resultado['tipo_reg'] == 'entrada') {
                                                        echo $lang['entrada'];
                                                    } elseif ($resultado['tipo_reg'] == 'salida') {
                                                        echo $lang['salida'];
                                                    } elseif ($resultado['tipo_reg'] == 'inicio-desayuno') {
                                                        echo 'Inicio Desayuno';
                                                    } elseif ($resultado['tipo_reg'] == 'fin-desayuno') {
                                                        echo 'Fin Desayuno';
                                                    } elseif ($resultado['tipo_reg'] == 'inicio-almuerzo') {
                                                        echo 'Inicio Almuerzo';
                                                    } elseif ($resultado['tipo_reg'] == 'fin-almuerzo') {
                                                        echo 'Fin Almuerzo';
                                                    } elseif ($resultado['tipo_reg'] == 'inicio-otros') {
                                                        echo 'Incio Otros';
                                                    } elseif ($resultado['tipo_reg'] == 'fin-otros') {
                                                        echo 'Fin Otros';
                                                    } else {
                                                        echo 'Desconocido';
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo $resultado['sede_ubi']; ?></td>
                                            <td><?php echo $resultado['nombre_ubi']; ?></td>
                                            <td><?php echo $resultado['manual']; ?></td>
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
            </div>

            <div class="col-lg-12" id="div_almacen" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $lang['inf_alm1'] ?></h5>

                        <form action="admin_cont.php?controller=index&action=exportar" method="post" id="form_almacen" class="row g-3">
                            <input type="hidden" name="form_almacen">

                            <!-- fechas -->
                            <div class="col-md-2">
                                <span id="name_fecha_ini" style="font-weight: bold;"><?php echo $lang['desde']; ?>: *</span>
                                <input type="date" max='<?php echo date('Y-m-d'); ?>' class="form-control" name="fecha_inicio_alm" id="fecha_inicio_alm" <?php if (isset($_POST['fecha_inicio_alm']) && $_POST['fecha_inicio_alm'] != "") { echo "value='" . $_POST['fecha_inicio_alm'] . "'"; } ?> required>
                            </div>
                            <div id="fecha_fin_informe" class="col-md-2">
                                <span style="font-weight: bold;"><?php echo $lang['hasta']; ?>:</span>
                                <input type="date" max='<?php echo date('Y-m-d'); ?>' class="form-control" name="fecha_fin_alm" <?php if (isset($_POST['fecha_fin_alm']) && $_POST['fecha_fin_alm'] != "") { echo "value='" . $_POST['fecha_fin_alm'] . "'"; } ?>>
                            </div>
                            
                            <!-- Select de la puerta para filtar -->
                            <div id="pernr_name_trab" class="col-md-3">
                                <span style="font-weight: bold;"><?php echo $lang['inf_alm3']; ?></span>
                                <select id="puertaTesa" name="puertaTesa" class="form-select">
                                    <option value="" <?php if (isset($_POST['puertaTesa']) && $_POST['puertaTesa'] == '') { echo 'selected'; } ?>><?php echo $lang['inf_alm2']; ?></option>
                                    <?php
                                            foreach ($params['puertas'] as $resultado) {
                                            ?>
                                                <option value="<?php echo $resultado['DOORID']; ?>" <?php if (isset($_POST['puertaTesa']) && $_POST['puertaTesa'] == $resultado['DOORID']) { echo 'selected'; } ?>><?php echo $resultado['DOORNAME']; ?></option>
                                            <?php
                                            }
                                        ?>
                                </select>
                            </div>

                            <!-- Buscador nombre y apellidos y pernr -->
                            <!-- <div id="pernr_name_trab" class="col-md-4">
                                <span style="font-weight: bold;">Cod. Trabajador, Nombre:</span>
                                <input type="text" class="form-control" name="pernr_trab_alm" <?php if (isset($_POST['pernr_trab_alm']) && $_POST['pernr_trab_alm'] != "") { echo "value='" . $_POST['pernr_trab_alm'] . "'"; } ?>>
                            </div> -->
                            <?php
                                $selectedValues = isset($_POST['pernr_trab_alm']) ? (array) $_POST['pernr_trab_alm'] : [];
                            ?>
                            <div class="col-md-5">
                                <span style="font-weight: bold;">Cod. Trabajador, Nombre: </span>
                                <select class="form-select select2" name="pernr_trab_alm[]" id="pernr_trab_alm" multiple>
                                    <?php
                                    foreach ($params['trabajadores_almacen'] as $trabajador) {
                                        $selected = in_array($trabajador['EXTERNALID'], $selectedValues) ? 'selected' : '';
                                        echo '<option value="' . $trabajador['USERNAME'] . '" ' . $selected . '>' . $trabajador['EXTERNALID'] . ' - ' . $trabajador['USERNAME'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>


                            <div class="clear"></div>
                            <div class="col-md-12">
                                <div class="row align-items-center">
                                    <!-- Botón de Exportar -->
                                    <div id="boton-exportar-container-almacen" class="col-auto">
                                        <input type="submit" name="enviar_cont" id="submit_export_almacen" value="<?php echo $lang['exportar']; ?>" class="btn btn-primary">
                                    </div>

                                    <!-- Botón de Cargando Exportar -->
                                    <div id="loading-exportar-container-almacen" class="col-auto" style="display: none;">
                                        <button class="btn btn-primary" type="button" disabled>
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                            Exportando...
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php 
                    if (isset($_POST['pernr_trab_alm'])) {
                        $pernr_nombre = $_POST['pernr_trab_alm'];
                    } else {
                        $pernr_nombre = '';
                    }
                    if (isset($params['datos_export_alm'])) {
                    ?>
                        <div class="card">
                            <div class="card-body">
                                <br>
                                <?php 
                                    // if (!empty($params['datos_export_alm'])) {
                                        
                                    //     echo "<div class='card-body'>
                                    //     <h5 style='color: #012970; margin-top: 10px;'>".$lang['metodo_exp']."</h5><br>
                                    //     <form action='' id='exportar' method='post' style='display: inline-block; margin-left: 15px;'>
                                    //         <input type='hidden' name='desde_informe_alm' value='". $_POST['fecha_inicio_alm'] ."'>
                                    //         <input type='hidden' name='hasta_informe_alm' value='". $_POST['fecha_fin_alm'] ."'>
                                    //         <input type='hidden' name='pernr_informe_alm' value='". $_POST['pernr_trab_alm'] ."'>
                                    //         <input type='hidden' name='puerta_informe_alm' value='". $_POST['puertaTesa'] ."'>
                                            
                                    //         <button type='button' target='_blank' onclick=\"document.getElementById('exportar').action='exportar.php?informe_presencia_alm_pdf'; document.getElementById('exportar').target='_blank'; document.getElementById('exportar').submit();\" style='background-color: white; margin-right: 60px;'>
                                    //             <img src='img/pdf.png' style='max-width: 100px; width: 50px;'>
                                    //         </button>
                                    //         <button type='button' target='_blank' onclick=\"document.getElementById('exportar').action='exportar.php?informe_presencia_alm_excel'; document.getElementById('exportar').submit();\" style='background-color: white;'>
                                    //             <img src='img/xls.png' style='max-width: 100px; width: 50px;'>
                                    //         </button>
                                    //     </form><br><br>
                                    // </div>";
                                    // } else {
                                        
                                    // }
                                ?>
                                <table class="table datatable" id="exportar_datos">
                                    <thead>
                                        <tr>
                                            <th class="col-1">Cod. Trabajador</th>
                                            <th class="col-3"><?php echo $lang['nombre']; ?></th>
                                            <th class="col-3"><?php echo $lang['fecha_reg']; ?></th>
                                            <th class="col-2">ID - <?php echo $lang['inf_alm3']; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($params['datos_export_alm'] as $resultado) {
                                        ?>
                                            <tr>
                                                <td><?php echo $resultado['EXTERNALID']; ?></td>
                                                <td><?php echo $resultado['USERNAME']; ?></td>
                                                <td><?php echo $resultado['OPENINGDATE']->format('Y-m-d H:i:s'); ?></td>
                                                <td><?php echo $resultado['DOORID']." - ".$resultado['DOORNAME']; ?></td>
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
            </div>
        </div>



        <script>
            $(document).ready(function() {
                const tipoInformeSelect = $('#tipoInforme');
                const divCampo = $('#div_campo');
                const divOficina = $('#div_oficina');
                const divAlmacen = $('#div_almacen');

                // Función para mostrar el formulario correspondiente
                function mostrarFormulario() {
                    const valorSeleccionado = tipoInformeSelect.val(); 

                    // Mostrar u ocultar divs según el tipo de informe seleccionado
                    if (valorSeleccionado === 'campo') {
                        divCampo.show();
                        divOficina.hide();
                        divAlmacen.hide();
                    } else if (valorSeleccionado === 'oficina') {
                        divOficina.show();
                        divCampo.hide();
                        divAlmacen.hide();
                    } else if (valorSeleccionado === 'almacen') {
                        divAlmacen.show();
                        divCampo.hide();
                        divOficina.hide();
                    } else {
                        divAlmacen.hide();
                        divCampo.hide();
                        divOficina.hide();
                    }
                }

                // Inicializar visibilidad de formularios al cargar la página
                mostrarFormulario();

                // Evento change para actualizar la visibilidad
                tipoInformeSelect.change(mostrarFormulario);
            


                // Función para actualizar la visibilidad del div de "Ubicación"
                function actualizarUbicacion() {
                    var sede = $('#nombre_sede').val(); // Obtener el valor seleccionado de sede
                    
                    if (sede === "") {
                        $('#ubicacion').hide();
                    } else {
                        $('#ubicacion').show();
                    }
                }

                // Manejador de eventos para el cambio en el select de "Sede"
                $('#nombre_sede').change(function() {
                    actualizarUbicacion(); // Llamar a la función cada vez que cambie el valor de sede
                });

                // Llamar a la función al cargar la página, si ya hay una sede seleccionada
                actualizarUbicacion();
            });


            

            document.addEventListener('DOMContentLoaded', function() {
                const sociedadSelect = document.getElementById('sociedad');
                const divisionSelect = document.getElementById('division');

                const opcionesDivision = {
                    '1000': [
                        { value: '1000', text: 'Huelva' },
                        { value: '1001', text: 'Avila' },
                        { value: '1002', text: 'Lugo' },
                        { value: '1003', text: 'Segovia' }
                    ],
                    '3001': [
                        { value: '3000', text: 'Moulay' },
                        { value: '3001', text: 'Agadir' }
                    ]
                };

                function actualizarDivisiones() {
                    const sociedadValue = sociedadSelect.value;

                    // Limpiar las opciones del select de división
                    divisionSelect.innerHTML = '<option value=""><?php echo $lang['todos']; ?></option>';

                    // Si se ha seleccionado un valor válido en el select de sociedad
                    if (opcionesDivision[sociedadValue]) {
                        opcionesDivision[sociedadValue].forEach(optionData => {
                            const option = document.createElement('option');
                            option.value = optionData.value;
                            option.textContent = optionData.text;
                            divisionSelect.appendChild(option);
                        });

                        // Establecer el valor seleccionado previamente
                        if (divisionSeleccionada) {
                            divisionSelect.value = divisionSeleccionada;
                        }
                    }
                }

                // Variables PHP
                const sociedadSeleccionada = "<?php echo $sociedadSeleccionada; ?>";
                const divisionSeleccionada = "<?php echo $divisionSeleccionada; ?>";

                // Establecer el valor seleccionado previamente en el select de sociedad
                sociedadSelect.value = sociedadSeleccionada;

                sociedadSelect.addEventListener('change', actualizarDivisiones);

                // Disparar el evento change para actualizar las opciones de división si ya hay un valor seleccionado
                if (sociedadSelect.value) {
                    actualizarDivisiones();
                }

            });

            // Script para checkbox marcar todos 
            document.getElementById('form_export').addEventListener('submit', function(event) {
                var checkboxes = document.querySelectorAll('input[name="fincas[]"]');
                var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
                if (!checkedOne) {
                    event.preventDefault(); 
                    alert('<?php echo $lang['alerta_fincas']; ?>');
                }
            });

            function checkAll(source) {
                var checkboxes = document.querySelectorAll('input[name="fincas[]"]');
                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].checked = source.checked;
                }
            }


            document.addEventListener('DOMContentLoaded', function() {
                // Arreglo con la configuración de los formularios
                const formularios = [
                    { formId: 'form_oficina', submitId: 'submit_export_oficina', botonContainerId: 'boton-exportar-container-oficina', loadingContainerId: 'loading-exportar-container-oficina' },
                    { formId: 'form_almacen', submitId: 'submit_export_almacen', botonContainerId: 'boton-exportar-container-almacen', loadingContainerId: 'loading-exportar-container-almacen' },
                    { formId: 'form_export', submitId: 'submit_export_campo', botonContainerId: 'boton-exportar-container-campo', loadingContainerId: 'loading-exportar-container-campo' }
                ];

                formularios.forEach(({ formId, submitId, botonContainerId, loadingContainerId }) => {
                    const form = document.getElementById(formId);
                    const submitButton = document.getElementById(submitId);
                    const botonContainer = document.getElementById(botonContainerId);
                    const loadingContainer = document.getElementById(loadingContainerId);

                    if (form) {
                        form.addEventListener('submit', function() {
                            submitButton.disabled = true;
                            botonContainer.style.display = 'none';
                            loadingContainer.style.display = 'block';
                        });
                    }
                });
            });

        </script>



    </section>


    <!-- Vendor JS Files -->
        <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
        <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="assets/vendor/chart.js/chart.umd.js"></script>
        <script src="assets/vendor/echarts/echarts.min.js"></script>
        <script src="assets/vendor/quill/quill.js"></script>
        <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
        <script src="assets/vendor/tinymce/tinymce.min.js"></script>
        <script src="assets/vendor/php-email-form/validate.js"></script>

        <!-- Template Main JS File -->
        <script src="assets/js/main.js"></script>

        <!-- Mis JS -->
        <script type="text/javascript" src="js/jquery.lightbox_me.js"></script>
        <script type="text/javascript" src="js/jquery.magnific-popup.min.js"></script>
        <script type="text/javascript" src="js/script.js?ver=1.6"></script>
        <script src="js/alertify.min.js"></script>
    <!-- -->
    

</body>

</html>