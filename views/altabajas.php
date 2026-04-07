<?php include_once("header.php"); ?>
<!-- El style es para reducir el tamaño del botón seleccionar en la lista -->
<style>
    #tabla_ab_resultados td {
        padding-top: 6px;
        padding-bottom: 6px;
        vertical-align: middle;
    }
</style>

<div class="pagetitle">
    <h1>Alta / Baja de trabajadores</h1>
    <a class="bi bi-arrow-left-square-fill atras" href="admin_cont.php?controller=index&action=home" style="text-decoration: none;"></a>
</div>

<nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="admin_cont.php?controller=index&action=home"><i class="bi bi-house-door"></i></a></li>
        <li class="breadcrumb-item">Trabajadores</li>
        <li class="breadcrumb-item active">Alta - Baja</li>
    </ol>
</nav>

<section class="section profile">
    <div class="card">
        <div class="card-body">
            <div class="profile-overview">
                <h5 class="card-title" >Tipo de solicitud</h5>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label"><b>Tipo</b></label>
                        <select id="ab_tipo" class="form-select" onchange="AltaBajas.onTipoChange()">
                            <option value="">-- Seleccionar --</option>
                            <option value="AL">Alta</option>
                            <option value="BA">Baja</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><b>Subtipo</b></label>
                        <select id="ab_subtipo" class="form-select" disabled onchange="AltaBajas.onSubtipoChange()">
                            <option value="">-- Seleccionar tipo primero --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><b>Sociedad</b></label>
                        <select id="ab_bukrs" class="form-select">
                            <option value="1000">1000 - Huelva</option>
                            <option value="1700">1700 - Levante</option>
                            <option value="2000">2000 - Portugal</option>
                            <option value="3001">3001 - Marruecos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label"><b>Fecha inicio</b></label>
                        <input type="date" id="ab_begda" class="form-control">
                    </div>
                </div>

                <div id="buscar-empleado" class="d-none">
                    <h5 class="card-title">Buscar empleado</h5>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <input type="text" id="ab_search_pernr" class="form-control" placeholder="Cod. Trabajador">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="ab_search_nombre" class="form-control" placeholder="<?php echo $lang['nom_ape']; ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="ab_search_dni" class="form-control" placeholder="DNI / NIE">
                        </div>
                        <div class="col-md-2">
                            <select id="ab_search_sociedad" class="form-select">
                                <option value="">Sociedad</option>
                                <option value="1000">1000 - Huelva</option>
                                <option value="1700">1700 - Levante</option>
                                <option value="2000">2000 - Portugal</option>
                                <option value="3001">3001 - Marruecos</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-2 d-flex gap-2">
                            <button class="btn btn-primary" type="button" onclick="AltaBajas.buscarEmpleado()">
                                Buscar
                            </button>
                            <button class="btn btn-danger" type="button" onclick="AltaBajas.resetBusqueda()">
                                Reset
                            </button>
                        </div>
                    </div>
                    <div id="ab_search_result"></div>
                </div>

                <div id="alta" class="d-none">
                    <h5 class="card-title">Datos del Trabajador</h5>
                    
                    <h5 class="card-title text-secondary pt-0" style="font-size: 16px;">Datos personales</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><b>Nombre</b></label>
                            <input type="text" id="ab_vorna" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><b>Primer apellido</b></label>
                            <input type="text" id="ab_nachn" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><b>Segundo apellido</b></label>
                            <input type="text" id="ab_nach2" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label"><b>Tipo documento</b></label>
                            <select id="ab_tipodocu" class="form-select">
                                <option value="1">DNI / NIE</option>
                                <option value="2">Pasaporte</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>Num. documento</b></label>
                            <input type="text" id="ab_numide" class="form-control" placeholder= "00000000A">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>Fecha nacimiento</b></label>
                            <input type="date" id="ab_gbdat" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>Sexo</b></label>
                            <select id="ab_gesch" class="form-select">
                                <option value="">-- Seleccionar --</option>
                                <option value="1">Masculino</option>
                                <option value="2">Femenino</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="card-title text-secondary border-top mt-3 pt-3" style="font-size: 16px;">Datos organizativos</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><b>Posición</b></label>
                            <select id="ab_plans" class="form-select"><option value="">Cargando...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><b>Grupo personal</b></label>
                            <select id="ab_persg" class="form-select"><option value="">Cargando...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><b>Motivo medida</b></label>
                            <select id="ab_massg" class="form-select"><option value="">Cargando...</option></select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><b>Categoría profesional</b></label>
                            <select id="ab_grupro" class="form-select"><option value="">Cargando...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><b>Tipo contrato</b></label>
                            <select id="ab_tipcon" class="form-select"><option value="">Cargando...</option></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><b>Almacén</b></label>
                            <select id="ab_lgort" class="form-select"><option value="">Cargando...</option></select>
                        </div>
                    </div>

                    <h5 class="card-title text-secondary border-top mt-3 pt-3" style="font-size: 16px;">Dirección y Pago</h5>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label class="form-label"><b>País</b></label>
                            <input type="text" id="ab_land1" class="form-control" placeholder="ES">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label"><b>Población</b></label>
                            <input type="text" id="ab_ort01" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><b>Calle</b></label>
                            <input type="text" id="ab_stras" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label"><b>Vía de pago</b></label>
                            <select id="ab_zlsch" class="form-select"><option value="">Cargando...</option></select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label"><b>IBAN</b></label>
                            <input type="text" id="ab_iban" class="form-control" placeholder="ES0000000000000000000000">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label"><b>Observaciones</b></label>
                            <textarea id="ab_text" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div id="baja" class="d-none">
                    <h5 class="card-title text-danger border-top mt-3 pt-3"><i class="bi bi-person-dash me-2"></i>Confirmar baja</h5>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Vas a crear una solicitud de <strong>BAJA</strong> para: <span class="fw-bold" id="ab_baja_info"></span>
                    </div>
                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label"><b>Observaciones de la baja</b></label>
                            <textarea id="ab_text_baja" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div id="boton-enviar" class="d-none mt-4 pt-3 ">
                    <button class="btn btn-primary" onclick="AltaBajas.abrirConfirmacion()">
                        <i class="bi bi-check-circle me-1"></i> Crear solicitud
                    </button>
                </div>
            </div> 
        </div>
    </div>
