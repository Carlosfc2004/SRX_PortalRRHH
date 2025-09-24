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
    <!-- Filtros y acciones -->
    <div class="card">
        <div class="card-body">
            <div class="row align-items-start g-3 mt-2">

                <!-- Filtro de Asistencia -->
                <div class="col-auto text-center">
                    <label for="filtroAsistencia" class="form-label"><strong>Filtrar por asistencia</strong></label>
                    <select id="filtroAsistencia" class="form-select" style="min-width: 180px;" aria-label="Filtrar por asistencia">
                        <option value="todos">Todos</option>
                        <option value="1">Con registro</option>
                        <option value="0" selected>Sin registro</option>
                    </select>
                </div>

                <!-- Buscador -->
                <div class="col-auto text-center">
                    <label for="buscador" class="form-label"><strong>Cod. Trabajador, nombre, asistencia</strong></label>
                    <input type="text" id="buscador" class="form-control" placeholder="Buscar..." style="min-width: 250px;" aria-label="Buscar trabajador">
                </div>

                <!-- Botón de exportar a Excel -->
                <?php if (!empty($params['trabajadores_presencia'])) { ?>
                    <div class="col-auto text-center d-flex align-items-end">
                        <form action="" method="post" id="exportar" class="mb-0">
                            <input type="hidden" name="fecha_inicio" value="<?= $_POST['fecha_inicio'] ?? $_GET['fecha_inicio'] ?? date('Y-m-d'); ?>">
                            <input type="hidden" name="tipo" value="<?= $_POST['tipo'] ?? $_GET['tipo'] ?? 'presencia'; ?>">

                            <!-- Nuevos inputs ocultos -->
                            <input type="hidden" name="filtroAsistencia" id="exportarFiltro">
                            <input type="hidden" name="buscador" id="exportarBuscador">

                            <button type="button" onclick="enviarExportacion()" style="background-color: white; border: none;" aria-label="Exportar a Excel">
                                <img src="img/xls.png" alt="Exportar a Excel" style="width: 30px;">
                            </button>
                        </form>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>

    <!-- Tabla de trabajadores -->
    <div class="card mt-3">
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
                        <?php foreach ($params['trabajadores_presencia'] as $trabajador) { ?>
                            <tr>
                                <td><?= $trabajador['A1_PERNR']; ?></td>
                                <td><?= $trabajador['NOMBREYAPELLIDOS']; ?></td>
                                <td><?= $trabajador['Estado'] === '1' ? 'Con registro' : 'Sin registro'; ?></td>
                                <td>
                                    <form action="admin_cont.php?controller=index&action=update_trabajador&id=<?= $trabajador['A1_PERNR']; ?>&presencia" method="POST">
                                        <input type="hidden" name="tipo" value="<?= $_POST['tipo'] ?? $_GET['tipo'] ?? ''; ?>">
                                        <input type="hidden" name="fecha_inicio" value="<?= $_POST['fecha_inicio'] ?? $_GET['fecha_inicio'] ?? date('Y-m-d'); ?>">
                                        <button type="submit" class="hvr-icon" style="background: none; border: none; padding: 0;" aria-label="Editar trabajador">
                                            <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div id="noResultados" class="alert alert-danger alert-dismissible fade show mt-3" role="alert" style="display: none;">
                No se han encontrado resultados con esos filtros.
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const filtroAsistencia = document.getElementById('filtroAsistencia');
        const buscador = document.getElementById('buscador');
        const filas = document.querySelectorAll('#trabajadores_presencia tbody tr');
        const mensajeNoResultados = document.getElementById('noResultados');

        function filtrarTabla() {
            const filtro = filtroAsistencia.value.trim().toLowerCase();
            const busqueda = buscador.value.trim().toLowerCase();
            let resultadosVisibles = 0;

            filas.forEach(fila => {
                const estado = fila.cells[2].textContent.trim().toLowerCase();
                const textoFila = fila.textContent.toLowerCase();
                let visible = true;

                if (filtro !== 'todos' && estado !== (filtro === '1' ? 'con registro' : 'sin registro')) {
                    visible = false;
                }

                if (visible && busqueda && !textoFila.includes(busqueda)) {
                    visible = false;
                }

                fila.style.display = visible ? '' : 'none';
                if (visible) resultadosVisibles++;
            });

            mensajeNoResultados.style.display = resultadosVisibles === 0 ? 'block' : 'none';
        }

        filtroAsistencia.addEventListener('change', filtrarTabla);
        buscador.addEventListener('input', filtrarTabla);

        filtrarTabla(); // inicializar
    });

    function enviarExportacion() {
        const filtro = document.getElementById('filtroAsistencia');
        const buscador = document.getElementById('buscador');
        const form = document.getElementById('exportar');

        // Rellenar los campos ocultos
        document.getElementById('exportarFiltro').value = filtro.value;
        document.getElementById('exportarBuscador').value = buscador.value;

        // Establecer acción y enviar
        form.action = 'exportar.php?sinregistros_excel';
        form.submit();
    }
</script>
<?php
include 'footer.php';
?>