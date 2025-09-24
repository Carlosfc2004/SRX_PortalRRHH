<?php
// var_dump($params['trabajadores_auditoria']);
// var_dump($params['datos_export_ofi']);
// die;
include_once("header.php");
?>

<style>
    .sticky-table {
        width: 100%;
        border-collapse: collapse;
        position: relative;
    }

    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .sticky-header th {
        padding: 12px;
        border-bottom: 2px solid #ddd;
    }

    .table-container {
        max-height: 585px;
        overflow-y: auto;
    }
</style>

<div class="pagetitle">
    <h1>Registro presencia</h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home">
                <i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active">Presencia</li>
    </ol>
</nav>

<section class="section">
    <div class='row'>
        <div class="col-lg-9" id="div_oficina">
            <!-- FILTROS -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Validación Registros Oficina</h5>

                    <form action="admin_cont.php?controller=index&action=auditor" method="post" id="form_export"
                        class="row g-3">
                        <input type="hidden" name="form_oficina">

                        <!-- fechas -->
                        <div class="col-md-3">
                            <span id="name_fecha_ini" style="font-weight: bold;">Desde: *</span>
                            <input type="date" max='<?php echo date('Y-m-d'); ?>' class="form-control"
                                name="fecha_inicio_ofi" id="fecha_inicio_inf" <?php if (isset($_POST['fecha_inicio_ofi']) && $_POST['fecha_inicio_ofi'] != "") {
                                    echo "value='" . $_POST['fecha_inicio_ofi'] . "'";
                                } ?> required>
                        </div>
                        <div id="fecha_fin_informe" class="col-md-3">
                            <span style="font-weight: bold;">Hasta:</span>
                            <input type="date" max='<?php echo date('Y-m-d'); ?>' class="form-control"
                                name="fecha_fin_ofi" <?php if (isset($_POST['fecha_fin_ofi']) && $_POST['fecha_fin_ofi'] != "") {
                                    echo "value='" . $_POST['fecha_fin_ofi'] . "'";
                                } ?> required>
                        </div>

                        <?php
                        $selectedValues = isset($_POST['pernr_nom_trab']) ? (array) $_POST['pernr_nom_trab'] : [];
                        ?>
                        <div class="col-md-6">
                            <span style="font-weight: bold;">Cod. Trabajador, Nombre: </span>
                            <select class="form-select select2" name="pernr_nom_trab[]" id="pernr_nom_trab" multiple>
                                <?php
                                foreach ($params['trabajadores_auditoria'] as $trabajador) {
                                    $selected = in_array($trabajador['pernr'], $selectedValues) ? 'selected' : '';
                                    echo '<option value="' . $trabajador['pernr'] . '" ' . $selected . '>' . $trabajador['pernr'] . ' - ' . $trabajador['NOMBREYAPELLIDOS'] . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="clear"></div>

                        <!-- select Filtros: Horas totales, Horas individuales -->
                        <div class="col-md-3">
                            <span style="font-weight: bold;">Modo de cálculo: </span>
                            <select class="form-select" name="filtro_horas" id="filtro_horas">
                                <option value="" <?php if (isset($_POST['filtro_horas']) && $_POST['filtro_horas'] == "") {
                                    echo "selected";
                                } ?>></option>
                                <option value="totales" <?php if (isset($_POST['filtro_horas']) && $_POST['filtro_horas'] == "totales") {
                                    echo "selected";
                                } ?>>Horas Totales</option>
                                <option value="individuales" <?php if (isset($_POST['filtro_horas']) && $_POST['filtro_horas'] == "individuales") {
                                    echo "selected";
                                } ?>>Horas Individuales
                                </option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <!-- Estado -->
                            <span style="font-weight: bold;">Estado: </span>
                            <select class="form-select" name="estado_ofi" id="estado_ofi">
                                <option value="" <?php if (isset($_POST['estado_ofi']) && $_POST['estado_ofi'] == "") {
                                    echo "selected";
                                } ?>></option>
                                <option value="2" <?php if (isset($_POST['estado_ofi']) && $_POST['estado_ofi'] == "2") {
                                    echo "selected";
                                } ?>>Por validar</option>
                                <option value="3" <?php if (isset($_POST['estado_ofi']) && $_POST['estado_ofi'] == "3") {
                                    echo "selected";
                                } ?>>Validado</option>
                                <!-- ambos -->
                                <!-- <option value="6" <?php if (isset($_POST['estado_ofi']) && $_POST['estado_ofi'] == "6") {
                                    echo "selected";
                                } ?>>Ambos</option> -->
                            </select>
                        </div>


                        <!-- Filtros condiciones tiempo -->
                        <div class="clear"></div>



<!-- 1º filtro -->
<div class="row filter mb-3" id="filtro_1">
    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Horario:</span><br>
        <select class="form-select mt-1" name="campo1_1" id="campo1_1">
            <option value="0" <?php if (isset($_POST['campo1_1']) && $_POST['campo1_1'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="1" <?php if (isset($_POST['campo1_1']) && $_POST['campo1_1'] == "1") {
                echo "selected";
            } ?>>Desayuno</option>
            <option value="2" <?php if (isset($_POST['campo1_1']) && $_POST['campo1_1'] == "2") {
                echo "selected";
            } ?>>Almuerzo</option>
            <option value="3" <?php if (isset($_POST['campo1_1']) && $_POST['campo1_1'] == "3") {
                echo "selected";
            } ?>>Otros</option>
            <option value="4" <?php if (isset($_POST['campo1_1']) && $_POST['campo1_1'] == "4") {
                echo "selected";
            } ?>>Descanso</option>
            <option value="5" <?php if (isset($_POST['campo1_1']) && $_POST['campo1_1'] == "5") {
                echo "selected";
            } ?>>Tiempo Efectivo</option>
            <option value="6" <?php if (isset($_POST['campo1_1']) && $_POST['campo1_1'] == "6") {
                echo "selected";
            } ?>>Horas Totales</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Condición:</span><br>
        <select class="form-select mt-1" name="campo2_1" id="campo2_1">
            <option value="0" <?php if (isset($_POST['campo2_1']) && $_POST['campo2_1'] == "0") {
                echo "selected";
            } ?>></option>
            <option value=">" <?php if (isset($_POST['campo2_1']) && $_POST['campo2_1'] == ">") {
                echo "selected";
            } ?>>Mayor</option>
            <option value="=" <?php if (isset($_POST['campo2_1']) && $_POST['campo2_1'] == "=") {
                echo "selected";
            } ?>>Igual</option>
            <option value="<" <?php if (isset($_POST['campo2_1']) && $_POST['campo2_1'] == "<") {
                echo "selected";
            } ?>>Menor</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Tiempo:</span><br>
        <input type="text" class="form-control mt-1" name="campo3_1" id="campo3_1"
            placeholder="hh:mm" maxlength="5" <?php if (isset($_POST['campo3_1']) && $_POST['campo3_1'] != "") {
                echo "value='" . $_POST['campo3_1'] . "'";
            } ?>>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Conector:</span><br>
        <select class="form-select mt-1" name="conector_1" id="conector_1"
            onchange="mostrarSiguienteFiltro(1)">
            <option value="0" <?php if (isset($_POST['conector_1']) && $_POST['conector_1'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="AND" <?php if (isset($_POST['conector_1']) && $_POST['conector_1'] == "AND") {
                echo "selected";
            } ?>>Y</option>
            <option value="OR" <?php if (isset($_POST['conector_1']) && $_POST['conector_1'] == "OR") {
                echo "selected";
            } ?>>O</option>
        </select>
    </div>
</div>


<!-- 2º filtro -->
<div class="row filter mb-2" id="filtro_2" style="display: none;">
    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Horario:</span><br>
        <select class="form-select mt-1" name="campo1_2" id="campo1_2">
            <option value="" <?php if (isset($_POST['campo1_2']) && $_POST['campo1_2'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="1" <?php if (isset($_POST['campo1_2']) && $_POST['campo1_2'] == "1") {
                echo "selected";
            } ?>>Desayuno</option>
            <option value="2" <?php if (isset($_POST['campo1_2']) && $_POST['campo1_2'] == "2") {
                echo "selected";
            } ?>>Almuerzo</option>
            <option value="3" <?php if (isset($_POST['campo1_2']) && $_POST['campo1_2'] == "3") {
                echo "selected";
            } ?>>Otros</option>
            <option value="4" <?php if (isset($_POST['campo1_2']) && $_POST['campo1_2'] == "4") {
                echo "selected";
            } ?>>Descanso</option>
            <option value="5" <?php if (isset($_POST['campo1_2']) && $_POST['campo1_2'] == "5") {
                echo "selected";
            } ?>>Tiempo Efectivo</option>
            <option value="6" <?php if (isset($_POST['campo1_2']) && $_POST['campo1_2'] == "6") {
                echo "selected";
            } ?>>Horas Totales</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Condición:</span><br>
        <select class="form-select mt-1" name="campo2_2" id="campo2_2">
            <option value="0" <?php if (isset($_POST['campo2_2']) && $_POST['campo2_2'] == "0") {
                echo "selected";
            } ?>></option>
            <option value=">" <?php if (isset($_POST['campo2_2']) && $_POST['campo2_2'] == ">") {
                echo "selected";
            } ?>>Mayor</option>
            <option value="=" <?php if (isset($_POST['campo2_2']) && $_POST['campo2_2'] == "=") {
                echo "selected";
            } ?>>Igual</option>
            <option value="<" <?php if (isset($_POST['campo2_2']) && $_POST['campo2_2'] == "<") {
                echo "selected";
            } ?>>Menor</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Tiempo:</span><br>
        <input type="text" class="form-control mt-1" name="campo3_2" id="campo3_2"
            placeholder="hh:mm" maxlength="5"
            value="<?php echo isset($_POST['campo3_2']) ? $_POST['campo3_2'] : ''; ?>">
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Conector:</span><br>
        <select class="form-select mt-1" name="conector_2" id="conector_2"
            onchange="mostrarSiguienteFiltro(2)">
            <option value="0" <?php if (isset($_POST['conector_2']) && $_POST['conector_2'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="AND" <?php if (isset($_POST['conector_2']) && $_POST['conector_2'] == "AND") {
                echo "selected";
            } ?>>Y</option>
            <option value="OR" <?php if (isset($_POST['conector_2']) && $_POST['conector_2'] == "OR") {
                echo "selected";
            } ?>>O</option>
        </select>
    </div>
</div>


<!-- 3º filtro -->
<div class="row filter mb-2" id="filtro_3" style="display: none;">
    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Horario:</span><br>
        <select class="form-select mt-1" name="campo1_3" id="campo1_3">
            <option value="0" <?php if (isset($_POST['campo1_3']) && $_POST['campo1_3'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="1" <?php if (isset($_POST['campo1_3']) && $_POST['campo1_3'] == "1") {
                echo "selected";
            } ?>>Desayuno</option>
            <option value="2" <?php if (isset($_POST['campo1_3']) && $_POST['campo1_3'] == "2") {
                echo "selected";
            } ?>>Almuerzo</option>
            <option value="3" <?php if (isset($_POST['campo1_3']) && $_POST['campo1_3'] == "3") {
                echo "selected";
            } ?>>Otros</option>
            <option value="4" <?php if (isset($_POST['campo1_3']) && $_POST['campo1_3'] == "4") {
                echo "selected";
            } ?>>Descanso</option>
            <option value="5" <?php if (isset($_POST['campo1_3']) && $_POST['campo1_3'] == "5") {
                echo "selected";
            } ?>>Tiempo Efectivo</option>
            <option value="6" <?php if (isset($_POST['campo1_3']) && $_POST['campo1_3'] == "6") {
                echo "selected";
            } ?>>Horas Totales</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Condición:</span><br>
        <select class="form-select mt-1" name="campo2_3" id="campo2_3">
            <option value="0" <?php if (isset($_POST['campo2_3']) && $_POST['campo2_3'] == "0") {
                echo "selected";
            } ?>></option>
            <option value=">" <?php if (isset($_POST['campo2_3']) && $_POST['campo2_3'] == ">") {
                echo "selected";
            } ?>>Mayor</option>
            <option value="=" <?php if (isset($_POST['campo2_3']) && $_POST['campo2_3'] == "=") {
                echo "selected";
            } ?>>Igual</option>
            <option value="<" <?php if (isset($_POST['campo2_3']) && $_POST['campo2_3'] == "<") {
                echo "selected";
            } ?>>Menor</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Tiempo:</span><br>
        <input type="text" class="form-control mt-1" name="campo3_3" id="campo3_3"
            placeholder="hh:mm" maxlength="5"
            value="<?php echo isset($_POST['campo3_3']) ? $_POST['campo3_3'] : ''; ?>">
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Conector:</span><br>
        <select class="form-select mt-1" name="conector_3" id="conector_3"
            onchange="mostrarSiguienteFiltro(3)">
            <option value="0" <?php if (isset($_POST['conector_3']) && $_POST['conector_3'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="AND" <?php if (isset($_POST['conector_3']) && $_POST['conector_3'] == "AND") {
                echo "selected";
            } ?>>Y</option>
            <option value="OR" <?php if (isset($_POST['conector_3']) && $_POST['conector_3'] == "OR") {
                echo "selected";
            } ?>>O</option>
        </select>
    </div>
</div>


<!-- 4º filtro -->
<div class="row filter mb-2" id="filtro_4" style="display: none;">
    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Horario:</span><br>
        <select class="form-select mt-1" name="campo1_4" id="campo1_4">
            <option value="0" <?php if (isset($_POST['campo1_4']) && $_POST['campo1_4'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="1" <?php if (isset($_POST['campo1_4']) && $_POST['campo1_4'] == "1") {
                echo "selected";
            } ?>>Desayuno</option>
            <option value="2" <?php if (isset($_POST['campo1_4']) && $_POST['campo1_4'] == "2") {
                echo "selected";
            } ?>>Almuerzo</option>
            <option value="4" <?php if (isset($_POST['campo1_4']) && $_POST['campo1_4'] == "3") {
                echo "selected";
            } ?>>Otros</option>
            <option value="4" <?php if (isset($_POST['campo1_4']) && $_POST['campo1_4'] == "4") {
                echo "selected";
            } ?>>Descanso</option>
            <option value="5" <?php if (isset($_POST['campo1_4']) && $_POST['campo1_4'] == "5") {
                echo "selected";
            } ?>>Tiempo Efectivo</option>
            <option value="6" <?php if (isset($_POST['campo1_4']) && $_POST['campo1_4'] == "6") {
                echo "selected";
            } ?>>Horas Totales</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Condición:</span><br>
        <select class="form-select mt-1" name="campo2_4" id="campo2_4">
            <option value="0" <?php if (isset($_POST['campo2_4']) && $_POST['campo2_4'] == "0") {
                echo "selected";
            } ?>></option>
            <option value=">" <?php if (isset($_POST['campo2_4']) && $_POST['campo2_4'] == ">") {
                echo "selected";
            } ?>>Mayor</option>
            <option value="=" <?php if (isset($_POST['campo2_4']) && $_POST['campo2_4'] == "=") {
                echo "selected";
            } ?>>Igual</option>
            <option value="<" <?php if (isset($_POST['campo2_4']) && $_POST['campo2_4'] == "<") {
                echo "selected";
            } ?>>Menor</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Tiempo:</span><br>
        <input type="text" class="form-control mt-1" name="campo3_4" id="campo3_4"
            placeholder="hh:mm" maxlength="5"
            value="<?php echo isset($_POST['campo3_4']) ? $_POST['campo3_4'] : ''; ?>">
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Conector:</span><br>
        <select class="form-select mt-1" name="conector_4" id="conector_4"
            onchange="mostrarSiguienteFiltro(4)">
            <option value="0" <?php if (isset($_POST['conector_4']) && $_POST['conector_4'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="AND" <?php if (isset($_POST['conector_4']) && $_POST['conector_4'] == "AND") {
                echo "selected";
            } ?>>Y</option>
            <option value="OR" <?php if (isset($_POST['conector_4']) && $_POST['conector_4'] == "OR") {
                echo "selected";
            } ?>>O</option>
        </select>
    </div>
</div>


<!-- 5º filtro -->
<div class="row filter mb-2" id="filtro_5" style="display: none;">
    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Horario:</span><br>
        <select class="form-select mt-1" name="campo1_5" id="campo1_5">
            <option value="0" <?php if (isset($_POST['campo1_5']) && $_POST['campo1_5'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="1" <?php if (isset($_POST['campo1_5']) && $_POST['campo1_5'] == "1") {
                echo "selected";
            } ?>>Desayuno</option>
            <option value="2" <?php if (isset($_POST['campo1_5']) && $_POST['campo1_5'] == "2") {
                echo "selected";
            } ?>>Almuerzo</option>
            <option value="4" <?php if (isset($_POST['campo1_5']) && $_POST['campo1_5'] == "3") {
                echo "selected";
            } ?>>Otros</option>
            <option value="4" <?php if (isset($_POST['campo1_5']) && $_POST['campo1_5'] == "4") {
                echo "selected";
            } ?>>Descanso</option>
            <option value="5" <?php if (isset($_POST['campo1_5']) && $_POST['campo1_5'] == "5") {
                echo "selected";
            } ?>>Tiempo Efectivo</option>
            <option value="6" <?php if (isset($_POST['campo1_5']) && $_POST['campo1_5'] == "6") {
                echo "selected";
            } ?>>Horas Totales</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Condición:</span><br>
        <select class="form-select mt-1" name="campo2_5" id="campo2_5">
            <option value="0" <?php if (isset($_POST['campo2_5']) && $_POST['campo2_5'] == "0") {
                echo "selected";
            } ?>></option>
            <option value=">" <?php if (isset($_POST['campo2_5']) && $_POST['campo2_5'] == ">") {
                echo "selected";
            } ?>>Mayor</option>
            <option value="=" <?php if (isset($_POST['campo2_5']) && $_POST['campo2_5'] == "=") {
                echo "selected";
            } ?>>Igual</option>
            <option value="<" <?php if (isset($_POST['campo2_5']) && $_POST['campo2_5'] == "<") {
                echo "selected";
            } ?>>Menor</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Tiempo:</span><br>
        <input type="text" class="form-control mt-1" name="campo3_5" id="campo3_5"
            placeholder="hh:mm" maxlength="5"
            value="<?php echo isset($_POST['campo3_5']) ? $_POST['campo3_5'] : ''; ?>">
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Conector:</span><br>
        <select class="form-select mt-1" name="conector_5" id="conector_5"
            onchange="mostrarSiguienteFiltro(5)">
            <option value="0" <?php if (isset($_POST['conector_5']) && $_POST['conector_5'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="AND" <?php if (isset($_POST['conector_5']) && $_POST['conector_5'] == "AND") {
                echo "selected";
            } ?>>Y</option>
            <option value="OR" <?php if (isset($_POST['conector_5']) && $_POST['conector_5'] == "OR") {
                echo "selected";
            } ?>>O</option>
        </select>
    </div>
</div>


<!-- 6º filtro -->
<div class="row filter mb-2" id="filtro_6" style="display: none;">
    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Horario:</span><br>
        <select class="form-select mt-1" name="campo1_6" id="campo1_6">
            <option value="0" <?php if (isset($_POST['campo1_6']) && $_POST['campo1_6'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="1" <?php if (isset($_POST['campo1_6']) && $_POST['campo1_6'] == "1") {
                echo "selected";
            } ?>>Desayuno</option>
            <option value="2" <?php if (isset($_POST['campo1_6']) && $_POST['campo1_6'] == "2") {
                echo "selected";
            } ?>>Almuerzo</option>
            <option value="4" <?php if (isset($_POST['campo1_6']) && $_POST['campo1_6'] == "3") {
                echo "selected";
            } ?>>Otros</option>
            <option value="4" <?php if (isset($_POST['campo1_6']) && $_POST['campo1_6'] == "4") {
                echo "selected";
            } ?>>Descanso</option>
            <option value="5" <?php if (isset($_POST['campo1_6']) && $_POST['campo1_6'] == "5") {
                echo "selected";
            } ?>>Tiempo Efectivo</option>
            <option value="6" <?php if (isset($_POST['campo1_6']) && $_POST['campo1_6'] == "6") {
                echo "selected";
            } ?>>Horas Totales</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Condición:</span><br>
        <select class="form-select mt-1" name="campo2_6" id="campo2_6">
            <option value="0" <?php if (isset($_POST['campo2_6']) && $_POST['campo2_6'] == "0") {
                echo "selected";
            } ?>></option>
            <option value=">" <?php if (isset($_POST['campo2_6']) && $_POST['campo2_6'] == ">") {
                echo "selected";
            } ?>>Mayor</option>
            <option value="=" <?php if (isset($_POST['campo2_6']) && $_POST['campo2_6'] == "=") {
                echo "selected";
            } ?>>Igual</option>
            <option value="<" <?php if (isset($_POST['campo2_6']) && $_POST['campo2_6'] == "<") {
                echo "selected";
            } ?>>Menor</option>
        </select>
    </div>

    <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Tiempo:</span><br>
        <input type="text" class="form-control mt-1" name="campo3_6" id="campo3_6"
            placeholder="hh:mm" maxlength="5"
            value="<?php echo isset($_POST['campo3_6']) ? $_POST['campo3_6'] : ''; ?>">
    </div>

    <!-- <div class="col-md-3 mt-2">
        <span style="font-weight: bold;">Conector:</span><br>
        <select class="form-select mt-1" name="conector_6" id="conector_6"
            onchange="mostrarSiguienteFiltro(6)">
            <option value="0" <?php if (isset($_POST['conector_6']) && $_POST['conector_6'] == "0") {
                echo "selected";
            } ?>></option>
            <option value="AND" <?php if (isset($_POST['conector_6']) && $_POST['conector_6'] == "AND") {
                echo "selected";
            } ?>>Y</option>
            <option value="OR" <?php if (isset($_POST['conector_6']) && $_POST['conector_6'] == "OR") {
                echo "selected";
            } ?>>O</option>
        </select>
    </div> -->
</div>


                        <div class="clear"></div>


                        <div class="col-md-12">
                            <input type="submit" name="enviar_cont" id="submit_export"
                                value="<?php echo $lang['exportar']; ?>" class="btn btn-primary">
                        </div>

                    </form>

                    <button id="btnExportando" class="btn btn-primary" type="button" disabled="" style="display: none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Por favor, espere...
                    </button>

                    <script>
                        // Mostrar el spinner y ocultar el botón "Exportar" al enviar el formulario
                        document.addEventListener("DOMContentLoaded", function () {
                            let form = document.getElementById("form_export");
                            let submitExport = document.getElementById("submit_export");
                            let btnExportando = document.getElementById("btnExportando");

                            form.addEventListener("submit", function (event) {
                                event.preventDefault(); // Evitar recargar la página

                                submitExport.style.display = "none"; // Ocultar el botón "Exportar"
                                btnExportando.style.display = "inline-block"; // Mostrar el botón con el spinner

                                // Enviar el formulario después de mostrar el spinner
                                form.submit();
                            });
                        });

                        // Función para mostrar el siguiente filtro si el actual está completo
                        function mostrarSiguienteFiltro(n) {
                            // Obtener los valores del filtro actual
                            const horario = document.getElementById(`campo1_${n}`).value;
                            const accion = document.getElementById(`campo2_${n}`).value;
                            const tiempo = document.getElementById(`campo3_${n}`).value;
                            const conector = document.getElementById(`conector_${n}`).value;

                            // Verificar si todos los campos del filtro actual están llenos
                            if (horario !== "0" && accion !== "0" && tiempo.trim() !== "" && (conector === "AND" || conector === "OR")) {
                                // Mostrar el siguiente filtro si todos los campos están completos
                                const siguienteFiltro = document.getElementById(`filtro_${n + 1}`);
                                if (siguienteFiltro) {
                                    siguienteFiltro.style.display = "flex"; // Mostrar el filtro
                                }
                            } else {
                                // Ocultar el siguiente filtro si no están completos
                                const siguienteFiltro = document.getElementById(`filtro_${n + 1}`);
                                if (siguienteFiltro) {
                                    siguienteFiltro.style.display = "none"; // Ocultar el filtro

                                    // Vaciar los campos del siguiente filtro cuando se oculta
                                    document.getElementById(`campo1_${n + 1}`).value = "0";
                                    document.getElementById(`campo2_${n + 1}`).value = "0";
                                    document.getElementById(`campo3_${n + 1}`).value = "";
                                    document.getElementById(`conector_${n + 1}`).value = "0";
                                }
                            }
                        }

                        // Función que se ejecuta al cargar la página
                        window.onload = function () {
                            // Iterar sobre los conectores de 1 a 6
                            for (let i = 1; i <= 6; i++) {
                                const conectorInput = document.getElementById(`conector_${i}`);
                                
                                // ⚠️ Verificar si el elemento existe antes de acceder a su valor
                                if (conectorInput && conectorInput.value !== "0") {
                                    mostrarSiguienteFiltro(i);
                                }
                            }
                        }

                        // Función para validar un filtro específico
                        function validarFiltro(numero) {
                            const horario = document.getElementById(`campo1_${numero}`).value;
                            const condicion = document.getElementById(`campo2_${numero}`).value;
                            const tiempo = document.getElementById(`campo3_${numero}`).value;

                            return horario !== "0" && condicion !== "0" && tiempo.trim() !== "";
                        }

                        // Función principal de validación del formulario
                        function validarFormulario(event) {
                            let formularioValido = true;
                            let mensaje = "";

                            // Validar cada filtro que tenga un conector seleccionado
                            for (let i = 1; i <= 5; i++) {
                                const conector = document.getElementById(`conector_${i}`).value;

                                // Si hay un conector seleccionado (AND u OR)
                                if (conector !== "0") {
                                    // Verificar si el siguiente filtro está completo
                                    if (!validarFiltro(i + 1)) {
                                        formularioValido = false;
                                        mensaje = `Si selecciona un conector en el filtro ${i}, debe completar todos los campos del filtro ${i + 1} o eliminar el conector.`;
                                        break;
                                    }
                                }
                            }

                            // Si el formulario no es válido, mostrar alerta y prevenir envío 
                            if (!formularioValido) {
                                alertify.alert('Error', mensaje);
                                event.preventDefault();
                                return false;
                            }

                            return true;
                        }

                        // Función para validar el formato de tiempo (hh:mm)
                        function validarFormatoTiempo(input) {
                            const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;

                            // Si el formato no es válido y el campo no está vacío
                            if (!timeRegex.test(input.value) && input.value !== '') {
                                alertify.alert(
                                    'Error de Formato', // Título del mensaje
                                    'El formato de tiempo ingresado no es válido. Por favor, ingrese el tiempo en formato hh:mm (ejemplo: 08:30)', // Mensaje de alerta
                                    function () {
                                        input.value = '';  // Limpiar el campo si el usuario confirma
                                        input.focus();  // Volver a enfocar el campo para que el usuario ingrese el tiempo correcto
                                    }
                                );
                            }
                        }

                        // Configuración de Alertify
                        function configurarAlertify() {
                            // Configurar el tiempo de duración de las notificaciones
                            alertify.set('notifier', 'position', 'top-right');
                            alertify.set('notifier', 'delay', 3);
                        }

                        // Agregar validación de formato de tiempo a todos los campos de tiempo
                        function inicializarValidaciones() {
                            for (let i = 1; i <= 6; i++) {
                                const campoTiempo = document.getElementById(`campo3_${i}`);
                                if (campoTiempo) {
                                    campoTiempo.addEventListener('blur', function () {
                                        validarFormatoTiempo(this);
                                    });
                                }
                            }
                        }

                        // Inicializar cuando el documento esté listo
                        document.addEventListener('DOMContentLoaded', function () {
                            // Configurar Alertify
                            configurarAlertify();

                            // Agregar validación al formulario
                            const form = document.querySelector('form');
                            if (form) {
                                form.addEventListener('submit', validarFormulario);
                            }

                            // Inicializar validaciones de formato de tiempo
                            inicializarValidaciones();
                        });
                    </script>
                </div>
            </div>
        </div>


        <!-- Detalles de presencia -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Detalles de presencia
                        <?php echo (isset($_POST['fecha_inicio_ofi']) ? ($_POST['fecha_inicio_ofi']) : date('Y-m-d')); ?>
                    </h5>
                    <!-- Formulario 1A -->
                    <form id="form_trab_1A" class="no-blank" target="_blank">
                        <input type="hidden" name="fecha_inicio"
                            value="<?php echo (isset($_POST['fecha_inicio_ofi']) ? ($_POST['fecha_inicio_ofi']) : date('Y-m-d')); ?>">
                        <input type="hidden" name="tipo" value="1A">
                        <button type="submit" class="btn btn-secondary m-2" id="btn_trab_1A">
                            Cargar 1A
                        </button> 1A (Personal mensual)
                    </form>

                    <div id="resultado_1A" class="mt-2"></div>

                    <!-- Formulario 1E -->
                    <form id="form_trab_1E" class="no-blank" target="_blank">
                        <input type="hidden" name="fecha_inicio"
                            value="<?php echo (isset($_POST['fecha_inicio_ofi']) ? ($_POST['fecha_inicio_ofi']) : date('Y-m-d')); ?>">
                        <input type="hidden" name="tipo" value="1E">
                        <button type="submit" class="btn btn-secondary m-2" id="btn_trab_1E">
                            Cargar 1E
                        </button> 1E (Prácticas)
                    </form>

                    <div id="resultado_1E" class="mt-2"></div>

                    <!-- Formulario 9A -->
                    <form id="form_trab_9A" class="no-blank" target="_blank">
                        <input type="hidden" name="fecha_inicio"
                            value="<?php echo (isset($_POST['fecha_inicio_ofi']) ? ($_POST['fecha_inicio_ofi']) : date('Y-m-d')); ?>">
                        <input type="hidden" name="tipo" value="9A">
                        <button type="submit" class="btn btn-secondary m-2" id="btn_trab_9A">
                            Cargar 9A
                        </button> 9A (Becarios)
                    </form>

                    <div id="resultado_9A" class="mt-2"></div>

                    <!-- Formulario 1D -->
                    <!-- <form id="form_trab_1D">
                            <input type="hidden" name="fecha_inicio" value="<?php echo (isset($_POST['fecha_inicio_ofi']) ? ($_POST['fecha_inicio_ofi']) : date('Y-m-d')); ?>">
                            <input type="hidden" name="tipo" value="1D">
                            <button type="submit" class="btn btn-secondary m-2" id="btn_trab_1D">
                                Cargar 1D
                            </button> 1D (Diario/pago mensual)
                        </form>

                        <div id="resultado_1D" class="mt-2"></div> -->

                    <script>
                        // Funcion para obtener los datos de presncia de los trabajadores automaticamente
                        document.addEventListener('DOMContentLoaded', function () {
                            // Array con los tipos de formularios
                            const formTypes = ['1A', '1E', '9A'];

                            // Inicializar cada formulario
                            formTypes.forEach(type => {
                                const form = document.getElementById(`form_trab_${type}`);
                                if (form) {
                                    loadData(form, type);

                                    // Añadir event listener para cada formulario
                                    form.addEventListener('submit', function (e) {
                                        e.preventDefault();

                                        const btn = document.getElementById(`btn_trab_${type}`);

                                        if (btn.getAttribute('data-loaded') === 'true') {
                                            form.method = 'POST';
                                            form.submit();
                                        } else {
                                            // Si no están cargados, cargamos por AJAX
                                            loadData(form, type);
                                        }
                                    });
                                }
                            });
                        });

                        function loadData(form, type) {
                            const formData = new FormData(form);

                            // Deshabilitamos el botón y mostramos icono de carga
                            const btn = document.getElementById(`btn_trab_${type}`);
                            btn.disabled = true;
                            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Cargando...';

                            // Enviamos la solicitud AJAX
                            fetch(`auto.php?trab_${type}`, {
                                method: 'POST',
                                body: formData
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Error en la respuesta del servidor');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    const totalPresencia = data.total_presencia;
                                    const totalTrabajadores = data.total_trabajadores;
                                    // console.log(`Datos de ${type}:`, data);

                                    // Actualizamos el contenido del botón
                                    btn.innerHTML = `${totalPresencia} / ${totalTrabajadores}`;
                                    btn.disabled = false;

                                    // Marcamos que ya se han cargado los datos
                                    btn.setAttribute('data-loaded', 'true');

                                    // Cambiar el action del formulario para futuras submissions
                                    form.action = `admin_cont.php?controller=index&action=presencia&tipo=${type}`;
                                    form.method = 'POST';
                                })
                                .catch(error => {
                                    console.error(`Error en ${type}:`, error);
                                    document.getElementById(`resultado_${type}`).innerText = `Error al cargar datos de ${type}.`;

                                    // Restauramos el estado del botón
                                    btn.innerHTML = `Cargar ${type}`;
                                    btn.disabled = false;
                                });
                        }
                    </script>

                </div>
            </div>
        </div>


        <?php
        // inciarlizar variables paras sumar los segundos de los horarios
        $segundos_desayuno = 0;
        $segundos_almuerzo = 0;
        $segundos_otros = 0;
        $segundos_descanso = 0;
        $segundos_tiempo_efectivo = 0;
        $segundos_horas_totales = 0;
        ?>

        <?php
        if (isset($params['datos_export_ofi'])) {
            $estadoSeleccionado = isset($_POST['estado_ofi']) ? $_POST['estado_ofi'] : '';
            ?>
            <div class="card">
                <div class="card-body">
                    <br>
                    <?php
                    // EXPORTAR REGISTROS
                    if (!empty($params['datos_export_ofi'])) {
                        ?>
                        <div class='card-body'>
                            <h5 style='color: #012970; margin-top: 10px;'><?php echo $lang['metodo_exp']; ?></h5><br>
                            <form action='' id='exportar' method='post' style='display: inline-block; margin-left: 15px;'>
                                <input type='hidden' name='fecha_inicio_ofi'
                                    value='<?php echo isset($_POST['fecha_inicio_ofi']) ? htmlspecialchars($_POST['fecha_inicio_ofi']) : ''; ?>'>
                                <input type='hidden' name='fecha_fin_ofi'
                                    value='<?php echo isset($_POST['fecha_fin_ofi']) ? htmlspecialchars($_POST['fecha_fin_ofi']) : ''; ?>'>
                                <input type='hidden' name='pernr_nom_trab'
                                    value='<?php echo isset($_POST['pernr_nom_trab']) ? htmlspecialchars(implode(',', $_POST['pernr_nom_trab'])) : ''; ?>'>

                                <input type='hidden' name='filtro_horas'
                                    value='<?php echo isset($_POST['filtro_horas']) ? htmlspecialchars($_POST['filtro_horas']) : ''; ?>'>

                                <input type='hidden' name='campo1_1'
                                    value='<?php echo isset($_POST['campo1_1']) ? htmlspecialchars($_POST['campo1_1']) : ''; ?>'>
                                <input type='hidden' name='campo2_1'
                                    value='<?php echo isset($_POST['campo2_1']) ? htmlspecialchars($_POST['campo2_1']) : ''; ?>'>
                                <input type='hidden' name='campo3_1'
                                    value='<?php echo isset($_POST['campo3_1']) ? htmlspecialchars($_POST['campo3_1']) : ''; ?>'>
                                <input type='hidden' name='conector_1'
                                    value='<?php echo isset($_POST['conector_1']) ? htmlspecialchars($_POST['conector_1']) : ''; ?>'>

                                <input type='hidden' name='campo1_2'
                                    value='<?php echo isset($_POST['campo1_2']) ? htmlspecialchars($_POST['campo1_2']) : ''; ?>'>
                                <input type='hidden' name='campo2_2'
                                    value='<?php echo isset($_POST['campo2_2']) ? htmlspecialchars($_POST['campo2_2']) : ''; ?>'>
                                <input type='hidden' name='campo3_2'
                                    value='<?php echo isset($_POST['campo3_2']) ? htmlspecialchars($_POST['campo3_2']) : ''; ?>'>
                                <input type='hidden' name='conector_2'
                                    value='<?php echo isset($_POST['conector_2']) ? htmlspecialchars($_POST['conector_2']) : ''; ?>'>

                                <input type='hidden' name='campo1_3'
                                    value='<?php echo isset($_POST['campo1_3']) ? htmlspecialchars($_POST['campo1_3']) : ''; ?>'>
                                <input type='hidden' name='campo2_3'
                                    value='<?php echo isset($_POST['campo2_3']) ? htmlspecialchars($_POST['campo2_3']) : ''; ?>'>
                                <input type='hidden' name='campo3_3'
                                    value='<?php echo isset($_POST['campo3_3']) ? htmlspecialchars($_POST['campo3_3']) : ''; ?>'>
                                <input type='hidden' name='conector_3'
                                    value='<?php echo isset($_POST['conector_3']) ? htmlspecialchars($_POST['conector_3']) : ''; ?>'>

                                <input type='hidden' name='campo1_4'
                                    value='<?php echo isset($_POST['campo1_4']) ? htmlspecialchars($_POST['campo1_4']) : ''; ?>'>
                                <input type='hidden' name='campo2_4'
                                    value='<?php echo isset($_POST['campo2_4']) ? htmlspecialchars($_POST['campo2_4']) : ''; ?>'>
                                <input type='hidden' name='campo3_4'
                                    value='<?php echo isset($_POST['campo3_4']) ? htmlspecialchars($_POST['campo3_4']) : ''; ?>'>
                                <input type='hidden' name='conector_4'
                                    value='<?php echo isset($_POST['conector_4']) ? htmlspecialchars($_POST['conector_4']) : ''; ?>'>

                                <input type='hidden' name='campo1_5'
                                    value='<?php echo isset($_POST['campo1_5']) ? htmlspecialchars($_POST['campo1_5']) : ''; ?>'>
                                <input type='hidden' name='campo2_5'
                                    value='<?php echo isset($_POST['campo2_5']) ? htmlspecialchars($_POST['campo2_5']) : ''; ?>'>
                                <input type='hidden' name='campo3_5'
                                    value='<?php echo isset($_POST['campo3_5']) ? htmlspecialchars($_POST['campo3_5']) : ''; ?>'>
                                <input type='hidden' name='conector_5'
                                    value='<?php echo isset($_POST['conector_5']) ? htmlspecialchars($_POST['conector_5']) : ''; ?>'>

                                <input type='hidden' name='campo1_6'
                                    value='<?php echo isset($_POST['campo1_6']) ? htmlspecialchars($_POST['campo1_6']) : ''; ?>'>
                                <input type='hidden' name='campo2_6'
                                    value='<?php echo isset($_POST['campo2_6']) ? htmlspecialchars($_POST['campo2_6']) : ''; ?>'>
                                <input type='hidden' name='campo3_6'
                                    value='<?php echo isset($_POST['campo3_6']) ? htmlspecialchars($_POST['campo3_6']) : ''; ?>'>
                                    
                                <button type="button" target="_blank"
                                    onclick="document.getElementById('exportar').action='exportar.php?informe_presencia_ofi_audi_pdf'; document.getElementById('exportar').target='_blank'; document.getElementById('exportar').submit();"
                                    style="background-color: white; margin-right: 60px;">
                                    <img src="img/pdf.png" style="max-width: 100px; width: 50px;">
                                </button>

                                <button type="button" target="_blank"
                                    onclick="document.getElementById('exportar').action='exportar.php?informe_presencia_ofi_audi_excel'; document.getElementById('exportar').submit();"
                                    style="background-color: white;">
                                    <img src="img/xls.png" style="max-width: 100px; width: 50px;">
                                </button>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="table-container" style="overflow-x: auto;">

                        <table class="table table-hover sticky-table" id="exportar_datos">
                            <thead class="sticky-header">
                                <tr>
                                    <th class="col-2">Trabajador</th>
                                    <th class="col-1">Fecha</th>
                                    <th class="col-1">Desayuno</th>
                                    <th class="col-1">Almuerzo</th>
                                    <th class="col-1">Otros</th>
                                    <th class="col-1">Descanso Total</th>
                                    <th class="col-1">Tiempo Efectivo</th>
                                    <th class="col-1">Horas Totales</th>
                                    <?php if (isset($_POST['filtro_horas']) && $_POST['filtro_horas'] == "totales") {

                                    } else { ?>
                                        <th class="col-1">Acciones</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($params['datos_export_ofi'] as $registro) {

                                    $mostrarValidacion = false;
                                    if (isset($registro['manual']) && ($registro['manual'] == '2' || $registro['manual'] == '3')) {
                                        $mostrarValidacion = true;
                                    }

                                    // Verificar si estamos en modo totales
                                    $esTotales = !isset($registro['fecha']) || $registro['fecha'] === null;

                                    if ($esTotales) {
                                        // Para totales, usamos valores por defecto
                                        $fecha = 'TOTAL';
                                        $pernr = $registro['pernr'];
                                    } else {
                                        // Para registros individuales
                                        $fecha = $registro['fecha']->format('Y-m-d');
                                        $pernr = $registro['pernr'];
                                    }

                                    
                                    $nombreCompleto = $registro['NOMBREYAPELLIDOS'];


                                    if (!$esTotales) {
                                        // Filtrar los registros según el valor seleccionado en el <select>
                                        // (Solo aplicar filtros de estado para registros individuales)
                                        if (isset($estadoSeleccionado) && $estadoSeleccionado != '') {
                                            if ($registro['estado_consolidado'] != $estadoSeleccionado) {
                                                continue;
                                            }
                                        }

                                        $tieneEstado3 = isset($registro['estado_consolidado']) && $registro['estado_consolidado'] == 3;
                                        $tieneEstado2 = isset($registro['estado_consolidado']) && $registro['estado_consolidado'] == 2;

                                        if (!$esTotales) {
                                            // Clase adicional acumulativa dependiendo del estado validado o no validado
                                            $claseFilaPrincipal = '';
                                            if ($tieneEstado3) {
                                                $claseFilaPrincipal = 'table-success';
                                            } elseif ($tieneEstado2) {
                                                $claseFilaPrincipal = 'table-warning';
                                            }
                                        }
                                    }
                                    ?>

                                    <!-- Fila principal -->
                                    <tr class="<?php echo $claseFilaPrincipal; ?> fila-principal"
                                        data-fecha="<?php echo $fecha; ?>" data-pernr="<?php echo $pernr; ?>">
                                        <td>
                                            <?php echo $nombreCompleto . "<br>" . $registro['pernr']; ?>
                                            <?php if ($esTotales) { ?>
                                                <br><small class="text-muted"><strong>TOTALES</strong></small>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $fecha; ?></td>
                                        <td><?php echo $registro['horas_desayuno']; ?></td>
                                        <td><?php echo $registro['horas_almuerzo']; ?></td>
                                        <td><?php echo $registro['horas_otros']; ?></td>
                                        <td><?php echo $registro['horas_descanso']; ?></td>
                                        <td><?php echo $registro['horas_producido']; ?></td>
                                        <td><?php echo $registro['horas_totales']; ?></td>

                                        <!-- Variables con la suma de cada segundos por tipo -->
                                        <?php
                                        $segundos_desayuno = $segundos_desayuno + $registro['segundos_desayuno'];
                                        $segundos_almuerzo = $segundos_almuerzo + $registro['segundos_almuerzo'];
                                        $segundos_otros = $segundos_otros + $registro['segundos_otros'];
                                        $segundos_descanso = $segundos_descanso + $registro['segundos_descanso'];
                                        $segundos_tiempo_efectivo = $segundos_tiempo_efectivo + $registro['segundos_producido'];
                                        $segundos_horas_totales = $segundos_horas_totales + $registro['segundos_totales'];
                                        ?>

                                        <?php if (!$esTotales) { ?>
                                            <td>
                                                <button class="btn btn-primary btn-sm toggle-button"
                                                    data-fecha="<?php echo $fecha; ?>" data-pernr="<?php echo $pernr; ?>"
                                                    onclick="toggleDetails('<?php echo $fecha; ?>', '<?php echo $pernr; ?>')">
                                                    Ver detalles
                                                </button>
                                            </td>
                                        <?php } else { ?>

                                        <?php } ?>
                                    </tr>

                                    <?php if (!$esTotales) { ?>
                                        <!-- Fila de detalles (solo para registros individuales) -->
                                        <tr class="detalle-<?php echo $fecha; ?>-<?php echo $pernr; ?> table-secondary"
                                            style="display: none;">
                                            <td colspan="9">
                                                Detalles para el registro de <?php echo $fecha; ?> - <?php echo $pernr; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    } // Fin de la verificación de si es totales
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            // Seleccionar todas las filas principales
                            document.querySelectorAll('.fila-principal').forEach(fila => {
                                fila.addEventListener('click', function (e) {
                                    // Evitar conflicto si se hace click en el botón
                                    if (e.target.closest('button')) {
                                        return;
                                    }

                                    const fecha = this.getAttribute('data-fecha');
                                    const pernr = this.getAttribute('data-pernr');

                                    if (fecha && pernr) {
                                        toggleDetails(fecha, pernr);
                                    }
                                });
                            });
                        });

                        function toggleDetails(fecha, pernr) {
                            const detalles = document.querySelector(`.detalle-${fecha}-${pernr}`);
                            const tdDetalles = detalles.querySelector("td");
                            const button = document.querySelector(`button[data-fecha="${fecha}"][data-pernr="${pernr}"]`);
                            const filaPrincipal = button.closest('tr');

                            if (detalles.style.display === 'none') {
                                const textoOriginal = button.innerText;
                                button.innerText = "Cargando...";
                                button.disabled = true;

                                fetch(`auto.php?registros_trabajador&fecha=${fecha}&pernr=${pernr}`)
                                    .then(response => response.json())
                                    .then(response => {
                                        if (response.success && Array.isArray(response.data)) {
                                            tdDetalles.innerHTML = "";
                                            const tablaHTML = construirTablaDetalles(response.data);
                                            tdDetalles.innerHTML = tablaHTML;
                                            detalles.style.display = '';
                                            button.innerText = "Ocultar detalles";
                                            button.classList.remove('btn-primary');
                                            button.classList.add('btn-danger');
                                            filaPrincipal.classList.add('table-secondary');

                                            // Tooltips
                                            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
                                        } else {
                                            tdDetalles.innerHTML = "<div class='text-danger'>Error: formato no válido</div>";
                                            detalles.style.display = '';
                                            button.innerText = textoOriginal;
                                        }
                                    })
                                    .catch(error => {
                                        console.error(error);
                                        tdDetalles.innerHTML = "<div class='text-danger'>Error al cargar los datos</div>";
                                        detalles.style.display = '';
                                        button.innerText = textoOriginal;
                                    })
                                    .finally(() => {
                                        button.disabled = false;
                                    });
                            } else {
                                detalles.style.display = 'none';
                                button.innerText = "Ver detalles";
                                button.classList.remove('btn-danger');
                                button.classList.add('btn-primary');
                                filaPrincipal.classList.remove('table-secondary');
                            }
                        }

                        function escapeHTML(text) {
                            if (!text) return "-";
                            return text
                                .replace(/&/g, "&amp;")
                                .replace(/</g, "&lt;")
                                .replace(/>/g, "&gt;")
                                .replace(/"/g, "&quot;")
                                .replace(/'/g, "&#039;");
                        }

                        function tieneContenido(valor) {
                            return valor && valor.toString().trim() !== '' && valor.toString().trim() !== '-';
                        }

                        function limpiarLocalizacion(localizacion) {
                            if (!localizacion) return '-';
                            const partes = localizacion.split(' | ');
                            return partes[0].trim();
                        }

                        function iconoDispositivo(dispositivo) {
                            if (!dispositivo) return "<i class='bi bi-tablet-landscape'></i>";
                            dispositivo = dispositivo.toLowerCase();
                            if (dispositivo.includes('android')) {
                                return "<i class='bx bxl-android'></i>";
                            } else if (dispositivo.includes('windows') || dispositivo.includes('linux') || dispositivo.includes('macintosh')) {
                                return "<i class='bi bi-laptop'></i>";
                            } else if (dispositivo.includes('iphone') || dispositivo.includes('ipad')) {
                                return "<i class='bx bxl-apple'></i>";
                            } else {
                                return "<i class='bi bi-tablet-landscape'></i>";
                            }
                        }

                        function construirBotonValidacion(reg) {
                            const id = reg.id;
                            const pernr = reg.pernr;
                            const tipoReg = escapeHTML(reg.tipo_reg || '-');

                            // Tomamos fecha_reg si existe, si no fecha
                            const fechaObj = reg.fecha_reg?.date ? reg.fecha_reg : reg.fecha;
                            const fechaOriginalStr = fechaObj?.date?.split('.')[0].replace(' ', 'T');
                            const fechaOriginal = fechaOriginalStr ? new Date(fechaOriginalStr) : null;
                            if (!fechaOriginal) return "";

                            const pad = (n) => n.toString().padStart(2, '0');

                            // Si la hora es <= 3, usamos día anterior
                            const fechaParaInput = new Date(fechaOriginal);
                            if (fechaParaInput.getHours() <= 3) {
                                fechaParaInput.setDate(fechaParaInput.getDate() - 1);
                            }

                            const minFecha = `${fechaParaInput.getFullYear()}-${pad(fechaParaInput.getMonth() + 1)}-${pad(fechaParaInput.getDate())}T00:00`;
                            const maxFecha = `${fechaOriginal.getFullYear()}-${pad(fechaOriginal.getMonth() + 1)}-${pad(fechaOriginal.getDate())}T23:59`;
                            const fechaValue = `${fechaParaInput.getFullYear()}-${pad(fechaParaInput.getMonth() + 1)}-${pad(fechaParaInput.getDate())}T${pad(fechaOriginal.getHours())}:${pad(fechaOriginal.getMinutes())}`;

                            return `
                                <button type='button' style='background-color: transparent;' data-bs-toggle='modal' data-bs-target='#validacionsalida_${id}'>
                                    <i data-bs-toggle='tooltip' data-bs-placement='right' title='Validar' class='bi bi-check2-square fs-4' style='margin-left: 25px; color: #2c384e;'></i>
                                </button>
                                <div class='modal fade' id='validacionsalida_${id}' tabindex='-1' aria-hidden='true'>
                                    <div class='modal-dialog modal-dialog-centered'>
                                        <div class='modal-content'>
                                            <form action='admin_cont.php?controller=index&action=auditor&validado' method='post'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title'>Validación horario</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>
                                                <div class='modal-body'>
                                                    <p style='font-size: 17px; text-align: justify;'>Validación de ${tipoReg} día ${fechaParaInput.toLocaleDateString('es-ES')}</p>
                                                    <label for='fecha_valida_${id}'>Introduce una fecha válida</label><br>
                                                    <input type='datetime-local' class='form-control w-50 mt-1' name='fecha_valida' id='fecha_valida_${id}' 
                                                        value='${fechaValue}' min='${minFecha}' max='${maxFecha}' required>
                                                    
                                                    <label for='motivo_${id}' class='mt-2'>Motivo</label><br>
                                                    <input type='text' class='form-control mt-1' name='motivo' id='motivo_${id}' required>

                                                    <input type='hidden' name='id' value='${id}'>
                                                    <input type='hidden' name='estado' value='3'>
                                                    <input type='hidden' name='pernr' value='${pernr}'>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                                    <button type='submit' class='btn btn-primary'>Validar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }

                        function construirBotonModificacion(reg) {
                            const id = reg.id;

                            const fechaObj = reg.fecha_reg?.date ? reg.fecha_reg : reg.fecha;
                            const fechaOriginalStr = fechaObj?.date?.split('.')[0].replace(' ', 'T');
                            const fechaOriginal = fechaOriginalStr ? new Date(fechaOriginalStr) : null;
                            if (!fechaOriginal) return "";

                            const pad = n => n.toString().padStart(2, '0');

                            // Si la hora es <= 3, usamos día anterior
                            const fechaParaInput = new Date(fechaOriginal);
                            if (fechaParaInput.getHours() <= 3) {
                                fechaParaInput.setDate(fechaParaInput.getDate() - 1);
                            }

                            const minFecha = `${fechaParaInput.getFullYear()}-${pad(fechaParaInput.getMonth() + 1)}-${pad(fechaParaInput.getDate())}T00:00`;
                            const maxFecha = `${fechaOriginal.getFullYear()}-${pad(fechaOriginal.getMonth() + 1)}-${pad(fechaOriginal.getDate())}T23:59`;
                            const fechaValue = `${fechaParaInput.getFullYear()}-${pad(fechaParaInput.getMonth() + 1)}-${pad(fechaParaInput.getDate())}T${pad(fechaOriginal.getHours())}:${pad(fechaOriginal.getMinutes())}`;

                            const comentarioAnterior = escapeHTML(reg.motivo || '');

                            return `
                                <button type='button' style='background-color: transparent;' data-bs-toggle='modal' data-bs-target='#modificacion_${id}'>
                                    <i data-bs-toggle='tooltip' data-bs-placement='right' title='Modificar' class='bi bi-pencil-square fs-4' style='margin-left: 25px; color: #2c384e;'></i>
                                </button>
                                <div class='modal fade' id='modificacion_${id}' tabindex='-1' aria-hidden='true'>
                                    <div class='modal-dialog modal-dialog-centered'>
                                        <div class='modal-content'>
                                            <form action='admin_cont.php?controller=index&action=auditor&modificar' method='post'>
                                                <div class='modal-header'>
                                                    <h5 class='modal-title'>Modificar registro</h5>
                                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                                </div>
                                                <div class='modal-body'>
                                                    <label for='fecha_mod_${id}'>Nueva fecha</label><br>
                                                    <input type='datetime-local' class='form-control w-50 mt-1' name='fecha_mod' id='fecha_mod_${id}' 
                                                        value='${fechaValue}' min='${minFecha}' max='${maxFecha}' required>
                                                    
                                                    <label for='comentario_${id}' class='mt-2'>Motivo</label><br>
                                                    <input type='text' class='form-control mt-1' name='comentario' id='comentario_${id}' value='${comentarioAnterior}' required>

                                                    <input type='hidden' name='id' value='${id}'>
                                                </div>
                                                <div class='modal-footer'>
                                                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cerrar</button>
                                                    <button type='submit' class='btn btn-primary'>Guardar cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }

                        function construirTablaDetalles(data) {
                            if (!data || data.length === 0) return "<div class='text-info'>No hay registros disponibles</div>";

                            const columnasConContenido = {
                                tipoReg: data.some(reg => tieneContenido(reg.tipo_reg)),
                                comentario: data.some(reg => tieneContenido(reg.comentario)),
                                motivo: data.some(reg => tieneContenido(reg.motivo)),
                                validacion: data.some(reg => reg.manual == 2 || reg.manual == 3),
                                sedeOubi: data.some(reg => tieneContenido(reg.sede) || tieneContenido(reg.nombre_ubi)),
                                localizacion: data.some(reg => tieneContenido(reg.localizacion))
                            };

                            let tabla = "<table class='table table-striped table-bordered'><thead><tr>";
                            tabla += "<th class='col-md-1'></th>";

                            if (columnasConContenido.tipoReg) {
                                tabla += "<th class='col-md-2'>Tipo Registro</th>";
                            }

                            tabla += "<th class='col-md-2'>Fecha Registro</th>";
                            tabla += "<th class='col-md-2'>Sede</th>";
                            tabla += "<th class='col-md-2'>Ubicación</th>";

                            if (columnasConContenido.comentario) {
                                tabla += "<th class='col-md-2'>Comentario</th>";
                            }

                            if (columnasConContenido.validacion) {
                                tabla += "<th class='col-md-1'>Validación</th>";
                            }

                            if (columnasConContenido.motivo) {
                                tabla += "<th class='col-md-2'>Motivo</th>";
                            }

                            tabla += "</tr></thead><tbody>";

                            data.forEach(reg => {
                                tabla += "<tr>";
                                tabla += `<td>${iconoDispositivo(reg.dispositivo)}</td>`;

                                if (columnasConContenido.tipoReg) {
                                    tabla += `<td>${escapeHTML(reg.tipo_reg || '-')}</td>`;
                                }

                                const fecha = reg.fecha_reg?.date ? reg.fecha_reg.date.split(" ")[0] + " " + reg.fecha_reg.date.split(" ")[1].slice(0, 5) : '-';
                                tabla += `<td>${fecha}</td>`;

                                if (reg.sede || reg.nombre_ubi) {
                                    tabla += `<td>${escapeHTML(reg.sede || '-')}</td>`;
                                    tabla += `<td>${escapeHTML(reg.nombre_ubi || '-')}</td>`;
                                } else {
                                    tabla += `<td colspan="2">${limpiarLocalizacion(reg.localizacion)}</td>`;
                                }

                                if (columnasConContenido.comentario) {
                                    tabla += `<td>${escapeHTML(reg.comentario || '-')}</td>`;
                                }

                                if (columnasConContenido.validacion) {
                                    let celdaValidacion = "";
                                    if (reg.manual == 2) {
                                        celdaValidacion = construirBotonValidacion(reg);
                                    } else if (reg.manual == 3) {
                                        celdaValidacion = construirBotonModificacion(reg);
                                    }
                                    tabla += `<td>${celdaValidacion}</td>`;
                                }

                                if (columnasConContenido.motivo) {
                                    tabla += `<td>${escapeHTML(reg.motivo || '-')}</td>`;
                                }

                                tabla += "</tr>";
                            });

                            tabla += "</tbody></table>";

                            return tabla;
                        }
                    </script>
                </div>
            </div>
        </div>

        <?php
        if (isset($params['datos_export_ofi']) && !empty($params['datos_export_ofi'])) {
            ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Detalles de Tiempo totales</h5>
                    <p class="card-text">
                        <!-- Tabla con los tiempos totales -->
                    <div style="overflow-x: auto; width: 100%;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="col-2"></th>
                                    <th class="col-1"></th>
                                    <th class="col-1">Desayuno</th>
                                    <th class="col-1">Almuerzo</th>
                                    <th class="col-1">Otros</th>
                                    <th class="col-1">Descanso</th>
                                    <th class="col-1">Tiempo <br>Efectivo</th>
                                    <th class="col-1">Horas Totales</th>
                                    <th class="col-1"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <?php
                                    // Función para convertir segundos a formato de horas:minutos
                                    function convertir_a_horas($segundos)
                                    {
                                        $horas = floor($segundos / 3600);
                                        $minutos = floor(($segundos % 3600) / 60);
                                        return sprintf("%02d:%02d", $horas, $minutos);
                                    }
                                    ?>
                                    <!-- cálculos para mostrar los segundos en horas y minutos dividiendo -->
                                    <td style='color: white;'>XXXXX XXXXXXXX, XXXX</td>
                                    <td style='color: white;'>XX-XX-XX</td>
                                    <td><?php echo convertir_a_horas($segundos_desayuno); ?></td>
                                    <td><?php echo convertir_a_horas($segundos_almuerzo); ?></td>
                                    <td><?php echo convertir_a_horas($segundos_otros); ?></td>
                                    <td><?php echo convertir_a_horas($segundos_descanso); ?></td>
                                    <td><?php echo convertir_a_horas($segundos_tiempo_efectivo); ?></td>
                                    <td><?php echo convertir_a_horas($segundos_horas_totales); ?></td>
                                    <td style='color: white;'>XXXXXXXX</td>
                                </tr>
                            </tbody>
                        </table>
                        <p class="additional-info">Nota: Los tiempos están expresados en horas y minutos.</p>
                    </div>
                    </p>
                </div>
            </div>
            <?php
        }
        } else {

        }
        ?>

</section>


<!-- redireccion con js a los 3 segundos cunado encuentre en la url 'validado' -->

<script>
    setTimeout(function () {
        if (window.location.href.indexOf('validado') !== -1) {
            window.location.href = 'admin_cont.php?controller=index&action=auditor';
        }
    }, 3000);
</script>

<?php
include_once("footer.php");
?>