<?php include_once("header.php"); ?>

<!-- Contenido dinámico según parámetro -->
<section class="section">
    <div class="pagetitle">
        <h1>
            <?php
            if (isset($_GET['cumple'])) {
                echo "Cumpleaños";
            } elseif (isset($_GET['doc'])) {
                echo "Documentos Caducados";
            }
            ?>
        </h1>
    </div>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="admin_cont.php?controller=index&action=home">
                        <i class="bi bi-house-door"></i>
                    </a>
                </li>
            </ol>
        </nav>

    <!-- CUMPLEAÑOS -->
    <?php if (isset($_GET['cumple']) && !empty($params['cumples'])) { ?>
    <div class="card">
        <div class="card-body mt-4">
            <table class="table datatable" id="tabla_cumple">
                <thead>
                    <tr>
                        <th class="col-2">PERNR</th>
                        <th class="col-4">Nombre</th>
                        <th class="col-2">Fecha nacimiento</th>
                        <th class="col-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($params['cumples'] as $resultado) { ?>
                        <tr>
                            <td><?php echo $resultado['PERNR']; ?></td>
                            <td><?php echo $resultado['NOMBREYAPELLIDOS']; ?></td>
                            <td><?php echo $resultado['FECHANACIMIENTO']; ?></td>
                            <td>
                                <li class="hvr-icon-back">
                                    <a href="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $resultado['PERNR']; ?>">
                                        <div class="hvr-icon" >
                                            <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                        </div>
                                    </a>
                                </li>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- DNI CADUCADOS -->  
    <?php } elseif (isset($_GET['doc']) && !empty($params['dni_caducados'])) { ?>
    <div class="card">
        <div class="card-body mt-4">
        <h5 style="font-size: 18px; font-weight: 500; color: #012970; font-family: Poppins, sans-serif;">Documentos expirados o por expirar</h5>
            <table class="table datatable" id="tabla_doc_cadu">
                <thead>
                    <tr>
                        <th class="col-1">PERNR</th>
                        <th class="col-2">Nombre</th>
                        <th class="col-1">Estado</th>
                        <th class="col-2">Fecha Validez (A-M-D)</th>
                        <th class="col-2">Tipo Documento</th>
                        <th class="col-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($params['dni_caducados'] as $resultado) { ?>
                        <tr>
                            <td><?php echo $resultado['pernr']; ?></td>
                            <td><?php echo $resultado['NOMBREYAPELLIDOS']; ?></td>
                            <td><?php
                                    if ($resultado['estado'] == 'Expirado') {
                                      echo "<i class='bi bi-circle-fill' style='color: #dc3545;'> </i>";
                                    } elseif ($resultado['estado'] == 'Expira pronto') {
                                      echo "<i class='bi bi-circle-fill' style='color: #ffc107;'></i> ";
                                    }
                                    echo $resultado['estado']; ?></td>
                            <td><?php echo $resultado['fecha_validez']->format('Y-m-d'); ?></td>
                            <td><?php echo $resultado['tipo_doc']; ?></td>
                            <td>
                                <li class="hvr-icon-back">
                                    <a href="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $resultado['pernr']; ?>">
                                        <div class="hvr-icon" >
                                            <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                        </div>
                                    </a>
                                </li>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Trabajadores sin llamamiento en remesas -->  
    <?php } elseif (isset($_GET['llama']) && !empty($params['trab_sinllama'])) { ?>
    <div class="card">
        <div class="card-body mt-4">
        <h5 style="font-size: 18px; font-weight: 500; color: #012970; font-family: Poppins, sans-serif;">Trabajadores sin llamamientos en remesas</h5>
            <table class="table datatable" id="tabla_trab_sinllama">
                <thead>
                    <tr>
                        <th class="col-2">PERNR</th>
                        <th class="col-3">Nombre</th>
                        <th class="col-3">Nombre remesa</th>
                        <th class="col-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($params['trab_sinllama'] as $resultado) { ?>
                        <tr>
                            <td><?php echo $resultado['PERNR']; ?></td>
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
                                <a href="admin_cont.php?controller=index&action=view_remesa_llama&id=<?php echo $resultado['id_remesa']; ?>&ano=<?php echo $resultado['ano_remesa']; ?>" target="_blank" style="color:#012970; text-decoration: underline;">
                                    <?php echo $resultado['nombre_remesa']; ?>
                                </a>
                            </td>
                            <td>
                                <li class="hvr-icon-back">
                                    <a href="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $resultado['PERNR']; ?>&showll&remesa&id_remesa=<?php echo $resultado['id_remesa']; ?>&ano_remesa=<?php echo $resultado['ano_remesa']; ?>">
                                        <div class="hvr-icon" >
                                            <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                        </div>
                                    </a>
                                </li>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Trabajadores Aceptados de baja -->  
    <?php } elseif (isset($_GET['aceptados']) && !empty($params['trab_aceptados_baja'])) { ?>
    <div class="card">
        <div class="card-body mt-4">
        <h5 style="font-size: 18px; font-weight: 500; color: #012970; font-family: Poppins, sans-serif;">Trabajadores aceptados que siguen de baja</h5>
            <table class="table datatable" id="tabla_trab_sinllama">
                <thead>
                    <tr>
                        <th class="col-2">PERNR</th>
                        <th class="col-3">Nombre</th>
                        <th class="col-3">Nombre remesa</th>
                        <th class="col-1"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($params['trab_aceptados_baja'] as $resultado) { ?>
                        <tr>
                            <td><?php echo $resultado['PERNR']; ?></td>
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
                                <a href="admin_cont.php?controller=index&action=view_remesa_llama&id=<?php echo $resultado['ID_REMESA']; ?>&ano=<?php echo $resultado['ANO_REMESA']; ?>" target="_blank" style="color:#012970; text-decoration: underline;">
                                    <?php echo $resultado['nombre_remesa']; ?>
                                </a>
                            </td>
                            <td>
                                <li class="hvr-icon-back">
                                    <a href="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $resultado['PERNR']; ?>&showll&remesa&id_remesa=<?php echo $resultado['ID_REMESA']; ?>&ano_remesa=<?php echo $resultado['ANO_REMESA']; ?>">
                                        <div class="hvr-icon" >
                                            <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                        </div>
                                    </a>
                                </li>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php 
        } else { 
        ?>
            <p>No hay datos disponibles para mostrar.</p>
        <?php 
        }
    ?>
</section>

<?php include_once("footer.php"); ?>
