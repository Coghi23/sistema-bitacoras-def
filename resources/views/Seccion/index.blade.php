@extends('Template-administrador')


@section('title', 'Registro de Sección')


@section('content')
<style>
    /* Fix for dropdown arrow positioning */
    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
        background-repeat: no-repeat !important;
        background-position: right 0.75rem center !important;
        background-size: 16px 12px !important;
        padding-right: 2.25rem !important;
    }
   
    /* Ensure no conflicting pseudo-elements */
    .form-select::after,
    .form-select::before {
        display: none !important;
    }
   
    /* Fix for input-group select styling */
    .input-group .form-select {
        position: relative;
        z-index: 1;
    }
</style>
<div class="wrapper">
    <div class="main-content">


        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
                <form id="busquedaForm" method="GET" action="{{ route('seccion.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input
                        type="text"
                        class="form-control"
                        placeholder="Buscar sección..."
                        name="busquedaSeccion"
                        value="{{ request('busquedaSeccion') }}"
                        id="inputBusqueda"
                        autocomplete="off"
                    >
                    @if(request('busquedaSeccion'))
                    <button
                        type="button"
                        class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2"
                        id="limpiarBusqueda"
                        title="Limpiar búsqueda"
                        style="background: transparent;"
                    >
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            @if(Auth::user() && !Auth::user()->hasRole('director'))
                <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center ms-3 btn-agregar"
                    data-bs-toggle="modal" data-bs-target="#modalAgregarSeccion"
                    title="Agregar Sección" style="background-color: #134496; font-size: 1.2rem;">
                    Agregar <i class="bi bi-plus-circle ms-2"></i>
                </button>
            @endif
        </div>


        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaSeccion'))
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $secciones->count() }} resultado(s) para "<strong>{{ request('busquedaSeccion') }}</strong>"
                    <a href="{{ route('seccion.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif


       


        {{-- Tabla --}}
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Sección</th>
                        <th class="text-center">Especialidad</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($secciones as $seccion)
                    <tr>
                        @if ($seccion->condicion == 1)
                            <td class="text-center">{{ $seccion->nombre }}</td>
                            <td class="text-center">
                                @if($seccion->especialidades->count() > 0)
                                    @foreach($seccion->especialidades as $especialidad)
                                        {{ $especialidad->nombre }}@if(!$loop->last), @endif
                                    @endforeach
                                @else
                                    <span class="text-muted">Sin especialidades</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(Auth::user() && !Auth::user()->hasRole('director'))
                                <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal"
                                    data-bs-target="#modalEditarSeccion-{{ $seccion->id }}">
                                    <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                </button>
                                <button class="btn btn-link text-danger p-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEliminarSeccion-{{ $seccion->id }}">
                                    <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                </button>
                                @else
                                <span class="text-muted">Solo vista</span>
                                @endif
                            </td>
                        @endif
                    </tr>


                    {{-- Modal Editar --}}
                    <div class="modal fade" id="modalEditarSeccion-{{ $seccion->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header modal-header-custom">
                                    <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                        <i class="bi bi-arrow-left"></i>
                                    </button>
                                    <h5 class="modal-title">Registro de sección</h5>
                                </div>
                <div class="modal-body px-4 py-4">
                    <form action="{{ route('seccion.update', $seccion->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sección</label>
                            <input type="text" name="nombre" class="form-control @if(session('modal_editar_id') && session('modal_editar_id') == $seccion->id && $errors->has('nombre')) is-invalid @endif"
                                value="{{ old('nombre', $seccion->nombre) }}" required>
                            @if(session('modal_editar_id') && session('modal_editar_id') == $seccion->id && $errors->has('nombre'))
                                <div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
                            @endif
                        </div>


                        <div class="mb-3">
                            <label class="form-label fw-bold">Especialidad</label>
                            @if(session('modal_editar_id') && session('modal_editar_id') == $seccion->id && $errors->has('especialidades'))
                                <div class="text-danger small mb-2">
                                    {{ $errors->first('especialidades') }}
                                    <br><small><i class="bi bi-info-circle"></i> Una sección debe tener al menos una especialidad asignada.</small>
                                </div>
                            @endif                                            <!-- Especialidades actualmente asignadas como checkboxes ocultos -->
                                            <div style="display: none;">
                                                @foreach($especialidades as $especialidad)
                                                    <input type="checkbox"
                                                           id="esp-{{ $seccion->id }}-{{ $especialidad->id }}"
                                                           name="especialidades[]"
                                                           value="{{ $especialidad->id }}"
                                                           @if($seccion->especialidades->where('id', $especialidad->id)->where('pivot.condicion', 1)->count() > 0) checked @endif>
                                                @endforeach
                                            </div>
                                           
                                            <!-- Select para agregar especialidades -->
                                            <div class="input-group dynamic-group mb-3">
                                                <select id="selectEspecialidadEdit-{{ $seccion->id }}" class="form-select">
                                                    <option value="">Seleccione una especialidad para agregar</option>
                                                    @foreach ($especialidades as $especialidad)
                                                        <option value="{{ $especialidad->id }}" data-nombre="{{ $especialidad->nombre }}">{{ $especialidad->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-success d-flex align-items-center justify-content-center" onclick="agregarEspecialidadSimple('{{ $seccion->id }}');" style="min-width: 38px; padding: 8px;">
                                                    <i class="bi bi-plus"></i>
                                                </button>
                                            </div>
                                           
                                            <!-- Contenedor para mensajes de validación -->
                                            <div id="mensajeValidacion-{{ $seccion->id }}" class="alert alert-danger d-none" role="alert">
                                                <i class="bi bi-exclamation-triangle"></i> <span id="textoMensaje-{{ $seccion->id }}"></span>
                                            </div>
                                           
                                            <!-- Especialidades visibles actualmente asignadas -->
                                            <div id="especialidadesVisuales-{{ $seccion->id }}">
                                                @foreach($seccion->especialidades->where('pivot.condicion', 1) as $especialidadAsignada)
                                                    <div class="input-group mt-2 especialidad-visual" data-id="{{ $especialidadAsignada->id }}">
                                                        <input type="text" class="form-control" value="{{ $especialidadAsignada->nombre }}" readonly>
                                                        <button type="button" class="btn btn-danger" onclick="eliminarEspecialidadSimple('{{ $seccion->id }}', '{{ $especialidadAsignada->id }}')">
                                                            <i class="bi bi-x"></i>
                                                        </button>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>


                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary">Modificar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- Modal Eliminar --}}
                        <div class="modal fade" id="modalEliminarSeccion-{{ $seccion->id }}" tabindex="-1" aria-labelledby="modalSeccionEliminarLabel-{{ $seccion->id }}"
                        aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content custom-modal">
                                    <div class="modal-body text-center">
                                        <div class="icon-container">
                                            <div class="circle-icon">
                                            <i class="bi bi-exclamation-circle"></i>
                                            </div>
                                        </div>
                                        <p class="modal-text">¿Desea eliminar la sección?</p>
                                        <div class="btn-group-custom">
                                            <form action="{{ route('seccion.destroy', ['seccion' => $seccion->id]) }}" method="post">
                                                @method('DELETE')
                                                @csrf
                                                <button type="submit" class="btn btn-custom {{ $seccion->condicion == 1 }}">Sí</button>
                                                <button type="button" class="btn btn-custom" data-bs-dismiss="modal">No</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>


{{-- Modal Crear Sección --}}
<div class="modal fade" id="modalAgregarSeccion" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Registro de sección</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form id="formCrearSeccion" action="{{ route('seccion.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sección</label>
                        <input type="text" name="nombre" class="form-control @if(session('modal_crear') && $errors->has('nombre')) is-invalid @endif"
                               value="{{ old('nombre') }}">
                        @if(session('modal_crear') && $errors->has('nombre'))
                            <div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
                        @endif
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
                        @if(session('modal_crear') && $errors->has('especialidades'))
                            <div class="text-danger small mb-2">
                                {{ $errors->first('especialidades') }}
                                <br><small><i class="bi bi-info-circle"></i> Una sección debe tener al menos una especialidad asignada.</small>
                            </div>
                        @endif
                        <div id="especialidades">
                            <div class="input-group dynamic-group">
                                <select id="selectEspecialidad" class="form-select">
                                    <option value="">Seleccione una especialidad</option>
                                    @foreach ($especialidades as $especialidad)
                                        <option value="{{ $especialidad->id }}" data-nombre="{{ $especialidad->nombre }}">{{ $especialidad->nombre }}</option>
                                    @endforeach
                                </select>
                                <button type="button" class="btn btn-success d-flex align-items-center justify-content-center" onclick="agregarEspecialidad()" style="height: 9%; min-width: 38px; padding: 0;">
                                    <i class="bi bi-plus" style="height: 49px;"></i>
                                </button>
                            </div>
                            <!-- Contenedor para especialidades seleccionadas -->
                            <div id="especialidadesSeleccionadas" class="mt-2"></div>
                           
                            <!-- Contenedor para mensajes de validación -->
                            <div id="mensajeValidacionCrear" class="alert alert-danger d-none mt-2" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <span id="textoMensajeCrear"></span>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    // Variables globales para crear sección
    let especialidadesAgregadas = [];


    // ========== FUNCIONES PARA VALIDACIÓN ==========
   
    function mostrarMensajeValidacion(seccionId, mensaje) {
        const contenedorMensaje = document.getElementById(`mensajeValidacion-${seccionId}`);
        const textoMensaje = document.getElementById(`textoMensaje-${seccionId}`);
       
        if (contenedorMensaje && textoMensaje) {
            textoMensaje.textContent = mensaje;
            contenedorMensaje.classList.remove('d-none');
           
            // Auto-ocultar después de 4 segundos
            setTimeout(() => {
                ocultarMensajeValidacion(seccionId);
            }, 4000);
        }
    }
   
    function ocultarMensajeValidacion(seccionId) {
        const contenedorMensaje = document.getElementById(`mensajeValidacion-${seccionId}`);
        if (contenedorMensaje) {
            contenedorMensaje.classList.add('d-none');
        }
    }
   
    // Funciones para validación del modal de crear
    function mostrarMensajeValidacionCrear(mensaje) {
        const contenedorMensaje = document.getElementById('mensajeValidacionCrear');
        const textoMensaje = document.getElementById('textoMensajeCrear');
       
        if (contenedorMensaje && textoMensaje) {
            textoMensaje.textContent = mensaje;
            contenedorMensaje.classList.remove('d-none');
           
            // Auto-ocultar después de 4 segundos
            setTimeout(() => {
                ocultarMensajeValidacionCrear();
            }, 4000);
        }
    }
   
    function ocultarMensajeValidacionCrear() {
        const contenedorMensaje = document.getElementById('mensajeValidacionCrear');
        if (contenedorMensaje) {
            contenedorMensaje.classList.add('d-none');
        }
    }


    // ========== FUNCIONES PARA CREAR SECCIÓN ==========


    function agregarEspecialidad() {
        // Ocultar mensaje de validación anterior
        ocultarMensajeValidacionCrear();
       
        const select = document.getElementById('selectEspecialidad');
        const selectedOption = select.options[select.selectedIndex];
       
        if (!selectedOption || !selectedOption.value) {
            mostrarMensajeValidacionCrear('Por favor seleccione una especialidad');
            return;
        }
       
        const id = selectedOption.value;
        const nombre = selectedOption.getAttribute('data-nombre');
       
        // Verificar si ya está agregada
        if (especialidadesAgregadas.includes(id)) {
            mostrarMensajeValidacionCrear('Esta especialidad ya está agregada');
            return;
        }
       
        // Agregar al array de control
        especialidadesAgregadas.push(id);
       
        // Crear elemento visual
        const contenedor = document.getElementById('especialidadesSeleccionadas');
        const especialidadDiv = document.createElement('div');
        especialidadDiv.className = 'input-group mt-2';
        especialidadDiv.setAttribute('data-id', id);
       
        // Crear los elementos por separado para evitar problemas con comillas
        const inputTexto = document.createElement('input');
        inputTexto.type = 'text';
        inputTexto.className = 'form-control';
        inputTexto.value = nombre;
        inputTexto.readOnly = true;
       
        const inputHidden = document.createElement('input');
        inputHidden.type = 'hidden';
        inputHidden.name = 'especialidades[]';
        inputHidden.value = id;
       
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.className = 'btn btn-danger';
        btnEliminar.innerHTML = '<i class="bi bi-x"></i>';
        btnEliminar.onclick = function() {
            quitarEspecialidad(this, id);
        };
       
        especialidadDiv.appendChild(inputTexto);
        especialidadDiv.appendChild(inputHidden);
        especialidadDiv.appendChild(btnEliminar);
       
        contenedor.appendChild(especialidadDiv);
        select.selectedIndex = 0;
    }


    function quitarEspecialidad(boton, id) {
        // Verificar si es la última especialidad
        const contenedor = document.getElementById('especialidadesSeleccionadas');
        const especialidadesActuales = contenedor.querySelectorAll('[data-id]');
       
        if (especialidadesActuales.length <= 1) {
            alert('No se puede eliminar la última especialidad. Una sección debe tener al menos una especialidad asignada.');
            return;
        }
       
        // Remover del array
        especialidadesAgregadas = especialidadesAgregadas.filter(espId => espId !== id);
        // Remover del DOM
        boton.parentElement.remove();
    }


    // Función para agregar especialidad cuando se repobla desde old()
    function agregarEspecialidadOld(id, nombre) {
        // Verificar si ya está agregada
        if (especialidadesAgregadas.includes(id)) {
            return;
        }
       
        // Agregar al array de control
        especialidadesAgregadas.push(id);
       
        // Crear elemento visual
        const contenedor = document.getElementById('especialidadesSeleccionadas');
        const especialidadDiv = document.createElement('div');
        especialidadDiv.className = 'input-group mt-2';
        especialidadDiv.setAttribute('data-id', id);
       
        // Crear los elementos por separado para evitar problemas con comillas
        const inputTexto = document.createElement('input');
        inputTexto.type = 'text';
        inputTexto.className = 'form-control';
        inputTexto.value = nombre;
        inputTexto.readOnly = true;
       
        const inputHidden = document.createElement('input');
        inputHidden.type = 'hidden';
        inputHidden.name = 'especialidades[]';
        inputHidden.value = id;
       
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.className = 'btn btn-danger';
        btnEliminar.innerHTML = '<i class="bi bi-x"></i>';
        btnEliminar.onclick = function() {
            quitarEspecialidad(this, id);
        };
       
        especialidadDiv.appendChild(inputTexto);
        especialidadDiv.appendChild(inputHidden);
        especialidadDiv.appendChild(btnEliminar);
       
        contenedor.appendChild(especialidadDiv);
    }


    // ========== ABRIR MODALES CON ERRORES ==========
   
    // Abrir modal de crear si hay errores de validación para crear
    @if (session('modal_crear'))
        document.addEventListener('DOMContentLoaded', function() {
            var modalCrear = new bootstrap.Modal(document.getElementById('modalAgregarSeccion'));
            modalCrear.show();
           
            // Repoblar especialidades seleccionadas
            @if(old('especialidades'))
                const especialidadesOld = @json(old('especialidades'));
                const especialidadesData = @json($especialidades->pluck('nombre', 'id'));
               
                especialidadesOld.forEach(function(id) {
                    if (especialidadesData[id]) {
                        agregarEspecialidadOld(id, especialidadesData[id]);
                    }
                });
            @endif
        });
    @endif


    // Abrir modal de editar si hay errores de validación para editar
    @if (session('modal_editar_id'))
        document.addEventListener('DOMContentLoaded', function() {
            var modalEditar = new bootstrap.Modal(document.getElementById('modalEditarSeccion-{{ session('modal_editar_id') }}'));
            modalEditar.show();
        });
    @endif


    // ========== FUNCIONES DE BÚSQUEDA ==========


    // Funcionalidad de búsqueda en tiempo real - con verificación de existencia
    document.addEventListener('DOMContentLoaded', function() {
        let timeoutId;
        const inputBusqueda = document.getElementById('inputBusqueda');
        const formBusqueda = document.getElementById('busquedaForm');
        const btnLimpiar = document.getElementById('limpiarBusqueda');
       
        if (inputBusqueda && formBusqueda) {
            inputBusqueda.addEventListener('input', function() {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(function() {
                    formBusqueda.submit();
                }, 500);
            });
           
            inputBusqueda.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formBusqueda.submit();
                }
            });
        }
       
        if (btnLimpiar) {
            btnLimpiar.addEventListener('click', function() {
                if (inputBusqueda) {
                    inputBusqueda.value = '';
                }
                window.location.href = '{{ route("seccion.index") }}';
            });
        }
       
    });


    // Función simplificada para agregar especialidad
    function agregarEspecialidadSimple(seccionId) {
        // Ocultar mensaje de validación anterior
        ocultarMensajeValidacion(seccionId);
       
        const select = document.getElementById(`selectEspecialidadEdit-${seccionId}`);
        const especialidadId = select.value;
        const nombreEspecialidad = select.options[select.selectedIndex].getAttribute('data-nombre');
       
        if (!especialidadId) {
            mostrarMensajeValidacion(seccionId, 'Por favor seleccione una especialidad');
            return;
        }
       
        // Verificar que no esté ya visible en la interfaz
        const contenedorVisual = document.getElementById(`especialidadesVisuales-${seccionId}`);
        const existeVisual = contenedorVisual.querySelector(`[data-id="${especialidadId}"]`);
       
        if (existeVisual) {
            mostrarMensajeValidacion(seccionId, 'Esta especialidad ya está asignada a la sección');
            return;
        }
       
        // Marcar el checkbox oculto
        const checkbox = document.getElementById(`esp-${seccionId}-${especialidadId}`);
        if (checkbox) {
            checkbox.checked = true;
        }
       
        // Agregar visual
        const elementoVisual = document.createElement('div');
        elementoVisual.className = 'input-group mt-2 especialidad-visual';
        elementoVisual.setAttribute('data-id', especialidadId);
        elementoVisual.innerHTML = `
            <input type="text" class="form-control" value="${nombreEspecialidad}" readonly>
            <button type="button" class="btn btn-danger" onclick="eliminarEspecialidadSimple('${seccionId}', '${especialidadId}')">
                <i class="bi bi-x"></i>
            </button>
        `;
        contenedorVisual.appendChild(elementoVisual);
       
        // Resetear select
        select.value = '';
       
        // Actualizar opciones del select para ocultar la especialidad agregada
        actualizarOpcionesSelect(seccionId);
    }
   
    // Función simplificada para eliminar especialidad
    function eliminarEspecialidadSimple(seccionId, especialidadId) {
        // Desmarcar checkbox oculto
        const checkbox = document.getElementById(`esp-${seccionId}-${especialidadId}`);
        if (checkbox) {
            checkbox.checked = false;
        }
       
        // Eliminar elemento visual
        const elementoVisual = document.querySelector(`#especialidadesVisuales-${seccionId} .especialidad-visual[data-id="${especialidadId}"]`);
        if (elementoVisual) {
            elementoVisual.remove();
        }
       
        // Actualizar opciones del select para mostrar la especialidad nuevamente
        actualizarOpcionesSelect(seccionId);
    }
   
    // Función para actualizar las opciones del select
    function actualizarOpcionesSelect(seccionId) {
        const select = document.getElementById(`selectEspecialidadEdit-${seccionId}`);
        const contenedorVisual = document.getElementById(`especialidadesVisuales-${seccionId}`);
       
        // Obtener IDs de especialidades visualmente asignadas
        const especialidadesAsignadas = [];
        const elementosVisuales = contenedorVisual.querySelectorAll('.especialidad-visual');
        elementosVisuales.forEach(elemento => {
            especialidadesAsignadas.push(elemento.getAttribute('data-id'));
        });
       
        // Mostrar/ocultar opciones del select
        Array.from(select.options).forEach(option => {
            if (option.value === '') {
                // Mantener la opción por defecto
                option.style.display = '';
                return;
            }
           
            if (especialidadesAsignadas.includes(option.value)) {
                option.style.display = 'none';
            } else {
                option.style.display = '';
            }
        });
       
        // Resetear selección si la opción actual está oculta
        if (select.value && especialidadesAsignadas.includes(select.value)) {
            select.value = '';
        }
    }
   
    // Event listener para formularios de editar
    document.addEventListener('DOMContentLoaded', function() {
        // Event listener para formulario de crear
        const modalCrear = document.getElementById('modalAgregarSeccion');
        const formCrear = modalCrear.querySelector('form');
       
        if (formCrear) {
            formCrear.addEventListener('submit', function(e) {
                // Ocultar mensajes de validación anteriores
                ocultarMensajeValidacionCrear();
               
                // Validar que haya al menos una especialidad
                if (especialidadesAgregadas.length === 0) {
                    e.preventDefault();
                    mostrarMensajeValidacionCrear('Debe asignar al menos una especialidad a la sección');
                    return false;
                }
            });
        }
       
        // Limpiar mensajes al abrir modal de crear
        modalCrear.addEventListener('shown.bs.modal', function() {
            ocultarMensajeValidacionCrear();
        });
       
        // Limpiar mensajes al cerrar modal de crear
        modalCrear.addEventListener('hidden.bs.modal', function() {
            ocultarMensajeValidacionCrear();
        });
       
        // Event listener para formulario de editar
        const modalEditar = document.getElementById('modalEditarSeccion');
       
        modalEditar.addEventListener('shown.bs.modal', function(event) {
            const button = event.relatedTarget;
            const seccionId = button.getAttribute('data-id');
            const form = modalEditar.querySelector('form');
           
            // Ocultar mensajes de validación al abrir el modal
            ocultarMensajeValidacion(seccionId);
           
            // Actualizar opciones del select al abrir el modal
            actualizarOpcionesSelect(seccionId);
           
            // Agregar event listener al formulario si no lo tiene
            if (!form.hasAttribute('data-listener-added')) {
                form.addEventListener('submit', function(e) {
                    // Ocultar mensajes de validación anteriores
                    ocultarMensajeValidacion(seccionId);
                   
                    // Validar especialidades usando checkboxes
                    const checkboxesChecked = form.querySelectorAll('input[name="especialidades[]"]:checked');
                   
                    if (checkboxesChecked.length === 0) {
                        e.preventDefault();
                        mostrarMensajeValidacion(seccionId, 'Debe asignar al menos una especialidad a la sección');
                        return false;
                    }
                });
               
                form.setAttribute('data-listener-added', 'true');
            }
        });
       
        // Limpiar mensajes al cerrar modal de editar
        modalEditar.addEventListener('hidden.bs.modal', function() {
            // Obtener el ID de la sección del modal
            const form = modalEditar.querySelector('form');
            const seccionIdInput = form ? form.querySelector('input[name="id"]') : null;
            const seccionId = seccionIdInput ? seccionIdInput.value : null;
           
            if (seccionId) {
                ocultarMensajeValidacion(seccionId);
            }
        });
    });
</script>
@endsection








