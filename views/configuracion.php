<?php
include_once("header.php");
?>


<div class="pagetitle">
    <h1><?php echo $lang['menu18']; ?></h1>
</div>
<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i
                    class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item active"><?php echo $lang['menu18']; ?></li>
    </ol>
</nav>
<section class="section">
    <div class="row">
        <div class="col-lg-12">

            <?php
            if ($_SESSION["tipo_user_surexport_appreclu"] == 1) {
                ?>
                <!-- Formulario para consultar los créditos SMS -->
                <div class="card">
                    <div class="card-body">
                        <h5 class='card-title'><?php echo $lang['credit_disp']; ?></h5>
                        <form method="POST" action="">
                            <button type="submit" class="btn btn-primary"
                                name="consultar"><?php echo $lang['consult_credit']; ?></button>
                        </form>
                    </div>
                </div>
                <?php
            }
            ?>

            <?php
            foreach ($_SESSION["menu_surexport_appreclu"] as $menu_item) {
                if ($_SESSION["tipo_user_surexport_appreclu"] == 1 || isset($menu_item['id_hijo']) && $menu_item['id_hijo'] == 25) {
                    ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class='card-title'>Configuración calendario laboral oficina</h5>

                            <!-- Botón para abrir modal -->
                            <button type="button" class="btn btn-secondary mb-4" data-bs-toggle="modal"
                                data-bs-target="#modalRangoFechas">
                                Añadir rango de fechas
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="modalRangoFechas" tabindex="-1" aria-labelledby="modalRangoFechasLabel"
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalRangoFechasLabel">Añadir rango de fechas</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST"
                                                action="admin_cont.php?controller=index&action=configuracion&añadido"
                                                id="formRangoFechas">
                                                <input type="hidden" name="rango_dias" value="1">
                                                <!-- Tipo día -->
                                                <div class="mb-3">
                                                    <label for="tipoDia" class="form-label">Tipo de día</label>
                                                    <select class="form-select" id="tipoDia" name="tipoDia" required>
                                                        <option value="">Seleccione un tipo de jornada</option>
                                                        <?php
                                                            foreach ($params['tipo_jornadas'] as $tipo) {
                                                                echo "<option value='{$tipo['tipo']}'>{$tipo['descripcion']}</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </div>

                                                <!-- Fecha inicio -->
                                                <div class="mb-3">
                                                    <label for="fechaInicio" class="form-label">Fecha de inicio</label>
                                                    <input type="date" class="form-control" id="fechaInicio" name="fechaInicio"
                                                        required>
                                                </div>

                                                <!-- Fecha fin -->
                                                <div class="mb-3">
                                                    <label for="fechaFin" class="form-label">Fecha de fin</label>
                                                    <input type="date" class="form-control" id="fechaFin" name="fechaFin"
                                                        required disabled>
                                                </div>

                                                <div id="alertaFechaExistente" class="alert alert-danger d-none"></div>
                                                <button type="submit" class="btn btn-primary mt-2">Añadir rango</button>
                                            </form>

                                            <!-- Validaciones -->
                                            <script>
                                                const fechaInicio = document.getElementById("fechaInicio");
                                                const fechaFin = document.getElementById("fechaFin");
                                                const tipoDia = document.getElementById("tipoDia");
                                                const alertaFechaExistente = document.getElementById("alertaFechaExistente");
                                                const btnSubmit = document.querySelector("#formRangoFechas button[type='submit']");

                                                // Inicialmente fecha fin bloqueada
                                                fechaFin.disabled = true;

                                                // Función de comprobación
                                                async function comprobarRango() {
                                                    alertaFechaExistente.classList.add("d-none");
                                                    btnSubmit.style.display = "inline-block";

                                                    if (!fechaInicio.value || !fechaFin.value || !tipoDia.value) return;

                                                    try {
                                                        const res = await fetch("auto.php?comprobar_rango=1", {
                                                            method: "POST",
                                                            headers: { "Content-Type": "application/json" },
                                                            body: JSON.stringify({
                                                                inicio: fechaInicio.value,
                                                                fin: fechaFin.value,
                                                                tipo: tipoDia.value
                                                            })
                                                        });
                                                        const json = await res.json();

                                                        if (json.mensaje) {
                                                            alertaFechaExistente.textContent = json.mensaje;
                                                            alertaFechaExistente.classList.remove("d-none");
                                                            if (json.existe) btnSubmit.style.display = "none";
                                                        }
                                                    } catch (err) {
                                                        console.error("Error en fetch:", err);
                                                    }
                                                }

                                                // Listeners
                                                fechaInicio.addEventListener("change", function () {
                                                    if (fechaInicio.value) {
                                                        fechaFin.disabled = false;
                                                        fechaFin.min = fechaInicio.value;
                                                        if (fechaFin.value && fechaFin.value < fechaInicio.value) {
                                                            fechaFin.value = fechaInicio.value;
                                                        }
                                                    } else {
                                                        fechaFin.disabled = true;
                                                        fechaFin.value = "";
                                                    }
                                                    comprobarRango();
                                                });

                                                fechaFin.addEventListener("change", function () {
                                                    if (fechaFin.value < fechaInicio.value) {
                                                        fechaFin.value = fechaInicio.value;
                                                    }
                                                    comprobarRango();
                                                });

                                                tipoDia.addEventListener("change", comprobarRango);
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Filtro para busqueda de rangos por año -->
                            <form method="POST" action="admin_cont.php?controller=index&action=configuracion"
                                class="row g-3 align-items-end mb-4">
                                <input type="hidden" name="filtro" value="1">

                                <!-- Campo: Año -->
                                <div class="col-md-3">
                                    <label for="filtro_anio" class="form-label"><b>Filtrar por año</b></label>
                                    <?php
                                        $anio = isset($_POST['filtro_anio']) ? (int)$_POST['filtro_anio'] : date('Y');
                                    ?>
                                    <select id="filtro_anio" name="filtro_anio" class="form-select" required>
                                        <option value="">Seleccione un año</option>
                                        <?php
                                            foreach ($params['años'] as $año) {
                                                echo '<option value="' . $año . '" ' . ($anio == $año ? 'selected' : '') . '>' . $año . '</option>';
                                            }
                                        ?>
                                    </select>
                                </div>

                                <!-- Botón Buscar -->
                                <div class="col-md-1">
                                    <button type="submit" class="btn btn-primary w-100">Buscar</button>
                                </div>
                            </form>

                            <!-- Botón para abrir el modal -->
                            <?php 
                            if (isset($params['rango_fechas']) && !empty($params['rango_fechas'])) {
                            ?>
                            <div class="d-flex align-items-center mb-3">
                                <h5 class="card-title mb-0 me-3">Ver calendario laboral</h5>
                                <form id="exportar" method="POST">
                                    <?php
                                    if (isset($_POST['filtro_anio'])) {
                                        $anio = $_POST['filtro_anio'];
                                    } else {
                                        $anio = date('Y');
                                    }
                                    ?>
                                    <input type="hidden" name="anio" value="<?php echo htmlspecialchars($anio); ?>">
                                    <button type="button" class="btn btn-light border" data-bs-toggle="modal"
                                        data-bs-target="#calendarioModal" title="Calendario laboral">
                                        <i class="bi bi-calendar4-week fs-4" style="color: #012970;"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Modal de Calendario -->
                            <div class="modal fade" id="calendarioModal" tabindex="-1" aria-labelledby="calendarioModalLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="calendarioModalLabel">Calendario <?php echo $anio; ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Cerrar"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="calendarios" class="d-flex flex-wrap justify-content-center gap-3">
                                                <div id="loading" class="d-flex justify-content-center" style="top: 80%; left: 70%;">
                                                    <div class="spinner-border" style="width: 50px; height: 50px;" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="leyenda mt-4">
                                                <h4>Leyenda</h4>
                                                <div class="leyenda-item"><span class="color-box"
                                                        style="background-color: #ff0000;"></span>Festivo Nacional</div>
                                                <div class="leyenda-item"><span class="color-box"
                                                        style="background-color: #00b050;"></span>Festivo Autonómico</div>
                                                <div class="leyenda-item"><span class="color-box"
                                                        style="background-color: #e2efda;"></span>Jornada Reducida (08:00 - 15:00)</div>
                                                <div class="leyenda-item"><span class="color-box"
                                                        style="background-color: #feccd4;"></span>Jornada Especial (09:00 - 14:00)</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <?php
                                $anio = isset($_POST['filtro_anio']) ? (int)$_POST['filtro_anio'] : 2025;

                                foreach ($params['rango_fechas'] as &$registro) {
                                    if ($registro['fecha_inicio'] instanceof DateTime) {
                                        $registro['fecha_inicio'] = $registro['fecha_inicio']->format('Y-m-d');
                                    }
                                    if ($registro['fecha_fin'] instanceof DateTime) {
                                        $registro['fecha_fin'] = $registro['fecha_fin']->format('Y-m-d');
                                    }
                                }
                                unset($registro);
                            ?>
                            <script>
                                const registros = <?php echo json_encode($params['rango_fechas']); ?>;

                                const meses = ["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
                                const diasSemana = ["L","M","M","J","V","S","D"];

                                // Devuelve tipo de jornada para una fecha
                                function getTipoDia(fechaStr) {
                                    let tipo = "";
                                    const fecha = new Date(fechaStr + "T00:00:00"); 
                                    for (let r of registros) {
                                        const inicio = new Date(r.fecha_inicio + "T00:00:00");
                                        const fin = new Date(r.fecha_fin + "T00:00:00");
                                        if (fecha >= inicio && fecha <= fin) {
                                            if (r.tipo.startsWith("festivo")) return r.tipo;
                                            if (r.tipo === "especial") tipo = "especial";
                                            else if (r.tipo === "reducida" && tipo === "") tipo = "reducida";
                                        }
                                    }
                                    return tipo;
                                }

                                // Verifica si un año es bisiesto
                                function esBisiesto(year) {
                                    return (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
                                }

                                // Genera calendario de un año
                                function generarCalendario(year) {
                                    const container = document.getElementById("calendarios");
                                    const loading = document.getElementById("loading");

                                    // Mostrar spinner
                                    loading.style.display = "flex";

                                    // Usamos setTimeout para que el spinner se renderice antes de generar el calendario
                                    setTimeout(() => {
                                        container.innerHTML = ""; // Limpiar el spinner y el contenido previo

                                        for (let m = 0; m < 12; m++) {
                                            let daysInMonth = (m === 1) ? (esBisiesto(year) ? 29 : 28) : new Date(year, m + 1, 0).getDate();
                                            const firstDay = new Date(year, m, 1).getDay();

                                            let table = `<div class="calendar" style="font-size:12px"><h3>${meses[m]}</h3><table><tr>`;
                                            diasSemana.forEach(d => table += `<th>${d}</th>`);
                                            table += `</tr><tr>`;

                                            let dayOfWeek = (firstDay === 0 ? 6 : firstDay - 1);
                                            for (let i = 0; i < dayOfWeek; i++) table += `<td></td>`;

                                            for (let day = 1; day <= daysInMonth; day++) {
                                                const fechaStr = `${year}-${String(m+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
                                                let tipo = getTipoDia(fechaStr);

                                                const diaSemana = new Date(year, m, day).getDay();
                                                if (!tipo.startsWith('festivo') && (diaSemana === 0 || diaSemana === 6)) tipo = 'fin_de_semana';

                                                table += `<td class="${tipo}">${day}</td>`;

                                                dayOfWeek++;
                                                if (dayOfWeek % 7 === 0 && day !== daysInMonth) table += `</tr><tr>`;
                                            }

                                            table += `</tr></table></div>`;
                                            container.innerHTML += table;
                                        }
                                    }, 100); // 100ms para que el spinner se muestre antes de renderizar
                                }

                                // Evento para abrir modal
                                const modalEl = document.getElementById('calendarioModal');
                                modalEl.addEventListener('shown.bs.modal', () => {
                                    generarCalendario(<?php echo $anio; ?>);
                                });
                            </script>

                            <?php } ?>


                            <!-- Tabla con el listado de rango de fechas -->
                            <?php if (!empty($params['rango_fechas'])) { ?>
                                <table class="table datatable mt-4">
                                    <thead>
                                        <tr>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Fin</th>
                                            <th>Tipo jornada</th>
                                            <th data-sortable="false"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($params['rango_fechas'] as $rango) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($rango['fecha_inicio']); ?></td>
                                                <td><?php echo htmlspecialchars($rango['fecha_fin']); ?></td>
                                                <td>
                                                    <?php
                                                    if ($rango['tipo'] == 'festivo_nacional') {
                                                        echo "Festivo nacional";
                                                    } elseif ($rango['tipo'] == 'festivo_autonomico') {
                                                        echo "Festivo autonómico";
                                                    } elseif ($rango['tipo'] == 'festivo_local') {
                                                        echo "Festivo local";
                                                    } elseif ($rango['tipo'] == 'reducida') {
                                                        echo "Reducida (08:00 - 15:00)";
                                                    } elseif ($rango['tipo'] == 'especial') {
                                                        echo "Especial (09:00 - 14:00)";
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if (strtotime($rango['fecha_inicio']) > time()) { ?>
                                                        <div style="display: flex; gap: 5px; align-items: center;">
                                                            <!-- Botón de editar -->
                                                            <button class="btn p-0 m-0"
                                                                onclick="abrirModalEditar('<?php echo htmlspecialchars($rango['id']); ?>')">
                                                                <i class="bi bi-pencil-square fs-4" style="color: #012970;"></i>
                                                            </button>

                                                            <!-- Formulario eliminar con alertify.confirm -->
                                                            <form method="post"
                                                                action="admin_cont.php?controller=index&action=configuracion&eliminado"
                                                                style="margin: 0; display: inline;" onsubmit="return confirmarEliminacion(this);">
                                                                <input type="hidden" name="id_rango" value="<?php echo htmlspecialchars($rango['id']); ?>">
                                                                <input type="hidden" name="eliminar_rango" value="1">
                                                                <button type="submit" class="btn p-0 m-0">
                                                                    <i class="bx bxs-trash fs-3" style="color: #99353a;"></i>
                                                                </button>
                                                            </form>
                                                        </div>

                                                        <script>
                                                            function confirmarEliminacion(form) {
                                                                alertify.confirm(
                                                                    'Confirmar eliminación',
                                                                    '¿Estás seguro de que deseas eliminar este rango de fechas?',
                                                                    function () {
                                                                        form.submit();
                                                                    },
                                                                    function () {
                                                                        alertify.error('Eliminación cancelada');
                                                                    }
                                                                );
                                                                return false; 
                                                            }
                                                        </script>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <div class="alert alert-info" role="alert">
                                    No hay rangos de fechas disponibles para el año seleccionado.
                                </div>
                            <?php } ?>


                            <div class="modal fade" id="modalEditar" tabindex="-1" role="dialog"
                                aria-labelledby="modalEditarLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalEditarLabel">Editar Rango de Fechas</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="formEditar" method="post"
                                                action="admin_cont.php?controller=index&action=configuracion&editado"
                                                onsubmit="return confirmarEdicion(this);">

                                                <input type="hidden" name="editar_rango" value="1">
                                                <input type="hidden" id="idRangoEditar" name="id_rango">

                                                <div class="mb-3">
                                                    <label for="tipoDia" class="form-label">Tipo de día</label>
                                                    <select class="form-select" id="tipoDiaEditar" name="tipoDia" required>
                                                        <option value="festivo_nacional">Festivo Nacional</option>
                                                        <option value="festivo_autonomico">Festivo Autonómico</option>
                                                        <option value="reducida">Reducida (08:00 - 15:00)</option>
                                                        <option value="especial">Especial (09:00 - 14:00)</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="fechaInicioEditar" class="form-label">Fecha de inicio</label>
                                                    <input type="date" class="form-control" id="fechaInicioEditar"
                                                        name="fechaInicio" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="fechaFinEditar" class="form-label">Fecha de fin</label>
                                                    <input type="date" class="form-control" id="fechaFinEditar" name="fechaFin"
                                                        required>
                                                </div>

                                                <button type="submit" class="btn btn-primary mt-2">Guardar Cambios</button>
                                            </form>

                                            <script>
                                                function confirmarEdicion(form) {
                                                    alertify.confirm(
                                                        'Confirmar edición',
                                                        '¿Estás seguro de que deseas guardar los cambios en este rango de fechas?',
                                                        function () {
                                                            form.submit(); // ✅ Enviar si confirma
                                                        },
                                                        function () {
                                                            alertify.error('Edición cancelada'); // ❌ Cancelado
                                                        }
                                                    );
                                                    return false; // ✅ Evita envío automático
                                                }
                                            </script>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                function abrirModalEditar(id) {
                                    fetch('auto.php?rango_fechas&id=' + id)
                                        .then(response => {
                                            if (!response.ok) throw new Error('Error HTTP: ' + response.status);
                                            return response.json();
                                        })
                                        .then(data => {
                                            console.log("Datos recibidos:", data);

                                            if (data && !data.error) {
                                                // reset form antes de rellenar
                                                const form = document.getElementById('formEditar');
                                                form.reset();

                                                document.getElementById('idRangoEditar').value = data.id;
                                                document.getElementById('fechaInicioEditar').value = data.fecha_inicio;
                                                document.getElementById('fechaFinEditar').value = data.fecha_fin;
                                                document.getElementById('tipoDiaEditar').value = data.tipo;

                                                // Mostrar modal (elige según versión de Bootstrap)
                                                if (typeof bootstrap !== 'undefined') {
                                                    var modal = new bootstrap.Modal(document.getElementById('modalEditar'));
                                                    modal.show();
                                                } else {
                                                    $('#modalEditar').modal('show');
                                                }
                                            } else {
                                                alertify.error(data.error || 'No se encontraron datos del registro.');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error al obtener los datos:', error);
                                            alertify.error('Error al cargar los datos.');
                                        });
                                }
                            </script>



                        </div>
                    </div>
                    <?php
                    break;
                }
            }
            ?>


        </div>
    </div>
</section>

<script>
    function redirigir() {
        // Obtén los parámetros de la URL actual
        var urlParams = new URLSearchParams(window.location.search);

        // Inicializa la variable para la URL de redirección
        var redirectUrl = '';

        // Verifica si el parámetro 'añadido' existe por rango de fechas
        if (urlParams.has('añadido')) {
            redirectUrl = 'admin_cont.php?controller=index&action=configuracion';
        } else if (urlParams.has('eliminado')) {
            redirectUrl = 'admin_cont.php?controller=index&action=configuracion';
        } else if (urlParams.has('editado')) {
            redirectUrl = 'admin_cont.php?controller=index&action=configuracion';
        }

        // Si se construyó una URL de redirección, realiza la redirección
        if (redirectUrl) {
            window.location.href = redirectUrl;
        }
    }

    // Llama a la función con un retraso de 2 segundos
    setTimeout(redirigir, 2000);
</script>


<?php
include_once("footer.php");
?>