@extends('Template-administrador')

@section('title', 'Registro de Sección')

@section('content')
<div class="wrapper">
    <div class="main-content">

        {{-- Búsqueda + botón agregar --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group w-50">
                <form id="busquedaForm" method="GET" action="{{ route('seccion.index') }}" class="d-flex w-100">
                    <span class="input-group-text bg-white border-white">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 shadow-sm"
                        placeholder="Buscar sección..." name="busquedaSeccion" 
                        value="{{ request('busquedaSeccion') }}" id="inputBusqueda" autocomplete="off" 
                        style="border-radius: 20px;">
                    @if(request('busquedaSeccion'))
                    <button type="button" class="btn btn-outline-secondary border-0" id="limpiarBusqueda" title="Limpiar búsqueda">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center"
                data-bs-toggle="modal" data-bs-target="#modalAgregarSeccion"
                title="Agregar Sección" style="background-color: #134496; font-size: 1.2rem; @if(Auth::user() && Auth::user()->hasRole('director')) display: none; @endif">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
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
                                    <form id="formEditarSeccion-{{ $seccion->id }}" action="{{ route('seccion.update', $seccion->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sección</label>
                                            <input type="text" name="nombre" class="form-control"
                                                value="{{ old('nombre', $seccion->nombre) }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Especialidad</label>
                                            <div id="especialidades-{{ $seccion->id }}">
                                                <div class="input-group dynamic-group">
                                                    <select id="selectEspecialidadEdit-{{ $seccion->id }}" class="form-select">
                                                        <option value="">Seleccione una especialidad</option>
                                                        @foreach ($especialidades as $especialidad)
                                                            <option value="{{ $especialidad->id }}" data-nombre="{{ $especialidad->nombre }}">{{ $especialidad->nombre }}</option>
                                                        @endforeach
                                                    </select>
                                                    <button type="button" class="btn btn-success" onclick="agregarEspecialidadEdit({{ $seccion->id }})">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                                <!-- Contenedor para especialidades seleccionadas -->
                                                <div id="especialidadesSeleccionadasEdit-{{ $seccion->id }}" class="mt-2">
                                                    {{-- Mostrar especialidades ya asociadas --}}
                                                    @foreach($seccion->especialidades as $especialidad)
                                                        @if($especialidad->pivot->condicion == 1)
                                                            <div class="input-group mt-2" data-id="{{ $especialidad->id }}">
                                                                <input type="text" class="form-control" value="{{ $especialidad->nombre }}" readonly>
                                                                <input type="hidden" name="especialidades[]" value="{{ $especialidad->id }}">
                                                                <button type="button" class="btn btn-danger" onclick="quitarEspecialidadEdit(this, '{{ $especialidad->id }}', {{ $seccion->id }})">
                                                                    <i class="bi bi-x"></i>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
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
                {{-- Mostrar errores de validación --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif
                
                <form id="formCrearSeccion" action="{{ route('seccion.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sección</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Especialidad</label>
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
                        <button type="submit" class="btn btn-primary" onclick="verificarFormulario(event)">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
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
            }, 500); // Espera 500ms después de que el usuario deje de escribir
        });
        
        // También permitir búsqueda al presionar Enter
        inputBusqueda.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                formBusqueda.submit();
            }
        });
    }
    
    // Funcionalidad del botón limpiar
    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            inputBusqueda.value = '';
            window.location.href = '{{ route("seccion.index") }}';
        });
    }



    // Array para llevar control de especialidades ya agregadas
    let especialidadesAgregadas = [];

    // Función para agregar especialidad desde el select
    function agregarEspecialidad() {
        console.log('Función agregarEspecialidad llamada');
        const select = document.getElementById('selectEspecialidad');
        
        if (!select) {
            console.error('No se encontró el select de especialidades');
            return;
        }
        
        const selectedOption = select.options[select.selectedIndex];
        console.log('Opción seleccionada:', selectedOption ? selectedOption.value : 'No hay opción seleccionada');
        
        if (!selectedOption || !selectedOption.value) {
            alert('Por favor seleccione una especialidad');
            return;
        }
        
        const id = selectedOption.value;
        const nombre = selectedOption.getAttribute('data-nombre');
        
        console.log('ID:', id, 'Nombre:', nombre);
        
        // Verificar si ya está agregada
        if (especialidadesAgregadas.includes(id)) {
            alert('Esta especialidad ya está agregada');
            return;
        }
        
        // Agregar al array de control
        especialidadesAgregadas.push(id);
        
        // Crear campo visual para la especialidad
        const contenedor = document.getElementById('especialidadesSeleccionadas');
        if (!contenedor) {
            console.error('No se encontró el contenedor de especialidades seleccionadas');
            return;
        }
        
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
        
        console.log('Especialidad agregada. Array actual:', especialidadesAgregadas);
        console.log('Contenido HTML del contenedor:', contenedor.innerHTML);
        
        // Resetear el select
        select.selectedIndex = 0;
    }

    // Función para quitar especialidad
    function quitarEspecialidad(boton, id) {
        // Remover del array de control
        especialidadesAgregadas = especialidadesAgregadas.filter(espId => espId !== id);
        
        // Remover el elemento del DOM
        boton.parentElement.remove();
    }

    // Función para verificar el formulario antes de enviarlo
    function verificarFormulario(event) {
        console.log('Verificando formulario antes del envío...');
        
        const form = document.getElementById('formCrearSeccion');
        const especialidadesInputs = form.querySelectorAll('input[name="especialidades[]"]');
        
        console.log('Campos de especialidades encontrados:', especialidadesInputs.length);
        console.log('Array de especialidades:', especialidadesAgregadas);
        
        if (especialidadesInputs.length === 0) {
            event.preventDefault();
            alert('Debe agregar al menos una especialidad.');
            return false;
        }
        
        return true;
    }

    // ========== FUNCIONES PARA EDITAR SECCIÓN ==========
    
    // Arrays para llevar control de especialidades por sección
    let especialidadesAgregardasEdit = {};

    // Función para agregar especialidad en modal de editar
    function agregarEspecialidadEdit(seccionId) {
        console.log('Función agregarEspecialidadEdit llamada para sección:', seccionId);
        const select = document.getElementById(`selectEspecialidadEdit-${seccionId}`);
        
        if (!select) {
            console.error('No se encontró el select de especialidades para editar');
            return;
        }
        
        const selectedOption = select.options[select.selectedIndex];
        console.log('Opción seleccionada:', selectedOption ? selectedOption.value : 'No hay opción seleccionada');
        
        if (!selectedOption || !selectedOption.value) {
            alert('Por favor seleccione una especialidad');
            return;
        }
        
        const id = selectedOption.value;
        const nombre = selectedOption.getAttribute('data-nombre');
        
        console.log('ID:', id, 'Nombre:', nombre);
        
        // Inicializar array para esta sección si no existe
        if (!especialidadesAgregardasEdit[seccionId]) {
            especialidadesAgregardasEdit[seccionId] = [];
        }
        
        // Verificar si ya está agregada (verificar tanto en DOM como en array)
        const contenedor = document.getElementById(`especialidadesSeleccionadasEdit-${seccionId}`);
        const existingElements = contenedor.querySelectorAll(`[data-id="${id}"]`);
        
        if (existingElements.length > 0 || (especialidadesAgregardasEdit[seccionId] && especialidadesAgregardasEdit[seccionId].includes(id))) {
            alert('Esta especialidad ya está agregada');
            return;
        }
        
        // Agregar al array de control
        especialidadesAgregardasEdit[seccionId].push(id);
        
        if (!contenedor) {
            console.error('No se encontró el contenedor de especialidades seleccionadas para editar');
            return;
        }
        
        const especialidadDiv = document.createElement('div');
        especialidadDiv.className = 'input-group mt-2';
        especialidadDiv.setAttribute('data-id', id);
        especialidadDiv.innerHTML = `
            <input type="text" class="form-control" value="${nombre}" readonly>
            <input type="hidden" name="especialidades[]" value="${id}">
            <button type="button" class="btn btn-danger" onclick="quitarEspecialidadEdit(this, '${id}', ${seccionId})">
                <i class="bi bi-x"></i>
            </button>
        `;
        
        contenedor.appendChild(especialidadDiv);
        
        console.log('Especialidad agregada. Array actual:', especialidadesAgregardasEdit[seccionId]);
        
        // Resetear el select
        select.selectedIndex = 0;
    }

    // Función para quitar especialidad en modal de editar
    function quitarEspecialidadEdit(boton, id, seccionId) {
        console.log('Quitando especialidad', id, 'de la sección', seccionId);
        
        // Remover del array de control
        if (especialidadesAgregardasEdit[seccionId]) {
            especialidadesAgregardasEdit[seccionId] = especialidadesAgregardasEdit[seccionId].filter(espId => espId !== id);
            console.log('Array actualizado:', especialidadesAgregardasEdit[seccionId]);
        }
        
        // Remover el elemento del DOM
        boton.parentElement.remove();
    }

    // Asegurar que todo esté listo cuando se cargue el DOM
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM cargado. Verificando elementos...');
        
        const select = document.getElementById('selectEspecialidad');
        const contenedor = document.getElementById('especialidadesSeleccionadas');
        
        console.log('Select encontrado:', !!select);
        console.log('Contenedor encontrado:', !!contenedor);
        
        if (select) {
            console.log('Opciones en select:', select.options.length);
        }
    });
</script>
@endsection


