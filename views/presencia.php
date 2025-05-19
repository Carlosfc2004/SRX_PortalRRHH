<?php
if (isset($params['trabajadores_presencia']) && !empty($params['trabajadores_presencia'])) {

} elseif (isset($_GET['tipo']) and $_GET['fecha_inicio']) { 
    
} else {
    header('Location: admin_cont.php?controller=index&action=auditor');
    exit;
}
include 'header.php';
?>

<div class="pagetitle">
    <h1>Trabajadores presencia (
        <?php
        // Verificar si los valores vienen por POST o GET
        $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : (isset($_GET['tipo']) ? $_GET['tipo'] : 'presencia');
        $fecha_inicio = isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : (isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d'));
        
        // Mostrar los valores con el formato deseado
        echo $tipo . " | " . (new DateTime($fecha_inicio))->format('d-m-Y');
        ?>
    )</h1>
</div>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
            <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=auditor">Presencia</a></li>
            <li class="breadcrumb-item active">Trabajadores presencia</li>
        </ol>
    </nav>

<section class="section">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-start g-3 mt-2">
                <!-- Filtro de Asistencia -->
                <div class="col-auto text-center">
                    <label for="filtroAsistencia" class="form-label"><b>Filtrar por asistencia</b></label>
                    <select id="filtroAsistencia" class="form-select" style="min-width: 180px;">
                        <option value="todos">Todos</option>
                        <option value="ha venido">Han venido</option>
                        <option value="no ha venido" selected>No han venido</option>
                    </select>
                </div>

                <!-- Buscador -->
                <div class="col-auto text-center">
                    <label for="buscador" class="form-label"><b>Cod. Trabajador, nombre, asistencia</b></label>
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar..." style="min-width: 250px;">
                </div>

                <!-- Botón de exportar a Excel -->
                <div class="col-auto text-center">
                    <br>
                    <?php 
                        if (isset($params['trabajadores_presencia']) && !empty($params['trabajadores_presencia'])) {
                        ?>
                            <form action="" method="post" id="exportar" class="mb-0">
                                <!-- Se asegura de que los valores de tipo y fecha_inicio se pasen correctamente, ya sea por POST o por GET -->
                                <input type="hidden" name="fecha_inicio" value="<?php echo isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : (isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d')); ?>">
                                <input type="hidden" name="tipo" value="<?php echo isset($_POST['tipo']) ? $_POST['tipo'] : (isset($_GET['tipo']) ? $_GET['tipo'] : 'presencia'); ?>">
                                
                                <!-- Botón para exportar a Excel -->
                                <button type="button" onclick="document.getElementById('exportar').action='exportar.php?sinregistros_excel'; document.getElementById('exportar').submit();" style="background-color: white; border: none;">
                                    <img src="img/xls.png" alt="Exportar a Excel" style="width: 30px;">
                                </button>
                            </form>
                        <?php
                        }
                    ?> 
                </div>
            </div>
        </div>
    </div>





    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Trabajadores presencia</h5>
            <div class="table-container" style="overflow-x: auto;">
                <table class="table table-hover" id="trabajadores_presencia"> 
                    <thead>
                        <tr>
                            <th scope="col">Cod. Trabajador</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Asistencia</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($params['trabajadores_presencia'] as $trabajador) { 
                            ?>
                                <tr>
                                    <td><?php echo $trabajador['A1_PERNR']; ?></td>
                                    <td><?php echo $trabajador['NOMBREYAPELLIDOS']; ?></td>
                                    <td><?php echo $trabajador['Estado']; ?></td>
                                    <td>
                                        <li class="hvr-icon-forward">
                                        <form action="admin_cont.php?controller=index&action=update_trabajador&id=<?php echo $trabajador['A1_PERNR']; ?>&presencia" method="POST">
                                            <!-- Verificar primero en $_POST y luego en $_GET para los valores -->
                                            <input type="hidden" name="tipo" value="<?php echo isset($_POST['tipo']) ? $_POST['tipo'] : (isset($_GET['tipo']) ? $_GET['tipo'] : ''); ?>">
                                            <input type="hidden" name="fecha_inicio" value="<?php echo isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : (isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : date('Y-m-d')); ?>">
                                            
                                            <button type="submit" class="hvr-icon" style="background: none; border: none; padding: 0;">
                                                <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                            </button>
                                        </form>
                                        </li>
                                    </td>
                                </tr>
                            <?php 
                            } 
                        ?>
                    </tbody>
                </table>
            </div>

            <div id="noResultados" class="alert alert-danger alert-dismissible fade show" role="alert">
                No se han encontrado resultados con esos filtros.
            </div>
            
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filtroAsistencia = document.getElementById('filtroAsistencia');
        const buscador = document.getElementById('buscador');
        const filas = document.querySelectorAll('#trabajadores_presencia tbody tr');
        const mensajeNoResultados = document.getElementById('noResultados');
        
        // Función para filtrar las filas según el filtro de asistencia y el buscador
        function filtrarTabla() {
            const filtro = filtroAsistencia.value.trim().toLowerCase();
            const busqueda = buscador.value.trim().toLowerCase();
            let resultadosVisibles = 0; // Contador de resultados visibles
            
            filas.forEach(fila => {
                const estado = fila.cells[2].textContent.trim().toLowerCase(); // Estado de la fila
                const celdas = fila.getElementsByTagName('td');
                let mostrar = true;
                
                // Filtrar por asistencia
                if (filtro !== 'todos' && estado !== filtro) {
                    mostrar = false;
                }
                
                // Filtrar por búsqueda global
                if (mostrar && busqueda) {
                    let coincideBusqueda = false;
                    for (let i = 0; i < celdas.length; i++) {
                        if (celdas[i].textContent.toLowerCase().includes(busqueda)) {
                            coincideBusqueda = true;
                            break;
                        }
                    }
                    if (!coincideBusqueda) {
                        mostrar = false;
                    }
                }
                
                // Mostrar u ocultar la fila dependiendo de los filtros
                if (mostrar) {
                    fila.style.display = '';
                    resultadosVisibles++;
                } else {
                    fila.style.display = 'none';
                }
            });

            // Mostrar u ocultar el mensaje de no resultados
            if (resultadosVisibles === 0) {
                mensajeNoResultados.style.display = 'block';
            } else {
                mensajeNoResultados.style.display = 'none';
            }
        }

        // Event listener para el filtro de asistencia
        filtroAsistencia.addEventListener('change', filtrarTabla);

        // Event listener para el buscador
        buscador.addEventListener('input', filtrarTabla);

        // Inicializar el filtrado
        filtrarTabla();
    });
</script>
<?php
include 'footer.php';
?>