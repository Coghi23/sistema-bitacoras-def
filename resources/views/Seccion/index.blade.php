@extends('Template-administrador')

@section('title', 'Registro de Sección')

@section('content')
<div class="wrapper">
    <div class="main-content">

        {{-- Búsqueda + botón agregar --}}
        <div class="search-bar-wrapper mb-4">
            <div class="search-bar">
                <form id="busquedaForm" method="GET" action="{{ route('seccion.index') }}" class="w-100 position-relative">
                    <span class="search-icon">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control"
                        placeholder="Buscar sección..." name="busquedaSeccion" 
                        value="{{ request('busquedaSeccion') }}" id="inputBusqueda" autocomplete="off">
                    @if(request('busquedaSeccion'))
                    <button type="button" class="btn btn-outline-secondary border-0 position-absolute end-0 top-50 translate-middle-y me-2" id="limpiarBusqueda" title="Limpiar búsqueda" style="background: transparent;">
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
                                    <h5 class="modal-title">Edición de Sección</h5>
                                </div>
                                <div class="modal-body px-4 py-4">

                                    {{-- Mostrar errores de validación para editar --}}
                                    @if (session('modal_editar_id') && session('modal_editar_id') == $seccion->id && $errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <form action="{{ route('seccion.update', $seccion->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="form_type" value="edit">
                                        <input type="hidden" name="seccion_id" value="{{ $seccion->id }}">
                                        <div class="mb-3">

                                            <label class="form-label fw-bold">Sección</label>
                                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"

                                                value="{{ old('nombre', $seccion->nombre) }}" required>
                                            @error('nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Especialidad</label>
                                            @error('especialidades')
                                                <div class="text-danger small mb-2">{{ $message }}</div>
                                            @enderror
                                            <div class="row">
                                                @foreach ($especialidades as $especialidad)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="especialidades[]" 
                                                                   value="{{ $especialidad->id }}"
                                                                   id="especialidad-edit-{{ $seccion->id }}-{{ $especialidad->id }}"
                                                                   @if(session('modal_editar_id') && session('modal_editar_id') == $seccion->id && old('especialidades') ? 
                                                                       in_array($especialidad->id, old('especialidades', [])) : 
                                                                       ($seccion->especialidades->contains('id', $especialidad->id) && 
                                                                        $seccion->especialidades->where('id', $especialidad->id)->first()->pivot->condicion == 1))
                                                                       checked
                                                                   @endif>
                                                            <label class="form-check-label" for="especialidad-edit-{{ $seccion->id }}-{{ $especialidad->id }}">
                                                                {{ $especialidad->nombre }}
                                                            </label>
                                                        </div>
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
                                        <p class="modal-text">¿Desea Eliminar la Sección?</p>
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
                <h5 class="modal-title">Crear Nueva Sección</h5>
            </div>
            <div class="modal-body px-4 py-4">

                {{-- Mostrar errores de validación para crear --}}
                @if (session('modal_crear') && $errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <form id="formCrearSeccion" action="{{ route('seccion.store') }}" method="POST">

                    @csrf
                    <input type="hidden" name="form_type" value="create">
                    <div class="mb-3">

                        <label class="form-label fw-bold">Sección</label>
                        <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre') }}">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
                        @error('especialidades')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror
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

    // ========== FUNCIONES PARA CREAR SECCIÓN ==========

    function agregarEspecialidad() {
        const select = document.getElementById('selectEspecialidad');
        const selectedOption = select.options[select.selectedIndex];
        
        if (!selectedOption || !selectedOption.value) {
            alert('Por favor seleccione una especialidad');
            return;
        }
        
        const id = selectedOption.value;
        const nombre = selectedOption.getAttribute('data-nombre');
        
        // Verificar si ya está agregada
        if (especialidadesAgregadas.includes(id)) {
            alert('Esta especialidad ya está agregada');
            return;
        }
        
        // Agregar al array de control
        especialidadesAgregadas.push(id);
        
        // Crear elemento visual
        const contenedor = document.getElementById('especialidadesSeleccionadas');
        const especialidadDiv = document.createElement('div');
        especialidadDiv.className = 'input-group mt-2';
        especialidadDiv.setAttribute('data-id', id);
        especialidadDiv.innerHTML = `
            <input type="text" class="form-control" value="${nombre}" readonly>
            <input type="hidden" name="especialidades[]" value="${id}">
            <button type="button" class="btn btn-danger" onclick="quitarEspecialidad(this, '${id}')">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        contenedor.appendChild(especialidadDiv);
        select.selectedIndex = 0;
    }

    function quitarEspecialidad(boton, id) {
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
        especialidadDiv.innerHTML = `
            <input type="text" class="form-control" value="${nombre}" readonly>
            <input type="hidden" name="especialidades[]" value="${id}">
            <button type="button" class="btn btn-danger" onclick="quitarEspecialidad(this, '${id}')">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        contenedor.appendChild(especialidadDiv);
    }

    // ========== FUNCIONES DE BÚSQUEDA ==========

    // Funcionalidad de búsqueda en tiempo real
    let timeoutId;
    const inputBusqueda = document.getElementById('inputBusqueda');
    const formBusqueda = document.getElementById('busquedaForm');
    const btnLimpiar = document.getElementById('limpiarBusqueda');
    
    if (inputBusqueda) {
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
            inputBusqueda.value = '';
            window.location.href = '{{ route("seccion.index") }}';
        });
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
</script>
@endsection