</section>

<!-- Modal confirmación envío -->
<div class="modal fade" id="modalConfirmarSolicitud" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Confirmar solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Vas a crear una solicitud de tipo <strong id="modal_confirm_tipo"></strong>.</p>
                <p class="text-muted small">Esta acción enviará los datos a SAP. ¿Confirmar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" onclick="AltaBajas.enviar()">
                    <i class="bi bi-check-circle me-1"></i>Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
<script>
    var AltaBajas = {

        catalogos: null,
        empleadoEncontrado: null,
        resultadosBusqueda: [],

        init: function() {
            document.getElementById('ab_begda').value = new Date().toISOString().substr(0, 10);
            this.cargarCatalogos();
        },

        cargarDireccion: function(pernr) {
            fetch('auto.php?datos_direccion=1&id=' + encodeURIComponent(pernr))
                .then(function(r) { return r.json(); })
                .then(function(result) {
                    if (!result.success || !result.data) return;
                    var d = result.data;
                    if (document.getElementById('ab_land1') && d.PAIS)         document.getElementById('ab_land1').value = d.PAIS;
                    if (document.getElementById('ab_ort01') && d.POBLACION)     document.getElementById('ab_ort01').value = d.POBLACION;
                    if (document.getElementById('ab_stras') && d.CALLE_NUMERO)  document.getElementById('ab_stras').value = d.CALLE_NUMERO;
                    if (document.getElementById('ab_pstlz') && d.COD_POSTAL)    document.getElementById('ab_pstlz').value = d.COD_POSTAL;
                })
                .catch(function() {});
        },

        cargarCatalogos: function() {
            fetch('auto.php?catalogos_altabajas=1')
                .then(function(r) { return r.json(); })
                .then(function(result) {
                    if (!result.success) return;
                    AltaBajas.catalogos = result;
                    AltaBajas.llenarSelects();
                })
                .catch(function(e) { console.error('Error cargando catálogos:', e); });
        },

        // Rellenar selects con datos de catálogo obtenidos de SAP
        llenarSelects: function() {
            var c = this.catalogos;
            if (!c) return;
            this.fillSelect('ab_plans',  c.posiciones,     'PLANS',    'STEXT');
            this.fillSelect('ab_massg',  c.motivo_medida,  'MASSG',    'MGTXT');
            this.fillSelect('ab_tipcon', c.tipo_contrato,  'ZZTIPCON', 'PES_TIPO');
            this.fillSelect('ab_lgort',  c.almacenes,      'LGORT',    'LGOBE');
            this.fillSelect('ab_zlsch',  c.via_pago,       'ZLSCH',    'TEXT1');
            this.fillSelect('ab_grupro', c.cat_profesional || [], 'ZZGRUPRO', 'PTEXT');

            if (c.area_personal && c.area_personal.length > 0) {
                var sel = document.getElementById('ab_persg');
                sel.innerHTML = '<option value="">-- Seleccionar --</option>';
                c.area_personal.forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value = (item.PERSG || '') + '|' + (item.PERSK || '') + '|' + (item.ABKRS || '');
                    opt.textContent = (item.PTEXT || '') + ' (' + (item.PERSG || '') + '/' + (item.PERSK || '') + ')';
                    sel.appendChild(opt);
                });
            }
        },

        // Función genérica para rellenar un select a partir de un array de objetos
        fillSelect: function(id, items, valKey, labelKey) {
            var sel = document.getElementById(id);
            if (!sel || !items) return;
            sel.innerHTML = '<option value="">-- Seleccionar --</option>';
            items.forEach(function(item) {
                var opt = document.createElement('option');
                opt.value = item[valKey] || '';
                opt.textContent = (item[valKey] || '') + ' - ' + (item[labelKey] || '');
                sel.appendChild(opt);
            });
        },

        onTipoChange: function() {
            var tipo = document.getElementById('ab_tipo').value;
            var subSel = document.getElementById('ab_subtipo');
            subSel.innerHTML = '<option value="">-- Seleccionar --</option>';
            subSel.disabled = !tipo;

            var subtipos = {
                'AL': [['AL1', 'Alta Nueva'], ['AL2', 'Alta Reingreso']],
                'BA': [['BA',  'Baja']]
            };
            (subtipos[tipo] || []).forEach(function(s) {
                var opt = document.createElement('option');
                opt.value = s[0]; opt.textContent = s[1];
                subSel.appendChild(opt);
            });
            this.resetPasos();
        },

        onSubtipoChange: function() {
            var subtipo = document.getElementById('ab_subtipo').value;
            this.resetPasos();
            if (!subtipo) return;
            if (subtipo === 'AL1') {
                document.getElementById('alta').classList.remove('d-none');
                document.getElementById('boton-enviar').classList.remove('d-none');
            } else {
                document.getElementById('buscar-empleado').classList.remove('d-none');
            }
        },

        resetPasos: function() {
            ['buscar-empleado','alta','baja','boton-enviar'].forEach(function(id) {
                document.getElementById(id).classList.add('d-none');
            });
            document.getElementById('ab_search_result').innerHTML = '';
            this.empleadoEncontrado = null;
        },

        // Buscar empleado por PERNR o nombre (se pueden combinar ambos criterios) haciendo una consulta a SAP a través de auto.php
        buscarEmpleado: function() {
            var pernr    = document.getElementById('ab_search_pernr').value.trim();
            var nombre   = document.getElementById('ab_search_nombre').value.trim();
            var sociedad = document.getElementById('ab_search_sociedad').value;
            var dni      = document.getElementById('ab_search_dni').value.trim();

            if (!pernr && !nombre && !dni) { alertify.warning('Introduce al menos un criterio de búsqueda'); return; }

            document.getElementById('ab_search_result').innerHTML =
                '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div><span class="text-muted">Buscando...</span>';

            var url = 'auto.php?buscar_trabajador_altabajas=1'
                + '&pernr='    + encodeURIComponent(pernr)
                + '&nombre='   + encodeURIComponent(nombre)
                + '&sociedad=' + encodeURIComponent(sociedad)
                + '&dni='      + encodeURIComponent(dni);

            fetch(url)
                .then(function(r) { return r.json(); })
                .then(function(result) {
                    if (!result.success || !result.data || result.data.length === 0) {
                        document.getElementById('ab_search_result').innerHTML =
                            '<div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-1"></i>No se encontraron trabajadores</div>';
                        return;
                    }
                    AltaBajas.renderTablaResultados(result.data);
                })
                .catch(function() {
                    document.getElementById('ab_search_result').innerHTML =
                        '<div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-1"></i>Error al buscar</div>';
                });
        },

        renderTablaResultados: function(trabajadores) {
            // Guardar trabajadores para acceder por índice
            this.resultadosBusqueda = trabajadores;

            var html = '<table class="table table-hover" id="tabla_ab_resultados">'
                + '<thead><tr>'
                + '<th>Cod. Trabajador</th><th>Nombre</th><th>DNI</th><th></th>'
                + '</tr></thead><tbody>';

            trabajadores.forEach(function(t, i) {
                var nombre = '';
                if (t.NACHN && t.VORNA) {
                    nombre = t.NACHN + (t.NACH2 ? ' ' + t.NACH2 : '') + ', ' + t.VORNA;
                } else if (t.SNAME_CALC) {
                    nombre = t.SNAME_CALC;
                }
                var dni = t.PERID || '--';

                var begda = (t.BEGDA_MEDIDA || '').toString();
                var endda = (t.ENDDA_MEDIDA || '').toString();
                var hoy   = new Date().toISOString().substr(0, 10);
                var begdaF = begda.length === 8 ? begda.substr(0,4)+'-'+begda.substr(4,2)+'-'+begda.substr(6,2) : '';
                var enddaF = endda.length === 8 ? endda.substr(0,4)+'-'+endda.substr(4,2)+'-'+endda.substr(6,2) : '';
                var activo = t.STAT2 == '3' && begdaF && begdaF <= hoy && enddaF && (enddaF >= hoy || enddaF === '9999-12-31');
                var circulo = activo
                    ? '<i class="bi bi-circle-fill me-1" style="color:green;"></i>'
                    : '<i class="bi bi-circle-fill me-1" style="color:red;"></i>';

                html += '<tr>'
                    + '<td>' + circulo + (t.PERNR || '') + '</td>'
                    + '<td>' + nombre + '</td>'
                    + '<td>' + dni + '</td>'
                    + '<td><button class="btn btn-sm btn-primary" onclick="AltaBajas.seleccionarEmpleado(' + i + ')">Seleccionar</button></td>'
                    + '</tr>';
            });

            html += '</tbody></table>';
            document.getElementById('ab_search_result').innerHTML = html;

            setTimeout(function() {
                if (typeof simpleDatatables !== 'undefined' && typeof simpleDatatables.DataTable !== 'undefined') {
                    new simpleDatatables.DataTable('#tabla_ab_resultados', {
                    perPage: 10,
                    perPageSelect: [10, 20, 30],
                    columns: [
                        { select: 3, sortable: false }
                    ],
                    labels: {
                        placeholder: 'Buscar...',
                        perPage: 'Resultados por página',
                        noRows: 'No se encontraron resultados',
                        info: 'Mostrando {start} a {end} de {rows} resultados'
                    }
                });
                }
            }, 0);
        },

        seleccionarEmpleado: function(idx) {
            var emp = this.resultadosBusqueda[idx];
            var subtipo = document.getElementById('ab_subtipo').value;

            // Validar si es baja y el empleado está inactivo
            if (subtipo === 'BA') {
                var begda = (emp.BEGDA_MEDIDA || '').toString();
                var endda = (emp.ENDDA_MEDIDA || '').toString();
                var hoy   = new Date().toISOString().substr(0, 10);
                var begdaF = begda.length === 8 ? begda.substr(0,4)+'-'+begda.substr(4,2)+'-'+begda.substr(6,2) : '';
                var enddaF = endda.length === 8 ? endda.substr(0,4)+'-'+endda.substr(4,2)+'-'+endda.substr(6,2) : '';
                var activo = emp.STAT2 == '3' && begdaF && begdaF <= hoy && enddaF && (enddaF >= hoy || enddaF === '9999-12-31');

                if (!activo) {
                    alertify.error('No se puede dar de baja a un empleado que ya está inactivo');
                    return;
                }
            }

            this.empleadoEncontrado = emp;
            var nombre = (emp.NACHN || '') + (emp.NACH2 ? ' ' + emp.NACH2 : '') + ', ' + (emp.VORNA || '');
            document.getElementById('ab_search_result').innerHTML =
                '<div class="alert alert-info mb-0 mt-2">'
                + '<i class="bi bi-person-check me-1"></i>'
                + '<strong>' + nombre + '</strong>'
                + ' &mdash; PERNR: <code>' + (emp.PERNR || '') + '</code>'
                + ' <button class="btn btn-sm btn-outline-secondary ms-2" onclick="AltaBajas.resetBusqueda()"><i class="bi bi-x"></i> Cambiar</button>'
                + '</div>';

            var subtipo = document.getElementById('ab_subtipo').value;
            if (subtipo === 'BA') {
                document.getElementById('ab_baja_info').textContent = nombre + ' (PERNR: ' + (emp.PERNR || '') + ')';
                document.getElementById('baja').classList.remove('d-none');
            } else {
                AltaBajas.rellenarFormulario(emp);
                document.getElementById('alta').classList.remove('d-none');
            }
            document.getElementById('boton-enviar').classList.remove('d-none');
        },

        resetBusqueda: function() {
            document.getElementById('ab_search_pernr').value  = '';
            document.getElementById('ab_search_nombre').value = '';
            document.getElementById('ab_search_dni').value = '';
            document.getElementById('ab_search_result').innerHTML = '';
            document.getElementById('alta').classList.add('d-none');
            document.getElementById('baja').classList.add('d-none');
            document.getElementById('boton-enviar').classList.add('d-none');
            this.empleadoEncontrado = null;
        },

        // Rellenar formulario de alta con datos del empleado encontrado (en caso de reingreso)
        rellenarFormulario: function(emp) {
            // Datos personales
            if (document.getElementById('ab_vorna'))   document.getElementById('ab_vorna').value  = emp.VORNA  || '';
            if (document.getElementById('ab_nachn'))   document.getElementById('ab_nachn').value  = emp.NACHN  || '';
            if (document.getElementById('ab_nach2'))   document.getElementById('ab_nach2').value  = emp.NACH2  || '';
            if (document.getElementById('ab_numide'))  document.getElementById('ab_numide').value = emp.PERID  || '';
            if (document.getElementById('ab_land1'))   document.getElementById('ab_land1').value  = emp.WERKS === '2000' ? 'PT' : (emp.WERKS === '3001' ? 'MA' : 'ES');
            if (document.getElementById('ab_natio'))   document.getElementById('ab_natio').value  = emp.NATIO  || '';
            if (document.getElementById('ab_stras'))   document.getElementById('ab_stras').value  = emp.STRAS  || '';
            if (document.getElementById('ab_ort01'))   document.getElementById('ab_ort01').value  = emp.ORT01  || '';
            if (document.getElementById('ab_pstlz'))   document.getElementById('ab_pstlz').value  = emp.PSTLZ  || '';
            if (document.getElementById('ab_iban'))    document.getElementById('ab_iban').value   = emp.IBAN   || '';

            // Tipo documento
            if (document.getElementById('ab_tipodocu')) {
                if (emp.DDTEXT && emp.DDTEXT.indexOf('PAS') !== -1) {
                    document.getElementById('ab_tipodocu').value = '2';
                } else {
                    document.getElementById('ab_tipodocu').value = '1';
                }
            }

            // Sexo
            if (document.getElementById('ab_gesch')) {
                document.getElementById('ab_gesch').value = emp.GESCH || '';
            }

            // Fecha nacimiento
            if (emp.GBDAT && emp.GBDAT.length === 8) {
                if (document.getElementById('ab_gbdat')) {
                    document.getElementById('ab_gbdat').value =
                        emp.GBDAT.substr(0,4) + '-' + emp.GBDAT.substr(4,2) + '-' + emp.GBDAT.substr(6,2);
                }
            }

            // Datos organizativos — rellenar si los catálogos están cargados
            if (emp.PLANS && document.getElementById('ab_plans')) {
                document.getElementById('ab_plans').value = emp.PLANS;
            }
            if (emp.MASSG && document.getElementById('ab_massg')) {
                document.getElementById('ab_massg').value = emp.MASSG;
            }
            if (emp.ZZLGORT && document.getElementById('ab_lgort')) {
                document.getElementById('ab_lgort').value = emp.ZZLGORT;
            }

            // Cargar la direccion del empleado
            if (emp.PERNR) {
                this.cargarDireccion(emp.PERNR);
            }
        },

        abrirConfirmacion: function() {
            var tipo    = document.getElementById('ab_tipo').value;
            var subtipo = document.getElementById('ab_subtipo').value;
            if (!tipo || !subtipo) { alertify.warning('Selecciona tipo y subtipo'); return; }
            document.getElementById('modal_confirm_tipo').textContent = tipo + ' / ' + subtipo;
            new bootstrap.Modal(document.getElementById('modalConfirmarSolicitud')).show();
        },

        enviar: function() {
            var tipo    = document.getElementById('ab_tipo').value;
            var subtipo = document.getElementById('ab_subtipo').value;
            var data    = {};

            if (subtipo === 'BA') {
                data = {
                    PERNR:  this.empleadoEncontrado ? (this.empleadoEncontrado.PERNR || '') : '',
                    BEGDA:  document.getElementById('ab_begda').value.replace(/-/g, ''),
                    BUKRS:  document.getElementById('ab_bukrs').value,
                    ZZTEXT: document.getElementById('ab_text_baja').value
                };
            } else {
                var persg_parts = (document.getElementById('ab_persg').value || '').split('|');
                data = {
                    BEGDA:      document.getElementById('ab_begda').value.replace(/-/g, ''),
                    BUKRS:      document.getElementById('ab_bukrs').value,
                    VORNA:      document.getElementById('ab_vorna').value,
                    NACHN:      document.getElementById('ab_nachn').value,
                    NACH2:      document.getElementById('ab_nach2').value,
                    ZZTIPODOCU: document.getElementById('ab_tipodocu').value,
                    ZZNUMIDE:   document.getElementById('ab_numide').value,
                    GBDAT:      document.getElementById('ab_gbdat').value.replace(/-/g, ''),
                    NATIO:      document.getElementById('ab_natio') ? document.getElementById('ab_natio').value : '',
                    GESCH:      document.getElementById('ab_gesch').value,
                    PLANS:      document.getElementById('ab_plans').value,
                    PERSG:      persg_parts[0] || '',
                    PERSK:      persg_parts[1] || '',
                    ABKRS:      persg_parts[2] || '',
                    ZZGRUPRO:   document.getElementById('ab_grupro').value,
                    ZZTIPCON:   document.getElementById('ab_tipcon').value,
                    MASSG:      document.getElementById('ab_massg').value,
                    ZZLGORT:    document.getElementById('ab_lgort').value,
                    LAND1:      document.getElementById('ab_land1').value,
                    ORT01:      document.getElementById('ab_ort01').value,
                    PSTLZ:      document.getElementById('ab_pstlz') ? document.getElementById('ab_pstlz').value : '',
                    STRAS:      document.getElementById('ab_stras').value,
                    ZLSCH:      document.getElementById('ab_zlsch').value,
                    IBAN:       document.getElementById('ab_iban').value,
                    ZZTEXT:     document.getElementById('ab_text').value
                };
                if (subtipo === 'AL2' && this.empleadoEncontrado) {
                    data.PERNR = this.empleadoEncontrado.PERNR || '';
                }
            }

            bootstrap.Modal.getInstance(document.getElementById('modalConfirmarSolicitud')).hide();

            fetch('auto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ crear_solicitud_altabajas: 1, tipo: tipo, subtipo: subtipo, data: data })
            })
            .then(function(r) { return r.json(); })
            .then(function(result) {
                if (result.success) {
                    alertify.success(result.message || 'Solicitud creada correctamente');
                    setTimeout(function() { window.location.reload(); }, 2000);
                } else {
                    alertify.error(result.message || result.error || 'Error al crear la solicitud');
                }
            })
            .catch(function() { alertify.error('Error al enviar la solicitud'); });
        }
    };

    document.addEventListener('DOMContentLoaded', function() { AltaBajas.init(); });
</script>

</main>
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="js/alertify.min.js"></script>
<script src="js/script.js?ver=1.7"></script>
</html>