<?php
// var_dump($params['llamamientos']);
// die;
include_once("header.php");
?>

<div class="pagetitle">
    <h1><?php echo $lang['titu_reg']; ?></h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i
                    class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item"><?php echo $lang['menu3']; ?></li>
        <li class="breadcrumb-item active"><?php echo $lang['menu6']; ?></li>
    </ol>
</nav>

<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <button id="toggleButton" class="filtros btn"><?php echo $lang['filtros']; ?></button>
                <div id="formContainer" class="card-body" style="display:none;"> <!-- Inicialmente oculto -->
                    <form id="filterForm" class="row g-3"
                        action="admin_cont.php?controller=index&action=registros_llama" method="post">
                        <div class="col-md-2">
                            <input type="text" name="txt_pernr" class="form-control" placeholder="PERNR"
                                value="<?php if (isset($_POST['txt_pernr'])) {
                                    echo $_POST['txt_pernr'];
                                } ?>">
                        </div>
                        <div class="col-md-2">
                            <select name="estado" class="form-select">
                                <option value="" <?php if (isset($_POST['estado']) && $_POST['estado'] === "") {echo "selected";} ?>><?php echo $lang['estado']; ?></option>
                                <option value="0" <?php if (isset($_POST['estado']) && $_POST['estado'] === "0") {echo "selected";} ?>><?php echo $lang['enviado']; ?></option>
                                <option value="1" <?php if (isset($_POST['estado']) && $_POST['estado'] === "1") {echo "selected";} ?>><?php echo $lang['aceptado']; ?></option>
                                <option value="2" <?php if (isset($_POST['estado']) && $_POST['estado'] === "2") {echo "selected";} ?>><?php echo $lang['rechazado']; ?></option>
                                <option value="3" <?php if (isset($_POST['estado']) && $_POST['estado'] === "3") {echo "selected";} ?>><?php echo $lang['pendiente']; ?></option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="tipo_llama" class="form-select">
                                <option value="" <?php if (isset($_POST['tipo_llama']) && $_POST['tipo_llama'] === "") {
                                    echo "selected";
                                } ?>><?php echo $lang['tipo']; ?></option>
                                <option value="SMS" <?php if (isset($_POST['tipo_llama']) && $_POST['tipo_llama'] === "SMS") {
                                    echo "selected";
                                } ?>>SMS</option>
                                <option value="Telefono" <?php if (isset($_POST['tipo_llama']) && $_POST['tipo_llama'] === "Telefono") {
                                    echo "selected";
                                } ?>>
                                    <?php echo $lang['telefono']; ?></option>
                                <option value="Correo" <?php if (isset($_POST['tipo_llama']) && $_POST['tipo_llama'] === "Correo") {
                                    echo "selected";
                                } ?>>
                                    <?php echo $lang['correo2']; ?></option>
                                <!-- <option value="WhatsApp" <?php if (isset($_POST['tipo_llama']) && $_POST['tipo_llama'] === "WhatsApp") {
                                    echo "selected";
                                } ?>>WhatsApp</option> -->
                            </select>
                        </div>
                        <div class="col-md-2">
                        </div>
                        <div class="clear"></div>
                        <div class="col-md-2">
                            <?php echo $lang['desde']; ?>
                            <input type="date" class="form-control" name="desde" placeholder="Desde"
                                value="<?php if (isset($_POST['desde'])) {
                                    echo $_POST['desde'];
                                } ?>">
                        </div>
                        <div class="col-md-2">
                            <?php echo $lang['hasta']; ?>
                            <input type="date" class="form-control" name="hasta" placeholder="Hasta"
                                value="<?php if (isset($_POST['hasta'])) {
                                    echo $_POST['hasta'];
                                } ?>">
                        </div>
                        <div class="clear"></div>
                        <div class="col-md-3">
                            <input type="submit" class="btn btn-primary mt-auto" name="buscar"
                                value="<?php echo $lang['buscar']; ?>">
                            <button type="button" id="clearFilters" class="btn btn-secondary mt-auto"
                                style="background-color: #dc3545; border: none;">
                                Reset
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            <script>
                // FILTROS pagina Registros llamamiento
                // Mostrar/Ocultar filtros
                var toggleButton = document.getElementById('toggleButton');
                if (toggleButton) {  // Verificar si el botón existe
                    toggleButton.addEventListener('click', function () {
                        var formContainer = document.getElementById('formContainer');
                        if (formContainer) {  // Verificar si el contenedor del formulario existe
                            if (formContainer.style.display === 'none') {
                                formContainer.style.display = 'block';  // Mostrar el formulario
                            } else {
                                formContainer.style.display = 'none';   // Ocultar el formulario
                            }
                        }
                    });
                }

                // Limpiar el formulario al hacer clic en el botón de limpiar
                var clearFilters = document.getElementById('clearFilters');
                if (clearFilters) {  // Verificar si el botón de limpiar existe
                    clearFilters.addEventListener('click', function () {
                        var filterForm = document.getElementById('filterForm');
                        if (filterForm) {  // Verificar si el formulario existe
                            // Restablecer el formulario a sus valores predeterminados
                            filterForm.reset();

                            // También puedes deseleccionar cualquier opción en los select
                            var selects = document.querySelectorAll('#filterForm select');
                            selects.forEach(function (select) {
                                select.selectedIndex = 0; // Restablece la selección al primer elemento
                            });

                            // Limpiar todos los campos de texto
                            var inputs = document.querySelectorAll('#filterForm input[type="text"], #filterForm input[type="date"]');
                            inputs.forEach(function (input) {
                                input.value = ''; // Limpia el valor de los campos de texto y de fecha
                            });
                        }
                    });
                }
            </script>




            <div class="card">
                <div class="card-body">
                    <br>
                    <?php
                    if (!empty($params['llamamientos'])) {
                        ?>
                        <table class="table datatable" id="tabla_reg_llama">
                            <thead>
                                <tr>
                                    <div class="col-9">

                                        <th class="col-3"><?php echo $lang['nombre']; ?></th>
                                        <th class="col-2"><?php echo $lang['tipo_llama']; ?></th>
                                        <th class="col-2"><?php echo $lang['fecha_reg']; ?></th>
                                        <th class="col-1"><?php echo $lang['estado']; ?></th>
                                        <th class="col-2"><?php echo $lang['contacto_rrhh']; ?></th>
                                        <th class="col-1"></th>
                                    </div>
                                </tr>
                            </thead>
                            <tbody>

                                <h2></h2>
                                <?php
                                foreach ($params['llamamientos'] as $resultado) {
                                    $current_date = new DateTime();

                                    $registro_date = ($resultado['FECHA_REGISTRO'] instanceof DateTime)
                                        ? $resultado['FECHA_REGISTRO']
                                        : new DateTime($resultado['FECHA_REGISTRO']);

                                    $interval = $current_date->diff($registro_date);

                                    $current_date = new DateTime("now", new DateTimeZone('Europe/Madrid'));

                                    $registro_date = ($resultado['FECHA_REGISTRO'] instanceof DateTime)
                                        ? $resultado['FECHA_REGISTRO']
                                        : new DateTime($resultado['FECHA_REGISTRO'], new DateTimeZone('Europe/Madrid'));

                                    $diff_seconds = $current_date->getTimestamp() - $registro_date->getTimestamp();
                                    $diff_hours = $diff_seconds / 3600;

                                    ?>
                                    <tr>

                                        <td>
                                            <?php
                                            if (
                                                ($resultado['ESTADO'] == 0 && $resultado['NUM_ENVIO'] == '1' && $diff_hours > 360)
                                                || ($resultado['ESTADO'] == 3 && $diff_hours > 360)
                                                || ($resultado['ESTADO'] == 0 && $resultado['NUM_ENVIO'] == '2' && $diff_hours > 120)
                                            ) {
                                                echo "<i class='bi bi-telephone-minus-fill' style='color: #f0ac2b;'> </i>";
                                            } elseif ($resultado['ESTADO'] == 2) {
                                                echo "<i class='bi bi-telephone-x-fill' style='color: #bb2d3b;'> </i>";
                                            } elseif ($resultado['ESTADO'] == 1) {
                                                echo "<i class='bi bi-telephone-plus-fill' style='color: #3c8500;'> </i>";
                                            } elseif ($resultado['ESTADO'] == 0) {
                                                echo "<i class='bi bi-telephone-forward-fill' style='color: #8c8c8c;'> </i>";
                                            } elseif ($resultado['ESTADO'] == 3) {
                                                echo "<i class='bi bi-stopwatch-fill' style='color: #31d2f2;'> </i>";
                                            }


                                            // Mostrar el nombre completo del trabajador
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
                                            } else {
                                                // Si no hay datos disponibles
                                                $nombre = 'Desconocido';
                                            }


                                            echo $nombre . "<br><p style='margin-left: 20px; margin-bottom: 0px !important'>" . $resultado['PERNR'] . "</p>"; ?>
                                        </td>


                                        <td>
                                            <?php echo $resultado['TIPO_LLAMAMIENTO']; ?>

                                            <!-- mostrar solo si el tipo de llamamiento es SMS -->
                                            <?php if ($resultado['TIPO_LLAMAMIENTO'] == 'SMS') { ?>
                                                <button type="button" class="btn"
                                                    onclick="toggleAccordion('<?php echo $resultado['ID']; ?>')">
                                                    (Ver SMS)
                                                </button>
                                                <div id="accordion_<?php echo $resultado['ID']; ?>"
                                                    style="display: none; margin-top: 10px; padding: 10px;">
                                                    <?php
                                                        if (!empty($resultado['MSG_ENVIO'])) {
                                                            $mensaje = $resultado['MSG_ENVIO'];

                                                            // Buscar la posición de "MAS INFO" o "MÁS INFO"
                                                            $pos = stripos($mensaje, 'MAS INFO');
                                                            $pos2 = stripos($mensaje, 'MÁS INFO');

                                                            // Determinar cuál posición usar (la que exista y sea más baja)
                                                            if ($pos !== false && $pos2 !== false) {
                                                                $posicion = min($pos, $pos2);
                                                            } elseif ($pos !== false) {
                                                                $posicion = $pos;
                                                            } elseif ($pos2 !== false) {
                                                                $posicion = $pos2;
                                                            } else {
                                                                $posicion = false;
                                                            }

                                                            // Cortar si encontramos alguna coincidencia
                                                            if ($posicion !== false) {
                                                                $mensaje = substr($mensaje, 0, $posicion);
                                                            }

                                                            echo trim($mensaje);
                                                            echo "<br><a href='./cartas/llama" . $resultado['NUM_ENVIO'] . "rrhh.php?pernr=" . md5($resultado['PERNR']) . "' target='_blank'> MÁS INFO</a>";
                                                        } else {
                                                            echo "No hay mensaje disponible.";
                                                        }
                                                    ?>
                                                </div>

                                                <script>
                                                    function toggleAccordion(id) {
                                                        var acc = document.getElementById("accordion_" + id);
                                                        acc.style.display = acc.style.display === "none" ? "block" : "none";
                                                    }
                                                </script>
                                            <?php } ?>
                                        </td>


                                        <td>
                                            <?php
                                            if ($resultado['TIPO_LLAMAMIENTO'] == 'Telefono') {
                                                $fecha = $resultado['FECHA_LLAMAMIENTO'] ?? $resultado['FECHA_REGISTRO'];
                                                echo date_format($fecha, 'Y-m-d H:i');
                                            } else {
                                                echo date_format($resultado['FECHA_REGISTRO'], 'Y-m-d H:i');
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (
                                                ($resultado['ESTADO'] == 0 && $resultado['NUM_ENVIO'] == '1' && $diff_hours < 360) // 15 días
                                                ||
                                                ($resultado['ESTADO'] == 0 && $resultado['NUM_ENVIO'] == '2' && $diff_hours < 120) // 5 días
                                                ||
                                                ($resultado['ESTADO'] == 0 &&
                                                    ($resultado['TIPO_LLAMAMIENTO'] == 'Correo' || $resultado['TIPO_LLAMAMIENTO'] == 'Telefono')
                                                    && $diff_hours < 120)
                                            ) {
                                                echo $lang['enviado'];
                                            } elseif ($resultado['ESTADO'] == 1) {
                                                echo $lang['aceptado'];
                                            } elseif ($resultado['ESTADO'] == 2) {
                                                echo $lang['rechazado'];
                                            } elseif ($resultado['ESTADO'] == 3 && $diff_hours < 360) {
                                                echo $lang['pendiente'];
                                            } elseif (
                                                ($resultado['ESTADO'] == 0 && $resultado['TIPO_LLAMAMIENTO'] == 'SMS' && $resultado['NUM_ENVIO'] == '1' && $diff_hours > 360)
                                                ||
                                                ($resultado['ESTADO'] == 0 && $resultado['TIPO_LLAMAMIENTO'] == 'SMS' && $resultado['NUM_ENVIO'] == '2' && $diff_hours > 120)
                                                ||
                                                ($resultado['ESTADO'] == 3 && $resultado['TIPO_LLAMAMIENTO'] == 'SMS' && $resultado['NUM_ENVIO'] == '1' && $diff_hours > 360)
                                                ||
                                                ($resultado['ESTADO'] == 3 && $resultado['TIPO_LLAMAMIENTO'] == 'SMS' && $resultado['NUM_ENVIO'] == '2' && $diff_hours > 120)
                                            ) {
                                                echo $lang['sin_respuesta'];
                                            } else {
                                                echo "Desconocido";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $resultado['NOMBRE_USUARIO']; ?>
                                        </td>
                                        <td>
                                            <li class="hvr-icon-back">
                                                <a
                                                    href="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $resultado['PERNR']; ?>&showll">
                                                    <div class="hvr-icon">
                                                        <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                                    </div>
                                                </a>
                                            </li>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        <button class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-html="true"
                            data-bs-placement="left" title="
                                    <i class='bi bi-telephone-minus-fill'></i> <?php echo $lang['sin_respuesta']; ?><br>
                                    <i class='bi bi-telephone-forward-fill'></i> <?php echo $lang['enviado']; ?><br>
                                    <i class='bi bi-telephone-plus-fill'></i> <?php echo $lang['aceptado']; ?><br>
                                    <i class='bi bi-telephone-x-fill'></i> <?php echo $lang['rechazado']; ?><br>
                                    <i class='bi bi-stopwatch-fill'></i> <?php echo $lang['pendiente']; ?>
                                ">
                            Info
                        </button>
                    <?php
                    } else {
                        echo "<p>No existe ningun registro de llamamiento</p>";
                    }
                    ?>
                    <br>

                </div>
            </div>
        </div>
    </div>
</section>




<?php
include_once("footer.php");
?>