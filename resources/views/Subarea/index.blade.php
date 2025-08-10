@extends('Template-administrador')

@section('title', 'Registro de Sub-Área')

@section('content')
<div class="wrapper">
    <div class="main-content">

        {{-- Encabezado de búsqueda y botón Agregar --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group w-50">
                <form id="busquedaForm" method="GET" action="{{ route('subarea.index') }}" class="d-flex w-100">
                    <span class="input-group-text bg-white border-white">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 shadow-sm"
                        placeholder="Buscar por subarea o especialidad..." name="busquedaSubarea" 
                        value="{{ request('busquedaSubarea') }}" id="inputBusqueda" autocomplete="off" 
                        style="border-radius: 20px;">
                    @if(request('busquedaSubarea'))
                    <button type="button" class="btn btn-outline-secondary border-0" id="limpiarBusqueda" title="Limpiar búsqueda">
                        <i class="bi bi-x-circle"></i>
                    </button>
                    @endif
                </form>
            </div>
            
            @if(Auth::user() && !Auth::user()->hasRole('director'))
            <button class="btn btn-primary rounded-pill px-4 d-flex align-items-center"
                data-bs-toggle="modal" data-bs-target="#modalAgregarSubArea"
                title="Agregar SubÁrea" style="background-color: #134496; font-size: 1.2rem;">
                Agregar <i class="bi bi-plus-circle ms-2"></i>
            </button>
            @endif

        </div>

        {{-- Indicador de resultados de búsqueda --}}
        @if(request('busquedaSubarea'))
            <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <span>
                    Mostrando {{ $subareas->count() }} resultado(s) para "<strong>{{ request('busquedaSubarea') }}</strong>"
                    <a href="{{ route('subarea.index') }}" class="btn btn-sm btn-outline-primary ms-2">Ver todas</a>
                </span>
            </div>
        @endif

        {{-- Tabla de Sub-Áreas --}}
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th class="text-center">Nombre</th>
                        <th class="text-center">Especialidad</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subareas as $subarea)
                    <tr>
                        @if ($subarea->condicion == 1)
                            <td class="text-center">{{ $subarea->nombre }}</td>
                            <td class="text-center">{{ $subarea->especialidad ? $subarea->especialidad->nombre : 'Sin especialidad' }}</td>
                            <td class="text-center">
                                @if(Auth::user() && !Auth::user()->hasRole('director'))
                                <button class="btn btn-link text-info p-0 me-2" data-bs-toggle="modal"
                                    data-bs-target="#modalEditarSubArea-{{ $subarea->id }}">
                                    <i class="bi bi-pencil" style="font-size: 1.5rem;"></i>
                                </button>
                                <button class="btn btn-link text-danger p-0" data-bs-toggle="modal"
                                    data-bs-target="#modalEliminarSubarea-{{ $subarea->id }}">
                                    <i class="bi bi-trash" style="font-size: 1.5rem;"></i>
                                </button>
                                @else
                                <span class="text-muted">Solo vista</span>
                                @endif
                            </td>
                        @endif
                    </tr>

                    {{-- Modal Editar SubÁrea --}}
                    <div class="modal fade" id="modalEditarSubArea-{{ $subarea->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header modal-header-custom">
                                    <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                        <i class="bi bi-arrow-left"></i>
                                    </button>
                                    <h5 class="modal-title">Registro de subárea</h5>
                                </div>
                                <div class="modal-body px-4 py-4">
                                    <form action="{{ route('subarea.update', $subarea->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">SubÁrea</label>
                                            <input type="text" name="nombre" class="form-control"
                                                value="{{ old('nombre', $subarea->nombre) }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Especialidad</label>
                                            <select name="id_especialidad" class="form-control" required>
                                                @foreach ($especialidades as $especialidad)
                                                    <option value="{{ $especialidad->id }}"
                                                        {{ old('id_especialidad', $subarea->id_especialidad) == $especialidad->id ? 'selected' : '' }}>
                                                        {{ $especialidad->nombre }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="text-center mt-4">
                                            <button type="submit" class="btn btn-primary">Modificar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Modal Eliminar SubÁrea --}}
                    <div class="modal fade" id="modalEliminarSubarea-{{ $subarea->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header modal-header-custom">
                                    <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                                        <i class="bi bi-arrow-left"></i>
                                    </button>
                                    <h5 class="modal-title">Confirmar eliminación</h5>
                                </div>
                                <div class="modal-body px-4 py-4 text-center">
                                    <div class="mb-3">
                                        <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    <h6 class="mb-3">¿Está seguro que desea eliminar esta subárea?</h6>
                                    <p class="text-muted mb-4">Esta acción no se puede deshacer.</p>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="{{ route('subarea.destroy', ['subarea' => $subarea->id]) }}" method="post" class="d-inline">
                                            @method('DELETE')
                                            @csrf
                                            <button type="submit" class="btn btn-danger">Sí</button>
                                        </form>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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

{{-- Modal Crear SubÁrea --}}
<div class="modal fade" id="modalAgregarSubArea" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header modal-header-custom">
                <button class="btn-back" data-bs-dismiss="modal" aria-label="Cerrar">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <h5 class="modal-title">Registro de Sub-Área</h5>
            </div>
            <div class="modal-body px-4 py-4">
                <form action="{{ route('subarea.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Sub Área</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" required>

                        <label class="form-label fw-bold mt-3">Especialidad</label>
                        <select name="id_especialidad" class="form-select" required>
                            <option value="">Elija la Especialidad</option>
                            @foreach ($especialidades as $especialidad)
                            <option value="{{ $especialidad->id }}">{{ $especialidad->nombre }}</option>
                            @endforeach
                        </select>
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
            window.location.href = '{{ route("subarea.index") }}';
        });
    }
</script>
@endsection