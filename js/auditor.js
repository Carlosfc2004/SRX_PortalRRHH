    setTimeout(function () {
        if (window.location.href.indexOf('validado') !== -1) {
            window.location.href = 'admin_cont.php?controller=index&action=auditor';
        }
    }, 3000);

    // JavaScript para el modal de nuevo registro
    let registrosData = {};  // Cambiar a objeto para agrupar por trabajador

    // Inicializar autocompletado para el modal
    document.addEventListener('DOMContentLoaded', function() {
        const trabajadoresInput = document.getElementById('trabajadoresNuevoRegistro');
        const sugerenciasDiv = document.getElementById('sugerencias_trabajadores');
        const pernrHidden = document.getElementById('pernr_seleccionado');
        
        // Datos de trabajadores desde PHP
        const trabajadores = window.trabajadoresAuditoria || [];
        
        // Inicializar fecha anterior
        const fechaInput = document.getElementById('fechaRegistro');
        if (fechaInput) {
            fechaAnterior = fechaInput.value;
        }
        
        // Función para filtrar trabajadores
        function filtrarTrabajadores(termino) {
            if (termino.length < 2) {
                sugerenciasDiv.style.display = 'none';
                return;
            }
            
            const filtrados = trabajadores.filter(trabajador => {
                const textoCompleto = trabajador.pernr + ' - ' + trabajador.NOMBREYAPELLIDOS;
                return textoCompleto.toLowerCase().includes(termino.toLowerCase());
            });
            
            mostrarSugerencias(filtrados);
        }
        
        // Función para mostrar sugerencias
        function mostrarSugerencias(trabajadoresFiltrados) {
            sugerenciasDiv.innerHTML = '';
            
            if (trabajadoresFiltrados.length === 0) {
                sugerenciasDiv.style.display = 'none';
                return;
            }
            
            trabajadoresFiltrados.forEach(trabajador => {
                const item = document.createElement('div');
                item.className = 'list-group-item list-group-item-action';
                item.style.cursor = 'pointer';
                item.textContent = trabajador.pernr + ' - ' + trabajador.NOMBREYAPELLIDOS;
                
                item.addEventListener('click', function() {
                    trabajadoresInput.value = trabajador.pernr + ' - ' + trabajador.NOMBREYAPELLIDOS;
                    pernrHidden.value = trabajador.pernr;
                    sugerenciasDiv.style.display = 'none';
                    actualizarTablaRegistrosExistentes();
                });
                
                sugerenciasDiv.appendChild(item);
            });
            
            sugerenciasDiv.style.display = 'block';
        }
        
        // Event listeners
        trabajadoresInput.addEventListener('input', function() {
            filtrarTrabajadores(this.value);
        });
        
        trabajadoresInput.addEventListener('blur', function() {
            setTimeout(() => {
                sugerenciasDiv.style.display = 'none';
            }, 200);
        });
        
        trabajadoresInput.addEventListener('focus', function() {
            if (this.value.length >= 2) {
                filtrarTrabajadores(this.value);
            }
        });

        // Limpiar formulario al cerrar modal
        $('#modalNuevoRegistro').on('hidden.bs.modal', function () {
            limpiarFormulario();
        });
    });

    // Función para mostrar registros existentes en una tabla simple
    function mostrarRegistrosExistentesEnTabla(registrosData) {
        const contenedor = document.getElementById('contenedorRegistrosExistentes');
        const tablaExistentes = document.getElementById('tablaRegistrosExistentes');
        
        contenedor.innerHTML = '';
        
        // Obtener el PERNR del trabajador seleccionado
        const pernrHidden = document.getElementById('pernr_seleccionado');
        let pernr = pernrHidden.value;
        
        if (!pernr) {
            // Si no hay PERNR en el campo oculto, intentar extraerlo del input
            const trabajadorInput = document.getElementById('trabajadoresNuevoRegistro');
            if (trabajadorInput.value) {
                const match = trabajadorInput.value.match(/^(\d+)\s*-\s*/);
                if (match) {
                    pernr = match[1];
                    pernrHidden.value = pernr;
                }
            }
        }
        
        if (!pernr || !registrosData.registros[pernr]) {
            tablaExistentes.style.display = 'none';
            return;
        }
        
        const registros = registrosData.registros[pernr];
        
        if (registros.length === 0) {
            // Mostrar mensaje de que no hay registros
            contenedor.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No hay registros existentes para esta fecha.
                </div>
            `;
            tablaExistentes.style.display = 'block';
            return;
        }
        
        // Crear tabla simple
        const tabla = document.createElement('table');
        tabla.className = 'table table-sm table-bordered table-striped';
        tabla.innerHTML = `
            <thead class="table-primary">
                <tr>
                    <th width="30%">Hora</th>
                    <th width="25%">Tipo</th>
                    <th width="45%">Ubicación</th>
                </tr>
            </thead>
            <tbody></tbody>
        `;
        
        const cuerpoTabla = tabla.querySelector('tbody');
        
        // Ordenar registros por hora
        const registrosOrdenados = [...registros].sort((a, b) => {
            const fechaA = obtenerFechaRegistro(a);
            const fechaB = obtenerFechaRegistro(b);
            return fechaA - fechaB;
        });
        
        registrosOrdenados.forEach(registro => {
            // Obtener hora del registro
            let hora = obtenerHoraDeRegistro(registro);
            if (!hora) hora = 'N/A';
            
            // Determinar color según el tipo
            let tipoBadge = 'secondary';
            
            switch(registro.tipo_reg) {
                case 'entrada':
                    tipoBadge = 'success';
                    break;
                case 'salida':
                    tipoBadge = 'danger';
                    break;
                case 'inicio-desayuno':
                case 'fin-desayuno':
                    tipoBadge = 'warning';
                    break;
                case 'inicio-almuerzo':
                case 'fin-almuerzo':
                    tipoBadge = 'info';
                    break;
                default:
                    tipoBadge = 'secondary';
                    break;
            }
            
            // Formatear tipo para mostrar
            let tipoTexto = registro.tipo_reg.toUpperCase().replace('-', ' ');
            
            const fila = document.createElement('tr');
            fila.innerHTML = `
                <td><strong>${hora}</strong></td>
                <td><span class="badge bg-${tipoBadge}">${tipoTexto}</span></td>
                <td><small class="text-muted">${registro.nombre_ubi || 'No especificada'}</small></td>
            `;
            
            cuerpoTabla.appendChild(fila);
        });
        
        contenedor.appendChild(tabla);
        tablaExistentes.style.display = 'block';
    }

    // Función para actualizar la tabla de registros existentes y autocompletar campos
    async function actualizarTablaRegistrosExistentes() {
        const trabajadorSelect = document.getElementById('trabajadoresNuevoRegistro');
        const fechaInput = document.getElementById('fechaRegistro');
        const pernrHidden = document.getElementById('pernr_seleccionado');
        
        // Si no hay trabajador o fecha seleccionados, ocultar tabla y limpiar autocompletado
        if (!trabajadorSelect.value || !fechaInput.value) {
            document.getElementById('tablaRegistrosExistentes').style.display = 'none';
            limpiarAutocompletado();
            return;
        }
        
        try {
            // Extraer el PERNR del valor del trabajador seleccionado
            let pernr = pernrHidden.value;
            if (!pernr && trabajadorSelect.value) {
                // Extraer PERNR del formato "PERNR - NOMBRE"
                const match = trabajadorSelect.value.match(/^(\d+)\s*-\s*/);
                if (match) {
                    pernr = match[1];
                    pernrHidden.value = pernr;
                }
            }
            
            if (!pernr) {
                console.error('No se pudo extraer el PERNR del trabajador seleccionado');
                document.getElementById('tablaRegistrosExistentes').style.display = 'none';
                limpiarAutocompletado();
                return;
            }
            
            const registrosExistentes = await verificarRegistrosExistentes([pernr], fechaInput.value);
            
            if (registrosExistentes.success) {
                // Mostrar registros existentes en la tabla
                mostrarRegistrosExistentesEnTabla(registrosExistentes);
                
                // Guardar registros para autocompletado
                window.registrosActuales = registrosExistentes.registros[pernr] || [];
                
                // Aplicar autocompletado si hay un tipo seleccionado
                const tipoSeleccionado = document.getElementById('tipoRegistro').value;
                if (tipoSeleccionado) {
                    aplicarAutocompletado(tipoSeleccionado);
                }
            }
        } catch (error) {
            console.error('Error al actualizar tabla de registros:', error);
            document.getElementById('tablaRegistrosExistentes').style.display = 'none';
            limpiarAutocompletado();
        }
    }
    

    // Función para aplicar autocompletado basado en registros existentes
    function aplicarAutocompletado(tipo) {
        if (!window.registrosActuales || window.registrosActuales.length === 0) {
            return;
        }

        // Limpiar campos antes de autocompletar
        limpiarCamposHora();
        limpiarCamposAutocompletados();

        switch(tipo) {
            case 'entrada_salida':
                autocompletarEntradaSalida();
                break;
            case 'desayuno':
                autocompletarDescanso('desayuno');
                break;
            case 'almuerzo':
                autocompletarDescanso('almuerzo');
                break;
            case 'otros':
                autocompletarDescanso('otros');
                break;
        }
    }

    // Función para autocompletar entrada/salida (solo si hay registros incompletos)
    function autocompletarEntradaSalida() {
        const registros = window.registrosActuales;
        
        // Buscar todas las entradas y salidas
        const entradas = [];
        const salidas = [];
        
        registros.forEach(registro => {
            const hora = obtenerHoraDeRegistro(registro);
            if (registro.tipo_reg === 'entrada') {
                entradas.push(hora);
            } else if (registro.tipo_reg === 'salida') {
                salidas.push(hora);
            }
        });

        // Verificar si hay registros incompletos
        let entradaSinSalida = null;
        let salidaSinEntrada = null;

        // Buscar entradas que no tienen salida correspondiente
        entradas.forEach(entrada => {
            const tieneSalidaCorrespondiente = salidas.some(salida => {
                // Verificar si hay una salida posterior a esta entrada
                const minutosEntrada = horaAMinutos(entrada);
                const minutosSalida = horaAMinutos(salida);
                return minutosSalida > minutosEntrada;
            });
            
            if (!tieneSalidaCorrespondiente && !entradaSinSalida) {
                entradaSinSalida = entrada;
            }
        });

        // Buscar salidas que no tienen entrada correspondiente
        salidas.forEach(salida => {
            const tieneEntradaCorrespondiente = entradas.some(entrada => {
                // Verificar si hay una entrada anterior a esta salida
                const minutosEntrada = horaAMinutos(entrada);
                const minutosSalida = horaAMinutos(salida);
                return minutosEntrada < minutosSalida;
            });
            
            if (!tieneEntradaCorrespondiente && !salidaSinEntrada) {
                salidaSinEntrada = salida;
            }
        });

        // Solo autocompletar si hay registros verdaderamente incompletos
        if (entradaSinSalida && !salidaSinEntrada) {
            // Hay una entrada sin salida correspondiente - autocompletar entrada y deshabilitarla
            const entradaInput = document.querySelector('input[name="entrada_hora"]');
            entradaInput.value = entradaSinSalida;
            entradaInput.disabled = true;
            entradaInput.style.backgroundColor = '#f8f9fa';
            entradaInput.setAttribute('data-autocompletado', 'true');
            // alertify.info(`Autocompletado: Entrada (${entradaSinSalida}). Añade la salida para completar el registro.`);
        } else if (salidaSinEntrada && !entradaSinSalida) {
            // Hay una salida sin entrada correspondiente - autocompletar salida y deshabilitarla
            const salidaInput = document.querySelector('input[name="salida_hora"]');
            salidaInput.value = salidaSinEntrada;
            salidaInput.disabled = true;
            salidaInput.style.backgroundColor = '#f8f9fa';
            salidaInput.setAttribute('data-autocompletado', 'true');
            // alertify.info(`Autocompletado: Salida (${salidaSinEntrada}). Añade la entrada para completar el registro.`);
        }
        // Si todos los registros están completos o no hay registros, dejar campos vacíos
    }

    // Función para autocompletar descansos (solo si hay registros incompletos)
    function autocompletarDescanso(tipo) {
        const registros = window.registrosActuales;
        
        // Buscar todos los inicios y fines del tipo específico
        const inicios = [];
        const fines = [];
        
        registros.forEach(registro => {
            const hora = obtenerHoraDeRegistro(registro);
            if (registro.tipo_reg === `inicio-${tipo}`) {
                inicios.push(hora);
            } else if (registro.tipo_reg === `fin-${tipo}`) {
                fines.push(hora);
            }
        });

        // Verificar si hay registros incompletos
        let inicioSinFin = null;
        let finSinInicio = null;

        // Buscar inicios que no tienen fin correspondiente
        inicios.forEach(inicio => {
            const tieneFinCorrespondiente = fines.some(fin => {
                // Verificar si hay un fin posterior a este inicio
                const minutosInicio = horaAMinutos(inicio);
                const minutosFin = horaAMinutos(fin);
                return minutosFin > minutosInicio;
            });
            
            if (!tieneFinCorrespondiente && !inicioSinFin) {
                inicioSinFin = inicio;
            }
        });

        // Buscar fines que no tienen inicio correspondiente
        fines.forEach(fin => {
            const tieneInicioCorrespondiente = inicios.some(inicio => {
                // Verificar si hay un inicio anterior a este fin
                const minutosInicio = horaAMinutos(inicio);
                const minutosFin = horaAMinutos(fin);
                return minutosInicio < minutosFin;
            });
            
            if (!tieneInicioCorrespondiente && !finSinInicio) {
                finSinInicio = fin;
            }
        });

        // Solo autocompletar si hay registros verdaderamente incompletos
        if (inicioSinFin && !finSinInicio) {
            // Hay un inicio sin fin correspondiente - autocompletar inicio y deshabilitarlo
            const inicioInput = document.querySelector(`input[name="${tipo}_inicio_hora"]`);
            inicioInput.value = inicioSinFin;
            inicioInput.disabled = true;
            inicioInput.style.backgroundColor = '#f8f9fa';
            inicioInput.setAttribute('data-autocompletado', 'true');
            // alertify.info(`Autocompletado ${tipo}: Inicio (${inicioSinFin}). Añade el fin para completar el registro.`);
        } else if (finSinInicio && !inicioSinFin) {
            // Hay un fin sin inicio correspondiente - autocompletar fin y deshabilitarlo
            const finInput = document.querySelector(`input[name="${tipo}_fin_hora"]`);
            finInput.value = finSinInicio;
            finInput.disabled = true;
            finInput.style.backgroundColor = '#f8f9fa';
            finInput.setAttribute('data-autocompletado', 'true');
            // alertify.info(`Autocompletado ${tipo}: Fin (${finSinInicio}). Añade el inicio para completar el registro.`);
        }
        // Si todos los registros están completos o no hay registros, dejar campos vacíos
    }

    // Función para limpiar campos autocompletados
    function limpiarCamposAutocompletados() {
        const camposFormulario = [
            'input[name="entrada_hora"]',
            'input[name="salida_hora"]',
            'input[name="almuerzo_inicio_hora"]',
            'input[name="almuerzo_fin_hora"]',
            'input[name="descanso_inicio_hora"]',
            'input[name="descanso_fin_hora"]'
        ];

        camposFormulario.forEach(selector => {
            const campo = document.querySelector(selector);
            if (campo) {
                campo.disabled = false;
                campo.style.backgroundColor = '';
                campo.removeAttribute('data-autocompletado');
            }
        });
    }

    // Función para verificar si hay campos autocompletados en el formulario
    function validarCamposAutocompletados() {
        const camposAutocompletados = document.querySelectorAll('input[data-autocompletado="true"]');
        
        camposAutocompletados.forEach(campo => {
            // Excluir campos autocompletados del envío
            campo.value = '';
            campo.removeAttribute('name');
        });
    }

    // Función auxiliar para obtener hora de un registro
    function obtenerHoraDeRegistro(registro) {
        const fechaObj = registro.fecha_reg?.date ? registro.fecha_reg : registro.fecha;
        
        if (fechaObj && fechaObj.date) {
            const fechaStr = fechaObj.date.split('.')[0].replace(' ', 'T');
            const fecha = new Date(fechaStr);
            if (!isNaN(fecha.getTime())) {
                return fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            }
        } else if (registro.fecha_reg && typeof registro.fecha_reg === 'string') {
            const fecha = new Date(registro.fecha_reg);
            if (!isNaN(fecha.getTime())) {
                return fecha.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            }
        }
        
        return null;
    }

    // Función para limpiar autocompletado
    function limpiarAutocompletado() {
        window.registrosActuales = [];
        limpiarCamposHora();
    }

    // Manejar cambio de tipo de registro
    document.getElementById('tipoRegistro').addEventListener('change', function() {
        const tipo = this.value;
        const camposHora = document.getElementById('camposHora');
        const tiposHora = document.querySelectorAll('.tipo-hora');
        
        // Ocultar todos los campos de hora
        tiposHora.forEach(campo => campo.style.display = 'none');
        
        if (tipo) {
            camposHora.style.display = 'block';
            
            // Mostrar campos específicos según el tipo
            switch(tipo) {
                case 'entrada_salida':
                    document.getElementById('horasEntradaSalida').style.display = 'block';
                    break;
                case 'desayuno':
                    document.getElementById('horasDesayuno').style.display = 'block';
                    break;
                case 'almuerzo':
                    document.getElementById('horasAlmuerzo').style.display = 'block';
                    break;
                case 'otros':
                    document.getElementById('horasOtros').style.display = 'block';
                    break;
            }

            // Aplicar autocompletado si hay registros disponibles
            if (window.registrosActuales && window.registrosActuales.length > 0) {
                aplicarAutocompletado(tipo);
            }
        } else {
            camposHora.style.display = 'none';
        }
    });
    
    // Función para verificar si hay registros sin enviar
    function hayRegistrosSinEnviar() {
        const trabajadores = Object.keys(registrosData);
        return trabajadores.length > 0;
    }

    // Función para limpiar solo la tabla de registros añadidos
    function limpiarTablaRegistros() {
        registrosData = {};  // Limpiar objeto de registros
        actualizarTablaRegistros();  // Actualizar la tabla (se ocultará automáticamente)
    }

    // Variable para guardar la fecha anterior
    let fechaAnterior = '';

    // Función para confirmar cambio de fecha con registros pendientes
    function confirmarCambioFecha() {
        const fechaInput = document.getElementById('fechaRegistro');
        const fechaNueva = fechaInput.value;
        
        if (hayRegistrosSinEnviar()) {
            alertify.confirm(
                'Cambiar fecha',
                'Tienes registros sin enviar. ¿Estás seguro de que quieres cambiar la fecha? Se perderán todos los registros añadidos.',
                function() {
                    // Usuario confirma: limpiar solo tabla de registros y continuar
                    limpiarTablaRegistros();
                    actualizarTablaRegistrosExistentes();
                    // Actualizar fecha anterior para la próxima vez
                    fechaAnterior = fechaNueva;
                },
                function() {
                    // Usuario cancela: restaurar fecha anterior
                    fechaInput.value = fechaAnterior;
                }
            );
        } else {
            // No hay registros pendientes, proceder normalmente
            actualizarTablaRegistrosExistentes();
            // Actualizar fecha anterior para la próxima vez
            fechaAnterior = fechaNueva;
        }
    }

    // Agregar event listeners para actualizar tabla cuando cambien trabajadores o fecha
    document.getElementById('pernr_seleccionado').addEventListener('change', actualizarTablaRegistrosExistentes);
    document.getElementById('fechaRegistro').addEventListener('change', confirmarCambioFecha);

    // Función auxiliar para comparar horas (formato HH:MM:SS o HH:MM)
    function compararHoras(horaInicio, horaFin) {
        const minutosInicio = horaAMinutos(horaInicio);
        const minutosFin = horaAMinutos(horaFin);
        
        return minutosInicio < minutosFin;
    }

    // Función para verificar registros existentes
    async function verificarRegistrosExistentes(trabajadores, fecha) {
        try {
            const response = await fetch('auto.php?verificar_registros_existentes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    trabajadores: trabajadores,
                    fecha: fecha
                })
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al verificar registros existentes:', error);
            throw error;
        }
    }

    // Función para analizar el estado actual de un trabajador basado en sus registros
    function analizarEstadoTrabajador(registros) {
        const estado = {
            jornada: 'fuera_jornada', // fuera_jornada | en_jornada
            desayuno: 'disponible',   // disponible | iniciado | completado
            almuerzo: 'disponible',   // disponible | iniciado | completado
            otros: 'disponible'       // disponible | iniciado | completado
        };

        // Ordenar registros por fecha/hora
        const registrosOrdenados = [...registros].sort((a, b) => {
            const fechaA = obtenerFechaRegistro(a);
            const fechaB = obtenerFechaRegistro(b);
            return fechaA - fechaB;
        });

        // Analizar cada registro para determinar el estado actual
        registrosOrdenados.forEach(registro => {
            const tipo = registro.tipo_reg;
            
            switch(tipo) {
                case 'entrada':
                    estado.jornada = 'en_jornada';
                    break;
                case 'salida':
                    estado.jornada = 'fuera_jornada';
                    break;
                case 'inicio-desayuno':
                    estado.desayuno = 'iniciado';
                    break;
                case 'fin-desayuno':
                    estado.desayuno = 'completado';
                    break;
                case 'inicio-almuerzo':
                    estado.almuerzo = 'iniciado';
                    break;
                case 'fin-almuerzo':
                    estado.almuerzo = 'completado';
                    break;
                case 'inicio-otros':
                    estado.otros = 'iniciado';
                    break;
                case 'fin-otros':
                    estado.otros = 'completado';
                    break;
            }
        });

        return estado;
    }

    // Función auxiliar para obtener fecha de registro
    function obtenerFechaRegistro(registro) {
        const fechaObj = registro.fecha_reg?.date ? registro.fecha_reg : registro.fecha;
        if (fechaObj && fechaObj.date) {
            const fechaStr = fechaObj.date.split('.')[0].replace(' ', 'T');
            return new Date(fechaStr);
        } else if (registro.fecha_reg && typeof registro.fecha_reg === 'string') {
            return new Date(registro.fecha_reg);
        }
        return new Date(0); // Fecha por defecto si no se puede procesar
    }

    // Función para determinar qué tipos de registro están disponibles para un trabajador
    function obtenerTiposDisponibles(estado) {
        const disponibles = [];

        // Entrada/Salida
        if (estado.jornada === 'fuera_jornada') {
            disponibles.push({
                value: 'entrada_salida',
                text: 'Entrada/Salida',
                subtipo: 'entrada',
                descripcion: 'Puede registrar entrada'
            });
        } else if (estado.jornada === 'en_jornada') {
            disponibles.push({
                value: 'entrada_salida',
                text: 'Entrada/Salida',
                subtipo: 'salida',
                descripcion: 'Puede registrar salida'
            });
        }

        // Solo permitir descansos si está en jornada
        if (estado.jornada === 'en_jornada') {
            // Desayuno
            if (estado.desayuno === 'disponible') {
                disponibles.push({
                    value: 'desayuno',
                    text: 'Desayuno',
                    subtipo: 'inicio',
                    descripcion: 'Puede iniciar desayuno'
                });
            } else if (estado.desayuno === 'iniciado') {
                disponibles.push({
                    value: 'desayuno',
                    text: 'Desayuno',
                    subtipo: 'fin',
                    descripcion: 'Debe finalizar desayuno'
                });
            }

            // Almuerzo
            if (estado.almuerzo === 'disponible') {
                disponibles.push({
                    value: 'almuerzo',
                    text: 'Almuerzo',
                    subtipo: 'inicio',
                    descripcion: 'Puede iniciar almuerzo'
                });
            } else if (estado.almuerzo === 'iniciado') {
                disponibles.push({
                    value: 'almuerzo',
                    text: 'Almuerzo',
                    subtipo: 'fin',
                    descripcion: 'Debe finalizar almuerzo'
                });
            }

            // Otros
            if (estado.otros === 'disponible') {
                disponibles.push({
                    value: 'otros',
                    text: 'Otros',
                    subtipo: 'inicio',
                    descripcion: 'Puede iniciar otros'
                });
            } else if (estado.otros === 'iniciado') {
                disponibles.push({
                    value: 'otros',
                    text: 'Otros',
                    subtipo: 'fin',
                    descripcion: 'Debe finalizar otros'
                });
            }
        }

        return disponibles;
    }


    // Función para agregar registro
    function agregarRegistro() {
        const trabajadorInput = document.getElementById('trabajadoresNuevoRegistro');
        const pernrHidden = document.getElementById('pernr_seleccionado');
        const fecha = document.getElementById('fechaRegistro').value;
        const tipo = document.getElementById('tipoRegistro').value;
        
        // Si no hay PERNR en el campo oculto, intentar extraerlo del input
        let pernr = pernrHidden.value;
        if (!pernr && trabajadorInput.value) {
            // Extraer PERNR del formato "PERNR - NOMBRE"
            const match = trabajadorInput.value.match(/^(\d+)\s*-\s*/);
            if (match) {
                pernr = match[1];
                pernrHidden.value = pernr;
            }
        }
        
        if (!trabajadorInput.value || !pernr || !fecha || !tipo) {
            alertify.alert('Error', 'Por favor, complete todos los campos requeridos y seleccione un trabajador válido.');
            return;
        }

        // Crear un objeto que simule el comportamiento del select anterior
        const trabajadorSelect = {
            value: pernr
        };

        // Proceder directamente con la validación y adición
        // Los registros existentes ya se muestran automáticamente en la tabla
        procederConValidacionYAdicion(trabajadorSelect, fecha, tipo);
    }

    // Función separada para proceder con la validación y adición (lógica original)
    function procederConValidacionYAdicion(trabajadoresSelect, fecha, tipo) {
        const pernr = trabajadoresSelect.value;
        
        // NUEVA VALIDACIÓN: Verificar que existe entrada/salida completa
        const validacionEntradaSalida = verificarEntradaSalidaCompleta(pernr, fecha);
        if (!validacionEntradaSalida.valido && tipo !== 'entrada_salida') {
            alertify.alert('Error', validacionEntradaSalida.mensaje);
            return;
        }

        // Validar y obtener las horas según el tipo
        let horasData = {};
        let esValido = true;
        
        switch(tipo) {
            case 'entrada_salida':
                const entradaHora = document.querySelector('input[name="entrada_hora"]').value;
                const salidaHora = document.querySelector('input[name="salida_hora"]').value;
                
                if (!entradaHora || !salidaHora) {
                    alertify.alert('Error', 'Por favor, complete todos los campos de hora para entrada y salida.');
                    return;
                }
                
                if (!compararHoras(entradaHora, salidaHora)) {
                    alertify.alert('Error', 'La hora de entrada debe ser menor que la hora de salida.');
                    return;
                }
                
                horasData = {
                    entrada: entradaHora,
                    salida: salidaHora
                };
                break;
                
            case 'desayuno':
                const desayunoInicio = document.querySelector('input[name="desayuno_inicio_hora"]').value;
                const desayunoFin = document.querySelector('input[name="desayuno_fin_hora"]').value;
                
                if (!desayunoInicio || !desayunoFin) {
                    alertify.alert('Error', 'Por favor, complete todos los campos de hora para el desayuno.');
                    return;
                }
                
                if (!compararHoras(desayunoInicio, desayunoFin)) {
                    alertify.alert('Error', 'La hora de inicio del desayuno debe ser menor que la hora de fin.');
                    return;
                }
                
                // Validar que el desayuno esté dentro del rango de entrada-salida
                const validacionDesayuno = validarRangoLaboral(desayunoInicio, desayunoFin, trabajadoresSelect.value);
                if (!validacionDesayuno.valido) {
                    alertify.alert('Error', validacionDesayuno.mensaje);
                    return;
                }
                
                horasData = {
                    inicio: desayunoInicio,
                    fin: desayunoFin
                };
                break;
                
            case 'almuerzo':
                const almuerzoInicio = document.querySelector('input[name="almuerzo_inicio_hora"]').value;
                const almuerzoFin = document.querySelector('input[name="almuerzo_fin_hora"]').value;
                
                if (!almuerzoInicio || !almuerzoFin) {
                    alertify.alert('Error', 'Por favor, complete todos los campos de hora para el almuerzo.');
                    return;
                }
                
                if (!compararHoras(almuerzoInicio, almuerzoFin)) {
                    alertify.alert('Error', 'La hora de inicio del almuerzo debe ser menor que la hora de fin.');
                    return;
                }
                
                // Validar que el almuerzo esté dentro del rango de entrada-salida
                const validacionAlmuerzo = validarRangoLaboral(almuerzoInicio, almuerzoFin, trabajadoresSelect.value);
                if (!validacionAlmuerzo.valido) {
                    alertify.alert('Error', validacionAlmuerzo.mensaje);
                    return;
                }
                
                horasData = {
                    inicio: almuerzoInicio,
                    fin: almuerzoFin
                };
                break;
                
            case 'otros':
                const otrosInicio = document.querySelector('input[name="otros_inicio_hora"]').value;
                const otrosFin = document.querySelector('input[name="otros_fin_hora"]').value;
                
                if (!otrosInicio || !otrosFin) {
                    alertify.alert('Error', 'Por favor, complete todos los campos de hora para otros.');
                    return;
                }
                
                if (!compararHoras(otrosInicio, otrosFin)) {
                    alertify.alert('Error', 'La hora de inicio de otros debe ser menor que la hora de fin.');
                    return;
                }
                
                // Validar que otros esté dentro del rango de entrada-salida
                const validacionOtros = validarRangoLaboral(otrosInicio, otrosFin, trabajadoresSelect.value);
                if (!validacionOtros.valido) {
                    alertify.alert('Error', validacionOtros.mensaje);
                    return;
                }
                
                horasData = {
                    inicio: otrosInicio,
                    fin: otrosFin
                };
                break;
        }

        // NUEVA VALIDACIÓN: Verificar campos autocompletados antes del envío
        validarCamposAutocompletados();

        // NUEVA VALIDACIÓN: Verificar duplicados de tipo y hora
        const validacionDuplicados = verificarDuplicadosTipoHora(pernr, fecha, tipo, horasData);
        if (!validacionDuplicados.valido) {
            alertify.alert('Error', validacionDuplicados.mensaje);
            return;
        }

        // NUEVA VALIDACIÓN: Verificar solapamientos de rangos horarios
        const validacionSolapamiento = verificarSolapamientoRangos(pernr, fecha, tipo, horasData);
        if (!validacionSolapamiento.valido) {
            alertify.alert('Error', validacionSolapamiento.mensaje);
            return;
        }

        // Agregar registro para el trabajador seleccionado
        const trabajadorInput = document.getElementById('trabajadoresNuevoRegistro');
        const nombre = trabajadorInput.value;
        
        // Si el trabajador no existe en registrosData, crearlo
        if (!registrosData[pernr]) {
            registrosData[pernr] = {
                pernr: pernr,
                nombre: nombre,
                registros: []
            };
        }
        
        // Verificar que el registro tenga al menos una hora válida antes de añadirlo
        let tieneHorasValidas = false;
        if (tipo === 'entrada_salida') {
            tieneHorasValidas = horasData.entrada || horasData.salida;
        } else {
            tieneHorasValidas = horasData.inicio && horasData.fin;
        }
        
        if (!tieneHorasValidas) {
            alertify.alert('Error', 'No se puede añadir un registro sin horas válidas. Todos los registros ya existen.');
            return;
        }

        // Añadir el nuevo registro al trabajador
        const registro = {
            id: Date.now() + Math.random(), // ID único
            fecha: fecha,
            tipo: tipo,
            horas: horasData
        };
        
        registrosData[pernr].registros.push(registro);

        // Actualizar la tabla
        actualizarTablaRegistros();
        
        // Limpiar campos de hora
        limpiarCamposHora();
        
        // Mostrar mensaje de éxito
        // alertify.success(`Registro añadido para ${nombre}.`);
    }

    // Función para actualizar la tabla de registros
    function actualizarTablaRegistros() {
        const contenedor = document.getElementById('contenedorTrabajadores');
        const tablaRegistros = document.getElementById('tablaRegistros');
        const btnEnviar = document.getElementById('btnEnviarDatos');
        
        contenedor.innerHTML = '';
        
        const trabajadores = Object.keys(registrosData);
        if (trabajadores.length === 0) {
            tablaRegistros.style.display = 'none';
            btnEnviar.style.display = 'none';
            return;
        }
        
        // Crear una tabla para cada trabajador
        trabajadores.forEach(pernr => {
            const trabajador = registrosData[pernr];
            
            // Crear contenedor para este trabajador
            const trabajadorDiv = document.createElement('div');
            trabajadorDiv.className = 'mb-4';
            
            // Título del trabajador
            const titulo = document.createElement('h6');
            titulo.className = 'text-primary fw-bold mb-2';
            titulo.innerHTML = `<i class="bi bi-person-fill"></i> ${trabajador.nombre} <span class="badge bg-secondary">${trabajador.registros.length} registro(s)</span>`;
            trabajadorDiv.appendChild(titulo);
            
            // Tabla para los registros de este trabajador
            const tabla = document.createElement('table');
            tabla.className = 'table table-sm table-bordered mb-3';
            
            // Cabecera de la tabla
            tabla.innerHTML = `
                <thead class="table-light">
                    <tr>
                        <th width="15%">Fecha</th>
                        <th width="20%">Tipo</th>
                        <th width="55%">Detalles</th>
                        <th width="10%">Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            `;
            
            const cuerpoTabla = tabla.querySelector('tbody');
            
            // Ordenar registros por hora antes de mostrarlos
            const registrosOrdenados = [...trabajador.registros].sort((a, b) => {
                // Función para obtener todas las horas de un registro en orden cronológico
                function obtenerHorasCronologicas(registro) {
                    let horas = [];
                    
                    switch(registro.tipo) {
                        case 'entrada_salida':
                            if (registro.horas.entrada) horas.push({hora: registro.horas.entrada, tipo: 'entrada'});
                            if (registro.horas.salida) horas.push({hora: registro.horas.salida, tipo: 'salida'});
                            break;
                        case 'desayuno':
                            if (registro.horas.inicio) horas.push({hora: registro.horas.inicio, tipo: 'inicio-desayuno'});
                            if (registro.horas.fin) horas.push({hora: registro.horas.fin, tipo: 'fin-desayuno'});
                            break;
                        case 'almuerzo':
                            if (registro.horas.inicio) horas.push({hora: registro.horas.inicio, tipo: 'inicio-almuerzo'});
                            if (registro.horas.fin) horas.push({hora: registro.horas.fin, tipo: 'fin-almuerzo'});
                            break;
                        case 'otros':
                            if (registro.horas.inicio) horas.push({hora: registro.horas.inicio, tipo: 'inicio-otros'});
                            if (registro.horas.fin) horas.push({hora: registro.horas.fin, tipo: 'fin-otros'});
                            break;
                    }
                    
                    // Ordenar las horas cronológicamente
                    return horas.sort((x, y) => x.hora.localeCompare(y.hora));
                }
                
                const horasA = obtenerHorasCronologicas(a);
                const horasB = obtenerHorasCronologicas(b);
                
                // Comparar por la primera hora de cada registro
                if (horasA.length === 0 && horasB.length === 0) return 0;
                if (horasA.length === 0) return 1;
                if (horasB.length === 0) return -1;
                
                return horasA[0].hora.localeCompare(horasB[0].hora);
            });
            
            // Añadir cada registro del trabajador ordenado por hora
            registrosOrdenados.forEach(registro => {
                const fila = document.createElement('tr');
                
                // Formatear detalles según el tipo
                let detalles = '';
                let tipoBadge = '';
                switch(registro.tipo) {
                    case 'entrada_salida':
                        let partes = [];
                        if (registro.horas.entrada) {
                            partes.push(`<strong>Entrada:</strong> ${registro.horas.entrada}`);
                        }
                        if (registro.horas.salida) {
                            partes.push(`<strong>Salida:</strong> ${registro.horas.salida}`);
                        }
                        detalles = partes.join(' | ');
                        tipoBadge = '<span class="badge bg-success">ENTRADA/SALIDA</span>';
                        break;
                    case 'desayuno':
                        detalles = `<strong>Inicio:</strong> ${registro.horas.inicio} | <strong>Fin:</strong> ${registro.horas.fin}`;
                        tipoBadge = '<span class="badge bg-warning">DESAYUNO</span>';
                        break;
                    case 'almuerzo':
                        detalles = `<strong>Inicio:</strong> ${registro.horas.inicio} | <strong>Fin:</strong> ${registro.horas.fin}`;
                        tipoBadge = '<span class="badge bg-info">ALMUERZO</span>';
                        break;
                    case 'otros':
                        detalles = `<strong>Inicio:</strong> ${registro.horas.inicio} | <strong>Fin:</strong> ${registro.horas.fin}`;
                        tipoBadge = '<span class="badge bg-secondary">OTROS</span>';
                        break;
                }
                
                fila.innerHTML = `
                    <td>${registro.fecha}</td>
                    <td>${tipoBadge}</td>
                    <td><small>${detalles}</small></td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarRegistro('${pernr}', '${registro.id}')" title="Eliminar registro">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                
                cuerpoTabla.appendChild(fila);
            });
            
            trabajadorDiv.appendChild(tabla);
            contenedor.appendChild(trabajadorDiv);
        });
        
        tablaRegistros.style.display = 'block';
        btnEnviar.style.display = 'inline-block';
    }

    // Función para eliminar un registro
    function eliminarRegistro(pernr, registroId) {
        if (registrosData[pernr]) {
            // Eliminar el registro específico del trabajador
            registrosData[pernr].registros = registrosData[pernr].registros.filter(registro => registro.id != registroId);
            
            // Si el trabajador no tiene más registros, eliminarlo completamente
            if (registrosData[pernr].registros.length === 0) {
                delete registrosData[pernr];
            }
        }
        actualizarTablaRegistros();
    }

    // Función para limpiar campos de hora
    function limpiarCamposHora() {
        document.querySelectorAll('#camposHora input[type="time"]').forEach(input => {
            input.value = '';
        });
    }


    // Función para limpiar todo el formulario
    function limpiarFormulario() {
        document.getElementById('formNuevoRegistro').reset();
        document.getElementById('camposHora').style.display = 'none';
        document.querySelectorAll('.tipo-hora').forEach(campo => campo.style.display = 'none');
        registrosData = {};  // Cambiar a objeto vacío
        actualizarTablaRegistros();
        
        // Ocultar tabla de registros existentes
        document.getElementById('tablaRegistrosExistentes').style.display = 'none';
        
        // Limpiar input de trabajador y campo oculto
        document.getElementById('trabajadoresNuevoRegistro').value = '';
        document.getElementById('pernr_seleccionado').value = '';
        document.getElementById('sugerencias_trabajadores').style.display = 'none';
        
        limpiarAutocompletado();
    }

    // Función auxiliar para obtener todos los rangos de entrada-salida del trabajador
    function obtenerRangoEntradaSalida(pernrActual) {
        const rangos = [];
        
        console.log(`Obteniendo rangos para trabajador ${pernrActual}`);
        
        // 1. Buscar en registros existentes de la base de datos
        if (typeof window.registrosActuales !== 'undefined' && window.registrosActuales && window.registrosActuales.length > 0) {
            console.log('Registros existentes en BD:', window.registrosActuales);
            
            // Ordenar registros por hora
            const registrosOrdenados = [...window.registrosActuales].sort((a, b) => {
                const horaA = obtenerHoraDeRegistro(a);
                const horaB = obtenerHoraDeRegistro(b);
                return horaA.localeCompare(horaB);
            });
            
            let entradaActual = null;
            registrosOrdenados.forEach(registro => {
                const hora = obtenerHoraDeRegistro(registro);
                console.log(`Registro BD: ${registro.tipo_reg} - ${hora}`);
                
                if (registro.tipo_reg === 'entrada') {
                    entradaActual = hora;
                    console.log(`Entrada encontrada en BD: ${entradaActual}`);
                } else if (registro.tipo_reg === 'salida' && entradaActual) {
                    rangos.push({ entrada: entradaActual, salida: hora });
                    console.log(`Rango completado: ${entradaActual} - ${hora}`);
                    entradaActual = null; // Reset para el siguiente rango
                }
            });
        }
        
        // 2. Buscar en registros añadidos manualmente (registrosData)
        if (registrosData[pernrActual] && registrosData[pernrActual].registros) {
            console.log('Registros manuales:', registrosData[pernrActual].registros);
            registrosData[pernrActual].registros.forEach(registro => {
                if (registro.tipo === 'entrada_salida' && registro.horas.entrada && registro.horas.salida) {
                    rangos.push({ entrada: registro.horas.entrada, salida: registro.horas.salida });
                    console.log(`Rango manual añadido: ${registro.horas.entrada} - ${registro.horas.salida}`);
                }
            });
        }
        
        console.log(`Rangos finales para ${pernrActual}:`, rangos);
        
        // Retornar todos los rangos encontrados
        return rangos.length > 0 ? rangos : null;
    }

    // Función auxiliar para convertir hora HH:MM a minutos
    function horaAMinutos(hora) {
        if (!hora) return null;
        const partes = hora.split(':');
        return parseInt(partes[0]) * 60 + parseInt(partes[1]);
    }

    // Función auxiliar para validar si una hora está dentro del rango laboral
    function validarRangoLaboral(horaInicio, horaFin, pernr) {
        const rangos = obtenerRangoEntradaSalida(pernr);
        
        console.log(`Validando rango laboral para ${pernr}: ${horaInicio}-${horaFin}`);
        console.log(`Rangos disponibles:`, rangos);
        
        // Si no hay rangos registrados, permitir cualquier hora
        if (!rangos || rangos.length === 0) {
            console.log('No hay rangos definidos, permitiendo cualquier hora');
            return { valido: true, mensaje: '' };
        }
        
        const minutosInicio = horaAMinutos(horaInicio);
        const minutosFin = horaAMinutos(horaFin);
        
        console.log(`Minutos inicio: ${minutosInicio}, Minutos fin: ${minutosFin}`);
        
        // Verificar si las horas están dentro de alguno de los rangos disponibles
        for (let rango of rangos) {
            const minutosEntrada = horaAMinutos(rango.entrada);
            const minutosSalida = horaAMinutos(rango.salida);
            
            console.log(`Verificando rango: ${rango.entrada} (${minutosEntrada}) - ${rango.salida} (${minutosSalida})`);
            
            // Verificar si tanto inicio como fin están dentro de este rango
            if (minutosInicio >= minutosEntrada && minutosInicio <= minutosSalida &&
                minutosFin >= minutosEntrada && minutosFin <= minutosSalida) {
                console.log('Validación de rango exitosa');
                return { valido: true, mensaje: '' };
            }
        }
        
        // Si llegamos aquí, las horas no están en ningún rango válido
        const rangosTexto = rangos.map(r => `${r.entrada}-${r.salida}`).join(', ');
        console.log(`Hora fuera de todos los rangos: ${horaInicio}-${horaFin}`);
        return {
            valido: false,
            mensaje: `Las horas (${horaInicio}-${horaFin}) deben estar dentro de alguno de los rangos laborales: ${rangosTexto}`
        };
    }

    // NUEVA FUNCIÓN: Verificar que existe entrada/salida completa
    function verificarEntradaSalidaCompleta(pernr, fecha) {
        let tieneEntrada = false;
        let tieneSalida = false;
        
        // Verificar en registros existentes de la base de datos
        if (typeof window.registrosActuales !== 'undefined' && window.registrosActuales && window.registrosActuales.length > 0) {
            window.registrosActuales.forEach(registro => {
                if (registro.tipo_reg === 'entrada') tieneEntrada = true;
                if (registro.tipo_reg === 'salida') tieneSalida = true;
            });
        }
        
        // Verificar en registros nuevos que se van a añadir
        if (registrosData[pernr] && registrosData[pernr].registros) {
            registrosData[pernr].registros.forEach(registro => {
                if (registro.fecha === fecha && registro.tipo === 'entrada_salida') {
                    tieneEntrada = true;
                    tieneSalida = true;
                }
            });
        }
        
        const entradaSalidaCompleta = tieneEntrada && tieneSalida;
        
        if (!entradaSalidaCompleta) {
            return {
                valido: false,
                mensaje: 'Debe existir una entrada y salida completa antes de añadir otros tipos de registros. Por favor, añada primero el registro de entrada/salida.'
            };
        }
        
        return { valido: true, mensaje: '' };
    }

    // NUEVA FUNCIÓN: Verificar solapamientos de rangos horarios
    function verificarSolapamientoRangos(pernr, fecha, tipo, horasData) {
        // Función auxiliar para convertir hora a minutos para comparación
        function horaAMinutos(hora) {
            if (!hora) return null;
            const partes = hora.split(':');
            return parseInt(partes[0]) * 60 + parseInt(partes[1]);
        }
        
        // Función auxiliar para verificar si dos rangos se solapan
        function rangosSeSolapan(inicio1, fin1, inicio2, fin2) {
            const min1Inicio = horaAMinutos(inicio1);
            const min1Fin = horaAMinutos(fin1);
            const min2Inicio = horaAMinutos(inicio2);
            const min2Fin = horaAMinutos(fin2);
            
            if (min1Inicio === null || min1Fin === null || min2Inicio === null || min2Fin === null) {
                return false;
            }
            
            // Dos rangos se solapan si: inicio1 < fin2 && inicio2 < fin1
            return min1Inicio < min2Fin && min2Inicio < min1Fin;
        }
        
        // Obtener todos los rangos existentes para este trabajador y fecha
        const rangosExistentes = [];
        
        // 1. Obtener rangos de registros existentes en la base de datos
        if (typeof window.registrosActuales !== 'undefined' && window.registrosActuales && window.registrosActuales.length > 0) {
            // Agrupar registros por tipo para formar rangos
            const entradasSalidas = [];
            const descansos = {
                desayuno: [],
                almuerzo: [],
                otros: []
            };
            
            window.registrosActuales.forEach(registro => {
                const hora = obtenerHoraDeRegistro(registro);
                if (!hora) return;
                
                switch(registro.tipo_reg) {
                    case 'entrada':
                    case 'salida':
                        entradasSalidas.push({ tipo: registro.tipo_reg, hora: hora });
                        break;
                    case 'inicio-desayuno':
                        descansos.desayuno.push({ tipo: 'inicio', hora: hora });
                        break;
                    case 'fin-desayuno':
                        descansos.desayuno.push({ tipo: 'fin', hora: hora });
                        break;
                    case 'inicio-almuerzo':
                        descansos.almuerzo.push({ tipo: 'inicio', hora: hora });
                        break;
                    case 'fin-almuerzo':
                        descansos.almuerzo.push({ tipo: 'fin', hora: hora });
                        break;
                    case 'inicio-otros':
                        descansos.otros.push({ tipo: 'inicio', hora: hora });
                        break;
                    case 'fin-otros':
                        descansos.otros.push({ tipo: 'fin', hora: hora });
                        break;
                }
            });
            
            // Formar múltiples rangos de entrada-salida
            const entradas = entradasSalidas.filter(r => r.tipo === 'entrada').map(r => r.hora).sort((a, b) => horaAMinutos(a) - horaAMinutos(b));
            const salidas = entradasSalidas.filter(r => r.tipo === 'salida').map(r => r.hora).sort((a, b) => horaAMinutos(a) - horaAMinutos(b));
            
            // Crear múltiples rangos de entrada-salida
            for (let i = 0; i < entradas.length; i++) {
                const entrada = entradas[i];
                // Buscar la primera salida después de esta entrada
                const salidaPosterior = salidas.find(salida => horaAMinutos(salida) > horaAMinutos(entrada));
                if (salidaPosterior) {
                    rangosExistentes.push({
                        tipo: 'entrada_salida',
                        inicio: entrada,
                        fin: salidaPosterior
                    });
                }
            }
            
            // Formar múltiples rangos de descansos
            Object.keys(descansos).forEach(tipoDescanso => {
                const registrosDescanso = descansos[tipoDescanso];
                const inicios = registrosDescanso.filter(r => r.tipo === 'inicio').map(r => r.hora).sort((a, b) => horaAMinutos(a) - horaAMinutos(b));
                const fines = registrosDescanso.filter(r => r.tipo === 'fin').map(r => r.hora).sort((a, b) => horaAMinutos(a) - horaAMinutos(b));
                
                // Crear múltiples rangos de descanso
                for (let i = 0; i < inicios.length; i++) {
                    const inicio = inicios[i];
                    // Buscar el primer fin después de este inicio
                    const finPosterior = fines.find(fin => horaAMinutos(fin) > horaAMinutos(inicio));
                    if (finPosterior) {
                        rangosExistentes.push({
                            tipo: tipoDescanso,
                            inicio: inicio,
                            fin: finPosterior
                        });
                    }
                }
            });
        }
        
        // 2. Obtener rangos de registros nuevos que se van a añadir
        if (registrosData[pernr] && registrosData[pernr].registros) {
            registrosData[pernr].registros.forEach(registro => {
                if (registro.fecha === fecha) {
                    if (registro.tipo === 'entrada_salida' && registro.horas.entrada && registro.horas.salida) {
                        rangosExistentes.push({
                            tipo: 'entrada_salida',
                            inicio: registro.horas.entrada,
                            fin: registro.horas.salida
                        });
                    } else if (registro.tipo !== 'entrada_salida' && registro.horas.inicio && registro.horas.fin) {
                        rangosExistentes.push({
                            tipo: registro.tipo,
                            inicio: registro.horas.inicio,
                            fin: registro.horas.fin
                        });
                    }
                }
            });
        }
        
        // 3. Verificar solapamientos según el tipo de registro actual
        let inicioActual, finActual;
        
        if (tipo === 'entrada_salida') {
            inicioActual = horasData.entrada;
            finActual = horasData.salida;
        } else {
            inicioActual = horasData.inicio;
            finActual = horasData.fin;
        }
        
        if (!inicioActual || !finActual) {
            return { valido: true, mensaje: '' };
        }
        
        // VALIDACIÓN 1: Para entrada/salida - no puede estar dentro de otro rango de entrada/salida
        if (tipo === 'entrada_salida') {
            for (let rangoExistente of rangosExistentes) {
                if (rangoExistente.tipo === 'entrada_salida') {
                    if (rangosSeSolapan(inicioActual, finActual, rangoExistente.inicio, rangoExistente.fin)) {
                        return {
                            valido: false,
                            mensaje: `El nuevo rango de entrada/salida (${inicioActual} - ${finActual}) se solapa con un rango existente (${rangoExistente.inicio} - ${rangoExistente.fin}).`
                        };
                    }
                }
            }
        }
        
        // VALIDACIÓN 2: Para descansos - debe estar dentro de un rango de entrada/salida
        if (tipo !== 'entrada_salida') {
            const rangosEntradaSalida = rangosExistentes.filter(r => r.tipo === 'entrada_salida');
            
            if (rangosEntradaSalida.length === 0) {
                return {
                    valido: false,
                    mensaje: 'No se puede añadir un descanso sin un rango de entrada/salida previo.'
                };
            }
            
            // Verificar que el descanso esté completamente dentro de al menos un rango de entrada/salida
            let dentroDeRangoTrabajo = false;
            for (let rangoTrabajo of rangosEntradaSalida) {
                const minutosInicioDescanso = horaAMinutos(inicioActual);
                const minutosFinDescanso = horaAMinutos(finActual);
                const minutosInicioTrabajo = horaAMinutos(rangoTrabajo.inicio);
                const minutosFinTrabajo = horaAMinutos(rangoTrabajo.fin);
                
                if (minutosInicioDescanso >= minutosInicioTrabajo && minutosFinDescanso <= minutosFinTrabajo) {
                    dentroDeRangoTrabajo = true;
                    break;
                }
            }
            
            if (!dentroDeRangoTrabajo) {
                return {
                    valido: false,
                    mensaje: `El descanso (${inicioActual} - ${finActual}) debe estar completamente dentro de un rango de entrada/salida.`
                };
            }
            
            // VALIDACIÓN 3: Los descansos no pueden solaparse entre ellos
            for (let rangoExistente of rangosExistentes) {
                if (rangoExistente.tipo !== 'entrada_salida' && rangoExistente.tipo !== tipo) {
                    if (rangosSeSolapan(inicioActual, finActual, rangoExistente.inicio, rangoExistente.fin)) {
                        return {
                            valido: false,
                            mensaje: `El ${tipo} (${inicioActual} - ${finActual}) se solapa con un ${rangoExistente.tipo} existente (${rangoExistente.inicio} - ${rangoExistente.fin}).`
                        };
                    }
                }
            }
            
            // VALIDACIÓN 4: Los descansos del mismo tipo no pueden solaparse
            for (let rangoExistente of rangosExistentes) {
                if (rangoExistente.tipo === tipo) {
                    if (rangosSeSolapan(inicioActual, finActual, rangoExistente.inicio, rangoExistente.fin)) {
                        return {
                            valido: false,
                            mensaje: `El ${tipo} (${inicioActual} - ${finActual}) se solapa con otro ${tipo} existente (${rangoExistente.inicio} - ${rangoExistente.fin}).`
                        };
                    }
                }
            }
        }
        
        return { valido: true, mensaje: '' };
    }

    // NUEVA FUNCIÓN: Verificar duplicados de tipo y hora - versión mejorada
    function verificarDuplicadosTipoHora(pernr, fecha, tipo, horasData) {
        // Función auxiliar para verificar si una hora ya existe
        function horaYaExiste(horaAComprobar, tipoRegistro) {
            // Verificar en registros existentes de la base de datos
            if (window.registrosActuales && window.registrosActuales.length > 0) {
                for (let registro of window.registrosActuales) {
                    const horaExistente = obtenerHoraDeRegistro(registro);
                    if (horaExistente === horaAComprobar && 
                        (registro.tipo_reg === tipoRegistro || 
                         (tipoRegistro === 'entrada' && registro.tipo_reg === 'entrada') ||
                         (tipoRegistro === 'salida' && registro.tipo_reg === 'salida'))) {
                        return true;
                    }
                }
            }
            
            // Verificar en registros nuevos que se van a añadir
            if (registrosData[pernr] && registrosData[pernr].registros) {
                for (let registro of registrosData[pernr].registros) {
                    if (registro.fecha === fecha) {
                        // Verificar conflictos entre diferentes tipos
                        const horasRegistro = [];
                        if (registro.tipo === 'entrada_salida') {
                            horasRegistro.push(registro.horas.entrada, registro.horas.salida);
                        } else {
                            horasRegistro.push(registro.horas.inicio, registro.horas.fin);
                        }
                        
                        if (horasRegistro.includes(horaAComprobar)) {
                            return true;
                        }
                    }
                }
            }
            
            return false;
        }
        
        // Verificar según el tipo de registro
        switch(tipo) {
            case 'entrada_salida':
                const entradaExiste = horaYaExiste(horasData.entrada, 'entrada');
                const salidaExiste = horaYaExiste(horasData.salida, 'salida');
                
                // Si ambas horas ya existen, no permitir el registro
                if (entradaExiste && salidaExiste) {
                    return {
                        valido: false,
                        mensaje: `Ya existen registros de entrada (${horasData.entrada}) y salida (${horasData.salida}). No se puede añadir el registro completo.`
                    };
                }
                
                // Si solo existe una de las dos, mostrar advertencia pero permitir el registro
                if (entradaExiste || salidaExiste) {
                    let mensaje = 'Advertencia: ';
                    if (entradaExiste) {
                        mensaje += `La entrada a las ${horasData.entrada} ya existe y no se añadirá. `;
                    }
                    if (salidaExiste) {
                        mensaje += `La salida a las ${horasData.salida} ya existe y no se añadirá. `;
                    }
                    mensaje += 'Solo se añadirán los registros que no existen.';
                    
                    // Mostrar advertencia pero permitir continuar
                    // alertify.warning(mensaje);
                    
                    // Modificar los datos para que solo incluyan las horas que no existen
                    if (entradaExiste) {
                        delete horasData.entrada;
                    }
                    if (salidaExiste) {
                        delete horasData.salida;
                    }
                }
                break;
                
            case 'desayuno':
            case 'almuerzo':
            case 'otros':
                if (horaYaExiste(horasData.inicio, `inicio-${tipo}`)) {
                    return {
                        valido: false,
                        mensaje: `Ya existe un registro a las ${horasData.inicio}. No se puede añadir el inicio de ${tipo} a la misma hora.`
                    };
                }
                if (horaYaExiste(horasData.fin, `fin-${tipo}`)) {
                    return {
                        valido: false,
                        mensaje: `Ya existe un registro a las ${horasData.fin}. No se puede añadir el fin de ${tipo} a la misma hora.`
                    };
                }
                
                // Nota: Se permite múltiples registros del mismo tipo para la misma fecha
                // siempre que no se solapen en horario (validado por verificarSolapamientoRangos)
                break;
        }
        
        return { valido: true, mensaje: '' };
    }

    // Función para enviar datos
    function enviarDatos() {
        const trabajadores = Object.keys(registrosData);
        if (trabajadores.length === 0) {
            alertify.alert('Error', 'No hay registros para enviar.');
            return;
        }

        // Transformar los datos para separar registros de inicio/fin, evitando duplicados
        const datosParaBackend = [];
        
        // Función auxiliar para verificar si un registro ya existe
        function yaExisteRegistro(pernr, fecha, tipo, hora) {
            if (!window.registrosActuales || window.registrosActuales.length === 0) {
                return false;
            }
            
            return window.registrosActuales.some(registro => {
                const horaExistente = obtenerHoraDeRegistro(registro);
                return registro.tipo_reg === tipo && horaExistente === hora;
            });
        }

        // Procesar cada trabajador y sus registros
        trabajadores.forEach(pernr => {
            const trabajador = registrosData[pernr];
            
            trabajador.registros.forEach(registro => {
                
                switch(registro.tipo) {
                    case 'entrada_salida':
                        // Para entrada/salida, verificar cada uno por separado solo si existe la hora
                        if (registro.horas.entrada && !yaExisteRegistro(pernr, registro.fecha, 'entrada', registro.horas.entrada)) {
                            datosParaBackend.push({
                                pernr: pernr,
                                fecha: registro.fecha,
                                tipo: 'entrada',
                                hora: registro.horas.entrada
                            });
                        }
                        if (registro.horas.salida && !yaExisteRegistro(pernr, registro.fecha, 'salida', registro.horas.salida)) {
                            datosParaBackend.push({
                                pernr: pernr,
                                fecha: registro.fecha,
                                tipo: 'salida',
                                hora: registro.horas.salida
                            });
                        }
                        break;
                        
                    case 'desayuno':
                        // Separar desayuno en inicio_desayuno y fin_desayuno, verificando duplicados
                        if (!yaExisteRegistro(pernr, registro.fecha, 'inicio-desayuno', registro.horas.inicio)) {
                            datosParaBackend.push({
                                pernr: pernr,
                                fecha: registro.fecha,
                                tipo: 'inicio-desayuno',
                                hora: registro.horas.inicio
                            });
                        }
                        if (!yaExisteRegistro(pernr, registro.fecha, 'fin-desayuno', registro.horas.fin)) {
                            datosParaBackend.push({
                                pernr: pernr,
                                fecha: registro.fecha,
                                tipo: 'fin-desayuno',
                                hora: registro.horas.fin
                            });
                        }
                        break;
                        
                    case 'almuerzo':
                        // Separar almuerzo en inicio_almuerzo y fin_almuerzo, verificando duplicados
                        if (!yaExisteRegistro(pernr, registro.fecha, 'inicio-almuerzo', registro.horas.inicio)) {
                            datosParaBackend.push({
                                pernr: pernr,
                                fecha: registro.fecha,
                                tipo: 'inicio-almuerzo',
                                hora: registro.horas.inicio
                            });
                        }
                        if (!yaExisteRegistro(pernr, registro.fecha, 'fin-almuerzo', registro.horas.fin)) {
                            datosParaBackend.push({
                                pernr: pernr,
                                fecha: registro.fecha,
                                tipo: 'fin-almuerzo',
                                hora: registro.horas.fin
                            });
                        }
                        break;
                        
                    case 'otros':
                        // Separar otros en inicio_otros y fin_otros, verificando duplicados
                        if (!yaExisteRegistro(pernr, registro.fecha, 'inicio-otros', registro.horas.inicio)) {
                            datosParaBackend.push({
                                pernr: pernr,
                                fecha: registro.fecha,
                                tipo: 'inicio-otros',
                                hora: registro.horas.inicio
                            });
                        }
                        if (!yaExisteRegistro(pernr, registro.fecha, 'fin-otros', registro.horas.fin)) {
                            datosParaBackend.push({
                                pernr: pernr,
                                fecha: registro.fecha,
                                tipo: 'fin-otros',
                                hora: registro.horas.fin
                            });
                        }
                        break;
                }
            });
        });

        
        // Calcular registros omitidos
        let totalRegistrosPosibles = 0;
        trabajadores.forEach(pernr => {
            const trabajador = registrosData[pernr];
            trabajador.registros.forEach(registro => {
                switch(registro.tipo) {
                    case 'entrada_salida':
                        totalRegistrosPosibles += 2; // entrada + salida
                        break;
                    case 'desayuno':
                    case 'almuerzo':
                    case 'otros':
                        totalRegistrosPosibles += 2; // inicio + fin
                        break;
                }
            });
        });

        console.log('Datos para backend:', datosParaBackend);
        // console.log('Total registros posibles:', totalRegistrosPosibles);
        
        
        // Implementar llamada AJAX al backend
        const formData = new FormData();
        formData.append('registros', JSON.stringify(datosParaBackend));
        
        fetch('admin_cont.php?controller=index&action=auditor&guardar_nuevos_registros', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                $('#modalNuevoRegistro').modal('hide');
                alertify.success('Registros guardados correctamente');
                // Recargar página después de 2 segundos
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                alertify.error('Error al guardar registros');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertify.error('Error al enviar datos');
        });
    }

    // Función para cerrar el modal y enviar el formulario
    function cerrarModalYEnviar(modalId) {
        const modal = document.getElementById(modalId);
        const form = modal.querySelector('form');
        
        // Cerrar el modal
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        }
        
        // Esperar a que el modal se cierre completamente y luego enviar el formulario
        modal.addEventListener('hidden.bs.modal', function() {
            form.submit();
        }, { once: true });
    }

    // Función para corregir el valor de pernr_nom_trab antes de enviar formularios de modificación
    document.addEventListener('DOMContentLoaded', function() {
        // Interceptar el envío de formularios de modificación
        document.addEventListener('submit', function(e) {
            if (e.target.action && e.target.action.includes('auditor&modificar')) {
                // Buscar el input oculto pernr_nom_trab en el formulario
                const pernrInput = e.target.querySelector('input[name="pernr_nom_trab"]');
                
                if (pernrInput) {
                    // Obtener el select principal de la página
                    const selectPrincipal = document.getElementById('pernr_nom_trab');
                    
                    if (selectPrincipal && selectPrincipal.selectedOptions) {
                        // Obtener los valores seleccionados del select
                        const valoresSeleccionados = Array.from(selectPrincipal.selectedOptions).map(option => option.value);
                        
                        // Asignar los valores al input oculto
                        pernrInput.value = valoresSeleccionados.join(',');
                    }
                }
            }
        });
    });

    // =====================================================
    // FUNCIONES DE VALIDACIÓN PARA EDICIÓN DE REGISTROS
    // =====================================================
    
    // Variable global para almacenar los registros existentes para edición
    window.registrosExistentesGlobalEdicion = [];

    // Función para verificar solapamientos al editar entrada/salida (excluyendo el registro actual)
    function verificarSolapamientoEntradaSalidaEdicion(nuevaHora, tipoRegistroEditando, idRegistroEditando) {
        if (!window.registrosExistentesGlobalEdicion || window.registrosExistentesGlobalEdicion.length === 0) {
            return { solapamiento: false };
        }
        
        function horaAMinutos(hora) {
            const [h, m] = hora.split(':').map(Number);
            return h * 60 + m;
        }
        
        function verificarSolapamiento(inicio1, fin1, inicio2, fin2) {
            const min1 = horaAMinutos(inicio1);
            const max1 = horaAMinutos(fin1);
            const min2 = horaAMinutos(inicio2);
            const max2 = horaAMinutos(fin2);
            
            return (min1 < max2 && max1 > min2);
        }
        
        // Identificar el rango completo al que pertenece el registro editado
        let rangoEditadoCompleto = null;
        
        // Encontrar todos los registros de entrada/salida y agruparlos en rangos
        const registrosEntradaSalida = window.registrosExistentesGlobalEdicion.filter(r => 
            r.tipo_reg === 'entrada' || r.tipo_reg === 'salida'
        );
        
        // Agrupar en rangos completos
        let entradaTemp = null;
        const rangosEntradaSalida = [];
        
        registrosEntradaSalida.sort((a, b) => new Date(a.fecha_reg.date) - new Date(b.fecha_reg.date));
        
        registrosEntradaSalida.forEach(registro => {
            const hora = obtenerHoraDeRegistro(registro);
            if (registro.tipo_reg === 'entrada') {
                entradaTemp = { entrada: hora, entradaId: registro.id };
            } else if (registro.tipo_reg === 'salida' && entradaTemp) {
                rangosEntradaSalida.push({
                    entrada: entradaTemp.entrada,
                    salida: hora,
                    entradaId: entradaTemp.entradaId,
                    salidaId: registro.id
                });
                
                // Verificar si este rango contiene el registro que se está editando
                if (entradaTemp.entradaId == idRegistroEditando || registro.id == idRegistroEditando) {
                    rangoEditadoCompleto = rangosEntradaSalida[rangosEntradaSalida.length - 1];
                }
                
                entradaTemp = null;
            }
        });
        
        // Filtrar otros rangos (excluyendo el rango que se está editando)
        const otrosRangos = rangosEntradaSalida.filter(rango => {
            return !(rango.entradaId == idRegistroEditando || rango.salidaId == idRegistroEditando);
        });
        
        // Calcular el nuevo rango después de la edición
        let nuevoRangoEntrada, nuevoRangoSalida;
        
        if (tipoRegistroEditando === 'entrada') {
            nuevoRangoEntrada = nuevaHora;
            nuevoRangoSalida = rangoEditadoCompleto ? rangoEditadoCompleto.salida : null;
        } else if (tipoRegistroEditando === 'salida') {
            nuevoRangoEntrada = rangoEditadoCompleto ? rangoEditadoCompleto.entrada : null;
            nuevoRangoSalida = nuevaHora;
        }
        
        if (!nuevoRangoEntrada || !nuevoRangoSalida) {
            return { solapamiento: false };
        }
        
        // Verificar solapamiento del nuevo rango con todos los otros rangos
        for (let rango of otrosRangos) {
            if (verificarSolapamiento(nuevoRangoEntrada, nuevoRangoSalida, rango.entrada, rango.salida)) {
                return {
                    solapamiento: true,
                    mensaje: `El rango modificado (${nuevoRangoEntrada} - ${nuevoRangoSalida}) se solaparía con el rango existente (${rango.entrada} - ${rango.salida}).`
                };
            }
        }
        
        return { solapamiento: false };
    }

    // Función para verificar solapamientos al editar descansos (excluyendo el registro actual)
    function verificarSolapamientoDescansosEdicion(nuevaHora, tipoRegistroEditando, idRegistroEditando) {
        if (!window.registrosExistentesGlobalEdicion || window.registrosExistentesGlobalEdicion.length === 0) {
            return { solapamiento: false };
        }
        
        function horaAMinutos(hora) {
            const [h, m] = hora.split(':').map(Number);
            return h * 60 + m;
        }
        
        function verificarSolapamiento(inicio1, fin1, inicio2, fin2) {
            const min1 = horaAMinutos(inicio1);
            const max1 = horaAMinutos(fin1);
            const min2 = horaAMinutos(inicio2);
            const max2 = horaAMinutos(fin2);
            
            return (min1 < max2 && max1 > min2);
        }
        
        // Determinar el tipo de descanso que se está editando
        let tipoDescanso = '';
        if (tipoRegistroEditando.includes('inicio-')) {
            tipoDescanso = tipoRegistroEditando.replace('inicio-', '');
        } else if (tipoRegistroEditando.includes('fin-')) {
            tipoDescanso = tipoRegistroEditando.replace('fin-', '');
        }
        
        // Identificar el rango completo al que pertenece el registro editado
        let rangoEditadoCompleto = null;
        
        // Primero, encontrar todos los rangos completos del mismo tipo
        const registrosTipoActual = window.registrosExistentesGlobalEdicion.filter(r => 
            r.tipo_reg === `inicio-${tipoDescanso}` || r.tipo_reg === `fin-${tipoDescanso}`
        );
        
        // Agrupar en rangos completos para el tipo actual
        let inicioTemp = null;
        const rangosTipoActual = [];
        
        registrosTipoActual.sort((a, b) => new Date(a.fecha_reg.date) - new Date(b.fecha_reg.date));
        
        registrosTipoActual.forEach(registro => {
            const hora = obtenerHoraDeRegistro(registro);
            if (registro.tipo_reg === `inicio-${tipoDescanso}`) {
                inicioTemp = { inicio: hora, inicioId: registro.id };
            } else if (registro.tipo_reg === `fin-${tipoDescanso}` && inicioTemp) {
                rangosTipoActual.push({
                    inicio: inicioTemp.inicio,
                    fin: hora,
                    inicioId: inicioTemp.inicioId,
                    finId: registro.id,
                    tipo: tipoDescanso
                });
                
                // Verificar si este rango contiene el registro que se está editando
                if (inicioTemp.inicioId == idRegistroEditando || registro.id == idRegistroEditando) {
                    rangoEditadoCompleto = rangosTipoActual[rangosTipoActual.length - 1];
                }
                
                inicioTemp = null;
            }
        });
        
        // Obtener todos los descansos de OTROS tipos (no del tipo que se está editando)
        const tiposDescanso = ['desayuno', 'almuerzo', 'otros'];
        const descansosOtrosTipos = [];
        
        tiposDescanso.forEach(tipo => {
            if (tipo !== tipoDescanso) {
                const registrosTipo = window.registrosExistentesGlobalEdicion.filter(r => 
                    r.tipo_reg === `inicio-${tipo}` || r.tipo_reg === `fin-${tipo}`
                );
                
                let inicioTempOtro = null;
                registrosTipo.sort((a, b) => new Date(a.fecha_reg.date) - new Date(b.fecha_reg.date));
                
                registrosTipo.forEach(registro => {
                    const hora = obtenerHoraDeRegistro(registro);
                    if (registro.tipo_reg === `inicio-${tipo}`) {
                        inicioTempOtro = hora;
                    } else if (registro.tipo_reg === `fin-${tipo}` && inicioTempOtro) {
                        descansosOtrosTipos.push({
                            inicio: inicioTempOtro,
                            fin: hora,
                            tipo: tipo
                        });
                        inicioTempOtro = null;
                    }
                });
            }
        });
        
        // Agregar otros rangos del mismo tipo (excluyendo el rango que se está editando)
        const otrosRangosMismoTipo = rangosTipoActual.filter(rango => {
            return !(rango.inicioId == idRegistroEditando || rango.finId == idRegistroEditando);
        });
        
        // Combinar todos los descansos a validar
        const todosLosDescansos = [...descansosOtrosTipos, ...otrosRangosMismoTipo];
        
        // Calcular el nuevo rango después de la edición
        let nuevoRangoInicio, nuevoRangoFin;
        
        if (tipoRegistroEditando.includes('inicio-')) {
            nuevoRangoInicio = nuevaHora;
            nuevoRangoFin = rangoEditadoCompleto ? rangoEditadoCompleto.fin : null;
        } else if (tipoRegistroEditando.includes('fin-')) {
            nuevoRangoInicio = rangoEditadoCompleto ? rangoEditadoCompleto.inicio : null;
            nuevoRangoFin = nuevaHora;
        }
        
        if (!nuevoRangoInicio || !nuevoRangoFin) {
            return { solapamiento: false };
        }
        
        // Verificar solapamiento del nuevo rango con todos los demás descansos
        for (let descanso of todosLosDescansos) {
            if (verificarSolapamiento(nuevoRangoInicio, nuevoRangoFin, descanso.inicio, descanso.fin)) {
                return {
                    solapamiento: true,
                    mensaje: `El ${tipoDescanso} modificado (${nuevoRangoInicio} - ${nuevoRangoFin}) se solaparía con el ${descanso.tipo} existente (${descanso.inicio} - ${descanso.fin}).`
                };
            }
        }
        
        // VALIDACIÓN ADICIONAL: Verificar que el descanso modificado esté dentro de un rango de entrada/salida
        const validacionRango = verificarDescansoEnRangoTrabajo(nuevoRangoInicio, nuevoRangoFin);
        if (!validacionRango.valido) {
            return {
                solapamiento: true,
                mensaje: validacionRango.mensaje
            };
        }
        
        return { solapamiento: false };
    }

    // Función para verificar que un descanso esté dentro del rango de entrada/salida
    function verificarDescansoEnRangoTrabajo(inicioDescanso, finDescanso) {
        if (!window.registrosExistentesGlobalEdicion || window.registrosExistentesGlobalEdicion.length === 0) {
            return {
                valido: false,
                mensaje: 'No se puede validar el rango de trabajo. No hay registros de entrada/salida.'
            };
        }
        
        function horaAMinutos(hora) {
            const [h, m] = hora.split(':').map(Number);
            return h * 60 + m;
        }
        
        // Obtener todos los rangos de entrada/salida del día
        const rangosTrabajo = [];
        let entradaTemp = null;
        
        const registrosEntradaSalida = window.registrosExistentesGlobalEdicion.filter(r => 
            r.tipo_reg === 'entrada' || r.tipo_reg === 'salida'
        ).sort((a, b) => new Date(a.fecha_reg.date) - new Date(b.fecha_reg.date));
        
        registrosEntradaSalida.forEach(registro => {
            const hora = obtenerHoraDeRegistro(registro);
            if (registro.tipo_reg === 'entrada') {
                entradaTemp = hora;
            } else if (registro.tipo_reg === 'salida' && entradaTemp) {
                rangosTrabajo.push({
                    entrada: entradaTemp,
                    salida: hora
                });
                entradaTemp = null;
            }
        });
        
        if (rangosTrabajo.length === 0) {
            return {
                valido: false,
                mensaje: 'No se puede añadir un descanso sin un rango de entrada/salida completo.'
            };
        }
        
        // Verificar si el descanso está completamente dentro de algún rango de trabajo
        const minutosInicioDescanso = horaAMinutos(inicioDescanso);
        const minutosFinDescanso = horaAMinutos(finDescanso);
        
        for (let rango of rangosTrabajo) {
            const minutosEntrada = horaAMinutos(rango.entrada);
            const minutosSalida = horaAMinutos(rango.salida);
            
            if (minutosInicioDescanso >= minutosEntrada && minutosFinDescanso <= minutosSalida) {
                return { valido: true };
            }
        }
        
        return {
            valido: false,
            mensaje: `El descanso (${inicioDescanso} - ${finDescanso}) debe estar completamente dentro de un rango de entrada/salida.`
        };
    }

    // Manejar envío del formulario de edición
    document.addEventListener('DOMContentLoaded', function() {
        const editarForm = document.getElementById("editarForm");
        if (editarForm) {
            editarForm.addEventListener("submit", function (e) {
                e.preventDefault();
                
                const fechaInput = document.getElementById("editarFecha");
                const horaInput = document.getElementById("editarHora");
                const comentarioInput = document.getElementById("editarComentario");
                const tipoRegistroInput = document.getElementById("editarTipoRegistro");
                const idInput = document.getElementById("editarId");
                
                // Validar campos obligatorios
                let esValido = true;
                
                if (!fechaInput.value) {
                    fechaInput.classList.add('is-invalid');
                    esValido = false;
                } else {
                    fechaInput.classList.remove('is-invalid');
                }
                
                if (!horaInput.value) {
                    horaInput.classList.add('is-invalid');
                    esValido = false;
                } else {
                    horaInput.classList.remove('is-invalid');
                }
                
                // Validar comentario si se proporciona
                if (comentarioInput.value.length > 149) {
                    comentarioInput.classList.add('is-invalid');
                    esValido = false;
                } else {
                    comentarioInput.classList.remove('is-invalid');
                }
                
                // Validar caracteres prohibidos en comentario
                const caracteresProhibidos = /[<>'"`;|-]|--|script/gi;
                if (comentarioInput.value && caracteresProhibidos.test(comentarioInput.value)) {
                    comentarioInput.classList.add('is-invalid');
                    alertify.alert('Error', 'El comentario contiene caracteres no permitidos.');
                    esValido = false;
                } else if (comentarioInput.value.length <= 149) {
                    comentarioInput.classList.remove('is-invalid');
                }
                
                if (esValido) {
                    // NUEVA VALIDACIÓN: Verificar campos autocompletados antes del envío
                    validarCamposAutocompletados();
                    
                    // Realizar validaciones de solapamiento específicas
                    const tipoRegistro = tipoRegistroInput.value;
                    const nuevaHora = horaInput.value;
                    const idRegistro = idInput.value;
                    
                    // Validar según el tipo de registro
                    if (tipoRegistro === 'entrada' || tipoRegistro === 'salida') {
                        const validacionEntradaSalida = verificarSolapamientoEntradaSalidaEdicion(nuevaHora, tipoRegistro, idRegistro);
                        if (validacionEntradaSalida.solapamiento) {
                            alertify.alert('Error de Solapamiento', validacionEntradaSalida.mensaje);
                            return;
                        }
                    } else if (tipoRegistro.includes('inicio-') || tipoRegistro.includes('fin-')) {
                        const validacionDescansos = verificarSolapamientoDescansosEdicion(nuevaHora, tipoRegistro, idRegistro);
                        if (validacionDescansos.solapamiento) {
                            alertify.alert('Error de Solapamiento', validacionDescansos.mensaje);
                            return;
                        }
                    }
                    
                    // Si todas las validaciones pasan, mostrar confirmación
                    alertify.confirm('Confirmar Edición', '¿Estás seguro de que quieres guardar estos cambios?', 
                        function() {
                            // Combinar fecha y hora en el campo fecha_mod
                            const fechaInput = document.getElementById('editarFecha');
                            const horaInput = document.getElementById('editarHora');
                            const fechaModInput = document.getElementById('editarFechaMod');
                            
                            if (fechaInput.value && horaInput.value) {
                                fechaModInput.value = fechaInput.value + 'T' + horaInput.value;
                            }
                            
                            // Cerrar modal y enviar formulario
                            const modal = bootstrap.Modal.getInstance(document.getElementById('editarModal'));
                            modal.hide();
                            
                            setTimeout(() => {
                                editarForm.submit();
                            }, 100);
                        },
                        function() {
                            // Cancelar - no hacer nada
                        }
                    );
                }
            });
        }
    });

    // Exponer funciones globalmente para uso desde HTML
    window.editarRegistro = function(id, pernr, fecha, hora, tipoRegistro, comentario) {
        // Cargar registros existentes para la fecha
        window.cargarRegistrosExistentesParaEdicion(fecha, pernr);
        
        // Poblar el modal con los datos del registro
        document.getElementById('editarId').value = id;
        document.getElementById('editarPernr').value = pernr;
        document.getElementById('editarTipoRegistro').value = tipoRegistro;
        document.getElementById('editarFecha').value = fecha;
        document.getElementById('editarHora').value = hora;
        document.getElementById('editarComentario').value = comentario || '';
        
        // Mostrar el modal
        const editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
        editarModal.show();
    };

    // Función para cargar registros existentes para edición
    window.cargarRegistrosExistentesParaEdicion = async function(fecha, pernr) {
        if (!fecha || !pernr) return;
        
        try {
            const response = await fetch(`auto.php?registros_trabajador&fecha=${fecha}&pernr=${pernr}`);
            const data = await response.json();
            window.registrosExistentesGlobalEdicion = data.success ? data.data : [];
        } catch (error) {
            console.error('Error al cargar registros para edición:', error);
            window.registrosExistentesGlobalEdicion = [];
        }
    };
